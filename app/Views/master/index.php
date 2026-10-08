<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
/* ═══ Pre-compute Summary Statistics across all 4 Master Data Pillars ═══ */
$totalRigs      = count($rigs);
$activeRigs     = 0;
$totalDailyOdr  = 0;
foreach ($rigs as $r) {
    if (!empty($r['aktif'])) {
        $activeRigs++;
        $totalDailyOdr += (float)($r['odr'] ?? 0);
    }
}
$totalHourlyOdr = $totalDailyOdr / 24;

$totalKategori  = count($kategori);
$countSbwc      = 0;
$countUnpaid    = 0;
foreach ($kategori as $k) {
    if (($k['tipe'] ?? '') === 'UNPAID') {
        $countUnpaid++;
    } else {
        $countSbwc++;
    }
}

$totalTp        = count($thirdParties);
$activeTp       = 0;
foreach ($thirdParties as $tp) {
    if (!empty($tp['aktif'])) $activeTp++;
}

$totalLokasi    = count($lokasi);
$activeLokasi   = 0;
foreach ($lokasi as $l) {
    if (!empty($l['aktif'])) $activeLokasi++;
}
?>

<style>
    /* ═══ Panel & Card Surface Styling (Selaras dengan Daily, Monthly & Rekap Tahunan) ═══ */
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
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
    }
    .dr-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-elevated);
        border-color: var(--primary);
    }
    .dr-stat-card.active-pillar {
        border-color: #d97706;
        box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.25), var(--shadow-card);
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
        height: 42px;
        padding: 0 0.875rem;
        border-radius: 0.65rem;
        background: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
        font-size: 0.88rem;
        font-weight: 700;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .dr-filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--ring);
    }
    .dr-action-primary {
        height: 42px;
        padding: 0 1.15rem;
        border-radius: 0.65rem;
        font-size: 0.82rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: 1.5px solid #1e40af;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .dr-action-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
        transform: translateY(-1px);
    }
    /* Segmented Pill Bar */
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
    /* Excel Sheet Replica Table */
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
        font-size: 12px;
        color: #000000;
        background: #ffffff;
    }
    .excel-sheet th,
    .excel-sheet td {
        border: 1px solid #334155;
        padding: 7px 10px;
        line-height: 1.3;
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

    <!-- ═══ 1. PANEL HEADER & TOMBOL TAMBAH DATA CEPAT ═══ -->
    <div class="dr-filter-panel p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0 shadow-sm">
                    <i class="fa-solid fa-database text-xl"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[11px] font-extrabold tracking-wider uppercase border border-amber-500/25">
                            MASTER DATA CONTROL CENTER &bull; SIMOR BMS
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold border border-emerald-500/25">
                            <i class="fa-solid fa-circle-check mr-1"></i>4 Pilar Referensi Operasional
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1" style="color: var(--foreground);">
                        PUSAT MASTER DATA TERPADU &amp; PARAMETER KONTRAK
                    </h2>
                    <p class="text-xs sm:text-sm mt-0.5" style="color: var(--muted-foreground);">
                        Kelola daftar Armada Rig &amp; Tarif ODR, Kategori Downtime (SBWC &amp; Unpaid), Mitra Vendor 3rd Party, serta Titik Lokasi Sumur.
                    </p>
                </div>
            </div>

            <!-- Tombol Tambah Cepat Sesuai Tab Aktif -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="openMasterModalForCurrentTab()" id="primaryAddMasterBtn" class="dr-action-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span id="primaryAddMasterLabel">Tambah Armada Rig Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ═══ 2. EMPAT KARTU PILAR MASTER DATA (KLIK UNTUK PINDAH TAB) ═══ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
        <!-- Card 1: Armada Rig -->
        <div onclick="switchMasterTab('rig')" id="pillarCard_rig"
             class="dr-stat-card p-4 sm:p-5 relative overflow-hidden <?= $activeTab === 'rig' ? 'active-pillar' : '' ?>">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        1. Master Armada Rig
                    </span>
                    <div class="flex items-baseline gap-2 mt-1 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-sky-500"><?= $activeRigs ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">/ <?= $totalRigs ?> Unit Aktif</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-500 shrink-0">
                    <i class="fa-solid fa-tower-broadcast"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Total ODR/Hari:</span>
                <strong class="text-emerald-500">Rp <?= number_format($totalDailyOdr, 0, ',', '.') ?></strong>
            </div>
        </div>

        <!-- Card 2: Kategori Downtime -->
        <div onclick="switchMasterTab('kategori')" id="pillarCard_kategori"
             class="dr-stat-card p-4 sm:p-5 relative overflow-hidden <?= $activeTab === 'kategori' ? 'active-pillar' : '' ?>">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        2. Kategori Downtime
                    </span>
                    <div class="flex items-baseline gap-2 mt-1 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-amber-500"><?= $totalKategori ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">Pos Klasifikasi</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 shrink-0">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>SBWC: <strong class="text-amber-500"><?= $countSbwc ?> Pos</strong></span>
                <span>UNPAID: <strong class="text-rose-500"><?= $countUnpaid ?> Pos</strong></span>
            </div>
        </div>

        <!-- Card 3: Vendor 3rd Party -->
        <div onclick="switchMasterTab('third_party')" id="pillarCard_third_party"
             class="dr-stat-card p-4 sm:p-5 relative overflow-hidden <?= $activeTab === 'third_party' ? 'active-pillar' : '' ?>">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        3. Vendor 3rd Party
                    </span>
                    <div class="flex items-baseline gap-2 mt-1 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-indigo-500"><?= $activeTp ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">/ <?= $totalTp ?> Mitra Aktif</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-500 shrink-0">
                    <i class="fa-solid fa-handshake"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Mitra Pendukung:</span>
                <strong class="text-indigo-500">BHI, HLS, WI, dll</strong>
            </div>
        </div>

        <!-- Card 4: Lokasi Sumur -->
        <div onclick="switchMasterTab('lokasi')" id="pillarCard_lokasi"
             class="dr-stat-card p-4 sm:p-5 relative overflow-hidden <?= $activeTab === 'lokasi' ? 'active-pillar' : '' ?>">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider block" style="color: var(--muted-foreground);">
                        4. Lokasi / Titik Sumur
                    </span>
                    <div class="flex items-baseline gap-2 mt-1 font-num">
                        <span class="text-2xl sm:text-3xl font-black text-emerald-500"><?= $activeLokasi ?></span>
                        <span class="text-xs font-bold" style="color: var(--muted-foreground);">/ <?= $totalLokasi ?> Sumur</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-xs font-num" style="border-color: var(--border); color: var(--muted-foreground);">
                <span>Status Database:</span>
                <strong class="text-emerald-500">Siap Pakai Input Log</strong>
            </div>
        </div>
    </div>

    <!-- ═══ 3. BARIS TAB PILAR & FILTER PENCARIAN INSTAN ═══ -->
    <div class="dr-filter-panel p-3.5 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="mr-segmented-group">
            <button type="button" onclick="switchMasterTab('rig')" id="masterTabBtn_rig"
                class="mr-tab-pill <?= $activeTab === 'rig' ? 'active' : '' ?>">
                <i class="fa-solid fa-tower-broadcast text-sky-500"></i>
                <span>1. Armada Rig (<?= $totalRigs ?>)</span>
            </button>
            <button type="button" onclick="switchMasterTab('kategori')" id="masterTabBtn_kategori"
                class="mr-tab-pill <?= $activeTab === 'kategori' ? 'active' : '' ?>">
                <i class="fa-solid fa-tags text-amber-500"></i>
                <span>2. Kategori Downtime (<?= $totalKategori ?>)</span>
            </button>
            <button type="button" onclick="switchMasterTab('third_party')" id="masterTabBtn_third_party"
                class="mr-tab-pill <?= $activeTab === 'third_party' ? 'active' : '' ?>">
                <i class="fa-solid fa-handshake text-indigo-500"></i>
                <span>3. Vendor 3rd Party (<?= $totalTp ?>)</span>
            </button>
            <button type="button" onclick="switchMasterTab('lokasi')" id="masterTabBtn_lokasi"
                class="mr-tab-pill <?= $activeTab === 'lokasi' ? 'active' : '' ?>">
                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                <span>4. Lokasi / Sumur (<?= $totalLokasi ?>)</span>
            </button>
        </div>

        <!-- Filter Status & Pencarian Cepat -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
            <select id="masterStatusFilter" onchange="filterMasterRows()" class="dr-filter-select sm:!w-40 text-xs">
                <option value="all">Semua Status</option>
                <option value="1">Status: Aktif</option>
                <option value="0">Status: Nonaktif</option>
            </select>
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs pointer-events-none" style="color: var(--muted-foreground);"></i>
                <input type="text" id="masterSearchInput" oninput="filterMasterRows()"
                    placeholder="Cari kode rig, kategori, vendor, sumur..."
                    class="dr-filter-select w-full !pl-9 !pr-8 text-xs">
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: MASTER ARMADA RIG & TARIF ODR                                     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="masterPanel_rig" class="<?= $activeTab === 'rig' ? '' : 'hidden' ?> space-y-4">
        <div class="excel-paper-container">
            <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#ffff00] inline-block"></span>
                    <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                        1. DAFTAR MASTER ARMADA RIG &amp; OPERATOR DAILY RATE (ODR) &mdash; <?= $totalRigs ?> UNIT
                    </h3>
                </div>
                <button type="button" onclick="openRigModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Tambah Rig Baru
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th class="excel-header-yellow w-12">NO</th>
                            <th class="excel-header-yellow w-28">KODE RIG</th>
                            <th class="excel-header-yellow text-left">NAMA ARMADA RIG OPERASIONAL</th>
                            <th class="excel-header-gold text-right">TARIF KONTRAK ODR / HARI (24 JAM)</th>
                            <th class="excel-header-yellow text-right">TARIF PER JAM (ODR &divide; 24)</th>
                            <th class="excel-header-yellow text-right">ESTIMASI TARGET 30 HARI</th>
                            <th class="excel-header-yellow w-28">STATUS</th>
                            <th class="excel-header-blue w-40">AKSI KELOLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($rigs as $r):
                            $odrDay   = (float)($r['odr'] ?? 0);
                            $odrHour  = $odrDay / 24;
                            $odrMonth = $odrDay * 30;
                            $isAktif  = !empty($r['aktif']) ? '1' : '0';
                        ?>
                        <tr class="excel-row-data master-filter-row"
                            data-search="<?= esc(strtolower($r['kode'] . ' ' . $r['nama_rig'])) ?>"
                            data-status="<?= $isAktif ?>">
                            <td class="text-center font-num font-bold text-slate-700"><?= $no++ ?></td>
                            <td class="text-center font-num font-black text-[#0f172a] bg-slate-100 text-xs">
                                <?= esc($r['kode']) ?>
                            </td>
                            <td class="font-bold text-[#0f172a]"><?= esc($r['nama_rig']) ?></td>
                            <td class="text-right font-num font-extrabold text-[#006100] bg-emerald-50/70">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($odrDay, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right font-num text-slate-700">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($odrHour, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right font-num text-slate-700">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($odrMonth, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-center">
                                <?php if ($isAktif === '1'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#dcfce7] text-[#166534] font-extrabold text-[11px] border border-[#86efac]">
                                        <i class="fa-solid fa-circle text-[7px]"></i> AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[11px]">
                                        NONAKTIF
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick='openRigModal(<?= json_encode([
                                            "id"       => $r["id"],
                                            "kode"     => $r["kode"],
                                            "nama_rig" => $r["nama_rig"],
                                            "odr"      => (int)$r["odr"],
                                            "aktif"    => (int)$r["aktif"]
                                        ]) ?>)'
                                        class="px-2.5 py-1 rounded bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition cursor-pointer"
                                        title="Edit Cepat <?= esc($r['kode']) ?>">
                                        <i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
                                    </button>
                                    <form action="<?= base_url('master/rig/hapus/' . $r['id']) ?>" method="POST"
                                          onsubmit="return confirm('Hapus armada rig <?= esc($r['kode']) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                            class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition cursor-pointer"
                                            title="Hapus <?= esc($r['kode']) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="excel-footer-gold font-num">
                            <td colspan="3" class="text-center font-black">TOTAL KAPASITAS ODR ARMADA AKTIF (<?= $activeRigs ?> RIG)</td>
                            <td class="text-right">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($totalDailyOdr, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($totalHourlyOdr, 0, ',', '.') ?></span></div>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-between gap-2"><span>Rp</span><span><?= number_format($totalDailyOdr * 30, 0, ',', '.') ?></span></div>
                            </td>
                            <td colspan="2" class="text-center text-[11px]"><?= $activeRigs ?> AKTIF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: MASTER KATEGORI DOWNTIME (SBWC & UNPAID)                          -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="masterPanel_kategori" class="<?= $activeTab === 'kategori' ? '' : 'hidden' ?> space-y-4">
        <div class="excel-paper-container">
            <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#ffc000] inline-block"></span>
                    <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                        2. DAFTAR KATEGORI DOWNTIME / NPT (SBWC &amp; UNPAID) &mdash; <?= $totalKategori ?> POS
                    </h3>
                </div>
                <button type="button" onclick="openKategoriModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori Downtime
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th class="excel-header-yellow w-20">URUTAN</th>
                            <th class="excel-header-yellow text-left">NAMA POS KATEGORI DOWNTIME (NPT)</th>
                            <th class="excel-header-gold w-36">TIPE KLASIFIKASI</th>
                            <th class="excel-header-yellow text-left">DAMPAK PERHITUNGAN KONTRAK &amp; RAU</th>
                            <th class="excel-header-yellow w-28">STATUS</th>
                            <th class="excel-header-blue w-40">AKSI KELOLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kategori as $k):
                            $isAktif = !empty($k['aktif']) ? '1' : '0';
                            $isUnpaid = ($k['tipe'] === 'UNPAID');
                        ?>
                        <tr class="excel-row-data master-filter-row"
                            data-search="<?= esc(strtolower($k['nama'] . ' ' . $k['tipe'])) ?>"
                            data-status="<?= $isAktif ?>">
                            <td class="text-center font-num font-black text-slate-800 bg-slate-100"><?= (int)$k['urutan'] ?></td>
                            <td class="font-bold text-[#0f172a]"><?= esc($k['nama']) ?></td>
                            <td class="text-center">
                                <?php if ($isUnpaid): ?>
                                    <span class="inline-block px-2.5 py-0.5 rounded bg-[#fee2e2] text-[#991b1b] font-black text-[11px] border border-[#fca5a5]">
                                        UNPAID (0% ODR)
                                    </span>
                                <?php else: ?>
                                    <span class="inline-block px-2.5 py-0.5 rounded bg-[#fef3c7] text-[#92400e] font-black text-[11px] border border-[#fcd34d]">
                                        SBWC (STANDBY)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-xs text-slate-700">
                                <?php if ($isUnpaid): ?>
                                    <span class="font-semibold text-[#991b1b]">Memotong Jam Reliability, Availability &amp; Revenue Aktual (Unpaid Downtime)</span>
                                <?php else: ?>
                                    <span class="font-semibold text-slate-700">Standby With Charge / Cuaca / Kondisi Lapangan (Masuk kelompok SBWC)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($isAktif === '1'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#dcfce7] text-[#166534] font-extrabold text-[11px] border border-[#86efac]">
                                        <i class="fa-solid fa-circle text-[7px]"></i> AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[11px]">
                                        NONAKTIF
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick='openKategoriModal(<?= json_encode([
                                            "id"     => $k["id"],
                                            "nama"   => $k["nama"],
                                            "tipe"   => $k["tipe"],
                                            "urutan" => (int)$k["urutan"],
                                            "aktif"  => (int)$k["aktif"]
                                        ]) ?>)'
                                        class="px-2.5 py-1 rounded bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
                                    </button>
                                    <form action="<?= base_url('master/kategori/hapus/' . $k['id']) ?>" method="POST"
                                          onsubmit="return confirm('Hapus kategori downtime <?= esc($k['nama']) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                            class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition cursor-pointer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: MASTER VENDOR 3RD PARTY                                           -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="masterPanel_third_party" class="<?= $activeTab === 'third_party' ? '' : 'hidden' ?> space-y-4">
        <div class="excel-paper-container">
            <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-400 inline-block"></span>
                    <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                        3. DAFTAR MITRA VENDOR PIHAK KETIGA (3RD PARTY) &mdash; <?= $totalTp ?> VENDOR
                    </h3>
                </div>
                <button type="button" onclick="openTpModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Tambah Vendor 3rd Party
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th class="excel-header-yellow w-14">NO</th>
                            <th class="excel-header-yellow text-left">KODE / NAMA VENDOR 3RD PARTY</th>
                            <th class="excel-header-yellow text-left">FUNGSI PADA LOG HARIAN &amp; NPT</th>
                            <th class="excel-header-yellow w-32">STATUS</th>
                            <th class="excel-header-blue w-40">AKSI KELOLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($thirdParties as $tp):
                            $isAktif = !empty($tp['aktif']) ? '1' : '0';
                        ?>
                        <tr class="excel-row-data master-filter-row"
                            data-search="<?= esc(strtolower($tp['nama'])) ?>"
                            data-status="<?= $isAktif ?>">
                            <td class="text-center font-num font-bold text-slate-700"><?= $no++ ?></td>
                            <td class="font-black text-[#0f172a] text-sm"><?= esc($tp['nama']) ?></td>
                            <td class="text-xs text-slate-700">
                                Digunakan saat mencatat rincian Waiting on 3rd Party (Logging, Cementing, Perforasi, Wellhead, dll)
                            </td>
                            <td class="text-center">
                                <?php if ($isAktif === '1'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#dcfce7] text-[#166534] font-extrabold text-[11px] border border-[#86efac]">
                                        <i class="fa-solid fa-circle text-[7px]"></i> AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[11px]">
                                        NONAKTIF
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick='openTpModal(<?= json_encode([
                                            "id"    => $tp["id"],
                                            "nama"  => $tp["nama"],
                                            "aktif" => (int)$tp["aktif"]
                                        ]) ?>)'
                                        class="px-2.5 py-1 rounded bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
                                    </button>
                                    <form action="<?= base_url('master/third-party/hapus/' . $tp['id']) ?>" method="POST"
                                          onsubmit="return confirm('Hapus vendor <?= esc($tp['nama']) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                            class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition cursor-pointer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: MASTER LOKASI / SUMUR MINYAK                                      -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="masterPanel_lokasi" class="<?= $activeTab === 'lokasi' ? '' : 'hidden' ?> space-y-4">
        <div class="excel-paper-container">
            <div class="px-4 py-3 bg-[#0f172a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-[#334155]">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                    <h3 class="text-xs sm:text-sm font-black tracking-wider uppercase">
                        4. DAFTAR MASTER LOKASI / IDENTITAS SUMUR &mdash; <?= $totalLokasi ?> TITIK SUMUR
                    </h3>
                </div>
                <button type="button" onclick="openLokasiModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Tambah Lokasi Sumur Baru
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="excel-sheet">
                    <thead>
                        <tr>
                            <th class="excel-header-yellow w-14">NO</th>
                            <th class="excel-header-yellow text-left">NAMA / TAG IDENTITAS LOKASI SUMUR</th>
                            <th class="excel-header-yellow text-left">PENGGUNAAN PADA DAILY REPORT</th>
                            <th class="excel-header-yellow w-32">STATUS</th>
                            <th class="excel-header-blue w-40">AKSI KELOLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($lokasi as $l):
                            $isAktif = !empty($l['aktif']) ? '1' : '0';
                        ?>
                        <tr class="excel-row-data master-filter-row"
                            data-search="<?= esc(strtolower($l['nama_lokasi'])) ?>"
                            data-status="<?= $isAktif ?>">
                            <td class="text-center font-num font-bold text-slate-700"><?= $no++ ?></td>
                            <td class="font-num font-black text-[#0f172a] text-xs sm:text-sm"><?= esc($l['nama_lokasi']) ?></td>
                            <td class="text-xs text-slate-700">
                                Tersedia pada pilihan Lokasi Sumur di Daily Report per Well &amp; Log Harian
                            </td>
                            <td class="text-center">
                                <?php if ($isAktif === '1'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#dcfce7] text-[#166534] font-extrabold text-[11px] border border-[#86efac]">
                                        <i class="fa-solid fa-circle text-[7px]"></i> AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[11px]">
                                        NONAKTIF
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick='openLokasiModal(<?= json_encode([
                                            "id"          => $l["id"],
                                            "nama_lokasi" => $l["nama_lokasi"],
                                            "aktif"       => (int)$l["aktif"]
                                        ]) ?>)'
                                        class="px-2.5 py-1 rounded bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
                                    </button>
                                    <form action="<?= base_url('master/lokasi/hapus/' . $l['id']) ?>" method="POST"
                                          onsubmit="return confirm('Hapus lokasi sumur <?= esc($l['nama_lokasi']) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                            class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition cursor-pointer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- MODAL QUICK-ADD / QUICK-EDIT MASTER DATA (SEMUA 4 PILAR)                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<div id="masterModalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
    <div class="dr-filter-panel w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: var(--border);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500">
                    <i id="masterModalIcon" class="fa-solid fa-database"></i>
                </div>
                <div>
                    <h4 id="masterModalTitle" class="text-sm font-black uppercase tracking-wider" style="color: var(--foreground);">
                        Tambah Data Master
                    </h4>
                    <p id="masterModalSubtitle" class="text-xs" style="color: var(--muted-foreground);">
                        Lengkapi parameter di bawah lalu klik Simpan
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeMasterModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-rose-500/15 hover:text-rose-500 transition" style="color: var(--muted-foreground);">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="masterModalForm" method="POST" action="" class="p-5 space-y-4">
            <?= csrf_field() ?>

            <!-- FIELD GROUP: RIG -->
            <div id="modalFields_rig" class="space-y-3.5 hidden">
                <div>
                    <label class="dr-filter-label">Kode Armada Rig</label>
                    <input type="text" name="kode" id="inpRigKode" class="dr-filter-select" placeholder="Contoh: BMS#01">
                </div>
                <div>
                    <label class="dr-filter-label">Nama Lengkap Rig</label>
                    <input type="text" name="nama_rig" id="inpRigNama" class="dr-filter-select" placeholder="Contoh: RIG BMS 01">
                </div>
                <div>
                    <label class="dr-filter-label">Operator Daily Rate (ODR / Hari dalam Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="text" name="odr" id="inpRigOdr" class="dr-filter-select font-num pl-10" placeholder="Contoh: 86.197.000">
                    </div>
                </div>
            </div>

            <!-- FIELD GROUP: KATEGORI DOWNTIME -->
            <div id="modalFields_kategori" class="space-y-3.5 hidden">
                <div>
                    <label class="dr-filter-label">Nama Pos Kategori Downtime</label>
                    <input type="text" name="nama" id="inpKatNama" class="dr-filter-select" placeholder="Contoh: Waiting on Daylight / Weather">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="dr-filter-label">Tipe Klasifikasi</label>
                        <select name="tipe" id="inpKatTipe" class="dr-filter-select">
                            <option value="SBWC">SBWC (Standby)</option>
                            <option value="UNPAID">UNPAID (0% ODR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="dr-filter-label">Nomor Urutan Kolom</label>
                        <input type="number" name="urutan" id="inpKatUrutan" class="dr-filter-select font-num" min="1" value="<?= $totalKategori + 1 ?>">
                    </div>
                </div>
            </div>

            <!-- FIELD GROUP: VENDOR 3RD PARTY -->
            <div id="modalFields_tp" class="space-y-3.5 hidden">
                <div>
                    <label class="dr-filter-label">Kode / Nama Vendor Pihak Ketiga (3rd Party)</label>
                    <input type="text" name="nama" id="inpTpNama" class="dr-filter-select" placeholder="Contoh: BHI / HLS / WI / HALCO">
                </div>
            </div>

            <!-- FIELD GROUP: LOKASI SUMUR -->
            <div id="modalFields_lokasi" class="space-y-3.5 hidden">
                <div>
                    <label class="dr-filter-label">Nama / Tag Identitas Lokasi Sumur</label>
                    <input type="text" name="nama_lokasi" id="inpLokasiNama" class="dr-filter-select" placeholder="Contoh: 5H-0310A / MINAS 4D-22">
                </div>
            </div>

            <!-- STATUS AKTIF (SHARED) -->
            <div class="pt-1 flex items-center gap-2.5">
                <input type="checkbox" name="aktif" id="inpSharedAktif" value="1" checked class="w-4 h-4 rounded accent-blue-600 cursor-pointer">
                <label for="inpSharedAktif" class="text-xs font-extrabold cursor-pointer" style="color: var(--foreground);">
                    Status Aktif (Tampilkan pada pilihan laporan operasional)
                </label>
            </div>

            <div class="pt-3 border-t flex items-center justify-end gap-2.5" style="border-color: var(--border);">
                <button type="button" onclick="closeMasterModal()" class="px-4 py-2 rounded-lg text-xs font-extrabold border transition cursor-pointer" style="border-color: var(--border-strong); color: var(--foreground);">
                    Batal
                </button>
                <button type="submit" class="dr-action-primary !h-9 !px-4 !text-xs">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentMasterTab = <?= json_encode($activeTab) ?>;

    const tabAddLabels = {
        rig:         'Tambah Armada Rig Baru',
        kategori:    'Tambah Kategori Downtime',
        third_party: 'Tambah Vendor 3rd Party',
        lokasi:      'Tambah Lokasi Sumur Baru'
    };

    function switchMasterTab(tabKey) {
        currentMasterTab = tabKey;
        ['rig', 'kategori', 'third_party', 'lokasi'].forEach(k => {
            const panel = document.getElementById('masterPanel_' + k);
            const btn   = document.getElementById('masterTabBtn_' + k);
            const card  = document.getElementById('pillarCard_' + k);
            if (panel) panel.classList.toggle('hidden', k !== tabKey);
            if (btn) btn.classList.toggle('active', k === tabKey);
            if (card) card.classList.toggle('active-pillar', k === tabKey);
        });

        const addLbl = document.getElementById('primaryAddMasterLabel');
        if (addLbl && tabAddLabels[tabKey]) {
            addLbl.textContent = tabAddLabels[tabKey];
        }

        // Update URL query param without reloading
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabKey);
            window.history.replaceState({}, '', url.toString());
        }

        filterMasterRows();
    }

    function filterMasterRows() {
        const q = (document.getElementById('masterSearchInput')?.value || '').toLowerCase().trim();
        const st = document.getElementById('masterStatusFilter')?.value || 'all';
        const activePanel = document.getElementById('masterPanel_' + currentMasterTab);
        if (!activePanel) return;

        activePanel.querySelectorAll('.master-filter-row').forEach(tr => {
            const hay = (tr.getAttribute('data-search') || '').toLowerCase();
            const rowSt = tr.getAttribute('data-status') || '1';
            const matchText = !q || hay.includes(q);
            const matchStatus = (st === 'all') || (rowSt === st);
            tr.style.display = (matchText && matchStatus) ? '' : 'none';
        });
    }

    function openMasterModalForCurrentTab() {
        if (currentMasterTab === 'rig') openRigModal();
        else if (currentMasterTab === 'kategori') openKategoriModal();
        else if (currentMasterTab === 'third_party') openTpModal();
        else if (currentMasterTab === 'lokasi') openLokasiModal();
    }

    function resetModalGroups(activeGroup) {
        ['rig', 'kategori', 'tp', 'lokasi'].forEach(g => {
            const el = document.getElementById('modalFields_' + g);
            if (!el) return;
            const isTarget = (g === activeGroup);
            el.classList.toggle('hidden', !isTarget);
            el.querySelectorAll('input, select').forEach(inp => {
                inp.disabled = !isTarget;
            });
        });
        const backdrop = document.getElementById('masterModalBackdrop');
        backdrop.classList.remove('hidden');
        backdrop.classList.add('flex');
    }

    function closeMasterModal() {
        const backdrop = document.getElementById('masterModalBackdrop');
        backdrop.classList.add('hidden');
        backdrop.classList.remove('flex');
    }

    function openRigModal(data = null) {
        resetModalGroups('rig');
        const form = document.getElementById('masterModalForm');
        document.getElementById('masterModalTitle').textContent = data ? 'Edit Armada Rig: ' + data.kode : 'Registrasi Armada Rig Baru';
        document.getElementById('masterModalSubtitle').textContent = 'Atur kode rig, nama unit, dan nilai kontrak Operator Daily Rate (ODR)';
        form.action = data
            ? `<?= base_url('master/rig/update') ?>/${data.id}`
            : `<?= base_url('master/rig/simpan') ?>`;

        document.getElementById('inpRigKode').value = data ? data.kode : '';
        document.getElementById('inpRigNama').value = data ? data.nama_rig : '';
        document.getElementById('inpRigOdr').value  = data ? new Intl.NumberFormat('id-ID').format(data.odr) : '26.500.000';
        document.getElementById('inpSharedAktif').checked = data ? (data.aktif == 1) : true;
    }

    function openKategoriModal(data = null) {
        resetModalGroups('kategori');
        const form = document.getElementById('masterModalForm');
        document.getElementById('masterModalTitle').textContent = data ? 'Edit Kategori Downtime' : 'Tambah Kategori Downtime Baru';
        document.getElementById('masterModalSubtitle').textContent = 'Tentukan nama pos downtime, klasifikasi SBWC / UNPAID, dan urutan kolom';
        form.action = data
            ? `<?= base_url('master/kategori/update') ?>/${data.id}`
            : `<?= base_url('master/kategori/simpan') ?>`;

        document.getElementById('inpKatNama').value   = data ? data.nama : '';
        document.getElementById('inpKatTipe').value   = data ? data.tipe : 'SBWC';
        document.getElementById('inpKatUrutan').value = data ? data.urutan : <?= $totalKategori + 1 ?>;
        document.getElementById('inpSharedAktif').checked = data ? (data.aktif == 1) : true;
    }

    function openTpModal(data = null) {
        resetModalGroups('tp');
        const form = document.getElementById('masterModalForm');
        document.getElementById('masterModalTitle').textContent = data ? 'Edit Mitra Vendor 3rd Party' : 'Tambah Mitra Vendor 3rd Party';
        document.getElementById('masterModalSubtitle').textContent = 'Masukkan kode / nama mitra vendor pendukung operasi sumur';
        form.action = data
            ? `<?= base_url('master/third-party/update') ?>/${data.id}`
            : `<?= base_url('master/third-party/simpan') ?>`;

        document.getElementById('inpTpNama').value = data ? data.nama : '';
        document.getElementById('inpSharedAktif').checked = data ? (data.aktif == 1) : true;
    }

    function openLokasiModal(data = null) {
        resetModalGroups('lokasi');
        const form = document.getElementById('masterModalForm');
        document.getElementById('masterModalTitle').textContent = data ? 'Edit Lokasi / Identitas Sumur' : 'Tambah Titik Lokasi Sumur Baru';
        document.getElementById('masterModalSubtitle').textContent = 'Masukkan kode atau nama lokasi sumur untuk pencatatan Daily Report';
        form.action = data
            ? `<?= base_url('master/lokasi/update') ?>/${data.id}`
            : `<?= base_url('master/lokasi/simpan') ?>`;

        document.getElementById('inpLokasiNama').value = data ? data.nama_lokasi : '';
        document.getElementById('inpSharedAktif').checked = data ? (data.aktif == 1) : true;
    }

    // Set initial primary button label
    document.addEventListener('DOMContentLoaded', () => {
        const addLbl = document.getElementById('primaryAddMasterLabel');
        if (addLbl && tabAddLabels[currentMasterTab]) {
            addLbl.textContent = tabAddLabels[currentMasterTab];
        }
    });
</script>
<?= $this->endSection() ?>
