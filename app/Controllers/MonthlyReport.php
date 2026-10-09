<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\MonthlySummaryModel;
use App\Models\KategoriDowntimeModel;
use App\Models\NptModel;

class MonthlyReport extends BaseController
{
    protected $rigModel;
    protected $monthlySummaryModel;
    protected $kategoriModel;
    protected $nptModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
        $this->monthlySummaryModel = new MonthlySummaryModel();
        $this->kategoriModel = new KategoriDowntimeModel();
        $this->nptModel = new NptModel();
    }

    public function index()
    {
        $bulan = (int)$this->request->getGet('bulan') ?: (int)date('n');
        $tahun = (int)$this->request->getGet('tahun') ?: (int)date('Y');
        return redirect()->to(base_url("monthly-report/{$bulan}/{$tahun}"));
    }

    public function view(int $bulan, int $tahun)
    {
        $rigs = $this->rigModel->getRigAktif();

        // Ambil summary yang sudah terhitung secara instan
        $summaries = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);

        // Hanya hitung otomatis jika ada rig yang belum memiliki record di database
        $hasMissing = false;
        foreach ($summaries as $s) {
            if (empty($s['id'])) {
                $this->monthlySummaryModel->hitungDanSimpan((int)$s['rig_id'], $bulan, $tahun);
                $hasMissing = true;
            }
        }
        if ($hasMissing) {
            $summaries = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);
        }
        $kategoriList = $this->kategoriModel->getKategoriAktif();
        $summaryAllRig = $this->nptModel->getSummaryAllRig($bulan, $tahun);

        // Aggregate Totals across all rigs
        $totMiru = 0;
        $totOps = 0;
        $totWell = 0;
        $totSbwc = 0;
        $totUnpaid = 0;
        $totRevTarget = 0;
        $totRevActual = 0;
        $totJam = 0;

        $activeRigsCount = 0;
        $sumRel = 0;
        $sumAvail = 0;
        $sumUtil = 0;
        $sumAvgMiru = 0;
        $sumAvgCycleTime = 0;

        foreach ($summaries as $s) {
            $rJam = (float)($s['total_jam'] ?? 0);
            $totMiru += (float)($s['total_miru'] ?? 0);
            $totOps += (float)($s['total_ops'] ?? 0);
            $totWell += (int)($s['total_well_job'] ?? 0);
            $totSbwc += (float)($s['sbwc_jam'] ?? 0);
            $totUnpaid += (float)($s['unpaid_jam'] ?? 0);
            $totRevTarget += (float)($s['revenue_target'] ?? 0);
            $totRevActual += (float)($s['revenue_actual'] ?? 0);
            $totJam += $rJam;

            if ($rJam > 0) {
                $activeRigsCount++;
                $sumRel += (float)($s['reliability'] ?? 0);
                $sumAvail += (float)($s['availability'] ?? 0);
                $sumUtil += (float)($s['utilization'] ?? 0);
                $sumAvgMiru += (float)($s['avg_miru'] ?? 0);
                $sumAvgCycleTime += (float)($s['avg_cycle_time'] ?? 0);
            }
        }

        // Formula Excel SUMMARY Row 22: AVERAGE(D5:D21), AVERAGE(E5:E21), AVERAGE(F5:F21)
        $avgReliability = $activeRigsCount > 0 ? ($sumRel / $activeRigsCount) : 0;
        $avgAvailability = $activeRigsCount > 0 ? ($sumAvail / $activeRigsCount) : 0;
        $avgUtilization = $activeRigsCount > 0 ? ($sumUtil / $activeRigsCount) : 0;
        $avgMiruAll = $totWell > 0 ? ($totMiru / $totWell) : 0;
        $avgCycleTimeAll = $totWell > 0 ? ($totJam - ($totWell - $activeRigsCount)) / $totWell : 0;

        $data = [
            'title'            => "Monthly Report - Bulan {$bulan}/{$tahun}",
            'page_title'       => "Monthly Report: Rekapitulasi Operasi Rig BMS",
            'page_subtitle'    => "Analisis KPI Reliabilitas (RAU), MIRU, Cycle Time, NPT Breakdown & Pendapatan",
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'summaries'        => $summaries,
            'kategoriList'     => $kategoriList,
            'summaryAllRig'    => $summaryAllRig,
            'totMiru'          => $totMiru,
            'totOps'           => $totOps,
            'totWell'          => $totWell,
            'totSbwc'          => $totSbwc,
            'totUnpaid'        => $totUnpaid,
            'totRevTarget'     => $totRevTarget,
            'totRevActual'     => $totRevActual,
            'totJam'           => $totJam,
            'avgMiruAll'       => $avgMiruAll,
            'avgCycleTimeAll'  => $avgCycleTimeAll,
            'avgReliability'   => $avgReliability,
            'avgAvailability'  => $avgAvailability,
            'avgUtilization'   => $avgUtilization,
        ];

        return view('monthly_report/view', $data);
    }

    public function hitung()
    {
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');

        $rigs = $this->rigModel->getRigAktif();
        foreach ($rigs as $r) {
            $this->monthlySummaryModel->hitungDanSimpan($r['id'], $bulan, $tahun);
        }

        return redirect()->to(base_url("monthly-report/{$bulan}/{$tahun}"))->with('success', 'Seluruh KPI bulanan berhasil dikalkulasi ulang.');
    }
}
