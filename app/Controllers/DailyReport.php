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
                $dtDetails[$dtr['daily_report_id']][$dtr['kategori_id']] =
                    ($dtDetails[$dtr['daily_report_id']][$dtr['kategori_id']] ?? 0.0) + (float)$dtr['jam'];
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
        $totalWellJob = $this->dailyReportModel->getTotalWellJob($rigId, $bulan, $tahun);
        $rawWellCount = count($reports);
        $wellJobCount = $totalWellJob;

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

        $reportMeta = $this->getReportMeta($rigId, $bulan, $tahun);

        $data = [
            'title'            => "Rekap Sumur {$rig['kode']} - Bulan {$bulan}/{$tahun}",
            'page_title'       => "Rekap Sumur (Well): {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle'    => "Pusat ringkasan pekerjaan sumur, durasi operasi, MIRU moving, dan pos downtime",
            'rig'              => $rig,
            'rigId'            => $rigId,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'reportMeta'       => $reportMeta,
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
            'totalWellJob'     => $totalWellJob,
            'rawWellCount'     => $rawWellCount,
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
        $existingWells = $this->dailyReportModel->getByRigBulanTahun($rigId, $bulan, $tahun);

        $maxDaysPeriod = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $minDatePeriod = sprintf('%04d-%02d-01', $tahun, $bulan);
        $maxDatePeriod = sprintf('%04d-%02d-%02d', $tahun, $bulan, $maxDaysPeriod);

        $lastWell = null;
        $latestEndDate = '';
        foreach ($existingWells as $ew) {
            $ewEnd = $ew['tanggal_selesai'] ?? $ew['tanggal_mulai'] ?? '';
            if (!$lastWell || $ewEnd >= $latestEndDate) {
                $latestEndDate = $ewEnd;
                $lastWell = $ew;
            }
        }

        $smartStartDate = $minDatePeriod;
        $sameDayOption = null;
        $nextDayOption = null;

        if ($lastWell && !empty($latestEndDate)) {
            $db = \Config\Database::connect();
            $rowUsed = $db->table('daily_report_log drl')
                ->select('SUM(drl.total_hrs) as used_hrs')
                ->join('daily_report dr', 'dr.id = drl.daily_report_id')
                ->where('dr.rig_id', $rigId)
                ->where('drl.tanggal', $latestEndDate)
                ->get()->getRowArray();

            $usedOnEndDay = (float)($rowUsed['used_hrs'] ?? 0.0);
            $nextDayTs = strtotime($latestEndDate . ' +1 day');
            $nextDayStr = date('Y-m-d', $nextDayTs);
            if ($nextDayStr > $maxDatePeriod) {
                $nextDayStr = $maxDatePeriod;
            }
            $nextDayOption = $nextDayStr;

            if ($usedOnEndDay > 0 && $usedOnEndDay < 23.99) {
                $sisaJam = max(0.0, 24.0 - $usedOnEndDay);
                $smartStartDate = $latestEndDate;
                $sameDayOption = [
                    'date'     => $latestEndDate,
                    'used_hrs' => $usedOnEndDay,
                    'sisa_hrs' => $sisaJam,
                ];
            } else {
                $smartStartDate = $nextDayStr;
            }
        }

        $smartEndTs = strtotime($smartStartDate . ' +3 days');
        $smartEndDate = date('Y-m-d', $smartEndTs);
        if ($smartEndDate > $maxDatePeriod) {
            $smartEndDate = $maxDatePeriod;
        }

        $data = [
            'title'          => "Tambah Sumur - {$rig['kode']}",
            'page_title'     => "Pencatatan Pekerjaan Sumur Baru: {$rig['kode']}",
            'page_subtitle'  => "Input rincian operasi sumur, jadwal tanggal mulai/selesai, dan lokasi",
            'rig'            => $rig,
            'rigId'          => $rigId,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'lokasiList'     => $lokasiList,
            'kategoriList'   => $kategoriList,
            'nextNoWell'     => $nextNoWell,
            'existingWells'  => $existingWells,
            'lastWell'       => $lastWell,
            'smartStartDate' => $smartStartDate,
            'smartEndDate'   => $smartEndDate,
            'sameDayOption'  => $sameDayOption,
            'nextDayOption'  => $nextDayOption,
        ];

        return view('daily_report/form', $data);
    }

    public function simpan()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');

        $tglMulai   = $this->request->getPost('tanggal_mulai') ?: sprintf('%04d-%02d-01', $tahun, $bulan);
        $tglSelesai = $this->request->getPost('tanggal_selesai') ?: $tglMulai;

        if ($tglSelesai < $tglMulai) {
            // Jika terbalik, sesuaikan agar mulai <= selesai
            $tmp = $tglMulai;
            $tglMulai = $tglSelesai;
            $tglSelesai = $tmp;
        }

        $lokasiIdInput   = (int)$this->request->getPost('lokasi_id');
        $namaLokasiInput = (string)$this->request->getPost('nama_lokasi_input');
        $finalLokasiId   = $this->lokasiModel->findOrCreate($namaLokasiInput, $lokasiIdInput);

        if ($finalLokasiId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Silakan ketik atau pilih Nama Sumur / Lokasi.');
        }

        $dailyReportId = $this->dailyReportModel->insert([
            'rig_id'          => $rigId,
            'lokasi_id'       => $finalLokasiId,
            'no_well'         => (int)$this->request->getPost('no_well'),
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'jarak'           => (float)str_replace(',', '.', (string)$this->request->getPost('jarak')),
            'miru_jam'        => 0,
            'ops_jam'         => 0,
            'total_dt'        => 0,
            'total_jam'       => 0,
            'status_job'      => $this->request->getPost('status_job') ?: 'JOB PROGRESS',
            'remark'          => null,
            'remark_unpaid'   => null,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

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
            "Mendaftarkan pekerjaan sumur No. {$noWell} pada {$rigKode} (Jadwal: {$tglMulai} s/d {$tglSelesai}).",
            $rigId
        );

        $redirectAction = $this->request->getPost('redirect_action') ?? 'log_harian';
        if ($redirectAction === 'log_harian') {
            return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$dailyReportId}&tanggal=" . urlencode($tglMulai)))
                ->with('success', "Sumur #{$noWell} (Jadwal: " . date('d/m/Y', strtotime($tglMulai)) . " s/d " . date('d/m/Y', strtotime($tglSelesai)) . ") berhasil didaftarkan! Silakan isi jam MIRU, OPS, & Downtime sesuai tanggal tersebut.");
        }

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', "Sumur #{$noWell} berhasil didaftarkan (Jadwal: " . date('d/m/Y', strtotime($tglMulai)) . " s/d " . date('d/m/Y', strtotime($tglSelesai)) . ").");
    }

    public function detail(int $id)
    {
        $this->recalcParentWell($id);
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
        $this->recalcParentWell($id);
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
            'page_subtitle' => "Perbarui identitas lokasi, jarak, dan rentang tanggal pengerjaan sumur",
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

        $tglMulai   = $this->request->getPost('tanggal_mulai') ?: $report['tanggal_mulai'];
        $tglSelesai = $this->request->getPost('tanggal_selesai') ?: $tglMulai;
        if (!empty($tglMulai) && !empty($tglSelesai) && $tglSelesai < $tglMulai) {
            $tmp = $tglMulai;
            $tglMulai = $tglSelesai;
            $tglSelesai = $tmp;
        }

        $lokasiIdInput   = (int)$this->request->getPost('lokasi_id');
        $namaLokasiInput = (string)$this->request->getPost('nama_lokasi_input');
        $finalLokasiId   = $this->lokasiModel->findOrCreate($namaLokasiInput, $lokasiIdInput);
        if ($finalLokasiId <= 0) {
            $finalLokasiId = (int)$report['lokasi_id'];
        }

        $newJarak = (float)str_replace(',', '.', (string)$this->request->getPost('jarak'));
        $updateFields = [
            'lokasi_id'       => $finalLokasiId,
            'no_well'         => (int)$this->request->getPost('no_well'),
            'tanggal_mulai'   => $tglMulai ?: null,
            'tanggal_selesai' => $tglSelesai ?: null,
            'jarak'           => $newJarak,
        ];

        if ($this->request->getPost('status_job')) {
            $updateFields['status_job'] = $this->request->getPost('status_job');
        }

        $this->dailyReportModel->update($id, $updateFields);

        // Sinkronkan jarak ke log pertama sumur agar recalcParentWell tidak menimpa jarak baru
        $db = \Config\Database::connect();
        $firstLog = $db->table('daily_report_log')->where('daily_report_id', $id)->orderBy('tanggal', 'ASC')->get()->getRowArray();
        if ($firstLog) {
            $db->table('daily_report_log')->where('id', $firstLog['id'])->update(['jarak' => $newJarak]);
        }

        // Pastikan total jam & rincian DT tetap dihitung akurat dari daily_report_log
        $this->recalcParentWell($id);

        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($report['rig_id'], $report['bulan'], $report['tahun']);

        // Catat jejak audit aktivitas
        $updatedReport = $this->dailyReportModel->find($id);
        $totalJam = (float)($updatedReport['total_jam'] ?? 0);
        $rig = $this->rigModel->find($report['rig_id']);
        $rigKode = $rig['kode'] ?? "Rig #{$report['rig_id']}";
        $noWell = (int)$this->request->getPost('no_well');
        ActivityLogModel::record(
            'DAILY_REPORT',
            'UPDATE_SUMUR',
            "Memperbarui data pekerjaan sumur No. {$noWell} pada {$rigKode} (Jadwal: {$tglMulai} s/d {$tglSelesai}, Total Jam: {$totalJam}j).",
            $report['rig_id']
        );

        return redirect()->to(base_url("daily-report/{$report['rig_id']}/{$report['bulan']}/{$report['tahun']}"))->with('success', 'Data pekerjaan sumur berhasil diperbarui.');
    }

    public function hapus(int $id)
    {
        $report = $this->dailyReportModel->find($id);
        if ($report) {
            $rigId = (int)$report['rig_id'];
            $bulan = (int)$report['bulan'];
            $tahun = (int)$report['tahun'];
            $db = \Config\Database::connect();

            // 1. Ambil seluruh tanggal log yang tercatat pada sumur ini
            $logs = $db->table('daily_report_log')
                ->select('tanggal')
                ->where('daily_report_id', $id)
                ->get()->getResultArray();
            $logDates = array_unique(array_filter(array_column($logs, 'tanggal')));

            // 2. Hapus seluruh baris log harian milik sumur ini
            $db->table('daily_report_log')->where('daily_report_id', $id)->delete();

            // 3. Hapus rincian daily_report_dt & header sumur
            $this->dailyReportDtModel->where('daily_report_id', $id)->delete();
            $this->dailyReportModel->delete($id);

            // 4. Sinkronkan ulang NPT Harian untuk setiap tanggal terkait (hapus NPT jika tidak ada sumur lain)
            foreach ($logDates as $tgl) {
                $this->syncRigDateToNpt($rigId, $tgl);
                // Pastikan jika tidak ada lagi log harian di tanggal tersebut, npt_harian bersih total
                $remainingLogs = $db->table('daily_report_log drl')
                    ->join('daily_report dr', 'dr.id = drl.daily_report_id')
                    ->where('dr.rig_id', $rigId)
                    ->where('drl.tanggal', $tgl)
                    ->countAllResults();
                if ($remainingLogs === 0) {
                    $db->table('npt_harian')->where('rig_id', $rigId)->where('tanggal', $tgl)->delete();
                }
            }

            // Jika sumur di bulan ini sudah kosong, pastikan npt_harian bulan ini juga bersih
            $remainingWells = $this->dailyReportModel->where('rig_id', $rigId)->where('bulan', $bulan)->where('tahun', $tahun)->countAllResults();
            if ($remainingWells === 0) {
                $db->table('npt_harian')
                    ->where('rig_id', $rigId)
                    ->where('MONTH(tanggal)', $bulan)
                    ->where('YEAR(tanggal)', $tahun)
                    ->delete();
            }

            // 5. Rekalkulasi Monthly Summary
            $summaryModel = new MonthlySummaryModel();
            $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

            // Catat jejak audit aktivitas
            $rig = $this->rigModel->find($rigId);
            $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
            ActivityLogModel::record(
                'DAILY_REPORT',
                'HAPUS_SUMUR',
                "Menghapus data pekerjaan sumur No. {$report['no_well']} pada {$rigKode} (Periode " . sprintf('%02d/%04d', $bulan, $tahun) . ") dan menyinkronkan NPT.",
                $rigId
            );

            return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Pekerjaan sumur dan seluruh log/NPT terkait berhasil dihapus & disinkronkan.');
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

        // Batch query seluruh tp_breakdown bulan ini dalam 1 query (menghilangkan N+1 query loop)
        $tpData = $db->table('npt_harian')
            ->where('rig_id', $rigId)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->where('kategori_id', 10)
            ->get()->getResultArray();

        $tpMapByDate = [];
        foreach ($tpData as $t) {
            $tDate = $t['tanggal'];
            $tpKey = $t['third_party_id'] !== null ? (int)$t['third_party_id'] : 0;
            $tpMapByDate[$tDate][$tpKey] = ($tpMapByDate[$tDate][$tpKey] ?? 0.0) + (float)$t['jam'];
        }

        // Susun rincian log harian
        foreach ($recentLogs as &$rl) {
            $rl['unpaid_hrs'] = (float)($rl['dt_rig'] ?? 0) + (float)($rl['dt_tool'] ?? 0);
            $rl['sbwc_hrs']   = max(0.0, (float)($rl['total_dt'] ?? 0) - $rl['unpaid_hrs']);
            $rl['tp_breakdown'] = [];
            if ((float)$rl['dt_3rd_party'] > 0) {
                if (!empty($tpMapByDate[$rl['tanggal']])) {
                    $rl['tp_breakdown'] = $tpMapByDate[$rl['tanggal']];
                } else {
                    $rl['tp_breakdown'][0] = (float)$rl['dt_3rd_party'];
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
            'rigs'          => $allRigs,
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

        // Validasi Tanggal Laporan terhadap Rentang Tanggal Sumur (tanggal_mulai s/d tanggal_selesai)
        $well = $this->dailyReportModel->find($repId);
        if (!$well) {
            return redirect()->back()->with('error', 'Sumur tidak ditemukan.');
        }

        $wellStart = !empty($well['tanggal_mulai']) ? $well['tanggal_mulai'] : null;
        $wellEnd   = !empty($well['tanggal_selesai']) ? $well['tanggal_selesai'] : null;

        if ($wellStart && $tanggal < $wellStart) {
            $fmtStart = date('d/m/Y', strtotime($wellStart));
            $fmtInput = date('d/m/Y', strtotime($tanggal));
            return redirect()->back()->withInput()->with('error', "Gagal menyimpan: Tanggal {$fmtInput} mendahului tanggal mulai Sumur #{$well['no_well']} ({$fmtStart}).");
        }

        // Kaidah Operasional Lapangan:
        // Pekerjaan sumur tidak menentu kapan habisnya dan berlanjut terus hari demi hari.
        // Jika tanggal log lebih besar dari tanggal_selesai sebelumnya (atau tanggal_selesai belum diset),
        // otomatis perpanjang tanggal_selesai sumur agar mencakup tanggal log ini.
        if (empty($wellEnd) || $tanggal > $wellEnd) {
            $this->dailyReportModel->update($well['id'], [
                'tanggal_selesai' => $tanggal,
            ]);
            $well['tanggal_selesai'] = $tanggal;
            $wellEnd = $tanggal;
        }

        $toDec = static fn($v): float => (float)str_replace(',', '.', (string)$v);

        $jarak      = $toDec($this->request->getPost('jarak'));
        $miruJam    = $toDec($this->request->getPost('miru_jam'));
        $opsJam     = $toDec($this->request->getPost('ops_jam'));

        // Pos SBWC
        $dtRain     = $toDec($this->request->getPost('dt_rain'));
        $dtDryRoad  = $toDec($this->request->getPost('dt_dry_road'));
        $dtDryPad   = $toDec($this->request->getPost('dt_dry_pad'));
        $dtPhrOp    = $toDec($this->request->getPost('dt_phr_op'));
        $dtTrans    = $toDec($this->request->getPost('dt_trans'));
        $dtCePe     = $toDec($this->request->getPost('dt_ce_pe'));
        $dt3rdParty = $toDec($this->request->getPost('dt_3rd_party'));
        $dtDaylight = $toDec($this->request->getPost('dt_daylight'));
        $dtPhrWell  = $toDec($this->request->getPost('dt_phr_well'));
        $dtFoam     = $toDec($this->request->getPost('dt_foam'));

        // Pos UNPAID
        $dtRig      = $toDec($this->request->getPost('dt_rig'));
        $dtTool     = $toDec($this->request->getPost('dt_tool'));

        // Shutdown
        $dtShutdown = $toDec($this->request->getPost('dt_shutdown'));

        // Proses Rincian 3rd Party (Bisa Tanpa Perusahaan [id=0] maupun Pilih Perusahaan [id>0])
        $tpCompanies = $this->request->getPost('tp_company') ?? [];
        $tpHours     = $this->request->getPost('tp_hours') ?? [];
        $tpJam       = [];
        $sumTpHours  = 0.0;

        if (is_array($tpCompanies) && is_array($tpHours)) {
            foreach ($tpCompanies as $idx => $compId) {
                $hrs = $toDec($tpHours[$idx] ?? 0);
                if ($hrs > 0) {
                    $cKey = (int)$compId; // 0 = Tanpa Perusahaan (NULL), >0 = ID Perusahaan
                    $tpJam[$cKey] = ($tpJam[$cKey] ?? 0.0) + $hrs;
                    $sumTpHours += $hrs;
                }
            }
        }

        // Jika ada rincian baris 3rd party, sinkronkan dt3rdParty dengan total rinciannya
        if ($sumTpHours > 0) {
            $dt3rdParty = $sumTpHours;
        } elseif ($dt3rdParty > 0 && empty($tpJam)) {
            // Jika diisi langsung angka total tanpa rincian baris, catat sebagai Tanpa Perusahaan (key 0)
            $tpJam[0] = $dt3rdParty;
        }

        $totalDt  = $dtRain + $dtDryRoad + $dtDryPad + $dtPhrOp + $dtTrans + $dtCePe + $dt3rdParty + $dtDaylight + $dtPhrWell + $dtFoam + $dtRig + $dtTool + $dtShutdown;
        $totalHrs = $miruJam + $opsJam + $totalDt;

        $db = \Config\Database::connect();

        // Cek apakah ada sumur lain (Lokasi A) pada rig & tanggal yang sama (Transisi Pindah Sumur di Hari yang Sama)
        $otherWellsRow = $db->table('daily_report_log drl')
            ->select('SUM(drl.total_hrs) as other_hrs, GROUP_CONCAT(CONCAT("Well #", dr.no_well, " (", drl.total_hrs, "j)") SEPARATOR ", ") as info_wells')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('drl.tanggal', $tanggal)
            ->where('drl.daily_report_id !=', $repId)
            ->get()->getRowArray();

        $otherWellsHrs = (float)($otherWellsRow['other_hrs'] ?? 0.0);

        if ($totalHrs > 24.001) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: Total jam harian (' . number_format($totalHrs, 2) . ' Jam) tidak boleh melebihi 24.00 Jam!');
        }

        if (($otherWellsHrs + $totalHrs) > 24.001) {
            $sisaMaks = max(0, 24.0 - $otherWellsHrs);
            $fmtTgl   = date('d/m/Y', strtotime($tanggal));
            return redirect()->back()->withInput()->with('error', "Gagal menyimpan: Pada tanggal {$fmtTgl} sudah tercatat {$otherWellsRow['info_wells']}. Sisa waktu maksimal untuk Sumur #{$well['no_well']} di tanggal ini adalah " . number_format($sisaMaks, 2) . " Jam (Total gabungan hari ini tidak boleh lewat 24.00 Jam)!");
        }

        $remarkNpt    = trim((string)$this->request->getPost('remark_npt'));
        $remarkUnpaid = trim((string)$this->request->getPost('remark_unpaid'));

        // Format otomatis standar Besmindo (Contoh: "0,5 HR SWA Heavy Rain + Thunder. 5,5 HR WO Dry Road.")
        $fmtHr = static function (float $val): string {
            $rounded = round($val, 2);
            return str_replace('.', ',', (string)$rounded);
        };

        $autoNptParts = [];
        $autoUnpaidParts = [];

        if ($dtRain > 0)     $autoNptParts[] = $fmtHr($dtRain) . ' HR SWA Heavy Rain + Thunder.';
        if ($dtDryRoad > 0)  $autoNptParts[] = $fmtHr($dtDryRoad) . ' HR WO Dry Road.';
        if ($dtDryPad > 0)   $autoNptParts[] = $fmtHr($dtDryPad) . ' HR WO Dry Well Pad.';
        if ($dtPhrOp > 0)    $autoNptParts[] = $fmtHr($dtPhrOp) . ' HR WO PHR Operator.';
        if ($dtTrans > 0)    $autoNptParts[] = $fmtHr($dtTrans) . ' HR WO Trans Sharing.';
        if ($dtCePe > 0)     $autoNptParts[] = $fmtHr($dtCePe) . ' HR WO CE/PE.';

        if ($dt3rdParty > 0) {
            if (!empty($tpJam)) {
                $tpRowsDb = $db->table('third_parties')->get()->getResultArray();
                $tpNameMap = [];
                foreach ($tpRowsDb as $tr) {
                    $tpNameMap[(int)$tr['id']] = $tr['nama'];
                }
                foreach ($tpJam as $tpIdKey => $hrsVal) {
                    if ($hrsVal > 0) {
                        $tpName = $tpNameMap[(int)$tpIdKey] ?? '';
                        if ($tpName !== '') {
                            $autoNptParts[] = $fmtHr((float)$hrsVal) . " HR WO 3rd Party ({$tpName}).";
                        } else {
                            $autoNptParts[] = $fmtHr((float)$hrsVal) . ' HR WO 3rd Party.';
                        }
                    }
                }
            } else {
                $autoNptParts[] = $fmtHr($dt3rdParty) . ' HR WO 3rd Party.';
            }
        }

        if ($dtDaylight > 0) $autoNptParts[] = $fmtHr($dtDaylight) . ' HR WO Daylight.';
        if ($dtPhrWell > 0)  $autoNptParts[] = $fmtHr($dtPhrWell) . ' HR WO PHR Well & Accessories.';
        if ($dtFoam > 0)     $autoNptParts[] = $fmtHr($dtFoam) . ' HR WO Foam Unit.';
        if ($dtShutdown > 0) $autoNptParts[] = $fmtHr($dtShutdown) . ' HR SWA Shut Down.';

        if ($dtRig > 0) {
            $p = $fmtHr($dtRig) . ' HR Repair Rig.';
            $autoNptParts[] = $p;
            $autoUnpaidParts[] = $p;
        }
        if ($dtTool > 0) {
            $p = $fmtHr($dtTool) . ' HR WO BMS Tool.';
            $autoNptParts[] = $p;
            $autoUnpaidParts[] = $p;
        }

        $autoNptStr    = implode(' ', $autoNptParts);
        $autoUnpaidStr = implode(' ', $autoUnpaidParts);

        // 1. Jika user mengisi/mengubah Remark Unpaid secara manual (bukan template otomatis),
        //    sedangkan Remark NPT masih berisi template otomatis, sinkronkan ke Remark NPT
        if ($remarkUnpaid !== '' && $remarkUnpaid !== $autoUnpaidStr && ($remarkNpt === '' || $remarkNpt === $autoNptStr)) {
            if ($autoUnpaidStr !== '' && $remarkNpt !== '' && strpos($remarkNpt, $autoUnpaidStr) !== false) {
                $remarkNpt = str_replace($autoUnpaidStr, $remarkUnpaid, $remarkNpt);
            } else {
                $remarkNpt = $remarkUnpaid;
            }
        }

        // 2. Jika user mengisi/mengubah Remark NPT secara manual (bukan template otomatis),
        //    jangan biarkan Remark Unpaid tetap menyimpan template otomatis ("1 HR Repair Rig.")
        if ($remarkNpt !== '' && $remarkNpt !== $autoNptStr && ($remarkUnpaid === '' || $remarkUnpaid === $autoUnpaidStr)) {
            $remarkUnpaid = ($dtRig > 0 || $dtTool > 0) ? $remarkNpt : '';
        }

        // 3. Hanya isi dengan template otomatis JIKA kolom remark benar-benar dikosongkan oleh user
        if ($remarkNpt === '' && $autoNptStr !== '') {
            $remarkNpt = $autoNptStr;
        }
        if ($remarkUnpaid === '' && $autoUnpaidStr !== '') {
            $remarkUnpaid = $autoUnpaidStr;
        } elseif ($dtRig <= 0 && $dtTool <= 0 && $remarkUnpaid === $autoUnpaidStr) {
            $remarkUnpaid = '';
        }

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

        $dbData = $logData;
        unset($dbData['tp_jam']);

        // PENTING: Cari apakah sudah ada log pada tanggal ini KHUSUS UNTUK SUMUR INI ($repId)
        // Agar jika Lokasi A & Lokasi B dikerjakan di tanggal yang sama (misal tgl 1), Lokasi B TIDAK menimpa Lokasi A!
        $existing = $db->table('daily_report_log')
            ->where('daily_report_id', $repId)
            ->where('tanggal', $tanggal)
            ->get()->getRowArray();

        if ($existing) {
            $db->table('daily_report_log')->where('id', $existing['id'])->update($dbData);
        } else {
            $db->table('daily_report_log')->insert($dbData);
        }

        // 1. REKALKULASI TOTAL SUMUR DI daily_report
        $this->recalcParentWell($repId);

        // Update status_job dan tanggal_selesai sumur
        $postedStatus     = strtoupper(trim((string)$this->request->getPost('status_job')));
        $selesaikanSumur  = (string)$this->request->getPost('selesaikan_sumur');
        $afterSaveAction  = (string)$this->request->getPost('after_save_action');
        $isCompletingWell = ($selesaikanSumur === '1' || $afterSaveAction === 'goto_tambah_sumur');

        if ($isCompletingWell) {
            $statusToSet = (!empty($postedStatus) && in_array($postedStatus, ['JOB COMPLETED', 'JOB SUSPEND'], true)) ? $postedStatus : 'JOB COMPLETED';
            $updateWell = [
                'status_job' => $statusToSet,
            ];
            // Tetapkan tanggal selesai ke tanggal log ini jika belum ada atau log lebih baru
            $wStart = $well['tanggal_mulai'] ?? $tanggal;
            $updateWell['tanggal_selesai'] = ($tanggal >= $wStart) ? $tanggal : ($well['tanggal_selesai'] ?? $tanggal);

            $this->dailyReportModel->update($repId, $updateWell);
            $this->recalcParentWell($repId);
        } elseif (in_array($postedStatus, ['JOB PROGRESS', 'JOB COMPLETED', 'JOB SUSPEND'], true)) {
            $this->dailyReportModel->update($repId, ['status_job' => $postedStatus]);
        }

        // Update remark pada header sumur jika diisi
        if (!empty($remarkUnpaid)) {
            $db->table('daily_report')->where('id', $repId)->update(['remark_unpaid' => $remarkUnpaid]);
        }
        if (!empty($remarkNpt)) {
            $db->table('daily_report')->where('id', $repId)->update(['remark' => $remarkNpt]);
        }

        // 2. AUTO-SYNC KE NPT HARIAN (Gabungkan semua log sumur pada tanggal & rig yang sama)
        $this->syncRigDateToNpt($rigId, $tanggal, $tpJam);

        // 3. AUTO-SYNC KE MONTHLY SUMMARY
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        // Catat jejak audit aktivitas
        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        $activityText = $isCompletingWell
            ? "Mencatat log harian tanggal {$tanggal} dan MENYELESAIKAN pekerjaan sumur No. {$well['no_well']} pada {$rigKode} (" . ($statusToSet ?? 'JOB COMPLETED') . ", MIRU: {$miruJam}j, OPS: {$opsJam}j, Total DT: {$totalDt}j, Total: {$totalHrs}j)."
            : "Mencatat log operasi harian tanggal {$tanggal} pada {$rigKode} Sumur #{$well['no_well']} (MIRU: {$miruJam}j, OPS: {$opsJam}j, Total DT: {$totalDt}j, Total: {$totalHrs}j).";
        ActivityLogModel::record(
            'DAILY_REPORT',
            $isCompletingWell ? 'SELESAIKAN_SUMUR' : 'SIMPAN_LOG_HARIAN',
            $activityText,
            $rigId
        );

        if ($isCompletingWell || $afterSaveAction === 'open_new_well') {
            $lokasi = $this->lokasiModel->find($well['lokasi_id'] ?? 0);
            $namaLokasi = $lokasi['nama_lokasi'] ?? ('Sumur #' . $well['no_well']);
            $finalStatus = $statusToSet ?? 'JOB COMPLETED';
            return redirect()->to(base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}"))
                ->with('success', "Log tanggal " . date('d/m/Y', strtotime($tanggal)) . " ({$totalHrs} Jam) berhasil disimpan dan Sumur #{$well['no_well']} ({$namaLokasi}) resmi diselesaikan ({$finalStatus})! Silakan daftarkan Sumur Baru (Well berikutnya) di bawah ini.");
        }

        $queryExtra = '';
        if (!empty($wellEnd) && $tanggal >= $wellEnd) {
            $queryExtra = '&well_completed_prompt=1';
        }

        return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$repId}{$queryExtra}"))
            ->with('success', "Log tanggal " . date('d/m/Y', strtotime($tanggal)) . " untuk Sumur #{$well['no_well']} ({$totalHrs} Jam) berhasil disimpan!");
    }

    /**
     * Helper: Gabungkan seluruh daily_report_log pada ($rigId, $tanggal) lintas sumur lalu sinkronkan ke npt_harian
     */
    private function syncRigDateToNpt(int $rigId, string $tanggal, array $latestTpJam = []): void
    {
        $db = \Config\Database::connect();
        $rowsOnDate = $db->table('daily_report_log drl')
            ->select('drl.*')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('drl.tanggal', $tanggal)
            ->get()->getResultArray();

        $aggLog = [
            'tanggal'       => $tanggal,
            'dt_rain'       => 0.0,
            'dt_dry_road'   => 0.0,
            'dt_dry_pad'    => 0.0,
            'dt_phr_op'     => 0.0,
            'dt_trans'      => 0.0,
            'dt_ce_pe'      => 0.0,
            'dt_3rd_party'  => 0.0,
            'dt_daylight'   => 0.0,
            'dt_phr_well'   => 0.0,
            'dt_foam'       => 0.0,
            'dt_rig'        => 0.0,
            'dt_tool'       => 0.0,
            'dt_shutdown'   => 0.0,
            'remark_npt'    => null,
            'remark_unpaid' => null,
            'tp_jam'        => $latestTpJam,
        ];

        $remarksNpt = [];
        $remarksUnpaid = [];
        foreach ($rowsOnDate as $r) {
            $aggLog['dt_rain']      += (float)$r['dt_rain'];
            $aggLog['dt_dry_road']  += (float)$r['dt_dry_road'];
            $aggLog['dt_dry_pad']   += (float)$r['dt_dry_pad'];
            $aggLog['dt_phr_op']    += (float)$r['dt_phr_op'];
            $aggLog['dt_trans']     += (float)$r['dt_trans'];
            $aggLog['dt_ce_pe']     += (float)$r['dt_ce_pe'];
            $aggLog['dt_3rd_party'] += (float)$r['dt_3rd_party'];
            $aggLog['dt_daylight']  += (float)$r['dt_daylight'];
            $aggLog['dt_phr_well']  += (float)$r['dt_phr_well'];
            $aggLog['dt_foam']      += (float)$r['dt_foam'];
            $aggLog['dt_rig']       += (float)$r['dt_rig'];
            $aggLog['dt_tool']      += (float)$r['dt_tool'];
            $aggLog['dt_shutdown']  += (float)$r['dt_shutdown'];
            if (!empty($r['remark_npt'])) {
                $remarksNpt[] = $r['remark_npt'];
            }
            if (!empty($r['remark_unpaid'])) {
                $remarksUnpaid[] = $r['remark_unpaid'];
            }
        }

        if (!empty($remarksNpt)) {
            $aggLog['remark_npt'] = implode(' | ', array_unique($remarksNpt));
        }
        if (!empty($remarksUnpaid)) {
            $aggLog['remark_unpaid'] = implode(' | ', array_unique($remarksUnpaid));
        }

        $this->nptModel->syncFromDailyLog($rigId, $aggLog);
    }

    /**
     * Helper: Hitung ulang subtotal jam di tabel parent daily_report & daily_report_dt berdasarkan semua daily_report_log miliknya
     */
    private function recalcParentWell(int $wellId): void
    {
        $db = \Config\Database::connect();
        $allLogs = $db->table('daily_report_log')
            ->where('daily_report_id', $wellId)
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        $sumMiru = 0.0;
        $sumOps  = 0.0;
        $sumDt   = 0.0;
        $maxJarak = 0.0;

        // Akumulasi per kategori_id untuk daily_report_dt
        $katSums = [
            1  => 0.0, // Repair Rig (UNPAID)
            2  => 0.0, // Tool / Personnel (UNPAID)
            3  => 0.0, // Rain
            4  => 0.0, // Dry Road
            5  => 0.0, // Dry Pad
            6  => 0.0, // Daylight
            8  => 0.0, // PHR Well
            10 => 0.0, // 3rd Party
            11 => 0.0, // Trans Sharing
            12 => 0.0, // Foam
            13 => 0.0, // PHR Operator
            14 => 0.0, // CE/PE
            15 => 0.0, // Shutdown
        ];

        $firstDate = null;
        foreach ($allLogs as $al) {
            if ($firstDate === null && !empty($al['tanggal'])) {
                $firstDate = $al['tanggal'];
            }
            $sumMiru  += (float)$al['miru_jam'];
            $sumOps   += (float)$al['ops_jam'];
            $sumDt    += (float)$al['total_dt'];
            if ((float)$al['jarak'] > $maxJarak) {
                $maxJarak = (float)$al['jarak'];
            }

            $katSums[1]  += (float)($al['dt_rig'] ?? 0);
            $katSums[2]  += (float)($al['dt_tool'] ?? 0);
            $katSums[3]  += (float)($al['dt_rain'] ?? 0);
            $katSums[4]  += (float)($al['dt_dry_road'] ?? 0);
            $katSums[5]  += (float)($al['dt_dry_pad'] ?? 0);
            $katSums[6]  += (float)($al['dt_daylight'] ?? 0);
            $katSums[8]  += (float)($al['dt_phr_well'] ?? 0);
            $katSums[10] += (float)($al['dt_3rd_party'] ?? 0);
            $katSums[11] += (float)($al['dt_trans'] ?? 0);
            $katSums[12] += (float)($al['dt_foam'] ?? 0);
            $katSums[13] += (float)($al['dt_phr_op'] ?? 0);
            $katSums[14] += (float)($al['dt_ce_pe'] ?? 0);
            $katSums[15] += (float)($al['dt_shutdown'] ?? 0);
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

        // Sinkronkan rincian daily_report_dt agar SBWC & UNPAID muncul akurat di tabel /daily-report
        $db->table('daily_report_dt')->where('daily_report_id', $wellId)->delete();
        $tglRef = $firstDate ?: date('Y-m-d');
        foreach ($katSums as $kId => $jamTotal) {
            if ($jamTotal > 0) {
                $db->table('daily_report_dt')->insert([
                    'daily_report_id' => $wellId,
                    'tanggal'         => $tglRef,
                    'kategori_id'     => (int)$kId,
                    'jam'             => $jamTotal,
                ]);
            }
        }
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

        // 3. Sinkronkan ulang NPT Harian untuk tanggal ini dari sisa sumur lain (jika ada) atau reset ke 0
        $this->syncRigDateToNpt($rigId, $tanggal);

        $remainingLogs = $db->table('daily_report_log drl')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('drl.tanggal', $tanggal)
            ->countAllResults();
        if ($remainingLogs === 0) {
            $db->table('npt_harian')->where('rig_id', $rigId)->where('tanggal', $tanggal)->delete();
        }

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

        return redirect()->to(base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$wellId}"))
            ->with('success', "Log tanggal {$tanggal} berhasil dihapus. Subtotal sumur & NPT telah disinkronkan kembali.");
    }

    public function simpanSumurCepat()
    {
        $rigId           = (int)$this->request->getPost('rig_id');
        $bulan           = (int)$this->request->getPost('bulan');
        $tahun           = (int)$this->request->getPost('tahun');
        $noWell          = (int)$this->request->getPost('no_well');
        $lokasiIdInput   = (int)$this->request->getPost('lokasi_id');
        $namaLokasiInput = (string)$this->request->getPost('nama_lokasi_input');
        $jarak           = (float)str_replace(',', '.', (string)$this->request->getPost('jarak'));
        $tglMulai        = $this->request->getPost('tanggal_mulai') ?: sprintf('%04d-%02d-01', $tahun, $bulan);
        $tglSelesai      = $this->request->getPost('tanggal_selesai') ?: $tglMulai;

        if ($tglSelesai < $tglMulai) {
            $tmp = $tglMulai;
            $tglMulai = $tglSelesai;
            $tglSelesai = $tmp;
        }

        $lokasiModel   = new \App\Models\LokasiModel();
        $finalLokasiId = $lokasiModel->findOrCreate($namaLokasiInput, $lokasiIdInput);

        if (!$rigId || !$bulan || !$tahun || !$noWell || $finalLokasiId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan isi No. Well dan ketik/pilih Nama Lokasi Sumur!']);
        }

        $lokasi = $lokasiModel->find($finalLokasiId);

        $db = \Config\Database::connect();
        
        $data = [
            'rig_id'          => $rigId,
            'lokasi_id'       => $finalLokasiId,
            'no_well'         => $noWell,
            'jarak'           => $jarak,
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'status_job'      => 'JOB PROGRESS',
            'created_at'      => date('Y-m-d H:i:s')
        ];
        
        try {
            $db->table('daily_report')->insert($data);
            $newId = (int)$db->insertID();

            // Sinkronisasi ke Monthly Summary & Activity Log
            $summaryModel = new MonthlySummaryModel();
            $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

            $rig = $this->rigModel->find($rigId);
            $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
            $namaLokFinal = $lokasi['nama_lokasi'] ?? strtoupper(trim($namaLokasiInput));
            ActivityLogModel::record(
                'DAILY_REPORT',
                'TAMBAH_SUMUR_CEPAT',
                "Mendaftarkan pekerjaan sumur No. {$noWell} ({$namaLokFinal}) pada {$rigKode} (Jadwal: {$tglMulai} s/d {$tglSelesai}).",
                $rigId
            );

            return $this->response->setJSON([
                'status'          => 'success',
                'id'              => $newId,
                'lokasi_id'       => $finalLokasiId,
                'no_well'         => $noWell,
                'nama_lokasi'     => $namaLokFinal,
                'jarak'           => $jarak,
                'tanggal_mulai'   => $tglMulai,
                'tanggal_selesai' => $tglSelesai,
                'status_job'      => 'JOB PROGRESS',
                'csrf_hash'       => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => $e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    /**
     * SELESAIKAN SUMUR (Pengguna Menentukan Sendiri Tanggal Berakhir Ketika Inputan Selesai)
     */
    public function selesaikanSumur(int $wellId)
    {
        $well = $this->dailyReportModel->find($wellId);
        if (!$well) {
            return redirect()->back()->with('error', 'Pekerjaan sumur tidak ditemukan.');
        }

        $tglSelesai = $this->request->getPost('tanggal_selesai') ?: date('Y-m-d');
        $statusJob  = $this->request->getPost('status_job') ?: 'JOB COMPLETED';

        $this->dailyReportModel->update($wellId, [
            'tanggal_selesai' => $tglSelesai,
            'status_job'      => $statusJob,
        ]);

        // Rekalkulasi Monthly Summary
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan((int)$well['rig_id'], (int)$well['bulan'], (int)$well['tahun']);

        $rig = $this->rigModel->find($well['rig_id']);
        $rigKode = $rig['kode'] ?? "Rig #{$well['rig_id']}";
        ActivityLogModel::record(
            'DAILY_REPORT',
            'SELESAIKAN_SUMUR',
            "Menyelesaikan pekerjaan sumur No. {$well['no_well']} pada {$rigKode} (Jadwal selesai: " . date('d/m/Y', strtotime($tglSelesai)) . ", Status: {$statusJob}).",
            (int)$well['rig_id']
        );

        $lokasi = $this->lokasiModel->find($well['lokasi_id'] ?? 0);
        $namaLokasi = $lokasi['nama_lokasi'] ?? ('Sumur #' . $well['no_well']);
        return redirect()->to(base_url("daily-report/tambah/{$well['rig_id']}/{$well['bulan']}/{$well['tahun']}"))
            ->with('success', "Pekerjaan Sumur #{$well['no_well']} ({$namaLokasi}) berhasil diselesaikan ({$statusJob}) pada tanggal " . date('d/m/Y', strtotime($tglSelesai)) . "! Silakan daftarkan Sumur Baru (Well berikutnya) di bawah ini.");
    }

    private function getReportMeta(int $rigId, int $bulan, int $tahun): array
    {
        $db = \Config\Database::connect();
        $row = $db->table('monthly_summary')
            ->where('rig_id', $rigId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()->getRowArray();

        $default = [
            'schedule_mtc' => 0.0,
            'notes'        => [],
        ];

        if (!$row || empty($row['remark'])) {
            return $default;
        }

        $decoded = json_decode((string)$row['remark'], true);
        if (is_array($decoded) && (isset($decoded['notes']) || isset($decoded['schedule_mtc']))) {
            return [
                'schedule_mtc' => (float)($decoded['schedule_mtc'] ?? 0.0),
                'notes'        => is_array($decoded['notes'] ?? null) ? array_values($decoded['notes']) : [],
            ];
        }

        $plain = trim((string)$row['remark']);
        if ($plain !== '') {
            $default['notes'][] = [
                'id'      => 'note_legacy_1',
                'lokasi'  => 'CATATAN LAPORAN',
                'tanggal' => sprintf('%02d/%04d', $bulan, $tahun),
                'isi'     => $plain,
                'waktu'   => '',
            ];
        }

        return $default;
    }

    private function saveReportMeta(int $rigId, int $bulan, int $tahun, array $meta): void
    {
        $summaryModel = new MonthlySummaryModel();
        $existing = $summaryModel->where('rig_id', $rigId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if (!$existing) {
            $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);
            $existing = $summaryModel->where('rig_id', $rigId)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();
        }

        $jsonStr = json_encode([
            'schedule_mtc' => (float)($meta['schedule_mtc'] ?? 0.0),
            'notes'        => array_values($meta['notes'] ?? []),
        ], JSON_UNESCAPED_UNICODE);

        if ($existing) {
            $summaryModel->update($existing['id'], ['remark' => $jsonStr]);
        }
    }

    public function simpanCatatan()
    {
        $rigId   = (int)$this->request->getPost('rig_id');
        $bulan   = (int)$this->request->getPost('bulan');
        $tahun   = (int)$this->request->getPost('tahun');

        if (!$rigId || !$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Parameter periode laporan tidak valid.');
        }

        $meta = $this->getReportMeta($rigId, $bulan, $tahun);

        if ($this->request->getPost('schedule_mtc') !== null && $this->request->getPost('schedule_mtc') !== '') {
            $meta['schedule_mtc'] = max(0.0, (float)str_replace(',', '.', (string)$this->request->getPost('schedule_mtc')));
        }

        $lokasi  = trim((string)$this->request->getPost('note_lokasi'));
        $tanggal = trim((string)$this->request->getPost('note_tanggal'));
        $isi     = trim((string)$this->request->getPost('note_isi'));
        $waktu   = trim((string)$this->request->getPost('note_waktu'));
        $noteId  = trim((string)$this->request->getPost('note_id'));

        if ($isi !== '' || $lokasi !== '') {
            $newNote = [
                'id'      => $noteId !== '' ? $noteId : ('note_' . uniqid()),
                'lokasi'  => $lokasi !== '' ? $lokasi : 'CATATAN RIG',
                'tanggal' => $tanggal !== '' ? $tanggal : date('d-M'),
                'isi'     => $isi,
                'waktu'   => $waktu,
            ];

            $found = false;
            foreach ($meta['notes'] as $idx => $n) {
                if (($n['id'] ?? '') === $newNote['id']) {
                    $meta['notes'][$idx] = $newNote;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $meta['notes'][] = $newNote;
            }
        }

        $this->saveReportMeta($rigId, $bulan, $tahun, $meta);

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', 'Catatan / Note Laporan & Schedule MTC berhasil disimpan.');
    }

    public function hapusCatatan()
    {
        $rigId  = (int)$this->request->getPost('rig_id');
        $bulan  = (int)$this->request->getPost('bulan');
        $tahun  = (int)$this->request->getPost('tahun');
        $noteId = trim((string)$this->request->getPost('note_id'));

        if ($rigId && $bulan && $tahun && $noteId !== '') {
            $meta = $this->getReportMeta($rigId, $bulan, $tahun);
            $meta['notes'] = array_values(array_filter($meta['notes'], static fn($n) => ($n['id'] ?? '') !== $noteId));
            $this->saveReportMeta($rigId, $bulan, $tahun, $meta);
        }

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', 'Catatan laporan berhasil dihapus.');
    }

    public function updateOdr()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan') ?: (int)date('n');
        $tahun = (int)$this->request->getPost('tahun') ?: (int)date('Y');
        $rawOdr = (string)$this->request->getPost('odr');
        $cleanOdr = (int)preg_replace('/[^\d]/', '', $rawOdr);

        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->back()->with('error', 'Armada rig tidak ditemukan.');
        }

        if ($cleanOdr <= 0) {
            return redirect()->back()->with('error', 'Nilai tarif ODR harus berupa angka lebih dari 0.');
        }

        // Simpan ODR baru ke tabel rigs
        $this->rigModel->update($rigId, ['odr' => $cleanOdr]);

        // Rekalkulasi Monthly Summary seketika agar langsung sinkron
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))
            ->with('success', "Tarif ODR {$rig['kode']} berhasil diubah menjadi Rp " . number_format($cleanOdr, 0, ',', '.') . " dan otomatis disinkronkan ke seluruh ringkasan KPI!");
    }
}
