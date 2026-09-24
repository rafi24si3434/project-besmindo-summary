<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\LokasiModel;
use App\Models\DailyReportModel;
use App\Models\DailyReportDtModel;
use App\Models\MonthlySummaryModel;
use App\Models\NptModel;
use App\Models\ActivityLogModel;

class DailyReport extends BaseController
{
    protected $rigModel;
    protected $lokasiModel;
    protected $dailyReportModel;
    protected $dailyReportDtModel;
    protected $kategoriModel;
    protected $nptModel;
    protected $thirdPartyModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
        $this->lokasiModel = new LokasiModel();
        $this->dailyReportModel = new DailyReportModel();
        $this->dailyReportDtModel = new DailyReportDtModel();
        $this->kategoriModel = new KategoriDowntimeModel();
        $this->nptModel = new NptModel();
        $this->thirdPartyModel = new \App\Models\ThirdPartyModel();
    }

    public function index()
    {
        $rigs = $this->rigModel->getRigAktif();
        $firstRigId = $rigs[0]['id'] ?? 1;

        // Prioritaskan query param, lalu session terakhir kita stay, lalu fallback ke default
        $rigId = (int)$this->request->getGet('rig_id') 
            ?: (int)session('last_rig_id') 
            ?: $firstRigId;

        $bulan = (int)$this->request->getGet('bulan') 
            ?: (int)session('last_bulan') 
            ?: (int)date('n');

        $tahun = (int)$this->request->getGet('tahun') 
            ?: (int)session('last_tahun') 
            ?: (int)date('Y');

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"));
    }

    public function grid(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('daily-report'))->with('error', 'Rig tidak ditemukan.');
        }

        // Simpan state rig, bulan, tahun terakhir ke session agar user stay di rig terakhir
        session()->set([
            'last_rig_id' => $rigId,
            'last_bulan'  => $bulan,
            'last_tahun'  => $tahun,
        ]);

        $allRigs = $this->rigModel->getRigAktif();
        $reports = $this->dailyReportModel->getByRigBulanTahun($rigId, $bulan, $tahun);
        $kategoriList = $this->kategoriModel->getKategoriAktif();

        // Ambil rincian breakdown downtime per daily_report_id
        $dtDetails = [];
        if (!empty($reports)) {
            $reportIds = array_column($reports, 'id');
            $db = \Config\Database::connect();
            $dtRows = $db->table('daily_report_dt')
                ->whereIn('daily_report_id', $reportIds)
                ->get()->getResultArray();

            foreach ($dtRows as $dtr) {
                $dtDetails[$dtr['daily_report_id']][$dtr['kategori_id']] = (float)$dtr['jam'];
            }

            // Ambil rincian log harian per tanggal dari tabel daily_report_log
            $logRows = $db->table('daily_report_log')
                ->whereIn('daily_report_id', $reportIds)
                ->orderBy('tanggal', 'ASC')
                ->get()->getResultArray();

            $dailyLogs = [];
            foreach ($logRows as $lr) {
                $dailyLogs[$lr['daily_report_id']][] = $lr;
            }
        } else {
            $dailyLogs = [];
        }

        $totalMiru = $this->dailyReportModel->getTotalMIRU($rigId, $bulan, $tahun);
        $totalOps = $this->dailyReportModel->getTotalOPS($rigId, $bulan, $tahun);
        $totalJam = $this->dailyReportModel->getTotalJam($rigId, $bulan, $tahun);
        $wellJobCount = count($reports);

        // Hitung total per pos downtime di footer
        $footerKatTotals = [];
        $grandTotalDt = 0;
        foreach ($reports as $rep) {
            $grandTotalDt += (float)$rep['total_dt'];
            foreach ($kategoriList as $k) {
                $val = $dtDetails[$rep['id']][$k['id']] ?? 0;
                $footerKatTotals[$k['id']] = ($footerKatTotals[$k['id']] ?? 0) + $val;
            }
        }

        // Tarif rate breakdown per jam
        $odr = (float)($rig['odr'] ?? 0);
        $rateMiru = $odr > 0 ? ($odr / 24.0 * 0.75) : 0; // Contoh proporsi ODR
        $rateOps = $odr > 0 ? ($odr / 24.0) : 0;
        $rateSbwc = $odr > 0 ? ($odr / 24.0 * 0.65) : 0;

        $data = [
            'title'            => "Rekap Sumur {$rig['kode']} - Bulan {$bulan}/{$tahun}",
            'page_title'       => "Rekap Sumur (Well): {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle'    => "Pusat ringkasan pekerjaan sumur, durasi operasi, MIRU moving, dan pos downtime",
            'rig'              => $rig,
            'rigId'            => $rigId,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'allRigs'          => $allRigs,
            'reports'          => $reports,
            'kategoriList'     => $kategoriList,
            'dtDetails'        => $dtDetails,
            'dailyLogs'        => $dailyLogs,
            'footerKatTotals'  => $footerKatTotals,
            'grandTotalDt'     => $grandTotalDt,
            'totalMiru'        => $totalMiru,
            'totalOps'         => $totalOps,
            'totalJam'         => $totalJam,
            'wellJobCount'     => $wellJobCount,
            'odr'              => $odr,
            'rateMiru'         => $rateMiru,
            'rateOps'          => $rateOps,
            'rateSbwc'         => $rateSbwc,
        ];

        return view('daily_report/grid', $data);
    }

    public function tambah(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        $lokasiList = $this->lokasiModel->getAktif();
        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $nextNoWell = $this->dailyReportModel->getNextNoWell($rigId, $bulan, $tahun);

        $data = [
            'title'         => "Tambah Sumur - {$rig['kode']}",
            'page_title'    => "Pencatatan Pekerjaan Sumur Baru: {$rig['kode']}",
            'page_subtitle' => "Input rincian operasi sumur, MIRU, OPS, dan pos downtime",
            'rig'           => $rig,
            'rigId'         => $rigId,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'lokasiList'    => $lokasiList,
            'kategoriList'  => $kategoriList,
            'nextNoWell'    => $nextNoWell,
        ];

        return view('daily_report/form', $data);
    }

    public function simpan()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');

        $miruJam = (float)$this->request->getPost('miru_jam');
        $opsJam = (float)$this->request->getPost('ops_jam');
        
        // Hitung total downtime dari pos kategori
        $downtimeInputs = $this->request->getPost('dt') ?? []; // [kategori_id] => jam
        $totalDt = 0;
        foreach ($downtimeInputs as $kId => $jam) {
            $totalDt += (float)$jam;
        }

        $totalJam = $miruJam + $opsJam + $totalDt;

        $dailyReportId = $this->dailyReportModel->insert([
            'rig_id'          => $rigId,
            'lokasi_id'       => (int)$this->request->getPost('lokasi_id'),
            'no_well'         => (int)$this->request->getPost('no_well'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'jarak'           => (float)$this->request->getPost('jarak'),
            'miru_jam'        => $miruJam,
            'ops_jam'         => $opsJam,
            'total_dt'        => $totalDt,
            'total_jam'       => $totalJam,
            'status_job'      => $this->request->getPost('status_job') ?? 'JOB COMPLETED',
            'remark'          => $this->request->getPost('remark'),
            'remark_unpaid'   => $this->request->getPost('remark_unpaid'),
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // Simpan rincian pos downtime ke daily_report_dt
        $tglPakai = $this->request->getPost('tanggal_mulai') ?: sprintf('%04d-%02d-01', $tahun, $bulan);
        foreach ($downtimeInputs as $kId => $jam) {
            $jamVal = (float)$jam;
            if ($jamVal > 0) {
                $this->dailyReportDtModel->insert([
                    'daily_report_id' => $dailyReportId,
                    'tanggal'         => $tglPakai,
                    'kategori_id'     => (int)$kId,
                    'jam'             => $jamVal,
                ]);
            }
        }

        // AUTO-SYNC ke NPT Harian
        $reportData = [
            'tanggal_mulai' => $tglPakai,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'remark'        => $this->request->getPost('remark'),
            'remark_unpaid' => $this->request->getPost('remark_unpaid'),
        ];
        $this->nptModel->syncFromDailyReportSummary($rigId, $reportData, $downtimeInputs);

        // Sinkronisasi ke Monthly Summary
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        // Catat jejak audit aktivitas
        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        $noWell = (int)$this->request->getPost('no_well');
        ActivityLogModel::record(
            'DAILY_REPORT',
            'TAMBAH_SUMUR',
            "Menambahkan data pekerjaan sumur No. {$noWell} pada {$rigKode} (Periode " . sprintf('%02d/%04d', $bulan, $tahun) . ", Total Jam: {$totalJam}j).",
            $rigId
        );

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Data pekerjaan sumur berhasil ditambahkan dan disinkronkan ke NPT Harian.');
    }

    public function detail(int $id)
    {
        $report = $this->dailyReportModel->find($id);
        if (!$report) {
            return redirect()->to(base_url('daily-report'))->with('error', 'Data pekerjaan sumur tidak ditemukan.');
        }

        $rig = $this->rigModel->find($report['rig_id']);
        $lokasi = $this->lokasiModel->find($report['lokasi_id']);
        $namaLokasi = $lokasi['nama_lokasi'] ?? ('Sumur #' . $report['no_well']);

        $db = \Config\Database::connect();
        
        // Ambil rincian log harian per tanggal
        $logs = $db->table('daily_report_log')
            ->where('daily_report_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        // Ambil rincian pos downtime terinci
        $dtRows = $db->table('daily_report_dt')
            ->select('daily_report_dt.*, kategori_downtime.nama as nama_kategori, kategori_downtime.tipe')
            ->join('kategori_downtime', 'kategori_downtime.id = daily_report_dt.kategori_id', 'left')
            ->where('daily_report_id', $id)
            ->get()->getResultArray();

        $kategoriList = $this->kategoriModel->getKategoriAktif();

        // Hitung total-total jam
        $miruJam  = (float)($report['miru_jam'] ?? 0);
        $opsJam   = (float)($report['ops_jam'] ?? 0);
        $totalDt  = (float)($report['total_dt'] ?? 0);
        $totalJam = (float)($report['total_jam'] ?? 0);
        if ($totalJam <= 0) {
            $totalJam = $miruJam + $opsJam + $totalDt;
        }

        // Breakdown SBWC vs UNPAID
        $sbwcJam = 0;
        $unpaidJam = 0;
        foreach ($dtRows as $dtr) {
            if (($dtr['tipe'] ?? '') === 'UNPAID') {
                $unpaidJam += (float)$dtr['jam'];
            } else {
                $sbwcJam += (float)$dtr['jam'];
            }
        }
        if ($sbwcJam == 0 && $totalDt > $unpaidJam) {
            $sbwcJam = $totalDt - $unpaidJam;
        }

        $data = [
            'title'         => "Detail Sumur {$namaLokasi} - {$rig['kode']}",
            'page_title'    => "Detail Pekerjaan Sumur {$namaLokasi}",
            'page_subtitle' => "Rig {$rig['kode']} ({$rig['nama_rig']}) — Periode {$report['bulan']}/{$report['tahun']}",
            'report'        => $report,
            'rig'           => $rig,
            'lokasi'        => $lokasi,
            'namaLokasi'    => $namaLokasi,
            'logs'          => $logs,
            'dtRows'        => $dtRows,
            'kategoriList'  => $kategoriList,
            'miruJam'       => $miruJam,
            'opsJam'        => $opsJam,
            'totalDt'       => $totalDt,
            'totalJam'      => $totalJam,
            'sbwcJam'       => $sbwcJam,
            'unpaidJam'     => $unpaidJam,
            'backUrl'       => base_url("daily-report/{$report['rig_id']}/{$report['bulan']}/{$report['tahun']}"),
        ];

        return view('daily_report/detail', $data);
    }

    public function edit(int $id)
    {
        $report = $this->dailyReportModel->find($id);
        if (!$report) {
            return redirect()->to(base_url('daily-report'))->with('error', 'Data tidak ditemukan.');
        }

        $rig = $this->rigModel->find($report['rig_id']);
        $lokasiList = $this->lokasiModel->getAktif();
        $kategoriList = $this->kategoriModel->getKategoriAktif();

        // Ambil existing downtime per kategori
        $db = \Config\Database::connect();
        $dtRows = $db->table('daily_report_dt')->where('daily_report_id', $id)->get()->getResultArray();
        $dtMap = [];
        foreach ($dtRows as $row) {
            $dtMap[$row['kategori_id']] = (float)$row['jam'];
        }

        $data = [
            'title'         => "Edit Sumur #{$report['no_well']} - {$rig['kode']}",
            'page_title'    => "Edit Pekerjaan Sumur #{$report['no_well']}: {$rig['kode']}",
            'page_subtitle' => "Perbarui waktu operasi, pos downtime, dan status pekerjaan",
            'rig'           => $rig,
            'report'        => $report,
            'lokasiList'    => $lokasiList,
            'kategoriList'  => $kategoriList,
            'dtMap'         => $dtMap,
        ];

        return view('daily_report/form', $data);
    }

    public function update(int $id)
    {
        $report = $this->dailyReportModel->find($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $miruJam = (float)$this->request->getPost('miru_jam');
        $opsJam = (float)$this->request->getPost('ops_jam');

        $downtimeInputs = $this->request->getPost('dt') ?? [];
        $totalDt = 0;
        foreach ($downtimeInputs as $kId => $jam) {
            $totalDt += (float)$jam;
        }

        $totalJam = $miruJam + $opsJam + $totalDt;

        $this->dailyReportModel->update($id, [
            'lokasi_id'       => (int)$this->request->getPost('lokasi_id'),
            'no_well'         => (int)$this->request->getPost('no_well'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'jarak'           => (float)$this->request->getPost('jarak'),
            'miru_jam'        => $miruJam,
            'ops_jam'         => $opsJam,
            'total_dt'        => $totalDt,
            'total_jam'       => $totalJam,
            'status_job'      => $this->request->getPost('status_job'),
            'remark'          => $this->request->getPost('remark'),
            'remark_unpaid'   => $this->request->getPost('remark_unpaid'),
        ]);

        // Perbarui rincian daily_report_dt
        $this->dailyReportDtModel->where('daily_report_id', $id)->delete();
        $tglPakai = $this->request->getPost('tanggal_mulai') ?: sprintf('%04d-%02d-01', $report['tahun'], $report['bulan']);
        foreach ($downtimeInputs as $kId => $jam) {
            $jamVal = (float)$jam;
            if ($jamVal > 0) {
                $this->dailyReportDtModel->insert([
                    'daily_report_id' => $id,
                    'tanggal'         => $tglPakai,
                    'kategori_id'     => (int)$kId,
                    'jam'             => $jamVal,
                ]);
            }
        }

        // AUTO-SYNC ke NPT Harian
        $reportData = [
            'tanggal_mulai' => $tglPakai,
            'bulan'         => $report['bulan'],
            'tahun'         => $report['tahun'],
            'remark'        => $this->request->getPost('remark'),
            'remark_unpaid' => $this->request->getPost('remark_unpaid'),
        ];
        $this->nptModel->syncFromDailyReportSummary($report['rig_id'], $reportData, $downtimeInputs);

        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($report['rig_id'], $report['bulan'], $report['tahun']);

        // Catat jejak audit aktivitas
        $rig = $this->rigModel->find($report['rig_id']);
        $rigKode = $rig['kode'] ?? "Rig #{$report['rig_id']}";
        $noWell = (int)$this->request->getPost('no_well');
        ActivityLogModel::record(
            'DAILY_REPORT',
            'UPDATE_SUMUR',
            "Memperbarui data pekerjaan sumur No. {$noWell} pada {$rigKode} (Periode " . sprintf('%02d/%04d', $report['bulan'], $report['tahun']) . ", Total Jam: {$totalJam}j).",
            $report['rig_id']
        );

        return redirect()->to(base_url("daily-report/{$report['rig_id']}/{$report['bulan']}/{$report['tahun']}"))->with('success', 'Data pekerjaan sumur berhasil diperbarui dan disinkronkan ke NPT Harian.');
    }

    public function hapus(int $id)
    {
        $report = $this->dailyReportModel->find($id);
        if ($report) {
            $rigId = $report['rig_id'];
            $bulan = $report['bulan'];
            $tahun = $report['tahun'];

            $this->dailyReportDtModel->where('daily_report_id', $id)->delete();
            $this->dailyReportModel->delete($id);

            $summaryModel = new MonthlySummaryModel();
            $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

            // Catat jejak audit aktivitas
            $rig = $this->rigModel->find($rigId);
            $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
            ActivityLogModel::record(
                'DAILY_REPORT',
                'HAPUS_SUMUR',
                "Menghapus data pekerjaan sumur No. {$report['no_well']} pada {$rigKode} (Periode " . sprintf('%02d/%04d', $bulan, $tahun) . ").",
                $rigId
            );

            return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Pekerjaan sumur berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data gagal dihapus.');
    }

    public function logHarianDefault()
    {
        $rigs = $this->rigModel->getRigAktif();
        $firstRigId = $rigs[0]['id'] ?? 1;

        $rigId = (int)$this->request->getGet('rig_id') 
            ?: (int)session('last_rig_id') 
            ?: $firstRigId;

        $bulan = (int)$this->request->getGet('bulan') 
            ?: (int)session('last_bulan') 
            ?: (int)date('n');

        $tahun = (int)$this->request->getGet('tahun') 
            ?: (int)session('last_tahun') 
            ?: (int)date('Y');

        return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}"));
    }

    /**
     * FORM & TABEL INPUT LOG OPERASI HARIAN (PER TANGGAL)
     * Tempat petugas mencatat aktivitas harian: Tanggal, Sumur apa, Jarak, MIRU, OPS, Pos Downtime
     */
    public function logHarian(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('daily-report'))->with('error', 'Rig tidak ditemukan.');
        }

        // Simpan state rig, bulan, tahun terakhir ke session
        session()->set([
            'last_rig_id' => $rigId,
            'last_bulan'  => $bulan,
            'last_tahun'  => $tahun,
        ]);

        $allRigs = $this->rigModel->getRigAktif();
        $wells = $this->dailyReportModel->getByRigBulanTahun($rigId, $bulan, $tahun);
        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $lokasiList = $this->lokasiModel->getAktif();
        $thirdParties = $this->thirdPartyModel->getAktif();

        $db = \Config\Database::connect();
        $recentLogs = $db->table('daily_report_log drl')
            ->select('drl.*, dr.no_well, l.nama_lokasi')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $rigId)
            ->where('MONTH(drl.tanggal)', $bulan)
            ->where('YEAR(drl.tanggal)', $tahun)
            ->orderBy('drl.tanggal', 'DESC')
            ->get()->getResultArray();

        // Ambil tp_breakdown dari npt_harian
        foreach ($recentLogs as &$rl) {
            $rl['tp_breakdown'] = [];
            if ((float)$rl['dt_3rd_party'] > 0) {
                $tpData = $db->table('npt_harian')
                    ->where('rig_id', $rigId)
                    ->where('tanggal', $rl['tanggal'])
                    ->where('kategori_id', 10)
                    ->where('third_party_id IS NOT NULL')
                    ->get()->getResultArray();
                foreach ($tpData as $t) {
                    $rl['tp_breakdown'][$t['third_party_id']] = (float)$t['jam'];
                }
            }
        }
        unset($rl);

        $data = [
            'title'         => "Input Daily Report - {$rig['kode']}",
            'page_title'    => "Input Daily Report: {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle' => "Pencatatan laporan operasi harian rig per tanggal (00:00 - 24:00)",
            'rig'           => $rig,
            'rigId'         => $rigId,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'allRigs'       => $allRigs,
            'wells'         => $wells,
            'kategoriList'  => $kategoriList,
            'lokasiList'    => $lokasiList,
            'thirdParties'  => $thirdParties,
            'recentLogs'    => $recentLogs,
        ];

        return view('daily_report/log_harian', $data);
    }

    /**
     * SIMPAN / UPDATE LOG HARIAN
     * Menyimpan ke daily_report_log, update subtotal di daily_report, dan auto-sync ke NPT Harian
     */
    public function simpanLog()
    {
        $rigId   = (int)$this->request->getPost('rig_id');
        $bulan   = (int)$this->request->getPost('bulan');
        $tahun   = (int)$this->request->getPost('tahun');
        $repId   = (int)$this->request->getPost('daily_report_id');
        $tanggal = $this->request->getPost('tanggal');

        if (!$repId || empty($tanggal)) {
            return redirect()->back()->with('error', 'Silakan pilih sumur dan tanggal operasi.');
        }

        $jarak      = (float)$this->request->getPost('jarak');
        $miruJam    = (float)$this->request->getPost('miru_jam');
        $opsJam     = (float)$this->request->getPost('ops_jam');

        // Pos SBWC
        $dtRain     = (float)$this->request->getPost('dt_rain');
        $dtDryRoad  = (float)$this->request->getPost('dt_dry_road');
        $dtDryPad   = (float)$this->request->getPost('dt_dry_pad');
        $dtPhrOp    = (float)$this->request->getPost('dt_phr_op');
        $dtTrans    = (float)$this->request->getPost('dt_trans');
        $dtCePe     = (float)$this->request->getPost('dt_ce_pe');
        $dt3rdParty = (float)$this->request->getPost('dt_3rd_party');
        $dtDaylight = (float)$this->request->getPost('dt_daylight');
        $dtPhrWell  = (float)$this->request->getPost('dt_phr_well');
        $dtFoam     = (float)$this->request->getPost('dt_foam');

        // Pos UNPAID
        $dtRig      = (float)$this->request->getPost('dt_rig');
        $dtTool     = (float)$this->request->getPost('dt_tool');

        // Shutdown
        $dtShutdown = (float)$this->request->getPost('dt_shutdown');

        $totalDt  = $dtRain + $dtDryRoad + $dtDryPad + $dtPhrOp + $dtTrans + $dtCePe + $dt3rdParty + $dtDaylight + $dtPhrWell + $dtFoam + $dtRig + $dtTool + $dtShutdown;
        $totalHrs = $miruJam + $opsJam + $totalDt;

        $remarkNpt    = $this->request->getPost('remark_npt');
        $remarkUnpaid = $this->request->getPost('remark_unpaid');
        $tpJam        = $this->request->getPost('tp_jam') ?? [];

        $logData = [
            'daily_report_id' => $repId,
            'tanggal'         => $tanggal,
            'jarak'           => $jarak,
            'miru_jam'        => $miruJam,
            'ops_jam'         => $opsJam,
            'dt_rain'         => $dtRain,
            'dt_dry_road'     => $dtDryRoad,
            'dt_dry_pad'      => $dtDryPad,
            'dt_phr_op'       => $dtPhrOp,
            'dt_trans'        => $dtTrans,
            'dt_ce_pe'        => $dtCePe,
            'dt_3rd_party'    => $dt3rdParty,
            'dt_daylight'     => $dtDaylight,
            'dt_phr_well'     => $dtPhrWell,
            'dt_foam'         => $dtFoam,
            'dt_rig'          => $dtRig,
            'dt_tool'         => $dtTool,
            'dt_shutdown'     => $dtShutdown,
            'total_dt'        => $totalDt,
            'total_hrs'       => $totalHrs,
            'remark_npt'      => $remarkNpt,
            'remark_unpaid'   => $remarkUnpaid,
            'tp_jam'          => $tpJam, // Untuk rincian 3rd party
        ];

        $db = \Config\Database::connect();

        $dbData = $logData;
        unset($dbData['tp_jam']);

        // Cari apakah sudah ada log pada tanggal ini untuk rig ini (lintas sumur jika dipindah)
        $existing = $db->table('daily_report_log drl')
            ->select('drl.id, drl.daily_report_id, dr.rig_id')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('drl.tanggal', $tanggal)
            ->get()->getRowArray();

        $oldRepId = null;
        if ($existing) {
            $oldRepId = (int)$existing['daily_report_id'];
            $db->table('daily_report_log')->where('id', $existing['id'])->update($dbData);
        } else {
            $db->table('daily_report_log')->insert($dbData);
        }

        // 1. REKALKULASI TOTAL SUMUR DI daily_report
        if ($oldRepId && $oldRepId !== $repId) {
            $this->recalcParentWell($oldRepId);
        }
        $this->recalcParentWell($repId);

        // Update remark pada header sumur jika diisi
        if (!empty($remarkUnpaid)) {
            $db->table('daily_report')->where('id', $repId)->update(['remark_unpaid' => $remarkUnpaid]);
        }
        if (!empty($remarkNpt)) {
            $db->table('daily_report')->where('id', $repId)->update(['remark' => $remarkNpt]);
        }

        // 2. AUTO-SYNC KE NPT HARIAN
        $this->nptModel->syncFromDailyLog($rigId, $logData);

        // 3. AUTO-SYNC KE MONTHLY SUMMARY
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        // Catat jejak audit aktivitas
        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        ActivityLogModel::record(
            'DAILY_REPORT',
            'SIMPAN_LOG_HARIAN',
            "Mencatat log operasi harian tanggal {$tanggal} pada {$rigKode} (MIRU: {$miruJam}j, OPS: {$opsJam}j, Total DT: {$totalDt}j, Total: {$totalHrs}j).",
            $rigId
        );

        return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', "Log tanggal {$tanggal} berhasil disimpan, disinkronkan ke sumur dan NPT Harian.");
    }

    /**
     * Helper: Hitung ulang subtotal jam di tabel parent daily_report berdasarkan semua daily_report_log miliknya
     */
    private function recalcParentWell(int $wellId): void
    {
        $db = \Config\Database::connect();
        $allLogs = $db->table('daily_report_log')->where('daily_report_id', $wellId)->get()->getResultArray();
        $sumMiru = 0; $sumOps = 0; $sumDt = 0; $maxJarak = 0;
        foreach ($allLogs as $al) {
            $sumMiru  += (float)$al['miru_jam'];
            $sumOps   += (float)$al['ops_jam'];
            $sumDt    += (float)$al['total_dt'];
            if ((float)$al['jarak'] > $maxJarak) $maxJarak = (float)$al['jarak'];
        }

        $updateParent = [
            'miru_jam'  => $sumMiru,
            'ops_jam'   => $sumOps,
            'total_dt'  => $sumDt,
            'total_jam' => $sumMiru + $sumOps + $sumDt,
        ];
        if ($maxJarak > 0) {
            $updateParent['jarak'] = $maxJarak;
        }

        $db->table('daily_report')->where('id', $wellId)->update($updateParent);
    }

    /**
     * HAPUS LOG HARIAN TERTENTU
     * Menghapus log, mengkalkulasi ulang subtotal sumur, me-reset sync NPT tanggal terkait, dan update monthly summary
     */
    public function hapusLog(int $id)
    {
        $db = \Config\Database::connect();
        $log = $db->table('daily_report_log drl')
            ->select('drl.*, dr.rig_id, dr.bulan, dr.tahun')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('drl.id', $id)
            ->get()->getRowArray();

        if (!$log) {
            return redirect()->back()->with('error', 'Catatan log tidak ditemukan.');
        }

        $rigId   = (int)$log['rig_id'];
        $bulan   = (int)$log['bulan'];
        $tahun   = (int)$log['tahun'];
        $wellId  = (int)$log['daily_report_id'];
        $tanggal = $log['tanggal'];

        // 1. Hapus record log harian
        $db->table('daily_report_log')->where('id', $id)->delete();

        // 2. Rekalkulasi subtotal jam di sumur parent
        $this->recalcParentWell($wellId);

        // 3. Reset sync NPT Harian untuk tanggal ini menjadi 0
        $zeroLog = [
            'tanggal'      => $tanggal,
            'dt_rain'      => 0, 'dt_dry_road' => 0, 'dt_dry_pad' => 0, 'dt_phr_op' => 0,
            'dt_trans'     => 0, 'dt_ce_pe' => 0, 'dt_3rd_party' => 0, 'dt_daylight' => 0,
            'dt_phr_well'  => 0, 'dt_foam' => 0, 'dt_rig' => 0, 'dt_tool' => 0, 'dt_shutdown' => 0,
            'remark_npt'   => null, 'remark_unpaid' => null,
        ];
        $this->nptModel->syncFromDailyLog($rigId, $zeroLog);

        // 4. Rekalkulasi Monthly Summary
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        // 5. Catat jejak audit aktivitas
        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        ActivityLogModel::record(
            'DAILY_REPORT',
            'HAPUS_LOG_HARIAN',
            "Menghapus log operasi harian tanggal {$tanggal} pada {$rigKode}.",
            $rigId
        );

        return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', "Log tanggal {$tanggal} berhasil dihapus. Subtotal sumur & NPT telah disinkronkan kembali.");
    }
    public function simpanSumurCepat()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $noWell = (int)$this->request->getPost('no_well');
        $lokasiId = (int)$this->request->getPost('lokasi_id');

        if (!$rigId || !$bulan || !$tahun || !$noWell || !$lokasiId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $lokasiModel = new \App\Models\LokasiModel();
        $lokasi = $lokasiModel->find($lokasiId);

        $db = \Config\Database::connect();
        
        $data = [
            'rig_id' => $rigId,
            'lokasi_id' => $lokasiId,
            'no_well' => $noWell,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'status_job' => 'Moving',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $db->table('daily_report')->insert($data);
            $newId = $db->insertID();
            
            return $this->response->setJSON([
                'status' => 'success',
                'id' => $newId,
                'no_well' => $noWell,
                'nama_lokasi' => $lokasi['nama_lokasi'] ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
