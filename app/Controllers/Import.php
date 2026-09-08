<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\MonthlySummaryModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\LokasiModel;
use App\Models\NptModel;
use App\Models\DailyReportModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Import extends BaseController
{
    protected $rigModel;
    protected $monthlySummaryModel;
    protected $kategoriModel;
    protected $thirdPartyModel;
    protected $lokasiModel;
    protected $nptModel;
    protected $dailyReportModel;
    protected $db;

    const BULAN_MAP = [
        'JANUARI' => 1, 'JAN' => 1, 'JANUARY' => 1,
        'FEBRUARI' => 2, 'FEB' => 2, 'FEBRUARY' => 2,
        'MARET' => 3, 'MAR' => 3, 'MARCH' => 3,
        'APRIL' => 4, 'APR' => 4,
        'MEI' => 5, 'MAY' => 5,
        'JUNI' => 6, 'JUN' => 6, 'JUNE' => 6,
        'JULI' => 7, 'JUL' => 7, 'JULY' => 7,
        'AGUSTUS' => 8, 'AGS' => 8, 'AGT' => 8, 'AUGUST' => 8,
        'SEPTEMBER' => 9, 'SEP' => 9, 'SEPT' => 9,
        'OKTOBER' => 10, 'OKT' => 10, 'OCTOBER' => 10,
        'NOVEMBER' => 11, 'NOV' => 11,
        'DESEMBER' => 12, 'DES' => 12, 'DECEMBER' => 12,
    ];

    public function __construct()
    {
        $this->rigModel            = new RigModel();
        $this->monthlySummaryModel = new MonthlySummaryModel();
        $this->kategoriModel       = new KategoriDowntimeModel();
        $this->thirdPartyModel     = new ThirdPartyModel();
        $this->lokasiModel         = new LokasiModel();
        $this->nptModel            = new NptModel();
        $this->dailyReportModel    = new DailyReportModel();
        $this->db                  = \Config\Database::connect();
    }

    public function index()
    {
        $allRigs = $this->rigModel->getRigAktif();

        $totalDaily = $this->db->table('daily_report')->countAllResults();
        $totalNpt   = $this->db->table('npt_harian')->countAllResults();
        $totalWells = $this->db->table('lokasi')->countAllResults();

        $data = [
            'title'         => 'Import Excel Cerdas',
            'page_title'    => 'Pusat Unggah & Import Excel Cerdas',
            'page_subtitle' => 'Unggah berkas laporan harian (Daily Report), rekap bulanan (Monthly Report), atau lembar NPT',
            'allRigs'       => $allRigs,
            'totalDaily'    => $totalDaily,
            'totalNpt'      => $totalNpt,
            'totalWells'    => $totalWells,
        ];

        return view('import/index', $data);
    }

    public function proses()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Silakan pilih berkas Excel (.xlsx, .xls, .csv) yang valid.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return redirect()->back()->with('error', 'Format berkas tidak didukung. Mohon unggah file .xlsx, .xls, atau .csv.');
        }

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');

        $mode          = $this->request->getPost('mode') ?: 'auto';
        $selectedRigId = (int)$this->request->getPost('rig_id') ?: null;
        $bulanFallback = (int)$this->request->getPost('bulan') ?: (int)date('n');
        $tahunFallback = (int)$this->request->getPost('tahun') ?: (int)date('Y');

        // Prioritas Cerdas Periode:
        // 1. Pilihan Form Pengguna (jika dipilih eksplisit)
        // 2. Nama Berkas (e.g. 'September 2026')
        $clientFilename = $file->getClientName();
        list($fnBulan, $fnTahun) = $this->parseMonthYear($clientFilename, null, null);
        if ($fnBulan && $fnTahun) {
            $bulanFallback = $fnBulan;
            $tahunFallback = $fnTahun;
        }

        $tempPath = $file->getTempName();

        try {
            $reader = IOFactory::createReaderForFile($tempPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tempPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca berkas Excel: ' . $e->getMessage());
        }

        $allRigs     = $this->rigModel->findAll();
        $allKategori = $this->kategoriModel->findAll();
        $sheetNames  = $spreadsheet->getSheetNames();

        $results = [
            'total_sheets'     => count($sheetNames),
            'processed_sheets' => 0,
            'imported_wells'   => 0,
            'imported_npt'     => 0,
            'imported_summary' => 0,
            'new_locations'    => 0,
            'affected_rigs'    => [],
            'messages'         => [],
        ];

        $affectedKeys = [];

        // ═══════════════════════════════════════════════════════════════════
        // KASUS KHUSUS 1: File adalah Monthly Report (Ada sheet 'SUMMARY OPERATION')
        // ═══════════════════════════════════════════════════════════════════
        if ($spreadsheet->sheetNameExists('SUMMARY OPERATION')) {
            $sheetSO = $spreadsheet->getSheetByName('SUMMARY OPERATION');
            $resSO   = $this->importMonthlyReportSheet($sheetSO, $bulanFallback, $tahunFallback, $allRigs);

            $results['processed_sheets']++;
            $results['imported_summary'] += $resSO['count'];
            $results['messages'][] = "Sheet [SUMMARY OPERATION]: Berhasil mengimpor ringkasan operasi bulanan {$resSO['count']} armada rig (Periode {$resSO['bulan']}/{$resSO['tahun']}).";

            foreach ($resSO['rigs'] as $r) {
                $results['affected_rigs'][] = "{$r['kode']} ({$resSO['bulan']}/{$resSO['tahun']})";
            }

            // Jika ada sheet 'NPT ALL RIG', impor juga
            if ($spreadsheet->sheetNameExists('NPT ALL RIG')) {
                $sheetNptAll = $spreadsheet->getSheetByName('NPT ALL RIG');
                $resNptAll = $this->importNptAllRigSheet($sheetNptAll, $resSO['bulan'], $resSO['tahun'], $allRigs, $allKategori);
                $results['processed_sheets']++;
                $results['imported_npt'] += $resNptAll['count'];
                $results['messages'][] = "Sheet [NPT ALL RIG]: Berhasil mengimpor {$resNptAll['count']} catatan downtime untuk seluruh rig.";
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            $results['affected_rigs'] = array_unique($results['affected_rigs']);
            return redirect()->to(base_url('import'))->with('import_results', $results);
        }

        // ═══════════════════════════════════════════════════════════════════
        // KASUS KHUSUS 2: File adalah Rekap NPT Tahunan (Ada sheet 'SUMMARY ALL RIG')
        // ═══════════════════════════════════════════════════════════════════
        if ($spreadsheet->sheetNameExists('SUMMARY ALL RIG')) {
            $sheetSAR = $spreadsheet->getSheetByName('SUMMARY ALL RIG');
            $resSAR   = $this->importRekapNptTahunanSheet($sheetSAR, $allRigs, $allKategori);

            $results['processed_sheets']++;
            $results['imported_npt'] += $resSAR['count'];
            $results['messages'][] = "Sheet [SUMMARY ALL RIG]: Berhasil mengimpor rekap NPT tahunan untuk {$resSAR['count']} catatan downtime pada {$resSAR['months_count']} bulan operasi.";

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return redirect()->to(base_url('import'))->with('import_results', $results);
        }

        // ═══════════════════════════════════════════════════════════════════
        // KASUS UMUM: Iterasi Seluruh Sheet Rig (Daily Report / NPT Multi-Month)
        // ═══════════════════════════════════════════════════════════════════
        foreach ($sheetNames as $sName) {
            $sheet = $spreadsheet->getSheetByName($sName);
            if (!$sheet) continue;

            $rigId = $this->resolveRigId($sName, $sheet, $selectedRigId, $allRigs);
            if (!$rigId) {
                $results['messages'][] = "Sheet [{$sName}]: Dilewati (bukan lembar data rig).";
                continue;
            }

            $rigInfo = $this->findRigById($rigId, $allRigs);
            $rigKode = $rigInfo['kode'] ?? "Rig #{$rigId}";

            $detectedMode = $mode;
            if ($mode === 'auto') {
                $detectedMode = $this->detectSheetFormat($sheet);
            }

            if ($detectedMode === 'daily_report') {
                $res = $this->importDailyReportMultiMonth($sheet, $rigId, $bulanFallback, $tahunFallback, $allKategori);
                if ($res['count'] > 0) {
                    $results['imported_wells'] += $res['count'];
                    $results['new_locations']  += $res['new_loc'];
                    $results['processed_sheets']++;
                    $results['messages'][] = "Sheet [{$sName}] ({$rigKode}): Berhasil mengimpor {$res['count']} baris pekerjaan sumur.";

                    foreach ($res['periods'] as $p) {
                        $affectedKeys["{$rigId}_{$p['bulan']}_{$p['tahun']}"] = [
                            'rig_id' => $rigId, 'bulan' => $p['bulan'], 'tahun' => $p['tahun'], 'kode' => $rigKode
                        ];
                    }
                } else {
                    $results['messages'][] = "Sheet [{$sName}] ({$rigKode}): Tidak ada data sumur valid (hanya template kosong).";
                }
            } elseif ($detectedMode === 'npt') {
                $res = $this->importNptSheet($sheet, $rigId, $bulanFallback, $tahunFallback, $allKategori);
                if ($res['count'] > 0) {
                    $results['imported_npt'] += $res['count'];
                    $results['processed_sheets']++;
                    $results['messages'][] = "Sheet [{$sName}] ({$rigKode}): Berhasil mengimpor {$res['count']} catatan downtime (NPT).";

                    foreach ($res['periods'] as $p) {
                        $affectedKeys["{$rigId}_{$p['bulan']}_{$p['tahun']}"] = [
                            'rig_id' => $rigId, 'bulan' => $p['bulan'], 'tahun' => $p['tahun'], 'kode' => $rigKode
                        ];
                    }
                }
            }
        }

        // Hapus rekaman dummy tanpa lokasi dan tanpa jam kerja
        $this->db->table('daily_report')
            ->like('remark', 'Auto-generated placeholder')
            ->orGroupStart()
                ->where('miru_jam', 0)
                ->where('ops_jam', 0)
                ->where('total_jam', 0)
                ->where('lokasi_id IS NULL')
            ->groupEnd()
            ->delete();

        // Rekalkulasi Monthly Summary untuk seluruh periode rig yang terpengaruh
        foreach ($affectedKeys as $k => $item) {
            $this->monthlySummaryModel->hitungDanSimpan($item['rig_id'], $item['bulan'], $item['tahun']);
            $results['affected_rigs'][] = "{$item['kode']} (Bulan {$item['bulan']}/{$item['tahun']})";
        }

        $results['affected_rigs'] = array_unique($results['affected_rigs']);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return redirect()->to(base_url('import'))->with('import_results', $results);
    }

    /**
     * Import Sheet SUMMARY OPERATION dari file Monthly Report
     */
    private function importMonthlyReportSheet($sheet, int $defBulan, int $defTahun, array $allRigs): array
    {
        // Cari bulan dan tahun di Cell B2..C3
        $titleVal = '';
        for ($r = 1; $r <= 3; $r++) {
            for ($c = 1; $c <= 4; $c++) {
                $titleVal .= ' ' . (string)$this->getCellValue($sheet, $c, $r);
            }
        }

        // Utamakan bulan & tahun dari pilihan form / nama file
        $bulan = $defBulan;
        $tahun = $defTahun;
        if (!$bulan || !$tahun) {
            list($bulan, $tahun) = $this->parseMonthYear($titleVal, $defBulan, $defTahun);
        }

        $highestRow = min(35, $sheet->getHighestRow());
        $count = 0;
        $rigsUpdated = [];

        // Data rig biasanya ada di baris 5 s/d 24
        for ($r = 4; $r <= $highestRow; $r++) {
            $kodeVal = trim((string)$this->getCellValue($sheet, 3, $r)); // Col C (NAME RIG)
            if (empty($kodeVal) || in_array(strtoupper($kodeVal), ['NAME RIG', 'AVERAGE', 'TOTAL', 'SUMMARY', '-'])) {
                continue;
            }

            $rigId = $this->findRigIdByCode($kodeVal, $allRigs);
            if (!$rigId) continue;

            $rel   = $this->toFloat($this->getCellValue($sheet, 4, $r)); // Col D
            $ava   = $this->toFloat($this->getCellValue($sheet, 5, $r)); // Col E
            $uti   = $this->toFloat($this->getCellValue($sheet, 6, $r)); // Col F
            $miru  = $this->toFloat($this->getCellValue($sheet, 7, $r)); // Col G
            $ops   = $this->toFloat($this->getCellValue($sheet, 8, $r)); // Col H
            $aMiru = $this->toFloat($this->getCellValue($sheet, 9, $r)); // Col I
            $aCyc  = $this->toFloat($this->getCellValue($sheet, 10, $r)); // Col J
            $wells = (int)$this->toFloat($this->getCellValue($sheet, 11, $r)); // Col K
            $sbwc  = $this->toFloat($this->getCellValue($sheet, 12, $r)); // Col L
            $unpd  = $this->toFloat($this->getCellValue($sheet, 13, $r)); // Col M
            $rTgt  = $this->toFloat($this->getCellValue($sheet, 14, $r)); // Col N
            $rAct  = $this->toFloat($this->getCellValue($sheet, 15, $r)); // Col O
            $tHrs  = $this->toFloat($this->getCellValue($sheet, 16, $r)); // Col P
            $rmk   = trim((string)$this->getCellValue($sheet, 17, $r));  // Col Q

            $saveData = [
                'rig_id'          => $rigId,
                'bulan'           => $bulan,
                'tahun'           => $tahun,
                'reliability'     => $rel,
                'availability'    => $ava,
                'utilization'     => $uti,
                'total_miru'      => $miru,
                'total_ops'       => $ops,
                'avg_miru'        => $aMiru,
                'avg_cycle_time'  => $aCyc,
                'total_well_job'  => $wells,
                'sbwc_jam'        => $sbwc,
                'unpaid_jam'      => $unpd,
                'revenue_target'  => (int)$rTgt,
                'revenue_actual'  => (int)$rAct,
                'total_jam'       => $tHrs ?: 720.0,
                'remark'          => $rmk ?: null,
            ];

            $this->monthlySummaryModel->upsertSummary($saveData);
            $count++;
            $rigsUpdated[] = ['kode' => $kodeVal];
        }

        return [
            'count' => $count,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'rigs'  => $rigsUpdated,
        ];
    }

    /**
     * Import Sheet NPT ALL RIG dari Monthly Report
     */
    private function importNptAllRigSheet($sheet, int $bulan, int $tahun, array $allRigs, array $allKategori): array
    {
        $highestRow = min(35, $sheet->getHighestRow());
        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

        // Header kategori ada di baris 4 atau 5
        $katMap = [];
        for ($c = 4; $c <= $highestCol; $c++) {
            $val = strtoupper(trim((string)$this->getCellValue($sheet, $c, 4) . ' ' . (string)$this->getCellValue($sheet, $c, 5)));
            if (empty($val) || strpos($val, 'TOTAL') !== false) continue;

            foreach ($allKategori as $kat) {
                $kName = strtoupper(trim($kat['nama']));
                if (strpos($val, $kName) !== false || strpos($kName, $val) !== false) {
                    $katMap[$c] = (int)$kat['id'];
                    break;
                }
            }
        }

        $imported = 0;
        $tglStr   = sprintf('%04d-%02d-01', $tahun, $bulan);

        for ($r = 6; $r <= $highestRow; $r++) {
            $kodeVal = trim((string)$this->getCellValue($sheet, 3, $r));
            if (empty($kodeVal) || in_array(strtoupper($kodeVal), ['TOTAL', 'NAME RIG', 'AVERAGE'])) continue;

            $rigId = $this->findRigIdByCode($kodeVal, $allRigs);
            if (!$rigId) continue;

            foreach ($katMap as $col => $katId) {
                $jam = $this->toFloat($this->getCellValue($sheet, $col, $r));
                if ($jam > 0) {
                    $this->nptModel->upsertNpt($rigId, $tglStr, $katId, null, $jam, 'Imported from NPT ALL RIG');
                    $imported++;
                }
            }
        }

        return ['count' => $imported];
    }

    /**
     * Import Sheet SUMMARY ALL RIG dari Rekap NPT Tahunan
     */
    private function importRekapNptTahunanSheet($sheet, array $allRigs, array $allKategori): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $count = 0;
        $monthsCount = 0;

        // Scan seluruh baris mencari blok bulan (e.g. "JANUARI 2026", "FEBRUARI 2026")
        for ($r = 1; $r <= $highestRow; $r++) {
            $cellA = strtoupper(trim((string)$this->getCellValue($sheet, 1, $r)));
            if (empty($cellA)) continue;

            list($bulan, $tahun) = $this->parseMonthYear($cellA, null, 2026);
            if ($bulan) {
                $monthsCount++;
                $tglStr = sprintf('%04d-%02d-01', $tahun, $bulan);

                // Baris data dimulai sekitar r + 4 sampai menemukan baris TOTAL
                for ($dataRow = $r + 3; $dataRow <= min($r + 30, $highestRow); $dataRow++) {
                    $rigKode = trim((string)$this->getCellValue($sheet, 2, $dataRow));
                    if (empty($rigKode) || strtoupper($rigKode) === 'TOTAL') {
                        break;
                    }

                    $rigId = $this->findRigIdByCode($rigKode, $allRigs);
                    if (!$rigId) continue;

                    // Unpaid di Col C, Col D
                    $unpaid1 = $this->toFloat($this->getCellValue($sheet, 3, $dataRow));
                    $unpaid2 = $this->toFloat($this->getCellValue($sheet, 4, $dataRow));
                    $totalUnpaid = $unpaid1 + $unpaid2;

                    // Total di Col AG (Col 33)
                    $totalDt = $this->toFloat($this->getCellValue($sheet, 33, $dataRow));
                    $totalSbwc = max(0, $totalDt - $totalUnpaid);

                    // Update summary bulanan untuk rig ini
                    $curSum = $this->monthlySummaryModel->where('rig_id', $rigId)->where('bulan', $bulan)->where('tahun', $tahun)->first();
                    $updateData = [
                        'rig_id'     => $rigId,
                        'bulan'      => $bulan,
                        'tahun'      => $tahun,
                        'sbwc_jam'   => $totalSbwc,
                        'unpaid_jam' => $totalUnpaid,
                    ];
                    $this->monthlySummaryModel->upsertSummary(array_merge($curSum ?: [], $updateData));
                    $count++;
                }
            }
        }

        return ['count' => $count, 'months_count' => $monthsCount];
    }

    /**
     * Import Daily Report yang Memiliki Banyak Blok Bulan (seperti REKAP DAILY REPORT THN 2026 SYS.xls)
     */
    private function importDailyReportMultiMonth($sheet, int $rigId, int $defBulan, int $defTahun, array $allKategori): array
    {
        $highestRow = $sheet->getHighestRow();
        $imported   = 0;
        $newLoc     = 0;
        $periods    = [];

        $currentBulan = $defBulan;
        $currentTahun = $defTahun;

        for ($r = 1; $r <= $highestRow; $r++) {
            $rowTitle = strtoupper(trim((string)$this->getCellValue($sheet, 2, $r) . ' ' . (string)$this->getCellValue($sheet, 1, $r)));

            // Cek apakah ada header blok bulan baru
            if (strpos($rowTitle, 'SUMMARY REPORT PER WELL') !== false) {
                list($b, $y) = $this->parseMonthYear($rowTitle, null, $defTahun);
                if ($b) {
                    $currentBulan = $b;
                    $currentTahun = $y;
                }
                continue;
            }

            // Cek apakah ini baris nomor sumur (Col B = 1, 2, 3...)
            $noVal = trim((string)$this->getCellValue($sheet, 2, $r));
            if (!is_numeric($noVal) || (int)$noVal <= 0) {
                continue;
            }

            $noWell = (int)$noVal;

            // Lokasi sumur ada di Col C (Col 3)
            $namaLokasi = trim((string)$this->getCellValue($sheet, 3, $r));
            $miruJam    = $this->toFloat($this->getCellValue($sheet, 6, $r)); // Col F (MIRU)
            $opsJam     = $this->toFloat($this->getCellValue($sheet, 7, $r)); // Col G (OPS)
            $totalDt    = $this->toFloat($this->getCellValue($sheet, 22, $r)); // Col V (TOTAL DT)
            $totalJam   = $this->toFloat($this->getCellValue($sheet, 23, $r)); // Col W (TOTAL HRS)

            // Abaikan baris kalkulasi footer atau header Excel
            $badNames = ['AVAILIBILITY', 'UTILITITAION', 'AVERANGE MIRU', 'AVERAGE', 'SCHEDULE MTC', 'PRESENTASE', 'REVENUE', 'REALIBILITY', 'TOTAL', 'GRAND TOTAL'];
            $cleanLocUpper = strtoupper($namaLokasi);
            foreach ($badNames as $bn) {
                if (strpos($cleanLocUpper, $bn) !== false) {
                    continue 2;
                }
            }

            // JANGAN pernah buat sumur dummy jika nama lokasi kosong dan jam kerja 0!
            if (empty($namaLokasi) && $miruJam == 0 && $opsJam == 0 && $totalDt == 0) {
                continue;
            }
            if (empty($namaLokasi) || $namaLokasi === '-') {
                continue; // Jangan buat SUMUR # dummy!
            }

            // Lokasi
            $lokasiId = $this->findOrCreateLokasi($namaLokasi, $newLoc);

            // Tanggal
            $tglVal = $this->getCellValue($sheet, 4, $r); // Col D
            $tglMulai = $this->parseExcelDate($tglVal) ?: sprintf('%04d-%02d-01', $currentTahun, $currentBulan);
            $tglSelesai = $tglMulai;

            $bulan = (int)date('n', strtotime($tglMulai)) ?: $currentBulan;
            $tahun = (int)date('Y', strtotime($tglMulai)) ?: $currentTahun;
            $periods["{$bulan}_{$tahun}"] = ['bulan' => $bulan, 'tahun' => $tahun];

            $jarak  = $this->toFloat($this->getCellValue($sheet, 5, $r)); // Col E (DISTANCE)
            $status = trim((string)$this->getCellValue($sheet, 25, $r)) ?: 'COMPLETED'; // Col Y
            $remark = trim((string)$this->getCellValue($sheet, 24, $r)) ?: ''; // Col X

            $existing = $this->db->table('daily_report')
                ->where('rig_id', $rigId)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('no_well', $noWell)
                ->get()->getRowArray();

            $saveData = [
                'rig_id'          => $rigId,
                'lokasi_id'       => $lokasiId,
                'no_well'         => $noWell,
                'tanggal_mulai'   => $tglMulai,
                'tanggal_selesai' => $tglSelesai,
                'jarak'           => $jarak,
                'miru_jam'        => $miruJam,
                'ops_jam'         => $opsJam,
                'total_dt'        => $totalDt,
                'total_jam'       => $totalJam ?: ($miruJam + $opsJam + $totalDt),
                'status_job'      => $status,
                'remark'          => $remark,
                'bulan'           => $bulan,
                'tahun'           => $tahun,
            ];

            if ($existing) {
                $this->db->table('daily_report')->where('id', $existing['id'])->update($saveData);
            } else {
                $saveData['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('daily_report')->insert($saveData);
            }

            $imported++;
        }

        return [
            'count'   => $imported,
            'new_loc' => $newLoc,
            'periods' => array_values($periods),
        ];
    }

    /**
     * Import Sheet NPT Harian
     */
    private function importNptSheet($sheet, int $rigId, int $defBulan, int $defTahun, array $allKategori): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $headerRow = 3;
        for ($r = 1; $r <= min(8, $highestRow); $r++) {
            for ($c = 1; $c <= $highestCol; $c++) {
                $val = strtoupper(trim((string)$this->getCellValue($sheet, $c, $r)));
                if (strpos($val, 'REPAIRE') !== false || strpos($val, 'PERSONEL') !== false || strpos($val, 'RAIN') !== false) {
                    $headerRow = $r;
                    break 2;
                }
            }
        }

        $katMap = [];
        $remarkCol = null;

        for ($c = 3; $c <= $highestCol; $c++) {
            $cVal = strtoupper(trim((string)$this->getCellValue($sheet, $c, $headerRow)));
            if (strpos($cVal, 'REMARK') !== false) {
                $remarkCol = $c;
                continue;
            }

            foreach ($allKategori as $kat) {
                $kName = strtoupper(trim($kat['nama']));
                if ($cVal === $kName || strpos($cVal, $kName) !== false || strpos($kName, $cVal) !== false) {
                    $katMap[$c] = (int)$kat['id'];
                    break;
                }
            }
        }

        $imported = 0;
        $periods  = ["{$defBulan}_{$defTahun}" => ['bulan' => $defBulan, 'tahun' => $defTahun]];

        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $dayVal = trim((string)$this->getCellValue($sheet, 1, $r));
            if (!is_numeric($dayVal)) {
                $dayVal = trim((string)$this->getCellValue($sheet, 2, $r));
            }

            if (!is_numeric($dayVal) || (int)$dayVal < 1 || (int)$dayVal > 31) {
                continue;
            }

            $dayNum = (int)$dayVal;
            $tglStr = sprintf('%04d-%02d-%02d', $defTahun, $defBulan, $dayNum);
            $remark = $remarkCol ? trim((string)$this->getCellValue($sheet, $remarkCol, $r)) : null;

            foreach ($katMap as $col => $katId) {
                $jamVal = $this->toFloat($this->getCellValue($sheet, $col, $r));
                if ($jamVal > 0) {
                    $this->nptModel->upsertNpt($rigId, $tglStr, $katId, null, $jamVal, $remark);
                    $imported++;
                }
            }
        }

        return [
            'count'   => $imported,
            'periods' => array_values($periods),
        ];
    }

    /**
     * Helpers & Resolvers
     */
    private function resolveRigId(string $sheetName, $sheet, ?int $fallbackRigId, array $allRigs): ?int
    {
        $cleanName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $sheetName));

        $ignoreKeywords = ['SUMMARY', 'REKAP', 'COVER', 'TOTAL', 'ALLRIG', 'AVERAGE', 'RAU'];
        foreach ($ignoreKeywords as $ik) {
            if (strpos($cleanName, $ik) !== false && strpos($cleanName, 'BMS') === false) {
                return null;
            }
        }

        foreach ($allRigs as $r) {
            $cleanKode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $r['kode']));
            $cleanNama = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $r['nama_rig']));

            if ($cleanName === $cleanKode || $cleanName === $cleanNama) {
                return (int)$r['id'];
            }
            if (strpos($cleanName, $cleanKode) !== false || strpos($cleanKode, $cleanName) !== false) {
                return (int)$r['id'];
            }
            $shortKode = str_replace('#', '', strtoupper($r['kode']));
            if (strpos($cleanName, $shortKode) !== false) {
                return (int)$r['id'];
            }
        }

        for ($r = 1; $r <= 3; $r++) {
            for ($c = 1; $c <= 4; $c++) {
                $val = strtoupper((string)$this->getCellValue($sheet, $c, $r));
                foreach ($allRigs as $rg) {
                    $cleanK = str_replace('#', '', strtoupper($rg['kode']));
                    if (strpos($val, $cleanK) !== false) {
                        return (int)$rg['id'];
                    }
                }
            }
        }

        return $fallbackRigId;
    }

    private function findRigById(int $id, array $allRigs): ?array
    {
        foreach ($allRigs as $r) {
            if ((int)$r['id'] === $id) return $r;
        }
        return null;
    }

    private function findRigIdByCode(string $code, array $allRigs): ?int
    {
        $cleanCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $code));
        foreach ($allRigs as $r) {
            $cleanK = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $r['kode']));
            $cleanN = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $r['nama_rig']));
            if ($cleanCode === $cleanK || $cleanCode === $cleanN) return (int)$r['id'];
            if (strpos($cleanCode, $cleanK) !== false || strpos($cleanK, $cleanCode) !== false) return (int)$r['id'];
        }
        return null;
    }

    private function detectSheetFormat($sheet): string
    {
        $highestRow = min(15, $sheet->getHighestRow());
        $highestCol = min(30, Coordinate::columnIndexFromString($sheet->getHighestColumn()));

        $textDump = '';
        for ($r = 1; $r <= $highestRow; $r++) {
            for ($c = 1; $c <= $highestCol; $c++) {
                $val = (string)$this->getCellValue($sheet, $c, $r);
                if ($val) $textDump .= ' ' . strtoupper($val);
            }
        }

        if (strpos($textDump, 'LOCATION') !== false || strpos($textDump, 'DISTANCE') !== false || (strpos($textDump, 'MIRU') !== false && strpos($textDump, 'OPS') !== false)) {
            return 'daily_report';
        }
        if (strpos($textDump, 'UNPAID') !== false || strpos($textDump, 'REPAIRE') !== false || strpos($textDump, 'STAND BY WITH CREW') !== false) {
            return 'npt';
        }
        return 'daily_report';
    }

    private function parseMonthYear(string $text, ?int $defBulan, ?int $defTahun): array
    {
        $textUpper = strtoupper($text);
        $bulan = $defBulan;
        foreach (self::BULAN_MAP as $bname => $bnum) {
            if (preg_match('/\b' . $bname . '\b/', $textUpper)) {
                $bulan = $bnum;
                break;
            }
        }
        $tahun = $defTahun;
        if (preg_match('/\b(202\d)\b/', $textUpper, $matches)) {
            $tahun = (int)$matches[1];
        }
        return [$bulan, $tahun];
    }

    private function findOrCreateLokasi(string $namaLokasi, int &$newLocCount): int
    {
        $existing = $this->lokasiModel->where('nama_lokasi', $namaLokasi)->first();
        if ($existing) return (int)$existing['id'];

        $newId = $this->lokasiModel->insert(['nama_lokasi' => $namaLokasi, 'aktif' => 1]);
        $newLocCount++;
        return (int)$newId;
    }

    private function toFloat($val): float
    {
        if (empty($val)) return 0.0;
        if ($val instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            $val = $val->getPlainText();
        }
        if (is_numeric($val)) return (float)$val;
        $clean = str_replace([',', ' ', 'Rp', 'rp', 'RP'], '', trim((string)$val));
        return is_numeric($clean) ? (float)$clean : 0.0;
    }

    private function getCellValue($sheet, int $col, int $row)
    {
        $colLetter = Coordinate::stringFromColumnIndex($col);
        $cell = $sheet->getCell("{$colLetter}{$row}");
        if (!$cell) return null;
        $val = $cell->getValue();
        if ($val instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            return $val->getPlainText();
        }
        return $val;
    }

    private function parseExcelDate($cellValue): ?string
    {
        if (empty($cellValue)) return null;
        if (is_numeric($cellValue)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($cellValue);
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        $str = trim((string)$cellValue);
        $time = strtotime($str);
        if ($time !== false && $time > 0) {
            return date('Y-m-d', $time);
        }
        return null;
    }

    public function template(string $jenis)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridLines(true);

        if ($jenis === 'daily-report') {
            $sheet->setTitle('DAILY_REPORT_TEMPLATE');
            $sheet->setCellValue('B2', 'TEMPLATE IMPORT DAILY REPORT (SUMMARY PER WELL) - SIMOR BMS');
            $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('002060');

            $headers = ['NO', 'LOCATION', 'TGL MULAI', 'TGL SELESAI', 'DISTANCE (Rp)', 'MIRU (JAM)', 'OPS (JAM)', 'DOWNTIME (JAM)', 'TOTAL JAM', 'STATUS JOB', 'REMARK'];
            $colIdx = 2;
            foreach ($headers as $h) {
                $cL = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$cL}4", $h);
                $colIdx++;
            }
            $endCol = Coordinate::stringFromColumnIndex($colIdx - 1);

            $sheet->getStyle("B4:{$endCol}4")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002060']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $samples = [
                [1, 'MINAS #501', '2026-09-01', '2026-09-02', 0, 10.5, 14.5, 0, 25.0, 'COMPLETED', 'Operasi lancar'],
                [2, 'DURI #820', '2026-09-03', '2026-09-05', 500000, 8.0, 36.0, 4.0, 48.0, 'COMPLETED', 'SWA Rain 4 jam'],
            ];
            $r = 5;
            foreach ($samples as $s) {
                for ($i = 0; $i < count($s); $i++) {
                    $cL = Coordinate::stringFromColumnIndex($i + 2);
                    $sheet->setCellValue("{$cL}{$r}", $s[$i]);
                }
                $r++;
            }

            foreach (range('B', $endCol) as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $filename = 'Template_Import_Daily_Report_SIMOR.xlsx';
        } else {
            $sheet->setTitle('NPT_TEMPLATE');
            $sheet->setCellValue('B2', 'TEMPLATE IMPORT NPT HARIAN - SIMOR BMS');
            $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('002060');

            $kategoriList = $this->kategoriModel->getKategoriAktif();
            $sheet->setCellValue('B4', 'NO');
            $sheet->setCellValue('C4', 'TANGGAL');

            $colIdx = 4;
            foreach ($kategoriList as $k) {
                $cL = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$cL}4", $k['nama']);
                $colIdx++;
            }
            $cLRem = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$cLRem}4", 'REMARK');

            $endCol = $cLRem;
            $sheet->getStyle("B4:{$endCol}4")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002060']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            for ($d = 1; $d <= 5; $d++) {
                $row = 4 + $d;
                $sheet->setCellValue("B{$row}", $d);
                $sheet->setCellValue("C{$row}", sprintf('2026-09-%02d', $d));
            }

            for ($c = 2; $c <= $colIdx; $c++) {
                $colL = Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colL)->setAutoSize(true);
            }

            $filename = 'Template_Import_NPT_Harian_SIMOR.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
