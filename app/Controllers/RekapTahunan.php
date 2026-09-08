<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\MonthlySummaryModel;

class RekapTahunan extends BaseController
{
    protected $rigModel;
    protected $monthlySummaryModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
        $this->monthlySummaryModel = new MonthlySummaryModel();
    }

    public function index()
    {
        $tahun = (int)$this->request->getGet('tahun') ?: (int)date('Y');
        return redirect()->to(base_url("rekap-tahunan/{$tahun}"));
    }

    public function view(int $tahun)
    {
        $rigs = $this->rigModel->getRigAktif();

        // Populate and recalculate 12 months for all rigs
        for ($m = 1; $m <= 12; $m++) {
            foreach ($rigs as $r) {
                $this->monthlySummaryModel->hitungDanSimpan($r['id'], $m, $tahun);
            }
        }

        // Fetch monthly summary for entire year
        $annualData = [];
        foreach ($rigs as $r) {
            $annualData[$r['id']] = [
                'rig'    => $r,
                'months' => $this->monthlySummaryModel->getByRigTahun($r['id'], $tahun),
            ];
        }

        $data = [
            'title'         => "Rekap Tahunan {$tahun} - Seluruh Rig",
            'page_title'    => "Rekapitulasi Tahunan Operasi Rig Tahun {$tahun}",
            'page_subtitle' => "Akumulasi kinerja 12 bulan (Januari - Desember) per armada rig",
            'tahun'         => $tahun,
            'rigs'          => $rigs,
            'annualData'    => $annualData,
        ];

        return view('rekap_tahunan/view', $data);
    }

    public function viewRig(int $rigId, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('rekap-tahunan'))->with('error', 'Rig tidak ditemukan.');
        }

        for ($m = 1; $m <= 12; $m++) {
            $this->monthlySummaryModel->hitungDanSimpan($rigId, $m, $tahun);
        }

        $months = $this->monthlySummaryModel->getByRigTahun($rigId, $tahun);

        $data = [
            'title'         => "Rekap Tahunan {$rig['kode']} - Tahun {$tahun}",
            'page_title'    => "Rekap Tahunan: {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle' => "Historis performa bulanan armada dalam satu tahun kalender",
            'rig'           => $rig,
            'tahun'         => $tahun,
            'months'        => $months,
        ];

        return view('rekap_tahunan/view_rig', $data);
    }

    /**
     * Rekap NPT Tahunan (matches exact layout of REKAP NPT TAHUN 2026 SYS.xlsx)
     */
    public function npt(int $tahun = 2026)
    {
        $jsonFile = WRITEPATH . 'rekap_npt_2026.json';
        $nptData = [];
        if (file_exists($jsonFile)) {
            $nptData = json_decode(file_get_contents($jsonFile), true);
        }

        $activeMonth = (int)($this->request->getGet('bulan') ?? 1);
        if ($activeMonth < 1 || $activeMonth > 8) {
            $activeMonth = 1;
        }

        $monthData = $nptData['months'][$activeMonth] ?? null;

        $data = [
            'title'         => "Rekap NPT Tahun {$tahun} - Seluruh Rig",
            'page_title'    => "Rekapitulasi Downtime (NPT) Tahun {$tahun}",
            'page_subtitle' => "Rekap NPT (SBWC & UNPAID) Rig BMS Periode 2026",
            'tahun'         => $tahun,
            'activeMonth'   => $activeMonth,
            'nptData'       => $nptData,
            'monthData'     => $monthData,
        ];

        return view('rekap_tahunan/view_npt', $data);
    }
}
