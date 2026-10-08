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
if ($prevBulan < 1) { $prevBulan = 12; $prevTahun--; }

$nextBulan = $bulan + 1;
$nextTahun = $tahun;
if ($nextBulan > 12) { $nextBulan = 1; $nextTahun++; }

$exportExcelUrl = base_url("export/npt-all/{$bulan}/{$tahun}");
?>

<style>
/* ═══ Dual-Theme Surface & Typography Tokens for Clean /npt ═══ */
.npt-clean-card {
    background-color: var(--card) !important;
    border: 1px solid var(--border) !important;
    border-radius: 12px;
    box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.04);
}
.npt-sub-panel {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    border-radius: 8px;
}
.npt-control-box {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    color: var(--foreground) !important;
    border-radius: 6px;
    transition: all 0.15s ease;
}
.npt-control-box:focus {
    border-color: #0284c7 !important;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.npt-tab-switch {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    padding: 3px;
    border-radius: 10px;
    display: inline-flex;
    gap: 3px;
}
.npt-switch-btn {
    padding: 6px 14px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.15s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--muted-foreground);
    border: 1px solid transparent;
    text-decoration: none;
}
.npt-switch-btn:hover {
    color: var(--foreground);
}
.npt-switch-btn.is-active {
    background-color: var(--card) !important;
    color: var(--foreground) !important;
    border-color: var(--border) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

/* Badge Styling */
.badge-unpaid {
    background-color: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.25);
}
.badge-sbwc {
    background-color: rgba(14, 165, 233, 0.12);
    color: #0284c7;
    border: 1px solid rgba(14, 165, 233, 0.25);
}
:root[data-theme="dark"] .badge-sbwc {
    color: #38bdf8;
}
.badge-neutral {
    background-color: rgba(148, 163, 184, 0.12);
    color: var(--muted-foreground);
    border: 1px solid var(--border);
}

/* ═══ Ultra-Compact Table Architecture (Excel-Density Scaled Down) ═══ */
/* ═══ Calibrated Table Architecture (Slightly Larger & Ultra-Clear) ═══ */
.excel-table-container {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid var(--border);
    background-color: var(--card);
    transition: all 0.15s ease;
    transform-origin: top left;
}

/* Base Table Rules */
.excel-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 9.5px;
    line-height: 1.3;
    font-variant-numeric: tabular-nums;
}
.excel-table th, .excel-table td {
    padding: 3px 5px !important;
    white-space: nowrap;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
    height: 24px !important;
}
.excel-table th {
    background-color: var(--secondary);
    color: var(--foreground);
    font-weight: 800;
    text-align: center;
    font-size: 9px;
    letter-spacing: 0.01em;
    line-height: 1.15;
}
.excel-table th.th-unpaid {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: #dc2626 !important;
    font-size: 9px;
}
:root[data-theme="dark"] .excel-table th.th-unpaid {
    color: #f87171 !important;
}
.excel-table th.th-sbwc {
    background-color: rgba(2, 132, 199, 0.09) !important;
    color: #0369a1 !important;
    font-size: 9px;
}
:root[data-theme="dark"] .excel-table th.th-sbwc {
    color: #38bdf8 !important;
}
.excel-table th.th-total {
    background-color: rgba(245, 158, 11, 0.12) !important;
    color: #b45309 !important;
    font-size: 9.5px;
}
:root[data-theme="dark"] .excel-table th.th-total {
    color: #fbbf24 !important;
}

/* ═══ 3-Tier Header & 3RD PARTY Nama Besar Tokens ═══ */
.excel-table th.th-unpaid-header {
    background-color: rgba(239, 68, 68, 0.14) !important;
    color: #dc2626 !important;
    border-bottom: 2px solid #ef4444 !important;
    font-weight: 900;
    font-size: 9px;
    letter-spacing: 0.03em;
    padding: 2.5px 4px !important;
}
:root[data-theme="dark"] .excel-table th.th-unpaid-header {
    color: #f87171 !important;
}
.excel-table th.th-sbwc-header {
    background-color: rgba(2, 132, 199, 0.10) !important;
    color: #0369a1 !important;
    border-bottom: 2px solid #0284c7 !important;
    font-weight: 900;
    font-size: 9px;
    letter-spacing: 0.03em;
    padding: 2.5px 4px !important;
}
:root[data-theme="dark"] .excel-table th.th-sbwc-header {
    color: #38bdf8 !important;
}

/* 3RD PARTY NAMA BESAR */
.excel-table th.th-tp-nama-besar {
    background: linear-gradient(180deg, rgba(2, 132, 199, 0.16) 0%, rgba(2, 132, 199, 0.28) 100%) !important;
    color: #0369a1 !important;
    border-top: 1.5px solid #0284c7 !important;
    border-bottom: 1.5px solid #0284c7 !important;
    letter-spacing: 0.03em;
    font-weight: 900 !important;
    font-size: 9px !important;
    text-transform: uppercase;
    padding: 2px 5px !important;
}
:root[data-theme="dark"] .excel-table th.th-tp-nama-besar {
    background: linear-gradient(180deg, rgba(2, 132, 199, 0.25) 0%, rgba(2, 132, 199, 0.4) 100%) !important;
    color: #38bdf8 !important;
    border-color: #38bdf8 !important;
}
.excel-table th.th-tp-vendor {
    background-color: rgba(2, 132, 199, 0.06) !important;
    color: #0284c7 !important;
    font-weight: 800 !important;
    font-size: 8.5px !important;
    letter-spacing: 0;
    padding: 2px 3px !important;
    border-bottom: 1px solid var(--border) !important;
    min-width: 30px !important;
}
:root[data-theme="dark"] .excel-table th.th-tp-vendor {
    color: #7dd3fc !important;
}

/* ═══ Penanganan Tuntas Cell Overflow Remark ═══ */
.excel-table td.td-remark,
.excel-table th.th-remark {
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: anywhere !important;
    max-width: 190px;
    min-width: 125px;
    padding: 2px 4px !important;
}
.remark-bubble {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 1.5px 6px;
    border-radius: 4px;
    background-color: var(--secondary);
    border: 1px solid var(--border);
    cursor: pointer;
    transition: all 0.12s ease;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 185px;
    height: 20px;
    font-size: 9px;
}
.remark-bubble:hover {
    border-color: #0284c7;
    background-color: rgba(2, 132, 199, 0.08);
}
.excel-table tbody tr {
    transition: background-color 0.1s ease;
    height: 24px !important;
}
.excel-table tbody tr:hover {
    background-color: rgba(2, 132, 199, 0.06);
}
.excel-table td.col-sticky-1,
.excel-table th.col-sticky-1 {
    position: sticky;
    left: 0;
    z-index: 10;
    width: 28px;
    min-width: 28px;
    max-width: 28px;
    background-color: var(--card);
    text-align: center;
    padding: 2px 2px !important;
    font-size: 9px;
}
.excel-table th.col-sticky-1 {
    z-index: 25;
    background-color: var(--secondary);
}
.excel-table td.col-sticky-2,
.excel-table th.col-sticky-2 {
    position: sticky;
    left: 28px;
    z-index: 10;
    background-color: var(--card);
    font-weight: 800;
    width: 70px;
    min-width: 70px;
    max-width: 80px;
    padding: 2px 5px !important;
    font-size: 9px;
}
.excel-table th.col-sticky-2 {
    z-index: 25;
    background-color: var(--secondary);
}
.excel-table tr.total-row td {
    background-color: rgba(245, 158, 11, 0.12) !important;
    font-weight: 800;
    border-top: 1.5px solid #f59e0b;
    border-bottom: 1.5px solid #f59e0b;
    font-size: 9.5px;
}

/* ═══ Interactive Scale / Density Modes (Mini 88%, Kompak 100%, Normal 112%) ═══ */
.table-scale-mini {
    zoom: 0.88;
}
.table-scale-mini .excel-table {
    font-size: 8.5px !important;
}
.table-scale-mini .excel-table th, 
.table-scale-mini .excel-table td {
    padding: 2px 3px !important;
    height: 20px !important;
}

.table-scale-compact {
    zoom: 1.0;
}

.table-scale-normal {
    zoom: 1.12;
}
.table-scale-normal .excel-table {
    font-size: 10.5px !important;
}
.table-scale-normal .excel-table th, 
.table-scale-normal .excel-table td {
    padding: 3.5px 6px !important;
    height: 26px !important;
}

/* Rig Pills Scroll Container */
.rig-pills-scroll {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 3px;
    scrollbar-width: thin;
}
.rig-pill-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
    border: 1px solid var(--border);
    background-color: var(--card);
    color: var(--muted-foreground);
    transition: all 0.14s ease;
    text-decoration: none;
}
.rig-pill-item:hover {
    color: var(--foreground);
    border-color: #0284c7;
}
.rig-pill-item.is-selected {
    background-color: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);
}

/* ═══ Modal Styling (Non-Scrollable & Focused Dead-Center) ═══ */
.npt-modal-backdrop {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(5px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    overflow: hidden !important;
    touch-action: none;
    overscroll-behavior: contain;
}
.npt-modal-backdrop.is-open {
    opacity: 1;
    pointer-events: auto;
}
.npt-modal-box {
    background-color: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
    max-width: 530px;
    width: 100%;
    transform: translateY(0);
    transition: transform 0.2s ease;
    overflow: hidden !important;
    margin: auto;
}
</style>

<div class="space-y-3 pb-8">
    <!-- ══════════════════════════════════════════════════════════════ -->
    <!-- HEADER RESMI: TITLE, PERIODE, EXPORT, & MODAL BUTTON -->
    <!-- ══════════════════════════════════════════════════════════════ -->
    <div class="npt-clean-card p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="w-7 h-7 rounded-lg bg-sky-500/15 text-sky-500 flex items-center justify-center font-black text-xs">
                    <i class="fa-solid fa-file-excel"></i>
                </span>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight text-[var(--foreground)]">
                        Informasi NPT Bulanan Rig BMS
                    </h1>
                    <p class="text-[11px] text-[var(--muted-foreground)]">
                        Format Buku Kerja Operasional Resmi (Rekap Seluruh Rig &amp; Lembar Per Rig)
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Navigasi Periode Bulan & Tahun -->
            <div class="inline-flex items-center gap-1 npt-sub-panel px-2 py-1">
                <a href="<?= base_url("npt/{$rigId}/{$prevBulan}/{$prevTahun}?view={$activeView}") ?>"
                    class="w-6 h-6 rounded flex items-center justify-center text-[11px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)] transition"
                    title="Bulan Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

                <form method="GET" action="" class="flex items-center gap-1 m-0" id="periodFilterForm">
                    <input type="hidden" name="view" value="<?= esc($activeView) ?>">
                    <select name="bulan" onchange="submitPeriodChange(this.value, document.getElementById('periodTahun').value)"
                        id="periodBulan" class="npt-control-box px-2 py-0.5 text-xs font-bold cursor-pointer">
                        <?php foreach ($bulanList as $k => $namaBulan): ?>
                        <option value="<?= $k ?>" <?= $k == $bulan ? 'selected' : '' ?>>
                            <?= $namaBulan ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="tahun" onchange="submitPeriodChange(document.getElementById('periodBulan').value, this.value)"
                        id="periodTahun" class="npt-control-box px-2 py-0.5 text-xs font-bold font-mono cursor-pointer">
                        <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </form>

                <a href="<?= base_url("npt/{$rigId}/{$nextBulan}/{$nextTahun}?view={$activeView}") ?>"
                    class="w-6 h-6 rounded flex items-center justify-center text-[11px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)] transition"
                    title="Bulan Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>

            <!-- Tombol Catat NPT Cepat -->
            <button type="button" onclick="openNptEventModal(<?= $rigId ?>)"
                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Catat NPT</span>
            </button>

            <!-- Tombol Export Excel Resmi -->
            <a href="<?= $exportExcelUrl ?>"
                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-xs transition flex items-center gap-1.5"
                title="Unduh Workbook Excel Lengkap persis NPT SEPTEMBER 2026.xlsx">
                <i class="fa-solid fa-file-excel text-[10px]"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════ -->
    <!-- SWITCHER TAMPILAN ELEGAN: SUMMARY ALL RIG VS RINCIAN PER RIG -->
    <!-- ══════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <div class="npt-tab-switch">
            <button type="button" onclick="switchMainView('summary')" id="tabBtnSummary"
                class="npt-switch-btn <?= $activeView === 'summary' ? 'is-active' : '' ?>">
                <i class="fa-solid fa-table-cells text-sky-500 text-[11px]"></i>
                <span>SUMMARY MONTH ALL RIG</span>
            </button>
            <button type="button" onclick="switchMainView('rig')" id="tabBtnRig"
                class="npt-switch-btn <?= $activeView === 'rig' ? 'is-active' : '' ?>">
                <i class="fa-solid fa-oil-well text-amber-500 text-[11px]"></i>
                <span>RINCIAN PER RIG (BMS 02 - 21)</span>
            </button>
        </div>

        <div class="text-[11px] text-[var(--muted-foreground)] flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Sinkronisasi otomatis dengan Daily Report &amp; Rekap Performa Bulanan</span>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════ -->
    <!-- VIEW 1: SUMMARY MONTH ALL RIG (PERSIS EXCEL) -->
    <!-- ══════════════════════════════════════════════════════════════ -->
    <div id="viewSummaryAllRig" class="space-y-3 <?= $activeView === 'summary' ? '' : 'hidden' ?>">
        <!-- 4 KPI Badges Rekapitulasi Armada (Compact) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="npt-clean-card p-2.5 sm:p-3">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-[var(--muted-foreground)] block mb-0.5">
                    Total Downtime Armada
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg sm:text-xl font-black font-mono text-[var(--foreground)]">
                        <?= number_format($fleetTotalHrs, 2) ?>
                    </span>
                    <span class="text-[10px] font-bold text-[var(--muted-foreground)]">Jam</span>
                </div>
                <span class="text-[9.5px] text-[var(--muted-foreground)] mt-0.5 block">
                    Seluruh 18 Armada Rig BMS
                </span>
            </div>

            <div class="npt-clean-card p-2.5 sm:p-3 border-l-4 !border-l-rose-500">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-rose-500 block mb-0.5">
                    Unpaid (Tanggung Jawab Rig)
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg sm:text-xl font-black font-mono text-rose-500">
                        <?= number_format($fleetTotalUnpaid, 2) ?>
                    </span>
                    <span class="text-[10px] font-bold text-rose-500">Jam</span>
                </div>
                <span class="text-[9.5px] text-[var(--muted-foreground)] mt-0.5 block">
                    <?= $fleetTotalHrs > 0 ? number_format(($fleetTotalUnpaid / $fleetTotalHrs) * 100, 1) : 0 ?>% dari total downtime
                </span>
            </div>

            <div class="npt-clean-card p-2.5 sm:p-3 border-l-4 !border-l-sky-500">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-sky-500 block mb-0.5">
                    SBWC (Standby with Crew)
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg sm:text-xl font-black font-mono text-sky-500">
                        <?= number_format($fleetTotalSbwc, 2) ?>
                    </span>
                    <span class="text-[10px] font-bold text-sky-500">Jam</span>
                </div>
                <span class="text-[9.5px] text-[var(--muted-foreground)] mt-0.5 block">
                    Tanggung Jawab Client &amp; Vendor
                </span>
            </div>

            <div class="npt-clean-card p-2.5 sm:p-3 border-l-4 !border-l-amber-500">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-amber-500 block mb-0.5">
                    Downtime Tertinggi Bulan Ini
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-base sm:text-lg font-black text-amber-500 truncate">
                        <?= esc($topRigName) ?>
                    </span>
                    <span class="text-[11px] font-bold font-mono text-amber-500">
                        (<?= number_format($topRigHrs, 2) ?>j)
                    </span>
                </div>
                <span class="text-[9.5px] text-[var(--muted-foreground)] mt-0.5 block">
                    Klik baris rig untuk rincian
                </span>
            </div>
        </div>

        <!-- Tabel Rekapitulasi 18 Rig Persis Sheet SUMMARY MONTH ALL RIG -->
        <div class="npt-clean-card p-2.5 sm:p-3 space-y-2">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1.5 border-b border-[var(--border)]">
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-[var(--foreground)]">
                        <?= esc($monthData['title'] ?? "DOWN TIME RIG BMS PERIOD " . strtoupper($bulanList[$bulan] ?? '') . " {$tahun}") ?>
                    </h2>
                    <p class="text-[10px] text-[var(--muted-foreground)]">
                        Klik pada baris rig mana pun untuk membuka lembar kerja rincian rig tersebut.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    <!-- Zoom / Scale Controller Langsung di Toolbar -->
                    <div class="inline-flex items-center gap-0.5 bg-[var(--secondary)] p-0.5 rounded-lg border border-[var(--border)]">
                        <span class="text-[8.5px] uppercase px-1 text-[var(--muted-foreground)] font-extrabold flex items-center gap-1">
                            <i class="fa-solid fa-compress text-[7.5px]"></i>Skala:
                        </span>
                        <button type="button" onclick="setNptTableScale('mini')" id="btnScaleMiniSummary"
                            class="scale-btn-mini px-1.5 py-0.5 rounded text-[9px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition cursor-pointer"
                            title="Mode Skala Mini (88% Zoom)">
                            Mini
                        </button>
                        <button type="button" onclick="setNptTableScale('compact')" id="btnScaleCompactSummary"
                            class="scale-btn-compact px-1.5 py-0.5 rounded text-[9px] font-bold bg-[var(--card)] text-sky-500 shadow-xs border border-[var(--border)] transition cursor-pointer"
                            title="Mode Skala Kompak Standar (100% Zoom)">
                            Kompak
                        </button>
                        <button type="button" onclick="setNptTableScale('normal')" id="btnScaleNormalSummary"
                            class="scale-btn-normal px-1.5 py-0.5 rounded text-[9px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition cursor-pointer"
                            title="Mode Skala Diperbesar (112% Zoom)">
                            Normal
                        </button>
                    </div>

                    <input type="text" id="filterRigSummaryInput" placeholder="Cari nama rig..." oninput="filterSummaryRows(this.value)"
                        class="npt-control-box px-2 py-0.5 text-[11px] w-28 sm:w-36 font-bold">
                    <button type="button" onclick="toggleZeroSummaryRows()" id="btnToggleZeroRows"
                        class="px-2 py-0.5 rounded-lg text-[10px] font-bold border border-[var(--border)] hover:bg-[var(--secondary)] transition cursor-pointer">
                        Semua (18)
                    </button>
                </div>
            </div>

            <!-- Tabel Excel 34 Kolom Persis Excel (Calibrated & Clean) -->
            <div class="excel-table-container">
                <table class="excel-table" id="tableSummaryMonth">
                    <thead>
                        <!-- Baris Header 1: Parent Macro Groups -->
                        <tr>
                            <th rowspan="3" class="col-sticky-1">NO</th>
                            <th rowspan="3" class="col-sticky-2">NAME RIG</th>
                            <th colspan="2" class="th-unpaid-header">UNPAID</th>
                            <th colspan="27" class="th-sbwc-header">STAND BY WITH CREW ( SBWC )</th>
                            <th rowspan="3" class="th-total min-w-[46px]">TOTAL<br>(HRS)</th>
                            <th rowspan="3" class="th-remark min-w-[125px] max-w-[190px] text-left">REMARK UNPAID</th>
                        </tr>
                        <!-- Baris Header 2: Sub-Groups & 3RD PARTY NAMA BESAR -->
                        <tr>
                            <!-- UNPAID (Cols 2, 3) -->
                            <th rowspan="2" class="th-unpaid min-w-[36px]">Repair<br>Rig</th>
                            <th rowspan="2" class="th-unpaid min-w-[34px]">Perso-<br>nel</th>

                            <!-- SBWC Langsung (Cols 4..11) -->
                            <th rowspan="2" class="th-sbwc min-w-[32px]">SWA<br>Rain</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">Dry<br>Road</th>
                            <th rowspan="2" class="th-sbwc min-w-[30px]">Dry<br>Pad</th>
                            <th rowspan="2" class="th-sbwc min-w-[34px]">Day-<br>light</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">CHAST</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">CE/PE</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">PHR<br>Well</th>
                            <th rowspan="2" class="th-sbwc min-w-[28px]">ESP</th>

                            <!-- 3RD PARTY NAMA BESAR (Cols 12..21 = Colspan 10) -->
                            <th colspan="10" class="th-tp-nama-besar">
                                <div class="inline-flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-handshake text-sky-500 text-[8.5px]"></i>
                                    <span>3RD PARTY</span>
                                    <span class="text-[7.5px] px-1 py-0 rounded bg-sky-500/15 text-sky-600 font-mono font-bold">(10 VENDOR)</span>
                                </div>
                            </th>

                            <!-- SBWC Operasional Lainnya (Cols 22..31) -->
                            <th rowspan="2" class="th-sbwc min-w-[32px]">UNISAT</th>
                            <th rowspan="2" class="th-sbwc min-w-[30px]">TRANS</th>
                            <th rowspan="2" class="th-sbwc min-w-[30px]">FOAM</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">Dec<br>LSC</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">PEMILU</th>
                            <th rowspan="2" class="th-sbwc min-w-[28px]">OMS</th>
                            <th rowspan="2" class="th-sbwc min-w-[28px]">PCM</th>
                            <th rowspan="2" class="th-sbwc min-w-[28px]">COSL</th>
                            <th rowspan="2" class="th-sbwc min-w-[32px]">SAFARI</th>
                            <th rowspan="2" class="th-sbwc min-w-[34px]">IDUL<br>FITRI</th>
                        </tr>
                        <!-- Baris Header 3: 10 Sub-Kolom Vendor Eksklusif di Bawah 3RD PARTY -->
                        <tr>
                            <th class="th-tp-vendor min-w-[29px]">BHI</th>
                            <th class="th-tp-vendor min-w-[29px]">HLS</th>
                            <th class="th-tp-vendor min-w-[27px]">WI</th>
                            <th class="th-tp-vendor min-w-[32px]">HALCO</th>
                            <th class="th-tp-vendor min-w-[29px]">EJP</th>
                            <th class="th-tp-vendor min-w-[32px]">SCHL</th>
                            <th class="th-tp-vendor min-w-[29px]">MGA</th>
                            <th class="th-tp-vendor min-w-[29px]">SGN</th>
                            <th class="th-tp-vendor min-w-[33px]">BUKAKA</th>
                            <th class="th-tp-vendor min-w-[29px]">PESI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($monthData['rows'])): ?>
                        <?php foreach ($monthData['rows'] as $idx => $r): ?>
                        <?php
                            $rowVals = $r['vals'] ?? [];
                            $rRigCode = trim($r['rig'] ?? '');
                            $rTot = (float)($rowVals[32] ?? 0);
                            $rUnp = (float)($rowVals[2] ?? 0) + (float)($rowVals[3] ?? 0);
                            $rRem = $rowVals[33] ?? '-';
                            // Temukan rig ID yang cocok untuk link navigasi
                            $matchingRigId = $rigId;
                            foreach ($allRigs as $ar) {
                                if (trim(str_replace('#', ' ', $ar['kode'])) === $rRigCode || trim($ar['kode']) === $rRigCode) {
                                    $matchingRigId = $ar['id'];
                                    break;
                                }
                            }
                        ?>
                        <tr class="summary-rig-row cursor-pointer" onclick="selectRigView(<?= $matchingRigId ?>)"
                            data-rig-name="<?= esc(strtolower($rRigCode)) ?>" data-has-dt="<?= $rTot > 0 ? '1' : '0' ?>">
                            <td class="col-sticky-1 text-center font-mono text-[var(--muted-foreground)]">
                                <?= $r['no'] ?? ($idx + 1) ?>
                            </td>
                            <td class="col-sticky-2 text-sky-500 hover:text-sky-400 font-bold">
                                <div class="flex items-center justify-between w-full leading-none">
                                    <span><?= esc($rRigCode) ?></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[7px] opacity-60 ml-0.5"></i>
                                </div>
                            </td>

                            <!-- UNPAID (Cols 2, 3) -->
                            <?php for ($c = 2; $c <= 3; $c++): ?>
                            <?php $val = (float)($rowVals[$c] ?? 0); ?>
                            <td class="text-right font-mono <?= $val > 0 ? 'text-rose-500 font-black bg-rose-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $val > 0 ? number_format($val, 2) : '-' ?>
                            </td>
                            <?php endfor; ?>

                            <!-- SBWC (Cols 4..31) -->
                            <?php for ($c = 4; $c <= 31; $c++): ?>
                            <?php $val = (float)($rowVals[$c] ?? 0); ?>
                            <td class="text-right font-mono <?= $val > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $val > 0 ? number_format($val, 2) : '-' ?>
                            </td>
                            <?php endfor; ?>

                            <!-- TOTAL (HRS) (Col 32) -->
                            <td class="text-right font-mono font-black text-amber-500 bg-amber-500/5">
                                <?= $rTot > 0 ? number_format($rTot, 2) : '-' ?>
                            </td>

                            <!-- REMARK UNPAID (Col 33) -->
                            <td class="text-left td-remark">
                                <?php if ($rRem && $rRem !== '-'): ?>
                                <div class="remark-bubble" onclick="openRemarkModal('<?= esc(addslashes($rRigCode)) ?>', '<?= esc($bulanList[$bulan] ?? '') ?> <?= $tahun ?>', '<?= esc(addslashes($rRem)) ?>', '<?= number_format($rTot, 2) ?> Jam (Downtime Total)')" title="Klik untuk membaca catatan lengkap">
                                    <?php if ($rUnp > 0): ?>
                                    <span class="badge-unpaid text-[7px] px-0.5 py-0 rounded font-mono font-bold shrink-0">UNPAID</span>
                                    <?php else: ?>
                                    <span class="badge-sbwc text-[7px] px-0.5 py-0 rounded font-mono font-bold shrink-0">SBWC</span>
                                    <?php endif; ?>
                                    <span class="text-[8px] text-[var(--foreground)] truncate font-medium"><?= esc($rRem) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="opacity-25 font-mono text-[8px] pl-0.5">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <!-- Baris Total Persis Excel -->
                        <?php $totals = $monthData['totals'] ?? []; ?>
                        <tr class="total-row">
                            <td class="col-sticky-1 text-center font-black">TOTAL</td>
                            <td class="col-sticky-2 text-center font-black">ALL RIG</td>

                            <!-- UNPAID Totals (Cols 2, 3) -->
                            <?php for ($c = 2; $c <= 3; $c++): ?>
                            <?php $val = (float)($totals[$c] ?? 0); ?>
                            <td class="text-right font-mono font-black text-rose-500">
                                <?= number_format($val, 2) ?>
                            </td>
                            <?php endfor; ?>

                            <!-- SBWC Totals (Cols 4..31) -->
                            <?php for ($c = 4; $c <= 31; $c++): ?>
                            <?php $val = (float)($totals[$c] ?? 0); ?>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= $val > 0 ? number_format($val, 2) : '-' ?>
                            </td>
                            <?php endfor; ?>

                            <!-- GRAND TOTAL (Col 32) -->
                            <td class="text-right font-mono font-black text-amber-500">
                                <?= number_format((float)($totals[32] ?? 0), 2) ?>
                            </td>

                            <td class="text-left font-bold td-remark text-[var(--muted-foreground)] text-[8px]">
                                Total Rekapitulasi Armada
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════ -->
    <!-- VIEW 2: RINCIAN PER RIG (LEMBAR RIG PERSIS EXCEL) -->
    <!-- ══════════════════════════════════════════════════════════════ -->
    <div id="viewRigDetailSheet" class="space-y-3 <?= $activeView === 'rig' ? '' : 'hidden' ?>">
        <!-- Rig Selector Carousel (18 Rig Pills - Compact) -->
        <div class="npt-clean-card p-2 sm:p-2.5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-[var(--muted-foreground)]">
                    Pilih Lembar Rig:
                </span>
                <span class="text-[10px] text-[var(--muted-foreground)] font-mono">
                    Total: <strong class="text-sky-500">18 Rig</strong>
                </span>
            </div>
            <div class="rig-pills-scroll">
                <?php foreach ($rigBadges as $rb): ?>
                <?php $isSelected = ($rb['id'] == $rigId); ?>
                <a href="<?= base_url("npt/{$rb['id']}/{$bulan}/{$tahun}?view=rig") ?>"
                    class="rig-pill-item <?= $isSelected ? 'is-selected' : '' ?>">
                    <span><?= esc($rb['clean_code']) ?></span>
                    <span class="text-[9px] px-1 py-0 rounded-full font-mono <?= $isSelected ? 'bg-white/20 text-white' : ($rb['total_hrs'] > 0 ? 'bg-sky-500/15 text-sky-500 font-bold' : 'opacity-40') ?>">
                        <?= number_format($rb['total_hrs'], 1) ?>j
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Rig Sheet Header Banner & Mini Stats (Compact) -->
        <div class="npt-clean-card p-2.5 sm:p-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-sky-500/15 text-sky-500 flex items-center justify-center font-black text-sm">
                    <i class="fa-solid fa-oil-well"></i>
                </span>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-[var(--foreground)]" id="activeRigTitle">
                        DOWN TIME RIG <?= esc(str_replace('#', ' ', $rig['kode'])) ?> PERIODE <?= strtoupper($bulanList[$bulan] ?? '') ?> <?= $tahun ?>
                    </h2>
                    <p class="text-[11px] text-[var(--muted-foreground)]">
                        Lembar Kerja Downtime Operasional &amp; Catatan Kejadian Harian
                    </p>
                </div>
            </div>

            <!-- Mini Summary Rig Ini -->
            <div class="flex flex-wrap items-center gap-1.5">
                <div class="npt-sub-panel px-2.5 py-1 text-center">
                    <span class="text-[8.5px] uppercase font-bold text-[var(--muted-foreground)] block">Total NPT</span>
                    <span class="text-xs font-black font-mono text-amber-500" id="statRigTotalHrs">
                        <?= number_format($selectedRigTotalHrs, 2) ?>j
                    </span>
                </div>
                <div class="npt-sub-panel px-2.5 py-1 text-center">
                    <span class="text-[8.5px] uppercase font-bold text-rose-500 block">Unpaid</span>
                    <span class="text-xs font-black font-mono text-rose-500" id="statRigUnpaidHrs">
                        <?= number_format($selectedRigUnpaid, 2) ?>j
                    </span>
                </div>
                <div class="npt-sub-panel px-2.5 py-1 text-center">
                    <span class="text-[8.5px] uppercase font-bold text-sky-500 block">SBWC</span>
                    <span class="text-xs font-black font-mono text-sky-500" id="statRigSbwcHrs">
                        <?= number_format($selectedRigSbwc, 2) ?>j
                    </span>
                </div>
                <div class="npt-sub-panel px-2.5 py-1 text-center">
                    <span class="text-[8.5px] uppercase font-bold text-[var(--muted-foreground)] block">Kejadian</span>
                    <span class="text-xs font-black font-mono text-[var(--foreground)]" id="statRigEventsCount">
                        <?= $selectedRigEventsCount ?> Hari
                    </span>
                </div>

                <button type="button" onclick="openNptEventModal(<?= $rigId ?>)"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition flex items-center gap-1 cursor-pointer ml-1">
                    <i class="fa-solid fa-plus text-[9px]"></i>
                    <span>+ Catat NPT</span>
                </button>
            </div>
        </div>

        <!-- Tabel Lembar Individual Rig Persis Sheet BMS 02..BMS 21 di Excel -->
        <div class="npt-clean-card p-2.5 sm:p-3 space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-1.5 border-b border-[var(--border)]">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-extrabold uppercase text-[var(--foreground)]">
                        Rincian Downtime Per Hari Operasi
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[9.5px] font-mono bg-sky-500/10 text-sky-500 font-bold" id="badgeEventsCount">
                        <?= count($rigSheetEvents) ?> Baris Kejadian
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Zoom / Scale Controller Langsung di Toolbar Rig -->
                    <div class="inline-flex items-center gap-0.5 bg-[var(--secondary)] p-0.5 rounded-lg border border-[var(--border)]">
                        <span class="text-[8.5px] uppercase px-1 text-[var(--muted-foreground)] font-extrabold flex items-center gap-1">
                            <i class="fa-solid fa-compress text-[7.5px]"></i>Skala:
                        </span>
                        <button type="button" onclick="setNptTableScale('mini')" id="btnScaleMiniRig"
                            class="scale-btn-mini px-1.5 py-0.5 rounded text-[9px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition cursor-pointer"
                            title="Mode Skala Mini (88% Zoom)">
                            Mini
                        </button>
                        <button type="button" onclick="setNptTableScale('compact')" id="btnScaleCompactRig"
                            class="scale-btn-compact px-1.5 py-0.5 rounded text-[9px] font-bold bg-[var(--card)] text-sky-500 shadow-xs border border-[var(--border)] transition cursor-pointer"
                            title="Mode Skala Kompak Standar (100% Zoom)">
                            Kompak
                        </button>
                        <button type="button" onclick="setNptTableScale('normal')" id="btnScaleNormalRig"
                            class="scale-btn-normal px-1.5 py-0.5 rounded text-[9px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition cursor-pointer"
                            title="Mode Skala Diperbesar (112% Zoom)">
                            Normal
                        </button>
                    </div>

                    <span class="text-[10px] text-[var(--muted-foreground)] hidden sm:inline">
                        Format sheet persis Excel: No, Lokasi/Well, Tanggal, Unpaid, SBWC, Total &amp; Remark
                    </span>
                </div>
            </div>

            <div class="excel-table-container">
                <table class="excel-table" id="tableRigDetail">
                    <thead>
                        <!-- Baris Header 1 -->
                        <tr>
                            <th rowspan="2" class="col-sticky-1">NO</th>
                            <th rowspan="2" class="col-sticky-2 text-left">NAME LOCATION</th>
                            <th rowspan="2" class="min-w-[52px] text-center">DATE</th>
                            <th colspan="1" class="th-unpaid-header min-w-[36px]">UNPAID</th>
                            <th colspan="11" class="th-sbwc-header">STAND BY WITH CREW ( SBWC )</th>
                            <th rowspan="2" class="th-total min-w-[46px]">TOTAL<br>(HRS)</th>
                            <th rowspan="2" class="th-remark min-w-[125px] max-w-[190px] text-left">REMARK</th>
                            <th rowspan="2" class="w-6 min-w-[26px] text-center">AKSI</th>
                        </tr>
                        <!-- Baris Header 2 -->
                        <tr>
                            <th class="th-unpaid text-[8.5px]">Rep/Pers</th>
                            <th class="th-sbwc min-w-[32px]">SWA<br>Rain</th>
                            <th class="th-sbwc min-w-[32px]">Dry<br>Road</th>
                            <th class="th-sbwc min-w-[30px]">Dry<br>Pad</th>
                            <th class="th-sbwc min-w-[34px]">Day-<br>light</th>
                            <th class="th-sbwc min-w-[32px]">Perfo</th>
                            <th class="th-sbwc min-w-[32px]">PHR<br>Well</th>
                            <th class="th-tp-nama-besar !py-0.5 text-[8.5px] min-w-[46px]">
                                <i class="fa-solid fa-handshake mr-0.5"></i>3RD
                            </th>
                            <th class="th-sbwc min-w-[30px]">TRANS</th>
                            <th class="th-sbwc min-w-[30px]">FOAM</th>
                            <th class="th-sbwc min-w-[32px]">CE/PE</th>
                            <th class="th-sbwc min-w-[34px]">Idul<br>Fitri</th>
                        </tr>
                    </thead>
                    <tbody id="rigSheetBody">
                        <?php if (!empty($rigSheetEvents)): ?>
                        <?php foreach ($rigSheetEvents as $ev): ?>
                        <tr>
                            <td class="col-sticky-1 text-center font-mono text-[var(--muted-foreground)]">
                                <?= $ev['no'] ?>
                            </td>
                            <td class="col-sticky-2 font-bold text-[var(--foreground)] truncate max-w-[65px]" title="<?= esc($ev['nama_lokasi']) ?>">
                                <?= esc($ev['nama_lokasi']) ?>
                            </td>
                            <td class="text-center font-mono text-[var(--foreground)]">
                                <?= esc($ev['tanggal_formatted']) ?>
                            </td>
                            <!-- UNPAID -->
                            <td class="text-right font-mono <?= $ev['total_unpaid'] > 0 ? 'text-rose-500 font-black bg-rose-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['total_unpaid'] > 0 ? number_format($ev['total_unpaid'], 2) : '-' ?>
                            </td>
                            <!-- SBWC Breakdowns -->
                            <td class="text-right font-mono <?= $ev['sbwc_rain'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_rain'] > 0 ? number_format($ev['sbwc_rain'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_road'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_road'] > 0 ? number_format($ev['sbwc_road'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_pad'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_pad'] > 0 ? number_format($ev['sbwc_pad'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_daylight'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_daylight'] > 0 ? number_format($ev['sbwc_daylight'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_perfo'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_perfo'] > 0 ? number_format($ev['sbwc_perfo'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_phr'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_phr'] > 0 ? number_format($ev['sbwc_phr'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_tp_sum'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_tp_sum'] > 0 ? number_format($ev['sbwc_tp_sum'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_trans'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_trans'] > 0 ? number_format($ev['sbwc_trans'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_foam'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_foam'] > 0 ? number_format($ev['sbwc_foam'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_cepe'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_cepe'] > 0 ? number_format($ev['sbwc_cepe'], 2) : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= $ev['sbwc_idul'] > 0 ? 'text-sky-500 font-bold bg-sky-500/5' : 'text-[var(--muted-foreground)] opacity-35' ?>">
                                <?= $ev['sbwc_idul'] > 0 ? number_format($ev['sbwc_idul'], 2) : '-' ?>
                            </td>
                            <!-- TOTAL (HRS) -->
                            <td class="text-right font-mono font-black text-amber-500 bg-amber-500/5">
                                <?= number_format($ev['total_hrs'], 2) ?>
                            </td>
                            <!-- REMARK (ANTI KELUAR GARIS & COMPACT PILL) -->
                            <td class="text-left td-remark">
                                <?php if (!empty($ev['remark_str']) && $ev['remark_str'] !== '-'): ?>
                                <div class="remark-bubble" onclick="openRemarkModal('<?= esc(addslashes(str_replace('#', ' ', $rig['kode']))) ?>', '<?= esc($ev['tanggal_formatted']) ?>', '<?= esc(addslashes($ev['remark_str'])) ?>', '<?= number_format($ev['total_hrs'], 2) ?> Jam (<?= esc(addslashes($ev['nama_lokasi'])) ?>)')" title="Klik untuk membaca catatan lengkap">
                                    <?php if ($ev['total_unpaid'] > 0): ?>
                                    <span class="badge-unpaid text-[7px] px-0.5 py-0 rounded font-mono font-bold shrink-0">UNPAID</span>
                                    <?php else: ?>
                                    <span class="badge-sbwc text-[7px] px-0.5 py-0 rounded font-mono font-bold shrink-0">SBWC</span>
                                    <?php endif; ?>
                                    <span class="text-[8px] text-[var(--foreground)] truncate font-medium"><?= esc($ev['remark_str']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="opacity-25 font-mono text-[8px] pl-0.5">-</span>
                                <?php endif; ?>
                            </td>
                            <!-- AKSI -->
                            <td class="text-center">
                                <button type="button" onclick='populateEditNptModal(<?= json_encode($ev, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= $rigId ?>)'
                                    class="w-4.5 h-4.5 rounded flex items-center justify-center text-sky-500 hover:bg-sky-500/10 transition cursor-pointer mx-auto"
                                    title="Edit kejadian tanggal ini">
                                    <i class="fa-solid fa-pen text-[8px]"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="18" class="text-center py-6 text-[var(--muted-foreground)]">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-shield-check text-xl text-emerald-500 opacity-80"></i>
                                    <p class="font-bold text-xs">Tidak ada catatan downtime pada periode ini</p>
                                    <p class="text-[10px] opacity-70">Operasi normal berjalan penuh atau downtime tercatat 0 jam.</p>
                                    <button type="button" onclick="openNptEventModal(<?= $rigId ?>)"
                                        class="mt-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-600 text-white">
                                        + Catat NPT untuk Rig Ini
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="col-sticky-1 text-center font-black">
                                TOTAL DOWNTIME <?= esc(str_replace('#', ' ', $rig['kode'])) ?>
                            </td>
                            <td class="text-right font-mono font-black text-rose-500">
                                <?= number_format($selectedRigUnpaid, 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_rain')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_road')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_pad')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_daylight')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_perfo')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_phr')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_tp_sum')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_trans')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_foam')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_cepe')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-sky-500">
                                <?= number_format(array_sum(array_column($rigSheetEvents, 'sbwc_idul')), 2) ?>
                            </td>
                            <td class="text-right font-mono font-black text-amber-500">
                                <?= number_format($selectedRigTotalHrs, 2) ?>
                            </td>
                            <td colspan="2" class="text-left font-bold td-remark text-[var(--muted-foreground)] text-[8px]">
                                Total Jam Operasi Rig
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- MODAL CATAT NPT CEPAT (MINIMALIS & EFEKTIF) -->
<!-- ══════════════════════════════════════════════════════════════ -->
<div id="modalNptEvent" class="npt-modal-backdrop" onclick="if(event.target===this) closeNptEventModal()">
    <div class="npt-modal-box p-5" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-sky-500/15 text-sky-500 font-black flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-[var(--foreground)]" id="modalNptTitle">Catat Downtime NPT</h3>
                    <p class="text-[11px] text-[var(--muted-foreground)]">Input kejadian downtime harian rig</p>
                </div>
            </div>
            <button type="button" onclick="closeNptEventModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs text-[var(--muted-foreground)] hover:bg-[var(--secondary)] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="<?= base_url('npt/simpan-event') ?>" id="formNptEvent">
            <?= csrf_field() ?>
            <input type="hidden" name="view" value="<?= esc($activeView) ?>">

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                            Pilih Rig <span class="text-rose-500">*</span>
                        </label>
                        <select name="rig_id" id="nptRigSelect" required class="npt-control-box w-full px-2.5 py-2 text-xs font-bold cursor-pointer">
                            <?php foreach ($allRigs as $ar): ?>
                            <option value="<?= $ar['id'] ?>" <?= $ar['id'] == $rigId ? 'selected' : '' ?>>
                                <?= esc(str_replace('#', ' ', $ar['kode'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                            Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="nptTanggalInput" required
                            value="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, min((int)date('d'), cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun))) ?>"
                            min="<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>"
                            max="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun)) ?>"
                            class="npt-control-box w-full px-2.5 py-2 text-xs font-mono font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                        Lokasi / Sumur
                    </label>
                    <select name="lokasi_id" id="nptLokasiSelect" class="npt-control-box w-full px-2.5 py-2 text-xs font-bold cursor-pointer">
                        <option value="">-- Lokasi Otomatis dari Daily Report --</option>
                        <?php if (!empty($lokasiList)): ?>
                        <?php foreach ($lokasiList as $lok): ?>
                        <option value="<?= $lok['id'] ?>"><?= esc($lok['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                            Kategori Downtime <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_id" id="nptKategoriSelect" required onchange="onNptKategoriChange(this.value)"
                            class="npt-control-box w-full px-2.5 py-2 text-xs font-bold cursor-pointer">
                            <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= $kat['id'] ?>">
                                [<?= strtoupper($kat['tipe']) ?>] <?= esc($kat['nama']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                            Durasi Jam <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.25" min="0" max="24" name="jam" id="nptJamInput" required value="1"
                            class="npt-control-box w-full px-2.5 py-2 text-xs font-mono font-bold text-right">
                    </div>
                </div>

                <div id="tpCompanyWrapper" class="hidden">
                    <label class="block text-xs font-bold text-sky-500 mb-1">
                        Vendor 3rd Party
                    </label>
                    <select name="third_party_id" id="nptThirdPartySelect" class="npt-control-box w-full px-2.5 py-2 text-xs font-bold cursor-pointer">
                        <option value="">-- Pilih Vendor Spesifik --</option>
                        <?php foreach ($thirdParties as $tp): ?>
                        <option value="<?= $tp['id'] ?>"><?= esc($tp['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[var(--foreground)] mb-1">
                        Keterangan / Remark
                    </label>
                    <textarea name="remark" id="nptRemarkInput" rows="2" placeholder="Catat penyebab downtime atau problem mesin..."
                        class="npt-control-box w-full p-2.5 text-xs"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-[var(--border)]">
                <button type="button" onclick="closeNptEventModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[var(--border)] hover:bg-[var(--secondary)] transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Catatan NPT</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- MODAL DETAIL CATATAN / REMARK LENGKAP (ANTI KELUAR GARIS) -->
<!-- ══════════════════════════════════════════════════════════════ -->
<div id="modalRemarkDetail" class="npt-modal-backdrop" onclick="if(event.target===this) closeRemarkModal()">
    <div class="npt-modal-box p-5 max-w-lg" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-sky-500/15 text-sky-500 font-black flex items-center justify-center text-sm">
                    <i class="fa-solid fa-note-sticky"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-[var(--foreground)]" id="modalRemarkRigTitle">Catatan Downtime Rig</h3>
                    <p class="text-[11px] text-[var(--muted-foreground)]" id="modalRemarkPeriodSubtitle">Periode / Tanggal</p>
                </div>
            </div>
            <button type="button" onclick="closeRemarkModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs text-[var(--muted-foreground)] hover:bg-[var(--secondary)] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div class="npt-sub-panel p-3 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-[var(--muted-foreground)] block">Durasi Downtime</span>
                    <span class="text-xs font-black font-mono text-amber-500" id="modalRemarkDuration">-</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-[var(--muted-foreground)] block">Status Integritas</span>
                    <span class="text-xs font-bold text-emerald-500"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i>Tersinkronisasi Presisi</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--foreground)] mb-1.5">
                    Isi Catatan / Narrative Lengkap:
                </label>
                <div id="modalRemarkContent" class="npt-control-box p-3.5 text-xs text-[var(--foreground)] leading-relaxed whitespace-pre-wrap break-words select-all font-sans bg-[var(--card)] border border-[var(--border)] rounded-lg">
                    -
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 mt-4 border-t border-[var(--border)]">
            <button type="button" onclick="copyRemarkText()" id="btnCopyRemark"
                class="px-3 py-1.5 rounded-xl text-xs font-bold border border-[var(--border)] hover:bg-[var(--secondary)] transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-regular fa-copy"></i>
                <span id="labelCopyRemark">Salin Teks</span>
            </button>
            <button type="button" onclick="closeRemarkModal()"
                class="px-4 py-2 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-sm transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const currentBulan = <?= $bulan ?>;
    const currentTahun = <?= $tahun ?>;
    let activeRigId    = <?= $rigId ?>;
    let currentView    = '<?= $activeView ?>';

    function submitPeriodChange(b, y) {
        window.location.href = `<?= base_url('npt') ?>/${activeRigId}/${b}/${y}?view=${currentView}`;
    }

    function switchMainView(mode) {
        currentView = mode;
        const viewSummary = document.getElementById('viewSummaryAllRig');
        const viewRig     = document.getElementById('viewRigDetailSheet');
        const btnSummary  = document.getElementById('tabBtnSummary');
        const btnRig      = document.getElementById('tabBtnRig');

        if (mode === 'summary') {
            viewSummary.classList.remove('hidden');
            viewRig.classList.add('hidden');
            btnSummary.classList.add('is-active');
            btnRig.classList.remove('is-active');
        } else {
            viewSummary.classList.add('hidden');
            viewRig.classList.remove('hidden');
            btnSummary.classList.remove('is-active');
            btnRig.classList.add('is-active');
        }

        // Perbarui URL tanpa reload penuh
        const newUrl = `<?= base_url('npt') ?>/${activeRigId}/${currentBulan}/${currentTahun}?view=${mode}`;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    }

    function selectRigView(rId) {
        window.location.href = `<?= base_url('npt') ?>/${rId}/${currentBulan}/${currentTahun}?view=rig`;
    }

    function filterSummaryRows(keyword) {
        keyword = (keyword || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.summary-rig-row');
        rows.forEach(r => {
            const name = r.getAttribute('data-rig-name') || '';
            if (!keyword || name.includes(keyword)) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    let isOnlyPositiveDt = false;
    function toggleZeroSummaryRows() {
        isOnlyPositiveDt = !isOnlyPositiveDt;
        const btn = document.getElementById('btnToggleZeroRows');
        const rows = document.querySelectorAll('.summary-rig-row');
        if (isOnlyPositiveDt) {
            btn.textContent = 'Hanya Yang Mengalami NPT';
            btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/15 border border-amber-500/30 text-amber-500 transition cursor-pointer';
            rows.forEach(r => {
                const hasDt = r.getAttribute('data-has-dt') === '1';
                r.style.display = hasDt ? '' : 'none';
            });
        } else {
            btn.textContent = 'Semua Rig (18)';
            btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold border border-[var(--border)] hover:bg-[var(--secondary)] transition cursor-pointer';
            rows.forEach(r => r.style.display = '');
        }
    }

    // ═══ Kunci Scroll Halaman Saat Modal Terbuka (Fokus Penuh di Tengah) ═══
    function lockPageScroll(locked) {
        if (locked) {
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            const mc = document.getElementById('main-content');
            if (mc) mc.style.overflow = 'hidden';
        } else {
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            const mc = document.getElementById('main-content');
            if (mc) mc.style.overflow = '';
        }
    }

    function openNptEventModal(rigIdToSelect) {
        const modal = document.getElementById('modalNptEvent');
        if (rigIdToSelect) {
            const sel = document.getElementById('nptRigSelect');
            if (sel) sel.value = String(rigIdToSelect);
        }
        if (modal) {
            modal.classList.add('is-open');
            lockPageScroll(true);
        }
    }

    function closeNptEventModal() {
        const modal = document.getElementById('modalNptEvent');
        if (modal) {
            modal.classList.remove('is-open');
            lockPageScroll(false);
        }
    }

    function onNptKategoriChange(katId) {
        const wrapper = document.getElementById('tpCompanyWrapper');
        if (!wrapper) return;
        // Kategori ID 10 = 3rd Party
        if (parseInt(katId, 10) === 10) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    function populateEditNptModal(ev, rigId) {
        openNptEventModal(rigId);
        document.getElementById('nptTanggalInput').value = ev.tanggal;
        document.getElementById('nptJamInput').value = ev.total_hrs;
        document.getElementById('nptRemarkInput').value = ev.remark_str !== '-' ? ev.remark_str : '';

        // Deteksi kategori dominan
        if (ev.total_unpaid > 0) {
            document.getElementById('nptKategoriSelect').value = '1';
        } else if (ev.sbwc_rain > 0) {
            document.getElementById('nptKategoriSelect').value = '3';
        } else if (ev.sbwc_daylight > 0) {
            document.getElementById('nptKategoriSelect').value = '6';
        } else if (ev.sbwc_tp_sum > 0) {
            document.getElementById('nptKategoriSelect').value = '10';
            onNptKategoriChange('10');
        }
    }

    function openRemarkModal(rigName, dateOrPeriod, fullText, durationInfo) {
        document.getElementById('modalRemarkRigTitle').textContent = `Catatan Downtime: ${rigName}`;
        document.getElementById('modalRemarkPeriodSubtitle').textContent = `Periode / Tanggal: ${dateOrPeriod}`;
        document.getElementById('modalRemarkDuration').textContent = durationInfo || '-';
        document.getElementById('modalRemarkContent').textContent = fullText && fullText !== '-' ? fullText : 'Tidak ada keterangan tambahan.';
        
        const btnCopy = document.getElementById('labelCopyRemark');
        if (btnCopy) btnCopy.textContent = 'Salin Teks';

        const modal = document.getElementById('modalRemarkDetail');
        if (modal) {
            modal.classList.add('is-open');
            lockPageScroll(true);
        }
    }

    function closeRemarkModal() {
        const modal = document.getElementById('modalRemarkDetail');
        if (modal) {
            modal.classList.remove('is-open');
            lockPageScroll(false);
        }
    }

    function copyRemarkText() {
        const text = document.getElementById('modalRemarkContent').textContent;
        if (text) {
            navigator.clipboard.writeText(text).then(() => {
                const label = document.getElementById('labelCopyRemark');
                if (label) {
                    label.textContent = 'Tersalin!';
                    setTimeout(() => { label.textContent = 'Salin Teks'; }, 2000);
                }
            });
        }
    }

    // ═══ Kontrol Skala & Kepadatan Tabel NPT Interaktif (Mini, Kompak, Normal) ═══
    function setNptTableScale(scale) {
        const containers = document.querySelectorAll('.excel-table-container');
        containers.forEach(c => {
            c.classList.remove('table-scale-mini', 'table-scale-compact', 'table-scale-normal');
            c.classList.add(`table-scale-${scale}`);
        });

        // Sinkronisasi status visual tombol skala di seluruh toolbar (Summary & Rig)
        const allModes = ['mini', 'compact', 'normal'];
        allModes.forEach(m => {
            const btns = document.querySelectorAll(`.scale-btn-${m}`);
            btns.forEach(b => {
                if (m === scale) {
                    b.className = `scale-btn-${m} px-1.5 py-0.5 rounded text-[9px] font-bold bg-[var(--card)] text-sky-500 shadow-xs border border-[var(--border)] transition cursor-pointer`;
                } else {
                    b.className = `scale-btn-${m} px-1.5 py-0.5 rounded text-[9px] font-bold text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition cursor-pointer`;
                }
            });
        });

        try {
            localStorage.setItem('npt_table_scale_mode', scale);
        } catch (e) {}
    }

    // Auto inisialisasi saat DOM selesai termuat (Default: compact)
    document.addEventListener('DOMContentLoaded', () => {
        let savedScale = 'compact';
        try {
            savedScale = localStorage.getItem('npt_table_scale_mode') || 'compact';
        } catch (e) {}
        setNptTableScale(savedScale);

        // Proteksi mutlak agar scroll mouse wheel tidak menggeser halaman atau modal saat modal terbuka
        document.querySelectorAll('.npt-modal-backdrop').forEach(modal => {
            modal.addEventListener('wheel', (e) => {
                e.preventDefault();
            }, { passive: false });
            modal.addEventListener('touchmove', (e) => {
                e.preventDefault();
            }, { passive: false });
        });

        // Dukungan tombol Escape keyboard untuk menutup modal dengan cepat
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeNptEventModal();
                closeRemarkModal();
            }
        });
    });
</script>
<?= $this->endSection() ?>
