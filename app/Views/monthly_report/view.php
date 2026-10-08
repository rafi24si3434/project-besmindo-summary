<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$bulanList = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$prevBulan = $bulan - 1;
$prevTahun = $tahun;
if ($prevBulan < 1) {
    $prevBulan = 12;
    $prevTahun--;
}

$nextBulan = $bulan + 1;
$nextTahun = $tahun;
if ($nextBulan > 12) {
    $nextBulan = 1;
    $nextTahun++;
}

$revPct = $totRevTarget > 0 ? min(100, round(($totRevActual / $totRevTarget) * 100, 1)) : 0;
$productiveHoursAll = $totMiru + $totOps;
$productivePctAll   = $totJam > 0 ? round(($productiveHoursAll / $totJam) * 100, 1) : 0;
$dtHoursAll         = $totSbwc + $totUnpaid;
$dtPctAll           = $totJam > 0 ? round(($dtHoursAll / $totJam) * 100, 1) : 0;

$decodeRemarkDisplay = static function (?string $raw): string {
    $raw = trim((string)$raw);
    if ($raw === '') return '-';
    $dec = json_decode($raw, true);
    if (is_array($dec) && (isset($dec['notes']) || isset($dec['schedule_mtc']))) {
        $items = [];
        foreach (($dec['notes'] ?? []) as $ni) {
            $lbl = trim(($ni['lokasi'] ?? '') . ' ' . ($ni['tanggal'] ?? ''));
            $txt = trim(($ni['isi'] ?? '') . ' ' . ($ni['waktu'] ?? ''));
            if ($txt !== '') {
                $items[] = ($lbl !== '' ? "[{$lbl}] " : '') . $txt;
            }
        }
        return !empty($items) ? implode(' | ', $items) : '-';
    }
    return $raw;
};
?>

<style>
    .dr-filter-panel {
        background-color: var(--card);
        border: 1.5px solid var(--border-strong);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
    }
    .dr-stat-card {
        background-color: var(--card);
        border: 1.5px solid var(--border-strong);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
    }
    .dr-stat-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.14);
    }
    .dr-filter-label {
        display: block;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--muted-foreground);
        margin-bottom: 6px;
    }
    .dr-filter-select {
        display: block;
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border-radius: 12px !important;
        background-color: var(--background) !important;
        border: 1.5px solid var(--border-strong) !important;
        color: var(--foreground) !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        transition: border-color 150ms ease, box-shadow 150ms ease;
        cursor: pointer;
    }
    .dr-filter-select:hover {
        border-color: #3b82f6 !important;
    }
    .dr-filter-select:focus {
        outline: none !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18) !important;
    }
    .dr-step-btn {
        height: 44px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 12px;
        background-color: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
        transition: all 150ms ease;
        cursor: pointer;
        text-decoration: none;
    }
    .dr-step-btn:hover {
        border-color: #2563eb;
        background-color: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }
    .dr-action-secondary {
        height: 42px;
        background-color: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
    }
    .dr-action-secondary:hover {
        border-color: #2563eb;
        background-color: rgba(37, 99, 235, 0.08);
    }
    .mr-segmented-group {
        background-color: var(--background);
        border: 1.5px solid var(--border-strong);
        padding: 4px;
        border-radius: 12px;
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
    }
    .mr-tab-pill {
        padding: 9px 15px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--muted-foreground);
        background: transparent;
        border: 1px solid transparent;
        transition: all 150ms ease;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }
    .mr-tab-pill:hover {
        color: var(--foreground);
        background-color: rgba(148, 163, 184, 0.12);
    }
    .mr-tab-pill.active {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #1d4ed8 !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .mr-tab-pill.active i {
        color: #ffffff !important;
    }
</style>

<div class="ui-screen ui-screen--report space-y-5">

    <!-- ═══ 1. PANEL KONTROL & FILTER PERIODE BULANAN (SERAGAM DENGAN DAILY REPORT) ═══ -->
    <div class="dr-filter-panel p-5 sm:p-6 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b" style="border-color: var(--border);">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-500/15 text-amber-500 border border-amber-500/30 text-[11px] font-mono font-extrabold uppercase tracking-wider">
                            MONTHLY SUMMARY RAU
                        </span>
                        <span class="text-xs font-semibold" style="color: var(--muted-foreground);">
                            Rekapitulasi Kinerja <?= count($summaries) ?> Armada Rig BMS
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1" style="color: var(--foreground);">
                        Summary Report Operation RIG BMS — <span class="text-amber-500"><?= $bulanList[$bulan] ?> <?= $tahun ?></span>
                    </h2>
                </div>
            </div>

            <!-- Tombol Aksi Utama (Hitung Ulang & Export Excel) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form action="<?= base_url('monthly-report/hitung') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">
                    <button type="submit"
                        class="dr-action-secondary px-4 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold flex items-center gap-2 transition cursor-pointer">
                        <i class="fa-solid fa-arrows-rotate text-sky-500"></i>
                        <span>Sinkron &amp; Hitung Ulang KPI</span>
                    </button>
                </form>

                <button type="button" onclick="openExportModal()"
                    class="h-[42px] px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-extrabold flex items-center gap-2 shadow-md transition cursor-pointer">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Download Excel (.xlsx)</span>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-80"></i>
                </button>
            </div>
        </div>

        <!-- Baris Pilih Bulan, Tahun, & Navigasi Cepat Bulan -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Pilih Bulan (4 Col) -->
            <div class="md:col-span-4">
                <label for="selectBulan" class="dr-filter-label">
                    <i class="fa-regular fa-calendar text-sky-500 mr-1"></i> Pilih Bulan Laporan
                </label>
                <select id="selectBulan" onchange="navigateReport()" class="dr-filter-select">
                    <?php foreach ($bulanList as $num => $nama): ?>
                        <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>>
                            Bulan <?= sprintf('%02d', $num) ?> — <?= $nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pilih Tahun (3 Col) -->
            <div class="md:col-span-3">
                <label for="selectTahun" class="dr-filter-label">
                    <i class="fa-solid fa-calendar-days text-amber-500 mr-1"></i> Pilih Tahun
                </label>
                <select id="selectTahun" onchange="navigateReport()" class="dr-filter-select">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>Tahun <?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Navigasi Cepat Bulan Sebelumnya / Berikutnya (5 Col) -->
            <div class="md:col-span-5">
                <span class="dr-filter-label">
                    <i class="fa-solid fa-compass text-emerald-500 mr-1"></i> Navigasi Cepat Periode
                </span>
                <div class="flex items-center gap-2">
                    <a href="<?= base_url("monthly-report/{$prevBulan}/{$prevTahun}") ?>"
                       class="dr-step-btn flex-1 justify-center">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span><?= substr($bulanList[$prevBulan], 0, 3) ?> <?= $prevTahun ?></span>
                    </a>
                    <button type="button" onclick="navigateReport()"
                        class="h-[44px] px-5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-extrabold flex items-center justify-center gap-1.5 shadow transition cursor-pointer shrink-0">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        <span>Tampilkan</span>
                    </button>
                    <a href="<?= base_url("monthly-report/{$nextBulan}/{$nextTahun}") ?>"
                       class="dr-step-btn flex-1 justify-center">
                        <span><?= substr($bulanList[$nextBulan], 0, 3) ?> <?= $nextTahun ?></span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 2. EMPAT KARTU RINGKASAN EKSEKUTIF (BENTO KPI CARDS) ═══ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Card 1: Indeks RAU Rata-Rata Armada -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Rata-Rata Indeks RAU Armada
                    </span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-emerald-500">
                            <?= number_format($avgReliability * 100, 1) ?>%
                        </span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Reliability</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Availability: <strong class="text-sky-500"><?= number_format($avgAvailability * 100, 1) ?>%</strong></span>
                <span>Utilization: <strong class="text-indigo-500"><?= number_format($avgUtilization * 100, 1) ?>%</strong></span>
            </div>
        </div>

        <!-- Card 2: Total Realisasi Revenue vs Target -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Realisasi Pendapatan (Actual)
                    </span>
                    <div class="text-lg sm:text-xl font-black text-amber-500 mt-1.5 font-num truncate" title="Rp <?= number_format($totRevActual, 0, ',', '.') ?>">
                        Rp <?= number_format($totRevActual, 0, ',', '.') ?>
                    </div>
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

        <!-- Card 3: Total Sumur & Jam Produktif -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Total Sumur &amp; Jam Produktif
                    </span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-sky-500">
                            <?= number_format($totWell, 0, ',', '.') ?>
                        </span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Sumur (Well Job)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-500 shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>OPS: <strong class="text-emerald-500"><?= number_format($totOps, 1) ?>j</strong></span>
                <span>MIRU: <strong class="text-sky-500"><?= number_format($totMiru, 1) ?>j</strong> (<?= $productivePctAll ?>%)</span>
            </div>
        </div>

        <!-- Card 4: Total Downtime (SBWC & Unpaid) -->
        <div class="dr-stat-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        Total Downtime / NPT Armada
                    </span>
                    <div class="flex items-baseline gap-2 mt-1.5 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-rose-500">
                            <?= number_format($dtHoursAll, 2) ?>
                        </span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Jam (<?= $dtPctAll ?>%)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-500 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>SBWC: <strong class="text-amber-500"><?= number_format($totSbwc, 2) ?>j</strong></span>
                <span>UNPAID: <strong class="text-rose-500"><?= number_format($totUnpaid, 2) ?>j</strong></span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. BARIS TAB MODE TAMPILAN & PENCARIAN CEPAT RIG ═══ -->
    <div class="dr-filter-panel p-3.5 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="mr-segmented-group">
            <button type="button" onclick="switchTab('summary')" id="tabBtn_summary"
                class="mr-tab-pill active">
                <i class="fa-solid fa-table text-amber-500"></i>
                <span>1. Summary Operation (Excel)</span>
            </button>
            <button type="button" onclick="switchTab('charts')" id="tabBtn_charts"
                class="mr-tab-pill">
                <i class="fa-solid fa-chart-column text-sky-500"></i>
                <span>2. Grafik Performa (5 Chart)</span>
            </button>
            <button type="button" onclick="switchTab('odrTable')" id="tabBtn_odrTable"
                class="mr-tab-pill">
                <i class="fa-solid fa-money-bill-1-wave text-emerald-500"></i>
                <span>3. Nilai Kontrak ODR &amp; Tarif</span>
            </button>
            <button type="button" onclick="switchTab('nptAll')" id="tabBtn_nptAll"
                class="mr-tab-pill">
                <i class="fa-solid fa-clock-rotate-left text-rose-500"></i>
                <span>4. Rincian NPT Semua Rig</span>
            </button>
        </div>

        <!-- Pencarian Cepat Kode Rig -->
        <div class="relative w-full lg:w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs pointer-events-none" style="color: var(--muted-foreground);"></i>
            <input type="text" id="searchMonthlyRigInput" oninput="filterMonthlyRigRows()"
                placeholder="Cari kode rig (misal: BMS#15)..."
                class="dr-filter-select w-full !pl-9 !pr-8 !py-2 text-xs">
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: SUMMARY OPERATION PERSIS EXCEL ASLI + TOMBOL DRILL-DOWN KE RIG    -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_summary" class="tab-content space-y-4">
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl overflow-hidden">
            <!-- Banner Judul Kuning Persis Excel + Info Klik -->
            <div class="bg-amber-400 text-slate-950 py-3 px-5 flex flex-col sm:flex-row items-center justify-between gap-2 border-b-2 border-amber-500">
                <div class="font-black text-xs sm:text-sm uppercase tracking-wider text-center sm:text-left">
                    <i class="fa-solid fa-file-spreadsheet mr-1.5"></i>
                    SUMMARY REPORT OPERATION RIG BMS PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?>
                </div>
                <span class="text-[11px] font-extrabold bg-slate-950/15 px-3 py-1 rounded-lg">
                    Klik nama Rig atau tombol "Rekap Sumur" untuk membuka Daily Report Rig terkait
                </span>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[11px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2.5 w-10 border border-slate-700 bg-[#0B1E4A]">NO</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[105px]">NAME RIG</th>
                            <th colspan="3" class="py-2 px-2 border border-slate-700 bg-[#0E2A66]">RAU INDEX</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">TOTAL MIRU<br>(HRS)</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">TOTAL OPS<br>(HRS)</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">AVG MIRU<br>(HRS)</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">AVG CYCLE<br>TIME (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0B1E4A] min-w-[72px]">TOTAL<br>WELL JOB</th>
                            <th colspan="2" class="py-2 px-2 border border-slate-700 bg-[#0E2A66]">DOWNTIME (NPT)</th>
                            <th colspan="2" class="py-2 px-3 border border-slate-700 bg-[#0E2A66]">REVENUE (Rp)</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[78px]">TOTAL<br>HOURS</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[160px]">REMARK / NOTE</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[115px]">DETAIL SUMUR</th>
                        </tr>
                        <tr class="text-[10px] bg-[#0A1A3F] border-t border-slate-700 text-slate-300">
                            <th class="py-1.5 px-2 border border-slate-700 text-sky-300">RELIABILITY (%)</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-sky-300">AVAILABILITY (%)</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-sky-300">UTILIZATION (%)</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-amber-300">SBWC (HRS)</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-300">UNPAID (HRS)</th>
                            <th class="py-1.5 px-2.5 border border-slate-700 text-slate-300">INCENTIVE TARGET</th>
                            <th class="py-1.5 px-2.5 border border-slate-700 text-emerald-300">ACTUAL REVENUE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php $no = 1; foreach ($summaries as $s):
                            $rel = (float)($s['reliability'] ?? 0) * 100;
                            $ava = (float)($s['availability'] ?? 0) * 100;
                            $uti = (float)($s['utilization'] ?? 0) * 100;
                            $cleanRemark = $decodeRemarkDisplay($s['remark'] ?? '');
                            $dailyUrl = base_url("daily-report/{$s['rig_id']}/{$bulan}/{$tahun}");
                        ?>
                        <tr class="hover:bg-slate-800/80 transition monthly-rig-row" data-rig="<?= strtolower(esc($s['kode'] . ' ' . ($s['nama_rig'] ?? '') . ' ' . $cleanRemark)) ?>">
                            <td class="py-2.5 px-2.5 text-center text-xs text-slate-400 border border-slate-800 bg-slate-900/50"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 font-extrabold text-white text-xs sm:text-[13px] border border-slate-800 font-sans bg-slate-900/50">
                                <a href="<?= $dailyUrl ?>" class="hover:text-amber-400 flex items-center justify-between gap-1.5 transition" title="Buka Laporan Per Well <?= esc($s['kode']) ?>">
                                    <span><?= esc($s['kode']) ?></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-60"></i>
                                </a>
                            </td>

                            <!-- Kolom RAU Index -->
                            <td class="py-2.5 px-2 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold <?= $rel >= 99.9 ? 'text-emerald-400' : 'text-amber-400' ?>"><?= number_format($rel, 2) ?>%</span>
                            </td>
                            <td class="py-2.5 px-2 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold <?= $ava >= 99.9 ? 'text-emerald-400' : 'text-amber-400' ?>"><?= number_format($ava, 2) ?>%</span>
                            </td>
                            <td class="py-2.5 px-2 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold text-indigo-400"><?= number_format($uti, 2) ?>%</span>
                            </td>

                            <!-- Total MIRU & Total OPS -->
                            <td class="py-2.5 px-2.5 text-right font-semibold text-[13px] border border-slate-800 text-sky-300"><?= number_format((float)($s['total_miru'] ?? 0), 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right font-semibold text-[13px] border border-slate-800 text-emerald-300"><?= number_format((float)($s['total_ops'] ?? 0), 2) ?></td>

                            <!-- Average MIRU & Cycle Time -->
                            <td class="py-2.5 px-2.5 text-right text-xs border border-slate-800 text-slate-300"><?= number_format((float)($s['avg_miru'] ?? 0), 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right text-xs border border-slate-800 text-slate-300"><?= number_format((float)($s['avg_cycle_time'] ?? 0), 2) ?></td>

                            <!-- Total Well Job -->
                            <td class="py-2.5 px-2 text-center text-[13.5px] font-extrabold text-white border border-slate-800"><?= (int)($s['total_well_job'] ?? 0) ?></td>

                            <!-- Downtime SBWC & UNPAID -->
                            <td class="py-2.5 px-2.5 text-right text-[13px] font-bold border border-slate-800 <?= (float)($s['sbwc_jam'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-500' ?>"><?= number_format((float)($s['sbwc_jam'] ?? 0), 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right text-[13px] font-bold border border-slate-800 <?= (float)($s['unpaid_jam'] ?? 0) > 0 ? 'text-rose-400' : 'text-slate-500' ?>"><?= number_format((float)($s['unpaid_jam'] ?? 0), 2) ?></td>

                            <!-- Revenue Incentive Target & Actual -->
                            <td class="py-2.5 px-2.5 text-right text-xs font-medium border border-slate-800 text-slate-300 whitespace-nowrap">
                                Rp <?= number_format((float)($s['revenue_target'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-2.5 text-right text-[13.5px] font-extrabold border border-slate-800 text-emerald-400 whitespace-nowrap">
                                Rp <?= number_format((float)($s['revenue_actual'] ?? 0), 0, ',', '.') ?>
                            </td>

                            <td class="py-2.5 px-2.5 text-right text-xs font-bold border border-slate-800 text-slate-300"><?= number_format((float)($s['total_jam'] ?? 0), 2) ?></td>
                            <td class="py-2.5 px-3 text-xs text-slate-300 font-sans max-w-xs truncate border border-slate-800" title="<?= esc($cleanRemark) ?>">
                                <?= esc($cleanRemark) ?>
                            </td>
                            <td class="py-2 px-2.5 text-center border border-slate-800 whitespace-nowrap">
                                <a href="<?= $dailyUrl ?>"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500 text-sky-300 hover:text-white border border-sky-500/30 text-[11px] font-extrabold font-sans transition">
                                    <i class="fa-solid fa-table-list text-[10px]"></i>
                                    <span>Rekap Sumur</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs font-num">
                        <tr>
                            <td colspan="2" class="py-3 px-3 text-center border border-slate-700 font-sans uppercase">AVERAGE / TOTAL</td>
                            <td class="py-3 px-2 text-center border border-slate-700 text-emerald-400"><?= number_format($avgReliability * 100, 2) ?>%</td>
                            <td class="py-3 px-2 text-center border border-slate-700 text-emerald-400"><?= number_format($avgAvailability * 100, 2) ?>%</td>
                            <td class="py-3 px-2 text-center border border-slate-700 text-indigo-300"><?= number_format($avgUtilization * 100, 2) ?>%</td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-sky-300"><?= number_format($totMiru, 2) ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-emerald-300"><?= number_format($totOps, 2) ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-slate-300"><?= number_format($avgMiruAll, 2) ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-slate-300"><?= number_format($avgCycleTimeAll, 2) ?></td>
                            <td class="py-3 px-2 text-center border border-slate-700 text-white text-sm"><?= $totWell ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-amber-300"><?= number_format($totSbwc, 2) ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-rose-300"><?= number_format($totUnpaid, 2) ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-slate-300 whitespace-nowrap">Rp <?= number_format($totRevTarget, 0, ',', '.') ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700 text-emerald-400 text-sm whitespace-nowrap">Rp <?= number_format($totRevActual, 0, ',', '.') ?></td>
                            <td class="py-3 px-2.5 text-right border border-slate-700"><?= number_format($totJam, 2) ?></td>
                            <td colspan="2" class="border border-slate-700"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: DAFTAR NILAI KONTRAK ODR & MATRIKS TARIF PER JAM                  -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_odrTable" class="tab-content hidden space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
            <!-- Kiri (4 Col): Tabel ODR Warna Asli Excel -->
            <div class="xl:col-span-4 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl overflow-hidden">
                <div class="bg-blue-600 text-white py-3 px-4 font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-between">
                    <span>TABEL ODR (EXCEL ASLI)</span>
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <table class="w-full text-left border-collapse border border-slate-700 text-xs font-num">
                    <tbody class="divide-y divide-slate-700">
                        <?php
                        $odrColors = [
                            'BMS#01'  => 'bg-slate-850 text-white',
                            'BMS#02'  => 'bg-sky-200 text-slate-900',
                            'BMS#17'  => 'bg-amber-100 text-slate-900',
                            'BMS#03'  => 'bg-sky-300 text-slate-900',
                            'BMS#08'  => 'bg-sky-300 text-slate-900',
                            'BMS#03A' => 'bg-sky-400 text-slate-900',
                            'BMS#05'  => 'bg-sky-400 text-slate-900',
                            'BMS#06'  => 'bg-sky-400 text-slate-900',
                            'BMS#11'  => 'bg-sky-400 text-slate-900',
                            'BMS#07'  => 'bg-yellow-300 text-slate-900',
                            'BMS#15'  => 'bg-yellow-300 text-slate-900',
                            'BMS#18'  => 'bg-yellow-300 text-slate-900',
                            'BMS#09'  => 'bg-lime-400 text-slate-900',
                            'BMS#10'  => 'bg-lime-400 text-slate-900',
                            'BMS#16'  => 'bg-lime-400 text-slate-900',
                            'BMS#19'  => 'bg-yellow-300 text-slate-900',
                            'BMS#20'  => 'bg-yellow-300 text-slate-900',
                            'BMS#21'  => 'bg-yellow-300 text-slate-900',
                        ];
                        foreach ($summaries as $s):
                            $kd = $s['kode'];
                            $rowStyle = $odrColors[$kd] ?? 'bg-slate-800 text-white';
                        ?>
                        <tr class="border-b border-slate-700 font-extrabold monthly-rig-row" data-rig="<?= strtolower(esc($kd)) ?>">
                            <td class="py-2.5 px-4 border-r border-slate-700 <?= $rowStyle ?> font-sans w-32">
                                <?= esc($kd) ?>
                            </td>
                            <td class="py-2.5 px-4 text-right <?= $rowStyle ?>">
                                Rp <?= number_format((float)($s['odr'] ?? 0), 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Kanan (8 Col): Matriks Lengkap Tarif Per Jam (OPS 100%, MIRU 75%, SBWC 65%) -->
            <div class="xl:col-span-8 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl overflow-hidden">
                <div class="bg-slate-950 text-white py-3 px-5 border-b border-slate-700 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider text-amber-400">
                            Matriks Rincian Tarif Kontrak Per Jam (ODR Breakdown)
                        </h4>
                        <p class="text-[11px] text-slate-400">
                            Acuan perhitungan otomatis baris tarif merah (`Rp`) pada laporan Summary Report Per Well
                        </p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs font-num">
                        <thead class="bg-[#0B1E4A] text-white uppercase text-[11px] font-extrabold text-center">
                            <tr>
                                <th class="py-2.5 px-3 border border-slate-700">Kode Rig</th>
                                <th class="py-2.5 px-3 border border-slate-700 text-right">ODR Harian (24 Jam)</th>
                                <th class="py-2.5 px-3 border border-slate-700 text-right text-emerald-300">Tarif OPS / Jam (100%)</th>
                                <th class="py-2.5 px-3 border border-slate-700 text-right text-sky-300">Tarif MIRU / Jam (75%)</th>
                                <th class="py-2.5 px-3 border border-slate-700 text-right text-amber-300">Tarif SBWC / Jam (65%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-200">
                            <?php foreach ($summaries as $s):
                                $rOdr  = (float)($s['odr'] ?? 0);
                                $rOps  = round($rOdr / 24);
                                $rMiru = round(($rOdr / 24) * 0.75);
                                $rSbwc = round(($rOdr / 24) * 0.65);
                            ?>
                            <tr class="hover:bg-slate-800/70 transition monthly-rig-row" data-rig="<?= strtolower(esc($s['kode'])) ?>">
                                <td class="py-2.5 px-3 font-extrabold text-white border border-slate-800 font-sans text-center"><?= esc($s['kode']) ?></td>
                                <td class="py-2.5 px-3 text-right font-bold text-white border border-slate-800">Rp <?= number_format($rOdr, 0, ',', '.') ?></td>
                                <td class="py-2.5 px-3 text-right font-semibold text-emerald-400 border border-slate-800">Rp <?= number_format($rOps, 0, ',', '.') ?></td>
                                <td class="py-2.5 px-3 text-right font-semibold text-sky-400 border border-slate-800">Rp <?= number_format($rMiru, 0, ',', '.') ?></td>
                                <td class="py-2.5 px-3 text-right font-semibold text-amber-400 border border-slate-800">Rp <?= number_format($rSbwc, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: NPT ALL RIG (MATRIKS RINCIAN 18 KATEGORI DOWNTIME)                 -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_nptAll" class="tab-content hidden space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-[var(--card)] border border-[var(--border)]">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-[var(--foreground)]">
                    Rekapitulasi NPT Seluruh Armada Rig BMS
                </h3>
                <p class="text-[11px] text-[var(--muted-foreground)]">
                    Tabel matriks downtime 18 kategori terpadu dengan buku kerja operasional resmi.
                </p>
            </div>
            <a href="<?= base_url("npt/2/{$bulan}/{$tahun}?view=summary") ?>"
                class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-file-excel"></i>
                <span>Buka Informasi NPT Persis Excel</span>
                <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
            </a>
        </div>
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th class="py-2.5 px-2.5 w-10 border border-slate-700 sticky left-0 bg-[#0B1E4A]">NO</th>
                            <th class="py-2.5 px-3 border border-slate-700 min-w-[95px] sticky left-10 bg-[#0B1E4A]">NAME RIG</th>
                            <?php foreach ($kategoriList as $kat): ?>
                                <th class="py-2 px-2 min-w-[78px] border border-slate-700" title="<?= esc($kat['nama']) ?>">
                                    <span class="block truncate <?= $kat['tipe'] == 'UNPAID' ? 'text-rose-400' : 'text-yellow-300' ?>"><?= esc($kat['nama']) ?></span>
                                    <span class="text-[8px] font-mono opacity-70"><?= $kat['tipe'] ?></span>
                                </th>
                            <?php endforeach; ?>
                            <th class="py-2.5 px-3 text-right min-w-[85px] bg-[#0E2A66] font-bold text-yellow-300 border border-slate-700">TOTAL (HRS)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php
                        $no = 1;
                        $grandTotalDT = 0;
                        $colTotals = [];
                        foreach ($summaries as $s):
                            $rigTotalDT = 0;
                        ?>
                        <tr class="hover:bg-slate-800 transition monthly-rig-row" data-rig="<?= strtolower(esc($s['kode'])) ?>">
                            <td class="py-2 px-2.5 text-center text-xs text-slate-400 border border-slate-800 sticky left-0 bg-slate-900"><?= $no++ ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 sticky left-10 bg-slate-900 font-sans"><?= esc($s['kode']) ?></td>
                            <?php foreach ($kategoriList as $kat):
                                $val = (float)($summaryAllRig[$s['rig_id']][$kat['id']] ?? 0);
                                $rigTotalDT += $val;
                                $colTotals[$kat['id']] = ($colTotals[$kat['id']] ?? 0) + $val;
                            ?>
                                <td class="py-2 px-1.5 text-center border border-slate-800 text-[13px] <?= $val > 0 ? ($kat['tipe'] == 'UNPAID' ? 'text-rose-400 font-bold' : 'text-yellow-300 font-bold') : 'text-slate-600' ?>">
                                    <?= $val > 0 ? number_format($val, 2) : '-' ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="py-2 px-3 text-right font-extrabold text-[13.5px] text-yellow-300 bg-[#0E2A66]/40 border border-slate-800">
                                <?= number_format($rigTotalDT, 2) ?>
                            </td>
                        </tr>
                        <?php
                            $grandTotalDT += $rigTotalDT;
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs font-num">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 sticky left-0 bg-[#0B1E4A] font-sans">TOTAL DOWNTIME</td>
                            <?php foreach ($kategoriList as $kat): ?>
                                <td class="py-2.5 px-1.5 text-center border border-slate-700 text-emerald-400">
                                    <?= number_format($colTotals[$kat['id']] ?? 0, 2) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="py-2.5 px-3 text-right font-extrabold text-[14px] text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($grandTotalDT, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: GRAFIK ANALISIS RESMI (NPT ALL RIG, RAU ALL RIG, AVERAGE MIRU, AVERAGE CYCLE TIME, TOTAL WELL JOB) -->
    <div id="tabContent_charts" class="tab-content hidden space-y-6">

        <!-- Executive Header & Quick Jump Navigation Bar -->
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 sm:p-5 shadow-sm space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center flex-shrink-0 text-xl shadow-xs">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-base sm:text-lg font-black text-[var(--foreground)] tracking-tight">GRAFIK PERFORMA OPERASI RIG BMS</h4>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-primary/10 text-primary border border-primary/20">
                                <i class="fa-solid fa-chart-line text-[9px]"></i> Executive KPI Analytics
                            </span>
                        </div>
                        <p class="text-xs text-[var(--muted-foreground)] mt-0.5">
                            Visualisasi 5 Indikator Kinerja Utama Periode: <strong class="text-[var(--foreground)] font-mono font-bold"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Rig View Filter Toggle (Semua Rig vs Hanya Rig Aktif) -->
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-[var(--secondary)] border border-[var(--border)] self-start lg:self-auto">
                    <button type="button" onclick="setChartFilter('all')" id="btnFilterChartAll"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs bg-[var(--card)] text-[var(--foreground)] border border-[var(--border)]">
                        <i class="fa-solid fa-layer-group mr-1 text-primary"></i> Semua Rig (<?= count($summaries) ?>)
                    </button>
                    <button type="button" onclick="setChartFilter('active')" id="btnFilterChartActive"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-all">
                        <i class="fa-solid fa-bolt mr-1 text-amber-500"></i> Hanya Rig Aktif
                    </button>
                </div>
            </div>

            <!-- Quick Jump Anchors -->
            <div class="pt-3 border-t border-[var(--border)] flex flex-wrap items-center gap-2 text-xs">
                <span class="text-[11px] font-bold text-[var(--muted-foreground)] uppercase tracking-wider mr-1">Lompat Ke:</span>
                <a href="#cardChartNpt" class="px-3 py-1.5 rounded-xl border border-[var(--border)] bg-[var(--card-elevated)] text-[var(--foreground)] hover:border-rose-500 hover:text-rose-500 transition font-medium flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>1. NPT All Rig</span>
                </a>
                <a href="#cardChartRau" class="px-3 py-1.5 rounded-xl border border-[var(--border)] bg-[var(--card-elevated)] text-[var(--foreground)] hover:border-emerald-500 hover:text-emerald-500 transition font-medium flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>2. RAU All Rig</span>
                </a>
                <a href="#cardChartMiru" class="px-3 py-1.5 rounded-xl border border-[var(--border)] bg-[var(--card-elevated)] text-[var(--foreground)] hover:border-blue-500 hover:text-blue-500 transition font-medium flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>3. Avg MIRU</span>
                </a>
                <a href="#cardChartCt" class="px-3 py-1.5 rounded-xl border border-[var(--border)] bg-[var(--card-elevated)] text-[var(--foreground)] hover:border-teal-500 hover:text-teal-500 transition font-medium flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    <span>4. Avg Cycle Time</span>
                </a>
                <a href="#cardChartWell" class="px-3 py-1.5 rounded-xl border border-[var(--border)] bg-[var(--card-elevated)] text-[var(--foreground)] hover:border-indigo-500 hover:text-indigo-500 transition font-medium flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>5. Total Well</span>
                </a>
            </div>
        </div>

        <!-- Executive KPI Summary Ribbon (5 Ringkasan Indikator Cepat) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <!-- 1. Reliability -->
            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[var(--muted-foreground)] mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Fleet Reliability</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">
                    <?= number_format($avgReliability * 100, 2) ?>%
                </div>
                <span class="text-[10px] text-[var(--muted-foreground)] mt-1">Rata-rata Keandalan Rig</span>
            </div>

            <!-- 2. Availability -->
            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[var(--muted-foreground)] mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Fleet Availability</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                </div>
                <div class="text-xl font-black text-sky-600 dark:text-sky-400 font-mono tracking-tight">
                    <?= number_format($avgAvailability * 100, 2) ?>%
                </div>
                <span class="text-[10px] text-[var(--muted-foreground)] mt-1">Rata-rata Kesiapan Kerja</span>
            </div>

            <!-- 3. Utilization -->
            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[var(--muted-foreground)] mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Fleet Utilization</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                </div>
                <div class="text-xl font-black text-indigo-600 dark:text-indigo-400 font-mono tracking-tight">
                    <?= number_format($avgUtilization * 100, 2) ?>%
                </div>
                <span class="text-[10px] text-[var(--muted-foreground)] mt-1">Tingkat Utilisasi Operasi</span>
            </div>

            <!-- 4. Total NPT -->
            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[var(--muted-foreground)] mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Total NPT (Downtime)</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                </div>
                <div class="text-xl font-black text-rose-600 dark:text-rose-400 font-mono tracking-tight">
                    <?= number_format($totSbwc + $totUnpaid, 2) ?> <span class="text-xs font-semibold">Jam</span>
                </div>
                <div class="flex items-center gap-1.5 text-[10px] text-[var(--muted-foreground)] mt-1">
                    <span class="text-rose-500 font-semibold font-mono">Unpaid: <?= number_format($totUnpaid, 1) ?>h</span>
                    <span>&bull;</span>
                    <span class="text-amber-500 font-semibold font-mono">SBWC: <?= number_format($totSbwc, 1) ?>h</span>
                </div>
            </div>

            <!-- 5. Total Well Job -->
            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between text-xs text-[var(--muted-foreground)] mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Total Well Job</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                </div>
                <div class="text-xl font-black text-purple-600 dark:text-purple-400 font-mono tracking-tight">
                    <?= number_format($totWell, 0, ',', '.') ?> <span class="text-xs font-semibold">Sumur</span>
                </div>
                <span class="text-[10px] text-[var(--muted-foreground)] mt-1">Total Sumur Selesai</span>
            </div>
        </div>

        <!-- GRAFIK 1: NPT ALL RIG (SBWC & UNPAID DOWNTIME PER RIG) -->
        <div id="cardChartNpt" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-sm overflow-hidden scroll-mt-6">
            <div class="p-4 sm:p-5 border-b border-[var(--border)] bg-[var(--card-elevated)]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-clock-rotate-left text-base"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h5 class="text-sm sm:text-base font-extrabold text-[var(--foreground)] tracking-tight">NPT ALL RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                            <?php if (($totSbwc + $totUnpaid) == 0): ?>
                                <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <i class="fa-solid fa-circle-check"></i> Zero Downtime
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-[var(--muted-foreground)]">Total Downtime Jam SBWC (Stand By With Crew) &amp; UNPAID per Armada Rig</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Segmented Mode Toggle: Kombinasi / Batang / Garis -->
                    <div class="inline-flex items-center p-0.5 rounded-lg bg-[var(--secondary)] border border-[var(--border)] text-xs">
                        <button type="button" onclick="setChartDisplayMode('npt', 'combo')" id="btnMode_npt_combo" class="px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]">Kombinasi</button>
                        <button type="button" onclick="setChartDisplayMode('npt', 'bar')" id="btnMode_npt_bar" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Batang</button>
                        <button type="button" onclick="setChartDisplayMode('npt', 'line')" id="btnMode_npt_line" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Garis</button>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-xs font-mono font-bold">
                        Unpaid: <?= number_format($totUnpaid, 2) ?> Jam
                    </span>
                    <span class="px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-mono font-bold">
                        SBWC: <?= number_format($totSbwc, 2) ?> Jam
                    </span>
                    <span class="px-2.5 py-1 rounded-xl bg-[var(--secondary)] text-[var(--foreground)] border border-[var(--border)] text-xs font-mono font-black">
                        Total: <?= number_format($totSbwc + $totUnpaid, 2) ?> Jam
                    </span>
                </div>
            </div>
            <div class="p-4 sm:p-6 bg-[var(--card)]">
                <?php if (($totSbwc + $totUnpaid) == 0): ?>
                    <div class="mb-4 p-3 rounded-xl bg-emerald-500/5 border border-emerald-500/20 flex items-center justify-between text-xs text-emerald-700 dark:text-emerald-300">
                        <span class="flex items-center gap-2 font-medium">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            Tidak ada catatan downtime (NPT) pada periode ini. Seluruh armada beroperasi optimal tanpa kehilangan waktu kerja.
                        </span>
                        <span class="font-mono font-bold">NPT: 0.00 Jam</span>
                    </div>
                <?php endif; ?>
                <div class="h-80 sm:h-96 w-full relative">
                    <canvas id="chartNptAllRig"></canvas>
                </div>
            </div>
        </div>

        <!-- GRAFIK 2: RAU ALL RIG (RELIABILITY & AVAILABILITY DENGAN UTILIZATION) -->
        <div id="cardChartRau" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-sm overflow-hidden scroll-mt-6">
            <div class="p-4 sm:p-5 border-b border-[var(--border)] bg-[var(--card-elevated)]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-gauge-high text-base"></i>
                    </div>
                    <div>
                        <h5 class="text-sm sm:text-base font-extrabold text-[var(--foreground)] tracking-tight">RAU ALL RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                        <p class="text-xs text-[var(--muted-foreground)]">Indikator Reliabilitas (%), Availability (%), &amp; Utilitas (%) Seluruh Rig</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Segmented Mode Toggle: Kombinasi / Batang / Garis -->
                    <div class="inline-flex items-center p-0.5 rounded-lg bg-[var(--secondary)] border border-[var(--border)] text-xs">
                        <button type="button" onclick="setChartDisplayMode('rau', 'combo')" id="btnMode_rau_combo" class="px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]">Kombinasi</button>
                        <button type="button" onclick="setChartDisplayMode('rau', 'bar')" id="btnMode_rau_bar" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Batang</button>
                        <button type="button" onclick="setChartDisplayMode('rau', 'line')" id="btnMode_rau_line" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Garis</button>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs font-mono font-bold">
                        Avg Rel: <?= number_format($avgReliability * 100, 2) ?>%
                    </span>
                    <span class="px-2.5 py-1 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 text-xs font-mono font-bold">
                        Avg Avail: <?= number_format($avgAvailability * 100, 2) ?>%
                    </span>
                    <span class="px-2.5 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-xs font-mono font-bold">
                        Avg Util: <?= number_format($avgUtilization * 100, 2) ?>%
                    </span>
                </div>
            </div>
            <div class="p-4 sm:p-6 bg-[var(--card)]">
                <div class="h-80 sm:h-96 w-full relative">
                    <canvas id="chartRauAllRig"></canvas>
                </div>
            </div>
        </div>

        <!-- GRID 2 KOLOM: AVERAGE MIRU & AVERAGE CYCLE TIME -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- GRAFIK 3: AVERAGE MIRU (HRS) -->
            <div id="cardChartMiru" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-sm overflow-hidden scroll-mt-6">
                <div class="p-4 sm:p-5 border-b border-[var(--border)] bg-[var(--card-elevated)]/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-truck-moving text-base"></i>
                        </div>
                        <div>
                            <h5 class="text-sm sm:text-base font-extrabold text-[var(--foreground)] tracking-tight">AVERAGE MIRU RIG BMS</h5>
                            <p class="text-xs text-[var(--muted-foreground)]">Durasi Moving In Rig Up (Jam/Sumur) Periode <?= $bulanList[$bulan] ?> <?= $tahun ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="inline-flex items-center p-0.5 rounded-lg bg-[var(--secondary)] border border-[var(--border)] text-xs">
                            <button type="button" onclick="setChartDisplayMode('miru', 'combo')" id="btnMode_miru_combo" class="px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]">Kombinasi</button>
                            <button type="button" onclick="setChartDisplayMode('miru', 'bar')" id="btnMode_miru_bar" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Batang</button>
                            <button type="button" onclick="setChartDisplayMode('miru', 'line')" id="btnMode_miru_line" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Garis</button>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 text-xs font-mono font-black">
                            Avg: <?= number_format($avgMiruAll, 2) ?> Jam
                        </span>
                    </div>
                </div>
                <div class="p-4 sm:p-6 bg-[var(--card)]">
                    <div class="h-72 sm:h-80 w-full relative">
                        <canvas id="chartAvgMiru"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 4: AVERAGE CYCLE TIME (HRS) -->
            <div id="cardChartCt" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-sm overflow-hidden scroll-mt-6">
                <div class="p-4 sm:p-5 border-b border-[var(--border)] bg-[var(--card-elevated)]/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-arrows-spin text-base"></i>
                        </div>
                        <div>
                            <h5 class="text-sm sm:text-base font-extrabold text-[var(--foreground)] tracking-tight">AVERAGE CYCLE TIME RIG BMS</h5>
                            <p class="text-xs text-[var(--muted-foreground)]">Durasi Rata-rata Operasi / Turnaround (Jam/Sumur)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="inline-flex items-center p-0.5 rounded-lg bg-[var(--secondary)] border border-[var(--border)] text-xs">
                            <button type="button" onclick="setChartDisplayMode('ct', 'combo')" id="btnMode_ct_combo" class="px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]">Kombinasi</button>
                            <button type="button" onclick="setChartDisplayMode('ct', 'bar')" id="btnMode_ct_bar" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Batang</button>
                            <button type="button" onclick="setChartDisplayMode('ct', 'line')" id="btnMode_ct_line" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Garis</button>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 text-xs font-mono font-black">
                            Avg: <?= number_format($avgCycleTimeAll, 2) ?> Jam
                        </span>
                    </div>
                </div>
                <div class="p-4 sm:p-6 bg-[var(--card)]">
                    <div class="h-72 sm:h-80 w-full relative">
                        <canvas id="chartAvgCycleTime"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- GRAFIK 5: TOTAL WELL JOB (SUMUR PER RIG) -->
        <div id="cardChartWell" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-sm overflow-hidden scroll-mt-6">
            <div class="p-4 sm:p-5 border-b border-[var(--border)] bg-[var(--card-elevated)]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-oil-well text-base"></i>
                    </div>
                    <div>
                        <h5 class="text-sm sm:text-base font-extrabold text-[var(--foreground)] tracking-tight">TOTAL WELL JOB RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                        <p class="text-xs text-[var(--muted-foreground)]">Jumlah Sumur yang Diselesaikan per Rig Periode <?= $bulanList[$bulan] ?> <?= $tahun ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="inline-flex items-center p-0.5 rounded-lg bg-[var(--secondary)] border border-[var(--border)] text-xs">
                        <button type="button" onclick="setChartDisplayMode('well', 'combo')" id="btnMode_well_combo" class="px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]">Kombinasi</button>
                        <button type="button" onclick="setChartDisplayMode('well', 'bar')" id="btnMode_well_bar" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Batang</button>
                        <button type="button" onclick="setChartDisplayMode('well', 'line')" id="btnMode_well_line" class="px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition">Garis</button>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-xs font-mono font-black">
                        Grand Total: <?= number_format($totWell, 0, ',', '.') ?> Sumur
                    </span>
                </div>
            </div>
            <div class="p-4 sm:p-6 bg-[var(--card)]">
                <div class="h-80 sm:h-96 w-full relative">
                    <canvas id="chartTotalWellJob"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ═══ MODAL PILIHAN EXPORT EXCEL (MONTHLY REPORT & BUNDLE) ══════ -->
<div id="exportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden transition-all duration-200">
    <div class="relative w-full max-w-xl rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-2xl p-6 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-2xl flex-shrink-0">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-white tracking-tight">Pilih Format Download Excel</h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Periode Laporan: <strong class="text-amber-400 font-semibold"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong> (Semua Armada Rig BMS)
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeExportModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition" title="Tutup (Esc)">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Opsi Pilihan Download -->
        <div class="space-y-3">
            <!-- OPSI 1: Monthly Report RAU (Matriks Bulanan Standar SYS) -->
            <a href="<?= base_url("export/monthly-report/{$bulan}/{$tahun}") ?>" onclick="closeExportModal()"
               class="group flex items-center justify-between p-4 rounded-2xl bg-slate-850 hover:bg-slate-800 border-2 border-slate-700 hover:border-emerald-500 transition-all duration-200 shadow-sm active:scale-[0.99]">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-200 flex-shrink-0">
                        <i class="fa-solid fa-table"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white group-hover:text-emerald-300 transition">Monthly Report RAU (Tabel Utama)</span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 border border-emerald-500/30 text-[10px] font-extrabold text-emerald-400 uppercase">Standar SYS</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                            Format Excel resmi: Sheet Summary Operation (Reliability, Availability, Utilization, ODR, Revenue) + NPT All Rig + Total Well.
                        </p>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-slate-800 group-hover:bg-emerald-500/20 text-slate-400 group-hover:text-emerald-400 flex items-center justify-center transition flex-shrink-0 ml-3">
                    <i class="fa-solid fa-download text-sm"></i>
                </div>
            </a>

            <!-- OPSI 2: Seluruh Pekerjaan Sumur Seluruh Rig (Daily Report All) -->
            <a href="<?= base_url("export/daily-report-all/{$bulan}/{$tahun}") ?>" onclick="closeExportModal()"
               class="group flex items-center justify-between p-4 rounded-2xl bg-slate-850 hover:bg-slate-800 border-2 border-slate-700 hover:border-cyan-500 transition-all duration-200 shadow-sm active:scale-[0.99]">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-xl group-hover:bg-cyan-500 group-hover:text-white transition-all duration-200 flex-shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white group-hover:text-cyan-300 transition">Pekerjaan Sumur Seluruh Rig (Daily Report All)</span>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/15 border border-cyan-500/30 text-[10px] font-extrabold text-cyan-400 uppercase">Multi-Sheet</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                            Rincian detail per sumur untuk setiap armada rig bulan <strong class="text-slate-200"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong> (Sheet per rig).
                        </p>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-slate-800 group-hover:bg-cyan-500/20 text-slate-400 group-hover:text-cyan-400 flex items-center justify-center transition flex-shrink-0 ml-3">
                    <i class="fa-solid fa-download text-sm"></i>
                </div>
            </a>

            <!-- OPSI 3: Paket Lengkap Seluruh Laporan (All in One Bundle) -->
            <a href="<?= base_url("export/bundle-all/{$bulan}/{$tahun}") ?>" onclick="closeExportModal()"
               class="group flex items-center justify-between p-4 rounded-2xl bg-gradient-to-r from-amber-500/10 to-blue-500/10 hover:from-amber-500/20 hover:to-blue-500/20 border-2 border-amber-500/40 hover:border-amber-400 transition-all duration-200 shadow-sm active:scale-[0.99]">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 flex items-center justify-center text-xl group-hover:bg-amber-500 group-hover:text-slate-950 transition-all duration-200 flex-shrink-0">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-amber-300 group-hover:text-amber-200 transition">Paket Lengkap Eksekutif (All-in-One)</span>
                            <span class="px-2 py-0.5 rounded-md bg-amber-500/25 border border-amber-500/40 text-[10px] font-extrabold text-amber-300 uppercase tracking-wider">Terlengkap</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5 leading-relaxed">
                            Download sekaligus: <strong class="text-white">Summary RAU</strong> + <strong class="text-white">Rekap Downtime NPT</strong> + <strong class="text-white">Rekap Pekerjaan Seluruh Sumur</strong> dalam 1 file Excel utuh.
                        </p>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 group-hover:bg-amber-500 group-hover:text-slate-950 flex items-center justify-center transition flex-shrink-0 ml-3">
                    <i class="fa-solid fa-download text-sm"></i>
                </div>
            </a>
        </div>

        <!-- Footer Modal -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span>Format Excel standar Besmindo (.xlsx)</span>
            </span>
            <button type="button" onclick="closeExportModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold transition">
                Tutup Jendela
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeExportModal();
        }
    });
    function navigateReport() {
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('monthly-report') ?>/${bulan}/${tahun}`;
    }

    function filterMonthlyRigRows() {
        const q = (document.getElementById('searchMonthlyRigInput')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.monthly-rig-row').forEach(tr => {
            const hay = (tr.getAttribute('data-rig') || '').toLowerCase();
            tr.style.display = (!q || hay.includes(q)) ? '' : 'none';
        });
    }

    let chartsInitialized = false;

    function switchTab(tabKey) {
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Remove active from all tabs (works for mr-tab-pill, ui-tab, and legacy tab-btn)
        document.querySelectorAll('.mr-tab-pill, .ui-tab, .tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-yellow-400', 'text-yellow-300', 'bg-slate-800');
            btn.classList.add('border-transparent');
        });

        // Show selected tab content
        const content = document.getElementById(`tabContent_${tabKey}`);
        if (content) content.classList.remove('hidden');

        // Activate selected tab button
        const activeBtn = document.getElementById(`tabBtn_${tabKey}`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('border-transparent');
        }

        if (tabKey === 'charts' && !chartsInitialized) {
            initMonthlyCharts();
            chartsInitialized = true;
        }
    }

    // Ekstraksi Data dari PHP
    <?php
    $labelsArray = [];
    $relArray    = [];
    $avaArray    = [];
    $utiArray    = [];
    $miruArray   = [];
    $ctArray     = [];
    $wellArray   = [];
    $sbwcArray   = [];
    $unpaidArray = [];
    $activeFlags = [];

    foreach ($summaries as $s) {
        $labelsArray[] = $s['kode'];
        $rRel          = round((float)($s['reliability'] ?? 0) * 100, 2);
        $rAva          = round((float)($s['availability'] ?? 0) * 100, 2);
        $rUti          = round((float)($s['utilization'] ?? 0) * 100, 2);
        $rMiru         = round((float)($s['avg_miru'] ?? 0), 2);
        $rCt           = round((float)($s['avg_cycle_time'] ?? 0), 2);
        $rWell         = (int)($s['total_well_job'] ?? 0);
        $rSbwc         = round((float)($s['sbwc_jam'] ?? 0), 2);
        $rUnpaid       = round((float)($s['unpaid_jam'] ?? 0), 2);
        $rOps          = (float)($s['total_ops'] ?? 0);

        $relArray[]    = $rRel;
        $avaArray[]    = $rAva;
        $utiArray[]    = $rUti;
        $miruArray[]   = $rMiru;
        $ctArray[]     = $rCt;
        $wellArray[]   = $rWell;
        $sbwcArray[]   = $rSbwc;
        $unpaidArray[] = $rUnpaid;

        // Rig dianggap aktif jika menyelesaikan sumur, memiliki jam operasi, atau mengalami downtime
        $activeFlags[] = ($rWell > 0 || $rOps > 0 || ($rSbwc + $rUnpaid) > 0);
    }
    ?>

    const fullChartData = {
        labels: <?= json_encode($labelsArray) ?>,
        rel:    <?= json_encode($relArray) ?>,
        ava:    <?= json_encode($avaArray) ?>,
        uti:    <?= json_encode($utiArray) ?>,
        miru:   <?= json_encode($miruArray) ?>,
        ct:     <?= json_encode($ctArray) ?>,
        well:   <?= json_encode($wellArray) ?>,
        sbwc:   <?= json_encode($sbwcArray) ?>,
        unpaid: <?= json_encode($unpaidArray) ?>,
        active: <?= json_encode($activeFlags) ?>
    };

    let currentChartFilter = 'all'; // 'all' or 'active'
    let chartInstances = {};

    function getChartThemeColors() {
        const isDark = document.documentElement.classList.contains('dark') ||
                       document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            isDark:         isDark,
            grid:           isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(15, 23, 42, 0.06)',
            ticks:          isDark ? '#94a3b8' : '#475569',
            legend:         isDark ? '#f1f5f9' : '#0f172a',
            tooltipBg:      isDark ? '#090d16' : '#0f172a',
            tooltipBorder:  isDark ? 'rgba(255, 255, 255, 0.15)' : 'rgba(255, 255, 255, 0.10)',
            tooltipTitle:   '#ffffff',
            tooltipBody:    '#e2e8f0',
            fontSans:       "'Geist', -apple-system, BlinkMacSystemFont, sans-serif",
            fontMono:       "'JetBrains Mono', monospace"
        };
    }

    function getFilteredData() {
        if (currentChartFilter === 'all') {
            return fullChartData;
        }
        const activeIndices = [];
        fullChartData.active.forEach((isActive, idx) => {
            if (isActive) activeIndices.push(idx);
        });

        // Jika tidak ada rig aktif sama sekali pada periode ini, fallback tampilkan semua
        if (activeIndices.length === 0) {
            return fullChartData;
        }

        return {
            labels: activeIndices.map(i => fullChartData.labels[i]),
            rel:    activeIndices.map(i => fullChartData.rel[i]),
            ava:    activeIndices.map(i => fullChartData.ava[i]),
            uti:    activeIndices.map(i => fullChartData.uti[i]),
            miru:   activeIndices.map(i => fullChartData.miru[i]),
            ct:     activeIndices.map(i => fullChartData.ct[i]),
            well:   activeIndices.map(i => fullChartData.well[i]),
            sbwc:   activeIndices.map(i => fullChartData.sbwc[i]),
            unpaid: activeIndices.map(i => fullChartData.unpaid[i]),
            active: activeIndices.map(i => fullChartData.active[i])
        };
    }

    function setChartFilter(mode) {
        currentChartFilter = mode;
        const btnAll    = document.getElementById('btnFilterChartAll');
        const btnActive = document.getElementById('btnFilterChartActive');

        if (btnAll && btnActive) {
            if (mode === 'all') {
                btnAll.className    = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs bg-[var(--card)] text-[var(--foreground)] border border-[var(--border)]';
                btnActive.className = 'px-3 py-1.5 rounded-lg text-xs font-medium text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-all';
            } else {
                btnActive.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs bg-[var(--card)] text-[var(--foreground)] border border-[var(--border)]';
                btnAll.className    = 'px-3 py-1.5 rounded-lg text-xs font-medium text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-all';
            }
        }

        updateAllChartsData();
    }

    function computeChartLines(d) {
        const totalNpt = d.unpaid.map((u, i) => Number(((u || 0) + (d.sbwc[i] || 0)).toFixed(2)));
        const sumNpt = totalNpt.reduce((a, b) => a + b, 0);
        const avgNpt = totalNpt.length ? Number((sumNpt / totalNpt.length).toFixed(2)) : 0;

        const sumMiru = d.miru.reduce((a, b) => a + b, 0);
        const avgMiru = d.miru.length ? Number((sumMiru / d.miru.length).toFixed(2)) : 0;

        const sumCt = d.ct.reduce((a, b) => a + b, 0);
        const avgCt = d.ct.length ? Number((sumCt / d.ct.length).toFixed(2)) : 0;

        const sumWell = d.well.reduce((a, b) => a + b, 0);
        const avgWell = d.well.length ? Number((sumWell / d.well.length).toFixed(1)) : 0;

        return {
            totalNpt,
            avgNpt,
            avgNptLine: Array(d.labels.length).fill(avgNpt),
            avgMiru,
            avgMiruLine: Array(d.labels.length).fill(avgMiru),
            avgCt,
            avgCtLine: Array(d.labels.length).fill(avgCt),
            avgWell,
            avgWellLine: Array(d.labels.length).fill(avgWell),
            targetKpiLine: Array(d.labels.length).fill(95.0)
        };
    }

    let chartDisplayModes = {
        npt: 'combo',
        rau: 'combo',
        miru: 'combo',
        ct: 'combo',
        well: 'combo'
    };

    function setChartDisplayMode(chartKey, mode) {
        chartDisplayModes[chartKey] = mode;
        const chart = chartInstances[chartKey];

        ['combo', 'bar', 'line'].forEach(m => {
            const btn = document.getElementById(`btnMode_${chartKey}_${m}`);
            if (btn) {
                if (m === mode) {
                    btn.className = 'px-2 py-0.5 rounded font-bold bg-[var(--card)] text-primary shadow-2xs border border-[var(--border)]';
                } else {
                    btn.className = 'px-2 py-0.5 rounded text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition';
                }
            }
        });

        if (!chart) return;

        chart.data.datasets.forEach(ds => {
            const isLine = ds.type === 'line';
            if (mode === 'combo') {
                ds.hidden = false;
            } else if (mode === 'bar') {
                ds.hidden = isLine;
            } else if (mode === 'line') {
                ds.hidden = !isLine;
            }
        });
        chart.update();
    }

    function updateAllChartsData() {
        if (!chartsInitialized) return;
        const d = getFilteredData();
        const lines = computeChartLines(d);

        if (chartInstances.npt) {
            chartInstances.npt.data.labels = d.labels;
            chartInstances.npt.data.datasets[0].data = d.unpaid;
            chartInstances.npt.data.datasets[1].data = d.sbwc;
            chartInstances.npt.data.datasets[2].data = lines.totalNpt;
            chartInstances.npt.data.datasets[3].data = lines.avgNptLine;
            chartInstances.npt.data.datasets[3].label = `Rata-rata Armada (${lines.avgNpt} Jam)`;
            chartInstances.npt.update();
            setChartDisplayMode('npt', chartDisplayModes.npt);
        }

        if (chartInstances.rau) {
            chartInstances.rau.data.labels = d.labels;
            chartInstances.rau.data.datasets[0].data = d.rel;
            chartInstances.rau.data.datasets[1].data = d.ava;
            chartInstances.rau.data.datasets[2].data = d.uti;
            chartInstances.rau.data.datasets[3].data = d.uti;
            chartInstances.rau.data.datasets[4].data = lines.targetKpiLine;
            chartInstances.rau.update();
            setChartDisplayMode('rau', chartDisplayModes.rau);
        }

        if (chartInstances.miru) {
            chartInstances.miru.data.labels = d.labels;
            chartInstances.miru.data.datasets[0].data = d.miru;
            chartInstances.miru.data.datasets[1].data = d.miru;
            chartInstances.miru.data.datasets[2].data = lines.avgMiruLine;
            chartInstances.miru.data.datasets[2].label = `Rata-rata Armada (${lines.avgMiru} Jam)`;
            chartInstances.miru.update();
            setChartDisplayMode('miru', chartDisplayModes.miru);
        }

        if (chartInstances.ct) {
            chartInstances.ct.data.labels = d.labels;
            chartInstances.ct.data.datasets[0].data = d.ct;
            chartInstances.ct.data.datasets[1].data = d.ct;
            chartInstances.ct.data.datasets[2].data = lines.avgCtLine;
            chartInstances.ct.data.datasets[2].label = `Rata-rata Armada (${lines.avgCt} Jam)`;
            chartInstances.ct.update();
            setChartDisplayMode('ct', chartDisplayModes.ct);
        }

        if (chartInstances.well) {
            chartInstances.well.data.labels = d.labels;
            chartInstances.well.data.datasets[0].data = d.well;
            chartInstances.well.data.datasets[1].data = d.well;
            chartInstances.well.data.datasets[2].data = lines.avgWellLine;
            chartInstances.well.data.datasets[2].label = `Rata-rata Armada (${lines.avgWell} Sumur)`;
            chartInstances.well.update();
            setChartDisplayMode('well', chartDisplayModes.well);
        }
    }

    function initMonthlyCharts() {
        const tc = getChartThemeColors();
        const d  = getFilteredData();
        const lines = computeChartLines(d);

        const commonTooltipConfig = {
            backgroundColor: tc.tooltipBg,
            titleColor:      tc.tooltipTitle,
            bodyColor:       tc.tooltipBody,
            borderColor:     tc.tooltipBorder,
            borderWidth:     1,
            padding:         12,
            cornerRadius:    10,
            boxPadding:      6,
            usePointStyle:   true,
            titleFont:       { family: tc.fontSans, size: 12, weight: 'bold' },
            bodyFont:        { family: tc.fontMono, size: 12 },
            footerFont:      { family: tc.fontMono, size: 11, weight: 'bold' }
        };

        const commonScaleTicks = {
            color: tc.ticks,
            font:  { size: 11, family: tc.fontMono }
        };

        const commonGrid = {
            color: tc.grid
        };

        // ══════════════════════════════════════════════════════════════════════
        // 1. CHART NPT ALL RIG (Combo Stacked Bar + Trendline & Fleet Average Line)
        // ══════════════════════════════════════════════════════════════════════
        const ctxNpt = document.getElementById('chartNptAllRig');
        if (ctxNpt) {
            chartInstances.npt = new Chart(ctxNpt, {
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'UNPAID (Jam)',
                            data: d.unpaid,
                            backgroundColor: 'rgba(244, 63, 94, 0.90)', // Crimson Rose
                            borderColor: '#e11d48',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            stack: 'Stack 0',
                            order: 2
                        },
                        {
                            type: 'bar',
                            label: 'SBWC (Jam)',
                            data: d.sbwc,
                            backgroundColor: 'rgba(245, 158, 11, 0.90)', // Warm Amber Honey
                            borderColor: '#d97706',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            stack: 'Stack 0',
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Garis Tren Total NPT (Jam)',
                            data: lines.totalNpt,
                            borderColor: '#e11d48',
                            backgroundColor: 'rgba(225, 29, 72, 0.08)',
                            borderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#e11d48',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            order: 0
                        },
                        {
                            type: 'line',
                            label: `Rata-rata Armada (${lines.avgNpt} Jam)`,
                            data: lines.avgNptLine,
                            borderColor: '#fb7185',
                            borderWidth: 1.8,
                            borderDash: [5, 4],
                            pointRadius: 0,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: tc.legend,
                                font: { size: 12, weight: 'bold', family: tc.fontSans },
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 16
                            }
                        },
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                footer: function(items) {
                                    const nptItem = items.find(it => it.dataset.type === 'line' && it.dataset.label.includes('Tren'));
                                    if (nptItem) {
                                        return `Total NPT: ${Number(nptItem.raw).toFixed(2)} Jam`;
                                    }
                                    let sum = 0;
                                    items.filter(it => it.dataset.type === 'bar').forEach(it => sum += it.parsed.y);
                                    return 'Total NPT: ' + sum.toFixed(2) + ' Jam';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: commonScaleTicks
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            suggestedMax: 10,
                            grid: commonGrid,
                            ticks: {
                                ...commonScaleTicks,
                                precision: 0,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // ══════════════════════════════════════════════════════════════════════
        // 2. CHART RAU ALL RIG (Combo Bar: Rel, Ava + Line: Utilisasi & Target 95%)
        // ══════════════════════════════════════════════════════════════════════
        const ctxRau = document.getElementById('chartRauAllRig');
        if (ctxRau) {
            chartInstances.rau = new Chart(ctxRau, {
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'RELIABILITY (%)',
                            data: d.rel,
                            backgroundColor: 'rgba(16, 185, 129, 0.90)', // Vibrant Emerald
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: { topLeft: 5, topRight: 5, bottomLeft: 0, bottomRight: 0 },
                            barPercentage: 0.85,
                            categoryPercentage: 0.82,
                            order: 2
                        },
                        {
                            type: 'bar',
                            label: 'AVAILABILITY (%)',
                            data: d.ava,
                            backgroundColor: 'rgba(14, 165, 233, 0.90)', // Radiant Sky Blue
                            borderColor: '#0284c7',
                            borderWidth: 1,
                            borderRadius: { topLeft: 5, topRight: 5, bottomLeft: 0, bottomRight: 0 },
                            barPercentage: 0.85,
                            categoryPercentage: 0.82,
                            order: 2
                        },
                        {
                            type: 'bar',
                            label: 'UTILIZATION (%)',
                            data: d.uti,
                            backgroundColor: 'rgba(99, 102, 241, 0.80)', // Royal Indigo
                            borderColor: '#4f46e5',
                            borderWidth: 1,
                            borderRadius: { topLeft: 5, topRight: 5, bottomLeft: 0, bottomRight: 0 },
                            barPercentage: 0.85,
                            categoryPercentage: 0.82,
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Garis Tren Utilisasi (%)',
                            data: d.uti,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.10)',
                            borderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            order: 0
                        },
                        {
                            type: 'line',
                            label: 'Target KPI (95%)',
                            data: lines.targetKpiLine,
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            pointRadius: 0,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: tc.legend,
                                font: { size: 12, weight: 'bold', family: tc.fontSans },
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 16
                            }
                        },
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(c) {
                                    return ` ${c.dataset.label}: ${Number(c.raw).toFixed(1)}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: commonScaleTicks
                        },
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 105,
                            grid: commonGrid,
                            ticks: {
                                ...commonScaleTicks,
                                callback: function(val) { return val + '%'; }
                            }
                        }
                    }
                }
            });
        }

        // ══════════════════════════════════════════════════════════════════════
        // 3. CHART AVERAGE MIRU (Combo Bar + Line Trend & Fleet Average)
        // ══════════════════════════════════════════════════════════════════════
        const ctxMiru = document.getElementById('chartAvgMiru');
        if (ctxMiru) {
            chartInstances.miru = new Chart(ctxMiru, {
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'AVERAGE MIRU (HRS)',
                            data: d.miru,
                            backgroundColor: 'rgba(37, 99, 235, 0.88)', // Cobalt Azure
                            borderColor: '#1d4ed8',
                            borderWidth: 1.5,
                            borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Garis Tren MIRU',
                            data: d.miru,
                            borderColor: '#1d4ed8',
                            borderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#1d4ed8',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            order: 0
                        },
                        {
                            type: 'line',
                            label: `Rata-rata Armada (${lines.avgMiru} Jam)`,
                            data: lines.avgMiruLine,
                            borderColor: '#60a5fa',
                            borderWidth: 1.8,
                            borderDash: [5, 4],
                            pointRadius: 0,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: tc.legend,
                                font: { size: 12, weight: 'bold', family: tc.fontSans },
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(c) {
                                    return ` ${c.dataset.label}: ${Number(c.raw).toFixed(2)} Jam/Sumur`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: commonScaleTicks
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 10,
                            grid: commonGrid,
                            ticks: {
                                ...commonScaleTicks,
                                precision: 0,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // ══════════════════════════════════════════════════════════════════════
        // 4. CHART AVERAGE CYCLE TIME (Combo Bar + Line Trend & Fleet Average)
        // ══════════════════════════════════════════════════════════════════════
        const ctxCt = document.getElementById('chartAvgCycleTime');
        if (ctxCt) {
            chartInstances.ct = new Chart(ctxCt, {
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'AVERAGE CYCLE TIME (HRS)',
                            data: d.ct,
                            backgroundColor: 'rgba(13, 148, 136, 0.88)', // Forest Teal
                            borderColor: '#0f766e',
                            borderWidth: 1.5,
                            borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Garis Tren Cycle Time',
                            data: d.ct,
                            borderColor: '#0f766e',
                            borderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0f766e',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            order: 0
                        },
                        {
                            type: 'line',
                            label: `Rata-rata Armada (${lines.avgCt} Jam)`,
                            data: lines.avgCtLine,
                            borderColor: '#2dd4bf',
                            borderWidth: 1.8,
                            borderDash: [5, 4],
                            pointRadius: 0,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: tc.legend,
                                font: { size: 12, weight: 'bold', family: tc.fontSans },
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(c) {
                                    return ` ${c.dataset.label}: ${Number(c.raw).toFixed(2)} Jam/Sumur`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: commonScaleTicks
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 24,
                            grid: commonGrid,
                            ticks: {
                                ...commonScaleTicks,
                                precision: 0,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // ══════════════════════════════════════════════════════════════════════
        // 5. CHART TOTAL WELL JOB (Combo Bar + Line Trend & Fleet Average)
        // ══════════════════════════════════════════════════════════════════════
        const ctxWell = document.getElementById('chartTotalWellJob');
        if (ctxWell) {
            chartInstances.well = new Chart(ctxWell, {
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'TOTAL WELL JOB',
                            data: d.well,
                            backgroundColor: 'rgba(124, 58, 237, 0.88)', // Royal Purple
                            borderColor: '#6d28d9',
                            borderWidth: 1.5,
                            borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Garis Tren Sumur',
                            data: d.well,
                            borderColor: '#6d28d9',
                            borderWidth: 2.5,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#6d28d9',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            order: 0
                        },
                        {
                            type: 'line',
                            label: `Rata-rata Armada (${lines.avgWell} Sumur)`,
                            data: lines.avgWellLine,
                            borderColor: '#a78bfa',
                            borderWidth: 1.8,
                            borderDash: [5, 4],
                            pointRadius: 0,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: tc.legend,
                                font: { size: 12, weight: 'bold', family: tc.fontSans },
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(c) {
                                    return ` ${c.dataset.label}: ${c.raw} Sumur`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: commonScaleTicks
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            grid: commonGrid,
                            ticks: {
                                ...commonScaleTicks,
                                precision: 0,
                                stepSize: 1,
                                callback: function(val) { return val + ' Sumur'; }
                            }
                        }
                    }
                }
            });
        }
    }

    // Sinkronisasi otomatis Chart.js saat user mengganti tema Light / Dark
    const chartThemeObserver = new MutationObserver(() => {
        if (!chartsInitialized) return;
        const tc = getChartThemeColors();

        Object.values(chartInstances).forEach(chart => {
            if (!chart || !chart.options) return;

            if (chart.options.scales) {
                Object.values(chart.options.scales).forEach(scale => {
                    if (scale.ticks) scale.ticks.color = tc.ticks;
                    if (scale.grid)  scale.grid.color  = tc.grid;
                });
            }

            if (chart.options.plugins?.legend?.labels) {
                chart.options.plugins.legend.labels.color = tc.legend;
            }

            if (chart.options.plugins?.tooltip) {
                chart.options.plugins.tooltip.backgroundColor = tc.tooltipBg;
                chart.options.plugins.tooltip.borderColor     = tc.tooltipBorder;
                chart.options.plugins.tooltip.titleColor       = tc.tooltipTitle;
                chart.options.plugins.tooltip.bodyColor        = tc.tooltipBody;
            }

            chart.update('none');
        });
    });

    chartThemeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme', 'class']
    });

    // Auto buka tab grafik jika ada hash #tabContent_charts atau query parameter ?tab=charts
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (window.location.hash.includes('charts') || urlParams.get('tab') === 'charts') {
            switchTab('charts');
        }
    });
</script>
<?= $this->endSection() ?>
