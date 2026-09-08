<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\NptModel;
use App\Models\DailyReportModel;
use App\Models\MonthlySummaryModel;
use App\Models\KategoriDowntimeModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $bulan = (int)$this->request->getGet('bulan') ?: (int)date('n');
        $tahun = (int)$this->request->getGet('tahun') ?: (int)date('Y');

        $rigModel             = new RigModel();
        $monthlySummaryModel  = new MonthlySummaryModel();
        $nptModel             = new NptModel();
        $kategoriModel        = new KategoriDowntimeModel();

        $rigs = $rigModel->getRigAktif();

        // Ensure calculations are prepared for each active rig
        foreach ($rigs as $r) {
            $monthlySummaryModel->hitungDanSimpan($r['id'], $bulan, $tahun);
        }

        $summaries = $monthlySummaryModel->getByBulanTahun($bulan, $tahun);

        // ── High-level KPI metrics ─────────────────────────────────────
        $totalWellJob       = 0;
        $totalRevenueTarget = 0;
        $totalRevenueActual = 0;
        $totalDowntime      = 0;
        $totalMiru          = 0;
        $totalOps           = 0;
        $sumUtil            = 0;
        $rigCount           = count($summaries);

        foreach ($summaries as $s) {
            $totalWellJob       += (int)($s['total_well_job'] ?? 0);
            $totalRevenueTarget += (float)($s['revenue_target'] ?? 0);
            $totalRevenueActual += (float)($s['revenue_actual'] ?? 0);
            $totalDowntime      += ((float)($s['sbwc_jam'] ?? 0) + (float)($s['unpaid_jam'] ?? 0));
            $totalMiru          += (float)($s['total_miru'] ?? 0);
            $totalOps           += (float)($s['total_ops'] ?? 0);
            $sumUtil            += (float)($s['utilization'] ?? 0);
        }

        $avgUtil = $rigCount > 0 ? ($sumUtil / $rigCount) * 100 : 0;

        // ── Doughnut: Downtime by kategori ─────────────────────────────
        $allKategori    = $kategoriModel->getKategoriAktif();
        $summaryAllRig  = $nptModel->getSummaryAllRig($bulan, $tahun);

        $catLabels = [];
        $catData   = [];
        foreach ($allKategori as $kat) {
            $catTotal = 0;
            foreach ($summaryAllRig as $rigId => $katArr) {
                $catTotal += (float)($katArr[$kat['id']] ?? 0);
            }
            if ($catTotal > 0) {
                $catLabels[] = $kat['nama'];
                $catData[]   = round($catTotal, 2);
            }
        }

        // ── Bar: Utilitas per Rig ────────────────────────────────────
        $rigLabels   = [];
        $rigUtilData = [];
        $rigRevTarget = [];
        $rigRevActual = [];
        $rigMiruData  = [];
        $rigOpsData   = [];
        $rigSbwcData  = [];
        $rigUnpaidData = [];
        $rigWellData  = [];

        foreach ($summaries as $s) {
            $rigLabels[]    = $s['kode'];
            $rigUtilData[]  = round(((float)($s['utilization'] ?? 0)) * 100, 2);
            $rigRevTarget[] = round((float)($s['revenue_target'] ?? 0) / 1_000_000, 2);
            $rigRevActual[] = round((float)($s['revenue_actual'] ?? 0) / 1_000_000, 2);
            $rigMiruData[]  = round((float)($s['total_miru'] ?? 0), 2);
            $rigOpsData[]   = round((float)($s['total_ops'] ?? 0), 2);
            $rigSbwcData[]  = round((float)($s['sbwc_jam'] ?? 0), 2);
            $rigUnpaidData[] = round((float)($s['unpaid_jam'] ?? 0), 2);
            $rigWellData[]  = (int)($s['total_well_job'] ?? 0);
        }

        // ── Line: Trend NPT Bulanan 2026 from JSON ───────────────────
        $nptJsonFile    = WRITEPATH . 'rekap_npt_2026.json';
        $nptRawData     = file_exists($nptJsonFile)
            ? json_decode(file_get_contents($nptJsonFile), true)
            : [];

        $nptMonthLabels = [];
        $nptUnpaidTrend = [];
        $nptSbwcTrend   = [];
        $nptTotalTrend  = [];

        for ($m = 1; $m <= 8; $m++) {
            $md = $nptRawData['months'][$m] ?? null;
            $nptMonthLabels[] = $md['name'] ?? "Bln $m";

            // totals[2]=Repaire(UNPAID), totals[3]=PERSONEL, totals[32]=TOTAL(HRS)
            $unpaid = round((float)($md['totals'][2] ?? 0) + (float)($md['totals'][3] ?? 0), 2);
            $total  = round((float)($md['totals'][32] ?? 0), 2);
            $sbwc   = round($total - $unpaid, 2);

            $nptUnpaidTrend[] = $unpaid;
            $nptSbwcTrend[]   = max(0, $sbwc);
            $nptTotalTrend[]  = $total;
        }

        $data = [
            'title'              => 'Dashboard Operasi Rig',
            'page_title'         => 'Dashboard Monitoring Operasi Rig',
            'page_subtitle'      => 'Ringkasan performa armada rig, keandalan, downtime, dan realisasi pendapatan',
            'bulan'              => $bulan,
            'tahun'              => $tahun,
            'summaries'          => $summaries,
            'totalWellJob'       => $totalWellJob,
            'totalRevenueTarget' => $totalRevenueTarget,
            'totalRevenueActual' => $totalRevenueActual,
            'totalDowntime'      => $totalDowntime,
            'totalMiru'          => $totalMiru,
            'totalOps'           => $totalOps,
            'avgUtil'            => round($avgUtil, 2),
            // Chart data (json-encoded)
            'catLabels'          => json_encode($catLabels),
            'catData'            => json_encode($catData),
            'rigLabels'          => json_encode($rigLabels),
            'rigUtilData'        => json_encode($rigUtilData),
            'rigRevTarget'       => json_encode($rigRevTarget),
            'rigRevActual'       => json_encode($rigRevActual),
            'rigMiruData'        => json_encode($rigMiruData),
            'rigOpsData'         => json_encode($rigOpsData),
            'rigSbwcData'        => json_encode($rigSbwcData),
            'rigUnpaidData'      => json_encode($rigUnpaidData),
            'rigWellData'        => json_encode($rigWellData),
            'nptMonthLabels'     => json_encode($nptMonthLabels),
            'nptUnpaidTrend'     => json_encode($nptUnpaidTrend),
            'nptSbwcTrend'       => json_encode($nptSbwcTrend),
            'nptTotalTrend'      => json_encode($nptTotalTrend),
        ];

        return view('dashboard/index', $data);
    }
}
