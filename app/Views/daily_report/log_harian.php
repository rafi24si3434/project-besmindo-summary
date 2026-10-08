<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* ═══ LOG HARIAN — EXECUTIVE LEDGER DUAL-THEME SYSTEM (DARK & LIGHT) ═══ */
    :root {
        --lh-card-bg: #0d111a;
        --lh-card-border: rgba(255, 255, 255, 0.08);
        --lh-card-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.45);
        --lh-sub-bg: #080b11;
        --lh-sub-border: rgba(255, 255, 255, 0.06);
        --lh-input-bg: #06080d;
        --lh-input-border: #222d40;
        --lh-input-text: #f8fafc;
        --lh-text-title: #f8fafc;
        --lh-text-sec: #cbd5e1;
        --lh-text-muted: #64748b;
        --lh-divider: rgba(255, 255, 255, 0.07);
        --lh-btn-bg: #141c2b;
        --lh-btn-border: #25334a;
        --lh-btn-text: #e2e8f0;
        --lh-btn-hover-bg: #1e293b;
        --lh-btn-hover-border: #38bdf8;
        --lh-table-head-bg: #080b12;
        --lh-table-head-text: #94a3b8;
        --lh-row-hover: rgba(56, 189, 248, 0.05);
        --lh-row-active: rgba(56, 189, 248, 0.12);
        --lh-ledger-row-bg: #090d14;
        --lh-ledger-row-hover: #0e1420;
        --lh-dt-active-amber-bg: rgba(245, 158, 11, 0.10);
        --lh-dt-active-amber-border: rgba(245, 158, 11, 0.45);
        --lh-dt-active-rose-bg: rgba(244, 63, 94, 0.10);
        --lh-dt-active-rose-border: rgba(244, 63, 94, 0.45);
    }

    :root[data-theme="light"] {
        --lh-card-bg: #ffffff;
        --lh-card-border: #e2e8f0;
        --lh-card-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
        --lh-sub-bg: #f8fafc;
        --lh-sub-border: #e2e8f0;
        --lh-input-bg: #ffffff;
        --lh-input-border: #cbd5e1;
        --lh-input-text: #0f172a;
        --lh-text-title: #0f172a;
        --lh-text-sec: #334155;
        --lh-text-muted: #64748b;
        --lh-divider: #e2e8f0;
        --lh-btn-bg: #f8fafc;
        --lh-btn-border: #cbd5e1;
        --lh-btn-text: #1e293b;
        --lh-btn-hover-bg: #f0f9ff;
        --lh-btn-hover-border: #0284c7;
        --lh-table-head-bg: #f1f5f9;
        --lh-table-head-text: #475569;
        --lh-row-hover: rgba(2, 132, 199, 0.04);
        --lh-row-active: rgba(2, 132, 199, 0.09);
        --lh-ledger-row-bg: #ffffff;
        --lh-ledger-row-hover: #f8fafc;
        --lh-dt-active-amber-bg: #fffbeb;
        --lh-dt-active-amber-border: #f59e0b;
        --lh-dt-active-rose-bg: #fff1f2;
        --lh-dt-active-rose-border: #f43f5e;
    }

    .lh-surface-card {
        background: var(--lh-card-bg);
        border: 1px solid var(--lh-card-border);
        border-radius: 14px;
        box-shadow: var(--lh-card-shadow);
        color: var(--lh-text-sec);
    }

    .lh-sub-panel {
        background: var(--lh-sub-bg);
        border: 1px solid var(--lh-sub-border);
        border-radius: 11px;
        color: var(--lh-text-sec);
    }

    .lh-text-title { color: var(--lh-text-title); }
    .lh-text-sec   { color: var(--lh-text-sec); }
    .lh-text-muted { color: var(--lh-text-muted); }
    .lh-divider-b  { border-bottom: 1px solid var(--lh-divider); }
    .lh-divider-t  { border-top: 1px solid var(--lh-divider); }

    .lh-control-box {
        background: var(--lh-input-bg);
        border: 1px solid var(--lh-input-border);
        color: var(--lh-input-text);
        border-radius: 9px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .lh-control-box:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        outline: none;
    }
    .lh-control-box option {
        background: var(--lh-card-bg);
        color: var(--lh-text-title);
    }

    .lh-preset-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid var(--lh-btn-border);
        background: var(--lh-btn-bg);
        color: var(--lh-btn-text);
        transition: all 0.15s ease;
        cursor: pointer;
        text-decoration: none;
    }
    .lh-preset-btn:hover {
        border-color: var(--lh-btn-hover-border);
        background: var(--lh-btn-hover-bg);
        color: var(--lh-text-title);
    }

    /* ═══ 13 POS DOWNTIME LEDGER TABLE ROWS ═══ */
    .lh-ledger-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 8px;
    }
    @media (min-width: 640px) {
        .lh-ledger-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .lh-ledger-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 11px;
        background: var(--lh-ledger-row-bg);
        border: 1px solid var(--lh-sub-border);
        border-radius: 10px;
        transition: all 0.15s ease;
    }
    .lh-ledger-item:hover {
        background: var(--lh-ledger-row-hover);
        border-color: var(--lh-input-border);
    }
    .lh-ledger-item.has-value {
        background: var(--lh-dt-active-amber-bg);
        border-color: var(--lh-dt-active-amber-border);
    }
    .lh-ledger-item.has-value-unpaid {
        background: var(--lh-dt-active-rose-bg);
        border-color: var(--lh-dt-active-rose-border);
    }

    .lh-num-badge {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--lh-btn-bg);
        border: 1px solid var(--lh-btn-border);
        color: var(--lh-text-muted);
    }
    .lh-ledger-item.has-value .lh-num-badge {
        background: rgba(245, 158, 11, 0.2);
        border-color: rgba(245, 158, 11, 0.5);
        color: #f59e0b;
    }
    .lh-ledger-item.has-value-unpaid .lh-num-badge {
        background: rgba(244, 63, 94, 0.2);
        border-color: rgba(244, 63, 94, 0.5);
        color: #f43f5e;
    }

    .lh-sisa-mini-btn {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 5px;
        background: var(--lh-btn-bg);
        border: 1px solid var(--lh-btn-border);
        color: var(--lh-text-muted);
        cursor: pointer;
        transition: all 0.12s ease;
        flex-shrink: 0;
    }
    .lh-sisa-mini-btn:hover {
        border-color: #0ea5e9;
        color: #0ea5e9;
    }

    .lh-table-head {
        background: var(--lh-table-head-bg);
        color: var(--lh-table-head-text);
        border-bottom: 1px solid var(--lh-divider);
    }

    .lh-table-row {
        border-bottom: 1px solid var(--lh-divider);
        transition: background-color 0.12s ease;
    }
    .lh-table-row:hover {
        background: var(--lh-row-hover);
    }
    .lh-table-row.is-active-editing {
        background: var(--lh-row-active) !important;
        box-shadow: inset 3px 0 0 #0ea5e9;
    }

    .lh-pill-empty {
        background: var(--lh-input-bg);
        color: var(--lh-text-sec);
        border: 1px solid var(--lh-input-border);
    }
    .lh-pill-empty:hover {
        border-color: #0ea5e9;
        color: var(--lh-text-title);
    }

    /* Semantic Badges */
    .lh-badge-sky {
        background: rgba(14, 165, 233, 0.12);
        border: 1px solid rgba(14, 165, 233, 0.3);
        color: #38bdf8;
    }
    :root[data-theme="light"] .lh-badge-sky {
        background: #e0f2fe;
        border-color: #7dd3fc;
        color: #0369a1;
    }

    .lh-badge-emerald {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
    }
    :root[data-theme="light"] .lh-badge-emerald {
        background: #d1fae5;
        border-color: #6ee7b7;
        color: #047857;
    }

    .lh-badge-amber {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }
    :root[data-theme="light"] .lh-badge-amber {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #b45309;
    }

    .lh-badge-rose {
        background: rgba(244, 63, 94, 0.12);
        border: 1px solid rgba(244, 63, 94, 0.3);
        color: #fb7185;
    }
    :root[data-theme="light"] .lh-badge-rose {
        background: #ffe4e6;
        border-color: #fda4af;
        color: #be123c;
    }

    /* ═══ PROMINENT TACTILE "+ SUMUR BARU" CTA BUTTON (DESIGN-TASTE) ═══ */
    .lh-btn-new-well {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 10px;
        background: #0284c7;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
        text-decoration: none;
    }
    .lh-btn-new-well:hover {
        background: #0369a1;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.38);
    }
    .lh-btn-new-well:active {
        transform: translateY(1px);
    }
    .lh-btn-new-well-icon {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    /* ═══ SMART CONTINUATION BANNER (SAAT SUMUR SELESAI / HARI TERAKHIR) ═══ */
    .lh-next-well-banner {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.32);
        border-radius: 11px;
        padding: 10px 14px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    :root[data-theme="light"] .lh-next-well-banner {
        background: #ecfdf5;
        border-color: #6ee7b7;
    }

    /* ═══ TACTILE ACTION BUTTONS (STICKY SIDEBAR COMMAND SYSTEM) ═══ */
    .lh-btn-submit-main {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: #0284c7;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
    }
    .lh-btn-submit-main * {
        color: #ffffff !important;
    }
    .lh-btn-submit-main:hover {
        background: #0369a1;
        box-shadow: 0 6px 18px rgba(2, 132, 199, 0.45);
    }
    .lh-btn-submit-main:active {
        transform: translateY(1px);
    }
    .lh-btn-submit-main:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        box-shadow: none;
    }
    .lh-btn-submit-main.is-error {
        background: #e11d48 !important;
        border-color: #be123c !important;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
    }
    :root[data-theme="light"] .lh-btn-submit-main {
        background: #0284c7 !important;
        border-color: #0369a1 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.25);
    }
    :root[data-theme="light"] .lh-btn-submit-main * {
        color: #ffffff !important;
    }
    :root[data-theme="light"] .lh-btn-submit-main:hover {
        background: #0369a1 !important;
    }

    /* Tombol Lanjut Buat Sumur Baru */
    .lh-btn-save-next {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399 !important;
    }
    .lh-btn-save-next * {
        color: #34d399 !important;
    }
    .lh-btn-save-next:hover {
        background: rgba(16, 185, 129, 0.16);
        border-color: rgba(16, 185, 129, 0.5);
        color: #6ee7b7 !important;
    }
    .lh-btn-save-next:hover * {
        color: #6ee7b7 !important;
    }
    .lh-btn-save-next:active {
        transform: translateY(1px);
    }
    .lh-btn-save-next:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Light Mode Default: Soft Mint Pastel Background with High-Contrast Deep Green Text */
    :root[data-theme="light"] .lh-btn-save-next {
        background: #f0fdf4 !important;
        border: 1px solid #86efac !important;
        color: #15803d !important;
    }
    :root[data-theme="light"] .lh-btn-save-next * {
        color: #15803d !important;
    }
    :root[data-theme="light"] .lh-btn-save-next:hover {
        background: #dcfce7 !important;
        border-color: #4ade80 !important;
        color: #166534 !important;
    }
    :root[data-theme="light"] .lh-btn-save-next:hover * {
        color: #166534 !important;
    }

    /* State Highlight: Saat Jadwal Sumur Selesai (Beralih jadi Solid CTA Emerald) */
    .lh-btn-save-next.is-highlighted {
        background: #059669 !important;
        border: 1px solid #047857 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35) !important;
    }
    .lh-btn-save-next.is-highlighted * {
        color: #ffffff !important;
    }
    .lh-btn-save-next.is-highlighted:hover {
        background: #047857 !important;
    }
    :root[data-theme="light"] .lh-btn-save-next.is-highlighted {
        background: #059669 !important;
        border: 1px solid #047857 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25) !important;
    }
    :root[data-theme="light"] .lh-btn-save-next.is-highlighted * {
        color: #ffffff !important;
    }

    /* Reset Button */
    .lh-btn-reset {
        width: 100%;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #141c2b;
        border: 1px solid #25334a;
        color: #94a3b8;
    }
    .lh-btn-reset:hover {
        background: #1e293b;
        color: #f8fafc;
        border-color: #38bdf8;
    }
    :root[data-theme="light"] .lh-btn-reset {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    :root[data-theme="light"] .lh-btn-reset:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Contextual Sidebar Notice */
    .lh-sidebar-notice {
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
    }
    :root[data-theme="light"] .lh-sidebar-notice {
        background: #f0fdf4 !important;
        border: 1px solid #86efac !important;
        color: #15803d !important;
    }
    :root[data-theme="light"] .lh-sidebar-notice p {
        color: #334155 !important;
    }

    /* ═══ IN-PAGE SMART NEW WELL MODAL ═══ */
    .lh-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 9990;
        background: rgba(6, 10, 18, 0.78);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .lh-modal-backdrop.is-open {
        opacity: 1;
        pointer-events: auto;
    }
    .lh-modal-dialog {
        width: 100%;
        max-width: 560px;
        background: var(--lh-card-bg);
        border: 1px solid var(--lh-input-border);
        border-radius: 16px;
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.65);
        transform: translateY(10px);
        transition: transform 0.22s cubic-bezier(0.22, 1, 0.36, 1);
        overflow: hidden;
    }
    .lh-modal-backdrop.is-open .lh-modal-dialog {
        transform: translateY(0);
    }
</style>

<?php
$bulanNames = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
$totalLogsCount  = count($recentLogs ?? []);
$totalWellsCount = count($wells ?? []);
$tambahSumurUrl  = base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}");
$maxWellNo = 0;
foreach (($wells ?? []) as $wItem) {
    if ((int)($wItem['no_well'] ?? 0) > $maxWellNo) {
        $maxWellNo = (int)$wItem['no_well'];
    }
}
$nextNoWellAuto = $maxWellNo + 1;
?>

<div class="space-y-3.5 pb-12">

    <!-- ═══ 1. HEADER KOMANDO ATAS (COMPACT SINGLE-ROW) ═══ -->
    <div class="bms-bezel-shell">
        <div class="bms-bezel-core px-4 py-2.5 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg lh-badge-sky flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-day text-xs"></i>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm sm:text-base font-extrabold lh-text-title tracking-tight">
                        Log Harian Operasional (24 Jam)
                    </h2>
                    <span class="px-2 py-0.5 rounded-md lh-badge-sky text-[11px] font-mono font-bold">
                        <?= esc($rig['kode']) ?>
                    </span>
                    <span class="px-2 py-0.5 rounded-md lh-sub-panel text-[11px] font-semibold">
                        <?= $bulanNames[$bulan] ?> <?= $tahun ?>
                    </span>
                </div>
            </div>

            <!-- Filter Armada & Navigasi Cepat -->
            <div class="flex flex-wrap items-center gap-1.5">
                <select id="selectRig" onchange="navigateLog()"
                    class="lh-control-box px-2.5 py-1 text-xs font-bold cursor-pointer">
                    <?php foreach (($allRigs ?? $rigs ?? []) as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $rigId ? 'selected' : '' ?>>
                        <?= esc($r['kode']) ?> — <?= esc($r['nama_rig']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select id="selectBulan" onchange="navigateLog()"
                    class="lh-control-box px-2 py-1 text-xs font-semibold cursor-pointer">
                    <?php foreach ($bulanNames as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $num == $bulan ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="selectTahun" onchange="navigateLog()"
                    class="lh-control-box px-2 py-1 text-xs font-mono font-bold cursor-pointer">
                    <?php for ($y = 2025; $y <= 2028; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>

                <a href="<?= $tambahSumurUrl ?>" class="lh-preset-btn !py-1.5 !px-3 !border-sky-500/40">
                    <i class="fa-solid fa-plus text-sky-400"></i>
                    <span>Tambah Sumur (Well #<?= $nextNoWellAuto ?>)</span>
                </a>

                <a href="<?= base_url("daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" class="lh-preset-btn !py-1 !px-2.5">
                    <i class="fa-solid fa-table-list text-sky-500"></i>
                    <span>Daily Report</span>
                </a>

                <a href="<?= base_url("npt/{$rigId}/{$bulan}/{$tahun}") ?>" class="lh-preset-btn !py-1 !px-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    <span>Grid NPT</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══ PERINGATAN BELUM ADA SUMUR (JIKA MASIH KOSONG) ═══ -->
    <?php if (empty($wells)): ?>
    <div id="emptyWellsAlertBanner" class="lh-surface-card p-4 border-l-4 border-l-amber-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl lh-badge-amber flex items-center justify-center shrink-0">
                <i class="fa-solid fa-bore-hole text-sm"></i>
            </div>
            <div>
                <h3 class="text-xs font-extrabold lh-text-title uppercase tracking-wide">
                    Belum Ada Pekerjaan Sumur (Well) di Bulan <?= $bulanNames[$bulan] ?> <?= $tahun ?>
                </h3>
                <p class="text-[11px] lh-text-muted">
                    Klik tombol di kanan untuk mendaftarkan <strong>Well #1</strong>, lalu kembali otomatis ke lembar kerja ini.
                </p>
            </div>
        </div>
        <a href="<?= $tambahSumurUrl ?>" class="lh-btn-new-well shrink-0">
            <span class="lh-btn-new-well-icon"><i class="fa-solid fa-plus"></i></span>
            <span class="text-xs sm:text-sm font-extrabold">Buat Sumur Pertama (Well #1)</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>
    <?php endif; ?>

    <!-- ═══ 2. WORKSPACE UTAMA: FORM DI KIRI (8 COL) + MONITOR 24 JAM & DAFTAR SUMUR DI KANAN (4 COL) ═══ -->
    <form method="POST" action="<?= base_url('daily-report/simpan-log') ?>" id="formDailyLog">
        <?= csrf_field() ?>
        <input type="hidden" name="rig_id" value="<?= $rigId ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">
        <input type="hidden" name="after_save_action" id="inputAfterSaveAction" value="">

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start" id="formSection">

            <!-- ══════════════════════════════════════════════════════════════════
                 KOLOM KIRI (8 COL): FORMULIR INPUT BERSIH & TERSTRUKTUR
                 ══════════════════════════════════════════════════════════════════ -->
            <div class="xl:col-span-8 space-y-3.5">

                <!-- ─── PANEL UTAMA ATAS: 01. SUMUR & TANGGAL + 02. JAM PRODUKTIF (MIRU & OPERASI) LANGSUNG TERLIHAT TANPA SCROLL ─── -->
                <div class="lh-surface-card p-4 sm:p-5 space-y-3.5">

                    <!-- BARIS HEADER 01: SUMUR & JADWAL + TOMBOL TACTILE BESAR ALIHKAN KE /daily-report/tambah -->
                    <div class="flex flex-wrap items-center justify-between gap-3 lh-divider-b pb-3.5">
                        <div class="flex flex-wrap items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-md lh-badge-sky font-mono text-xs font-black flex items-center justify-center shrink-0">01</span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-xs sm:text-[13px] font-extrabold uppercase tracking-wider lh-text-title">
                                        Sumur (Well), Tanggal Operasi &amp; Status Pekerjaan
                                    </h3>
                                    <span id="wellScheduleNotice" class="inline-flex items-center text-[11px] lh-text-muted"></span>
                                </div>
                                <p class="text-[11px] lh-text-muted mt-0.5">
                                    Selesai mengisi jadwal sumur ini? Klik tombol <strong>Tambah Sumur Baru (Well #<?= $nextNoWellAuto ?>)</strong> di kanan.
                                </p>
                            </div>
                        </div>

                        <!-- Prominent Tactile CTA Button -> Direct to /daily-report/tambah -->
                        <a href="<?= $tambahSumurUrl ?>"
                            class="lh-btn-new-well shrink-0 !py-2.5 !px-4"
                            title="Buka halaman pendaftaran Sumur Baru (Well #<?= $nextNoWellAuto ?>)">
                            <span class="lh-btn-new-well-icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                            <span class="flex flex-col text-left leading-tight pr-1">
                                <span class="text-xs sm:text-[13px] font-extrabold tracking-tight">
                                    Tambah Sumur Baru (Well #<?= $nextNoWellAuto ?>)
                                </span>
                                <span class="text-[10px] text-sky-100/90 font-medium">
                                    Lanjut ke sumur berikutnya →
                                </span>
                            </span>
                        </a>
                    </div>

                    <!-- BARIS KONTROL 1 BARIS (SUMUR + TANGGAL + JARAK + STATUS) -->
                    <?php
                        $requestedWellId = (int)($_GET['well_id'] ?? 0);
                        if ($requestedWellId <= 0 && !empty($wells)) {
                            $latestWell = end($wells);
                            $requestedWellId = (int)($latestWell['id'] ?? 0);
                            reset($wells);
                        }
                    ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-end">
                        <!-- Pilih Sumur (5 Col) -->
                        <div class="lg:col-span-5">
                            <label class="block text-[10px] font-extrabold lh-text-sec uppercase mb-1">
                                Pilih Pekerjaan Sumur (Well) <span class="text-rose-500">*</span>
                            </label>
                            <select name="daily_report_id" id="selectWell" required onchange="onWellSelectChange(true)"
                                class="lh-control-box w-full px-2.5 py-2 text-xs font-bold cursor-pointer">
                                <?php foreach ($wells as $w): ?>
                                <?php
                                    $wStart = $w['tanggal_mulai'] ?? sprintf('%04d-%02d-01', $tahun, $bulan);
                                    $wEnd   = $w['tanggal_selesai'] ?? $wStart;
                                    $isSelectedWell = ((int)$w['id'] === $requestedWellId);
                                ?>
                                <option value="<?= $w['id'] ?>"
                                    <?= $isSelectedWell ? 'selected' : '' ?>
                                    data-start="<?= esc($wStart) ?>"
                                    data-end="<?= esc($wEnd) ?>"
                                    data-jarak="<?= (float)($w['jarak'] ?? 0) ?>"
                                    data-nowell="<?= esc($w['no_well']) ?>"
                                    data-lokasi="<?= esc($w['nama_lokasi']) ?>"
                                    data-status="<?= esc($w['status_job'] ?: 'JOB PROGRESS') ?>">
                                    Well #<?= $w['no_well'] ?> — <?= esc($w['nama_lokasi']) ?>
                                    (<?= date('d/m', strtotime($wStart)) ?> - <?= date('d/m/Y', strtotime($wEnd)) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tanggal Operasi (3 Col) -->
                        <div class="lg:col-span-3">
                            <label class="block text-[10px] font-extrabold lh-text-sec uppercase mb-1 flex items-center justify-between">
                                <span>Tanggal <span class="text-rose-500">*</span></span>
                                <span id="dateRangeLockLabel" class="text-[10px] font-mono text-amber-500 font-semibold"></span>
                            </label>
                            <input type="date" name="tanggal" id="inputTanggal" required
                                value="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, min((int)date('d'), cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun))) ?>"
                                min="<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>"
                                max="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun)) ?>"
                                onchange="onTanggalInputChange()"
                                class="lh-control-box w-full px-2.5 py-2 text-xs font-mono font-bold">
                        </div>

                        <!-- Distance / Jarak (2 Col) -->
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-extrabold lh-text-sec uppercase mb-1">
                                Distance / Jarak
                            </label>
                            <input type="text" inputmode="decimal" name="jarak" id="inputJarak" value="0" placeholder="0"
                                class="lh-control-box w-full px-2.5 py-2 text-xs font-mono font-bold text-right">
                        </div>

                        <!-- Status Pekerjaan (2 Col) -->
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-extrabold lh-text-sec uppercase mb-1">
                                Status Sumur
                            </label>
                            <select name="status_job" id="selectStatusJob"
                                class="lh-control-box w-full px-2 py-2 text-xs font-bold cursor-pointer">
                                <option value="JOB PROGRESS">JOB PROGRESS</option>
                                <option value="JOB COMPLETED">JOB COMPLETED</option>
                                <option value="JOB SUSPEND">JOB SUSPEND</option>
                            </select>
                        </div>
                    </div>

                    <!-- Navigasi Cepat Pill Tanggal Sumur (Compact Inline Bar) -->
                    <div id="wellDatePillsWrapper" class="lh-sub-panel px-3 py-2 hidden">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="text-[11px] font-bold lh-text-sec flex items-center gap-1 mr-1">
                                    <i class="fa-regular fa-calendar-check text-sky-500"></i>
                                    <span>Jadwal Tanggal:</span>
                                </span>
                                <div id="wellDatePillsContainer" class="flex flex-wrap gap-1.5"></div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                                <span id="wellDateRangeSummary" class="text-[11px] lh-text-muted font-mono mr-1"></span>
                                <button type="button" onclick="lanjutHariBerikutnya()"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-sky-500/15 border border-sky-500/30 text-sky-400 hover:bg-sky-500/25 transition cursor-pointer flex items-center gap-1"
                                    title="Lanjut input hari berikutnya untuk sumur ini">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Lanjut Hari Berikutnya
                                </button>
                                <button type="button" onclick="jumpToNextUnfilledDate()"
                                    class="lh-sisa-mini-btn" title="Lompat ke tanggal kosong berikutnya pada sumur ini">
                                    <i class="fa-solid fa-forward-step mr-0.5"></i> Tgl Kosong
                                </button>
                                <button type="button" onclick="openModalSelesaikanSumur()"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/25 transition cursor-pointer flex items-center gap-1"
                                    title="Pekerjaan sumur telah rampung, tetapkan tanggal berakhir dan selesaikan">
                                    <i class="fa-solid fa-flag-checkered text-[10px]"></i> Selesaikan Sumur
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ─── BAGIAN 2 (TERINTEGRASI LANGSUNG): JAM PRODUKTIF RIG (MIRU & OPERASI) ─── -->
                    <div class="pt-3 border-t lh-divider-t">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md lh-badge-emerald font-mono text-[11px] font-black flex items-center justify-center">02</span>
                                <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title">
                                    Jam Produktif Rig (MIRU &amp; Operasi)
                                </h3>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                <button type="button" onclick="setFullOpsDay()" class="lh-preset-btn !py-1 !px-2.5" title="Set 24 Jam (atau sisa jam hari ini) ke Operasi Normal">
                                    <i class="fa-solid fa-bolt text-emerald-500"></i>
                                    <span>Full Operasi 24j</span>
                                </button>
                                <button type="button" onclick="fillRemainingToField('inputOps')" class="lh-preset-btn !py-1 !px-2.5" title="Masukkan sisa jam hari ini ke Operasi">
                                    <span>+ Sisa ke OPS</span>
                                </button>
                                <button type="button" onclick="fillRemainingToField('inputMiru')" class="lh-preset-btn !py-1 !px-2.5" title="Masukkan sisa jam hari ini ke MIRU">
                                    <span>+ Sisa ke MIRU</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <!-- MIRU -->
                            <div class="lh-sub-panel p-3.5 sm:p-4 flex items-center justify-between gap-3 border-l-4" style="border-left-color: #6366f1;">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border"
                                         style="background: rgba(99, 102, 241, 0.12); border-color: rgba(99, 102, 241, 0.28); color: #6366f1;">
                                        <i class="fa-solid fa-truck-moving text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <label for="inputMiru" class="text-sm sm:text-[15px] font-extrabold lh-text-title block cursor-pointer leading-tight">
                                            MIRU (Moving &amp; Rig Up)
                                        </label>
                                        <span class="text-[11px] lh-text-muted block mt-0.5">Pindah lokasi &amp; pasang menara</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="fillRemainingToField('inputMiru')"
                                        class="lh-sisa-mini-btn !px-3 !py-2 !text-xs !rounded-lg" title="Isi sisa jam ke MIRU">+Sisa</button>
                                    <div class="relative w-36 sm:w-40">
                                        <input type="number" step="0.01" min="0" max="24" name="miru_jam" id="inputMiru" value="0"
                                            oninput="calcDailyTotal()"
                                            class="lh-control-box w-full px-3.5 py-2.5 font-mono text-lg sm:text-xl font-black text-right pr-11">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono font-bold lh-text-muted pointer-events-none">jam</span>
                                    </div>
                                </div>
                            </div>

                            <!-- OPERASI -->
                            <div class="lh-sub-panel p-3.5 sm:p-4 flex items-center justify-between gap-3 border-l-4" style="border-left-color: #10b981;">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border"
                                         style="background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.28); color: #10b981;">
                                        <i class="fa-solid fa-gears text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <label for="inputOps" class="text-sm sm:text-[15px] font-extrabold lh-text-title block cursor-pointer leading-tight">
                                            OPERASI (Wellwork)
                                        </label>
                                        <span class="text-[11px] lh-text-muted block mt-0.5">Jam kerja operasi sumur</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="fillRemainingToField('inputOps')"
                                        class="lh-sisa-mini-btn !px-3 !py-2 !text-xs !rounded-lg" title="Isi sisa jam ke Operasi">+Sisa</button>
                                    <div class="relative w-36 sm:w-40">
                                        <input type="number" step="0.01" min="0" max="24" name="ops_jam" id="inputOps" value="0"
                                            oninput="calcDailyTotal()"
                                            class="lh-control-box w-full px-3.5 py-2.5 font-mono text-lg sm:text-xl font-black text-right pr-11">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono font-bold lh-text-muted pointer-events-none">jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── BAGIAN 3: TABEL 13 POS DOWNTIME / NPT (SELALU TAMPIL RAPI & ENAK DI-INPUT) ─── -->
                <div class="lh-surface-card p-5 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 lh-divider-b pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md lh-badge-amber font-mono text-xs font-black flex items-center justify-center">03</span>
                            <div>
                                <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title">
                                    Tabel 13 Kategori Jam Downtime / NPT (SBWC &amp; Unpaid)
                                </h3>
                                <p class="text-[11px] lh-text-muted">
                                    Isi langsung pada baris kategori yang mengalami downtime, atau biarkan <strong>0</strong> jika operasi lancar.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md lh-badge-amber text-[11px] font-mono font-bold">
                                Subtotal SBWC: <strong id="labelSbwcTotal">0.00</strong>j
                            </span>
                            <span class="px-2.5 py-1 rounded-md lh-badge-rose text-[11px] font-mono font-bold">
                                Subtotal UNPAID: <strong id="labelUnpaidTotal">0.00</strong>j
                            </span>
                        </div>
                    </div>

                    <!-- A. 11 POS SBWC (STANDBY WITH CREW) -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved"></i>
                                A. Kategori SBWC (11 Pos Standby / Cuaca / Eksternal — Tetap Dibayar)
                            </span>
                        </div>

                        <?php
                        $sbwcItems = [
                            ['no' => '01', 'name' => 'dt_rain',     'label' => 'Rain (Hujan)',             'sub' => 'Waiting on weather'],
                            ['no' => '02', 'name' => 'dt_dry_road', 'label' => 'Dry Road (Jalan Basah)',   'sub' => 'Waiting on dry road'],
                            ['no' => '03', 'name' => 'dt_dry_pad',  'label' => 'Dry Pad (Lokasi Becek)',   'sub' => 'Waiting on dry location'],
                            ['no' => '04', 'name' => 'dt_phr_op',   'label' => 'PHR Operation',            'sub' => 'Waiting on PHR operation'],
                            ['no' => '05', 'name' => 'dt_trans',    'label' => 'Transportation',           'sub' => 'Waiting on transportation'],
                            ['no' => '06', 'name' => 'dt_ce_pe',    'label' => 'CE / PE Engineer',         'sub' => 'Waiting on instruction'],
                            ['no' => '08', 'name' => 'dt_daylight', 'label' => 'Daylight Only',            'sub' => 'Standby malam (12 jam)'],
                            ['no' => '09', 'name' => 'dt_phr_well', 'label' => 'PHR Well Problem',         'sub' => 'Masalah teknis sumur PHR'],
                            ['no' => '10', 'name' => 'dt_foam',     'label' => 'Foam Unit',               'sub' => 'Waiting on foam unit'],
                            ['no' => '11', 'name' => 'dt_shutdown', 'label' => 'Shut Down Area',          'sub' => 'Safety / area stop'],
                        ];
                        ?>
                        <div class="lh-ledger-grid">
                            <?php foreach ($sbwcItems as $item): ?>
                            <div class="lh-ledger-item" id="card_<?= $item['name'] ?>">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="lh-num-badge"><?= $item['no'] ?></span>
                                    <div class="min-w-0">
                                        <label for="<?= $item['name'] ?>" class="text-xs font-bold lh-text-title block truncate cursor-pointer">
                                            <?= $item['label'] ?>
                                        </label>
                                        <span class="text-[10px] lh-text-muted block truncate"><?= $item['sub'] ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" onclick="fillRemainingToField('<?= $item['name'] ?>')"
                                        class="lh-sisa-mini-btn" title="Isi sisa jam hari ini ke <?= $item['label'] ?>">
                                        +Sisa
                                    </button>
                                    <div class="relative w-24">
                                        <input type="number" step="0.25" min="0" max="24"
                                            name="<?= $item['name'] ?>" id="<?= $item['name'] ?>" value="0"
                                            oninput="calcDailyTotal()"
                                            class="dt-input dt-sbwc lh-control-box w-full px-2.5 py-1.5 text-right font-mono text-xs font-bold pr-7">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-mono lh-text-muted pointer-events-none">j</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pos 07: Waiting on 3rd Party (Bisa Tanpa Perusahaan atau Pilih Perusahaan) -->
                        <div class="lh-ledger-item flex-col !items-stretch gap-2.5" id="card_dt_3rd_party">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="lh-num-badge">07</span>
                                    <div>
                                        <span class="text-xs font-bold lh-text-title">
                                            Waiting on 3rd Party (Perusahaan Jasa / Umum)
                                        </span>
                                        <span class="text-[11px] lh-text-muted ml-1.5">
                                            Total: <strong id="label3rdTotal" class="font-mono text-amber-500">0.00</strong> Jam
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="addTpRow()"
                                    class="lh-preset-btn !py-1 !px-2.5 !text-[11px]">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Baris 3rd Party</span>
                                </button>
                            </div>

                            <input type="hidden" name="dt_3rd_party" id="dt_3rd_party" value="0" class="dt-input dt-sbwc">
                            <div id="tpRowsList" class="space-y-2 pt-1">
                                <div class="tp-entry-row grid grid-cols-12 gap-2 items-center">
                                    <div class="col-span-7 sm:col-span-8">
                                        <select name="tp_company[]" class="tp-company-select lh-control-box w-full px-2.5 py-1.5 text-xs font-semibold cursor-pointer">
                                            <option value="0">— Tanpa Perusahaan (3rd Party Umum) —</option>
                                            <?php foreach ($thirdParties ?? [] as $tp): ?>
                                            <option value="<?= $tp['id'] ?>">Perusahaan: <?= esc($tp['nama']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-span-4 sm:col-span-3">
                                        <input type="number" step="0.25" min="0" max="24" name="tp_hours[]" value="0" oninput="calc3rdPartyTotal()"
                                            placeholder="0.00 Jam"
                                            class="dt-tp-input lh-control-box w-full px-2.5 py-1.5 text-right font-mono text-xs font-bold">
                                    </div>
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" onclick="removeTpRow(this)" title="Hapus / Reset Baris"
                                            class="lh-sisa-mini-btn w-7 h-7 flex items-center justify-center">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. 2 POS UNPAID (NPT INTERNAL RIG) -->
                    <div class="space-y-2.5 pt-3 lh-divider-t">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            B. Kategori UNPAID (2 Pos NPT Internal Rig — Mengurangi Revenue &amp; Reliability)
                        </span>

                        <div class="lh-ledger-grid">
                            <!-- Pos 12 (Unpaid 01): Repair / Waiting For Rig -->
                            <div class="lh-ledger-item" id="card_dt_rig">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="lh-num-badge">12</span>
                                    <div class="min-w-0">
                                        <label for="dt_rig" class="text-xs font-bold lh-text-title block truncate cursor-pointer">
                                            Repair / Waiting For Rig
                                        </label>
                                        <span class="text-[10px] lh-text-muted block truncate">Kerusakan mesin rig, drawwork, pompa</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" onclick="fillRemainingToField('dt_rig')"
                                        class="lh-sisa-mini-btn" title="Isi sisa jam ke Repair Rig">
                                        +Sisa
                                    </button>
                                    <div class="relative w-24">
                                        <input type="number" step="0.25" min="0" max="24"
                                            name="dt_rig" id="dt_rig" value="0"
                                            oninput="calcDailyTotal()"
                                            class="dt-input dt-unpaid lh-control-box w-full px-2.5 py-1.5 text-right font-mono text-xs font-bold pr-7">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-mono lh-text-muted pointer-events-none">j</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pos 13 (Unpaid 02): Waiting Tool & Personel -->
                            <div class="lh-ledger-item" id="card_dt_tool">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="lh-num-badge">13</span>
                                    <div class="min-w-0">
                                        <label for="dt_tool" class="text-xs font-bold lh-text-title block truncate cursor-pointer">
                                            Waiting Tool &amp; Personel
                                        </label>
                                        <span class="text-[10px] lh-text-muted block truncate">Kendala peralatan kerja atau kru rig</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" onclick="fillRemainingToField('dt_tool')"
                                        class="lh-sisa-mini-btn" title="Isi sisa jam ke Waiting Tool">
                                        +Sisa
                                    </button>
                                    <div class="relative w-24">
                                        <input type="number" step="0.25" min="0" max="24"
                                            name="dt_tool" id="dt_tool" value="0"
                                            oninput="calcDailyTotal()"
                                            class="dt-input dt-unpaid lh-control-box w-full px-2.5 py-1.5 text-right font-mono text-xs font-bold pr-7">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-mono lh-text-muted pointer-events-none">j</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── BAGIAN 4: REMARK / KETERANGAN HARIAN ─── -->
                <div class="lh-surface-card p-5 space-y-3.5">
                    <div class="flex flex-wrap items-center justify-between gap-2 lh-divider-b pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md lh-badge-sky font-mono text-xs font-black flex items-center justify-center">04</span>
                            <div>
                                <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title">
                                    Keterangan / Remark Operasional Harian
                                </h3>
                                <p class="text-[11px] lh-text-muted">
                                    Terisi otomatis sesuai jam Downtime/NPT (contoh: <span class="font-mono font-semibold">0,5 HR SWA Heavy Rain + Thunder. 5,5 HR WO Dry Road.</span>)
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="generateSmartRemark(true)"
                            class="lh-preset-btn text-[11px]">
                            <i class="fa-solid fa-wand-magic-sparkles text-sky-500"></i>
                            <span>Buat Remark Otomatis dari Jam</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold lh-text-sec uppercase mb-1.5">
                                Remark Kegiatan / NPT Umum
                            </label>
                            <textarea name="remark_npt" id="remarkNpt" rows="2"
                                oninput="onManualRemarkInput('npt')"
                                placeholder="Otomatis terisi saat jam diisi (Contoh: 0,5 HR SWA Heavy Rain + Thunder. 5,5 HR WO Dry Road.)"
                                class="lh-control-box w-full px-3 py-2 text-xs font-medium"></textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-rose-500 uppercase mb-1.5">
                                Remark Khusus Unpaid (Jika Ada Kerusakan Rig/Alat)
                            </label>
                            <textarea name="remark_unpaid" id="remarkUnpaid" rows="2"
                                oninput="onManualRemarkInput('unpaid')"
                                placeholder="Otomatis terisi jika ada jam Unpaid (Contoh: 1,5 HR Repair Rig.)"
                                class="lh-control-box w-full px-3 py-2 text-xs font-medium"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ─── STRATEGIC COMPLETION & TRANSITION BAR (SAAT SELESAI INPUT / HARI TERAKHIR SUMUR) ─── -->
                <div id="wellCompleteNextBanner" class="hidden lh-surface-card p-4 sm:p-5 border-l-4 border-l-emerald-500 space-y-3 transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                        <div class="flex items-start gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl lh-badge-emerald flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-flag-checkered text-base text-emerald-500"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        Fase Selesai
                                    </span>
                                    <h4 class="text-sm font-black lh-text-title tracking-tight" id="nextWellBannerTitle">
                                        Jadwal Sumur Aktif Selesai — Siap Pindah ke Sumur Berikutnya?
                                    </h4>
                                </div>
                                <p class="text-xs lh-text-sec mt-1" id="nextWellBannerDesc">
                                    Seluruh log harian pada jadwal sumur ini telah tercatat. Klik tombol di kanan untuk menyimpan catatan hari ini dan langsung membuat <strong>Well #<?= $nextNoWellAuto ?></strong>.
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons di Akhir Input -->
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button type="button" onclick="submitLogAndOpenNewWell()"
                                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-600/25 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                                <i class="fa-solid fa-floppy-disk text-xs"></i>
                                <span>Simpan &amp; Lanjut Buat Well #<span class="js-next-well-num"><?= $nextNoWellAuto ?></span> →</span>
                            </button>
                            <a href="<?= $tambahSumurUrl ?>"
                                class="lh-preset-btn !py-2.5 !px-3.5 !text-xs !border-emerald-500/40 hover:!border-emerald-500"
                                title="Buka form pendaftaran sumur baru tanpa menyimpan log">
                                <span>Buka Form Well #<span class="js-next-well-num"><?= $nextNoWellAuto ?></span> ↗</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════════════
                 KOLOM KANAN (4 COL): MONITOR 24 JAM & TOMBOL SIMPAN + DAFTAR SUMUR
                 (Berada di sisi kanan sehingga TIDAK menutupi form saat di-scroll!)
                 ══════════════════════════════════════════════════════════════════ -->
            <div class="xl:col-span-4 space-y-4 xl:sticky xl:top-4">

                <!-- KARTU 1: MONITOR KALKULATOR 24 JAM & TOMBOL SIMPAN -->
                <div class="lh-surface-card p-5 space-y-4">
                    <div class="flex items-center justify-between gap-2 lh-divider-b pb-3">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-widest lh-text-muted block">Status Kalkulasi</span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title mt-0.5">
                                Monitor Neraca 24 Jam
                            </h3>
                        </div>
                        <span id="formModeBadge" class="px-2.5 py-1 rounded-md lh-badge-emerald text-[10px] font-extrabold uppercase tracking-wider">
                            Input Baru
                        </span>
                    </div>

                    <!-- Angka Utama Total Jam -->
                    <div class="lh-sub-panel p-4 text-center">
                        <div class="text-[11px] font-bold uppercase tracking-wider lh-text-muted mb-1" id="remainingHoursHint">
                            Sisa Hari Ini: 24.00 Jam
                        </div>
                        <div id="labelGrandTotal" class="font-mono font-black text-2xl tracking-tight text-sky-500">
                            0.00 / 24.00 Jam
                        </div>

                        <!-- Progress Bar 24 Jam -->
                        <div class="w-full h-2.5 rounded-full bg-slate-500/15 overflow-hidden flex mt-3">
                            <div id="barOtherWell" class="h-full bg-violet-500 transition-all duration-200" style="width:0%" title="Jam Sumur Lain"></div>
                            <div id="barMiru" class="h-full bg-sky-500 transition-all duration-200" style="width:0%" title="MIRU"></div>
                            <div id="barOps" class="h-full bg-emerald-500 transition-all duration-200" style="width:0%" title="Operasi"></div>
                            <div id="barSbwc" class="h-full bg-amber-500 transition-all duration-200" style="width:0%" title="SBWC"></div>
                            <div id="barUnpaid" class="h-full bg-rose-500 transition-all duration-200" style="width:0%" title="Unpaid"></div>
                        </div>

                        <div id="badgeValidation" class="text-[11px] font-mono font-bold lh-text-sec mt-2.5">
                            ✓ Siap diisi (Maksimal 24.00 Jam/Hari)
                        </div>
                    </div>

                    <!-- Breakdown Rincian Jam -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="lh-sub-panel p-2.5">
                            <span class="text-[10px] font-bold uppercase lh-text-muted block">MIRU</span>
                            <strong id="recapMiru" class="font-mono text-sm font-black text-sky-500">0.00</strong>
                            <span class="text-[10px] lh-text-muted">j</span>
                        </div>
                        <div class="lh-sub-panel p-2.5">
                            <span class="text-[10px] font-bold uppercase lh-text-muted block">Operasi</span>
                            <strong id="recapOps" class="font-mono text-sm font-black text-emerald-500">0.00</strong>
                            <span class="text-[10px] lh-text-muted">j</span>
                        </div>
                        <div class="lh-sub-panel p-2.5">
                            <span class="text-[10px] font-bold uppercase lh-text-muted block">Downtime</span>
                            <strong id="recapDt" class="font-mono text-sm font-black text-amber-500">0.00</strong>
                            <span class="text-[10px] lh-text-muted">j</span>
                        </div>
                    </div>

                    <!-- Tombol Simpan, Simpan & Lanjut Sumur Baru, serta Reset -->
                    <div class="space-y-2 pt-1">
                        <!-- Contextual Completion Banner for Sticky Sidebar -->
                        <div id="sidebarWellCompleteNotice" class="hidden lh-sidebar-notice space-y-1 transition-all">
                            <div class="flex items-center gap-2 text-xs font-black tracking-tight">
                                <i class="fa-solid fa-flag-checkered"></i>
                                <span id="sidebarNoticeTitle">Jadwal Sumur Telah Selesai</span>
                            </div>
                            <p class="text-[11px] leading-snug" id="sidebarNoticeDesc">
                                Siap lanjut? Gunakan tombol hijau di bawah untuk simpan &amp; lanjut ke Well berikutnya.
                            </p>
                        </div>

                        <button type="submit" id="btnSubmitDaily" <?= empty($wells) ? 'disabled' : '' ?>
                            onclick="document.getElementById('inputAfterSaveAction').value = ''"
                            class="lh-btn-submit-main">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>SIMPAN DAILY REPORT</span>
                        </button>

                        <button type="button" id="btnSaveAndNewWell" <?= empty($wells) ? 'disabled' : '' ?>
                            onclick="submitLogAndOpenNewWell()"
                            class="lh-btn-save-next"
                            title="Simpan catatan hari ini, lalu otomatis buka formulir pembuatan Sumur Baru (Well berikutnya)">
                            <i class="fa-solid fa-plus-circle text-xs"></i>
                            <span>Simpan &amp; Lanjut Buat Well #<span class="js-next-well-num"><?= $nextNoWellAuto ?></span></span>
                        </button>

                        <button type="button" onclick="resetFormToDefault()"
                            class="lh-btn-reset">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Reset / Bersihkan Form</span>
                        </button>
                    </div>
                </div>

                <!-- KARTU 2: DAFTAR SUMUR BULAN INI -->
                <div class="lh-surface-card p-5">
                    <div class="flex items-center justify-between gap-2 mb-3 lh-divider-b pb-3">
                        <div>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title flex items-center gap-1.5">
                                <i class="fa-solid fa-oil-well text-sky-500"></i>
                                <span>Daftar Sumur Bulan Ini (<span id="wellCountBadge"><?= count($wells) ?></span> Well)</span>
                            </h3>
                            <p class="text-[11px] lh-text-muted mt-0.5">
                                Klik sumur untuk memilih &amp; mengisi tanggalnya.
                            </p>
                        </div>
                        <a href="<?= $tambahSumurUrl ?>"
                            class="px-3.5 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-extrabold transition flex items-center gap-1.5 shrink-0 shadow-sm">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Well #<?= $nextNoWellAuto ?></span>
                        </a>
                    </div>

                    <div id="wellCardsContainer" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        <?php if (!empty($wells)): ?>
                        <?php foreach ($wells as $w): ?>
                        <?php
                            $wStart = $w['tanggal_mulai'] ?? '';
                            $wEnd   = $w['tanggal_selesai'] ?? $wStart;
                            $stJob  = strtoupper($w['status_job'] ?: 'JOB PROGRESS');
                            $stBadge = 'lh-badge-amber';
                            if ($stJob === 'JOB COMPLETED') {
                                $stBadge = 'lh-badge-emerald';
                            } elseif ($stJob === 'JOB SUSPEND') {
                                $stBadge = 'lh-badge-rose';
                            }
                        ?>
                        <button type="button"
                            onclick="selectWellFromCard('<?= $w['id'] ?>')"
                            id="wellCard_<?= $w['id'] ?>"
                            class="well-summary-card w-full lh-sub-panel p-2.5 text-left hover:border-sky-500 transition flex items-center justify-between gap-2 cursor-pointer">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded lh-badge-sky font-mono text-[10px] font-black">
                                        #<?= esc($w['no_well']) ?>
                                    </span>
                                    <span class="text-xs font-extrabold lh-text-title truncate">
                                        <?= esc($w['nama_lokasi']) ?>
                                    </span>
                                </div>
                                <div class="text-[11px] lh-text-muted font-mono mt-0.5">
                                    <?= $wStart ? date('d/m', strtotime($wStart)) : '-' ?> s/d <?= $wEnd ? date('d/m/Y', strtotime($wEnd)) : '-' ?>
                                    · Total: <strong class="lh-text-title"><?= number_format((float)($w['total_jam'] ?? 0), 2) ?>j</strong>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase shrink-0 <?= $stBadge ?>">
                                <?= esc($stJob) ?>
                            </span>
                        </button>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div id="emptyWellsSidebarText" class="text-center py-5 text-xs lh-text-muted">
                            Belum ada sumur terdaftar di bulan ini. Klik <strong>Tambah Well #1</strong> di atas.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- ═══ 3. TABEL RIWAYAT LOG HARIAN BULAN INI (FULL-WIDTH DI BAWAH AGAR LEGA & RAPI) ═══ -->
    <div class="lh-surface-card overflow-hidden">
        <div class="p-4 lh-divider-b flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider lh-text-title flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                    <span>Riwayat Catatan Log Harian Bulan <?= $bulanNames[$bulan] ?> <?= $tahun ?> (<?= $totalLogsCount ?> Hari Tercatat)</span>
                </h3>
                <p class="text-[11px] lh-text-muted mt-0.5">
                    Klik baris tanggal mana saja untuk memuat datanya ke formulir di atas jika ingin merevisi.
                </p>
            </div>
            <input type="text" id="searchHistoryInput" oninput="filterHistoryTable()"
                placeholder="Cari tanggal / nama sumur..."
                class="lh-control-box px-3 py-1.5 text-xs w-56">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="lh-table-head">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-bold">Sumur (Well)</th>
                        <th class="px-3 py-3 text-right font-bold">Distance</th>
                        <th class="px-3 py-3 text-right font-bold">MIRU (Jam)</th>
                        <th class="px-3 py-3 text-right font-bold">Operasi (Jam)</th>
                        <th class="px-3 py-3 text-right font-bold">SBWC (Jam)</th>
                        <th class="px-3 py-3 text-right font-bold">Unpaid (Jam)</th>
                        <th class="px-3 py-3 text-center font-bold">Total Jam</th>
                        <th class="px-4 py-3 text-left font-bold">Remark Harian</th>
                        <th class="px-4 py-3 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <?php if (empty($recentLogs)): ?>
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center lh-text-muted">
                            Belum ada catatan log harian di bulan <?= $bulanNames[$bulan] ?> <?= $tahun ?>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentLogs as $lg): ?>
                    <?php
                        $totHrs    = (float)($lg['total_hrs'] ?? 0);
                        $isFull    = abs($totHrs - 24.0) < 0.01;
                        $isOver    = $totHrs > 24.001;
                        $unpaidHrs = (float)($lg['unpaid_hrs'] ?? ((float)($lg['dt_rig'] ?? 0) + (float)($lg['dt_tool'] ?? 0)));
                        $sbwcHrs   = (float)($lg['sbwc_hrs'] ?? max(0, (float)($lg['total_dt'] ?? 0) - $unpaidHrs));
                    ?>
                    <tr class="lh-table-row cursor-pointer history-row-item"
                        data-rowkey="<?= $lg['daily_report_id'] ?>_<?= $lg['tanggal'] ?>"
                        data-search="<?= strtolower(esc($lg['tanggal'] . ' ' . ($lg['nama_lokasi'] ?? '') . ' well ' . ($lg['no_well'] ?? '') . ' ' . ($lg['remark_npt'] ?? ''))) ?>"
                        onclick='loadLogToForm(<?= json_encode($lg, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, true)'>
                        <td class="px-4 py-3 font-mono font-bold lh-text-title whitespace-nowrap">
                            <?= date('d M Y', strtotime($lg['tanggal'])) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-1.5 py-0.5 rounded lh-badge-sky font-mono text-[10px] font-bold mr-1">
                                #<?= esc($lg['no_well']) ?>
                            </span>
                            <span class="font-bold lh-text-title"><?= esc($lg['nama_lokasi'] ?? '-') ?></span>
                        </td>
                        <td class="px-3 py-3 text-right font-mono <?= (float)($lg['jarak'] ?? 0) > 0 ? 'font-bold text-emerald-500' : 'lh-text-muted' ?>">
                            <?= (float)($lg['jarak'] ?? 0) > 0 ? number_format((float)$lg['jarak'], 2, ',', '.') : '-' ?>
                        </td>
                        <td class="px-3 py-3 text-right font-mono <?= $lg['miru_jam'] > 0 ? 'font-bold text-sky-500' : 'lh-text-muted' ?>">
                            <?= $lg['miru_jam'] > 0 ? number_format($lg['miru_jam'], 2) : '-' ?>
                        </td>
                        <td class="px-3 py-3 text-right font-mono <?= $lg['ops_jam'] > 0 ? 'font-bold text-emerald-500' : 'lh-text-muted' ?>">
                            <?= $lg['ops_jam'] > 0 ? number_format($lg['ops_jam'], 2) : '-' ?>
                        </td>
                        <td class="px-3 py-3 text-right font-mono <?= $sbwcHrs > 0 ? 'font-bold text-amber-500' : 'lh-text-muted' ?>">
                            <?= $sbwcHrs > 0 ? number_format($sbwcHrs, 2) : '-' ?>
                        </td>
                        <td class="px-3 py-3 text-right font-mono <?= $unpaidHrs > 0 ? 'font-bold text-rose-500' : 'lh-text-muted' ?>">
                            <?= $unpaidHrs > 0 ? number_format($unpaidHrs, 2) : '-' ?>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2.5 py-0.5 rounded-md font-mono text-[11px] font-bold <?= $isOver ? 'lh-badge-rose' : ($isFull ? 'lh-badge-emerald' : 'lh-badge-sky') ?>">
                                <?= number_format($totHrs, 2) ?>j
                            </span>
                        </td>
                        <td class="px-4 py-3 lh-text-sec max-w-xs truncate" title="<?= esc($lg['remark_npt']) ?>">
                            <?= esc($lg['remark_npt'] ?: ($lg['remark_unpaid'] ?: '-')) ?>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap" onclick="event.stopPropagation()">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button"
                                    onclick='loadLogToForm(<?= json_encode($lg, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, true)'
                                    class="px-2.5 py-1 rounded-lg lh-badge-sky text-[11px] font-bold transition cursor-pointer">
                                    <i class="fa-solid fa-pen mr-1"></i> Edit
                                </button>
                                <form method="POST" action="<?= base_url('daily-report/hapus-log') ?>"
                                    onsubmit="return confirmHapusLog('<?= date('d/m/Y', strtotime($lg['tanggal'])) ?>', 'Well #<?= esc($lg['no_well']) ?> - <?= esc($lg['nama_lokasi'] ?? '') ?>')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="daily_report_id" value="<?= $lg['daily_report_id'] ?>">
                                    <input type="hidden" name="tanggal" value="<?= $lg['tanggal'] ?>">
                                    <button type="submit"
                                        class="px-2.5 py-1 rounded-lg lh-badge-rose text-[11px] font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Selesaikan Sumur -->
<div id="modalSelesaikanSumur" class="lh-modal-backdrop" onclick="if(event.target===this) closeSelesaikanModal()">
    <div class="lh-modal-dialog max-w-md w-full p-5" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b lh-divider-b mb-4">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 font-black flex items-center justify-center text-sm">
                    <i class="fa-solid fa-flag-checkered"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black lh-text-title" id="modalSelesaikanTitle">Selesaikan Pekerjaan Sumur</h3>
                    <p class="text-[11px] lh-text-muted">Tetapkan tanggal berakhir &amp; tandai sumur telah rampung</p>
                </div>
            </div>
            <button type="button" onclick="closeSelesaikanModal()" class="lh-badge-slate w-7 h-7 rounded-lg flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formSelesaikanSumur" method="POST" action="">
            <?= csrf_field() ?>
            <div class="space-y-3.5">
                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300">
                    <p id="selesaikanWellSummaryText">Pekerjaan sumur ini akan ditandai <strong>JOB COMPLETED</strong> dengan tanggal akhir yang Anda tentukan di bawah.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold lh-text-sec mb-1">
                        Tanggal Berakhir / Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai" id="selesaikanTanggalInput" required
                        min="<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>"
                        max="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun)) ?>"
                        class="lh-control-box w-full px-3 py-2 text-xs font-mono font-bold rounded-lg">
                    <span class="text-[10px] lh-text-muted mt-0.5 block">Selesai pada tanggal log terakhir atau tentukan sesuai operasi di lapangan.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold lh-text-sec mb-1">Status Akhir Pekerjaan</label>
                    <select name="status_job" id="selesaikanStatusInput" class="lh-control-box w-full px-3 py-2 text-xs font-bold rounded-lg cursor-pointer">
                        <option value="JOB COMPLETED" selected>JOB COMPLETED (Pekerjaan Selesai)</option>
                        <option value="JOB SUSPEND">JOB SUSPEND (Ditangguhkan)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t lh-divider-t">
                <button type="button" onclick="closeSelesaikanModal()" class="lh-secondary-btn !py-2 !px-3.5 text-xs font-bold">
                    Batal
                </button>
                <button type="submit" class="lh-submit-btn !py-2 !px-4 text-xs font-bold flex items-center gap-1.5 !bg-emerald-600 hover:!bg-emerald-500 !text-white">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan &amp; Selesaikan Sumur</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const periodMinDate = '<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>';
    const periodMaxDate = '<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun)) ?>';
    const existingLogsData = <?= json_encode($recentLogs ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const logsByWellDateMap = {};
    existingLogsData.forEach(lg => {
        const key = `${lg.daily_report_id}_${lg.tanggal}`;
        logsByWellDateMap[key] = lg;
    });

    function getOtherWellsInfoOnDate(currentWellId, dateStr) {
        let otherHours = 0;
        const labels = [];
        existingLogsData.forEach(lg => {
            if (lg.tanggal === dateStr && String(lg.daily_report_id) !== String(currentWellId)) {
                const h = parseFloat(lg.total_hrs) || 0;
                otherHours += h;
                labels.push(`Well #${lg.no_well} (${lg.nama_lokasi || ''}: ${h.toFixed(2)}j)`);
            }
        });
        return {
            hours: otherHours,
            label: labels.join(', ')
        };
    }

    function getRemainingDayHoursExcluding(targetFieldId) {
        const curWellId = document.getElementById('selectWell')?.value || '';
        const curDateStr = document.getElementById('inputTanggal')?.value || '';
        const otherInfo = (curWellId && curDateStr) ? getOtherWellsInfoOnDate(curWellId, curDateStr) : { hours: 0 };
        const maxForThisWell = Math.max(0, 24.0 - otherInfo.hours);

        let usedOtherFields = 0;
        const allIds = [
            'inputMiru', 'inputOps',
            'dt_rain', 'dt_dry_road', 'dt_dry_pad', 'dt_phr_op', 'dt_trans',
            'dt_ce_pe', 'dt_3rd_party', 'dt_daylight', 'dt_phr_well', 'dt_foam',
            'dt_shutdown', 'dt_rig', 'dt_tool'
        ];
        allIds.forEach(id => {
            if (id !== targetFieldId) {
                const el = document.getElementById(id);
                if (el) usedOtherFields += parseFloat(el.value) || 0;
            }
        });
        return Math.max(0, +(maxForThisWell - usedOtherFields).toFixed(2));
    }

    function fillRemainingToField(fieldId) {
        const el = document.getElementById(fieldId);
        if (!el) return;
        const sisa = getRemainingDayHoursExcluding(fieldId);
        el.value = sisa;
        calcDailyTotal();
    }

    function setFullOpsDay() {
        document.getElementById('inputMiru').value = '0';
        document.querySelectorAll('.dt-input').forEach(inp => inp.value = '0');
        const tpList = document.getElementById('tpRowsList');
        if (tpList) {
            tpList.innerHTML = '';
            addTpRow(0, 0);
        }
        const sisa = getRemainingDayHoursExcluding('inputOps');
        document.getElementById('inputOps').value = sisa;
        calcDailyTotal();
    }

    let isRemarkNptAuto = true;
    let isRemarkUnpaidAuto = true;
    let lastAutoRemarkNpt = '';
    let lastAutoRemarkUnpaid = '';

    function formatHrIndo(val) {
        const num = Math.round((parseFloat(val) || 0) * 100) / 100;
        return String(num).replace('.', ',');
    }

    function onManualRemarkInput(type) {
        const nptEl = document.getElementById('remarkNpt');
        const unpEl = document.getElementById('remarkUnpaid');
        if (type === 'npt') {
            const val = (nptEl?.value || '').trim();
            isRemarkNptAuto = (val === '' || val === lastAutoRemarkNpt);
            // Jika user mengetik remark custom di kolom utama sedangkan kolom Unpaid masih berisi template otomatis, sinkronkan
            if (!isRemarkNptAuto && isRemarkUnpaidAuto && unpEl) {
                const rigDt  = parseFloat(document.getElementById('dt_rig')?.value) || 0;
                const toolDt = parseFloat(document.getElementById('dt_tool')?.value) || 0;
                unpEl.value = (rigDt > 0 || toolDt > 0) ? val : '';
            }
        } else if (type === 'unpaid') {
            const val = (unpEl?.value || '').trim();
            isRemarkUnpaidAuto = (val === '' || val === lastAutoRemarkUnpaid);
            // Jika user mengetik remark custom di kolom Unpaid sedangkan kolom NPT utama masih template otomatis, sinkronkan
            if (!isRemarkUnpaidAuto && isRemarkNptAuto && nptEl) {
                const { nptText, unpaidText } = buildBesmindoAutoRemark();
                if (unpaidText && nptText.includes(unpaidText)) {
                    nptEl.value = nptText.replace(unpaidText, val);
                } else {
                    nptEl.value = val;
                }
            }
        }
    }

    function buildBesmindoAutoRemark() {
        const nptParts = [];
        const unpaidParts = [];

        const sbwcMap = [
            { id: 'dt_rain',     phrase: 'SWA Heavy Rain + Thunder' },
            { id: 'dt_dry_road', phrase: 'WO Dry Road' },
            { id: 'dt_dry_pad',  phrase: 'WO Dry Well Pad' },
            { id: 'dt_phr_op',   phrase: 'WO PHR Operator' },
            { id: 'dt_trans',    phrase: 'WO Trans Sharing' },
            { id: 'dt_ce_pe',    phrase: 'WO CE/PE' }
        ];

        sbwcMap.forEach(item => {
            const val = parseFloat(document.getElementById(item.id)?.value) || 0;
            if (val > 0) {
                nptParts.push(`${formatHrIndo(val)} HR ${item.phrase}.`);
            }
        });

        // Pos 07: 3rd Party (bisa rincian per perusahaan atau umum)
        let tpHandled = false;
        const tpRows = document.querySelectorAll('#tpRowsList .tp-entry-row');
        if (tpRows.length > 0) {
            tpRows.forEach(row => {
                const hrs = parseFloat(row.querySelector('.dt-tp-input')?.value) || 0;
                if (hrs > 0) {
                    tpHandled = true;
                    const sel = row.querySelector('.tp-company-select');
                    const compId = parseInt(sel?.value || '0', 10);
                    let compName = '';
                    if (compId > 0 && sel && sel.selectedIndex >= 0) {
                        compName = (sel.options[sel.selectedIndex].text || '').replace(/^Perusahaan:\s*/i, '').trim();
                    }
                    if (compName) {
                        nptParts.push(`${formatHrIndo(hrs)} HR WO 3rd Party (${compName}).`);
                    } else {
                        nptParts.push(`${formatHrIndo(hrs)} HR WO 3rd Party.`);
                    }
                }
            });
        }
        if (!tpHandled) {
            const tpVal = parseFloat(document.getElementById('dt_3rd_party')?.value) || 0;
            if (tpVal > 0) {
                nptParts.push(`${formatHrIndo(tpVal)} HR WO 3rd Party.`);
            }
        }

        const sbwcMapTail = [
            { id: 'dt_daylight', phrase: 'WO Daylight' },
            { id: 'dt_phr_well', phrase: 'WO PHR Well & Accessories' },
            { id: 'dt_foam',     phrase: 'WO Foam Unit' },
            { id: 'dt_shutdown', phrase: 'SWA Shut Down' }
        ];

        sbwcMapTail.forEach(item => {
            const val = parseFloat(document.getElementById(item.id)?.value) || 0;
            if (val > 0) {
                nptParts.push(`${formatHrIndo(val)} HR ${item.phrase}.`);
            }
        });

        // Pos Unpaid (12 & 13)
        const rigDt  = parseFloat(document.getElementById('dt_rig')?.value) || 0;
        const toolDt = parseFloat(document.getElementById('dt_tool')?.value) || 0;
        if (rigDt > 0) {
            const p = `${formatHrIndo(rigDt)} HR Repair Rig.`;
            nptParts.push(p);
            unpaidParts.push(p);
        }
        if (toolDt > 0) {
            const p = `${formatHrIndo(toolDt)} HR WO BMS Tool.`;
            nptParts.push(p);
            unpaidParts.push(p);
        }

        return {
            nptText: nptParts.join(' '),
            unpaidText: unpaidParts.join(' ')
        };
    }

    function generateSmartRemark(forceOverwrite = false) {
        const { nptText, unpaidText } = buildBesmindoAutoRemark();
        const remarkNptEl = document.getElementById('remarkNpt');
        const remarkUnpaidEl = document.getElementById('remarkUnpaid');

        if (forceOverwrite) {
            isRemarkNptAuto = true;
            isRemarkUnpaidAuto = true;
        }

        if (remarkNptEl && (forceOverwrite || isRemarkNptAuto)) {
            remarkNptEl.value = nptText;
            lastAutoRemarkNpt = nptText;
            isRemarkNptAuto = true;
        }

        if (remarkUnpaidEl && (forceOverwrite || isRemarkUnpaidAuto)) {
            remarkUnpaidEl.value = unpaidText;
            lastAutoRemarkUnpaid = unpaidText;
            isRemarkUnpaidAuto = true;
        }
    }

    function navigateLog() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('daily-report/log-harian') ?>/${rigId}/${bulan}/${tahun}`;
    }

    const thirdPartyOptionsHtml = `
        <option value="0">— Tanpa Perusahaan (3rd Party Umum) —</option>
        <?php foreach ($thirdParties ?? [] as $tp): ?>
        <option value="<?= $tp['id'] ?>">Perusahaan: <?= esc($tp['nama']) ?></option>
        <?php endforeach; ?>
    `;

    function addTpRow(companyId = 0, hours = 0) {
        const list = document.getElementById('tpRowsList');
        const row = document.createElement('div');
        row.className = 'tp-entry-row grid grid-cols-12 gap-2 items-center';
        row.innerHTML = `
            <div class="col-span-7 sm:col-span-8">
                <select name="tp_company[]" onchange="calc3rdPartyTotal()" class="tp-company-select lh-control-box w-full px-2.5 py-1.5 text-xs font-semibold cursor-pointer">
                    ${thirdPartyOptionsHtml}
                </select>
            </div>
            <div class="col-span-4 sm:col-span-3">
                <input type="number" step="0.25" min="0" max="24" name="tp_hours[]" value="${hours}" oninput="calc3rdPartyTotal()"
                    placeholder="0.00 Jam"
                    class="dt-tp-input lh-control-box w-full px-2.5 py-1.5 text-right font-mono text-xs font-bold">
            </div>
            <div class="col-span-1 flex justify-end">
                <button type="button" onclick="removeTpRow(this)" title="Hapus / Reset Baris"
                    class="lh-sisa-mini-btn w-7 h-7 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        `;
        row.querySelector('.tp-company-select').value = String(companyId);
        list.appendChild(row);
        calc3rdPartyTotal();
    }

    function removeTpRow(btn) {
        const list = document.getElementById('tpRowsList');
        const rows = list.querySelectorAll('.tp-entry-row');
        const currentRow = btn.closest('.tp-entry-row');
        if (rows.length > 1) {
            currentRow.remove();
        } else {
            currentRow.querySelector('.tp-company-select').value = '0';
            currentRow.querySelector('.dt-tp-input').value = '0';
        }
        calc3rdPartyTotal();
    }

    function calc3rdPartyTotal() {
        let tpSum = 0;
        document.querySelectorAll('.dt-tp-input').forEach(inp => {
            tpSum += parseFloat(inp.value) || 0;
        });
        document.getElementById('label3rdTotal').textContent = tpSum.toFixed(2);
        document.getElementById('dt_3rd_party').value = tpSum;
        calcDailyTotal();
    }

    function calcDailyTotal() {
        const miru = parseFloat(document.getElementById('inputMiru')?.value) || 0;
        const ops  = parseFloat(document.getElementById('inputOps')?.value) || 0;

        let sbwcSum = 0;
        document.querySelectorAll('.dt-sbwc').forEach(inp => {
            const v = parseFloat(inp.value) || 0;
            sbwcSum += v;
            const card = document.getElementById('card_' + inp.id);
            if (card) {
                card.classList.toggle('has-value', v > 0);
            }
        });

        let unpaidSum = 0;
        document.querySelectorAll('.dt-unpaid').forEach(inp => {
            const v = parseFloat(inp.value) || 0;
            unpaidSum += v;
            const card = document.getElementById('card_' + inp.id);
            if (card) {
                card.classList.toggle('has-value-unpaid', v > 0);
            }
        });

        const totalDt = sbwcSum + unpaidSum;
        const grandTotal = miru + ops + totalDt;

        document.getElementById('labelSbwcTotal').textContent = sbwcSum.toFixed(2);
        document.getElementById('labelUnpaidTotal').textContent = unpaidSum.toFixed(2);

        document.getElementById('recapMiru').textContent = miru.toFixed(2);
        document.getElementById('recapOps').textContent = ops.toFixed(2);
        document.getElementById('recapDt').textContent = totalDt.toFixed(2);

        const grandEl = document.getElementById('labelGrandTotal');
        const badgeEl = document.getElementById('badgeValidation');
        const remHint = document.getElementById('remainingHoursHint');
        const btnSubmit = document.getElementById('btnSubmitDaily');

        const curWellId = document.getElementById('selectWell')?.value || '';
        const curDateStr = document.getElementById('inputTanggal')?.value || '';
        const otherInfo = (curWellId && curDateStr) ? getOtherWellsInfoOnDate(curWellId, curDateStr) : { hours: 0, label: '' };
        const otherHrs = otherInfo.hours;
        const maxAllowedForThisWell = Math.max(0, 24.0 - otherHrs);
        const combinedDayTotal = grandTotal + otherHrs;
        const sisaJam = Math.max(0, 24.0 - combinedDayTotal);

        const pct = (val) => Math.min(100, Math.max(0, (val / 24.0) * 100)).toFixed(2) + '%';
        document.getElementById('barOtherWell').style.width = pct(otherHrs);
        document.getElementById('barMiru').style.width = pct(miru);
        document.getElementById('barOps').style.width = pct(ops);
        document.getElementById('barSbwc').style.width = pct(sbwcSum);
        document.getElementById('barUnpaid').style.width = pct(unpaidSum);

        grandEl.textContent = `${grandTotal.toFixed(2)} / ${maxAllowedForThisWell.toFixed(2)} Jam`;
        if (remHint) {
            remHint.textContent = otherHrs > 0
                ? `Sumur Lain: ${otherHrs.toFixed(2)}j · Sisa: ${sisaJam.toFixed(2)}j`
                : `Sisa Hari Ini: ${sisaJam.toFixed(2)} Jam`;
        }

        if (combinedDayTotal > 24.001) {
            const lebih = (combinedDayTotal - 24.0).toFixed(2);
            grandEl.className = 'font-mono font-black text-2xl tracking-tight text-rose-500 animate-pulse';
            badgeEl.className = 'text-[11px] font-mono font-bold text-rose-500 mt-2.5';
            badgeEl.textContent = otherHrs > 0
                ? `⚠️ Lewat 24j Gabungan! (${otherInfo.label} + Sumur Ini ${grandTotal.toFixed(2)}j)`
                : `⚠️ Melebihi batas 24 Jam (+${lebih} Jam)`;

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('is-error');
                btnSubmit.innerHTML = `<i class="fa-solid fa-lock text-xs"></i> <span>MELEBIHI BATAS (${maxAllowedForThisWell.toFixed(2)}j)</span>`;
            }
        } else if (Math.abs(combinedDayTotal - 24.0) <= 0.001) {
            grandEl.className = 'font-mono font-black text-2xl tracking-tight text-emerald-500';
            badgeEl.className = 'text-[11px] font-mono font-bold text-emerald-500 mt-2.5';
            badgeEl.textContent = otherHrs > 0
                ? `✓ Pas 24.00 Jam Gabungan (${otherInfo.label})`
                : '✓ Pas 24.00 Jam (Full Day Complete)';

            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('is-error');
                btnSubmit.innerHTML = `<i class="fa-solid fa-floppy-disk text-xs"></i> <span>SIMPAN DAILY REPORT (${grandTotal.toFixed(2)}j)</span>`;
            }
        } else {
            grandEl.className = 'font-mono font-black text-2xl tracking-tight text-sky-500';
            badgeEl.className = 'text-[11px] font-mono font-bold text-sky-500 mt-2.5';
            badgeEl.textContent = otherHrs > 0
                ? `✓ Transisi Multi-Sumur (${otherInfo.label})`
                : `✓ Terisi ${grandTotal.toFixed(2)} Jam (Sisa ${sisaJam.toFixed(2)} Jam)`;

            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('is-error');
                btnSubmit.innerHTML = `<i class="fa-solid fa-floppy-disk text-xs"></i> <span>SIMPAN DAILY REPORT (${grandTotal.toFixed(2)}j)</span>`;
            }
        }

        generateSmartRemark(false);
    }

    function resetFormToDefault() {
        document.getElementById('formDailyLog').reset();
        document.getElementById('inputMiru').value = '0';
        document.getElementById('inputOps').value = '0';
        document.getElementById('inputJarak').value = '0';
        document.querySelectorAll('.dt-input').forEach(inp => inp.value = '0');

        const tpList = document.getElementById('tpRowsList');
        tpList.innerHTML = '';
        addTpRow(0, 0);

        isRemarkNptAuto = true;
        isRemarkUnpaidAuto = true;

        onWellSelectChange(true);
        calcDailyTotal();
    }

    function clearOnlyHourFields() {
        document.getElementById('inputMiru').value = '0';
        document.getElementById('inputOps').value = '0';
        document.querySelectorAll('.dt-input').forEach(inp => inp.value = '0');
        const tpList = document.getElementById('tpRowsList');
        tpList.innerHTML = '';
        addTpRow(0, 0);
        document.getElementById('remarkNpt').value = '';
        document.getElementById('remarkUnpaid').value = '';
        isRemarkNptAuto = true;
        isRemarkUnpaidAuto = true;
        lastAutoRemarkNpt = '';
        lastAutoRemarkUnpaid = '';

        const badge = document.getElementById('formModeBadge');
        badge.className = 'px-2.5 py-1 rounded-md lh-badge-emerald text-[10px] font-extrabold uppercase tracking-wider';
        badge.textContent = `Input Baru (${document.getElementById('inputTanggal').value})`;

        highlightActiveHistoryRow('');
        calcDailyTotal();
    }

    function getDatesBetween(startDateStr, endDateStr) {
        const dates = [];
        if (!startDateStr) return dates;
        const start = new Date(startDateStr + 'T00:00:00');
        const end = new Date((endDateStr || startDateStr) + 'T00:00:00');
        let curr = new Date(start);
        let guard = 0;
        while (curr <= end && guard < 65) {
            const yyyy = curr.getFullYear();
            const mm = String(curr.getMonth() + 1).padStart(2, '0');
            const dd = String(curr.getDate()).padStart(2, '0');
            dates.push(`${yyyy}-${mm}-${dd}`);
            curr.setDate(curr.getDate() + 1);
            guard++;
        }
        return dates;
    }

    function formatShortIndoDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    }

    function selectWellFromCard(wellId) {
        const sel = document.getElementById('selectWell');
        if (!sel) return;
        sel.value = String(wellId);
        onWellSelectChange(true);
    }

    function jumpToNextUnfilledDate() {
        const sel = document.getElementById('selectWell');
        if (!sel || !sel.value) return;
        const opt = sel.options[sel.selectedIndex];
        const wStart = opt.getAttribute('data-start') || periodMinDate;
        const wEnd   = opt.getAttribute('data-end') || wStart || periodMaxDate;
        const scheduleDates = getDatesBetween(wStart, wEnd);

        for (const dStr of scheduleDates) {
            if (!logsByWellDateMap[`${sel.value}_${dStr}`]) {
                selectWellDatePill(dStr);
                return;
            }
        }
        alert('Semua tanggal pada jadwal sumur ini sudah terisi log harian!');
    }

    function onWellSelectChange(autoPickUnfilledDate = true) {
        const sel = document.getElementById('selectWell');
        const tglInput = document.getElementById('inputTanggal');
        const jarakInput = document.getElementById('inputJarak');
        const noticeEl = document.getElementById('wellScheduleNotice');
        const lockLabel = document.getElementById('dateRangeLockLabel');
        const pillsWrapper = document.getElementById('wellDatePillsWrapper');

        document.querySelectorAll('.well-summary-card').forEach(c => {
            c.classList.remove('ring-2', 'ring-sky-500');
        });
        if (sel && sel.value) {
            const activeCard = document.getElementById('wellCard_' + sel.value);
            if (activeCard) activeCard.classList.add('ring-2', 'ring-sky-500');
        }

        if (!sel || !sel.value) {
            tglInput.min = periodMinDate;
            tglInput.max = periodMaxDate;
            if (noticeEl) noticeEl.textContent = 'Pilih sumur untuk mengunci rentang tanggal';
            if (lockLabel) lockLabel.textContent = '';
            if (pillsWrapper) pillsWrapper.classList.add('hidden');
            return;
        }

        const opt = sel.options[sel.selectedIndex];
        const wStart = opt.getAttribute('data-start') || periodMinDate;
        const wEnd   = opt.getAttribute('data-end') || wStart || periodMaxDate;
        const wJarak = parseFloat(opt.getAttribute('data-jarak')) || 0;
        const wNo    = opt.getAttribute('data-nowell') || '';
        const wLok   = opt.getAttribute('data-lokasi') || '';

        const wStatus = (opt.getAttribute('data-status') || 'JOB PROGRESS').toUpperCase();
        tglInput.min = wStart;
        // Jika sumur masih dalam proses (JOB PROGRESS), tanggal bebas berlanjut sampai akhir periode bulan
        tglInput.max = (wStatus === 'JOB COMPLETED') ? wEnd : periodMaxDate;

        if (noticeEl) {
            if (wStatus === 'JOB COMPLETED') {
                noticeEl.innerHTML = `<i class="fa-solid fa-circle-check text-[10px] mr-1 text-emerald-500"></i> Jadwal Selesai Well #${wNo} (${wLok}): <strong class="lh-text-title">${formatShortIndoDate(wStart)} s/d ${formatShortIndoDate(wEnd)}</strong>`;
            } else {
                noticeEl.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-[10px] mr-1 text-sky-400"></i> Jadwal Berlanjut Well #${wNo} (${wLok}): Mulai <strong class="lh-text-title">${formatShortIndoDate(wStart)}</strong> (Berlanjut terus)`;
            }
        }
        if (lockLabel) {
            lockLabel.textContent = `${wStart.slice(8,10)}/${wStart.slice(5,7)} - ${wEnd ? (wEnd.slice(8,10)+'/'+wEnd.slice(5,7)) : 'Berlanjut'}`;
        }

        const scheduleDates = getDatesBetween(wStart, wEnd);

        if (autoPickUnfilledDate && scheduleDates.length > 0) {
            let targetDate = null;
            for (const dStr of scheduleDates) {
                const wellKey = `${sel.value}_${dStr}`;
                if (!logsByWellDateMap[wellKey]) {
                    targetDate = dStr;
                    break;
                }
            }
            if (!targetDate) {
                targetDate = (tglInput.value < wStart) ? scheduleDates[0] : tglInput.value;
            }

            tglInput.value = targetDate;

            const wellKey = `${sel.value}_${targetDate}`;
            if (logsByWellDateMap[wellKey]) {
                loadLogToForm(logsByWellDateMap[wellKey], false);
            } else {
                clearOnlyHourFields();
                if (targetDate === wStart && wJarak > 0) {
                    jarakInput.value = wJarak;
                }
            }
        } else {
            if (tglInput.value < wStart) tglInput.value = wStart;
        }

        syncWellStatusSelect(wStatus, tglInput.value, wEnd);
        renderWellDatePills(sel.value, scheduleDates, tglInput.value);
        calcDailyTotal();
    }

    function syncWellStatusSelect(wStatus, chosenDate, wEnd) {
        const statusSel = document.getElementById('selectStatusJob');
        if (!statusSel) return;
        if (wStatus === 'JOB SUSPEND') {
            statusSel.value = 'JOB SUSPEND';
        } else if (wStatus === 'JOB COMPLETED') {
            statusSel.value = 'JOB COMPLETED';
        } else {
            // Pekerjaan berlanjut terus (JOB PROGRESS) sampai pengguna sendiri yang memilih selesai
            statusSel.value = 'JOB PROGRESS';
        }
    }

    function onTanggalInputChange() {
        const sel = document.getElementById('selectWell');
        const tglInput = document.getElementById('inputTanggal');
        const noticeEl = document.getElementById('selectedWellDateNotice');
        if (!sel || !sel.value) return;

        const opt = sel.options[sel.selectedIndex];
        const wStart = opt.getAttribute('data-start') || periodMinDate;
        const wEnd   = opt.getAttribute('data-end') || wStart || periodMaxDate;
        const wNo    = opt.getAttribute('data-nowell') || '';
        const wLok   = opt.getAttribute('data-lokasi') || '';
        const wStatus = (opt.getAttribute('data-status') || 'JOB PROGRESS').toUpperCase();

        if (tglInput.value < wStart) {
            alert(`Tanggal operasi tidak boleh mendahului tanggal mulai Sumur #${wNo} (${formatShortIndoDate(wStart)})!`);
            tglInput.value = wStart;
        }

        // Jika tanggal log lebih besar dari wEnd yang tercatat saat sumur masih berjalan,
        // perpanjang wEnd secara otomatis pada atribut option agar tanggal berlanjut mulus!
        if (tglInput.value > wEnd && wStatus !== 'JOB COMPLETED') {
            opt.setAttribute('data-end', tglInput.value);
            if (noticeEl) {
                noticeEl.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-[10px] mr-1 text-sky-400"></i> Jadwal Berlanjut Well #${wNo} (${wLok}): Mulai <strong class="lh-text-title">${formatShortIndoDate(wStart)}</strong> (Berlanjut s/d <strong class="text-sky-400">${formatShortIndoDate(tglInput.value)}</strong>)`;
            }
        }

        const chosenDate = tglInput.value;
        const currentEnd = opt.getAttribute('data-end') || chosenDate;
        const scheduleDates = getDatesBetween(wStart, currentEnd);
        const wellKey = `${sel.value}_${chosenDate}`;

        if (logsByWellDateMap[wellKey]) {
            loadLogToForm(logsByWellDateMap[wellKey], false);
        } else {
            clearOnlyHourFields();
        }

        syncWellStatusSelect(wStatus, chosenDate, currentEnd);
        renderWellDatePills(sel.value, scheduleDates, chosenDate);
        calcDailyTotal();
    }

    function selectWellDatePill(dateStr) {
        const tglInput = document.getElementById('inputTanggal');
        tglInput.value = dateStr;
        onTanggalInputChange();
    }

    function renderWellDatePills(wellId, scheduleDates, activeDateStr) {
        const wrapper   = document.getElementById('wellDatePillsWrapper');
        const container = document.getElementById('wellDatePillsContainer');
        const summaryEl = document.getElementById('wellDateRangeSummary');
        if (!wrapper || !container) return;

        if (!scheduleDates || scheduleDates.length === 0) {
            wrapper.classList.add('hidden');
            return;
        }

        wrapper.classList.remove('hidden');
        container.innerHTML = '';

        let filledCount = 0;
        scheduleDates.forEach(dStr => {
            const wellKey = `${wellId}_${dStr}`;
            const existingLog = logsByWellDateMap[wellKey];
            const otherInfo = getOtherWellsInfoOnDate(wellId, dStr);
            const isFilled = Boolean(existingLog);
            if (isFilled) filledCount++;

            const isActive = (dStr === activeDateStr);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = () => selectWellDatePill(dStr);

            const dayLabel = formatShortIndoDate(dStr);
            if (isFilled) {
                const hrs = parseFloat(existingLog.total_hrs || 0).toFixed(2);
                btn.className = `px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition flex items-center gap-1.5 cursor-pointer ${
                    isActive
                        ? 'bg-emerald-600 text-white ring-2 ring-emerald-400/50 shadow-sm'
                        : 'lh-badge-emerald'
                }`;
                btn.innerHTML = `<i class="fa-solid fa-circle-check text-[10px]"></i> <span>${dayLabel}</span> <span class="text-[10px] opacity-90">(${hrs}j)</span>`;
            } else {
                const sisaJam = Math.max(0, 24.0 - otherInfo.hours).toFixed(0);
                const subText = otherInfo.hours > 0 ? `Sisa ${sisaJam}j` : 'Kosong';
                btn.className = `px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition flex items-center gap-1.5 cursor-pointer ${
                    isActive
                        ? 'bg-sky-600 text-white ring-2 ring-sky-400/50 shadow-sm'
                        : 'lh-pill-empty'
                }`;
                btn.innerHTML = `<i class="fa-regular fa-calendar text-[10px] text-amber-500"></i> <span>${dayLabel}</span> <span class="text-[10px] text-amber-500">(${subText})</span>`;
            }
            container.appendChild(btn);
        });

        if (summaryEl) {
            if (filledCount >= scheduleDates.length && scheduleDates.length > 0) {
                summaryEl.innerHTML = `<span class="px-2 py-0.5 rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-[10px]"><i class="fa-solid fa-check mr-1"></i>Selesai (${filledCount}/${scheduleDates.length}H)</span>`;
            } else {
                summaryEl.innerHTML = `Terisi: <strong class="text-emerald-500">${filledCount}/${scheduleDates.length}</strong> hari`;
            }
        }

        updateCompletionNextBanner(wellId, scheduleDates, filledCount, activeDateStr);
    }

    function addDaysToIsoDate(dateStr, daysToAdd) {
        if (!dateStr) return periodMinDate;
        const d = new Date(dateStr + 'T00:00:00');
        d.setDate(d.getDate() + daysToAdd);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const res = `${yyyy}-${mm}-${dd}`;
        if (res < periodMinDate) return periodMinDate;
        if (res > periodMaxDate) return periodMaxDate;
        return res;
    }

    function getTotalLoggedHoursOnDate(dateStr) {
        let total = 0;
        const details = [];
        existingLogsData.forEach(lg => {
            if (lg.tanggal === dateStr) {
                const h = parseFloat(lg.total_hrs) || 0;
                total += h;
                details.push(`Well #${lg.no_well} (${h.toFixed(2)}j)`);
            }
        });
        return { total, details: details.join(', ') };
    }

    function computeSmartNextWellParams() {
        const sel = document.getElementById('selectWell');
        let maxWellNo = 0;
        let latestEndDate = '';
        let latestWellLabel = '';

        if (sel && sel.options.length > 0) {
            Array.from(sel.options).forEach(opt => {
                const nw = parseInt(opt.getAttribute('data-nowell') || '0', 10);
                const wEnd = opt.getAttribute('data-end') || '';
                const wLok = opt.getAttribute('data-lokasi') || '';
                if (nw > maxWellNo) {
                    maxWellNo = nw;
                }
                if (!latestEndDate || wEnd >= latestEndDate) {
                    latestEndDate = wEnd;
                    latestWellLabel = `Well #${nw} (${wLok})`;
                }
            });
        }

        const nextNo = maxWellNo + 1;
        let suggestedStart = periodMinDate;
        let sameDayOption = null;
        let nextDayOption = null;
        let explanation = '';

        if (latestEndDate) {
            const onEndDay = getTotalLoggedHoursOnDate(latestEndDate);
            const nextDay = addDaysToIsoDate(latestEndDate, 1);
            nextDayOption = nextDay;

            if (onEndDay.total > 0 && onEndDay.total < 23.99) {
                // Ada sisa jam di tanggal selesai sumur sebelumnya (Transisi Pindah Sumur di Hari yang Sama)
                const sisaJam = Math.max(0, 24.0 - onEndDay.total);
                suggestedStart = latestEndDate;
                sameDayOption = {
                    date: latestEndDate,
                    sisaJam: sisaJam,
                    usedLabel: onEndDay.details
                };
                explanation = `Pada akhir jadwal <strong>${latestWellLabel}</strong> (${formatShortIndoDate(latestEndDate)}), baru terpakai <strong>${onEndDay.total.toFixed(2)} Jam</strong>. Tersedia <strong>Sisa ${sisaJam.toFixed(2)} Jam</strong> di tanggal ${formatShortIndoDate(latestEndDate)} untuk transisi pindah sumur (MIRU), atau mulai besoknya (${formatShortIndoDate(nextDay)}).`;
            } else {
                suggestedStart = nextDay;
                explanation = `Melanjutkan otomatis setelah <strong>${latestWellLabel}</strong> (selesai ${formatShortIndoDate(latestEndDate)}) → Tanggal mulai disetel ke <strong>${formatShortIndoDate(suggestedStart)}</strong>.`;
            }
        } else {
            explanation = `Ini adalah pekerjaan sumur pertama pada periode ini. Tanggal mulai disetel ke awal bulan (${formatShortIndoDate(suggestedStart)}).`;
        }

        const suggestedEnd = addDaysToIsoDate(suggestedStart, 3);
        return {
            nextNo,
            suggestedStart,
            suggestedEnd,
            sameDayOption,
            nextDayOption,
            latestWellLabel,
            explanation
        };
    }

    function updateNextWellNumberBadges(nextNo) {
        document.querySelectorAll('.js-next-well-num').forEach(el => {
            el.textContent = String(nextNo);
        });
        const badge = document.getElementById('modalQuickWellBadge');
        if (badge) badge.textContent = '#' + nextNo;
    }

    function updateCompletionNextBanner(wellId, scheduleDates, filledCount, activeDateStr) {
        const banner = document.getElementById('wellCompleteNextBanner');
        const titleEl = document.getElementById('nextWellBannerTitle');
        const descEl  = document.getElementById('nextWellBannerDesc');
        const sel     = document.getElementById('selectWell');
        const sidebarNotice = document.getElementById('sidebarWellCompleteNotice');
        const sidebarTitle  = document.getElementById('sidebarNoticeTitle');
        const sidebarDesc   = document.getElementById('sidebarNoticeDesc');
        const btnSaveNew    = document.getElementById('btnSaveAndNewWell');

        if (!sel || !sel.value) return;

        const opt = sel.options[sel.selectedIndex];
        const wNo  = opt.getAttribute('data-nowell') || '';
        const wLok = opt.getAttribute('data-lokasi') || '';
        const wEnd = opt.getAttribute('data-end') || '';
        const params = computeSmartNextWellParams();
        updateNextWellNumberBadges(params.nextNo);

        const isAllDatesFilled = (scheduleDates.length > 0 && filledCount >= scheduleDates.length);
        const isOnFinalDate    = Boolean(wEnd && activeDateStr >= wEnd);
        const urlParams        = new URLSearchParams(window.location.search);
        const promptFlag       = urlParams.get('well_completed_prompt') === '1';

        const shouldShowTransition = Boolean(isAllDatesFilled || isOnFinalDate || promptFlag);

        if (shouldShowTransition) {
            if (banner) {
                banner.classList.remove('hidden');
                if (titleEl && descEl) {
                    if (isAllDatesFilled) {
                        titleEl.innerHTML = `Jadwal Well #${wNo} (${wLok}) Lengkap (${filledCount}/${scheduleDates.length} Hari) — Siap Lanjut ke Well #${params.nextNo}?`;
                        descEl.innerHTML  = `Seluruh log harian pada jadwal sumur ini telah tercatat. Simpan catatan hari ini dan langsung buat <strong>Well #${params.nextNo}</strong> (Mulai otomatis: <strong>${formatShortIndoDate(params.suggestedStart)}</strong>).`;
                    } else {
                        titleEl.innerHTML = `Hari Terakhir Jadwal Well #${wNo} (${wLok}: ${formatShortIndoDate(wEnd)})`;
                        descEl.innerHTML  = `Setelah menyimpan log hari ini, Anda siap melanjutkan ke <strong>Well #${params.nextNo}</strong> (Mulai otomatis: <strong>${formatShortIndoDate(params.suggestedStart)}</strong>).`;
                    }
                }
            }

            if (sidebarNotice) {
                sidebarNotice.classList.remove('hidden');
                if (sidebarTitle) {
                    sidebarTitle.textContent = isAllDatesFilled ? `Jadwal Well #${wNo} Lengkap` : `Hari Terakhir Well #${wNo}`;
                }
                if (sidebarDesc) {
                    sidebarDesc.textContent = isAllDatesFilled
                        ? `Seluruh hari kerja tercatat. Klik tombol hijau di bawah untuk simpan & lanjut buat Well #${params.nextNo}.`
                        : `Selesai input hari ini? Klik tombol hijau di bawah untuk simpan & lanjut buat Well #${params.nextNo}.`;
                }
            }

            if (btnSaveNew) {
                btnSaveNew.classList.add('is-highlighted');
            }
        } else {
            if (banner) {
                banner.classList.add('hidden');
            }
            if (sidebarNotice) {
                sidebarNotice.classList.add('hidden');
            }
            if (btnSaveNew) {
                btnSaveNew.classList.remove('is-highlighted');
            }
        }
    }

    function openQuickWellModal() {
        const modal = document.getElementById('modalQuickNewWell');
        const errBox = document.getElementById('quickWellErrorBox');
        const noInput = document.getElementById('quickNoWell');
        const lokInput = document.getElementById('quickNamaLokasiInput');
        const jarakInput = document.getElementById('quickJarakInput');
        const startInput = document.getElementById('quickTglMulai');
        const endInput   = document.getElementById('quickTglSelesai');
        const contText   = document.getElementById('quickWellContinuityText');
        const switchRow  = document.getElementById('quickStartDateSwitchRow');

        if (errBox) {
            errBox.classList.add('hidden');
            errBox.textContent = '';
        }

        const params = computeSmartNextWellParams();
        updateNextWellNumberBadges(params.nextNo);

        if (noInput) noInput.value = params.nextNo;
        if (lokInput) lokInput.value = '';
        if (jarakInput) jarakInput.value = '0';
        if (startInput) startInput.value = params.suggestedStart;
        if (endInput) endInput.value = params.suggestedEnd;
        if (contText) contText.innerHTML = params.explanation;

        if (switchRow) {
            if (params.sameDayOption && params.nextDayOption) {
                switchRow.classList.remove('hidden');
                switchRow.classList.add('flex');
                switchRow.innerHTML = `
                    <span class="text-[10px] font-bold uppercase lh-text-muted mr-1">Opsi Mulai:</span>
                    <button type="button" onclick="setQuickStartDate('${params.sameDayOption.date}')"
                        class="lh-sisa-mini-btn !px-2.5 !py-1 !border-sky-500/50 !text-sky-400">
                        ⚡ Mulai ${formatShortIndoDate(params.sameDayOption.date)} (Sisa ${params.sameDayOption.sisaJam.toFixed(1)}j)
                    </button>
                    <button type="button" onclick="setQuickStartDate('${params.nextDayOption}')"
                        class="lh-sisa-mini-btn !px-2.5 !py-1">
                        📅 Mulai Besoknya (${formatShortIndoDate(params.nextDayOption)})
                    </button>
                `;
            } else {
                switchRow.classList.add('hidden');
                switchRow.classList.remove('flex');
                switchRow.innerHTML = '';
            }
        }

        if (modal) {
            modal.classList.add('is-open');
            setTimeout(() => {
                if (lokInput) lokInput.focus();
            }, 80);
        }
    }

    function closeQuickWellModal() {
        const modal = document.getElementById('modalQuickNewWell');
        if (modal) modal.classList.remove('is-open');
    }

    function openModalSelesaikanSumur() {
        const sel = document.getElementById('selectWell');
        if (!sel || !sel.value) {
            alert('Pilih pekerjaan sumur terlebih dahulu.');
            return;
        }
        const opt = sel.options[sel.selectedIndex];
        const wellId = sel.value;
        const wellNo = opt.getAttribute('data-nowell') || '';
        const wellLok = opt.getAttribute('data-lokasi') || '';
        const wStart = opt.getAttribute('data-start') || '';
        const wEnd = opt.getAttribute('data-end') || '';
        const activeDate = document.getElementById('inputTanggal').value;

        const titleEl = document.getElementById('modalSelesaikanTitle');
        const descEl = document.getElementById('selesaikanWellSummaryText');
        const tglEl = document.getElementById('selesaikanTanggalInput');
        const formEl = document.getElementById('formSelesaikanSumur');

        if (titleEl) titleEl.textContent = `Selesaikan Well #${wellNo} (${wellLok})`;
        if (descEl) descEl.innerHTML = `Pekerjaan <strong>Well #${wellNo} (${wellLok})</strong> (Mulai: ${formatShortIndoDate(wStart)}) akan diselesaikan secara resmi. Rekap bulanan rig akan otomatis dihitung ulang.`;
        
        const finishDate = activeDate || wEnd || periodMinDate;
        if (tglEl) {
            tglEl.value = finishDate;
            tglEl.min = wStart || periodMinDate;
        }
        if (formEl) {
            formEl.action = '<?= base_url('daily-report/selesaikan') ?>/' + wellId;
        }

        const modal = document.getElementById('modalSelesaikanSumur');
        if (modal) modal.classList.add('is-open');
    }

    function closeSelesaikanModal() {
        const modal = document.getElementById('modalSelesaikanSumur');
        if (modal) modal.classList.remove('is-open');
    }

    function lanjutHariBerikutnya() {
        const sel = document.getElementById('selectWell');
        if (!sel || !sel.value) return;
        const opt = sel.options[sel.selectedIndex];
        const wEnd = opt.getAttribute('data-end') || '';
        const tglInput = document.getElementById('inputTanggal');
        const currDate = tglInput.value || wEnd || periodMinDate;
        
        if (currDate >= periodMaxDate) {
            alert('Tanggal sudah mencapai akhir bulan (' + formatShortIndoDate(periodMaxDate) + '). Silakan beralih ke bulan berikutnya jika operasi berlanjut ke bulan depan.');
            return;
        }

        const nextDate = addDaysToIsoDate(currDate, 1);
        tglInput.value = nextDate;
        onTanggalInputChange();

        const summaryEl = document.getElementById('wellDateRangeSummary');
        if (summaryEl) {
            const oldHtml = summaryEl.innerHTML;
            summaryEl.innerHTML = `<span class="text-sky-400 font-bold"><i class="fa-solid fa-arrow-right mr-1"></i>Lanjut: ${formatShortIndoDate(nextDate)}</span>`;
            setTimeout(() => { if (summaryEl) summaryEl.innerHTML = oldHtml; }, 3000);
        }
    }

    function setQuickStartDate(dateStr) {
        const startInput = document.getElementById('quickTglMulai');
        const endInput   = document.getElementById('quickTglSelesai');
        if (!startInput || !endInput) return;
        startInput.value = dateStr;
        if (endInput.value < dateStr) {
            endInput.value = addDaysToIsoDate(dateStr, 3);
        }
    }

    function onQuickTglMulaiChange() {
        const startInput = document.getElementById('quickTglMulai');
        const endInput   = document.getElementById('quickTglSelesai');
        if (!startInput || !endInput) return;
        if (endInput.value < startInput.value) {
            endInput.value = startInput.value;
        }
    }

    function setQuickWellDuration(daysCount) {
        const startInput = document.getElementById('quickTglMulai');
        const endInput   = document.getElementById('quickTglSelesai');
        if (!startInput || !endInput) return;
        const baseDate = startInput.value || periodMinDate;
        if (daysCount >= 90) {
            endInput.value = periodMaxDate;
        } else {
            endInput.value = addDaysToIsoDate(baseDate, Math.max(0, daysCount - 1));
        }
    }

    function updateCsrfTokensOnPage(newHash) {
        if (!newHash) return;
        document.querySelectorAll('input[name="csrf_simor_token"]').forEach(inp => {
            inp.value = newHash;
        });
    }

    function showQuickWellToast(title, body) {
        const toast = document.getElementById('quickWellToast');
        const tEl   = document.getElementById('quickWellToastTitle');
        const bEl   = document.getElementById('quickWellToastBody');
        if (!toast) return;
        if (tEl) tEl.textContent = title;
        if (bEl) bEl.textContent = body;
        toast.classList.remove('opacity-0', 'translate-y-3');
        toast.classList.add('opacity-100', 'translate-y-0');
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-3');
        }, 4200);
    }

    async function submitQuickNewWell(e) {
        if (e) e.preventDefault();

        const noWell    = parseInt(document.getElementById('quickNoWell')?.value || '0', 10);
        const namaLok   = (document.getElementById('quickNamaLokasiInput')?.value || '').trim().toUpperCase();
        const jarak     = (document.getElementById('quickJarakInput')?.value || '0').trim();
        const tglMulai  = document.getElementById('quickTglMulai')?.value || periodMinDate;
        const tglSelesai= document.getElementById('quickTglSelesai')?.value || tglMulai;
        const errBox    = document.getElementById('quickWellErrorBox');
        const btnSubmit = document.getElementById('btnSubmitQuickWell');
        const txtSubmit = document.getElementById('textSubmitQuickWell');
        const icnSubmit = document.getElementById('iconSubmitQuickWell');

        if (!noWell || !namaLok) {
            if (errBox) {
                errBox.textContent = 'Mohon isi Nomor Well dan Nama Sumur / Lokasi terlebih dahulu.';
                errBox.classList.remove('hidden');
            }
            return false;
        }

        if (btnSubmit) btnSubmit.disabled = true;
        if (txtSubmit) txtSubmit.textContent = 'Mendaftarkan Sumur Baru...';
        if (icnSubmit) icnSubmit.className = 'fa-solid fa-circle-notch fa-spin text-xs';

        const csrfInput = document.querySelector('input[name="csrf_simor_token"]');
        const formData = new FormData();
        formData.append('rig_id', '<?= $rigId ?>');
        formData.append('bulan', '<?= $bulan ?>');
        formData.append('tahun', '<?= $tahun ?>');
        formData.append('no_well', String(noWell));
        formData.append('nama_lokasi_input', namaLok);
        formData.append('jarak', jarak);
        formData.append('tanggal_mulai', tglMulai);
        formData.append('tanggal_selesai', tglSelesai);
        if (csrfInput) {
            formData.append(csrfInput.name, csrfInput.value);
        }

        try {
            const resp = await fetch('<?= base_url('daily-report/simpan-sumur-cepat') ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const res = await resp.json();

            if (res.csrf_hash) {
                updateCsrfTokensOnPage(res.csrf_hash);
            }

            if (res.status !== 'success') {
                throw new Error(res.message || 'Gagal menyimpan data sumur baru.');
            }

            // 1. Tambahkan option baru ke #selectWell & langsung pilih
            const sel = document.getElementById('selectWell');
            if (sel) {
                const fmtStart = `${res.tanggal_mulai.slice(8,10)}/${res.tanggal_mulai.slice(5,7)}`;
                const fmtEnd   = `${res.tanggal_selesai.slice(8,10)}/${res.tanggal_selesai.slice(5,7)}/${res.tanggal_selesai.slice(0,4)}`;
                const opt = document.createElement('option');
                opt.value = String(res.id);
                opt.setAttribute('data-start', res.tanggal_mulai);
                opt.setAttribute('data-end', res.tanggal_selesai);
                opt.setAttribute('data-jarak', String(res.jarak || 0));
                opt.setAttribute('data-nowell', String(res.no_well));
                opt.setAttribute('data-lokasi', res.nama_lokasi);
                opt.setAttribute('data-status', 'JOB PROGRESS');
                opt.textContent = `Well #${res.no_well} — ${res.nama_lokasi} (${fmtStart} - ${fmtEnd})`;
                sel.appendChild(opt);
                sel.value = String(res.id);
            }

            // 2. Tambahkan kartu sumur baru ke Daftar Sumur di Sidebar Kanan
            const cardsContainer = document.getElementById('wellCardsContainer');
            const emptyText = document.getElementById('emptyWellsSidebarText');
            if (emptyText) emptyText.remove();

            if (cardsContainer) {
                const fmtStart = `${res.tanggal_mulai.slice(8,10)}/${res.tanggal_mulai.slice(5,7)}`;
                const fmtEnd   = `${res.tanggal_selesai.slice(8,10)}/${res.tanggal_selesai.slice(5,7)}/${res.tanggal_selesai.slice(0,4)}`;
                const cardBtn = document.createElement('button');
                cardBtn.type = 'button';
                cardBtn.id = `wellCard_${res.id}`;
                cardBtn.className = 'well-summary-card w-full lh-sub-panel p-2.5 text-left hover:border-sky-500 transition flex items-center justify-between gap-2 cursor-pointer';
                cardBtn.onclick = () => selectWellFromCard(String(res.id));
                cardBtn.innerHTML = `
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded lh-badge-sky font-mono text-[10px] font-black">#${res.no_well}</span>
                            <span class="text-xs font-extrabold lh-text-title truncate">${res.nama_lokasi}</span>
                        </div>
                        <div class="text-[11px] lh-text-muted font-mono mt-0.5">
                            ${fmtStart} s/d ${fmtEnd} · Total: <strong class="lh-text-title">0.00j</strong>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase shrink-0 lh-badge-amber">JOB PROGRESS</span>
                `;
                cardsContainer.appendChild(cardBtn);
            }

            // 3. Update counter sumur & sembunyikan alert kosong
            const countBadge = document.getElementById('wellCountBadge');
            if (countBadge && sel) {
                countBadge.textContent = String(sel.options.length);
            }
            const emptyBanner = document.getElementById('emptyWellsAlertBanner');
            if (emptyBanner) emptyBanner.classList.add('hidden');

            const btnSubmitDaily = document.getElementById('btnSubmitDaily');
            const btnSaveNew     = document.getElementById('btnSaveAndNewWell');
            if (btnSubmitDaily) btnSubmitDaily.disabled = false;
            if (btnSaveNew) btnSaveNew.disabled = false;

            // 4. Pilih sumur baru, kunci jadwal tanggalnya, tutup modal & fokus ke MIRU
            onWellSelectChange(true);
            closeQuickWellModal();

            showQuickWellToast(
                `Well #${res.no_well} (${res.nama_lokasi}) Siap Diisi!`,
                `Jadwal ${formatShortIndoDate(res.tanggal_mulai)} s/d ${formatShortIndoDate(res.tanggal_selesai)} telah dipilih otomatis. Silakan isi jam MIRU & Operasi.`
            );

            setTimeout(() => {
                const miruEl = document.getElementById('inputMiru');
                if (miruEl) {
                    miruEl.focus();
                    miruEl.select();
                }
            }, 120);
        } catch (err) {
            if (errBox) {
                errBox.textContent = err.message || 'Terjadi kesalahan saat menyimpan sumur baru.';
                errBox.classList.remove('hidden');
            }
        } finally {
            if (btnSubmit) btnSubmit.disabled = false;
            if (txtSubmit) {
                const nextNoAfter = computeSmartNextWellParams().nextNo;
                txtSubmit.innerHTML = `Daftarkan &amp; Langsung Isi Log Well #<span class="js-next-well-num">${nextNoAfter}</span>`;
            }
            if (icnSubmit) icnSubmit.className = 'fa-solid fa-check-circle text-xs';
        }

        return false;
    }

    function submitLogAndOpenNewWell() {
        const form = document.getElementById('formDailyLog');
        const actionInput = document.getElementById('inputAfterSaveAction');
        if (!form) return;
        if (actionInput) actionInput.value = 'goto_tambah_sumur';
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    }

    function highlightActiveHistoryRow(rowKey) {
        document.querySelectorAll('.history-row-item').forEach(tr => {
            tr.classList.toggle('is-active-editing', tr.getAttribute('data-rowkey') === rowKey);
        });
    }

    function filterHistoryTable() {
        const q = (document.getElementById('searchHistoryInput')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.history-row-item').forEach(tr => {
            const hay = tr.getAttribute('data-search') || '';
            tr.style.display = (!q || hay.includes(q)) ? '' : 'none';
        });
    }

    function loadLogToForm(data, shouldScroll = true) {
        const badge = document.getElementById('formModeBadge');
        badge.className = 'px-2.5 py-1 rounded-md lh-badge-amber text-[10px] font-extrabold uppercase tracking-wider';
        badge.textContent = `Edit: ${data.tanggal}`;

        document.getElementById('selectWell').value = data.daily_report_id;
        onWellSelectChange(false);
        document.getElementById('inputTanggal').value = data.tanggal;
        document.getElementById('inputJarak').value = parseFloat(data.jarak) || 0;
        document.getElementById('inputMiru').value = parseFloat(data.miru_jam) || 0;
        document.getElementById('inputOps').value = parseFloat(data.ops_jam) || 0;

        const dtFields = [
            'dt_rain', 'dt_dry_road', 'dt_dry_pad', 'dt_phr_op', 'dt_trans',
            'dt_ce_pe', 'dt_daylight', 'dt_phr_well', 'dt_foam',
            'dt_shutdown', 'dt_rig', 'dt_tool'
        ];

        dtFields.forEach(field => {
            const el = document.getElementById(field);
            if (el) {
                el.value = parseFloat(data[field]) || 0;
            }
        });

        const tpList = document.getElementById('tpRowsList');
        tpList.innerHTML = '';
        let addedRows = 0;

        if (data.tp_breakdown && Object.keys(data.tp_breakdown).length > 0) {
            for (const [tpId, jam] of Object.entries(data.tp_breakdown)) {
                const jamVal = parseFloat(jam) || 0;
                if (jamVal > 0) {
                    addTpRow(tpId, jamVal);
                    addedRows++;
                }
            }
        } else if ((parseFloat(data.dt_3rd_party) || 0) > 0) {
            addTpRow(0, parseFloat(data.dt_3rd_party));
            addedRows++;
        }

        if (addedRows === 0) {
            addTpRow(0, 0);
        }

        const loadedNpt = (data.remark_npt || '').trim();
        const loadedUnp = (data.remark_unpaid || '').trim();
        document.getElementById('remarkNpt').value = loadedNpt;
        document.getElementById('remarkUnpaid').value = loadedUnp;

        const { nptText, unpaidText } = buildBesmindoAutoRemark();
        lastAutoRemarkNpt = nptText;
        lastAutoRemarkUnpaid = unpaidText;

        // Hanya aktifkan mode otomatis jika field masih kosong atau persis sama dengan teks auto-remark
        isRemarkNptAuto = (loadedNpt === '' || loadedNpt === nptText);
        isRemarkUnpaidAuto = (loadedUnp === '' || loadedUnp === unpaidText);

        const sel = document.getElementById('selectWell');
        if (sel && sel.value) {
            const opt = sel.options[sel.selectedIndex];
            const wStart = opt.getAttribute('data-start') || periodMinDate;
            const wEnd   = opt.getAttribute('data-end') || wStart || periodMaxDate;
            renderWellDatePills(sel.value, getDatesBetween(wStart, wEnd), data.tanggal);
        }

        highlightActiveHistoryRow(`${data.daily_report_id}_${data.tanggal}`);
        calcDailyTotal();

        if (shouldScroll) {
            const formSec = document.getElementById('formSection');
            formSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function confirmHapusLog(tanggal, wellLabel) {
        return confirm(`Hapus catatan log harian tanggal ${tanggal} (${wellLabel})?\n\nSubtotal sumur dan NPT akan dihitung ulang.`);
    }

    document.addEventListener('keydown', (ev) => {
        if (ev.altKey && (ev.key === 'n' || ev.key === 'N')) {
            ev.preventDefault();
            window.location.href = '<?= $tambahSumurUrl ?>';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const qWellId = urlParams.get('well_id');
        const qTanggal = urlParams.get('tanggal');
        const sel = document.getElementById('selectWell');

        if (sel && qWellId) {
            const matchingOpt = Array.from(sel.options).find(opt => opt.value === String(qWellId));
            if (matchingOpt) {
                sel.value = String(qWellId);
            }
        }

        onWellSelectChange(true);

        if (sel && qTanggal && sel.selectedIndex >= 0) {
            const opt = sel.options[sel.selectedIndex];
            const wStart = opt.getAttribute('data-start') || periodMinDate;
            const wEnd   = opt.getAttribute('data-end') || wStart || periodMaxDate;
            if (qTanggal >= wStart && qTanggal <= wEnd) {
                selectWellDatePill(qTanggal);
            }
        }

        calcDailyTotal();
    });
</script>
<?= $this->endSection() ?>
