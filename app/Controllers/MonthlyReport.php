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

        // Calculate and cache KPIs for all active rigs
        foreach ($rigs as $r) {
            $this->monthlySummaryModel->hitungDanSimpan($r['id'], $bulan, $tahun);
        }

        $summaries = $this->monthlySummaryModel->getByBulanTahun($bulan, $tahun);
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

        foreach ($summaries as $s) {
            $totMiru += (float)($s['total_miru'] ?? 0);
            $totOps += (float)($s['total_ops'] ?? 0);
            $totWell += (int)($s['total_well_job'] ?? 0);
            $totSbwc += (float)($s['sbwc_jam'] ?? 0);
            $totUnpaid += (float)($s['unpaid_jam'] ?? 0);
            $totRevTarget += (float)($s['revenue_target'] ?? 0);
            $totRevActual += (float)($s['revenue_actual'] ?? 0);
            $totJam += (float)($s['total_jam'] ?? 0);
        }

        $count = count($summaries) ?: 1;
        $avgMiruAll = $totWell > 0 ? $totMiru / $totWell : 0;
        $avgCycleTimeAll = $totWell > 0 ? $totOps / $totWell : 0;
        $avgReliability = $totJam > 0 ? max(0, 1 - ($totUnpaid / $totJam)) : 0;
        $avgAvailability = $avgReliability;
        $avgUtilization = $totJam > 0 ? ($totOps / $totJam) : 0;

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
