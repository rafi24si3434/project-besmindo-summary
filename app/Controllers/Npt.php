<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\NptModel;
use App\Models\MonthlySummaryModel;

class Npt extends BaseController
{
    protected $rigModel;
    protected $kategoriModel;
    protected $thirdPartyModel;
    protected $nptModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
        $this->kategoriModel = new KategoriDowntimeModel();
        $this->thirdPartyModel = new ThirdPartyModel();
        $this->nptModel = new NptModel();
    }

    public function index()
    {
        $rigs = $this->rigModel->getRigAktif();
        $firstRigId = $rigs[0]['id'] ?? 1;
        $bulan = (int)$this->request->getGet('bulan') ?: (int)date('n');
        $tahun = (int)$this->request->getGet('tahun') ?: (int)date('Y');
        $rigId = (int)$this->request->getGet('rig_id') ?: $firstRigId;

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}"));
    }

    public function grid(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('npt'))->with('error', 'Rig tidak ditemukan.');
        }

        $allRigs = $this->rigModel->getRigAktif();
        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $thirdParties = $this->thirdPartyModel->getAktif();

        // Calculate days in selected month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        // Fetch existing input data
        $existingData = $this->nptModel->getByRigBulanTahun($rigId, $bulan, $tahun);
        $totalsPerKat = $this->nptModel->getTotalByKategori($rigId, $bulan, $tahun);
        $totalUnpaid = $this->nptModel->getTotalUnpaid($rigId, $bulan, $tahun);
        $totalSBWC = $this->nptModel->getTotalSBWC($rigId, $bulan, $tahun);
        $totalDT = $this->nptModel->getTotalDowntime($rigId, $bulan, $tahun);

        $data = [
            'title'         => "NPT Harian {$rig['kode']} - Bulan {$bulan}/{$tahun}",
            'page_title'    => "Input NPT Harian: {$rig['kode']} ({$rig['nama_rig']})",
            'page_subtitle' => "Matriks pencatatan jam downtime harian per pos kerusakan & standby",
            'rig'           => $rig,
            'rigId'         => $rigId,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'daysInMonth'   => $daysInMonth,
            'allRigs'       => $allRigs,
            'kategoriList'  => $kategoriList,
            'thirdParties'  => $thirdParties,
            'existingData'  => $existingData,
            'totalsPerKat'  => $totalsPerKat,
            'totalUnpaid'   => $totalUnpaid,
            'totalSBWC'     => $totalSBWC,
            'totalDT'       => $totalDT,
        ];

        return view('npt/grid', $data);
    }

    public function simpan()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $entries = $this->request->getPost('npt') ?? []; // [day][kategori_id] = jam
        $remarks = $this->request->getPost('remark') ?? []; // [day] = text

        foreach ($entries as $day => $katArr) {
            $formattedDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);
            $dayRemark = $remarks[$day] ?? null;

            foreach ($katArr as $katId => $jam) {
                $jamVal = (float)$jam;
                $this->nptModel->upsertNpt(
                    $rigId,
                    $formattedDate,
                    (int)$katId,
                    null,
                    $jamVal,
                    $jamVal > 0 ? $dayRemark : null
                );
            }
        }

        // Auto update monthly summary for this rig
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Data NPT harian berhasil disimpan & disinkronisasi ke laporan bulanan.');
    }
}
