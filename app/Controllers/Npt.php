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

        // Ambil data Daily Report Log untuk mendeteksi data yang disinkronkan & remark_unpaid dari Daily
        $db = \Config\Database::connect();
        $dailyLogMap = [];
        $logRows = $db->table('daily_report_log drl')
            ->select('drl.*, dr.no_well, l.nama_lokasi')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $rigId)
            ->where('MONTH(drl.tanggal)', $bulan)
            ->where('YEAR(drl.tanggal)', $tahun)
            ->get()->getResultArray();

        foreach ($logRows as $lr) {
            $dailyLogMap[$lr['tanggal']] = $lr;
        }

        // Ambil rincian data 3rd Party per perusahaan yang sudah tersimpan di npt_harian
        $tpEntries = [];
        $tpRows = $db->table('npt_harian')
            ->where('rig_id', $rigId)
            ->where('kategori_id', 10)
            ->where('third_party_id IS NOT NULL')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->get()->getResultArray();

        foreach ($tpRows as $tpr) {
            $tpEntries[$tpr['tanggal']][$tpr['third_party_id']] = (float)$tpr['jam'];
        }

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
            'dailyLogMap'   => $dailyLogMap,
            'tpEntries'     => $tpEntries,
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
        $remarksUnpaid = $this->request->getPost('remark_unpaid') ?? []; // [day] = text
        $tpInputs = $this->request->getPost('tp') ?? []; // [day][third_party_id] = jam

        foreach ($entries as $day => $katArr) {
            $formattedDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);
            $dayRemark = $remarks[$day] ?? null;
            $dayRemarkUnpaid = $remarksUnpaid[$day] ?? null;

            foreach ($katArr as $katId => $jam) {
                $katIdInt = (int)$katId;
                $jamVal = (float)$jam;
                $isUnpaid = in_array($katIdInt, [1, 2]);
                $finalRemark = $isUnpaid ? ($dayRemarkUnpaid ?: $dayRemark) : $dayRemark;

                // Jangan timpa pos 3rd Party jika diisi via rincian perusahaan (tpInputs)
                if ($katIdInt === 10 && !empty($tpInputs[$day])) {
                    continue;
                }

                $this->nptModel->upsertNpt(
                    $rigId,
                    $formattedDate,
                    $katIdInt,
                    null,
                    $jamVal,
                    $jamVal > 0 ? $finalRemark : null
                );

                // Sinkronisasi balik ke Daily Report Log jika ada perubahan
                $this->nptModel->syncNptBackToDaily($rigId, $formattedDate, $katIdInt, $jamVal, $finalRemark);
            }

            // Simpan rincian per 3rd Party Company jika ada input
            if (isset($tpInputs[$day])) {
                $totalTpPerDay = 0;
                foreach ($tpInputs[$day] as $tpId => $tpJam) {
                    $tpJamVal = (float)$tpJam;
                    $tpIdInt = (int)$tpId;
                    if ($tpJamVal > 0) {
                        $totalTpPerDay += $tpJamVal;
                        $this->nptModel->upsertNpt(
                            $rigId,
                            $formattedDate,
                            10, // Kategori 3rd Party
                            $tpIdInt,
                            $tpJamVal,
                            $dayRemark
                        );
                    } else {
                        // Hapus jika 0
                        $this->nptModel->upsertNpt($rigId, $formattedDate, 10, $tpIdInt, 0, null);
                    }
                }

                // Jika ada rincian per company, perbarui atau hapus baris agregat umum (third_party_id = null)
                if ($totalTpPerDay > 0) {
                    // Update total 3rd Party di Daily Report Log
                    $this->nptModel->syncNptBackToDaily($rigId, $formattedDate, 10, $totalTpPerDay, $dayRemark);
                }
            }
        }

        // Auto update monthly summary for this rig
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}"))->with('success', 'Data NPT harian berhasil disimpan & disinkronkan timbal balik dengan Daily Report.');
    }
}
