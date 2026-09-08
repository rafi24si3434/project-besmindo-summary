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
            $sheet->setCellValue("Q{$row}", $s['remark'] ?? '-');

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
     * =========================================================================
     * 4. EXPORT DAILY REPORT PER WELL
     * Persis Format Asli Daily Report SEPTEMBER 2026 SYS.xls
     * =========================================================================
     */
    public function dailyReport(int $rigId, int $bulan, int $tahun)
    {
        $rig = $this->rigModel->find($rigId);
        if (!$rig) {
            return redirect()->back()->with('error', 'Rig tidak ditemukan');
        }

        $bulanName   = self::BULAN_NAMES[$bulan] ?? "BULAN {$bulan}";
        $reports     = $this->dailyReportModel->getByRigBulanTahun($rigId, $bulan, $tahun);
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($rig['kode']);
        $sheet->setShowGridLines(true);

        $odrFormatted = number_format((float)($rig['odr'] ?? 0), 0, ',', '.');
        $this->renderCompanyHeader(
            $sheet,
            "SUMMARY REPORT PER WELL - {$rig['kode']} ({$rig['nama_rig']})",
            "PERIODE: {$bulanName} {$tahun} | KONTRAK ODR: Rp {$odrFormatted}/HARI",
            'L'
        );

        $headers = [
            'NO'             => Alignment::HORIZONTAL_CENTER,
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

        foreach ($reports as $rep) {
            $sheet->setCellValue("B{$row}", $no++);
            $sheet->setCellValue("C{$row}", $rep['nama_lokasi'] ?? '-');
            $sheet->setCellValue("D{$row}", $rep['tanggal_mulai'] ?: '-');
            $sheet->setCellValue("E{$row}", $rep['tanggal_selesai'] ?: '-');
            $sheet->setCellValue("F{$row}", (float)($rep['jarak'] ?? 0));
            $sheet->setCellValue("G{$row}", (float)($rep['miru_jam'] ?? 0));
            $sheet->setCellValue("H{$row}", (float)($rep['ops_jam'] ?? 0));
            $sheet->setCellValue("I{$row}", (float)($rep['total_dt'] ?? 0));
            $sheet->setCellValue("J{$row}", (float)($rep['total_jam'] ?? 0));
            $sheet->setCellValue("K{$row}", $rep['status_job'] ?? '-');
            $sheet->setCellValue("L{$row}", $rep['remark'] ?? '-');

            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setBold(true);
            $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("G{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$row}")->getFont()->setBold(true);

            // Highlight status job
            $status = strtoupper(trim((string)($rep['status_job'] ?? '')));
            if ($status === 'COMPLETED' || $status === 'SELESAI') {
                $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('15803D');
            } elseif ($status === 'RUNNING' || $status === 'ON GOING') {
                $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('0284C7');
            }

            if ($no % 2 == 0) {
                $sheet->getStyle("B{$row}:L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $rowEnd = $row - 1;
        if ($rowEnd >= $rowStart) {
            $this->applyGridBorders($sheet, "B{$rowStart}:{$endCol}{$rowEnd}");

            // Footer Total
            $totRow = $row;
            $sheet->setCellValue("B{$totRow}", 'TOTAL');
            $sheet->mergeCells("B{$totRow}:E{$totRow}");
            $sheet->getStyle("B{$totRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue("F{$totRow}", "=SUM(F{$rowStart}:F{$rowEnd})");
            $sheet->setCellValue("G{$totRow}", "=SUM(G{$rowStart}:G{$rowEnd})");
            $sheet->setCellValue("H{$totRow}", "=SUM(H{$rowStart}:H{$rowEnd})");
            $sheet->setCellValue("I{$totRow}", "=SUM(I{$rowStart}:I{$rowEnd})");
            $sheet->setCellValue("J{$totRow}", "=SUM(J{$rowStart}:J{$rowEnd})");
            $sheet->setCellValue("K{$totRow}", '-');
            $sheet->setCellValue("L{$totRow}", '-');

            $sheet->getStyle("F{$totRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("G{$totRow}:J{$totRow}")->getNumberFormat()->setFormatCode('#,##0.00');

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
        }

        $this->autoFitColumns($sheet, 'B', $endCol);
        $sheet->freezePane('F6');

        $filename = "Daily_Report_{$rig['kode']}_{$bulanName}_{$tahun}.xlsx";
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
