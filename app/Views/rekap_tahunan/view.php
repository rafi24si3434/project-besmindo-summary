<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$bulanShort = [
    1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
    5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
    9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
];
$bulanFull = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

/* ═══ Pre-compute Annual Data per Rig + Monthly Fleet Aggregates ═══ */
$grandWellJob   = 0;
$grandRevTarget = 0;
$grandRevActual = 0;
$grandMiru      = 0;
$grandOps       = 0;
$grandSbwc      = 0;
$grandUnpaid    = 0;
$grandHoursAll  = 0;

$sumRelAll  = 0;
$sumAvaAll  = 0;
$sumUtilAll = 0;
$rigCount   = 0;

$monthlyFleetAgg = [];
for ($m = 1; $m <= 12; $m++) {
    $monthlyFleetAgg[$m] = [
        'well'       => 0,
        'rev_target' => 0,
        'rev_actual' => 0,
        'miru'       => 0,
        'ops'        => 0,
        'sbwc'       => 0,
        'unpaid'     => 0,
        'rel_sum'    => 0,
        'ava_sum'    => 0,
        'uti_sum'    => 0,
        'rig_cnt'    => 0,
    ];
}

$tableRows = [];
foreach ($annualData as $rigId => $item) {
    $r = $item['rig'];
    $monthsRaw = $item['months'] ?? [];
    $monthsByNum = [];
    foreach ($monthsRaw as $mRow) {
        $bNum = (int)($mRow['bulan'] ?? 0);
        if ($bNum >= 1 && $bNum <= 12) {
            $monthsByNum[$bNum] = $mRow;
        }
    }

    $sumWellJob   = 0;
    $sumRevTarget = 0;
    $sumRevActual = 0;
    $sumMiru      = 0;
    $sumOps       = 0;
    $sumSbwc      = 0;
    $sumUnpaid    = 0;
    $sumHours     = 0;
    $sumRel       = 0;
    $sumAva       = 0;
    $sumUtil      = 0;
    $mCount       = 0;

    for ($m = 1; $m <= 12; $m++) {
        $rowM = $monthsByNum[$m] ?? null;
        $w  = (int)($rowM['total_well_job'] ?? 0);
        $rt = (float)($rowM['revenue_target'] ?? 0);
        $ra = (float)($rowM['revenue_actual'] ?? 0);
        $mi = (float)($rowM['total_miru'] ?? 0);
        $op = (float)($rowM['total_ops'] ?? 0);
        $sb = (float)($rowM['sbwc_jam'] ?? 0);
        $un = (float)($rowM['unpaid_jam'] ?? 0);
        $tj = (float)($rowM['total_jam'] ?? (cal_days_in_month(CAL_GREGORIAN, $m, $tahun) * 24));
        $rl = (float)($rowM['reliability'] ?? 0);
        $av = (float)($rowM['availability'] ?? 0);
        $ut = (float)($rowM['utilization'] ?? 0);

        $sumWellJob   += $w;
        $sumRevTarget += $rt;
        $sumRevActual += $ra;
        $sumMiru      += $mi;
        $sumOps       += $op;
        $sumSbwc      += $sb;
        $sumUnpaid    += $un;
        $sumHours     += $tj;
        $sumRel       += $rl;
        $sumAva       += $av;
        $sumUtil      += $ut;
        $mCount++;

        $monthlyFleetAgg[$m]['well']       += $w;
        $monthlyFleetAgg[$m]['rev_target'] += $rt;
        $monthlyFleetAgg[$m]['rev_actual'] += $ra;
        $monthlyFleetAgg[$m]['miru']       += $mi;
        $monthlyFleetAgg[$m]['ops']        += $op;
        $monthlyFleetAgg[$m]['sbwc']       += $sb;
        $monthlyFleetAgg[$m]['unpaid']     += $un;
        $monthlyFleetAgg[$m]['rel_sum']    += $rl;
        $monthlyFleetAgg[$m]['ava_sum']    += $av;
        $monthlyFleetAgg[$m]['uti_sum']    += $ut;
        $monthlyFleetAgg[$m]['rig_cnt']++;
    }

    $avgRel  = $mCount > 0 ? ($sumRel / $mCount) * 100 : 0;
    $avgAva  = $mCount > 0 ? ($sumAva / $mCount) * 100 : 0;
    $avgUtil = $mCount > 0 ? ($sumUtil / $mCount) * 100 : 0;
    $sumDt   = $sumSbwc + $sumUnpaid;
    $avgMiruWell  = $sumWellJob > 0 ? ($sumMiru / $sumWellJob) : 0;
    $avgCycleWell = $sumWellJob > 0 ? ($sumOps / $sumWellJob) : 0;
    $revPctRig    = $sumRevTarget > 0 ? round(($sumRevActual / $sumRevTarget) * 100, 1) : 0;

    $grandWellJob   += $sumWellJob;
    $grandRevTarget += $sumRevTarget;
    $grandRevActual += $sumRevActual;
    $grandMiru      += $sumMiru;
    $grandOps       += $sumOps;
    $grandSbwc      += $sumSbwc;
    $grandUnpaid    += $sumUnpaid;
    $grandHoursAll  += $sumHours;

    $sumRelAll  += ($avgRel / 100);
    $sumAvaAll  += ($avgAva / 100);
    $sumUtilAll += ($avgUtil / 100);
    $rigCount++;

    $tableRows[] = [
        'r'             => $r,
        'monthsByNum'   => $monthsByNum,
        'sumWellJob'    => $sumWellJob,
        'sumRevTarget'  => $sumRevTarget,
        'sumRevActual'  => $sumRevActual,
        'revPctRig'     => $revPctRig,
        'sumMiru'       => $sumMiru,
        'sumOps'        => $sumOps,
        'sumSbwc'       => $sumSbwc,
        'sumUnpaid'     => $sumUnpaid,
        'sumDt'         => $sumDt,
        'avgRel'        => $avgRel,
        'avgAva'        => $avgAva,
        'avgUtil'       => $avgUtil,
        'avgMiruWell'   => $avgMiruWell,
        'avgCycleWell'  => $avgCycleWell,
    ];
}

$avgReliability  = $rigCount > 0 ? ($sumRelAll / $rigCount) * 100 : 0;
$avgAvailability = $rigCount > 0 ? ($sumAvaAll / $rigCount) * 100 : 0;
$avgUtilization  = $rigCount > 0 ? ($sumUtilAll / $rigCount) * 100 : 0;
$grandDt         = $grandSbwc + $grandUnpaid;
$grandRevPct     = $grandRevTarget > 0 ? round(($grandRevActual / $grandRevTarget) * 100, 1) : 0;
$grandProdPct    = $grandHoursAll > 0 ? round((($grandOps + $grandMiru) / $grandHoursAll) * 100, 1) : 0;
$grandDtPct      = $grandHoursAll > 0 ? round(($grandDt / $grandHoursAll) * 100, 2) : 0;
$grandAvgMiru    = $grandWellJob > 0 ? ($grandMiru / $grandWellJob) : 0;
$grandAvgCycle   = $grandWellJob > 0 ? ($grandOps / $grandWellJob) : 0;

/* Top Performers for Leaderboard Strip */
$sortedByRev  = $tableRows;
usort($sortedByRev, fn($a, $b) => $b['sumRevActual'] <=> $a['sumRevActual']);
$topRevRig = $sortedByRev[0] ?? null;

$sortedByWell = $tableRows;
usort($sortedByWell, fn($a, $b) => $b['sumWellJob'] <=> $a['sumWellJob']);
$topWellRig = $sortedByWell[0] ?? null;

$sortedByRel  = $tableRows;
usort($sortedByRel, fn($a, $b) => $b['avgRel'] <=> $a['avgRel']);
$topRelRig = $sortedByRel[0] ?? null;
?>

<style>
    /* ═══ Panel & Card Surface Styling (Selaras dengan Daily & Monthly Report) ═══ */
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
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .dr-stat-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-elevated);
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
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .dr-filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--ring);
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
        transition: all 0.15s ease;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
    }
    .dr-step-btn:hover {
        background: var(--accent);
        border-color: var(--primary);
        color: var(--primary);
    }
    .dr-action-primary {
        height: 44px;
        padding: 0 1.25rem;
        border-radius: 0.65rem;
        font-size: 0.85rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: #ffffff !important;
        border: 1.5px solid #166534;
        box-shadow: 0 3px 10px rgba(22, 163, 74, 0.25);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .dr-action-primary:hover {
        background: linear-gradient(135deg, #15803d 0%, #14532d 100%);
        transform: translateY(-1px);
    }
    .dr-action-secondary {
        height: 44px;
        padding: 0 1.1rem;
        border-radius: 0.65rem;
        font-size: 0.85rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: var(--background);
        color: var(--foreground);
        border: 1.5px solid var(--border-strong);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .dr-action-secondary:hover {
        background: var(--accent);
        border-color: var(--primary);
        color: var(--primary);
    }
    /* Tab Switcher Pill Group */
    .mr-segmented-group {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        background: var(--background);
        border: 1.5px solid var(--border-strong);
        padding: 0.35rem;
        border-radius: 0.85rem;
    }
    .mr-tab-pill {
        padding: 0.55rem 1rem;
        border-radius: 0.6rem;
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--muted-foreground);
        background: transparent;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .mr-tab-pill:hover {
        color: var(--foreground);
        background: var(--accent);
    }
    .mr-tab-pill.active {
        background: var(--card);
        color: var(--foreground);
        border-color: var(--border-strong);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1);
    }

    /* ═══ Excel Sheet Replica Table ═══ */
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

    <!-- ═══ 1. PANEL KONTROL & FILTER REKAP TAHUNAN ═══ -->
    <div class="dr-filter-panel p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 mb-4 border-b" style="border-color: var(--border);">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0 shadow-sm">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[11px] font-extrabold tracking-wider uppercase border border-amber-500/25">
                            ANNUAL FLEET INTELLIGENCE &bull; JANUARI – DESEMBER <?= $tahun ?>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 text-[11px] font-bold border border-sky-500/25">
                            <i class="fa-solid fa-oil-well mr-1"></i><?= $rigCount ?> Unit Rig Aktif
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold border border-emerald-500/25">
                            <i class="fa-solid fa-clock mr-1"></i>12 Bulan Kalender
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1" style="color: var(--foreground);">
                        REKAPITULASI TAHUNAN OPERASI &amp; REVENUE ARMADA RIG &mdash; TAHUN <?= $tahun ?>
                    </h2>
                    <p class="text-xs sm:text-sm mt-0.5" style="color: var(--muted-foreground);">
                        Akumulasi kinerja 12 bulan penuh (RAU, MIRU, Operasi, Well Job, SBWC/Unpaid NPT, dan Realisasi Pendapatan ODR) seluruh armada.
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Cepat -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="<?= base_url("rekap-tahunan/npt/{$tahun}") ?>" class="dr-action-secondary" title="Lihat Matriks NPT Tahunan">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Pusat NPT Tahunan</span>
                </a>
                <a href="<?= base_url("export/rekap-tahunan/{$tahun}") ?>" class="dr-action-primary" title="Download Excel Rekap Tahunan <?= $tahun ?>">
                    <i class="fa-solid fa-file-excel text-base"></i>
                    <span>Download Excel <?= $tahun ?></span>
                </a>
            </div>
        </div>

        <!-- Baris Kontrol Filter Tahun, Filter Performa & Navigasi Cepat -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Pilih Tahun -->
            <div class="md:col-span-3">
                <label for="selectTahun" class="dr-filter-label">
                    <i class="fa-solid fa-calendar text-amber-500"></i>
                    <span>Pilih Tahun Kalender</span>
                </label>
                <select id="selectTahun" onchange="navigateTahun()" class="dr-filter-select font-num">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>Tahun Kalender <?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Filter Kategori Utilitas Armada -->
            <div class="md:col-span-4">
                <label for="filterUtilLevel" class="dr-filter-label">
                    <i class="fa-solid fa-filter text-sky-500"></i>
                    <span>Filter Tingkat Utilitas Armada</span>
                </label>
                <select id="filterUtilLevel" onchange="filterAnnualRigRows()" class="dr-filter-select">
                    <option value="all">Semua Armada Rig (<?= $rigCount ?> Unit)</option>
                    <option value="high">Utilitas Tinggi (&ge; 50%)</option>
                    <option value="mid">Utilitas Menengah (20% &ndash; 49%)</option>
                    <option value="low">Utilitas Rendah (&lt; 20%)</option>
                </select>
            </div>

            <!-- Navigasi Cepat Tahun -->
            <div class="md:col-span-5">
                <span class="dr-filter-label">
                    <i class="fa-solid fa-bolt text-emerald-500"></i>
                    <span>Navigasi Cepat Periode Tahunan</span>
                </span>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="<?= base_url('rekap-tahunan/' . ($tahun - 1)) ?>" class="dr-step-btn flex-1">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span>Tahun <?= $tahun - 1 ?></span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan/' . date('Y')) ?>" class="dr-step-btn" title="Kembali ke Tahun Berjalan">
                        <i class="fa-solid fa-rotate-left text-amber-500"></i>
                        <span>Tahun Ini</span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan/' . ($tahun + 1)) ?>" class="dr-step-btn flex-1">
                        <span>Tahun <?= $tahun + 1 ?></span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 2. EMPAT KARTU RINGKASAN KPI TAHUNAN (EXECUTIVE METRICS) ═══ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
        <!-- Card 1: Rata-rata RAU Armada Tahunan -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Rata-Rata Reliability <?= $tahun ?>
                    </span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-500 mt-1 font-num">
                        <?= number_format($avgReliability, 2) ?>%
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Availability: <strong class="text-sky-500"><?= number_format($avgAvailability, 1) ?>%</strong></span>
                <span>Utilization: <strong class="text-indigo-500"><?= number_format($avgUtilization, 1) ?>%</strong></span>
            </div>
        </div>

        <!-- Card 2: Total Realisasi Revenue Tahunan vs Target -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Realisasi Revenue <?= $tahun ?>
                    </span>
                    <div class="text-lg sm:text-xl font-black text-amber-500 mt-1.5 font-num truncate" title="Rp <?= number_format($grandRevActual, 0, ',', '.') ?>">
                        Rp <?= number_format($grandRevActual, 0, ',', '.') ?>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span class="truncate">Target: <strong>Rp <?= number_format($grandRevTarget, 0, ',', '.') ?></strong></span>
                <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-500 font-bold"><?= $grandRevPct ?>%</span>
            </div>
        </div>

        <!-- Card 3: Total Sumur & Jam Produktif 1 Tahun -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Total Sumur &amp; Jam Produktif
                    </span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-sky-500">
                            <?= number_format($grandWellJob, 0, ',', '.') ?>
                        </span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Sumur (Well Job)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-500 shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>OPS: <strong class="text-emerald-500"><?= number_format($grandOps, 1) ?>j</strong></span>
                <span>MIRU: <strong class="text-sky-500"><?= number_format($grandMiru, 1) ?>j</strong> (<?= $grandProdPct ?>%)</span>
            </div>
        </div>

        <!-- Card 4: Total Downtime Tahunan (SBWC & Unpaid) -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Total Downtime / NPT <?= $tahun ?>
                    </span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-rose-500">
                            <?= number_format($grandDt, 1) ?>
                        </span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Jam (<?= $grandDtPct ?>%)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-500 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>SBWC: <strong class="text-amber-500"><?= number_format($grandSbwc, 1) ?>j</strong></span>
                <span>UNPAID: <strong class="text-rose-500"><?= number_format($grandUnpaid, 1) ?>j</strong></span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. BARIS TAB MODE TAMPILAN & PENCARIAN CEPAT RIG ═══ -->
    <div class="dr-filter-panel p-3.5 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="mr-segmented-group">
            <button type="button" onclick="switchRekapTab('table')" id="rekapTabBtn_table"
                class="mr-tab-pill active">
                <i class="fa-solid fa-table text-amber-500"></i>
                <span>1. Ringkasan Tahunan Per Rig (Excel)</span>
            </button>
            <button type="button" onclick="switchRekapTab('matrix')" id="rekapTabBtn_matrix"
                class="mr-tab-pill">
                <i class="fa-solid fa-calendar-days text-emerald-500"></i>
                <span>2. Matriks 12 Bulan (Jan &ndash; Des)</span>
            </button>
            <button type="button" onclick="switchRekapTab('charts')" id="rekapTabBtn_charts"
                class="mr-tab-pill">
                <i class="fa-solid fa-chart-column text-sky-500"></i>
                <span>3. Grafik Analisis Tahunan (5 Chart)</span>
            </button>
            <a href="<?= base_url("rekap-tahunan/npt/{$tahun}") ?>"
                class="mr-tab-pill">
                <i class="fa-solid fa-clock-rotate-left text-rose-500"></i>
                <span>4. Rincian NPT Tahunan</span>
            </a>
        </div>

        <!-- Pencarian Cepat Kode / Nama Rig -->
        <div class="relative w-full lg:w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs pointer-events-none" style="color: var(--muted-foreground);"></i>
            <input type="text" id="searchAnnualRigInput" oninput="filterAnnualRigRows()"
                placeholder="Cari kode / nama rig (misal: BMS#15)..."
                class="dr-filter-select w-full !pl-9 !pr-8 !py-2 text-xs">
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: RINGKASAN TAHUNAN PER RIG (EXCEL EXECUTIVE REPLICA)               -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="rekapTab_table" class="space-y-4">

        <!-- Sorotan 3 Armada Terbaik Tahun Ini -->
        <?php if ($topRevRig && $topWellRig && $topRelRig): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="dr-stat-card p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0 font-bold">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Kontributor Revenue Tertinggi</span>
                        <div class="text-sm font-black truncate" style="color: var(--foreground);">
                            <?= esc($topRevRig['r']['kode']) ?> <span class="text-xs font-semibold" style="color: var(--muted-foreground);">(<?= esc($topRevRig['r']['nama_rig']) ?>)</span>
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 font-num font-extrabold text-xs shrink-0">
                    Rp <?= number_format($topRevRig['sumRevActual'] / 1000000, 1, ',', '.') ?> Jt
                </span>
            </div>

            <div class="dr-stat-card p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-500 shrink-0 font-bold">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Penyelesaian Sumur Terbanyak</span>
                        <div class="text-sm font-black truncate" style="color: var(--foreground);">
                            <?= esc($topWellRig['r']['kode']) ?> <span class="text-xs font-semibold" style="color: var(--muted-foreground);">(<?= esc($topWellRig['r']['nama_rig']) ?>)</span>
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-num font-extrabold text-xs shrink-0">
                    <?= $topWellRig['sumWellJob'] ?> Sumur
                </span>
            </div>

            <div class="dr-stat-card p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0 font-bold">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">Reliability Terbaik</span>
                        <div class="text-sm font-black truncate" style="color: var(--foreground);">
                            <?= esc($topRelRig['r']['kode']) ?> <span class="text-xs font-semibold" style="color: var(--muted-foreground);">(<?= esc($topRelRig['r']['nama_rig']) ?>)</span>
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-num font-extrabold text-xs shrink-0">
                    <?= number_format($topRelRig['avgRel'], 2) ?>%
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabel Rekapitulasi Tahunan Bergaya Excel -->
        <div class="excel-paper-container">
            <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#ffff00] inline-block"></span>
                    <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                        ANNUAL SUMMARY OPERATION &amp; REVENUE &mdash; PT BESMINDO MATERI SEWAKOTTAMA (TAHUN <?= $tahun ?>)
                    </h3>
                </div>
                <div class="flex items-center gap-2 text-[11px] text-slate-300">
                    <span><i class="fa-solid fa-hand-pointer text-amber-400 mr-1"></i>Klik <strong>Rincian 12B</strong> pada baris rig untuk melihat detail Januari &ndash; Desember</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th rowspan="2" class="excel-header-yellow w-9">NO</th>
                            <th rowspan="2" class="excel-header-yellow">RIG</th>
                            <th rowspan="2" class="excel-header-yellow text-left">NAMA ARMADA &amp; ODR</th>
                            <th colspan="3" class="excel-header-yellow">RATA-RATA RAU (1 TAHUN)</th>
                            <th colspan="4" class="excel-header-yellow">AKUMULASI JAM PRODUKTIF</th>
                            <th rowspan="2" class="excel-header-yellow">TOTAL<br>WELL JOB</th>
                            <th colspan="2" class="excel-header-yellow">AKUMULASI DOWNTIME</th>
                            <th colspan="2" class="excel-header-gold">REVENUE AKUMULASI (1 TAHUN)</th>
                            <th rowspan="2" class="excel-header-blue">AKSI<br>DETAIL</th>
                        </tr>
                        <tr>
                            <th class="excel-header-yellow">RELIABILITY</th>
                            <th class="excel-header-yellow">AVAILABILITY</th>
                            <th class="excel-header-yellow">UTILIZATION</th>
                            <th class="excel-header-yellow">MIRU (JAM)</th>
                            <th class="excel-header-yellow">OPS (JAM)</th>
                            <th class="excel-header-yellow">AVG MIRU</th>
                            <th class="excel-header-yellow">CYCLE TIME</th>
                            <th class="excel-header-yellow">SBWC (JAM)</th>
                            <th class="excel-header-yellow">UNPAID (JAM)</th>
                            <th class="excel-header-gold">TARGET (RP)</th>
                            <th class="excel-header-gold">ACTUAL (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($tableRows as $row):
                            $r = $row['r'];
                            $utilCat = $row['avgUtil'] >= 50 ? 'high' : ($row['avgUtil'] >= 20 ? 'mid' : 'low');
                            $relClass = $row['avgRel'] < 95 ? 'text-[#c00000] font-bold bg-[#fee2e2]' : 'text-[#006100] font-bold';
                            $avaClass = $row['avgAva'] < 95 ? 'text-[#c00000] font-bold bg-[#fee2e2]' : 'text-[#006100] font-bold';
                            $utiClass = $row['avgUtil'] >= 50 ? 'text-[#006100] font-bold bg-[#dcfce7]' : ($row['avgUtil'] >= 20 ? 'text-[#92400e] font-bold bg-[#fef3c7]' : 'text-[#c00000] font-bold bg-[#fee2e2]');
                            $unpClass = $row['sumUnpaid'] > 0 ? 'text-[#c00000] font-extrabold bg-[#fee2e2]' : 'text-slate-600';
                        ?>
                        <tr class="excel-row-data annual-rig-row"
                            data-rig="<?= esc(strtolower($r['kode'] . ' ' . $r['nama_rig'])) ?>"
                            data-util="<?= $utilCat ?>">
                            <td class="text-center font-num font-bold text-slate-700"><?= $no++ ?></td>
                            <td class="text-center font-num font-black text-[#0f172a] bg-slate-100">
                                <a href="<?= base_url("rekap-tahunan/rig/{$r['id']}/{$tahun}") ?>"
                                   class="hover:underline text-blue-800" title="Buka Rekap 12 Bulan <?= esc($r['kode']) ?>">
                                    <?= esc($r['kode']) ?>
                                </a>
                            </td>
                            <td class="text-left">
                                <div class="font-bold text-[#0f172a]"><?= esc($r['nama_rig']) ?></div>
                                <div class="text-[10px] font-num text-slate-600">ODR: Rp <?= number_format((float)$r['odr'], 0, ',', '.') ?>/hari</div>
                            </td>
                            <td class="text-right font-num <?= $relClass ?>"><?= number_format($row['avgRel'], 2) ?>%</td>
                            <td class="text-right font-num <?= $avaClass ?>"><?= number_format($row['avgAva'], 2) ?>%</td>
                            <td class="text-right font-num <?= $utiClass ?>"><?= number_format($row['avgUtil'], 2) ?>%</td>
                            <td class="text-right font-num"><?= number_format($row['sumMiru'], 2) ?></td>
                            <td class="text-right font-num font-bold"><?= number_format($row['sumOps'], 2) ?></td>
                            <td class="text-right font-num"><?= number_format($row['avgMiruWell'], 2) ?></td>
                            <td class="text-right font-num"><?= number_format($row['avgCycleWell'], 2) ?></td>
                            <td class="text-center font-num font-black text-blue-900 bg-blue-50/70 text-xs"><?= $row['sumWellJob'] ?></td>
                            <td class="text-right font-num"><?= number_format($row['sumSbwc'], 2) ?></td>
                            <td class="text-right font-num <?= $unpClass ?>"><?= number_format($row['sumUnpaid'], 2) ?></td>
                            <td class="text-right font-num text-slate-700">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($row['sumRevTarget'], 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right font-num font-extrabold text-[#006100] bg-emerald-50/70">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($row['sumRevActual'], 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url("rekap-tahunan/rig/{$r['id']}/{$tahun}") ?>"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-blue-700 hover:bg-blue-800 text-white font-bold text-[10.5px] shadow-sm transition"
                                   title="Lihat Rincian 12 Bulan <?= esc($r['kode']) ?>">
                                    <i class="fa-solid fa-calendar-week"></i>
                                    <span>Rincian 12B</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="excel-footer-yellow font-num">
                            <td colspan="3" class="text-center font-black">RATA-RATA ARMADA (<?= $rigCount ?> RIG)</td>
                            <td class="text-right"><?= number_format($avgReliability, 2) ?>%</td>
                            <td class="text-right"><?= number_format($avgAvailability, 2) ?>%</td>
                            <td class="text-right"><?= number_format($avgUtilization, 2) ?>%</td>
                            <td class="text-right"><?= $rigCount > 0 ? number_format($grandMiru / $rigCount, 2) : '0.00' ?></td>
                            <td class="text-right"><?= $rigCount > 0 ? number_format($grandOps / $rigCount, 2) : '0.00' ?></td>
                            <td class="text-right"><?= number_format($grandAvgMiru, 2) ?></td>
                            <td class="text-right"><?= number_format($grandAvgCycle, 2) ?></td>
                            <td class="text-center"><?= $rigCount > 0 ? number_format($grandWellJob / $rigCount, 1) : '0' ?></td>
                            <td class="text-right"><?= $rigCount > 0 ? number_format($grandSbwc / $rigCount, 2) : '0.00' ?></td>
                            <td class="text-right"><?= $rigCount > 0 ? number_format($grandUnpaid / $rigCount, 2) : '0.00' ?></td>
                            <td class="text-right">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= $rigCount > 0 ? number_format($grandRevTarget / $rigCount, 0, ',', '.') : '0' ?></span></div>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= $rigCount > 0 ? number_format($grandRevActual / $rigCount, 0, ',', '.') : '0' ?></span></div>
                            </td>
                            <td class="text-center text-[10px]">AVG</td>
                        </tr>
                        <tr class="excel-footer-gold font-num">
                            <td colspan="3" class="text-center font-black">GRAND TOTAL AKUMULASI <?= $tahun ?></td>
                            <td colspan="3" class="text-center bg-[#d9d9d9]">12 BULAN KALENDER</td>
                            <td class="text-right"><?= number_format($grandMiru, 2) ?></td>
                            <td class="text-right"><?= number_format($grandOps, 2) ?></td>
                            <td class="text-center bg-[#d9d9d9]">&mdash;</td>
                            <td class="text-center bg-[#d9d9d9]">&mdash;</td>
                            <td class="text-center text-sm"><?= number_format($grandWellJob, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($grandSbwc, 2) ?></td>
                            <td class="text-right text-[#c00000]"><?= number_format($grandUnpaid, 2) ?></td>
                            <td class="text-right">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($grandRevTarget, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-between gap-1.5"><span>Rp</span><span><?= number_format($grandRevActual, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-center text-[10px] font-black"><?= $grandRevPct ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: MATRIKS 12 BULAN (JANUARI – DESEMBER) PER RIG                     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="rekapTab_matrix" class="hidden space-y-4">
        <div class="dr-filter-panel p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h4 class="text-sm font-black uppercase tracking-wider" style="color: var(--foreground);">
                    <i class="fa-solid fa-calendar-days text-emerald-500 mr-1.5"></i>
                    MATRIKS HISTORIS 12 BULAN (JANUARI &ndash; DESEMBER <?= $tahun ?>)
                </h4>
                <p class="text-xs mt-0.5" style="color: var(--muted-foreground);">
                    Pilih indikator di sebelah kanan untuk membandingkan tren bulanan setiap rig. Klik nama bulan di kepala tabel untuk membuka Monthly Report bulan tersebut.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <label for="matrixMetricSelect" class="text-xs font-extrabold uppercase" style="color: var(--muted-foreground);">Metrik:</label>
                <select id="matrixMetricSelect" onchange="changeMatrixMetric(this.value)" class="dr-filter-select !w-auto !h-10 !text-xs">
                    <option value="well">Jumlah Well Job (Sumur)</option>
                    <option value="rev">Revenue Actual (Juta Rp)</option>
                    <option value="ops">Jam Operasi / OPS (Jam)</option>
                    <option value="util">Utilization Rate (%)</option>
                    <option value="dt">Total Downtime / NPT (Jam)</option>
                </select>
            </div>
        </div>

        <div class="excel-paper-container">
            <div class="px-4 py-2.5 bg-[#0f172a] text-white flex items-center justify-between border-b border-[#334155]">
                <span class="text-xs font-black uppercase tracking-wider" id="matrixTitleBanner">
                    MATRIKS JUMLAH WELL JOB (SUMUR) BULANAN &mdash; TAHUN <?= $tahun ?>
                </span>
                <span class="text-[11px] text-amber-300 font-num">Klik header JAN&ndash;DES untuk menuju Monthly Report</span>
            </div>
            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th class="excel-header-yellow w-9">NO</th>
                            <th class="excel-header-yellow">RIG</th>
                            <th class="excel-header-yellow text-left">NAMA ARMADA</th>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <th class="excel-header-blue">
                                    <a href="<?= base_url("monthly-report/{$m}/{$tahun}") ?>"
                                       class="hover:underline text-blue-900 block"
                                       title="Buka Monthly Report <?= $bulanFull[$m] ?> <?= $tahun ?>">
                                        <?= $bulanShort[$m] ?>
                                    </a>
                                </th>
                            <?php endfor; ?>
                            <th class="excel-header-gold">TOTAL / AVG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $noM = 1; foreach ($tableRows as $row):
                            $r = $row['r'];
                            $utilCat = $row['avgUtil'] >= 50 ? 'high' : ($row['avgUtil'] >= 20 ? 'mid' : 'low');
                        ?>
                        <tr class="excel-row-data annual-rig-row"
                            data-rig="<?= esc(strtolower($r['kode'] . ' ' . $r['nama_rig'])) ?>"
                            data-util="<?= $utilCat ?>">
                            <td class="text-center font-num font-bold text-slate-700"><?= $noM++ ?></td>
                            <td class="text-center font-num font-black text-[#0f172a] bg-slate-100"><?= esc($r['kode']) ?></td>
                            <td class="text-left font-semibold text-[#0f172a]"><?= esc($r['nama_rig']) ?></td>
                            <?php for ($m = 1; $m <= 12; $m++):
                                $mRow = $row['monthsByNum'][$m] ?? null;
                                $valWell = (int)($mRow['total_well_job'] ?? 0);
                                $valRev  = round(((float)($mRow['revenue_actual'] ?? 0)) / 1000000, 1);
                                $valOps  = round((float)($mRow['total_ops'] ?? 0), 1);
                                $valUtil = round(((float)($mRow['utilization'] ?? 0)) * 100, 1);
                                $valDt   = round(((float)($mRow['sbwc_jam'] ?? 0)) + ((float)($mRow['unpaid_jam'] ?? 0)), 1);
                            ?>
                                <td class="text-right font-num matrix-cell"
                                    data-well="<?= $valWell ?>"
                                    data-rev="<?= number_format($valRev, 1, ',', '.') ?>"
                                    data-ops="<?= number_format($valOps, 1, ',', '.') ?>"
                                    data-util="<?= number_format($valUtil, 1, ',', '.') ?>%"
                                    data-dt="<?= number_format($valDt, 1, ',', '.') ?>">
                                    <?= $valWell ?>
                                </td>
                            <?php endfor; ?>
                            <td class="text-right font-num font-black bg-amber-50 text-[#0f172a] matrix-total-cell"
                                data-well="<?= $row['sumWellJob'] ?>"
                                data-rev="<?= number_format($row['sumRevActual'] / 1000000, 1, ',', '.') ?> Jt"
                                data-ops="<?= number_format($row['sumOps'], 1, ',', '.') ?>"
                                data-util="<?= number_format($row['avgUtil'], 1, ',', '.') ?>%"
                                data-dt="<?= number_format($row['sumDt'], 1, ',', '.') ?>">
                                <?= $row['sumWellJob'] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="excel-footer-gold font-num">
                            <td colspan="3" class="text-center font-black">TOTAL / RATA-RATA BULANAN ARMADA</td>
                            <?php for ($m = 1; $m <= 12; $m++):
                                $agg = $monthlyFleetAgg[$m];
                                $fWell = $agg['well'];
                                $fRev  = round($agg['rev_actual'] / 1000000, 1);
                                $fOps  = round($agg['ops'], 1);
                                $fUtil = $agg['rig_cnt'] > 0 ? round(($agg['uti_sum'] / $agg['rig_cnt']) * 100, 1) : 0;
                                $fDt   = round($agg['sbwc'] + $agg['unpaid'], 1);
                            ?>
                                <td class="text-right matrix-foot-cell"
                                    data-well="<?= $fWell ?>"
                                    data-rev="<?= number_format($fRev, 0, ',', '.') ?>"
                                    data-ops="<?= number_format($fOps, 0, ',', '.') ?>"
                                    data-util="<?= number_format($fUtil, 1, ',', '.') ?>%"
                                    data-dt="<?= number_format($fDt, 1, ',', '.') ?>">
                                    <?= $fWell ?>
                                </td>
                            <?php endfor; ?>
                            <td class="text-right font-black matrix-grand-cell"
                                data-well="<?= $grandWellJob ?>"
                                data-rev="<?= number_format($grandRevActual / 1000000, 1, ',', '.') ?> Jt"
                                data-ops="<?= number_format($grandOps, 1, ',', '.') ?>"
                                data-util="<?= number_format($avgUtilization, 1, ',', '.') ?>%"
                                data-dt="<?= number_format($grandDt, 1, ',', '.') ?>">
                                <?= $grandWellJob ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: GRAFIK ANALISIS TAHUNAN (5 INTERACTIVE CHARTS)                    -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="rekapTab_charts" class="hidden space-y-5">

        <!-- CHART 1 (FULL WIDTH): Tren Kinerja Bulanan Armada (Januari – Desember) -->
        <div class="dr-filter-panel overflow-hidden">
            <div class="px-5 py-3.5 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-2" style="border-color: var(--border);">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider" style="color: var(--foreground);">
                            1. TREN KINERJA BULANAN ARMADA (JANUARI &ndash; DESEMBER <?= $tahun ?>)
                        </h4>
                        <p class="text-[11px]" style="color: var(--muted-foreground);">
                            Kombinasi Realisasi Revenue Bulanan (Juta Rp) dan Penyelesaian Sumur (Well Job) seluruh armada per bulan
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 text-xs font-num font-bold">
                    Total <?= $tahun ?>: Rp <?= number_format($grandRevActual, 0, ',', '.') ?>
                </span>
            </div>
            <div class="p-4 sm:p-5">
                <div class="h-80 w-full">
                    <canvas id="chartMonthlyTrend"></canvas>
                </div>
            </div>
        </div>

        <!-- GRID 2 KOLOM: Well Job per Rig & Revenue per Rig -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- CHART 2: Total Well Job per Rig -->
            <div class="dr-filter-panel overflow-hidden">
                <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color: var(--border);">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-oil-well text-sky-500"></i>
                        <h5 class="text-xs font-black uppercase tracking-wider" style="color: var(--foreground);">
                            2. TOTAL WELL JOB (SUMUR) PER RIG &mdash; <?= $tahun ?>
                        </h5>
                    </div>
                    <span class="text-xs font-num font-bold text-sky-500">Total: <?= $grandWellJob ?> Sumur</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualWell"></canvas>
                    </div>
                </div>
            </div>

            <!-- CHART 3: Revenue Actual vs Target per Rig -->
            <div class="dr-filter-panel overflow-hidden">
                <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color: var(--border);">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-sack-dollar text-emerald-500"></i>
                        <h5 class="text-xs font-black uppercase tracking-wider" style="color: var(--foreground);">
                            3. REALISASI VS TARGET REVENUE PER RIG (JUTA RP)
                        </h5>
                    </div>
                    <span class="text-xs font-num font-bold text-emerald-500">Capaian: <?= $grandRevPct ?>%</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualRevenue"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID 2 KOLOM: Jam Produktif vs Downtime & RAU % per Rig -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- CHART 4: Jam Produktif (OPS + MIRU) vs Downtime -->
            <div class="dr-filter-panel overflow-hidden">
                <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color: var(--border);">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-rose-500"></i>
                        <h5 class="text-xs font-black uppercase tracking-wider" style="color: var(--foreground);">
                            4. JAM PRODUKTIF (OPS &amp; MIRU) VS DOWNTIME PER RIG
                        </h5>
                    </div>
                    <span class="text-xs font-num" style="color: var(--muted-foreground);">Satuan: Jam</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualOpsDt"></canvas>
                    </div>
                </div>
            </div>

            <!-- CHART 5: Rata-Rata RAU % per Rig -->
            <div class="dr-filter-panel overflow-hidden">
                <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color: var(--border);">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high text-indigo-500"></i>
                        <h5 class="text-xs font-black uppercase tracking-wider" style="color: var(--foreground);">
                            5. RATA-RATA RAU (RELIABILITY, AVAILABILITY, UTILIZATION)
                        </h5>
                    </div>
                    <span class="text-xs font-num text-indigo-500 font-bold">Avg Rel: <?= number_format($avgReliability, 1) ?>%</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualUtil"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function navigateTahun() {
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('rekap-tahunan') ?>/${tahun}`;
    }

    function filterAnnualRigRows() {
        const q = (document.getElementById('searchAnnualRigInput')?.value || '').toLowerCase().trim();
        const utilFilter = document.getElementById('filterUtilLevel')?.value || 'all';

        document.querySelectorAll('.annual-rig-row').forEach(tr => {
            const hay = (tr.getAttribute('data-rig') || '').toLowerCase();
            const utilCat = tr.getAttribute('data-util') || '';
            const matchText = !q || hay.includes(q);
            const matchUtil = (utilFilter === 'all') || (utilCat === utilFilter);
            tr.style.display = (matchText && matchUtil) ? '' : 'none';
        });
    }

    function changeMatrixMetric(metric) {
        const titles = {
            well: 'MATRIKS JUMLAH WELL JOB (SUMUR) BULANAN — TAHUN <?= $tahun ?>',
            rev:  'MATRIKS REALISASI REVENUE BULANAN (DALAM JUTA RP) — TAHUN <?= $tahun ?>',
            ops:  'MATRIKS JAM OPERASI (OPS) BULANAN — TAHUN <?= $tahun ?>',
            util: 'MATRIKS RATA-RATA UTILIZATION (%) BULANAN — TAHUN <?= $tahun ?>',
            dt:   'MATRIKS TOTAL DOWNTIME / NPT (JAM) BULANAN — TAHUN <?= $tahun ?>'
        };
        const banner = document.getElementById('matrixTitleBanner');
        if (banner && titles[metric]) banner.textContent = titles[metric];

        document.querySelectorAll('.matrix-cell').forEach(td => {
            td.textContent = td.getAttribute('data-' + metric) || '0';
        });
        document.querySelectorAll('.matrix-total-cell').forEach(td => {
            td.textContent = td.getAttribute('data-' + metric) || '0';
        });
        document.querySelectorAll('.matrix-foot-cell').forEach(td => {
            td.textContent = td.getAttribute('data-' + metric) || '0';
        });
        document.querySelectorAll('.matrix-grand-cell').forEach(td => {
            td.textContent = td.getAttribute('data-' + metric) || '0';
        });
    }

    let rekapChartsInited = false;

    function switchRekapTab(tabKey) {
        ['table', 'matrix', 'charts'].forEach(k => {
            const panel = document.getElementById('rekapTab_' + k);
            const btn   = document.getElementById('rekapTabBtn_' + k);
            if (panel) panel.classList.toggle('hidden', k !== tabKey);
            if (btn) btn.classList.toggle('active', k === tabKey);
        });

        if (tabKey === 'charts' && !rekapChartsInited) {
            initAnnualCharts();
            rekapChartsInited = true;
        }
    }

    <?php
    $rLabels    = [];
    $rWell      = [];
    $rRevActual = [];
    $rRevTarget = [];
    $rOps       = [];
    $rMiru      = [];
    $rDt        = [];
    $rRel       = [];
    $rAva       = [];
    $rUtil      = [];

    foreach ($tableRows as $row) {
        $rLabels[]    = $row['r']['kode'];
        $rWell[]      = (int)$row['sumWellJob'];
        $rRevActual[] = round($row['sumRevActual'] / 1000000, 2);
        $rRevTarget[] = round($row['sumRevTarget'] / 1000000, 2);
        $rOps[]       = round($row['sumOps'], 1);
        $rMiru[]      = round($row['sumMiru'], 1);
        $rDt[]        = round($row['sumDt'], 1);
        $rRel[]       = round($row['avgRel'], 1);
        $rAva[]       = round($row['avgAva'], 1);
        $rUtil[]      = round($row['avgUtil'], 1);
    }

    $mLabels = array_values($bulanShort);
    $mRevArr = [];
    $mWellArr = [];
    for ($m = 1; $m <= 12; $m++) {
        $mRevArr[]  = round($monthlyFleetAgg[$m]['rev_actual'] / 1000000, 2);
        $mWellArr[] = (int)$monthlyFleetAgg[$m]['well'];
    }
    ?>

    const annualLabels    = <?= json_encode($rLabels) ?>;
    const annualWell      = <?= json_encode($rWell) ?>;
    const annualRevActual = <?= json_encode($rRevActual) ?>;
    const annualRevTarget = <?= json_encode($rRevTarget) ?>;
    const annualOps       = <?= json_encode($rOps) ?>;
    const annualMiru      = <?= json_encode($rMiru) ?>;
    const annualDt        = <?= json_encode($rDt) ?>;
    const annualRel       = <?= json_encode($rRel) ?>;
    const annualAva       = <?= json_encode($rAva) ?>;
    const annualUtil      = <?= json_encode($rUtil) ?>;

    const monthLabels     = <?= json_encode($mLabels) ?>;
    const monthRevData    = <?= json_encode($mRevArr) ?>;
    const monthWellData   = <?= json_encode($mWellArr) ?>;

    function initAnnualCharts() {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.14)' : 'rgba(15, 23, 42, 0.08)';
        const tickColor = isDark ? '#cbd5e1' : '#334155';
        const CH_GRID  = { color: gridColor };
        const CH_TICKS = { color: tickColor, font: { size: 11, family: "'JetBrains Mono', monospace", weight: '600' } };
        const CH_LEGEND = { labels: { color: tickColor, font: { size: 11, weight: 'bold' } } };

        // 1. Monthly Fleet Trend Chart (Combo Bar + Line)
        const ctxTrend = document.getElementById('chartMonthlyTrend');
        if (ctxTrend) {
            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Revenue Aktual Bulanan (Juta Rp)',
                            data: monthRevData,
                            backgroundColor: 'rgba(245, 158, 11, 0.82)',
                            borderColor: '#d97706',
                            borderWidth: 1,
                            borderRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            type: 'line',
                            label: 'Total Well Job Bulanan (Sumur)',
                            data: monthWellData,
                            borderColor: '#0284c7',
                            backgroundColor: '#0284c7',
                            borderWidth: 3,
                            pointRadius: 4,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: CH_LEGEND },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            grid: CH_GRID,
                            ticks: { ...CH_TICKS, callback: v => 'Rp ' + v.toLocaleString('id-ID') + ' Jt' }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            ticks: { ...CH_TICKS, callback: v => v + ' Sumur' }
                        }
                    }
                }
            });
        }

        // 2. Chart Well Job per Rig
        const ctxWell = document.getElementById('chartAnnualWell');
        if (ctxWell) {
            new Chart(ctxWell, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [{
                        label: 'Total Well Job (Sumur)',
                        data: annualWell,
                        backgroundColor: '#0284c7',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, precision: 0 } }
                    }
                }
            });
        }

        // 3. Chart Revenue Actual vs Target
        const ctxRev = document.getElementById('chartAnnualRevenue');
        if (ctxRev) {
            new Chart(ctxRev, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [
                        {
                            label: 'Target Kontrak (Juta Rp)',
                            data: annualRevTarget,
                            backgroundColor: isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(100, 116, 139, 0.3)',
                            borderRadius: 4
                        },
                        {
                            label: 'Realisasi Actual (Juta Rp)',
                            data: annualRevActual,
                            backgroundColor: '#10b981',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: CH_LEGEND,
                        tooltip: { callbacks: { label: c => ' ' + c.dataset.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID') + ' Juta' } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => 'Rp ' + v + ' Jt' } }
                    }
                }
            });
        }

        // 4. Chart Jam OPS + MIRU vs Downtime
        const ctxOps = document.getElementById('chartAnnualOpsDt');
        if (ctxOps) {
            new Chart(ctxOps, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [
                        { label: 'Jam OPS', data: annualOps, backgroundColor: '#0284c7', borderRadius: 4 },
                        { label: 'Jam MIRU', data: annualMiru, backgroundColor: '#38bdf8', borderRadius: 4 },
                        { label: 'Downtime (SBWC+Unpaid)', data: annualDt, backgroundColor: '#f43f5e', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: CH_LEGEND },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => v + 'j' } }
                    }
                }
            });
        }

        // 5. Chart RAU % per Rig
        const ctxUtil = document.getElementById('chartAnnualUtil');
        if (ctxUtil) {
            new Chart(ctxUtil, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [
                        { label: 'Reliability (%)', data: annualRel, backgroundColor: '#10b981', borderRadius: 3 },
                        { label: 'Availability (%)', data: annualAva, backgroundColor: '#0ea5e9', borderRadius: 3 },
                        { label: 'Utilization (%)', data: annualUtil, backgroundColor: '#6366f1', borderRadius: 3 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: CH_LEGEND,
                        tooltip: { callbacks: { label: c => ' ' + c.dataset.label + ': ' + c.raw + '%' } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, max: 100, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => v + '%' } }
                    }
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
