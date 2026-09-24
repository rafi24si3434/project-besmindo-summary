<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\NptModel;
use App\Models\MonthlySummaryModel;
use App\Models\ActivityLogModel;

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

        $tab = $this->request->getGet('tab') ?: 'harian';
        $rigQuery = $this->request->getGet('chrono_rig') ? ('&chrono_rig=' . urlencode($this->request->getGet('chrono_rig'))) : '';

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}?tab={$tab}{$rigQuery}"));
    }

    public function grid(int $rigId, int $bulan, ?int $tahun = null)
    {
        $tahun = $tahun ?: (int)$this->request->getGet('tahun') ?: (int)session('last_tahun') ?: (int)date('Y');

        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->to(base_url('npt'))->with('error', 'Rig tidak ditemukan.');
        }

        // Simpan rig, bulan, tahun terakhir ke session
        session()->set([
            'last_rig_id' => $rigId,
            'last_bulan'  => $bulan,
            'last_tahun'  => $tahun,
        ]);

        $activeTab = $this->request->getGet('tab') ?: 'harian';
        if (!in_array($activeTab, ['harian', 'bulanan', 'tahunan', 'chrono'])) {
            $activeTab = 'harian';
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

        // ══════════════════════════════════════════════════════════════
        // DATA TAB 2 & 3: REKAP BULANAN 18 RIG & REKAP TAHUNAN SYS
        // ══════════════════════════════════════════════════════════════
        $jsonFile = WRITEPATH . 'rekap_npt_2026.json';
        $nptJson = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

        // Rekap bulanan 18 rig (gabungan live database dan JSON historis)
        $monthlyAllRigData = $this->getMonthlyAllRigSummary($allRigs, $bulan, $tahun, $nptJson);

        // Month data from JSON for detailed SYS columns if available
        $monthData = $nptJson['months'][$bulan] ?? $nptJson['months'][(string)$bulan] ?? null;

        // ══════════════════════════════════════════════════════════════
        // DATA TAB 4: LOG KRONOLOGIS DOWNTIME
        // ══════════════════════════════════════════════════════════════
        $rigListNames = $nptJson['rig_list'] ?? [];
        if (empty($rigListNames)) {
            foreach ($allRigs as $ar) {
                $rigListNames[] = $ar['kode'];
            }
        }

        $selectedChronoRig = $this->request->getGet('chrono_rig') ?: $rig['kode'];
        $cleanChronoCode = str_replace('#', ' ', $selectedChronoRig);
        $chronoEvents = $nptJson['chronological_by_rig'][$cleanChronoCode] 
            ?? $nptJson['chronological_by_rig'][$selectedChronoRig] 
            ?? [];

        // Ambil kejadian downtime live dari database daily_report_log untuk rig yang dipilih
        $chronoRigRow = $db->table('rigs')->where('kode', $selectedChronoRig)->orWhere('kode', str_replace(' ', '#', $selectedChronoRig))->get()->getRowArray();
        $targetRigId = $chronoRigRow ? (int)$chronoRigRow['id'] : $rigId;

        $dbChrono = $db->table('daily_report_log drl')
            ->select('drl.tanggal, drl.total_dt, drl.remark_npt, drl.remark_unpaid, dr.no_well, l.nama_lokasi, r.kode as kode_rig')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('rigs r', 'r.id = dr.rig_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $targetRigId)
            ->where('drl.total_dt >', 0)
            ->orderBy('drl.tanggal', 'DESC')
            ->get()->getResultArray();

        // Jika JSON kosong atau kita ingin menggabungkan kejadian live
        if (empty($chronoEvents) && !empty($dbChrono)) {
            $evIdx = 1;
            foreach ($dbChrono as $dbc) {
                $chronoEvents[] = [
                    'no'            => $evIdx++,
                    'date'          => date('d/m/Y', strtotime($dbc['tanggal'])),
                    'rig'           => $dbc['kode_rig'],
                    'location'      => ($dbc['nama_lokasi'] ?? '-') . ($dbc['no_well'] ? ' / Well ' . $dbc['no_well'] : ''),
                    'remark'        => $dbc['remark_npt'] ?: '-',
                    'remark_unpaid' => $dbc['remark_unpaid'] ?: '',
                    'hrs'           => (float)$dbc['total_dt'],
                ];
            }
        }

        // Ambil daftar sumur untuk rig aktif pada periode bulan & tahun ini
        $rigWells = $db->table('daily_report dr')
            ->select('dr.*, l.nama_lokasi')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $rigId)
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('dr.no_well', 'ASC')
            ->get()->getResultArray();

        // Deteksi hari yang belum memiliki log operasi
        $missingLogDays = [];
        $isCurrentMonth = ($tahun == (int)date('Y') && $bulan == (int)date('n'));
        $maxDayToCheck = $isCurrentMonth ? min($daysInMonth, (int)date('j')) : $daysInMonth;
        for ($d = 1; $d <= $maxDayToCheck; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            if (!isset($dailyLogMap[$dateStr])) {
                $missingLogDays[] = $d;
            }
        }

        $data = [
            'missingLogDays'    => $missingLogDays,
            'title'             => "NPT Center — {$rig['kode']} ({$bulan}/{$tahun})",
            'rigWells'          => $rigWells,
            'page_title'        => "NPT (Non-Productive Time) Center",
            'page_subtitle'     => "Pusat Rekapitulasi & Verifikasi Downtime Armada Rig BMS (Harian, Bulanan, & Tahunan)",
            'rig'               => $rig,
            'rigId'             => $rigId,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'daysInMonth'       => $daysInMonth,
            'allRigs'           => $allRigs,
            'kategoriList'      => $kategoriList,
            'thirdParties'      => $thirdParties,
            'existingData'      => $existingData,
            'totalsPerKat'      => $totalsPerKat,
            'totalUnpaid'       => $totalUnpaid,
            'totalSBWC'         => $totalSBWC,
            'totalDT'           => $totalDT,
            'dailyLogMap'       => $dailyLogMap,
            'tpEntries'         => $tpEntries,
            // Unified Hub datasets:
            'activeTab'         => $activeTab,
            'monthlyAllRigData' => $monthlyAllRigData,
            'annualNptData'     => $nptJson,
            'monthData'         => $monthData,
            'rigListNames'      => $rigListNames,
            'selectedChronoRig' => $selectedChronoRig,
            'chronoEvents'      => $chronoEvents,
        ];

        return view('npt/grid', $data);
    }

    /**
     * Membentuk rekap bulanan seluruh 18 armada rig (gabungan live DB npt_harian & data historis JSON)
     */
    private function getMonthlyAllRigSummary(array $allRigs, int $bulan, int $tahun, ?array $nptJson): array
    {
        $db = \Config\Database::connect();
        $isJsonAvailable = ($tahun == 2026 && isset($nptJson['months'][$bulan]['rows']));

        // 1. Query live database npt_harian
        $dbData = [];
        $dbRows = $db->table('npt_harian')
            ->select('rig_id, kategori_id, SUM(jam) as total_jam')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->groupBy(['rig_id', 'kategori_id'])
            ->get()->getResultArray();

        foreach ($dbRows as $r) {
            $dbData[(int)$r['rig_id']][(int)$r['kategori_id']] = (float)$r['total_jam'];
        }

        // 2. Remark unpaid per rig
        $unpaidRemarks = [];
        $unpaidRemRows = $db->query("
            SELECT nh.rig_id, GROUP_CONCAT(DISTINCT nh.remark SEPARATOR '; ') as unpaid_remark
            FROM npt_harian nh
            WHERE MONTH(nh.tanggal) = ? AND YEAR(nh.tanggal) = ?
              AND nh.kategori_id IN (1, 2) AND nh.remark IS NOT NULL AND nh.remark != ''
            GROUP BY nh.rig_id
        ", [$bulan, $tahun])->getResultArray();
        foreach ($unpaidRemRows as $ur) {
            $unpaidRemarks[(int)$ur['rig_id']] = $ur['unpaid_remark'];
        }

        // 3. Remark unpaid fallback dari daily_report_log
        $logRemRows = $db->query("
            SELECT dr.rig_id, GROUP_CONCAT(DISTINCT drl.remark_unpaid SEPARATOR '; ') as log_unpaid_rem
            FROM daily_report_log drl
            JOIN daily_report dr ON dr.id = drl.daily_report_id
            WHERE MONTH(drl.tanggal) = ? AND YEAR(drl.tanggal) = ?
              AND drl.remark_unpaid IS NOT NULL AND drl.remark_unpaid != ''
            GROUP BY dr.rig_id
        ", [$bulan, $tahun])->getResultArray();
        foreach ($logRemRows as $lr) {
            $rId = (int)$lr['rig_id'];
            if (empty($unpaidRemarks[$rId])) {
                $unpaidRemarks[$rId] = $lr['log_unpaid_rem'];
            }
        }

        $rows = [];
        $grandRepair = 0; $grandPers = 0; $grandUnpaid = 0;
        $grandRain = 0; $grandRoad = 0; $grandDay = 0; $grandTp = 0; $grandSbwc = 0;
        $grandTotalHrs = 0;

        $no = 1;
        foreach ($allRigs as $r) {
            $rigId = (int)$r['id'];
            $kodeClean = str_replace('#', ' ', $r['kode']);

            $jsonRow = null;
            if ($isJsonAvailable && !empty($nptJson['months'][$bulan]['rows'])) {
                foreach ($nptJson['months'][$bulan]['rows'] as $jr) {
                    if (trim($jr['rig']) === trim($kodeClean) || trim($jr['rig']) === trim($r['kode'])) {
                        $jsonRow = $jr;
                        break;
                    }
                }
            }

            $repairVal = 0; $persVal = 0; $rainVal = 0; $roadVal = 0; $dayVal = 0; $tpSum = 0; $totSbwc = 0; $totHrs = 0;
            $remUnpaid = $unpaidRemarks[$rigId] ?? '';

            if (!empty($dbData[$rigId])) {
                // Diambil dari live database npt_harian
                $repairVal = $dbData[$rigId][1] ?? 0;
                $persVal   = $dbData[$rigId][2] ?? 0;
                $rainVal   = $dbData[$rigId][3] ?? 0;
                $roadVal   = ($dbData[$rigId][4] ?? 0) + ($dbData[$rigId][5] ?? 0);
                $dayVal    = $dbData[$rigId][6] ?? 0;
                $tpSum     = $dbData[$rigId][10] ?? 0;

                for ($k = 3; $k <= 15; $k++) {
                    $totSbwc += $dbData[$rigId][$k] ?? 0;
                }
                $totHrs = $repairVal + $persVal + $totSbwc;
            } elseif ($jsonRow) {
                // Fallback ke data historis JSON
                $vals = $jsonRow['vals'] ?? [];
                $repairVal = (float)($vals[2] ?? 0);
                $persVal   = (float)($vals[3] ?? 0);
                $rainVal   = (float)($vals[4] ?? 0);
                $roadVal   = (float)($vals[5] ?? 0) + (float)($vals[6] ?? 0);
                $dayVal    = (float)($vals[7] ?? 0);
                for ($c = 12; $c <= 21; $c++) $tpSum += (float)($vals[$c] ?? 0);
                for ($c = 4; $c <= 31; $c++) $totSbwc += (float)($vals[$c] ?? 0);
                $totHrs = (float)($vals[32] ?? ($repairVal + $persVal + $totSbwc));
                $remUnpaid = $vals[33] ?? '';
            }

            $totUnpaid = $repairVal + $persVal;

            $rows[] = [
                'no'         => $no++,
                'rig_id'     => $rigId,
                'kode'       => $r['kode'],
                'nama_rig'   => $r['nama_rig'],
                'repair'     => $repairVal,
                'pers'       => $persVal,
                'tot_unpaid' => $totUnpaid,
                'rain'       => $rainVal,
                'road'       => $roadVal,
                'day'        => $dayVal,
                'tp'         => $tpSum,
                'tot_sbwc'   => $totSbwc,
                'total_hrs'  => $totHrs,
                'rem_unpaid' => $remUnpaid,
            ];

            $grandRepair   += $repairVal;
            $grandPers     += $persVal;
            $grandUnpaid   += $totUnpaid;
            $grandRain     += $rainVal;
            $grandRoad     += $roadVal;
            $grandDay      += $dayVal;
            $grandTp       += $tpSum;
            $grandSbwc     += $totSbwc;
            $grandTotalHrs += $totHrs;
        }

        return [
            'rows'       => $rows,
            'grandTotal' => [
                'repair'     => $grandRepair,
                'pers'       => $grandPers,
                'tot_unpaid' => $grandUnpaid,
                'rain'       => $grandRain,
                'road'       => $grandRoad,
                'day'        => $grandDay,
                'tp'         => $grandTp,
                'tot_sbwc'   => $grandSbwc,
                'total_hrs'  => $grandTotalHrs,
            ]
        ];
    }

    public function simpan()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');

        // Dukung payload JSON terkompresi untuk menghindari limit max_input_vars PHP (1000 input fields)
        $jsonPayload = $this->request->getPost('payload_json');
        if (!empty($jsonPayload)) {
            $payloadData   = json_decode($jsonPayload, true) ?: [];
            $entries       = $payloadData['npt'] ?? [];
            $remarks       = $payloadData['remark'] ?? [];
            $remarksUnpaid = $payloadData['remark_unpaid'] ?? [];
            $tpInputs      = $payloadData['tp'] ?? [];
        } else {
            $entries       = $this->request->getPost('npt') ?? []; // [day][kategori_id] = jam
            $remarks       = $this->request->getPost('remark') ?? []; // [day] = text
            $remarksUnpaid = $this->request->getPost('remark_unpaid') ?? []; // [day] = text
            $tpInputs      = $this->request->getPost('tp') ?? []; // [day][third_party_id] = jam
        }

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

        // Catat jejak audit aktivitas
        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        ActivityLogModel::record(
            'NPT',
            'SIMPAN_NPT',
            "Menyimpan matriks NPT {$rigKode} periode " . sprintf('%02d/%04d', $bulan, $tahun) . " & sinkronisasi otomatis ke Daily Report dan Monthly Report.",
            $rigId
        );

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}?tab=harian"))->with('success', 'Data NPT harian berhasil disimpan & disinkronkan timbal balik dengan Daily Report.');
    }
    /**
     * AJAX: Ambil rincian data sumur & performa operasional sebuah rig pada bulan & tahun tertentu
     */
    public function getRigWells(int $targetRigId, int $bulan, ?int $tahun = null)
    {
        $tahun = $tahun ?: (int)$this->request->getGet('tahun') ?: (int)session('last_tahun') ?: (int)date('Y');
        $rig = $this->rigModel->find($targetRigId);
        if (!$rig) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Rig tidak ditemukan']);
        }

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $db = \Config\Database::connect();

        // 1. Ambil daftar sumur untuk rig ini di bulan & tahun terpilih
        $wells = $db->table('daily_report dr')
            ->select('dr.*, l.nama_lokasi')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $targetRigId)
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('dr.no_well', 'ASC')
            ->get()->getResultArray();

        $wellsData = [];
        $totMiru = 0;
        $totOps = 0;
        $totDt = 0;
        $totUnpaid = 0;
        $totSbwc = 0;
        $totJam = 0;

        foreach ($wells as $w) {
            $wId = (int)$w['id'];
            $wMiru = (float)$w['miru_jam'];
            $wOps = (float)$w['ops_jam'];
            $wDt = (float)$w['total_dt'];
            $wTot = (float)$w['total_jam'];

            $totMiru += $wMiru;
            $totOps += $wOps;
            $totDt += $wDt;
            $totJam += $wTot;

            // Rincian kategori downtime di sumur ini
            $dtBreakdown = $db->table('daily_report_dt drdt')
                ->select('drdt.jam, kd.nama, kd.tipe')
                ->join('kategori_downtime kd', 'kd.id = drdt.kategori_id')
                ->where('drdt.daily_report_id', $wId)
                ->where('drdt.jam >', 0)
                ->get()->getResultArray();

            $wellUnpaid = 0;
            $wellSbwc = 0;
            foreach ($dtBreakdown as $dbItem) {
                if ($dbItem['tipe'] === 'UNPAID') {
                    $wellUnpaid += (float)$dbItem['jam'];
                } else {
                    $wellSbwc += (float)$dbItem['jam'];
                }
            }
            $totUnpaid += $wellUnpaid;
            $totSbwc += $wellSbwc;

            // Hitung hari log tercatat
            $logCount = $db->table('daily_report_log')
                ->where('daily_report_id', $wId)
                ->countAllResults();

            $wellsData[] = [
                'id'              => $wId,
                'no_well'         => (int)$w['no_well'],
                'nama_lokasi'     => $w['nama_lokasi'] ?: 'Lokasi Belum Diset',
                'tanggal_mulai'   => $w['tanggal_mulai'] ? date('d M Y', strtotime($w['tanggal_mulai'])) : '-',
                'tanggal_selesai' => $w['tanggal_selesai'] ? date('d M Y', strtotime($w['tanggal_selesai'])) : '-',
                'jarak'           => (float)$w['jarak'],
                'miru_jam'        => $wMiru,
                'ops_jam'         => $wOps,
                'total_dt'        => $wDt,
                'unpaid_jam'      => $wellUnpaid,
                'sbwc_jam'        => $wellSbwc,
                'total_jam'       => $wTot,
                'status_job'      => $w['status_job'] ?: 'PROGRESS',
                'remark'          => $w['remark'] ?: '',
                'log_count'       => $logCount,
                'dt_breakdown'    => $dtBreakdown,
            ];
        }

        // 2. Total sumur tahunan
        $annualWellsCount = $db->table('daily_report')
            ->where('rig_id', $targetRigId)
            ->where('tahun', $tahun)
            ->countAllResults();

        // 3. Ambil data Monthly Summary jika ada
        $monthlySummary = $db->table('monthly_summary')
            ->where('rig_id', $targetRigId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()->getRowArray();

        $odr = (float)($rig['odr'] ?? 0);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $revTarget = $odr * $daysInMonth;

        return $this->response->setJSON([
            'status' => 'success',
            'rig' => [
                'id'            => (int)$rig['id'],
                'kode'          => $rig['kode'],
                'nama_rig'      => $rig['nama_rig'],
                'odr'           => $odr,
                'odr_formatted' => 'Rp ' . number_format($odr, 0, ',', '.'),
            ],
            'periode' => [
                'bulan'         => $bulan,
                'bulan_nama'    => $bulanList[$bulan] ?? '',
                'tahun'         => $tahun,
                'days'          => $daysInMonth,
            ],
            'summary' => [
                'total_wells'   => count($wellsData),
                'annual_wells'  => $annualWellsCount,
                'total_miru'    => round($totMiru, 2),
                'total_ops'     => round($totOps, 2),
                'total_dt'      => round($totDt, 2),
                'unpaid_dt'     => round($totUnpaid, 2),
                'sbwc_dt'       => round($totSbwc, 2),
                'total_jam'     => round($totJam, 2),
                'revenue_target'=> $revTarget,
                'revenue_target_formatted' => 'Rp ' . number_format($revTarget, 0, ',', '.'),
                'reliability'   => $monthlySummary ? round((float)$monthlySummary['reliability'] * 100, 2) : null,
                'availability'  => $monthlySummary ? round((float)$monthlySummary['availability'] * 100, 2) : null,
                'utilization'   => $monthlySummary ? round((float)$monthlySummary['utilization'] * 100, 2) : null,
            ],
            'wells' => $wellsData
        ]);
    }
}
