<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\MonthlySummaryModel;
use App\Models\KategoriDowntimeModel;
use App\Models\NptModel;
use App\Models\DailyReportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Export extends BaseController
{
    protected $rigModel;
    protected $monthlySummaryModel;
    protected $kategoriModel;
    protected $nptModel;
    protected $dailyReportModel;
    protected $db;

    // Palette Konstanta Standar Besmindo (Excel SYS)
    const COLOR_NAVY    = '002060'; // Header Utama Navy Blue
    const COLOR_SLATE   = '1E293B'; // Header Sekunder Slate Dark
    const COLOR_BLUE_HD = '0F4C81'; // Header Classic Blue
    const COLOR_PEACH   = 'FCE4D6'; // Kolom RIG & Total Hours
    const COLOR_YELLOW  = 'FFFF00'; // Baris Total Akhir
    const COLOR_GOLD    = 'FFC000'; // Baris Rata-rata Average
    const COLOR_ZEBRA   = 'F8FAFC'; // Baris Selang-seling Soft
    const COLOR_BORDER  = '94A3B8'; // Border Abu-abu Jelas
    const COLOR_RED     = 'FF0000'; // Angka UNPAID Merah

    const BULAN_NAMES = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
        5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
        9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
    ];

    public function __construct()
    {
        $this->rigModel            = new RigModel();
        $this->monthlySummaryModel = new MonthlySummaryModel();
        $this->kategoriModel       = new KategoriDowntimeModel();
        $this->nptModel            = new NptModel();
        $this->dailyReportModel    = new DailyReportModel();
        $this->db                  = \Config\Database::connect();
    }

    /**
     * Helper: Format Banner Judul Perusahaan di bagian atas Sheet
     */
    private function renderCompanyHeader($sheet, string $reportTitle, string $periodTitle, string $endCol = 'Q')
    {
        // Baris 1: Nama Perusahaan
        $sheet->setCellValue('B1', 'PT. BESMINDO MATERI SEWATAMA');
        $sheet->getStyle('B1')->getFont()->setName('Calibri')->setSize(14)->setBold(true)->getColor()->setRGB('002060');

        // Baris 2: Judul Laporan
        $sheet->setCellValue('B2', strtoupper($reportTitle));
        $sheet->getStyle('B2')->getFont()->setName('Calibri')->setSize(12)->setBold(true)->getColor()->setRGB('1E293B');

        // Baris 3: Periode Laporan
        $sheet->setCellValue('B3', strtoupper($periodTitle));
        $sheet->getStyle('B3')->getFont()->setName('Calibri')->setSize(10)->setItalic(true)->getColor()->setRGB('475569');

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(18);
    }

    /**
     * Helper: Styling Header Tabel Standar Besmindo (Navy #002060, Font Putih Tebal, Center)
     */
    private function applyHeaderStyle($sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'name'  => 'Calibri',
                'size'  => 10,
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::COLOR_NAVY],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);
    }

    /**
     * Helper: Styling Data Cell Grid Borders
     */
    private function applyGridBorders($sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => self::COLOR_BORDER],
                ],
            ],
        ]);
    }

    /**
     * Helper: Auto Fit Column Widths dengan safety padding
     */
    private function autoFitColumns($sheet, string $startCol, string $endCol, int $minWidth = 12)
    {
        $startIdx = Coordinate::columnIndexFromString($startCol);
        $endIdx   = Coordinate::columnIndexFromString($endCol);

        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }

    /**
     * =========================================================================
     * 1. EXPORT MONTHLY REPORT (SUMMARY OPERATION + NPT ALL RIG + TOTAL WELL)
     * Persis Format Asli Excel Monthly Report September 2026 SYS.xlsx
     * =========================================================================
     */
    public function monthlyReport(int $bulan, int $tahun)
    {
        $spreadsheet = new Spreadsheet();
        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";

        // ─────────────────────────────────────────────────────────────
        // SHEET 1: SUMMARY OPERATION
        // ─────────────────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SUMMARY OPERATION');
        $sheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheet,
            'SUMMARY REPORT OPERATION RIG BMS',
            "PERIODE: {$bulanName} {$tahun}",
            'Q'
        );

        // Header Baris 5 & 6 (Merged Headers)
        $sheet->setCellValue('B5', 'NO');
        $sheet->setCellValue('C5', 'NAME RIG');
        $sheet->setCellValue('D5', 'RAU');
        $sheet->setCellValue('G5', 'TOTAL MIRU (HRS)');
        $sheet->setCellValue('H5', 'TOTAL OPS (HRS)');
        $sheet->setCellValue('I5', 'AVERAGE MIRU (HRS)');
        $sheet->setCellValue('J5', 'AVERAGE CYCLE TIME (HRS)');
        $sheet->setCellValue('K5', 'TOTAL WELL JOB');
        $sheet->setCellValue('L5', 'DOWNTIME (HRS)');
        $sheet->setCellValue('N5', 'REVENUE');
        $sheet->setCellValue('P5', 'TOTAL HOURS');
        $sheet->setCellValue('Q5', 'REMARK');

        // Sub-headers Baris 6
        $sheet->setCellValue('D6', 'RELIABILITY (%)');
        $sheet->setCellValue('E6', 'AVAILABILITY (%)');
        $sheet->setCellValue('F6', 'UTILIZATION (%)');
        $sheet->setCellValue('L6', 'SBWC (HRS)');
        $sheet->setCellValue('M6', 'UNPAID (HRS)');
        $sheet->setCellValue('N6', 'TARGET (Rp)');
        $sheet->setCellValue('O6', 'ACTUAL (Rp)');

        // Merge Vertikal & Horizontal
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        $sheet->mergeCells('D5:F5');
        $sheet->mergeCells('G5:G6');
        $sheet->mergeCells('H5:H6');
        $sheet->mergeCells('I5:I6');
        $sheet->mergeCells('J5:J6');
        $sheet->mergeCells('K5:K6');
        $sheet->mergeCells('L5:M5');
        $sheet->mergeCells('N5:O5');
        $sheet->mergeCells('P5:P6');
        $sheet->mergeCells('Q5:Q6');

        $this->applyHeaderStyle($sheet, 'B5:Q6');
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->getRowDimension(6)->setRowHeight(24);

        // Ambil data summary
        $summaries = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);
        $startRow  = 7;
        $row       = $startRow;
        $no        = 1;

        foreach ($summaries as $s) {
            $sheet->setCellValue("B{$row}", $no++);
            $sheet->setCellValue("C{$row}", $s['kode']);
            $sheet->setCellValue("D{$row}", (float)($s['reliability'] ?? 0));
            $sheet->setCellValue("E{$row}", (float)($s['availability'] ?? 0));
            $sheet->setCellValue("F{$row}", (float)($s['utilization'] ?? 0));
            $sheet->setCellValue("G{$row}", (float)($s['total_miru'] ?? 0));
            $sheet->setCellValue("H{$row}", (float)($s['total_ops'] ?? 0));
            $sheet->setCellValue("I{$row}", (float)($s['avg_miru'] ?? 0));
            $sheet->setCellValue("J{$row}", (float)($s['avg_cycle_time'] ?? 0));
            $sheet->setCellValue("K{$row}", (int)($s['total_well_job'] ?? 0));
            $sheet->setCellValue("L{$row}", (float)($s['sbwc_jam'] ?? 0));
            $sheet->setCellValue("M{$row}", (float)($s['unpaid_jam'] ?? 0));
            $sheet->setCellValue("N{$row}", (float)($s['revenue_target'] ?? 0));
            $sheet->setCellValue("O{$row}", (float)($s['revenue_actual'] ?? 0));
            $sheet->setCellValue("P{$row}", (float)($s['total_jam'] ?? 0));
            $rawRem = trim((string)($s['remark'] ?? ''));
            $remDisplay = '-';
            if ($rawRem !== '') {
                $decRem = json_decode($rawRem, true);
                if (is_array($decRem) && isset($decRem['notes'])) {
                    $nList = [];
                    foreach (($decRem['notes'] ?? []) as $ni) {
                        $lbl = trim(($ni['lokasi'] ?? '') . ' ' . ($ni['tanggal'] ?? ''));
                        $txt = trim(($ni['isi'] ?? '') . ' ' . ($ni['waktu'] ?? ''));
                        if ($txt !== '') $nList[] = ($lbl !== '' ? "[{$lbl}] " : '') . $txt;
                    }
                    $remDisplay = !empty($nList) ? implode(' | ', $nList) : '-';
                } else {
                    $remDisplay = $rawRem;
                }
            }
            $sheet->setCellValue("Q{$row}", $remDisplay);

            // Alignment & Fonts
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            // Format Numbering
            $sheet->getStyle("D{$row}:F{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$row}:M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("N{$row}:O{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("P{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            // Unpaid > 0 merah tebal
            if ((float)($s['unpaid_jam'] ?? 0) > 0) {
                $sheet->getStyle("M{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
            }

            // Actual revenue tebal
            $sheet->getStyle("O{$row}")->getFont()->setBold(true);

            // Zebra shading
            if ($no % 2 == 0) {
                $sheet->getStyle("D{$row}:Q{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $endDataRow = $row - 1;
        $this->applyGridBorders($sheet, "B{$startRow}:Q{$endDataRow}");

        // ── Baris AVERAGE (Row Gold/Orange) ──────────────────────────
        $avgRow = $row;
        $sheet->setCellValue("B{$avgRow}", 'AVERAGE');
        $sheet->mergeCells("B{$avgRow}:C{$avgRow}");
        $sheet->getStyle("B{$avgRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("D{$avgRow}", "=AVERAGE(D{$startRow}:D{$endDataRow})");
        $sheet->setCellValue("E{$avgRow}", "=AVERAGE(E{$startRow}:E{$endDataRow})");
        $sheet->setCellValue("F{$avgRow}", "=AVERAGE(F{$startRow}:F{$endDataRow})");
        $sheet->getStyle("D{$avgRow}:F{$avgRow}")->getNumberFormat()->setFormatCode('0.00%');

        $sheet->setCellValue("G{$avgRow}", "=SUM(G{$startRow}:G{$endDataRow})");
        $sheet->setCellValue("H{$avgRow}", "=SUM(H{$startRow}:H{$endDataRow})");
        $sheet->setCellValue("I{$avgRow}", "=AVERAGE(I{$startRow}:I{$endDataRow})");
        $sheet->setCellValue("J{$avgRow}", "=AVERAGE(J{$startRow}:J{$endDataRow})");
        $sheet->setCellValue("K{$avgRow}", "=SUM(K{$startRow}:K{$endDataRow})");
        $sheet->setCellValue("L{$avgRow}", "=SUM(L{$startRow}:L{$endDataRow})");
        $sheet->setCellValue("M{$avgRow}", "=SUM(M{$startRow}:M{$endDataRow})");
        $sheet->setCellValue("N{$avgRow}", "=SUM(N{$startRow}:N{$endDataRow})");
        $sheet->setCellValue("O{$avgRow}", "=SUM(O{$startRow}:O{$endDataRow})");
        $sheet->setCellValue("P{$avgRow}", "=SUM(P{$startRow}:P{$endDataRow})");
        $sheet->setCellValue("Q{$avgRow}", '-');

        $sheet->getStyle("G{$avgRow}:J{$avgRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("K{$avgRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("L{$avgRow}:M{$avgRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("N{$avgRow}:O{$avgRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle("P{$avgRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Style Row Average
        $sheet->getStyle("B{$avgRow}:Q{$avgRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getStyle("D{$avgRow}:F{$avgRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_GOLD);
        $sheet->getStyle("M{$avgRow}")->getFont()->getColor()->setRGB(self::COLOR_RED);
        $sheet->getRowDimension($avgRow)->setRowHeight(24);

        $this->autoFitColumns($sheet, 'B', 'Q');
        $sheet->freezePane('D7');

        // ─────────────────────────────────────────────────────────────
        // SHEET 2: NPT ALL RIG
        // ─────────────────────────────────────────────────────────────
        $sheetNpt = $spreadsheet->createSheet();
        $sheetNpt->setTitle('NPT ALL RIG');
        $sheetNpt->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheetNpt,
            'DOWN TIME (NPT) ALL RIG BMS',
            "PERIODE: {$bulanName} {$tahun}",
            'AH'
        );

        $kategoriList  = $this->kategoriModel->getKategoriAktif();
        $summaryAllRig = $this->nptModel->getSummaryAllRig($bulan, $tahun);

        // Header NPT
        $sheetNpt->setCellValue('B5', 'NO');
        $sheetNpt->setCellValue('C5', 'NAME RIG');
        $sheetNpt->mergeCells('B5:B6');
        $sheetNpt->mergeCells('C5:C6');

        $colIdx = 4; // Col D
        foreach ($kategoriList as $kat) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheetNpt->setCellValue("{$colLetter}5", $kat['nama']);
            $sheetNpt->setCellValue("{$colLetter}6", strtoupper($kat['tipe']));
            $colIdx++;
        }

        $colTotal = Coordinate::stringFromColumnIndex($colIdx);
        $sheetNpt->setCellValue("{$colTotal}5", 'TOTAL');
        $sheetNpt->setCellValue("{$colTotal}6", 'HOURS');

        $endNptCol = $colTotal;
        $this->applyHeaderStyle($sheetNpt, "B5:{$endNptCol}6");
        $sheetNpt->getRowDimension(5)->setRowHeight(24);
        $sheetNpt->getRowDimension(6)->setRowHeight(20);

        $nptRowStart = 7;
        $nptRow = $nptRowStart;
        $nNo = 1;

        foreach ($summaries as $s) {
            $sheetNpt->setCellValue("B{$nptRow}", $nNo++);
            $sheetNpt->setCellValue("C{$nptRow}", $s['kode']);
            $sheetNpt->getStyle("B{$nptRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetNpt->getStyle("C{$nptRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetNpt->getStyle("C{$nptRow}")->getFont()->setBold(true);
            $sheetNpt->getStyle("B{$nptRow}:C{$nptRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $cIdx = 4;
            $firstDataCol = Coordinate::stringFromColumnIndex(4);
            $lastDataCol  = Coordinate::stringFromColumnIndex(4 + count($kategoriList) - 1);

            foreach ($kategoriList as $kat) {
                $cL  = Coordinate::stringFromColumnIndex($cIdx);
                $val = (float)($summaryAllRig[$s['rig_id']][$kat['id']] ?? 0);
                $sheetNpt->setCellValue("{$cL}{$nptRow}", $val > 0 ? $val : 0);
                $sheetNpt->getStyle("{$cL}{$nptRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                if ($kat['tipe'] === 'UNPAID' && $val > 0) {
                    $sheetNpt->getStyle("{$cL}{$nptRow}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
                }
                $cIdx++;
            }

            // Total Formula
            $tL = Coordinate::stringFromColumnIndex($cIdx);
            $sheetNpt->setCellValue("{$tL}{$nptRow}", "=SUM({$firstDataCol}{$nptRow}:{$lastDataCol}{$nptRow})");
            $sheetNpt->getStyle("{$tL}{$nptRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheetNpt->getStyle("{$tL}{$nptRow}")->getFont()->setBold(true);
            $sheetNpt->getStyle("{$tL}{$nptRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $sheetNpt->getRowDimension($nptRow)->setRowHeight(20);
            $nptRow++;
        }

        $nptRowEnd = $nptRow - 1;
        $this->applyGridBorders($sheetNpt, "B{$nptRowStart}:{$endNptCol}{$nptRowEnd}");

        // Footer Total NPT
        $totNptRow = $nptRow;
        $sheetNpt->setCellValue("B{$totNptRow}", 'TOTAL');
        $sheetNpt->mergeCells("B{$totNptRow}:C{$totNptRow}");
        $sheetNpt->getStyle("B{$totNptRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($c = 4; $c <= $colIdx; $c++) {
            $cL = Coordinate::stringFromColumnIndex($c);
            $sheetNpt->setCellValue("{$cL}{$totNptRow}", "=SUM({$cL}{$nptRowStart}:{$cL}{$nptRowEnd})");
            $sheetNpt->getStyle("{$cL}{$totNptRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheetNpt->getStyle("B{$totNptRow}:{$endNptCol}{$totNptRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheetNpt->getRowDimension($totNptRow)->setRowHeight(24);

        $this->autoFitColumns($sheetNpt, 'B', $endNptCol);
        $sheetNpt->freezePane('D7');

        // ─────────────────────────────────────────────────────────────
        // SHEET 3: TOTAL WELL JOB
        // ─────────────────────────────────────────────────────────────
        $sheetWell = $spreadsheet->createSheet();
        $sheetWell->setTitle('TOTAL WELL JOB');
        $sheetWell->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheetWell,
            'TOTAL WELL JOB SELURUH RIG BMS',
            "PERIODE: {$bulanName} {$tahun}",
            'E'
        );

        $sheetWell->setCellValue('B5', 'NO');
        $sheetWell->setCellValue('C5', 'NAME RIG');
        $sheetWell->setCellValue('D5', 'TOTAL WELL JOB');
        $this->applyHeaderStyle($sheetWell, 'B5:D5');
        $sheetWell->getRowDimension(5)->setRowHeight(24);

        $wRowStart = 6;
        $wRow = $wRowStart;
        $wNo = 1;
        foreach ($summaries as $s) {
            $sheetWell->setCellValue("B{$wRow}", $wNo++);
            $sheetWell->setCellValue("C{$wRow}", $s['kode']);
            $sheetWell->setCellValue("D{$wRow}", (int)($s['total_well_job'] ?? 0));

            $sheetWell->getStyle("B{$wRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetWell->getStyle("C{$wRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetWell->getStyle("C{$wRow}")->getFont()->setBold(true);
            $sheetWell->getStyle("D{$wRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheetWell->getStyle("D{$wRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetWell->getStyle("D{$wRow}")->getFont()->setBold(true);

            if ($wNo % 2 == 0) {
                $sheetWell->getStyle("B{$wRow}:D{$wRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }
            $sheetWell->getRowDimension($wRow)->setRowHeight(20);
            $wRow++;
        }
        $wRowEnd = $wRow - 1;
        $this->applyGridBorders($sheetWell, "B{$wRowStart}:D{$wRowEnd}");

        // Total Wells
        $sheetWell->setCellValue("B{$wRow}", 'TOTAL');
        $sheetWell->mergeCells("B{$wRow}:C{$wRow}");
        $sheetWell->getStyle("B{$wRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetWell->setCellValue("D{$wRow}", "=SUM(D{$wRowStart}:D{$wRowEnd})");
        $sheetWell->getStyle("D{$wRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheetWell->getStyle("D{$wRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheetWell->getStyle("B{$wRow}:D{$wRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheetWell->getRowDimension($wRow)->setRowHeight(24);
        $this->autoFitColumns($sheetWell, 'B', 'D');

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Monthly_Report_{$bulanName}_{$tahun}_SYS.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 2. EXPORT NPT HARIAN PER RIG
     * Persis Format Asli NPT SEPTEMBER 2026 SYS.xlsx
     * =========================================================================
     */
    public function npt(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->back()->with('error', 'Rig tidak ditemukan');
        }

        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($rig['kode']);
        $sheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheet,
            "LEMBAR DOWNTIME (NPT) HARIAN - {$rig['kode']} ({$rig['nama_rig']})",
            "PERIODE: {$bulanName} {$tahun}",
            'AJ'
        );

        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $daysInMonth  = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $existingData = $this->nptModel->getByRigBulanTahun($rigId, $bulan, $tahun);

        // Header Rows 5 & 6
        $sheet->setCellValue('B5', 'NO');
        $sheet->setCellValue('C5', 'TANGGAL');
        $sheet->setCellValue('D5', 'HARI');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        $sheet->mergeCells('D5:D6');

        $colIdx = 5; // Col E
        foreach ($kategoriList as $kat) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$colLetter}5", $kat['nama']);
            $sheet->setCellValue("{$colLetter}6", strtoupper($kat['tipe']));
            $colIdx++;
        }

        $colTotal = Coordinate::stringFromColumnIndex($colIdx);
        $sheet->setCellValue("{$colTotal}5", 'TOTAL (HRS)');
        $sheet->mergeCells("{$colTotal}5:{$colTotal}6");

        $colRemark = Coordinate::stringFromColumnIndex($colIdx + 1);
        $sheet->setCellValue("{$colRemark}5", 'REMARK');
        $sheet->mergeCells("{$colRemark}5:{$colRemark}6");

        $endCol = $colRemark;
        $this->applyHeaderStyle($sheet, "B5:{$endCol}6");
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->getRowDimension(6)->setRowHeight(20);

        $rowStart = 7;
        $row = $rowStart;

        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $tglStr    = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $timestamp = strtotime($tglStr);
            $namaHari  = $hariIndo[date('w', $timestamp)];

            $sheet->setCellValue("B{$row}", $d);
            $sheet->setCellValue("C{$row}", $tglStr);
            $sheet->setCellValue("D{$row}", $namaHari);

            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Weekend highlight
            $isWeekend = (date('w', $timestamp) == 0 || date('w', $timestamp) == 6);
            if ($isWeekend) {
                $sheet->getStyle("B{$row}:D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF2F2');
            }

            $cIdx         = 5;
            $firstDataCol = Coordinate::stringFromColumnIndex(5);
            $lastDataCol  = Coordinate::stringFromColumnIndex(5 + count($kategoriList) - 1);
            $remark       = '';

            foreach ($kategoriList as $kat) {
                $cL  = Coordinate::stringFromColumnIndex($cIdx);
                $val = (float)($existingData[$tglStr][$kat['id']][0]['jam'] ?? 0);
                if (empty($remark) && !empty($existingData[$tglStr][$kat['id']][0]['remark'])) {
                    $remark = $existingData[$tglStr][$kat['id']][0]['remark'];
                }

                $sheet->setCellValue("{$cL}{$row}", $val > 0 ? $val : 0);
                $sheet->getStyle("{$cL}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                if ($kat['tipe'] === 'UNPAID' && $val > 0) {
                    $sheet->getStyle("{$cL}{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
                }
                $cIdx++;
            }

            // Total Hours Formula
            $tL = Coordinate::stringFromColumnIndex($cIdx);
            $sheet->setCellValue("{$tL}{$row}", "=SUM({$firstDataCol}{$row}:{$lastDataCol}{$row})");
            $sheet->getStyle("{$tL}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$tL}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("{$tL}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            // Remark
            $rL = Coordinate::stringFromColumnIndex($cIdx + 1);
            $sheet->setCellValue("{$rL}{$row}", $remark);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $rowEnd = $row - 1;
        $this->applyGridBorders($sheet, "B{$rowStart}:{$endCol}{$rowEnd}");

        // Footer Total
        $totRow = $row;
        $sheet->setCellValue("B{$totRow}", 'TOTAL (HRS)');
        $sheet->mergeCells("B{$totRow}:D{$totRow}");
        $sheet->getStyle("B{$totRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($c = 5; $c <= $colIdx; $c++) {
            $cL = Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue("{$cL}{$totRow}", "=SUM({$cL}{$rowStart}:{$cL}{$rowEnd})");
            $sheet->getStyle("{$cL}{$totRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $rL = Coordinate::stringFromColumnIndex($colIdx + 1);
        $sheet->setCellValue("{$rL}{$totRow}", '-');

        $sheet->getStyle("B{$totRow}:{$endCol}{$totRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension($totRow)->setRowHeight(24);

        $this->autoFitColumns($sheet, 'B', $endCol);
        $sheet->freezePane('E7');

        $filename = "NPT_{$rig['kode']}_{$bulanName}_{$tahun}.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 3. EXPORT NPT ALL RIG BULANAN (STANDALONE)
     * =========================================================================
     */
    public function nptAll(int $bulan, int $tahun)
    {
        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('NPT ALL RIG');
        $sheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheet,
            'REKAPITULASI DOWNTIME (NPT) SELURUH ARMADA RIG BMS',
            "PERIODE: {$bulanName} {$tahun}",
            'AJ'
        );

        $summaries     = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);
        $kategoriList  = $this->kategoriModel->getKategoriAktif();
        $summaryAllRig = $this->nptModel->getSummaryAllRig($bulan, $tahun);

        $sheet->setCellValue('B5', 'NO');
        $sheet->setCellValue('C5', 'NAME RIG');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');

        $colIdx = 4;
        foreach ($kategoriList as $kat) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$colLetter}5", $kat['nama']);
            $sheet->setCellValue("{$colLetter}6", strtoupper($kat['tipe']));
            $colIdx++;
        }

        $colTotal = Coordinate::stringFromColumnIndex($colIdx);
        $sheet->setCellValue("{$colTotal}5", 'TOTAL');
        $sheet->setCellValue("{$colTotal}6", 'HOURS');

        $endCol = $colTotal;
        $this->applyHeaderStyle($sheet, "B5:{$endCol}6");
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->getRowDimension(6)->setRowHeight(20);

        $rowStart = 7;
        $row = $rowStart;
        $no = 1;

        foreach ($summaries as $s) {
            $sheet->setCellValue("B{$row}", $no++);
            $sheet->setCellValue("C{$row}", $s['kode']);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $cIdx = 4;
            $firstDataCol = Coordinate::stringFromColumnIndex(4);
            $lastDataCol  = Coordinate::stringFromColumnIndex(4 + count($kategoriList) - 1);

            foreach ($kategoriList as $kat) {
                $cL  = Coordinate::stringFromColumnIndex($cIdx);
                $val = (float)($summaryAllRig[$s['rig_id']][$kat['id']] ?? 0);
                $sheet->setCellValue("{$cL}{$row}", $val > 0 ? $val : 0);
                $sheet->getStyle("{$cL}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                if ($kat['tipe'] === 'UNPAID' && $val > 0) {
                    $sheet->getStyle("{$cL}{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
                }
                $cIdx++;
            }

            $tL = Coordinate::stringFromColumnIndex($cIdx);
            $sheet->setCellValue("{$tL}{$row}", "=SUM({$firstDataCol}{$row}:{$lastDataCol}{$row})");
            $sheet->getStyle("{$tL}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$tL}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("{$tL}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $rowEnd = $row - 1;
        $this->applyGridBorders($sheet, "B{$rowStart}:{$endCol}{$rowEnd}");

        // Footer Total
        $totRow = $row;
        $sheet->setCellValue("B{$totRow}", 'TOTAL');
        $sheet->mergeCells("B{$totRow}:C{$totRow}");
        $sheet->getStyle("B{$totRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($c = 4; $c <= $colIdx; $c++) {
            $cL = Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue("{$cL}{$totRow}", "=SUM({$cL}{$rowStart}:{$cL}{$rowEnd})");
            $sheet->getStyle("{$cL}{$totRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->getStyle("B{$totRow}:{$endCol}{$totRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension($totRow)->setRowHeight(24);

        $this->autoFitColumns($sheet, 'B', $endCol);
        $sheet->freezePane('D7');

        $filename = "NPT_ALL_RIG_{$bulanName}_{$tahun}.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Helper: Membangun Lembar Kerja Excel Persis Template Asli:
     * "SUMMARY REPORT PER WELL {RIG} {BULAN} {TAHUN}"
     * Lengkap dengan Baris Tarif Kontrak Rp (ODR, MIRU, OPS, SBWC), Rincian Tanggal per Sumur,
     * Merge Vertikal (No, Location, Distance, Job), dan Baris Subtotal Biru Muda (#D9E1F2) per Sumur.
     */
    private function populateSummaryReportPerWellSheet($sheet, array $rig, int $bulan, int $tahun): void
    {
        $rigId     = (int)$rig['id'];
        $rigKode   = strtoupper(trim($rig['kode'] ?? 'RIG'));
        $bulanName = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $odr       = (float)($rig['odr'] ?? 0);

        $rateOps  = $odr > 0 ? round($odr / 24) : 0;
        $rateMiru = $odr > 0 ? round(($odr / 24) * 0.75) : 0;
        $rateSbwc = $odr > 0 ? round(($odr / 24) * 0.65) : 0;

        $reports = $this->dailyReportModel->getByRigBulanTahun($rigId, $bulan, $tahun);
        $db      = \Config\Database::connect();

        // Ambil semua daily_report_log dikelompokkan per daily_report_id
        $rawLogs = $db->table('daily_report_log drl')
            ->select('drl.*')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('drl.tanggal', 'ASC')
            ->orderBy('drl.id', 'ASC')
            ->get()->getResultArray();

        $logsByWell = [];
        foreach ($rawLogs as $lg) {
            $logsByWell[(int)$lg['daily_report_id']][] = $lg;
        }

        // Ambil fallback daily_report_dt bila ada sumur tanpa daily_report_log
        $rawDts = $db->table('daily_report_dt drd')
            ->select('drd.*')
            ->join('daily_report dr', 'dr.id = drd.daily_report_id')
            ->where('dr.rig_id', $rigId)
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->get()->getResultArray();

        $dtByWell = [];
        foreach ($rawDts as $dRow) {
            $wId = (int)$dRow['daily_report_id'];
            $kId = (int)$dRow['kategori_id'];
            $dtByWell[$wId][$kId] = ($dtByWell[$wId][$kId] ?? 0.0) + (float)$dRow['jam'];
        }

        $sheet->setShowGridLines(true);

        // ── BARIS 1: JUDUL UTAMA ──
        $sheet->mergeCells('B1:V1');
        $sheet->setCellValue('B1', "SUMMARY REPORT PER WELL {$rigKode} {$bulanName} {$tahun}");
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // ── BARIS 2 & 3: HEADER TABEL PERSIS TEMPLATE EXCEL ──
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Location');
        $sheet->setCellValue('C2', 'Date');
        $sheet->setCellValue('D2', 'DISTANCE');
        $sheet->setCellValue('E2', 'MIRU');
        $sheet->setCellValue('F2', 'OPS');
        $sheet->setCellValue('G2', 'SBWC');
        $sheet->setCellValue('Q2', 'UNPAID');
        $sheet->setCellValue('S2', "SHUT\nDOWN");
        $sheet->setCellValue('T2', "TOTAL\nDT");
        $sheet->setCellValue('U2', "TOTAL\nHRS");
        $sheet->setCellValue('V2', 'REMARK NPT');
        $sheet->setCellValue('W2', 'JOB');

        // Sub-header SBWC & UNPAID di Baris 3
        $sheet->setCellValue('G3', "Rain\n(U.C)");
        $sheet->setCellValue('H3', 'DRY ROAD');
        $sheet->setCellValue('I3', "DRY\nWELL\nPAD");
        $sheet->setCellValue('J3', "PHR\nOperator");
        $sheet->setCellValue('K3', "Trans\nSharing");
        $sheet->setCellValue('L3', 'CE/PE');
        $sheet->setCellValue('M3', '3 Party');
        $sheet->setCellValue('N3', "WO\nDAYLIGHT");
        $sheet->setCellValue('O3', "PHR Well &\nAccessories");
        $sheet->setCellValue('P3', "W.O\nFOAM\nUNIT");
        $sheet->setCellValue('Q3', 'RIG');
        $sheet->setCellValue('R3', "BMS\nTOOL");

        // Merge Header
        $sheet->mergeCells('A2:A3');
        $sheet->mergeCells('B2:B3');
        $sheet->mergeCells('C2:C3');
        $sheet->mergeCells('D2:D3');
        $sheet->mergeCells('E2:E3');
        $sheet->mergeCells('F2:F3');
        $sheet->mergeCells('G2:P2');
        $sheet->mergeCells('Q2:R2');
        $sheet->mergeCells('S2:S3');
        $sheet->mergeCells('T2:T3');
        $sheet->mergeCells('U2:U3');
        $sheet->mergeCells('V2:V3');
        $sheet->mergeCells('W2:W3');

        $sheet->getStyle('A2:W3')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Warna Header Persis Gambar Template
        $fillCell = static function($sheet, string $range, string $rgb) {
            $sheet->getStyle($range)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($rgb);
        };
        $fillCell($sheet, 'A2:D3', '92CDDC'); // Sky Blue (No, Location, Date, Distance)
        $fillCell($sheet, 'E2:F3', '92D050'); // Bright Green (MIRU, OPS)
        $fillCell($sheet, 'G2:P3', 'FFFF00'); // Yellow (SBWC)
        $fillCell($sheet, 'Q2:R3', 'FFC000'); // Gold/Orange (UNPAID)
        $fillCell($sheet, 'S2:S3', 'FF0000'); // Red (SHUT DOWN)
        $fillCell($sheet, 'T2:T3', 'F4B084'); // Peach (TOTAL DT)
        $fillCell($sheet, 'U2:U3', 'A9D08E'); // Soft Green (TOTAL HRS)
        $fillCell($sheet, 'V2:V3', '92CDDC'); // Sky Blue (REMARK NPT)
        $fillCell($sheet, 'W2:W3', 'E6B8B7'); // Rose Pink (JOB)

        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(32);

        // ── BARIS 4: BARIS TARIF KONTRAK (RP MERAH) ──
        $sheet->setCellValue('C4', $odr);
        $sheet->setCellValue('E4', $rateMiru);
        $sheet->setCellValue('F4', $rateOps);
        $sheet->mergeCells('G4:P4');
        $sheet->setCellValue('G4', $rateSbwc);
        $sheet->mergeCells('Q4:R4');
        $sheet->setCellValue('Q4', 'Rp      -');
        $sheet->setCellValue('S4', 'Rp   -');

        $sheet->getStyle('A4:W4')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => ['rgb' => 'FF0000']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getStyle('C4')->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('E4:G4')->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('Q4:S4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(18);

        // ── BARIS 5..N: DATA PER SUMUR (DENGAN SUBTOTAL PER SUMUR) ──
        $row = 5;
        $subtotalRows = [];

        foreach ($reports as $rep) {
            $repId    = (int)$rep['id'];
            $wellLogs = $logsByWell[$repId] ?? [];
            $dt       = $dtByWell[$repId] ?? [];

            if (empty($wellLogs)) {
                $fallbackDt  = (float)($rep['total_dt'] ?? 0);
                $fallbackHrs = (float)($rep['total_jam'] ?? ((float)$rep['miru_jam'] + (float)$rep['ops_jam'] + $fallbackDt));
                $wellLogs = [[
                    'tanggal'       => $rep['tanggal_mulai'] ?: sprintf('%04d-%02d-01', $tahun, $bulan),
                    'miru_jam'      => (float)($rep['miru_jam'] ?? 0),
                    'ops_jam'       => (float)($rep['ops_jam'] ?? 0),
                    'dt_rain'       => (float)($dt[3] ?? 0),
                    'dt_dry_road'   => (float)($dt[4] ?? 0),
                    'dt_dry_pad'    => (float)($dt[5] ?? 0),
                    'dt_phr_op'     => (float)($dt[13] ?? 0),
                    'dt_trans'      => (float)($dt[11] ?? 0),
                    'dt_ce_pe'      => (float)($dt[14] ?? 0),
                    'dt_3rd_party'  => (float)($dt[10] ?? 0),
                    'dt_daylight'   => (float)($dt[6] ?? 0),
                    'dt_phr_well'   => (float)($dt[8] ?? 0),
                    'dt_foam'       => (float)($dt[12] ?? 0),
                    'dt_rig'        => (float)($dt[1] ?? 0),
                    'dt_tool'       => (float)($dt[2] ?? 0),
                    'dt_shutdown'   => (float)($dt[15] ?? 0),
                    'total_dt'      => $fallbackDt,
                    'total_hrs'     => $fallbackHrs,
                    'remark_npt'    => $rep['remark'] ?? '',
                    'remark_unpaid' => '',
                ]];
            }

            $wStartRow = $row;
            foreach ($wellLogs as $dRow) {
                $dateFormatted = !empty($dRow['tanggal']) ? date('d-M-y', strtotime($dRow['tanggal'])) : '-';
                $sheet->setCellValue("C{$row}", $dateFormatted);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $colValues = [
                    'E' => (float)($dRow['miru_jam'] ?? 0),
                    'F' => (float)($dRow['ops_jam'] ?? 0),
                    'G' => (float)($dRow['dt_rain'] ?? 0),
                    'H' => (float)($dRow['dt_dry_road'] ?? 0),
                    'I' => (float)($dRow['dt_dry_pad'] ?? 0),
                    'J' => (float)($dRow['dt_phr_op'] ?? 0),
                    'K' => (float)($dRow['dt_trans'] ?? 0),
                    'L' => (float)($dRow['dt_ce_pe'] ?? 0),
                    'M' => (float)($dRow['dt_3rd_party'] ?? 0),
                    'N' => (float)($dRow['dt_daylight'] ?? 0),
                    'O' => (float)($dRow['dt_phr_well'] ?? 0),
                    'P' => (float)($dRow['dt_foam'] ?? 0),
                    'Q' => (float)($dRow['dt_rig'] ?? 0),
                    'R' => (float)($dRow['dt_tool'] ?? 0),
                    'S' => (float)($dRow['dt_shutdown'] ?? 0),
                ];

                foreach ($colValues as $colL => $val) {
                    if ($val > 0) {
                        $sheet->setCellValue("{$colL}{$row}", $val);
                        $sheet->getStyle("{$colL}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                }

                // Warna Merah untuk Unpaid Rig / Tool > 0
                if ($colValues['Q'] > 0) {
                    $sheet->getStyle("Q{$row}")->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                }
                if ($colValues['R'] > 0) {
                    $sheet->getStyle("R{$row}")->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                }

                // TOTAL DT (Col T) & TOTAL HRS (Col U)
                $sheet->setCellValue("T{$row}", "=SUM(G{$row}:S{$row})");
                $sheet->setCellValue("U{$row}", "=E{$row}+F{$row}+T{$row}");
                $sheet->getStyle("T{$row}:U{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $fillCell($sheet, "T{$row}", 'F4B084');

                // REMARK NPT (Col V)
                $remParts = [];
                if (!empty($dRow['remark_npt']))    $remParts[] = trim($dRow['remark_npt']);
                if (!empty($dRow['remark_unpaid'])) $remParts[] = trim($dRow['remark_unpaid']);
                $remText = implode(' | ', array_unique($remParts));
                if ($remText !== '') {
                    $sheet->setCellValue("V{$row}", $remText);
                    if ($colValues['Q'] > 0 || $colValues['R'] > 0 || !empty($dRow['remark_unpaid'])) {
                        $sheet->getStyle("V{$row}")->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                    }
                }

                $sheet->getRowDimension($row)->setRowHeight(17);
                $row++;
            }
            $wEndRow = $row - 1;

            // Merge No (A), Location (B), DISTANCE (D), JOB (W) untuk Sumur Ini
            $sheet->mergeCells("A{$wStartRow}:A{$wEndRow}");
            $sheet->mergeCells("B{$wStartRow}:B{$wEndRow}");
            $sheet->mergeCells("D{$wStartRow}:D{$wEndRow}");
            $sheet->mergeCells("W{$wStartRow}:W{$wEndRow}");

            $sheet->setCellValue("A{$wStartRow}", (int)$rep['no_well']);
            $sheet->setCellValue("B{$wStartRow}", strtoupper(trim($rep['nama_lokasi'] ?? ('WELL #' . $rep['no_well']))));

            $jarakVal = (float)($rep['jarak'] ?? 0);
            if ($jarakVal > 0) {
                $jarakFormatted = rtrim(rtrim(number_format($jarakVal, 2, ',', '.'), '0'), ',') . ' KM';
                $sheet->setCellValue("D{$wStartRow}", $jarakFormatted);
            }
            $fillCell($sheet, "D{$wStartRow}:D{$wEndRow}", 'DDD9C4');

            $jobStatus = strtoupper(trim($rep['status_job'] ?: 'JOB PROGRESS'));
            $sheet->setCellValue("W{$wStartRow}", str_replace(' ', "\n", $jobStatus));
            $fillCell($sheet, "W{$wStartRow}:W{$wEndRow}", 'E6B8B7');

            $sheet->getStyle("A{$wStartRow}:B{$wEndRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("D{$wStartRow}:D{$wEndRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("W{$wStartRow}:W{$wEndRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            // ── BARIS SUBTOTAL PER SUMUR (BIRU MUDA #D9E1F2) ──
            $subRow = $row;
            $subtotalRows[] = $subRow;
            $sumCols = ['E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U'];
            foreach ($sumCols as $sCol) {
                $sheet->setCellValue("{$sCol}{$subRow}", "=SUM({$sCol}{$wStartRow}:{$sCol}{$wEndRow})");
                $sheet->getStyle("{$sCol}{$subRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $fillCell($sheet, "A{$subRow}:W{$subRow}", 'D9E1F2');
            $fillCell($sheet, "T{$subRow}", 'F4B084');
            $sheet->getStyle("A{$subRow}:W{$subRow}")->getFont()->setBold(true);
            $sheet->getRowDimension($subRow)->setRowHeight(18);
            $row++;
        }

        $lastWellRow = max(4, $row - 1);
        $sheet->getStyle("A2:W{$lastWellRow}")->applyFromArray([
            'font' => ['name' => 'Calibri'],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // ── BARIS FOOTER & KPI SUMMARY BLOCK (PERSIS FOTO 1) + REPORT NOTES (PERSIS FOTO 2) ──
        if (!empty($subtotalRows)) {
            $thinBorder = [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ];

            // Ambil metadata Schedule MTC & Notes dari monthly_summary.remark
            $summaryRowDb = $this->db->table('monthly_summary')
                ->where('rig_id', $rigId)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()->getRowArray();

            $scheduleMtc = 0.0;
            $reportNotes = [];
            if ($summaryRowDb && !empty($summaryRowDb['remark'])) {
                $decoded = json_decode((string)$summaryRowDb['remark'], true);
                if (is_array($decoded) && (isset($decoded['notes']) || isset($decoded['schedule_mtc']))) {
                    $scheduleMtc = (float)($decoded['schedule_mtc'] ?? 0.0);
                    $reportNotes = is_array($decoded['notes'] ?? null) ? array_values($decoded['notes']) : [];
                } else {
                    $plain = trim((string)$summaryRowDb['remark']);
                    if ($plain !== '') {
                        $reportNotes[] = [
                            'lokasi'  => 'CATATAN LAPORAN',
                            'tanggal' => sprintf('%02d/%04d', $bulan, $tahun),
                            'isi'     => $plain,
                            'waktu'   => '',
                        ];
                    }
                }
            }

            $miruWellCnt = 0;
            $compWellCnt = 0;
            foreach ($reports as $rItem) {
                if ((float)($rItem['miru_jam'] ?? 0) > 0) $miruWellCnt++;
                if (strtoupper(trim((string)($rItem['status_job'] ?? ''))) === 'JOB COMPLETED') $compWellCnt++;
            }
            $miruDivisor = max(1, $miruWellCnt);
            $compDivisor = max(1, $compWellCnt);

            // 1. BARIS TOTAL & SUBTOTAL KELOMPOK DT
            $grandRow = $row;
            $groupRow = $grandRow + 1;
            $roundRow = $grandRow + 2;

            $sheet->mergeCells("A{$grandRow}:C{$grandRow}");
            $sheet->setCellValue("A{$grandRow}", 'TOTAL');
            $sheet->getStyle("A{$grandRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sumCols = ['E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U'];
            foreach ($sumCols as $sCol) {
                $parts = array_map(static fn($r) => "{$sCol}{$r}", $subtotalRows);
                $sheet->setCellValue("{$sCol}{$grandRow}", '=' . implode('+', $parts));
                $sheet->getStyle("{$sCol}{$grandRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            }

            $fillCell($sheet, "A{$grandRow}:D{$grandRow}", '92CDDC');
            $fillCell($sheet, "E{$grandRow}:S{$grandRow}", '92D050');
            $fillCell($sheet, "T{$grandRow}:T{$groupRow}", 'FFC000');
            $fillCell($sheet, "U{$grandRow}:U{$groupRow}", 'A9D08E');

            $sheet->mergeCells("T{$grandRow}:T{$groupRow}");
            $sheet->mergeCells("U{$grandRow}:U{$groupRow}");
            $sheet->getStyle("T{$grandRow}:U{$groupRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$grandRow}:U{$grandRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$grandRow}:U{$grandRow}")->applyFromArray($thinBorder);

            // 2. BARIS KELOMPOK DT (Rain+Road | Other SBWC | Unpaid | Shutdown)
            $sheet->mergeCells("G{$groupRow}:H{$groupRow}");
            $sheet->setCellValue("G{$groupRow}", "=G{$grandRow}+H{$grandRow}");
            $fillCell($sheet, "G{$groupRow}:H{$groupRow}", 'FFFF00');

            $sheet->mergeCells("I{$groupRow}:P{$groupRow}");
            $sheet->setCellValue("I{$groupRow}", "=SUM(I{$grandRow}:P{$grandRow})");
            $fillCell($sheet, "I{$groupRow}:P{$groupRow}", 'FFFF00');

            $sheet->mergeCells("Q{$groupRow}:R{$groupRow}");
            $sheet->setCellValue("Q{$groupRow}", "=Q{$grandRow}+R{$grandRow}");
            $fillCell($sheet, "Q{$groupRow}:R{$groupRow}", 'FFC000');

            $sheet->setCellValue("S{$groupRow}", "=S{$grandRow}");
            $fillCell($sheet, "S{$groupRow}", 'FF0000');

            $sheet->getStyle("G{$groupRow}:S{$groupRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("G{$groupRow}:U{$groupRow}")->getFont()->setBold(true);
            $sheet->getStyle("G{$groupRow}:S{$groupRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$groupRow}:U{$groupRow}")->applyFromArray($thinBorder);

            // 3. BARIS ANGKA 1 DESIMAL (Round Row)
            $sheet->mergeCells("G{$roundRow}:H{$roundRow}");
            $sheet->setCellValue("G{$roundRow}", "=G{$groupRow}");
            $sheet->getStyle("G{$roundRow}")->getNumberFormat()->setFormatCode('#,##0.0');

            $sheet->mergeCells("I{$roundRow}:P{$roundRow}");
            $sheet->setCellValue("I{$roundRow}", "=I{$groupRow}");
            $sheet->getStyle("I{$roundRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            $sheet->mergeCells("Q{$roundRow}:R{$roundRow}");
            $sheet->setCellValue("Q{$roundRow}", "=Q{$groupRow}");
            $sheet->getStyle("Q{$roundRow}")->getNumberFormat()->setFormatCode('#,##0.0');

            $sheet->setCellValue("U{$roundRow}", "=U{$grandRow}");
            $sheet->getStyle("U{$roundRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("G{$roundRow}:U{$roundRow}")->getFont()->setBold(true);
            $sheet->getStyle("G{$roundRow}:R{$roundRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 4. BARIS PRESENTASE & REVENUE
            $pctRow = $roundRow + 2;
            $revRow = $pctRow + 1;

            $sheet->mergeCells("A{$pctRow}:C{$pctRow}");
            $sheet->setCellValue("A{$pctRow}", 'PRESENTASE');
            $sheet->setCellValue("E{$pctRow}", "=IF(U{$grandRow}>0,E{$grandRow}/U{$grandRow},0)");
            $sheet->setCellValue("F{$pctRow}", "=IF(U{$grandRow}>0,F{$grandRow}/U{$grandRow},0)");
            $sheet->mergeCells("G{$pctRow}:H{$pctRow}");
            $sheet->setCellValue("G{$pctRow}", "=IF(U{$grandRow}>0,G{$groupRow}/U{$grandRow},0)");
            $sheet->mergeCells("I{$pctRow}:P{$pctRow}");
            $sheet->setCellValue("I{$pctRow}", "=IF(U{$grandRow}>0,I{$groupRow}/U{$grandRow},0)");
            $sheet->mergeCells("Q{$pctRow}:R{$pctRow}");
            $sheet->setCellValue("Q{$pctRow}", "=IF(U{$grandRow}>0,Q{$groupRow}/U{$grandRow},0)");
            $sheet->setCellValue("S{$pctRow}", "=IF(U{$grandRow}>0,S{$groupRow}/U{$grandRow},0)");
            $sheet->mergeCells("T{$pctRow}:U{$pctRow}");
            $sheet->setCellValue("T{$pctRow}", "=E{$pctRow}+F{$pctRow}+G{$pctRow}+I{$pctRow}+Q{$pctRow}+S{$pctRow}");

            $fillCell($sheet, "A{$pctRow}:U{$pctRow}", 'FFFF00');
            $sheet->getStyle("E{$pctRow}:U{$pctRow}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("G{$pctRow}:S{$pctRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$pctRow}:U{$pctRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$pctRow}:U{$pctRow}")->applyFromArray($thinBorder);

            // REVENUE ROW (#F8CBAD)
            $rpDashFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
            $sheet->mergeCells("A{$revRow}:C{$revRow}");
            $sheet->setCellValue("A{$revRow}", 'REVENUE');
            $sheet->mergeCells("D{$revRow}:E{$revRow}");
            $sheet->setCellValue("D{$revRow}", "=E{$grandRow}*E4");
            $sheet->setCellValue("F{$revRow}", "=F{$grandRow}*F4");
            $sheet->mergeCells("G{$revRow}:H{$revRow}");
            $sheet->setCellValue("G{$revRow}", "=G{$groupRow}*G4");
            $sheet->mergeCells("I{$revRow}:P{$revRow}");
            $sheet->setCellValue("I{$revRow}", "=I{$groupRow}*G4");
            $sheet->mergeCells("Q{$revRow}:R{$revRow}");
            $sheet->setCellValue("Q{$revRow}", 0);
            $sheet->setCellValue("S{$revRow}", 0);
            $sheet->mergeCells("T{$revRow}:U{$revRow}");
            $sheet->setCellValue("T{$revRow}", "=D{$revRow}+F{$revRow}+G{$revRow}+I{$revRow}");

            $fillCell($sheet, "A{$revRow}:U{$revRow}", 'F8CBAD');
            $sheet->getStyle("D{$revRow}:U{$revRow}")->getNumberFormat()->setFormatCode($rpDashFmt);
            $sheet->getStyle("A{$revRow}:C{$revRow}")->getFont()->setBold(true);
            $sheet->getStyle("T{$revRow}:U{$revRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$revRow}:U{$revRow}")->applyFromArray($thinBorder);

            // 5. KPI BLOCK (REALIBILITY, AVAILIBILITY, UTILITITAION, AVERANGE MIRU, CYCLE TIME)
            $relRow   = $revRow + 1;
            $availRow = $revRow + 2;
            $utilRow  = $revRow + 3;
            $avgMRow  = $revRow + 4;
            $cycRow   = $revRow + 5;

            // REALIBILITY
            $sheet->mergeCells("A{$relRow}:C{$relRow}");
            $sheet->setCellValue("A{$relRow}", 'REALIBILITY');
            $sheet->setCellValue("D{$relRow}", "=IF(U{$grandRow}>0,(U{$grandRow}-Q{$groupRow})/U{$grandRow},0)");
            $sheet->getStyle("D{$relRow}")->getNumberFormat()->setFormatCode('0.00%');
            $fillCell($sheet, "A{$relRow}:D{$relRow}", 'B1A0C7');
            $sheet->getStyle("D{$relRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$relRow}:D{$relRow}")->applyFromArray($thinBorder);

            $sheet->mergeCells("T{$relRow}:U{$relRow}");
            $sheet->setCellValue("T{$relRow}", "=T{$revRow}");
            $sheet->getStyle("T{$relRow}:U{$relRow}")->getNumberFormat()->setFormatCode($rpDashFmt);

            // AVAILIBILITY + SCHEDULE MTC
            $sheet->mergeCells("A{$availRow}:C{$availRow}");
            $sheet->setCellValue("A{$availRow}", 'AVAILIBILITY');
            $sheet->setCellValue("D{$availRow}", "=IF(U{$grandRow}>0,(U{$grandRow}-Q{$groupRow}-F{$availRow})/U{$grandRow},0)");
            $sheet->getStyle("D{$availRow}")->getNumberFormat()->setFormatCode('0.00%');
            $fillCell($sheet, "A{$availRow}:D{$availRow}", 'B1A0C7');
            $sheet->getStyle("D{$availRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$availRow}:D{$availRow}")->applyFromArray($thinBorder);

            $sheet->setCellValue("E{$availRow}", 'SCHEDULE MTC');
            $sheet->setCellValue("F{$availRow}", $scheduleMtc);
            $sheet->getStyle("E{$availRow}:F{$availRow}")->getFont()->setBold(true);
            $sheet->getStyle("E{$availRow}:F{$availRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$availRow}:F{$availRow}")->applyFromArray($thinBorder);

            // UTILITITAION + INCENTIVE TARGET
            $sheet->mergeCells("A{$utilRow}:C{$utilRow}");
            $sheet->setCellValue("A{$utilRow}", 'UTILITITAION');
            $sheet->setCellValue("D{$utilRow}", "=IF(U{$grandRow}>0,(E{$grandRow}+F{$grandRow})/U{$grandRow},0)");
            $sheet->getStyle("D{$utilRow}")->getNumberFormat()->setFormatCode('0.00%');
            $fillCell($sheet, "A{$utilRow}:D{$utilRow}", 'B1A0C7');
            $sheet->getStyle("D{$utilRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$utilRow}:D{$utilRow}")->applyFromArray($thinBorder);

            $sheet->mergeCells("Q{$utilRow}:S{$utilRow}");
            $sheet->setCellValue("Q{$utilRow}", 'INCENTIVE TARGET');
            $sheet->mergeCells("T{$utilRow}:U{$utilRow}");
            $sheet->setCellValue("T{$utilRow}", "=C4*(U{$grandRow}/24)*0.92");
            $sheet->getStyle("T{$utilRow}:U{$utilRow}")->getNumberFormat()->setFormatCode($rpDashFmt);
            $fillCell($sheet, "Q{$utilRow}:U{$utilRow}", 'FFC000');
            $sheet->getStyle("Q{$utilRow}:U{$utilRow}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("Q{$utilRow}:S{$utilRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("Q{$utilRow}:U{$utilRow}")->applyFromArray($thinBorder);

            // AVERANGE MIRU
            $sheet->mergeCells("A{$avgMRow}:C{$avgMRow}");
            $sheet->setCellValue("A{$avgMRow}", 'AVERANGE MIRU');
            $sheet->setCellValue("D{$avgMRow}", "=E{$grandRow}/{$miruDivisor}");
            $sheet->getStyle("D{$avgMRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $fillCell($sheet, "A{$avgMRow}:D{$avgMRow}", '92D050');
            $sheet->getStyle("D{$avgMRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$avgMRow}:D{$avgMRow}")->applyFromArray($thinBorder);

            // CYCLE TIME
            $sheet->mergeCells("A{$cycRow}:C{$cycRow}");
            $sheet->setCellValue("A{$cycRow}", 'CYCLE TIME');
            $sheet->setCellValue("D{$cycRow}", "=U{$grandRow}/{$compDivisor}");
            $sheet->getStyle("D{$cycRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $fillCell($sheet, "A{$cycRow}:D{$cycRow}", 'FFFF00');
            $sheet->getStyle("D{$cycRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$cycRow}:D{$cycRow}")->applyFromArray($thinBorder);

            // 6. CATATAN / NOTE LAPORAN DI BAWAH TABEL (PERSIS FOTO 2)
            $noteRow = $cycRow + 2;
            foreach ($reportNotes as $nItem) {
                $lokText   = trim((string)($nItem['lokasi'] ?? ''));
                $tglText   = trim((string)($nItem['tanggal'] ?? ''));
                $isiText   = trim((string)($nItem['isi'] ?? ''));
                $waktuText = trim((string)($nItem['waktu'] ?? ''));

                if ($lokText !== '' || $tglText !== '') {
                    $sheet->setCellValue("B{$noteRow}", $lokText);
                    $sheet->setCellValue("E{$noteRow}", $tglText);
                    $sheet->getStyle("B{$noteRow}:E{$noteRow}")->getFont()->setName('Calibri')->setSize(11)->setBold(true);
                    $noteRow++;
                }
                if ($isiText !== '') {
                    $lines = preg_split('/\r\n|\r|\n/', $isiText);
                    foreach ($lines as $lineStr) {
                        $sheet->setCellValue("B{$noteRow}", $lineStr);
                        $sheet->getStyle("B{$noteRow}")->getFont()->setName('Calibri')->setSize(11)->setBold(true);
                        $noteRow++;
                    }
                }
                if ($waktuText !== '') {
                    $sheet->setCellValue("B{$noteRow}", $waktuText);
                    $sheet->getStyle("B{$noteRow}")->getFont()->setName('Calibri')->setSize(11)->setBold(true);
                    $noteRow++;
                }
                $noteRow++;
            }
        }

        // Set Column Widths agar proporsional seperti Template Asli
        $colWidths = [
            'A' => 5,  'B' => 16, 'C' => 12, 'D' => 11,
            'E' => 12, 'F' => 12,
            'G' => 8,  'H' => 10, 'I' => 9,  'J' => 10, 'K' => 9,
            'L' => 8,  'M' => 8,  'N' => 10, 'O' => 12, 'P' => 9,
            'Q' => 8,  'R' => 8,  'S' => 9,
            'T' => 10, 'U' => 11, 'V' => 36, 'W' => 14,
        ];
        foreach ($colWidths as $cL => $w) {
            $sheet->getColumnDimension($cL)->setWidth($w);
        }
        $sheet->freezePane('E5');
    }

    /**
     * =========================================================================
     * 4. EXPORT DAILY REPORT PER RIG (TEMPLATE ASLI SUMMARY REPORT PER WELL)
     * =========================================================================
     */
    public function dailyReport(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->back()->with('error', 'Rig tidak ditemukan');
        }

        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $spreadsheet = new Spreadsheet();

        // SHEET 1: SUMMARY REPORT PER WELL (PERSIS TEMPLATE EXCEL ASLI)
        $sheetPerWell = $spreadsheet->getActiveSheet();
        $sheetTitle = substr(preg_replace('/[\\\\\/\?\*\[\]]/', '', $rig['kode']), 0, 30);
        $sheetPerWell->setTitle($sheetTitle ?: 'SUMMARY PER WELL');
        $this->populateSummaryReportPerWellSheet($sheetPerWell, $rig, $bulan, $tahun);

        $spreadsheet->setActiveSheetIndex(0);
        $filename = "SUMMARY_REPORT_PER_WELL_{$rig['kode']}_{$bulanName}_{$tahun}.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 4B. EXPORT DAILY REPORT SELURUH RIG (MULTI-SHEET / ALL RIGS)
     * Menghasilkan 1 Workbook Excel dengan:
     * Sheet 1: REKAP ALL RIGS (Ringkasan Pekerjaan Sumur Seluruh Rig)
     * Sheet 2: DETAIL LOG ALL RIGS (Rincian Hari per Hari Lengkap Tanggal & Tempat)
     * Sheet 3..N: Lembar Rincian Tersendiri untuk Setiap Armada Rig Aktif
     * =========================================================================
     */
    public function dailyReportAll(int $bulan, int $tahun)
    {
        $rigs = $this->rigModel->getRigAktif();
        if (empty($rigs)) {
            return redirect()->back()->with('error', 'Tidak ada armada rig aktif.');
        }

        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $spreadsheet = new Spreadsheet();
        $db          = \Config\Database::connect();

        // ─────────────────────────────────────────────────────────────
        // 1. Sheet Ringkasan Semua Sumur Lintas Armada
        // ─────────────────────────────────────────────────────────────
        $rekapSheet = $spreadsheet->getActiveSheet();
        $rekapSheet->setTitle('REKAP ALL RIGS');
        $rekapSheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $rekapSheet,
            "REKAPITULASI PEKERJAAN SUMUR SELURUH RIG BMS",
            "PERIODE OPERASIONAL: {$bulanName} {$tahun}",
            'M'
        );

        $headers = [
            'NO'                     => Alignment::HORIZONTAL_CENTER,
            'ARMADA RIG'             => Alignment::HORIZONTAL_CENTER,
            'SUMUR / TEMPAT LOKASI'  => Alignment::HORIZONTAL_LEFT,
            'TGL MULAI'              => Alignment::HORIZONTAL_CENTER,
            'TGL SELESAI'            => Alignment::HORIZONTAL_CENTER,
            'DISTANCE (KM)'          => Alignment::HORIZONTAL_RIGHT,
            'MIRU (JAM)'             => Alignment::HORIZONTAL_RIGHT,
            'OPS (JAM)'              => Alignment::HORIZONTAL_RIGHT,
            'DOWNTIME (JAM)'         => Alignment::HORIZONTAL_RIGHT,
            'TOTAL JAM'              => Alignment::HORIZONTAL_RIGHT,
            'STATUS JOB'             => Alignment::HORIZONTAL_CENTER,
            'REMARK'                 => Alignment::HORIZONTAL_LEFT,
        ];

        $colIdx = 2; // Col B
        foreach ($headers as $h => $align) {
            $cL = Coordinate::stringFromColumnIndex($colIdx);
            $rekapSheet->setCellValue("{$cL}5", $h);
            $colIdx++;
        }
        $endCol = Coordinate::stringFromColumnIndex($colIdx - 1);
        $this->applyHeaderStyle($rekapSheet, "B5:{$endCol}5");
        $rekapSheet->getRowDimension(5)->setRowHeight(26);

        $rowStart = 6;
        $row = $rowStart;
        $no = 1;

        foreach ($rigs as $rig) {
            $reports = $this->dailyReportModel->getByRigBulanTahun($rig['id'], $bulan, $tahun);
            if (empty($reports)) continue;

            foreach ($reports as $rep) {
                $rekapSheet->setCellValue("B{$row}", $no++);
                $rekapSheet->setCellValue("C{$row}", $rig['kode']);
                $rekapSheet->setCellValue("D{$row}", $rep['nama_lokasi'] ?? '-');
                $rekapSheet->setCellValue("E{$row}", $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-');
                $rekapSheet->setCellValue("F{$row}", $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '-');
                $rekapSheet->setCellValue("G{$row}", (float)($rep['jarak'] ?? 0));
                $rekapSheet->setCellValue("H{$row}", (float)($rep['miru_jam'] ?? 0));
                $rekapSheet->setCellValue("I{$row}", (float)($rep['ops_jam'] ?? 0));
                $rekapSheet->setCellValue("J{$row}", (float)($rep['total_dt'] ?? 0));
                $rekapSheet->setCellValue("K{$row}", (float)($rep['total_jam'] ?? 0));
                $rekapSheet->setCellValue("L{$row}", $rep['status_job'] ?? '-');
                $rekapSheet->setCellValue("M{$row}", $rep['remark'] ?? '-');

                $rekapSheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rekapSheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rekapSheet->getStyle("C{$row}")->getFont()->setBold(true);
                $rekapSheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
                $rekapSheet->getStyle("D{$row}")->getFont()->setBold(true);
                $rekapSheet->getStyle("E{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rekapSheet->getStyle("G{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $rekapSheet->getStyle("L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rekapSheet->getStyle("L{$row}")->getFont()->setBold(true);

                $status = strtoupper(trim((string)($rep['status_job'] ?? '')));
                if ($status === 'COMPLETED' || $status === 'SELESAI' || $status === 'JOB COMPLETED') {
                    $rekapSheet->getStyle("L{$row}")->getFont()->getColor()->setRGB('15803D');
                } elseif ($status === 'RUNNING' || $status === 'ON GOING' || $status === 'JOB PROGRESS') {
                    $rekapSheet->getStyle("L{$row}")->getFont()->getColor()->setRGB('0284C7');
                }

                if ($no % 2 == 0) {
                    $rekapSheet->getStyle("B{$row}:M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
                }
                $rekapSheet->getRowDimension($row)->setRowHeight(20);
                $row++;
            }
        }

        $rowEnd = $row - 1;
        if ($rowEnd >= $rowStart) {
            $this->applyGridBorders($rekapSheet, "B{$rowStart}:{$endCol}{$rowEnd}");
            $totRow = $row;
            $rekapSheet->setCellValue("B{$totRow}", 'TOTAL KESELURUHAN');
            $rekapSheet->mergeCells("B{$totRow}:F{$totRow}");
            $rekapSheet->getStyle("B{$totRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rekapSheet->setCellValue("G{$totRow}", "=SUM(G{$rowStart}:G{$rowEnd})");
            $rekapSheet->setCellValue("H{$totRow}", "=SUM(H{$rowStart}:H{$rowEnd})");
            $rekapSheet->setCellValue("I{$totRow}", "=SUM(I{$rowStart}:I{$rowEnd})");
            $rekapSheet->setCellValue("J{$totRow}", "=SUM(J{$rowStart}:J{$rowEnd})");
            $rekapSheet->setCellValue("K{$totRow}", "=SUM(K{$rowStart}:K{$rowEnd})");
            $rekapSheet->setCellValue("L{$totRow}", '-');
            $rekapSheet->setCellValue("M{$totRow}", '-');

            $rekapSheet->getStyle("G{$totRow}:K{$totRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            $rekapSheet->getStyle("B{$totRow}:{$endCol}{$totRow}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ]);
            $rekapSheet->getRowDimension($totRow)->setRowHeight(24);
        }
        $this->autoFitColumns($rekapSheet, 'B', $endCol);
        $rekapSheet->freezePane('G6');

        // ─────────────────────────────────────────────────────────────
        // 2. Sheet Rincian Detail Log Harian Seluruh Rig (Konsolidasi Tanggal & Lokasi)
        // ─────────────────────────────────────────────────────────────
        $allLogsSheet = $spreadsheet->createSheet();
        $allLogsSheet->setTitle('DETAIL LOG ALL RIGS');
        $allLogsSheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $allLogsSheet,
            "RINCIAN LOG HARIAN OPERASI SELURUH ARMADA RIG BMS",
            "PERIODE OPERASI: {$bulanName} {$tahun}",
            'R'
        );

        $headersAllLogs = [
            'NO'                       => Alignment::HORIZONTAL_CENTER,
            'ARMADA RIG'               => Alignment::HORIZONTAL_CENTER,
            'TANGGAL OPERASI'          => Alignment::HORIZONTAL_CENTER,
            'TEMPAT / LOKASI SUMUR'    => Alignment::HORIZONTAL_LEFT,
            'JARAK (KM)'               => Alignment::HORIZONTAL_RIGHT,
            'MIRU (JAM)'               => Alignment::HORIZONTAL_RIGHT,
            'OPS (JAM)'                => Alignment::HORIZONTAL_RIGHT,
            'SBWC RAIN (JAM)'          => Alignment::HORIZONTAL_RIGHT,
            'SBWC ROAD/PAD (JAM)'      => Alignment::HORIZONTAL_RIGHT,
            'SBWC DAYLIGHT (JAM)'      => Alignment::HORIZONTAL_RIGHT,
            'SBWC 3RD PARTY (JAM)'     => Alignment::HORIZONTAL_RIGHT,
            'UNPAID RIG (JAM)'         => Alignment::HORIZONTAL_RIGHT,
            'UNPAID TOOL (JAM)'        => Alignment::HORIZONTAL_RIGHT,
            'TOTAL DOWNTIME (JAM)'     => Alignment::HORIZONTAL_RIGHT,
            'TOTAL JAM OPERASI (JAM)'  => Alignment::HORIZONTAL_RIGHT,
            'STATUS JOB'               => Alignment::HORIZONTAL_CENTER,
            'REMARK / URAIAN PEKERJAAN'=> Alignment::HORIZONTAL_LEFT,
        ];

        $cLogIdx = 2; // Col B
        foreach ($headersAllLogs as $h => $align) {
            $cL = Coordinate::stringFromColumnIndex($cLogIdx);
            $allLogsSheet->setCellValue("{$cL}5", $h);
            $cLogIdx++;
        }
        $endColAllLogs = Coordinate::stringFromColumnIndex($cLogIdx - 1);
        $this->applyHeaderStyle($allLogsSheet, "B5:{$endColAllLogs}5");
        $allLogsSheet->getRowDimension(5)->setRowHeight(28);

        // Fetch all logs across all active rigs for this month/year
        $allLogs = $db->table('daily_report_log drl')
            ->select('drl.*, r.kode as kode_rig, r.nama_rig, l.nama_lokasi, dr.no_well, dr.status_job as parent_status')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('rigs r', 'r.id = dr.rig_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('r.id', 'ASC')
            ->orderBy('drl.tanggal', 'ASC')
            ->orderBy('drl.id', 'ASC')
            ->get()->getResultArray();

        $lRowStart = 6;
        $lRow = $lRowStart;
        $lNo = 1;

        foreach ($allLogs as $lg) {
            $tglFormatted = $lg['tanggal'] ? date('d/m/Y', strtotime($lg['tanggal'])) : '-';
            $lokasiNama   = !empty($lg['nama_lokasi']) ? $lg['nama_lokasi'] : ('Sumur #' . ($lg['no_well'] ?? '-'));
            $roadPad      = (float)($lg['dt_dry_road'] ?? 0) + (float)($lg['dt_dry_pad'] ?? 0);
            $thirdParty   = (float)($lg['dt_3rd_party'] ?? 0) + (float)($lg['dt_phr_op'] ?? 0) + (float)($lg['dt_trans'] ?? 0) + (float)($lg['dt_ce_pe'] ?? 0) + (float)($lg['dt_phr_well'] ?? 0) + (float)($lg['dt_foam'] ?? 0);
            $remarkText   = !empty($lg['remark_npt']) ? $lg['remark_npt'] : (!empty($lg['remark_unpaid']) ? $lg['remark_unpaid'] : '-');

            $allLogsSheet->setCellValue("B{$lRow}", $lNo++);
            $allLogsSheet->setCellValue("C{$lRow}", $lg['kode_rig']);
            $allLogsSheet->setCellValue("D{$lRow}", $tglFormatted);
            $allLogsSheet->setCellValue("E{$lRow}", $lokasiNama);
            $allLogsSheet->setCellValue("F{$lRow}", (float)($lg['jarak'] ?? 0));
            $allLogsSheet->setCellValue("G{$lRow}", (float)($lg['miru_jam'] ?? 0));
            $allLogsSheet->setCellValue("H{$lRow}", (float)($lg['ops_jam'] ?? 0));
            $allLogsSheet->setCellValue("I{$lRow}", (float)($lg['dt_rain'] ?? 0));
            $allLogsSheet->setCellValue("J{$lRow}", $roadPad);
            $allLogsSheet->setCellValue("K{$lRow}", (float)($lg['dt_daylight'] ?? 0));
            $allLogsSheet->setCellValue("L{$lRow}", $thirdParty);
            $allLogsSheet->setCellValue("M{$lRow}", (float)($lg['dt_rig'] ?? 0));
            $allLogsSheet->setCellValue("N{$lRow}", (float)($lg['dt_tool'] ?? 0));
            $allLogsSheet->setCellValue("O{$lRow}", (float)($lg['total_dt'] ?? 0));
            $allLogsSheet->setCellValue("P{$lRow}", (float)($lg['total_hrs'] ?? 0));
            $allLogsSheet->setCellValue("Q{$lRow}", $lg['parent_status'] ?? '-');
            $allLogsSheet->setCellValue("R{$lRow}", $remarkText);

            $allLogsSheet->getStyle("B{$lRow}:D{$lRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $allLogsSheet->getStyle("C{$lRow}")->getFont()->setBold(true);
            $allLogsSheet->getStyle("C{$lRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
            $allLogsSheet->getStyle("D{$lRow}")->getFont()->setBold(true);
            $allLogsSheet->getStyle("E{$lRow}")->getFont()->setBold(true);
            $allLogsSheet->getStyle("F{$lRow}:P{$lRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $allLogsSheet->getStyle("Q{$lRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $allLogsSheet->getStyle("Q{$lRow}")->getFont()->setBold(true);

            if ((float)($lg['dt_rig'] ?? 0) > 0 || (float)($lg['dt_tool'] ?? 0) > 0) {
                $allLogsSheet->getStyle("M{$lRow}:N{$lRow}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
            }

            if ($lNo % 2 == 0) {
                $allLogsSheet->getStyle("B{$lRow}:R{$lRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }
            $allLogsSheet->getRowDimension($lRow)->setRowHeight(21);
            $lRow++;
        }

        $lRowEnd = $lRow - 1;
        if ($lRowEnd >= $lRowStart) {
            $this->applyGridBorders($allLogsSheet, "B{$lRowStart}:{$endColAllLogs}{$lRowEnd}");

            $totLRow = $lRow;
            $allLogsSheet->setCellValue("B{$totLRow}", 'TOTAL AKUMULASI SELURUH RIG');
            $allLogsSheet->mergeCells("B{$totLRow}:E{$totLRow}");
            $allLogsSheet->getStyle("B{$totLRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $allLogsSheet->setCellValue("F{$totLRow}", "=SUM(F{$lRowStart}:F{$lRowEnd})");
            $allLogsSheet->setCellValue("G{$totLRow}", "=SUM(G{$lRowStart}:G{$lRowEnd})");
            $allLogsSheet->setCellValue("H{$totLRow}", "=SUM(H{$lRowStart}:H{$lRowEnd})");
            $allLogsSheet->setCellValue("I{$totLRow}", "=SUM(I{$lRowStart}:I{$lRowEnd})");
            $allLogsSheet->setCellValue("J{$totLRow}", "=SUM(J{$lRowStart}:J{$lRowEnd})");
            $allLogsSheet->setCellValue("K{$totLRow}", "=SUM(K{$lRowStart}:K{$lRowEnd})");
            $allLogsSheet->setCellValue("L{$totLRow}", "=SUM(L{$lRowStart}:L{$lRowEnd})");
            $allLogsSheet->setCellValue("M{$totLRow}", "=SUM(M{$lRowStart}:M{$lRowEnd})");
            $allLogsSheet->setCellValue("N{$totLRow}", "=SUM(N{$lRowStart}:N{$lRowEnd})");
            $allLogsSheet->setCellValue("O{$totLRow}", "=SUM(O{$lRowStart}:O{$lRowEnd})");
            $allLogsSheet->setCellValue("P{$totLRow}", "=SUM(P{$lRowStart}:P{$lRowEnd})");
            $allLogsSheet->setCellValue("Q{$totLRow}", '-');
            $allLogsSheet->setCellValue("R{$totLRow}", '-');

            $allLogsSheet->getStyle("F{$totLRow}:P{$totLRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            $allLogsSheet->getStyle("B{$totLRow}:{$endColAllLogs}{$totLRow}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
            ]);
            $allLogsSheet->getRowDimension($totLRow)->setRowHeight(24);
        }
        $this->autoFitColumns($allLogsSheet, 'B', $endColAllLogs);
        $allLogsSheet->freezePane('F6');

        // ─────────────────────────────────────────────────────────────
        // 3. Buat Lembar Sheet Per Rig Persis Template "SUMMARY REPORT PER WELL"
        // ─────────────────────────────────────────────────────────────
        foreach ($rigs as $rig) {
            $rigReports = $this->dailyReportModel->getByRigBulanTahun($rig['id'], $bulan, $tahun);
            if (empty($rigReports)) continue;

            $sheet = $spreadsheet->createSheet();
            $sheetTitle = substr(preg_replace('/[\\\\\/\?\*\[\]]/', '', $rig['kode']), 0, 30);
            $sheet->setTitle($sheetTitle);
            $this->populateSummaryReportPerWellSheet($sheet, $rig, $bulan, $tahun);
        }

        $spreadsheet->setActiveSheetIndex(0);
        $filename = "Daily_Report_Detail_ALL_RIGS_{$bulanName}_{$tahun}.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 4C. EXPORT BUNDLE ALL-IN-ONE (EXECUTIVE COMPLETE PACKAGE)
     * Menggabungkan semua lembar laporan dalam 1 Workbook Excel:
     * 1. Summary Operation RAU (Kinerja Reliabilitas, Utilitas, Revenue)
     * 2. Rekap NPT Seluruh Armada Rig
     * 3. Rekap Seluruh Pekerjaan Sumur (Daily Report All Rigs)
     * 4. Nilai Kontrak ODR Armada Rig
     * =========================================================================
     */
    public function bundleAll(int $bulan, int $tahun)
    {
        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $spreadsheet = new Spreadsheet();
        $rigs        = $this->rigModel->getRigAktif();
        $summaries   = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);
        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $db          = $this->db;

        // ─────────────────────────────────────────────────────────────
        // SHEET 1: SUMMARY OPERATION RAU
        // ─────────────────────────────────────────────────────────────
        $s1 = $spreadsheet->getActiveSheet();
        $s1->setTitle('1. SUMMARY OPERATION');
        $s1->setShowGridLines(true);
        $this->renderCompanyHeader($s1, 'SUMMARY REPORT OPERATION RIG BMS', "PERIODE: {$bulanName} {$tahun}", 'Q');

        $s1->setCellValue('B5', 'NO');
        $s1->setCellValue('C5', 'NAME RIG');
        $s1->setCellValue('D5', 'RAU');
        $s1->setCellValue('G5', 'TOTAL MIRU (HRS)');
        $s1->setCellValue('H5', 'TOTAL OPS (HRS)');
        $s1->setCellValue('I5', 'AVERAGE MIRU (HRS)');
        $s1->setCellValue('J5', 'AVERAGE CYCLE TIME (HRS)');
        $s1->setCellValue('K5', 'TOTAL WELL JOB');
        $s1->setCellValue('L5', 'DOWNTIME (HRS)');
        $s1->setCellValue('N5', 'REVENUE');
        $s1->setCellValue('P5', 'TOTAL HOURS');
        $s1->setCellValue('Q5', 'REMARK');

        $s1->setCellValue('D6', 'RELIABILITY (%)');
        $s1->setCellValue('E6', 'AVAILABILITY (%)');
        $s1->setCellValue('F6', 'UTILIZATION (%)');
        $s1->setCellValue('L6', 'SBWC (HRS)');
        $s1->setCellValue('M6', 'UNPAID (HRS)');
        $s1->setCellValue('N6', 'TARGET (Rp)');
        $s1->setCellValue('O6', 'ACTUAL (Rp)');

        $s1->mergeCells('B5:B6');
        $s1->mergeCells('C5:C6');
        $s1->mergeCells('D5:F5');
        $s1->mergeCells('G5:G6');
        $s1->mergeCells('H5:H6');
        $s1->mergeCells('I5:I6');
        $s1->mergeCells('J5:J6');
        $s1->mergeCells('K5:K6');
        $s1->mergeCells('L5:M5');
        $s1->mergeCells('N5:O5');
        $s1->mergeCells('P5:P6');
        $s1->mergeCells('Q5:Q6');

        $this->applyHeaderStyle($s1, 'B5:Q6');
        $s1->getRowDimension(5)->setRowHeight(24);
        $s1->getRowDimension(6)->setRowHeight(24);

        $row = 7;
        $no = 1;
        foreach ($summaries as $s) {
            $s1->setCellValue("B{$row}", $no++);
            $s1->setCellValue("C{$row}", $s['kode']);
            $s1->setCellValue("D{$row}", (float)($s['reliability'] ?? 0));
            $s1->setCellValue("E{$row}", (float)($s['availability'] ?? 0));
            $s1->setCellValue("F{$row}", (float)($s['utilization'] ?? 0));
            $s1->setCellValue("G{$row}", (float)($s['total_miru'] ?? 0));
            $s1->setCellValue("H{$row}", (float)($s['total_ops'] ?? 0));
            $s1->setCellValue("I{$row}", (float)($s['avg_miru'] ?? 0));
            $s1->setCellValue("J{$row}", (float)($s['avg_cycle_time'] ?? 0));
            $s1->setCellValue("K{$row}", (int)($s['total_well_job'] ?? 0));
            $s1->setCellValue("L{$row}", (float)($s['sbwc_jam'] ?? 0));
            $s1->setCellValue("M{$row}", (float)($s['unpaid_jam'] ?? 0));
            $s1->setCellValue("N{$row}", (float)($s['revenue_target'] ?? 0));
            $s1->setCellValue("O{$row}", (float)($s['revenue_actual'] ?? 0));
            $s1->setCellValue("P{$row}", (float)($s['total_jam'] ?? 0));
            $s1->setCellValue("Q{$row}", $s['remark'] ?? '-');

            $s1->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s1->getStyle("C{$row}")->getFont()->setBold(true);
            $s1->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
            $s1->getStyle("D{$row}:F{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $s1->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s1->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $s1->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s1->getStyle("L{$row}:M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s1->getStyle("N{$row}:O{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $s1->getStyle("P{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            if ((float)($s['unpaid_jam'] ?? 0) > 0) {
                $s1->getStyle("M{$row}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
            }
            if ($no % 2 == 0) {
                $s1->getStyle("D{$row}:Q{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }
            $s1->getRowDimension($row)->setRowHeight(20);
            $row++;
        }
        $endR = $row - 1;
        if ($endR >= 7) {
            $this->applyGridBorders($s1, "B7:Q{$endR}");
            $s1->setCellValue("B{$row}", 'TOTAL');
            $s1->mergeCells("B{$row}:C{$row}");
            $s1->setCellValue("D{$row}", "=AVERAGE(D7:D{$endR})");
            $s1->setCellValue("E{$row}", "=AVERAGE(E7:E{$endR})");
            $s1->setCellValue("F{$row}", "=AVERAGE(F7:F{$endR})");
            $s1->setCellValue("G{$row}", "=SUM(G7:G{$endR})");
            $s1->setCellValue("H{$row}", "=SUM(H7:H{$endR})");
            $s1->setCellValue("I{$row}", "=AVERAGE(I7:I{$endR})");
            $s1->setCellValue("J{$row}", "=AVERAGE(J7:J{$endR})");
            $s1->setCellValue("K{$row}", "=SUM(K7:K{$endR})");
            $s1->setCellValue("L{$row}", "=SUM(L7:L{$endR})");
            $s1->setCellValue("M{$row}", "=SUM(M7:M{$endR})");
            $s1->setCellValue("N{$row}", "=SUM(N7:N{$endR})");
            $s1->setCellValue("O{$row}", "=SUM(O7:O{$endR})");
            $s1->setCellValue("P{$row}", "=SUM(P7:P{$endR})");
            $s1->setCellValue("Q{$row}", '-');

            $s1->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s1->getStyle("D{$row}:F{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $s1->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s1->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $s1->getStyle("L{$row}:M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s1->getStyle("N{$row}:O{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $s1->getStyle("P{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            $s1->getStyle("B{$row}:Q{$row}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }
        $this->autoFitColumns($s1, 'B', 'Q');
        $s1->freezePane('D7');

        // ─────────────────────────────────────────────────────────────
        // SHEET 2: NPT ALL RIGS
        // ─────────────────────────────────────────────────────────────
        $s2 = $spreadsheet->createSheet();
        $s2->setTitle('2. DOWNTIME NPT ALL RIG');
        $s2->setShowGridLines(true);
        $this->renderCompanyHeader($s2, 'REKAPITULASI DOWNTIME (NPT) SELURUH ARMADA RIG BMS', "PERIODE: {$bulanName} {$tahun}", 'AJ');

        $summaryAllRig = $this->nptModel->getSummaryAllRig($bulan, $tahun);
        $s2->setCellValue('B5', 'NO');
        $s2->setCellValue('C5', 'NAME RIG');
        $s2->mergeCells('B5:B6');
        $s2->mergeCells('C5:C6');

        $colIdx = 4;
        foreach ($kategoriList as $kat) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $s2->setCellValue("{$colLetter}5", $kat['nama']);
            $s2->setCellValue("{$colLetter}6", strtoupper($kat['tipe']));
            $colIdx++;
        }
        $colTotal = Coordinate::stringFromColumnIndex($colIdx);
        $s2->setCellValue("{$colTotal}5", 'TOTAL');
        $s2->setCellValue("{$colTotal}6", 'HOURS');
        $endColNpt = $colTotal;
        $this->applyHeaderStyle($s2, "B5:{$endColNpt}6");

        $r2 = 7;
        $n2 = 1;
        foreach ($summaries as $s) {
            $s2->setCellValue("B{$r2}", $n2++);
            $s2->setCellValue("C{$r2}", $s['kode']);
            $s2->getStyle("B{$r2}:C{$r2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s2->getStyle("C{$r2}")->getFont()->setBold(true);
            $s2->getStyle("B{$r2}:C{$r2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $cIdx = 4;
            $firstC = Coordinate::stringFromColumnIndex(4);
            $lastC  = Coordinate::stringFromColumnIndex(4 + count($kategoriList) - 1);
            foreach ($kategoriList as $kat) {
                $cL  = Coordinate::stringFromColumnIndex($cIdx);
                $val = (float)($summaryAllRig[$s['rig_id']][$kat['id']] ?? 0);
                $s2->setCellValue("{$cL}{$r2}", $val > 0 ? $val : 0);
                $s2->getStyle("{$cL}{$r2}")->getNumberFormat()->setFormatCode('#,##0.00');
                if ($kat['tipe'] === 'UNPAID' && $val > 0) {
                    $s2->getStyle("{$cL}{$r2}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
                }
                $cIdx++;
            }
            $tL = Coordinate::stringFromColumnIndex($cIdx);
            $s2->setCellValue("{$tL}{$r2}", "=SUM({$firstC}{$r2}:{$lastC}{$r2})");
            $s2->getStyle("{$tL}{$r2}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s2->getStyle("{$tL}{$r2}")->getFont()->setBold(true);
            $s2->getStyle("{$tL}{$r2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
            $s2->getRowDimension($r2)->setRowHeight(20);
            $r2++;
        }
        $endR2 = $r2 - 1;
        if ($endR2 >= 7) {
            $this->applyGridBorders($s2, "B7:{$endColNpt}{$endR2}");
            $s2->setCellValue("B{$r2}", 'TOTAL');
            $s2->mergeCells("B{$r2}:C{$r2}");
            $s2->getStyle("B{$r2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($c = 4; $c <= $colIdx; $c++) {
                $cL = Coordinate::stringFromColumnIndex($c);
                $s2->setCellValue("{$cL}{$r2}", "=SUM({$cL}7:{$cL}{$endR2})");
                $s2->getStyle("{$cL}{$r2}")->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $s2->getStyle("B{$r2}:{$endColNpt}{$r2}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }
        $this->autoFitColumns($s2, 'B', $endColNpt);
        $s2->freezePane('D7');

        // ─────────────────────────────────────────────────────────────
        // SHEET 3: PEKERJAAN SUMUR REKAP SELURUH RIG
        // ─────────────────────────────────────────────────────────────
        $s3 = $spreadsheet->createSheet();
        $s3->setTitle('3. PEKERJAAN SUMUR');
        $s3->setShowGridLines(true);
        $this->renderCompanyHeader($s3, 'REKAPITULASI PEKERJAAN SUMUR SELURUH RIG BMS', "PERIODE: {$bulanName} {$tahun}", 'M');

        $headersSumur = [
            'NO'             => Alignment::HORIZONTAL_CENTER,
            'ARMADA RIG'     => Alignment::HORIZONTAL_CENTER,
            'SUMUR / LOKASI' => Alignment::HORIZONTAL_LEFT,
            'TGL MULAI'      => Alignment::HORIZONTAL_CENTER,
            'TGL SELESAI'    => Alignment::HORIZONTAL_CENTER,
            'DISTANCE (Rp)'  => Alignment::HORIZONTAL_RIGHT,
            'MIRU (JAM)'     => Alignment::HORIZONTAL_RIGHT,
            'OPS (JAM)'      => Alignment::HORIZONTAL_RIGHT,
            'DOWNTIME (JAM)' => Alignment::HORIZONTAL_RIGHT,
            'TOTAL JAM'      => Alignment::HORIZONTAL_RIGHT,
            'STATUS JOB'     => Alignment::HORIZONTAL_CENTER,
            'REMARK'         => Alignment::HORIZONTAL_LEFT,
        ];
        $cIdx3 = 2;
        foreach ($headersSumur as $h => $align) {
            $cL = Coordinate::stringFromColumnIndex($cIdx3);
            $s3->setCellValue("{$cL}5", $h);
            $cIdx3++;
        }
        $endColSumur = Coordinate::stringFromColumnIndex($cIdx3 - 1);
        $this->applyHeaderStyle($s3, "B5:{$endColSumur}5");
        $s3->getRowDimension(5)->setRowHeight(26);

        $r3 = 6;
        $n3 = 1;
        foreach ($rigs as $rig) {
            $reports = $this->dailyReportModel->getByRigBulanTahun($rig['id'], $bulan, $tahun);
            if (empty($reports)) continue;

            foreach ($reports as $rep) {
                $s3->setCellValue("B{$r3}", $n3++);
                $s3->setCellValue("C{$r3}", $rig['kode']);
                $s3->setCellValue("D{$r3}", $rep['nama_lokasi'] ?? '-');
                $s3->setCellValue("E{$r3}", $rep['tanggal_mulai'] ?: '-');
                $s3->setCellValue("F{$r3}", $rep['tanggal_selesai'] ?: '-');
                $s3->setCellValue("G{$r3}", (float)($rep['jarak'] ?? 0));
                $s3->setCellValue("H{$r3}", (float)($rep['miru_jam'] ?? 0));
                $s3->setCellValue("I{$r3}", (float)($rep['ops_jam'] ?? 0));
                $s3->setCellValue("J{$r3}", (float)($rep['total_dt'] ?? 0));
                $s3->setCellValue("K{$r3}", (float)($rep['total_jam'] ?? 0));
                $s3->setCellValue("L{$r3}", $rep['status_job'] ?? '-');
                $s3->setCellValue("M{$r3}", $rep['remark'] ?? '-');

                $s3->getStyle("B{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle("C{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle("C{$r3}")->getFont()->setBold(true);
                $s3->getStyle("C{$r3}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
                $s3->getStyle("D{$r3}")->getFont()->setBold(true);
                $s3->getStyle("E{$r3}:F{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle("G{$r3}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $s3->getStyle("H{$r3}:K{$r3}")->getNumberFormat()->setFormatCode('#,##0.00');
                $s3->getStyle("L{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $s3->getStyle("L{$r3}")->getFont()->setBold(true);

                if ($n3 % 2 == 0) {
                    $s3->getStyle("B{$r3}:M{$r3}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
                }
                $s3->getRowDimension($r3)->setRowHeight(20);
                $r3++;
            }
        }
        $endR3 = $r3 - 1;
        if ($endR3 >= 6) {
            $this->applyGridBorders($s3, "B6:{$endColSumur}{$endR3}");
            $s3->setCellValue("B{$r3}", 'TOTAL KESELURUHAN');
            $s3->mergeCells("B{$r3}:F{$r3}");
            $s3->getStyle("B{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $s3->setCellValue("G{$r3}", "=SUM(G6:G{$endR3})");
            $s3->setCellValue("H{$r3}", "=SUM(H6:H{$endR3})");
            $s3->setCellValue("I{$r3}", "=SUM(I6:I{$endR3})");
            $s3->setCellValue("J{$r3}", "=SUM(J6:J{$endR3})");
            $s3->setCellValue("K{$r3}", "=SUM(K6:K{$endR3})");
            $s3->setCellValue("L{$r3}", '-');
            $s3->setCellValue("M{$r3}", '-');

            $s3->getStyle("G{$r3}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $s3->getStyle("H{$r3}:K{$r3}")->getNumberFormat()->setFormatCode('#,##0.00');

            $s3->getStyle("B{$r3}:{$endColSumur}{$r3}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }
        $this->autoFitColumns($s3, 'B', $endColSumur);
        $s3->freezePane('G6');

        // ─────────────────────────────────────────────────────────────
        // SHEET 4: RINCIAN DETAIL LOG OPERASI SELURUH RIG (DAY BY DAY)
        // ─────────────────────────────────────────────────────────────
        $s4 = $spreadsheet->createSheet();
        $s4->setTitle('4. DETAIL LOG OPERASI');
        $s4->setShowGridLines(true);
        $this->renderCompanyHeader(
            $s4,
            'RINCIAN LOG HARIAN OPERASI & DOWNTIME SELURUH RIG BMS',
            "PERIODE OPERASIONAL: {$bulanName} {$tahun}",
            'R'
        );

        $headersBundleLog = [
            'NO'                       => Alignment::HORIZONTAL_CENTER,
            'ARMADA RIG'               => Alignment::HORIZONTAL_CENTER,
            'TANGGAL OPERASI'          => Alignment::HORIZONTAL_CENTER,
            'TEMPAT / LOKASI SUMUR'    => Alignment::HORIZONTAL_LEFT,
            'JARAK (KM)'               => Alignment::HORIZONTAL_RIGHT,
            'MIRU (JAM)'               => Alignment::HORIZONTAL_RIGHT,
            'OPS (JAM)'                => Alignment::HORIZONTAL_RIGHT,
            'SBWC RAIN (JAM)'          => Alignment::HORIZONTAL_RIGHT,
            'SBWC ROAD/PAD (JAM)'      => Alignment::HORIZONTAL_RIGHT,
            'SBWC DAYLIGHT (JAM)'      => Alignment::HORIZONTAL_RIGHT,
            'SBWC 3RD PARTY (JAM)'     => Alignment::HORIZONTAL_RIGHT,
            'UNPAID RIG (JAM)'         => Alignment::HORIZONTAL_RIGHT,
            'UNPAID TOOL (JAM)'        => Alignment::HORIZONTAL_RIGHT,
            'TOTAL DOWNTIME (JAM)'     => Alignment::HORIZONTAL_RIGHT,
            'TOTAL JAM OPERASI (JAM)'  => Alignment::HORIZONTAL_RIGHT,
            'STATUS JOB'               => Alignment::HORIZONTAL_CENTER,
            'REMARK / URAIAN PEKERJAAN'=> Alignment::HORIZONTAL_LEFT,
        ];

        $cIdx4 = 2;
        foreach ($headersBundleLog as $h => $align) {
            $cL = Coordinate::stringFromColumnIndex($cIdx4);
            $s4->setCellValue("{$cL}5", $h);
            $cIdx4++;
        }
        $endColBundleLog = Coordinate::stringFromColumnIndex($cIdx4 - 1);
        $this->applyHeaderStyle($s4, "B5:{$endColBundleLog}5");
        $s4->getRowDimension(5)->setRowHeight(28);

        $allLogsBundle = $db->table('daily_report_log drl')
            ->select('drl.*, r.kode as kode_rig, r.nama_rig, l.nama_lokasi, dr.no_well, dr.status_job as parent_status')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->join('rigs r', 'r.id = dr.rig_id')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('r.id', 'ASC')
            ->orderBy('drl.tanggal', 'ASC')
            ->orderBy('drl.id', 'ASC')
            ->get()->getResultArray();

        $r4 = 6;
        $n4 = 1;
        foreach ($allLogsBundle as $lg) {
            $tglFormatted = $lg['tanggal'] ? date('d/m/Y', strtotime($lg['tanggal'])) : '-';
            $lokasiNama   = !empty($lg['nama_lokasi']) ? $lg['nama_lokasi'] : ('Sumur #' . ($lg['no_well'] ?? '-'));
            $roadPad      = (float)($lg['dt_dry_road'] ?? 0) + (float)($lg['dt_dry_pad'] ?? 0);
            $thirdParty   = (float)($lg['dt_3rd_party'] ?? 0) + (float)($lg['dt_phr_op'] ?? 0) + (float)($lg['dt_trans'] ?? 0) + (float)($lg['dt_ce_pe'] ?? 0) + (float)($lg['dt_phr_well'] ?? 0) + (float)($lg['dt_foam'] ?? 0);
            $remarkText   = !empty($lg['remark_npt']) ? $lg['remark_npt'] : (!empty($lg['remark_unpaid']) ? $lg['remark_unpaid'] : '-');

            $s4->setCellValue("B{$r4}", $n4++);
            $s4->setCellValue("C{$r4}", $lg['kode_rig']);
            $s4->setCellValue("D{$r4}", $tglFormatted);
            $s4->setCellValue("E{$r4}", $lokasiNama);
            $s4->setCellValue("F{$r4}", (float)($lg['jarak'] ?? 0));
            $s4->setCellValue("G{$r4}", (float)($lg['miru_jam'] ?? 0));
            $s4->setCellValue("H{$r4}", (float)($lg['ops_jam'] ?? 0));
            $s4->setCellValue("I{$r4}", (float)($lg['dt_rain'] ?? 0));
            $s4->setCellValue("J{$r4}", $roadPad);
            $s4->setCellValue("K{$r4}", (float)($lg['dt_daylight'] ?? 0));
            $s4->setCellValue("L{$r4}", $thirdParty);
            $s4->setCellValue("M{$r4}", (float)($lg['dt_rig'] ?? 0));
            $s4->setCellValue("N{$r4}", (float)($lg['dt_tool'] ?? 0));
            $s4->setCellValue("O{$r4}", (float)($lg['total_dt'] ?? 0));
            $s4->setCellValue("P{$r4}", (float)($lg['total_hrs'] ?? 0));
            $s4->setCellValue("Q{$r4}", $lg['parent_status'] ?? '-');
            $s4->setCellValue("R{$r4}", $remarkText);

            $s4->getStyle("B{$r4}:D{$r4}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s4->getStyle("C{$r4}")->getFont()->setBold(true);
            $s4->getStyle("C{$r4}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
            $s4->getStyle("D{$r4}")->getFont()->setBold(true);
            $s4->getStyle("E{$r4}")->getFont()->setBold(true);
            $s4->getStyle("F{$r4}:P{$r4}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s4->getStyle("Q{$r4}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $s4->getStyle("Q{$r4}")->getFont()->setBold(true);

            if ((float)($lg['dt_rig'] ?? 0) > 0 || (float)($lg['dt_tool'] ?? 0) > 0) {
                $s4->getStyle("M{$r4}:N{$r4}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_RED);
            }

            if ($n4 % 2 == 0) {
                $s4->getStyle("B{$r4}:R{$r4}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }
            $s4->getRowDimension($r4)->setRowHeight(21);
            $r4++;
        }

        $endR4 = $r4 - 1;
        if ($endR4 >= 6) {
            $this->applyGridBorders($s4, "B6:{$endColBundleLog}{$endR4}");
            $totR4 = $r4;
            $s4->setCellValue("B{$totR4}", 'TOTAL KESELURUHAN LOG OPERASI');
            $s4->mergeCells("B{$totR4}:E{$totR4}");
            $s4->getStyle("B{$totR4}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $s4->setCellValue("F{$totR4}", "=SUM(F6:F{$endR4})");
            $s4->setCellValue("G{$totR4}", "=SUM(G6:G{$endR4})");
            $s4->setCellValue("H{$totR4}", "=SUM(H6:H{$endR4})");
            $s4->setCellValue("I{$totR4}", "=SUM(I6:I{$endR4})");
            $s4->setCellValue("J{$totR4}", "=SUM(J6:J{$endR4})");
            $s4->setCellValue("K{$totR4}", "=SUM(K6:K{$endR4})");
            $s4->setCellValue("L{$totR4}", "=SUM(L6:L{$endR4})");
            $s4->setCellValue("M{$totR4}", "=SUM(M6:M{$endR4})");
            $s4->setCellValue("N{$totR4}", "=SUM(N6:N{$endR4})");
            $s4->setCellValue("O{$totR4}", "=SUM(O6:O{$endR4})");
            $s4->setCellValue("P{$totR4}", "=SUM(P6:P{$endR4})");
            $s4->setCellValue("Q{$totR4}", '-');
            $s4->setCellValue("R{$totR4}", '-');

            $s4->getStyle("F{$totR4}:P{$totR4}")->getNumberFormat()->setFormatCode('#,##0.00');

            $s4->getStyle("B{$totR4}:{$endColBundleLog}{$totR4}")->applyFromArray([
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
            $s4->getRowDimension($totR4)->setRowHeight(24);
        }
        $this->autoFitColumns($s4, 'B', $endColBundleLog);
        $s4->freezePane('F6');

        $spreadsheet->setActiveSheetIndex(0);
        $filename = "LAPORAN_LENGKAP_EKSEKUTIF_BMS_{$bulanName}_{$tahun}.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 5. EXPORT REKAP TAHUNAN OPERASI
     * =========================================================================
     */
    public function rekapTahunan(int $tahun)
    {
        $rigs        = $this->rigModel->getRigAktif();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle("REKAP TAHUN {$tahun}");
        $sheet->setShowGridLines(true);

        $this->renderCompanyHeader(
            $sheet,
            "REKAPITULASI TAHUNAN OPERASI ARMADA RIG BMS",
            "TAHUN OPERASIONAL {$tahun}",
            'J'
        );

        $headers = [
            'NO'                      => Alignment::HORIZONTAL_CENTER,
            'KODE RIG'                => Alignment::HORIZONTAL_CENTER,
            'NAMA RIG'                => Alignment::HORIZONTAL_LEFT,
            'TOTAL SUMUR'             => Alignment::HORIZONTAL_CENTER,
            'AVG UTILITAS (%)'        => Alignment::HORIZONTAL_RIGHT,
            'TOTAL OPS (JAM)'         => Alignment::HORIZONTAL_RIGHT,
            'TOTAL DOWNTIME (JAM)'    => Alignment::HORIZONTAL_RIGHT,
            'TARGET REVENUE (Rp)'     => Alignment::HORIZONTAL_RIGHT,
            'REALISASI REVENUE (Rp)'  => Alignment::HORIZONTAL_RIGHT,
            'PENCAPAIAN (%)'          => Alignment::HORIZONTAL_RIGHT,
        ];

        $colIdx = 2; // Col B
        foreach ($headers as $h => $align) {
            $cL = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$cL}5", $h);
            $colIdx++;
        }

        $endCol = Coordinate::stringFromColumnIndex($colIdx - 1);
        $this->applyHeaderStyle($sheet, "B5:{$endCol}5");
        $sheet->getRowDimension(5)->setRowHeight(26);

        $rowStart = 6;
        $row = $rowStart;
        $no = 1;

        foreach ($rigs as $r) {
            $months     = $this->monthlySummaryModel->getByRigTahun($r['id'], $tahun);
            $sumWellJob = 0;
            $sumRevTgt  = 0;
            $sumRevenue = 0;
            $sumOps     = 0;
            $sumDt      = 0;
            $sumUtil    = 0;
            $mCount     = 0;

            foreach ($months as $m) {
                $sumWellJob += (int)($m['total_well_job'] ?? 0);
                $sumRevTgt  += (float)($m['revenue_target'] ?? 0);
                $sumRevenue += (float)($m['revenue_actual'] ?? 0);
                $sumOps     += (float)($m['total_ops'] ?? 0);
                $sumDt      += ((float)($m['sbwc_jam'] ?? 0) + (float)($m['unpaid_jam'] ?? 0));
                $sumUtil    += (float)($m['utilization'] ?? 0);
                $mCount++;
            }
            $avgUtil = $mCount > 0 ? ($sumUtil / $mCount) : 0;

            $sheet->setCellValue("B{$row}", $no++);
            $sheet->setCellValue("C{$row}", $r['kode']);
            $sheet->setCellValue("D{$row}", $r['nama_rig']);
            $sheet->setCellValue("E{$row}", $sumWellJob);
            $sheet->setCellValue("F{$row}", $avgUtil);
            $sheet->setCellValue("G{$row}", $sumOps);
            $sheet->setCellValue("H{$row}", $sumDt);
            $sheet->setCellValue("I{$row}", $sumRevTgt);
            $sheet->setCellValue("J{$row}", $sumRevenue);
            $sheet->setCellValue("K{$row}", "=IF(I{$row}>0, J{$row}/I{$row}, 0)");

            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle("G{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$row}:J{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("J{$row}")->getFont()->setBold(true);
            $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('0.00%');

            if ($no % 2 == 0) {
                $sheet->getStyle("B{$row}:K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $rowEnd = $row - 1;
        $this->applyGridBorders($sheet, "B{$rowStart}:K{$rowEnd}");

        // Footer Total
        $totRow = $row;
        $sheet->setCellValue("B{$totRow}", 'TOTAL / RATA-RATA');
        $sheet->mergeCells("B{$totRow}:D{$totRow}");
        $sheet->getStyle("B{$totRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("E{$totRow}", "=SUM(E{$rowStart}:E{$rowEnd})");
        $sheet->setCellValue("F{$totRow}", "=AVERAGE(F{$rowStart}:F{$rowEnd})");
        $sheet->setCellValue("G{$totRow}", "=SUM(G{$rowStart}:G{$rowEnd})");
        $sheet->setCellValue("H{$totRow}", "=SUM(H{$rowStart}:H{$rowEnd})");
        $sheet->setCellValue("I{$totRow}", "=SUM(I{$rowStart}:I{$rowEnd})");
        $sheet->setCellValue("J{$totRow}", "=SUM(J{$rowStart}:J{$rowEnd})");
        $sheet->setCellValue("K{$totRow}", "=IF(I{$totRow}>0, J{$totRow}/I{$totRow}, 0)");

        $sheet->getStyle("E{$totRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F{$totRow}")->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle("G{$totRow}:H{$totRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("I{$totRow}:J{$totRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle("K{$totRow}")->getNumberFormat()->setFormatCode('0.00%');

        $sheet->getStyle("B{$totRow}:K{$totRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        $sheet->getRowDimension($totRow)->setRowHeight(24);

        $this->autoFitColumns($sheet, 'B', 'K');
        $sheet->freezePane('E6');

        $filename = "REKAP_OPERASI_TAHUN_{$tahun}_SYS.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * =========================================================================
     * 6. EXPORT REKAP NPT TAHUNAN
     * =========================================================================
     */
    public function rekapNptTahunan(int $tahun)
    {
        $originalFile = 'C:\\Users\\LENOVO\\Downloads\\System\\REKAP NPT TAHUN  2026 SYS.xlsx';
        if (file_exists($originalFile)) {
            $filename = "REKAP_NPT_TAHUN_{$tahun}_SYS.xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Content-Length: ' . filesize($originalFile));
            header('Cache-Control: max-age=0');
            readfile($originalFile);
            exit;
        }

        // Fallback generator
        $jsonFile = WRITEPATH . 'rekap_npt_2026.json';
        $nptData  = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SUMMARY ALL RIG');
        $sheet->setShowGridLines(true);

        $currRow = 1;
        foreach ($nptData['months'] ?? [] as $mNum => $mData) {
            $sheet->setCellValue("A{$currRow}", $mData['title']);
            $sheet->getStyle("A{$currRow}")->getFont()->setName('Calibri')->setSize(13)->setBold(true)->getColor()->setRGB(self::COLOR_NAVY);
            $currRow++;

            $sheet->setCellValue("A{$currRow}", 'NO');
            $sheet->setCellValue("B{$currRow}", 'NAME RIG');
            $sheet->setCellValue("C{$currRow}", 'UNPAID');
            $sheet->setCellValue("E{$currRow}", 'STAND BY WITH CREW ( SBWC )');
            $sheet->setCellValue("AG{$currRow}", 'TOTAL (HRS)');
            $sheet->setCellValue("AH{$currRow}", 'REMARK UNPAID');

            $this->applyHeaderStyle($sheet, "A{$currRow}:AH" . ($currRow + 2));
            $currRow += 3;

            foreach ($mData['rows'] as $r) {
                $sheet->setCellValue("A{$currRow}", $r['no']);
                $sheet->setCellValue("B{$currRow}", $r['rig']);
                $sheet->getStyle("A{$currRow}:B{$currRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);

                for ($c = 1; $c <= 34; $c++) {
                    $colL = Coordinate::stringFromColumnIndex($c);
                    $val  = $r['vals'][$c - 1] ?? null;
                    if ($val !== null) {
                        $sheet->setCellValue("{$colL}{$currRow}", $val);
                    }
                }

                $sheet->getStyle("C{$currRow}:D{$currRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFF0000'));
                $sheet->getStyle("AG{$currRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_PEACH);
                $currRow++;
            }

            // Total Row
            $sheet->setCellValue("A{$currRow}", '');
            $sheet->setCellValue("B{$currRow}", 'TOTAL');
            for ($c = 1; $c <= 34; $c++) {
                $colL   = Coordinate::stringFromColumnIndex($c);
                $totVal = $mData['totals'][$c - 1] ?? null;
                if ($totVal !== null) {
                    $sheet->setCellValue("{$colL}{$currRow}", $totVal);
                }
            }
            $sheet->getStyle("A{$currRow}:AH{$currRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_YELLOW]],
                'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 10],
            ]);
            $sheet->getStyle("C{$currRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFF0000'));
            $currRow += 2;
        }

        $filename = "REKAP_NPT_TAHUN_{$tahun}_SYS.xlsx";
        $this->outputSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Helper: Stream file spreadsheet ke browser
     */
    private function outputSpreadsheet(Spreadsheet $spreadsheet, string $filename)
    {
        // Pastikan tidak ada output buffer yang bocor
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
