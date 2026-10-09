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

        // Month data from JSON for detailed SYS columns if available; jika tidak ada di arsip JSON (seperti September, Oktober berjalan, dst), bangun otomatis dari live database!
        $monthData = $nptJson['months'][$bulan] ?? $nptJson['months'][(string)$bulan] ?? null;
        if (empty($monthData) || empty($monthData['rows'])) {
            $monthData = $this->buildSysMonthDataFromDb($allRigs, $bulan, $tahun);
        }

        // Akumulasi tahunan grand_total secara dinamis (hanya dihitung saat tab tahunan aktif untuk efisiensi beban server)
        $annualNptData = $nptJson;
        if ($activeTab === 'tahunan') {
            $dynamicGrandTotal = array_fill(0, 34, 0.0);
            for ($m = 1; $m <= 12; $m++) {
                $mData = $nptJson['months'][$m] ?? $nptJson['months'][(string)$m] ?? null;
                if (empty($mData) || empty($mData['totals'])) {
                    $mData = $this->buildSysMonthDataFromDb($allRigs, $m, $tahun);
                }
                if (!empty($mData['totals'])) {
                    for ($c = 2; $c <= 32; $c++) {
                        $dynamicGrandTotal[$c] += (float)($mData['totals'][$c] ?? 0);
                    }
                }
            }
            $annualNptData['grand_total'] = $dynamicGrandTotal;
        }

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

        // Mode Tampilan: 'summary' (Summary Month All Rig persis Excel) atau 'rig' (Lembar Kerja Rincian Rig)
        $activeView = $this->request->getGet('view') 
            ?: ($this->request->getGet('tab') === 'bulanan' ? 'summary' : ($this->request->getGet('tab') === 'rig' ? 'rig' : 'summary'));
        if (!in_array($activeView, ['summary', 'rig'])) {
            $activeView = 'summary';
        }

        // Metrik Agregat Armada Seluruh Rig
        $fleetTotalHrs = (float)($monthData['totals'][32] ?? 0);
        $fleetTotalRepair = (float)($monthData['totals'][2] ?? 0);
        $fleetTotalPersonel = (float)($monthData['totals'][3] ?? 0);
        $fleetTotalUnpaid = $fleetTotalRepair + $fleetTotalPersonel;
        $fleetTotalSbwc = max(0, $fleetTotalHrs - $fleetTotalUnpaid);

        // Data badge 18 rig untuk pill selector
        $rigBadges = [];
        $topRigName = '-';
        $topRigHrs = 0;
        foreach ($allRigs as $ar) {
            $arId = (int)$ar['id'];
            $arKode = $ar['kode'];
            $cleanCode = str_replace('#', ' ', $arKode);

            $foundRow = null;
            if (!empty($monthData['rows'])) {
                foreach ($monthData['rows'] as $mr) {
                    if (trim($mr['rig']) === trim($cleanCode) || trim($mr['rig']) === trim($arKode)) {
                        $foundRow = $mr;
                        break;
                    }
                }
            }

            $rHrs = (float)($foundRow['vals'][32] ?? 0);
            $rUnp = (float)($foundRow['vals'][2] ?? 0) + (float)($foundRow['vals'][3] ?? 0);
            $rSbwc = max(0, $rHrs - $rUnp);
            $rRem = $foundRow['vals'][33] ?? '-';

            if ($rHrs > $topRigHrs) {
                $topRigHrs = $rHrs;
                $topRigName = $cleanCode;
            }

            $rigBadges[] = [
                'id'        => $arId,
                'kode'      => $arKode,
                'clean_code'=> $cleanCode,
                'total_hrs' => $rHrs,
                'unpaid'    => $rUnp,
                'sbwc'      => $rSbwc,
                'remark'    => $rRem,
            ];
        }

        // Rincian Lembar Kejadian Downtime untuk Rig Terpilih (Persis Sheet Individual Rig di Excel)
        $rigSheetEvents = $this->getRigSheetEvents($rigId, $bulan, $tahun);
        $selectedRigEventsCount = count($rigSheetEvents);
        $selectedRigTotalHrs = array_sum(array_column($rigSheetEvents, 'total_hrs'));
        $selectedRigUnpaid = array_sum(array_column($rigSheetEvents, 'total_unpaid'));
        $selectedRigSbwc = array_sum(array_column($rigSheetEvents, 'total_sbwc'));

        $lokasiModel = new \App\Models\LokasiModel();
        $lokasiList = $lokasiModel->getAktif();

        $data = [
            'missingLogDays'          => $missingLogDays,
            'title'                   => "Informasi NPT Bulanan — {$bulan}/{$tahun}",
            'rigWells'                => $rigWells,
            'page_title'              => "Informasi NPT Bulanan Rig BMS",
            'page_subtitle'           => "Format Buku Kerja Operasional Resmi (Rekap Seluruh Rig & Lembar Per Rig)",
            'rig'                     => $rig,
            'rigId'                   => $rigId,
            'bulan'                   => $bulan,
            'tahun'                   => $tahun,
            'daysInMonth'             => $daysInMonth,
            'allRigs'                 => $allRigs,
            'kategoriList'            => $kategoriList,
            'thirdParties'            => $thirdParties,
            'existingData'            => $existingData,
            'totalsPerKat'            => $totalsPerKat,
            'totalUnpaid'             => $totalUnpaid,
            'totalSBWC'               => $totalSBWC,
            'totalDT'                 => $totalDT,
            'dailyLogMap'             => $dailyLogMap,
            'tpEntries'               => $tpEntries,
            // Mode Tampilan Persis Excel:
            'activeView'              => $activeView,
            'fleetTotalHrs'           => $fleetTotalHrs,
            'fleetTotalUnpaid'        => $fleetTotalUnpaid,
            'fleetTotalSbwc'          => $fleetTotalSbwc,
            'topRigName'              => $topRigName,
            'topRigHrs'               => $topRigHrs,
            'rigBadges'               => $rigBadges,
            'rigSheetEvents'          => $rigSheetEvents,
            'selectedRigEventsCount'  => $selectedRigEventsCount,
            'selectedRigTotalHrs'     => $selectedRigTotalHrs,
            'selectedRigUnpaid'       => $selectedRigUnpaid,
            'selectedRigSbwc'         => $selectedRigSbwc,
            'lokasiList'              => $lokasiList,
            // Dataset Legacy & Tambahan:
            'activeTab'               => $activeTab,
            'monthlyAllRigData'       => $monthlyAllRigData,
            'annualNptData'           => $annualNptData,
            'monthData'               => $monthData,
            'rigListNames'            => $rigListNames,
            'selectedChronoRig'       => $selectedChronoRig,
            'chronoEvents'            => $chronoEvents,
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

    /**
     * Membangun struktur Matriks Rinci SYS (32 Kolom) secara dinamis dari live database
     * Digunakan ketika data arsip JSON tidak tersedia (misal: September, Oktober, atau bulan/tahun baru)
     */
    public function buildSysMonthDataFromDb(array $allRigs, int $bulan, int $tahun): array
    {
        $db = \Config\Database::connect();
        $bulanNames = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];

        // 1. Query data downtime dari npt_harian
        $dbRows = $db->table('npt_harian')
            ->select('rig_id, kategori_id, third_party_id, SUM(jam) as total_jam')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->groupBy(['rig_id', 'kategori_id', 'third_party_id'])
            ->get()->getResultArray();

        $rigKatMap = [];
        $rigTpMap  = [];
        foreach ($dbRows as $r) {
            $rId = (int)$r['rig_id'];
            $kId = (int)$r['kategori_id'];
            $jam = (float)$r['total_jam'];

            $rigKatMap[$rId][$kId] = ($rigKatMap[$rId][$kId] ?? 0.0) + $jam;
            if (!empty($r['third_party_id'])) {
                $tpId = (int)$r['third_party_id'];
                $rigTpMap[$rId][$tpId] = ($rigTpMap[$rId][$tpId] ?? 0.0) + $jam;
            }
        }

        // 2. Query Remark Unpaid per rig dari npt_harian
        $unpaidRemRows = $db->query("
            SELECT nh.rig_id, GROUP_CONCAT(DISTINCT nh.remark SEPARATOR '; ') as unpaid_remark
            FROM npt_harian nh
            WHERE MONTH(nh.tanggal) = ? AND YEAR(nh.tanggal) = ?
              AND nh.kategori_id IN (1, 2) AND nh.remark IS NOT NULL AND nh.remark != ''
            GROUP BY nh.rig_id
        ", [$bulan, $tahun])->getResultArray();
        $remUnpaid = [];
        foreach ($unpaidRemRows as $ur) {
            $remUnpaid[(int)$ur['rig_id']] = $ur['unpaid_remark'];
        }

        // 3. Fallback remark unpaid dari daily_report_log jika di npt_harian belum ada
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
            if (empty($remUnpaid[$rId])) {
                $remUnpaid[$rId] = $lr['log_unpaid_rem'];
            }
        }

        // 4. Fallback jika masih kosong: tangkap catatan/remark apa pun (SBWC/Umum) dari npt_harian & daily_report_log
        $generalRemRows = $db->query("
            SELECT nh.rig_id, GROUP_CONCAT(DISTINCT nh.remark SEPARATOR '; ') as gen_rem
            FROM npt_harian nh
            WHERE MONTH(nh.tanggal) = ? AND YEAR(nh.tanggal) = ?
              AND nh.remark IS NOT NULL AND nh.remark != ''
            GROUP BY nh.rig_id
        ", [$bulan, $tahun])->getResultArray();
        foreach ($generalRemRows as $gr) {
            $rId = (int)$gr['rig_id'];
            if (empty($remUnpaid[$rId])) {
                $remUnpaid[$rId] = $gr['gen_rem'];
            }
        }

        $logGeneralRemRows = $db->query("
            SELECT dr.rig_id, GROUP_CONCAT(DISTINCT drl.remark_npt SEPARATOR '; ') as log_gen_rem
            FROM daily_report_log drl
            JOIN daily_report dr ON dr.id = drl.daily_report_id
            WHERE MONTH(drl.tanggal) = ? AND YEAR(drl.tanggal) = ?
              AND drl.remark_npt IS NOT NULL AND drl.remark_npt != ''
            GROUP BY dr.rig_id
        ", [$bulan, $tahun])->getResultArray();
        foreach ($logGeneralRemRows as $lgr) {
            $rId = (int)$lgr['rig_id'];
            if (empty($remUnpaid[$rId])) {
                $remUnpaid[$rId] = $lgr['log_gen_rem'];
            }
        }

        // Pemetaan ID 10 Vendor 3rd Party ke Indeks Kolom Tabel SYS (Cols 12..21)
        $tpColMap = [
            12 => 1,  // BHI
            13 => 2,  // HLS
            14 => 15, // WI
            15 => 5,  // HALCO
            16 => 4,  // EJP
            17 => 6,  // SCHL
            18 => 7,  // MGA
            19 => 8,  // SGN
            20 => 3,  // BUKAKA
            21 => 9,  // PESI
        ];

        // Pastikan armada rig yang memiliki data di bulan ini (bahkan jika inactive) tetap dimasukkan
        $rigList = $allRigs;
        $activeRigIds = array_map('intval', array_column($rigList, 'id'));
        foreach ($dbRows as $r) {
            $rId = (int)$r['rig_id'];
            if (!in_array($rId, $activeRigIds, true)) {
                $extraRig = $this->rigModel->find($rId);
                if ($extraRig) {
                    $rigList[] = $extraRig;
                    $activeRigIds[] = $rId;
                }
            }
        }

        $rows = [];
        $totals = array_fill(0, 34, 0.0);
        $totals[0] = 'TOTAL';
        $totals[1] = '';

        $no = 1;
        foreach ($rigList as $r) {
            $rigId = (int)$r['id'];
            $kodeDisplay = str_replace('#', ' ', $r['kode']);

            $vals = array_fill(0, 34, 0.0);
            $vals[0] = $no;
            $vals[1] = $kodeDisplay;

            // UNPAID
            $vals[2] = $rigKatMap[$rigId][1] ?? 0.0; // Repair Rig
            $vals[3] = $rigKatMap[$rigId][2] ?? 0.0; // Personel

            // SBWC
            $vals[4]  = $rigKatMap[$rigId][3] ?? 0.0; // SWA Rain
            $vals[5]  = $rigKatMap[$rigId][4] ?? 0.0; // Dry Road
            $vals[6]  = $rigKatMap[$rigId][5] ?? 0.0; // Dry Pad
            $vals[7]  = $rigKatMap[$rigId][6] ?? 0.0; // Daylight
            $vals[8]  = $rigKatMap[$rigId][13] ?? 0.0; // PT CHAST / PHR Operator
            $vals[9]  = ($rigKatMap[$rigId][14] ?? 0.0) + ($rigKatMap[$rigId][19] ?? 0.0); // WO PDC / CE/PE
            $vals[10] = $rigKatMap[$rigId][8] ?? 0.0; // PHR Well
            $vals[11] = $rigKatMap[$rigId][20] ?? 0.0; // ESP

            // 3rd Party 10 Vendors
            $sumTpRow = 0.0;
            foreach ($tpColMap as $colIdx => $tpId) {
                $tpVal = $rigTpMap[$rigId][$tpId] ?? 0.0;
                $vals[$colIdx] = $tpVal;
                $sumTpRow += $tpVal;
            }
            // Jika ada jam 3rd party yang dicatat tanpa perincian vendor spesifik
            $totalKat10 = (float)($rigKatMap[$rigId][10] ?? 0.0);
            if ($totalKat10 > $sumTpRow) {
                $sisaTp = $totalKat10 - $sumTpRow;
                $vals[12] += $sisaTp; // Alokasikan ke vendor 3rd party pertama
            }

            $vals[22] = $rigTpMap[$rigId][10] ?? 0.0; // UNISAT
            $vals[23] = $rigKatMap[$rigId][11] ?? 0.0; // TRANS Sharing
            $vals[24] = $rigKatMap[$rigId][12] ?? 0.0; // Foam Unit
            $vals[25] = $rigKatMap[$rigId][18] ?? 0.0; // WO Decision from LSC
            $vals[26] = $rigKatMap[$rigId][21] ?? 0.0; // PEMILU
            $vals[27] = ($rigKatMap[$rigId][17] ?? 0.0) + ($rigTpMap[$rigId][14] ?? 0.0); // WO OMS
            $vals[28] = $rigTpMap[$rigId][11] ?? 0.0; // PT PCM
            $vals[29] = $rigTpMap[$rigId][12] ?? 0.0; // WO COSL
            $vals[30] = 0.0; // SAFARI
            $vals[31] = $rigKatMap[$rigId][15] ?? 0.0; // IDUL FITRI / Shutdown

            // TOTAL (HRS)
            $rowTotal = 0.0;
            for ($c = 2; $c <= 31; $c++) {
                $rowTotal += (float)$vals[$c];
                $totals[$c] += (float)$vals[$c];
            }
            $vals[32] = $rowTotal;
            $totals[32] += $rowTotal;

            $vals[33] = !empty($remUnpaid[$rigId]) ? $remUnpaid[$rigId] : '-';

            $rows[] = [
                'no'     => $no++,
                'rig'    => $kodeDisplay,
                'vals'   => $vals,
                'remark' => $vals[33]
            ];
        }

        return [
            'title'  => "DOWN TIME RIG BMS PERIODE " . ($bulanNames[$bulan] ?? '') . " {$tahun}",
            'cols'   => [],
            'rows'   => $rows,
            'totals' => $totals
        ];
    }

    /**
     * Mengambil seluruh kejadian downtime pada sheet rig tertentu (persis lembar BMS 02..BMS 21 di Excel NPT SEPTEMBER 2026.xlsx)
     */
    public function getRigSheetEvents(int $rigId, int $bulan, int $tahun): array
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT nh.id, nh.tanggal, nh.jam, nh.remark, nh.kategori_id, nh.third_party_id,
                   kd.nama as nama_kategori, kd.tipe as tipe_kat,
                   tp.nama as nama_tp,
                   COALESCE(
                       l_nh.nama_lokasi,
                       (SELECT l.nama_lokasi FROM daily_report dr JOIN lokasi l ON l.id = dr.lokasi_id WHERE dr.rig_id = nh.rig_id AND nh.tanggal BETWEEN dr.tanggal_mulai AND dr.tanggal_selesai LIMIT 1),
                       (SELECT l.nama_lokasi FROM daily_report_log drl JOIN daily_report dr ON dr.id = drl.daily_report_id JOIN lokasi l ON l.id = dr.lokasi_id WHERE dr.rig_id = nh.rig_id AND drl.tanggal = nh.tanggal LIMIT 1),
                       '-'
                   ) as nama_lokasi
            FROM npt_harian nh
            LEFT JOIN kategori_downtime kd ON kd.id = nh.kategori_id
            LEFT JOIN third_parties tp ON tp.id = nh.third_party_id
            LEFT JOIN lokasi l_nh ON l_nh.id = nh.lokasi_id
            WHERE nh.rig_id = ? AND MONTH(nh.tanggal) = ? AND YEAR(nh.tanggal) = ?
            ORDER BY nh.tanggal ASC, nh.id ASC
        ";

        $entries = $db->query($query, [$rigId, $bulan, $tahun])->getResultArray();

        $datesMap = [];
        foreach ($entries as $e) {
            $d = $e['tanggal'];
            if (!isset($datesMap[$d])) {
                $datesMap[$d] = [
                    'tanggal'           => $d,
                    'tanggal_formatted' => date('d-M-Y', strtotime($d)),
                    'nama_lokasi'       => $e['nama_lokasi'] ?: '-',
                    'unpaid_rep'        => 0.0,
                    'unpaid_per'        => 0.0,
                    'sbwc_rain'         => 0.0,
                    'sbwc_road'         => 0.0,
                    'sbwc_pad'          => 0.0,
                    'sbwc_daylight'     => 0.0,
                    'sbwc_perfo'        => 0.0,
                    'sbwc_phr'          => 0.0,
                    'sbwc_cpi'          => 0.0,
                    'sbwc_tp'           => [],
                    'sbwc_tp_sum'       => 0.0,
                    'sbwc_trans'        => 0.0,
                    'sbwc_foam'         => 0.0,
                    'sbwc_pesi'         => 0.0,
                    'sbwc_cepe'         => 0.0,
                    'sbwc_idul'         => 0.0,
                    'sbwc_other'        => 0.0,
                    'total_unpaid'      => 0.0,
                    'total_sbwc'        => 0.0,
                    'total_hrs'         => 0.0,
                    'remarks'           => [],
                    'entries_raw'       => [],
                ];
            }

            if ($e['nama_lokasi'] !== '-' && $datesMap[$d]['nama_lokasi'] === '-') {
                $datesMap[$d]['nama_lokasi'] = $e['nama_lokasi'];
            }

            $jam  = (float)$e['jam'];
            $kId  = (int)$e['kategori_id'];
            $tpId = (int)$e['third_party_id'];

            if ($kId === 1) {
                $datesMap[$d]['unpaid_rep'] += $jam;
                $datesMap[$d]['total_unpaid'] += $jam;
            } elseif ($kId === 2) {
                $datesMap[$d]['unpaid_per'] += $jam;
                $datesMap[$d]['total_unpaid'] += $jam;
            } else {
                $datesMap[$d]['total_sbwc'] += $jam;
                if ($kId === 3) {
                    $datesMap[$d]['sbwc_rain'] += $jam;
                } elseif ($kId === 4) {
                    $datesMap[$d]['sbwc_road'] += $jam;
                } elseif ($kId === 5) {
                    $datesMap[$d]['sbwc_pad'] += $jam;
                } elseif ($kId === 6) {
                    $datesMap[$d]['sbwc_daylight'] += $jam;
                } elseif ($kId === 7) {
                    $datesMap[$d]['sbwc_perfo'] += $jam;
                } elseif ($kId === 8) {
                    $datesMap[$d]['sbwc_phr'] += $jam;
                } elseif ($kId === 9) {
                    $datesMap[$d]['sbwc_cpi'] += $jam;
                } elseif ($kId === 10) {
                    $tpName = $e['nama_tp'] ?: '3rd Party';
                    $datesMap[$d]['sbwc_tp'][$tpName] = ($datesMap[$d]['sbwc_tp'][$tpName] ?? 0.0) + $jam;
                    $datesMap[$d]['sbwc_tp_sum'] += $jam;
                    if ($tpId === 9) {
                        $datesMap[$d]['sbwc_pesi'] += $jam;
                    }
                } elseif ($kId === 11) {
                    $datesMap[$d]['sbwc_trans'] += $jam;
                } elseif ($kId === 12) {
                    $datesMap[$d]['sbwc_foam'] += $jam;
                } elseif (in_array($kId, [14, 19])) {
                    $datesMap[$d]['sbwc_cepe'] += $jam;
                } elseif ($kId === 15) {
                    $datesMap[$d]['sbwc_idul'] += $jam;
                } else {
                    $datesMap[$d]['sbwc_other'] += $jam;
                }
            }

            $datesMap[$d]['total_hrs'] += $jam;
            if (!empty($e['remark']) && !in_array($e['remark'], $datesMap[$d]['remarks'])) {
                $datesMap[$d]['remarks'][] = $e['remark'];
            }
            $datesMap[$d]['entries_raw'][] = $e;
        }

        // Tambahan Cerdas: Tarik seluruh catatan & kejadian dari daily_report_log untuk rig ini di bulan/tahun terpilih
        $dailyLogs = $db->table('daily_report_log drl')
            ->select('drl.tanggal, drl.total_dt, drl.remark_npt, drl.remark_unpaid, drl.dt_rig, drl.dt_tool, l.nama_lokasi, dr.no_well')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $rigId)
            ->where('MONTH(drl.tanggal)', $bulan)
            ->where('YEAR(drl.tanggal)', $tahun)
            ->where('(drl.total_dt > 0 OR (drl.remark_npt IS NOT NULL AND drl.remark_npt != "") OR (drl.remark_unpaid IS NOT NULL AND drl.remark_unpaid != ""))')
            ->get()->getResultArray();

        foreach ($dailyLogs as $dl) {
            $d = $dl['tanggal'];
            if (isset($datesMap[$d])) {
                // Perbarui nama lokasi jika sebelumnya belum terdeteksi
                if (($datesMap[$d]['nama_lokasi'] === '-' || empty($datesMap[$d]['nama_lokasi'])) && !empty($dl['nama_lokasi'])) {
                    $datesMap[$d]['nama_lokasi'] = $dl['nama_lokasi'] . ($dl['no_well'] ? " (Well #{$dl['no_well']})" : '');
                }
                // Masukkan narasi remark dari daily report log jika belum tercantum
                if (!empty($dl['remark_unpaid'])) {
                    $unpTag = '[UNPAID] ' . $dl['remark_unpaid'];
                    if (!in_array($dl['remark_unpaid'], $datesMap[$d]['remarks']) && !in_array($unpTag, $datesMap[$d]['remarks'])) {
                        $datesMap[$d]['remarks'][] = $unpTag;
                    }
                }
                if (!empty($dl['remark_npt'])) {
                    if (!in_array($dl['remark_npt'], $datesMap[$d]['remarks'])) {
                        $datesMap[$d]['remarks'][] = $dl['remark_npt'];
                    }
                }
            } else if ((float)$dl['total_dt'] > 0) {
                // Ada downtime di daily_report_log yang belum tercatat di npt_harian
                $dtHrs  = (float)$dl['total_dt'];
                $unpRep = (float)($dl['dt_rig'] ?? 0);
                $unpPer = (float)($dl['dt_tool'] ?? 0);
                $totUnp = $unpRep + $unpPer;
                $totSbwc = max(0, $dtHrs - $totUnp);
                $remList = [];
                if (!empty($dl['remark_unpaid'])) $remList[] = '[UNPAID] ' . $dl['remark_unpaid'];
                if (!empty($dl['remark_npt']) && !in_array($dl['remark_npt'], $remList)) $remList[] = $dl['remark_npt'];

                $datesMap[$d] = [
                    'tanggal'           => $d,
                    'tanggal_formatted' => date('d-M-Y', strtotime($d)),
                    'nama_lokasi'       => (!empty($dl['nama_lokasi']) ? $dl['nama_lokasi'] : '-') . ($dl['no_well'] ? " (Well #{$dl['no_well']})" : ''),
                    'unpaid_rep'        => $unpRep,
                    'unpaid_per'        => $unpPer,
                    'sbwc_rain'         => 0.0,
                    'sbwc_road'         => 0.0,
                    'sbwc_pad'          => 0.0,
                    'sbwc_daylight'     => 0.0,
                    'sbwc_perfo'        => 0.0,
                    'sbwc_phr'          => 0.0,
                    'sbwc_cpi'          => 0.0,
                    'sbwc_tp'           => [],
                    'sbwc_tp_sum'       => 0.0,
                    'sbwc_trans'        => 0.0,
                    'sbwc_foam'         => 0.0,
                    'sbwc_pesi'         => 0.0,
                    'sbwc_cepe'         => 0.0,
                    'sbwc_idul'         => 0.0,
                    'sbwc_other'        => $totSbwc,
                    'total_unpaid'      => $totUnp,
                    'total_sbwc'        => $totSbwc,
                    'total_hrs'         => $dtHrs,
                    'remarks'           => $remList,
                    'entries_raw'       => [],
                ];
            }
        }

        // Urutkan kembali berdasarkan tanggal
        ksort($datesMap);

        $result = [];
        $no = 1;
        foreach ($datesMap as $d => $row) {
            $row['no'] = $no++;
            $row['remark_str'] = !empty($row['remarks']) ? implode('; ', $row['remarks']) : '-';
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Endpoint AJAX untuk lembar kerja rig per bulan
     */
    public function ajaxRigSheet(int $rigId, int $bulan, ?int $tahun = null)
    {
        $tahun = $tahun ?: (int)$this->request->getGet('tahun') ?: (int)date('Y');
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Rig tidak ditemukan']);
        }

        $events = $this->getRigSheetEvents($rigId, $bulan, $tahun);
        $totalHrs = array_sum(array_column($events, 'total_hrs'));
        $totalUnpaid = array_sum(array_column($events, 'total_unpaid'));
        $totalSbwc = array_sum(array_column($events, 'total_sbwc'));

        return $this->response->setJSON([
            'status' => 'success',
            'rig' => [
                'id'       => (int)$rig['id'],
                'kode'     => $rig['kode'],
                'nama_rig' => $rig['nama_rig'],
            ],
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            'stats' => [
                'total_hrs'    => round($totalHrs, 2),
                'total_unpaid' => round($totalUnpaid, 2),
                'total_sbwc'   => round($totalSbwc, 2),
                'events_count' => count($events),
            ],
            'events' => $events
        ]);
    }

    /**
     * Simpan Event NPT Satuan Secara Cepat (Tanpa Form 31 Hari yang Padat)
     */
    public function simpanEvent()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $tanggal = $this->request->getPost('tanggal');
        $kategoriId = (int)$this->request->getPost('kategori_id');
        $thirdPartyId = $this->request->getPost('third_party_id') ? (int)$this->request->getPost('third_party_id') : null;
        $jam = (float)$this->request->getPost('jam');
        $remark = trim($this->request->getPost('remark') ?? '');
        $lokasiId = $this->request->getPost('lokasi_id') ? (int)$this->request->getPost('lokasi_id') : null;

        if (!$rigId || !$tanggal || !$kategoriId || $jam < 0) {
            return redirect()->back()->with('error', 'Data input NPT tidak lengkap.');
        }

        $bulan = (int)date('n', strtotime($tanggal));
        $tahun = (int)date('Y', strtotime($tanggal));

        $this->nptModel->upsertNpt(
            $rigId,
            $tanggal,
            $kategoriId,
            $thirdPartyId,
            $jam,
            $remark ?: null
        );

        if ($lokasiId) {
            $db = \Config\Database::connect();
            $db->table('npt_harian')
                ->where('rig_id', $rigId)
                ->where('tanggal', $tanggal)
                ->where('kategori_id', $kategoriId)
                ->update(['lokasi_id' => $lokasiId]);
        }

        // Sinkronisasi ke daily report log & monthly summary
        $this->nptModel->syncNptBackToDaily($rigId, $tanggal, $kategoriId, $jam, $remark);
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        ActivityLogModel::record(
            'NPT',
            'SIMPAN_EVENT',
            "Mencatat NPT {$rigKode} tanggal " . date('d/m/Y', strtotime($tanggal)) . " ({$jam} Jam).",
            $rigId
        );

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}?view=rig"))->with('success', "Catatan NPT {$rigKode} tanggal " . date('d/m/Y', strtotime($tanggal)) . " berhasil disimpan.");
    }

    /**
     * Hapus Baris Event NPT Satuan
     */
    public function hapusEvent()
    {
        $rigId = (int)$this->request->getPost('rig_id');
        $tanggal = $this->request->getPost('tanggal');
        $kategoriId = (int)$this->request->getPost('kategori_id');
        $thirdPartyId = $this->request->getPost('third_party_id') ? (int)$this->request->getPost('third_party_id') : null;

        $this->nptModel->hapusRow($rigId, $tanggal, $kategoriId, $thirdPartyId);

        $bulan = (int)date('n', strtotime($tanggal));
        $tahun = (int)date('Y', strtotime($tanggal));

        $this->nptModel->syncNptBackToDaily($rigId, $tanggal, $kategoriId, 0, '');
        $summaryModel = new MonthlySummaryModel();
        $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

        $rig = $this->rigModel->find($rigId);
        $rigKode = $rig['kode'] ?? "Rig #{$rigId}";
        ActivityLogModel::record(
            'NPT',
            'HAPUS_EVENT',
            "Menghapus catatan NPT {$rigKode} tanggal " . date('d/m/Y', strtotime($tanggal)) . ".",
            $rigId
        );

        return redirect()->to(base_url("npt/{$rigId}/{$bulan}/{$tahun}?view=rig"))->with('success', "Catatan NPT tanggal " . date('d/m/Y', strtotime($tanggal)) . " berhasil dihapus.");
    }
}

