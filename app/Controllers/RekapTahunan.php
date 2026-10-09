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

        // Ambil seluruh rekap tahunan yang sudah tersimpan di database dalam 1 query cepat
        $existing = $this->monthlySummaryModel->where('tahun', $tahun)->orderBy('bulan', 'ASC')->findAll();

        // Jika data tahun ini benar-benar belum pernah digenerate sama sekali, lakukan inisialisasi awal
        if (empty($existing)) {
            for ($m = 1; $m <= 12; $m++) {
                foreach ($rigs as $r) {
                    $this->monthlySummaryModel->hitungDanSimpan($r['id'], $m, $tahun);
                }
            }
            $existing = $this->monthlySummaryModel->where('tahun', $tahun)->orderBy('bulan', 'ASC')->findAll();
        }

        // Kelompokkan data per armada rig di memori PHP (0 query tambahan)
        $summariesByRig = [];
        foreach ($existing as $row) {
            $summariesByRig[$row['rig_id']][] = $row;
        }

        $annualData = [];
        foreach ($rigs as $r) {
            $annualData[$r['id']] = [
                'rig'    => $r,
                'months' => $summariesByRig[$r['id']] ?? [],
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

        $months = $this->monthlySummaryModel->getByRigTahun($rigId, $tahun);
        if (empty($months)) {
            for ($m = 1; $m <= 12; $m++) {
                $this->monthlySummaryModel->hitungDanSimpan($rigId, $m, $tahun);
            }
            $months = $this->monthlySummaryModel->getByRigTahun($rigId, $tahun);
        }

        $rigs = $this->rigModel->getRigAktif();

        $data = [
            'title'         => "Rekap Tahunan {$rig['kode']} - Tahun {$tahun}",
            'page_title'    => "Rekap Tahunan: {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle' => "Historis performa bulanan armada dalam satu tahun kalender",
            'rig'           => $rig,
            'rigs'          => $rigs,
            'tahun'         => $tahun,
            'months'        => $months,
        ];

        return view('rekap_tahunan/view_rig', $data);
    }

    /**
     * Rekap NPT Tahunan (dialihkan ke Pusat NPT Terpadu /npt)
     */
    public function npt(int $tahun = 2026)
    {
        $bulan = (int)($this->request->getGet('bulan') ?? date('n'));
        $tab = $this->request->getGet('mode') === 'kronologis' ? 'chrono' : 'tahunan';
        return redirect()->to(base_url("npt?tab={$tab}&tahun={$tahun}&bulan={$bulan}"));
    }
}
