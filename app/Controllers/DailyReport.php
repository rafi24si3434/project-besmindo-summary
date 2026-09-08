<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\LokasiModel;
use App\Models\DailyReportModel;
use App\Models\DailyReportDtModel;
use App\Models\MonthlySummaryModel;

class DailyReport extends BaseController
{
    protected $rigModel;
    protected $lokasiModel;
    protected $dailyReportModel;
    protected $dailyReportDtModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
        $this->lokasiModel = new LokasiModel();
        $this->dailyReportModel = new DailyReportModel();
        $this->dailyReportDtModel = new DailyReportDtModel();
        $this->kategoriModel = new KategoriDowntimeModel();
    }

    public function index()
    {
        $rigs = $this->rigModel->getRigAktif();
        $firstRigId = $rigs[0]['id'] ?? 1;
        $bulan = (int)$this->request->getGet('bulan') ?: (int)date('n');
        $tahun = (int)$this->request->getGet('tahun') ?: (int)date('Y');
        $rigId = (int)$this->request->getGet('rig_id') ?: $firstRigId;

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"));
    }

    public function grid(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('daily-report'))->with('error', 'Rig tidak ditemukan.');
        }

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
            'title'            => "Daily Report {$rig['kode']} - Bulan {$bulan}/{$tahun}",
            'page_title'       => "Summary Report Per Well: {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle'    => "Matriks Pengawasan Operasi Sumur, MIRU, OPS, Pos SBWC & UNPAID",
            'rig'              => $rig,
            'rigId'            => $rigId,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'allRigs'          => $allRigs,
            'reports'          => $reports,
            'kategoriList'     => $kategoriList,
            'dtDetails'        => $dtDetails,
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

        // Sinkronisasi ke Monthly Summary
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Data pekerjaan sumur berhasil ditambahkan.');
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

        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($report['rig_id'], $report['bulan'], $report['tahun']);

        return redirect()->to(base_url("daily-report/{$report['rig_id']}/{$report['bulan']}/{$report['tahun']}"))->with('success', 'Data pekerjaan sumur berhasil diperbarui.');
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

            return redirect()->to(base_url("daily-report/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Pekerjaan sumur berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data gagal dihapus.');
    }
}
