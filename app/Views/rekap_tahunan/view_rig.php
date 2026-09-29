<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$bulanNames = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$bulanShort = [
    1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
    5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
    9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
];

$indexedMonths = [];
foreach ($months as $m) {
    $indexedMonths[(int)$m['bulan']] = $m;
}

$totMiru      = 0;
$totOps       = 0;
$totWell      = 0;
$totSbwc      = 0;
$totUnpaid    = 0;
$totRevTarget = 0;
$totRevActual = 0;
$totHours     = 0;
$sumRel       = 0;
$sumAva       = 0;
$sumUti       = 0;
$mCount       = 0;

for ($bln = 1; $bln <= 12; $bln++) {
    $m = $indexedMonths[$bln] ?? null;
    if ($m) {
        $totMiru      += (float)($m['total_miru'] ?? 0);
        $totOps       += (float)($m['total_ops'] ?? 0);
        $totWell      += (int)($m['total_well_job'] ?? 0);
        $totSbwc      += (float)($m['sbwc_jam'] ?? 0);
        $totUnpaid    += (float)($m['unpaid_jam'] ?? 0);
        $totRevTarget += (float)($m['revenue_target'] ?? 0);
        $totRevActual += (float)($m['revenue_actual'] ?? 0);
        $totHours     += (float)($m['total_jam'] ?? (cal_days_in_month(CAL_GREGORIAN, $bln, $tahun) * 24));
        $sumRel       += (float)($m['reliability'] ?? 0);
        $sumAva       += (float)($m['availability'] ?? 0);
        $sumUti       += (float)($m['utilization'] ?? 0);
        $mCount++;
    }
}

$avgRel = $mCount > 0 ? ($sumRel / $mCount) * 100 : 0;
$avgAva = $mCount > 0 ? ($sumAva / $mCount) * 100 : 0;
$avgUti = $mCount > 0 ? ($sumUti / $mCount) * 100 : 0;
$totDt  = $totSbwc + $totUnpaid;
$revPct = $totRevTarget > 0 ? round(($totRevActual / $totRevTarget) * 100, 1) : 0;
$avgMiruWell  = $totWell > 0 ? ($totMiru / $totWell) : 0;
$avgCycleWell = $totWell > 0 ? ($totOps / $totWell) : 0;
?>

<style>
    .dr-filter-panel {
        background: var(--card);
        border: 1.5px solid var(--border-strong);
        border-radius: 1rem;
        box-shadow: var(--shadow-card);
    }
    .dr-stat-card {
        background: var(--card);
        border: 1.5px solid var(--border-strong);
        border-radius: 0.875rem;
        box-shadow: var(--shadow-card);
    }
    .dr-filter-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted-foreground);
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    .dr-filter-select {
        width: 100%;
        height: 44px;
        padding: 0 0.875rem;
        border-radius: 0.65rem;
        background: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
    }
    .dr-step-btn {
        height: 44px;
        padding: 0 0.95rem;
        border-radius: 0.65rem;
        background: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
        font-size: 0.82rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .dr-step-btn:hover {
        background: var(--accent);
        border-color: var(--primary);
        color: var(--primary);
    }
    .excel-paper-container {
        background: #ffffff;
        color: #000000;
        border: 2px solid #1e293b;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.28);
    }
    .excel-sheet {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Plus Jakarta Sans', Arial, sans-serif;
        font-size: 11.5px;
        color: #000000;
        background: #ffffff;
    }
    .excel-sheet th,
    .excel-sheet td {
        border: 1px solid #334155;
        padding: 6px 8px;
        line-height: 1.25;
        white-space: nowrap;
    }
    .excel-header-yellow {
        background: #ffff00;
        color: #000000;
        font-weight: 800;
        text-transform: uppercase;
        text-align: center;
    }
    .excel-header-gold {
        background: #ffc000;
        color: #000000;
        font-weight: 800;
        text-transform: uppercase;
        text-align: center;
    }
    .excel-header-blue {
        background: #dbeafe;
        color: #0f172a;
        font-weight: 800;
        text-transform: uppercase;
        text-align: center;
    }
    .excel-row-data:nth-child(even) {
        background-color: #f8fafc;
    }
    .excel-row-data:hover {
        background-color: #fef9c3 !important;
    }
    .excel-footer-yellow {
        background: #ffff00;
        color: #000000;
        font-weight: 800;
    }
    .excel-footer-gold {
        background: #ffc000;
        color: #000000;
        font-weight: 900;
    }
    .font-num {
        font-family: 'JetBrains Mono', monospace;
        font-variant-numeric: tabular-nums;
    }
</style>

<div class="space-y-5 pb-10">

    <!-- ═══ 1. PANEL HEADER & SWITCHER RIG / TAHUN ═══ -->
    <div class="dr-filter-panel p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 mb-4 border-b" style="border-color: var(--border);">
            <div class="flex items-start sm:items-center gap-3.5">
                <a href="<?= base_url("rekap-tahunan/{$tahun}") ?>" class="dr-step-btn !px-3.5" title="Kembali ke Rekap Tahunan Seluruh Rig">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Semua Rig</span>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[11px] font-extrabold tracking-wider uppercase border border-amber-500/25">
                            HISTORI 12 BULAN ARMADA &bull; <?= esc($rig['kode']) ?>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[11px] font-num font-bold border border-emerald-500/25">
                            ODR: Rp <?= number_format((float)$rig['odr'], 0, ',', '.') ?>/Hari
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1" style="color: var(--foreground);">
                        REKAPITULASI 12 BULAN: <?= esc($rig['kode']) ?> (<?= esc($rig['nama_rig']) ?>) &mdash; TAHUN <?= $tahun ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <div class="md:col-span-5">
                <label for="selectRigAnnual" class="dr-filter-label">
                    <i class="fa-solid fa-oil-well text-sky-500"></i>
                    <span>Pilih Armada Rig</span>
                </label>
                <select id="selectRigAnnual" onchange="navigateRigTahun()" class="dr-filter-select">
                    <?php if (!empty($rigs)): foreach ($rigs as $rItem): ?>
                        <option value="<?= $rItem['id'] ?>" <?= $rItem['id'] == $rig['id'] ? 'selected' : '' ?>>
                            <?= esc($rItem['kode']) ?> &mdash; <?= esc($rItem['nama_rig']) ?> (ODR Rp <?= number_format((float)$rItem['odr'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; else: ?>
                        <option value="<?= $rig['id'] ?>"><?= esc($rig['kode']) ?> &mdash; <?= esc($rig['nama_rig']) ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="selectYearRig" class="dr-filter-label">
                    <i class="fa-solid fa-calendar text-amber-500"></i>
                    <span>Pilih Tahun</span>
                </label>
                <select id="selectYearRig" onchange="navigateRigTahun()" class="dr-filter-select font-num">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>Tahun <?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="md:col-span-4">
                <span class="dr-filter-label">
                    <i class="fa-solid fa-bolt text-emerald-500"></i>
                    <span>Navigasi Tahun</span>
                </span>
                <div class="flex items-center gap-2">
                    <a href="<?= base_url("rekap-tahunan/rig/{$rig['id']}/" . ($tahun - 1)) ?>" class="dr-step-btn flex-1">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span><?= $tahun - 1 ?></span>
                    </a>
                    <a href="<?= base_url("rekap-tahunan/rig/{$rig['id']}/" . ($tahun + 1)) ?>" class="dr-step-btn flex-1">
                        <span><?= $tahun + 1 ?></span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 2. EMPAT KARTU KPI RIG 1 TAHUN ═══ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
        <div class="dr-stat-card p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Rata-Rata Reliability</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-500 mt-1 font-num"><?= number_format($avgRel, 2) ?>%</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Availability: <strong class="text-sky-500"><?= number_format($avgAva, 1) ?>%</strong></span>
                <span>Utilization: <strong class="text-indigo-500"><?= number_format($avgUti, 1) ?>%</strong></span>
            </div>
        </div>

        <div class="dr-stat-card p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Realisasi Revenue <?= $tahun ?></span>
                    <div class="text-lg sm:text-xl font-black text-amber-500 mt-1.5 font-num truncate">Rp <?= number_format($totRevActual, 0, ',', '.') ?></div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span class="truncate">Target: <strong>Rp <?= number_format($totRevTarget, 0, ',', '.') ?></strong></span>
                <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-500 font-bold"><?= $revPct ?>%</span>
            </div>
        </div>

        <div class="dr-stat-card p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Total Sumur &amp; Jam Kerja</span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-sky-500"><?= $totWell ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Sumur (12 Bln)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-500 shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>OPS: <strong class="text-emerald-500"><?= number_format($totOps, 1) ?>j</strong></span>
                <span>MIRU: <strong class="text-sky-500"><?= number_format($totMiru, 1) ?>j</strong></span>
            </div>
        </div>

        <div class="dr-stat-card p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Akumulasi Downtime / NPT</span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-rose-500"><?= number_format($totDt, 1) ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Jam</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-500 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>SBWC: <strong class="text-amber-500"><?= number_format($totSbwc, 1) ?>j</strong></span>
                <span>UNPAID: <strong class="text-rose-500"><?= number_format($totUnpaid, 1) ?>j</strong></span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. TABEL RINCIAN 12 BULAN BERGAYA EXCEL ═══ -->
    <div class="excel-paper-container">
        <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-[#ffff00] inline-block"></span>
                <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                    RINCIAN PERFORMA BULANAN (JANUARI &ndash; DESEMBER <?= $tahun ?>) &mdash; <?= esc($rig['kode']) ?> (<?= esc($rig['nama_rig']) ?>)
                </h3>
            </div>
            <span class="text-[11px] text-slate-300 font-num">Klik Daily Report atau Monthly Report pada kolom Aksi untuk membuka detail bulan tersebut</span>
        </div>
        <div class="overflow-x-auto">
            <table class="excel-sheet">
                <thead>
                    <tr>
                        <th rowspan="2" class="excel-header-yellow w-9">NO</th>
                        <th rowspan="2" class="excel-header-yellow text-left">BULAN KALENDER</th>
                        <th colspan="3" class="excel-header-yellow">INDIKATOR RAU (%)</th>
                        <th colspan="4" class="excel-header-yellow">JAM KERJA PRODUKTIF</th>
                        <th rowspan="2" class="excel-header-yellow">WELL<br>JOB</th>
                        <th colspan="2" class="excel-header-yellow">DOWNTIME (JAM)</th>
                        <th colspan="2" class="excel-header-gold">REVENUE BULANAN (RP)</th>
                        <th rowspan="2" class="excel-header-blue">AKSI<br>LAPORAN</th>
                    </tr>
                    <tr>
                        <th class="excel-header-yellow">RELIABILITY</th>
                        <th class="excel-header-yellow">AVAILABILITY</th>
                        <th class="excel-header-yellow">UTILIZATION</th>
                        <th class="excel-header-yellow">MIRU (JAM)</th>
                        <th class="excel-header-yellow">OPS (JAM)</th>
                        <th class="excel-header-yellow">AVG MIRU</th>
                        <th class="excel-header-yellow">CYCLE TIME</th>
                        <th class="excel-header-yellow">SBWC</th>
                        <th class="excel-header-yellow">UNPAID</th>
                        <th class="excel-header-gold">TARGET (RP)</th>
                        <th class="excel-header-gold">ACTUAL (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($bln = 1; $bln <= 12; $bln++):
                        $m = $indexedMonths[$bln] ?? null;
                        $rel = $m ? ((float)$m['reliability'] * 100) : 0;
                        $ava = $m ? ((float)$m['availability'] * 100) : 0;
                        $uti = $m ? ((float)$m['utilization'] * 100) : 0;
                        $miru = $m ? (float)$m['total_miru'] : 0;
                        $ops  = $m ? (float)$m['total_ops'] : 0;
                        $avgM = $m ? (float)$m['avg_miru'] : 0;
                        $avgC = $m ? (float)$m['avg_cycle_time'] : 0;
                        $well = $m ? (int)$m['total_well_job'] : 0;
                        $sbwc = $m ? (float)$m['sbwc_jam'] : 0;
                        $unp  = $m ? (float)$m['unpaid_jam'] : 0;
                        $revT = $m ? (float)$m['revenue_target'] : 0;
                        $revA = $m ? (float)$m['revenue_actual'] : 0;

                        $relClass = $rel < 95 ? 'text-[#c00000] font-bold bg-[#fee2e2]' : 'text-[#006100] font-bold';
                        $avaClass = $ava < 95 ? 'text-[#c00000] font-bold bg-[#fee2e2]' : 'text-[#006100] font-bold';
                        $unpClass = $unp > 0 ? 'text-[#c00000] font-extrabold bg-[#fee2e2]' : 'text-slate-600';
                    ?>
                    <tr class="excel-row-data">
                        <td class="text-center font-num font-bold text-slate-700"><?= $bln ?></td>
                        <td class="text-left font-black text-[#0f172a]"><?= $bulanNames[$bln] ?> <?= $tahun ?></td>
                        <td class="text-right font-num <?= $relClass ?>"><?= number_format($rel, 2) ?>%</td>
                        <td class="text-right font-num <?= $avaClass ?>"><?= number_format($ava, 2) ?>%</td>
                        <td class="text-right font-num font-bold"><?= number_format($uti, 2) ?>%</td>
                        <td class="text-right font-num"><?= number_format($miru, 2) ?></td>
                        <td class="text-right font-num font-bold"><?= number_format($ops, 2) ?></td>
                        <td class="text-right font-num"><?= number_format($avgM, 2) ?></td>
                        <td class="text-right font-num"><?= number_format($avgC, 2) ?></td>
                        <td class="text-center font-num font-black text-blue-900 bg-blue-50/70"><?= $well ?></td>
                        <td class="text-right font-num"><?= number_format($sbwc, 2) ?></td>
                        <td class="text-right font-num <?= $unpClass ?>"><?= number_format($unp, 2) ?></td>
                        <td class="text-right font-num text-slate-700">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($revT, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-right font-num font-extrabold text-[#006100] bg-emerald-50/70">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($revA, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-center">
                            <div class="inline-flex items-center gap-1">
                                <a href="<?= base_url("daily-report/{$rig['id']}/{$bln}/{$tahun}") ?>"
                                   class="px-2 py-0.5 rounded bg-blue-700 hover:bg-blue-800 text-white font-bold text-[10px]"
                                   title="Buka Daily Report <?= esc($rig['kode']) ?> - <?= $bulanNames[$bln] ?>">
                                    Daily
                                </a>
                                <a href="<?= base_url("monthly-report/{$bln}/{$tahun}") ?>"
                                   class="px-2 py-0.5 rounded bg-slate-700 hover:bg-slate-800 text-white font-bold text-[10px]"
                                   title="Buka Monthly Report <?= $bulanNames[$bln] ?>">
                                    Monthly
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr class="excel-footer-yellow font-num">
                        <td colspan="2" class="text-center font-black">RATA-RATA BULANAN</td>
                        <td class="text-right"><?= number_format($avgRel, 2) ?>%</td>
                        <td class="text-right"><?= number_format($avgAva, 2) ?>%</td>
                        <td class="text-right"><?= number_format($avgUti, 2) ?>%</td>
                        <td class="text-right"><?= number_format($totMiru / 12, 2) ?></td>
                        <td class="text-right"><?= number_format($totOps / 12, 2) ?></td>
                        <td class="text-right"><?= number_format($avgMiruWell, 2) ?></td>
                        <td class="text-right"><?= number_format($avgCycleWell, 2) ?></td>
                        <td class="text-center"><?= number_format($totWell / 12, 1) ?></td>
                        <td class="text-right"><?= number_format($totSbwc / 12, 2) ?></td>
                        <td class="text-right"><?= number_format($totUnpaid / 12, 2) ?></td>
                        <td class="text-right">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($totRevTarget / 12, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($totRevActual / 12, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-center text-[10px]">AVG</td>
                    </tr>
                    <tr class="excel-footer-gold font-num">
                        <td colspan="2" class="text-center font-black">TOTAL 1 TAHUN (<?= $tahun ?>)</td>
                        <td colspan="3" class="text-center bg-[#d9d9d9]">12 BULAN</td>
                        <td class="text-right"><?= number_format($totMiru, 2) ?></td>
                        <td class="text-right"><?= number_format($totOps, 2) ?></td>
                        <td class="text-center bg-[#d9d9d9]">&mdash;</td>
                        <td class="text-center bg-[#d9d9d9]">&mdash;</td>
                        <td class="text-center text-sm"><?= $totWell ?></td>
                        <td class="text-right"><?= number_format($totSbwc, 2) ?></td>
                        <td class="text-right text-[#c00000]"><?= number_format($totUnpaid, 2) ?></td>
                        <td class="text-right">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($totRevTarget, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($totRevActual, 0, ',', '.') ?></span></div>
                        </td>
                        <td class="text-center text-[10px] font-black"><?= $revPct ?>%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<script>
    function navigateRigTahun() {
        const rigId = document.getElementById('selectRigAnnual').value;
        const tahun = document.getElementById('selectYearRig').value;
        window.location.href = `<?= base_url('rekap-tahunan/rig') ?>/${rigId}/${tahun}`;
    }
</script>
<?= $this->endSection() ?>
