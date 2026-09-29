<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$bulanList = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Hitung periode sebelumnya & berikutnya untuk navigasi cepat
$prevBulan = $bulan - 1;
$prevTahun = $tahun;
if ($prevBulan < 1) { $prevBulan = 12; $prevTahun--; }

$nextBulan = $bulan + 1;
$nextTahun = $tahun;
if ($nextBulan > 12) { $nextBulan = 1; $nextTahun++; }

// Pre-hitung statistik hari & kolom yang aktif
$initDowntimeDays = 0;
$initUnpaidDays = 0;
$initOver24Days = 0;
$activeKatIdsInMonth = [];

for ($d = 1; $d <= $daysInMonth; $d++) {
    $dStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
    $dTot = 0;
    $dUnp = 0;
    if (isset($existingData[$dStr])) {
        foreach ($existingData[$dStr] as $kId => $entries) {
            foreach ($entries as $en) {
                $j = (float)($en['jam'] ?? 0);
                if ($j > 0) {
                    $dTot += $j;
                    $activeKatIdsInMonth[(int)$kId] = true;
                    if (in_array((int)$kId, [1, 2])) {
                        $dUnp += $j;
                    }
                }
            }
        }
    }
    if ($dTot > 0) $initDowntimeDays++;
    if ($dUnp > 0) $initUnpaidDays++;
    if ($dTot > 24) $initOver24Days++;
}
$initNormalDays = $daysInMonth - $initDowntimeDays;

// Kolom utama yang selalu tampil di mode "Kolom Praktis" (ditambah kategori apapun yang nilainya > 0 bulan ini)
$corePracticalKatIds = [1, 2, 3, 4, 5, 6, 10, 8, 13];
foreach ($activeKatIdsInMonth as $akId => $_) {
    if (!in_array($akId, $corePracticalKatIds)) {
        $corePracticalKatIds[] = $akId;
    }
}

$shortKatNames = [
    1  => 'Repair Rig',
    2  => 'Personnel',
    3  => 'SWA Rain',
    4  => 'Dry Road',
    5  => 'Dry Pad',
    6  => 'WO Daylight',
    7  => 'Perfo Job',
    8  => 'PHR Well',
    9  => 'CPI Rig',
    10 => '3rd Party',
    11 => 'Sharing Trans',
    12 => 'Foam Unit',
    13 => 'PHR Operator',
    14 => 'CE / PE',
    15 => 'Idul Fitri/Pilkada',
    16 => 'WO PLN',
    17 => 'WO OMS',
    18 => 'WO Dec LSC',
    19 => 'WO PDC',
    20 => 'ESP',
    21 => 'PEMILU',
];
?>
<style>
/* Hilangkan spinner bawaan browser pada input jam matriks NPT */
.npt-matrix-table input[type="number"]::-webkit-inner-spin-button,
.npt-matrix-table input[type="number"]::-webkit-outer-spin-button,
input.npt-no-spinner::-webkit-inner-spin-button,
input.npt-no-spinner::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
.npt-matrix-table input[type="number"],
input.npt-no-spinner {
    -moz-appearance: textfield !important;
    appearance: textfield !important;
}

/* ═══ Dual-Theme Surface & Editorial Tokens for /npt ═══ */
.npt-surface-card {
    background-color: var(--card) !important;
    border: 1px solid var(--border) !important;
    box-shadow: var(--shadow-card);
}
.npt-sub-surface {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
}
.npt-control-box {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    transition: border-color 0.15s ease;
}
.npt-control-box:focus-within {
    border-color: var(--muted-foreground) !important;
}
.npt-stepper-btn {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    color: var(--muted-foreground) !important;
    transition: all 0.15s ease;
}
.npt-stepper-btn:hover {
    background-color: var(--secondary) !important;
    color: var(--foreground) !important;
}
.npt-segmented-group {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    padding: 3px;
    border-radius: 10px;
}
.npt-tab-pill {
    color: var(--muted-foreground) !important;
    border-radius: 7px;
    transition: all 0.15s ease;
}
.npt-tab-pill:hover {
    color: var(--foreground) !important;
}
.npt-tab-pill.is-active {
    background-color: var(--card) !important;
    color: var(--foreground) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    border: 1px solid var(--border);
}
.npt-table-head {
    background-color: var(--background) !important;
    color: var(--muted-foreground) !important;
    border-bottom: 1px solid var(--border) !important;
}
.npt-table-head th {
    background-color: var(--background) !important;
    color: var(--muted-foreground) !important;
    border-color: var(--border) !important;
}
.npt-table-foot {
    background-color: var(--background) !important;
    color: var(--foreground) !important;
    border-top: 2px solid var(--border) !important;
}
.npt-table-foot td {
    background-color: var(--background) !important;
    border-color: var(--border) !important;
}
.npt-sticky-col {
    background-color: var(--card) !important;
}
.npt-matrix-row:hover .npt-sticky-col {
    background-color: var(--secondary) !important;
}
.npt-chip-btn {
    background-color: var(--background) !important;
    border: 1px solid var(--border) !important;
    color: var(--muted-foreground) !important;
    transition: all 0.15s ease;
}
.npt-chip-btn:hover {
    color: var(--foreground) !important;
    border-color: var(--muted-foreground) !important;
}
.npt-chip-btn.is-selected {
    background-color: var(--foreground) !important;
    color: var(--background) !important;
    border-color: var(--foreground) !important;
}
/* Sembunyikan kolom non-utama saat mode Kolom Praktis aktif */
.npt-matrix-table.is-compact-cols .col-extra-kat {
    display: none !important;
}
</style>

<div class="ui-screen ui-screen--downtime space-y-3.5">

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- 1. UNIFIED COMMAND & NAVIGATION BAR (RINGKAS, BERSIH, 1 BARIS TERPADU)  -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div class="p-3 rounded-xl npt-surface-card flex flex-col xl:flex-row xl:items-center justify-between gap-3">
        <!-- Kiri: Pilih Rig, Stepper Bulan/Tahun, & Pintasan Sumur -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Selector Rig -->
            <div class="flex items-center npt-control-box rounded-lg px-2.5 py-1.5">
                <i class="fa-solid fa-oil-well text-slate-400 text-xs mr-2"></i>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1.5">Rig</span>
                <select id="selectRig" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-extrabold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $rigId ? 'selected' : '' ?>><?= esc($r['kode']) ?> &mdash; <?= esc($r['nama_rig']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Stepper Periode Bulan & Tahun -->
            <div class="flex items-center gap-1">
                <a href="<?= base_url("npt/{$rigId}/{$prevBulan}/{$prevTahun}?tab={$activeTab}") ?>"
                   class="w-8 h-8 rounded-lg npt-stepper-btn flex items-center justify-center"
                   title="Bulan Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </a>
                <div class="flex items-center npt-control-box rounded-lg px-2.5 py-1.5">
                    <select id="selectBulan" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                        <?php foreach ($bulanList as $num => $nama): ?>
                            <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-slate-500 mx-1">/</span>
                    <select id="selectTahun" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer">
                        <?php for ($y = 2024; $y <= 2028; $y++): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <a href="<?= base_url("npt/{$rigId}/{$nextBulan}/{$nextTahun}?tab={$activeTab}") ?>"
                   class="w-8 h-8 rounded-lg npt-stepper-btn flex items-center justify-center"
                   title="Bulan Berikutnya">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <!-- Pintasan Detail Sumur Rig Ini -->
            <button type="button" onclick="showRigWellsModal(<?= $rigId ?>)"
                class="px-2.5 py-1.5 rounded-lg npt-stepper-btn font-bold text-xs flex items-center gap-1.5 cursor-pointer"
                title="Lihat daftar sumur armada <?= esc($rig['kode']) ?>">
                <i class="fa-solid fa-layer-group text-[11px]"></i>
                <span>Sumur <?= esc($rig['kode']) ?></span>
                <span class="px-1.5 py-0.2 rounded bg-slate-800 text-slate-300 font-mono text-[10px] font-bold" id="headerWellBadge"><?= count($rigWells ?? []) ?></span>
            </button>

            <!-- Pintasan ke Daily Report Step 2 -->
            <a href="<?= base_url('daily-report/log-harian/' . ($rigId ?? 1) . '/' . $bulan . '/' . $tahun) ?>"
               class="px-2.5 py-1.5 rounded-lg npt-stepper-btn font-bold text-xs flex items-center gap-1.5"
               title="Buka Input Log Harian Daily Report">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Log Harian DR</span>
            </a>
        </div>

        <!-- Kanan: 4 Tab Switcher & Export Excel -->
        <div class="flex flex-wrap items-center justify-between xl:justify-end gap-2">
            <div class="flex items-center npt-segmented-group overflow-x-auto custom-scrollbar">
                <button type="button" onclick="switchNptHubTab('harian')" id="hubTabBtn_harian"
                    class="hub-tab-btn npt-tab-pill px-3 py-1.5 font-bold text-xs flex items-center gap-1.5 whitespace-nowrap <?= $activeTab === 'harian' ? 'is-active' : '' ?>">
                    <i class="fa-solid fa-bolt text-[11px] text-amber-500"></i>
                    <span>Input &amp; Harian</span>
                </button>
                <button type="button" onclick="switchNptHubTab('bulanan')" id="hubTabBtn_bulanan"
                    class="hub-tab-btn npt-tab-pill px-3 py-1.5 font-bold text-xs flex items-center gap-1.5 whitespace-nowrap <?= $activeTab === 'bulanan' ? 'is-active' : '' ?>">
                    <i class="fa-solid fa-table-list text-[11px]"></i>
                    <span>Rekap 18 Rig</span>
                </button>
                <button type="button" onclick="switchNptHubTab('tahunan')" id="hubTabBtn_tahunan"
                    class="hub-tab-btn npt-tab-pill px-3 py-1.5 font-bold text-xs flex items-center gap-1.5 whitespace-nowrap <?= $activeTab === 'tahunan' ? 'is-active' : '' ?>">
                    <i class="fa-solid fa-table-cells text-[11px]"></i>
                    <span>Matriks SYS</span>
                </button>
                <button type="button" onclick="switchNptHubTab('chrono')" id="hubTabBtn_chrono"
                    class="hub-tab-btn npt-tab-pill px-3 py-1.5 font-bold text-xs flex items-center gap-1.5 whitespace-nowrap <?= $activeTab === 'chrono' ? 'is-active' : '' ?>">
                    <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                    <span>Kronologis</span>
                    <span class="text-[10px] font-mono opacity-75">(<?= count($chronoEvents) ?>)</span>
                </button>
            </div>

            <!-- Dropdown Export Excel -->
            <div class="relative inline-block text-left" id="exportDropdownWrapper">
                <button type="button" onclick="toggleExportMenu()" class="px-3 py-1.5 rounded-lg npt-stepper-btn font-bold text-xs flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-file-excel text-emerald-500 text-xs"></i>
                    <span>Export</span>
                    <i class="fa-solid fa-chevron-down text-[9px] opacity-70"></i>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-68 rounded-xl npt-surface-card shadow-2xl z-50 py-1.5 divide-y divide-slate-800/60">
                    <a href="<?= base_url("export/npt/{$rigId}/{$bulan}/{$tahun}") ?>" class="flex items-start gap-2.5 px-3.5 py-2 text-xs text-slate-200 hover:bg-slate-800/60 transition">
                        <i class="fa-solid fa-calendar-day text-emerald-500 mt-0.5"></i>
                        <div>
                            <div class="font-bold text-white">Matriks Harian <?= esc($rig['kode']) ?></div>
                            <div class="text-[10px] text-slate-400">Periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></div>
                        </div>
                    </a>
                    <a href="<?= base_url("export/npt-all/{$bulan}/{$tahun}") ?>" class="flex items-start gap-2.5 px-3.5 py-2 text-xs text-slate-200 hover:bg-slate-800/60 transition">
                        <i class="fa-solid fa-table-cells-large text-emerald-500 mt-0.5"></i>
                        <div>
                            <div class="font-bold text-white">Rekap Bulanan 18 Rig</div>
                            <div class="text-[10px] text-slate-400">Down Time Rig BMS</div>
                        </div>
                    </a>
                    <a href="<?= base_url("export/rekap-npt-tahunan/{$tahun}") ?>" class="flex items-start gap-2.5 px-3.5 py-2 text-xs text-slate-200 hover:bg-slate-800/60 transition">
                        <i class="fa-solid fa-trophy text-emerald-500 mt-0.5"></i>
                        <div>
                            <div class="font-bold text-white">Rekap Tahunan SYS (12 Bulan)</div>
                            <div class="text-[10px] text-slate-400">Format 32 Kolom SYS</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: WORKBENCH INPUT PRAKTIS & MATRIKS HARIAN                          -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_harian" class="hub-tab-content space-y-3.5 <?= $activeTab === 'harian' ? '' : 'hidden' ?>">

        <!-- ═══ A. 4 Kartu Ringkasan Eksekutif (Minimalist Editorial) ═══ -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Total UNPAID (Potong ODR)</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-black font-mono text-white" id="kpiCardUnpaid"><?= number_format($totalUnpaid, 2) ?></span>
                        <span class="text-xs text-slate-400 font-medium">Jam</span>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-rose-500 px-2 py-0.5 rounded bg-rose-500/10 border border-rose-500/20">
                    <?= $totalDT > 0 ? round(($totalUnpaid / $totalDT) * 100, 0) : 0 ?>%
                </span>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500 block">Total SBWC (Standby Rate)</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-black font-mono text-white" id="kpiCardSbwc"><?= number_format($totalSBWC, 2) ?></span>
                        <span class="text-xs text-slate-400 font-medium">Jam</span>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-amber-500 px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20">
                    <?= $totalDT > 0 ? round(($totalSBWC / $totalDT) * 100, 0) : 0 ?>%
                </span>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Akumulasi NPT</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-black font-mono text-white" id="kpiCardTotalDt"><?= number_format($totalDT, 2) ?></span>
                        <span class="text-xs text-slate-400 font-medium">Jam</span>
                    </div>
                </div>
                <span class="text-[11px] font-mono font-semibold text-slate-400" id="kpiCardActiveDays">
                    <?= $initDowntimeDays ?> / <?= $daysInMonth ?> Hari
                </span>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Estimasi Potongan ODR</span>
                    <div class="text-lg font-black font-mono text-rose-500 mt-1 tracking-tight" id="liveFinancialLoss">
                        -Rp <?= number_format(($totalUnpaid / 24) * $rig['odr'], 0, ',', '.') ?>
                    </div>
                </div>
                <span class="text-[11px] font-mono text-slate-400" title="Persentase penurunan Reliability bulan ini">
                    Rel Drop: <strong id="liveRelDrop" class="text-rose-500"><?= number_format($daysInMonth > 0 ? ($totalUnpaid / ($daysInMonth * 24)) * 100 : 0, 2) ?>%</strong>
                </span>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════════════ -->
        <!-- B. PANEL INPUT PRAKTIS LANGSUNG (TANPA HARUS CARI 21 KOLOM DI MATRIKS)   -->
        <!-- ════════════════════════════════════════════════════════════════════════ -->
        <div class="rounded-xl npt-surface-card p-3.5 sm:p-4 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-800/70">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 text-xs font-bold">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>Input Cepat &amp; Praktis per Tanggal</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-slate-800 text-slate-300" id="dockCurrentWellBadge">Pilih Tanggal</span>
                        </h4>
                        <p class="text-[11px] text-slate-400">Pilih tanggal &amp; pos kategori di bawah ini untuk mengisi jam dan remark secara instan tanpa perlu geser tabel.</p>
                    </div>
                </div>

                <!-- Chip Pintasan Pos Kategori yang Paling Sering Dipakai -->
                <div class="flex flex-wrap items-center gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1">Pos Cepat:</span>
                    <?php
                    $quickChipKats = [
                        1 => ['label' => 'Repair Rig', 'tipe' => 'UNPAID'],
                        2 => ['label' => 'Personnel', 'tipe' => 'UNPAID'],
                        3 => ['label' => 'SWA Rain', 'tipe' => 'SBWC'],
                        4 => ['label' => 'Dry Road', 'tipe' => 'SBWC'],
                        5 => ['label' => 'Dry Pad', 'tipe' => 'SBWC'],
                        6 => ['label' => 'WO Daylight', 'tipe' => 'SBWC'],
                        10 => ['label' => '3rd Party', 'tipe' => 'SBWC'],
                    ];
                    foreach ($quickChipKats as $qId => $qInfo):
                        $isUnp = ($qInfo['tipe'] === 'UNPAID');
                    ?>
                        <button type="button" onclick="selectDockCategory(<?= $qId ?>)"
                                id="dockChip_<?= $qId ?>"
                                class="dock-kat-chip npt-chip-btn px-2 py-1 rounded-md text-[11px] font-bold flex items-center gap-1 cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isUnp ? 'bg-rose-500' : 'bg-amber-500' ?>"></span>
                            <span><?= $qInfo['label'] ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Baris Form Input Praktis (Tanggal | Kategori | Jam + Stepper | Remark | Tombol Terapkan & Simpan) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 items-end">
                <!-- 1. Pilih Tanggal (2 Col) -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">1. Tanggal</label>
                    <select id="dockDaySelect" onchange="onDockDayOrKatChange()" class="w-full npt-control-box rounded-lg px-2.5 py-2 text-xs font-extrabold text-white focus:outline-none cursor-pointer">
                        <?php
                        $indoHariShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                        for ($d = 1; $d <= $daysInMonth; $d++):
                            $dStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                            $hName = $indoHariShort[(int)date('w', strtotime($dStr))] ?? '';
                            $wRow = $dailyLogMap[$dStr] ?? null;
                            $wTag = !empty($wRow['no_well']) ? " · W-{$wRow['no_well']}" : '';
                        ?>
                            <option value="<?= $d ?>">Tgl <?= sprintf('%02d', $d) ?> (<?= $hName ?>)<?= $wTag ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- 2. Pilih Kategori Downtime (3 Col) -->
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">2. Kategori Downtime</label>
                    <select id="dockKatSelect" onchange="onDockDayOrKatChange()" class="w-full npt-control-box rounded-lg px-2.5 py-2 text-xs font-extrabold text-white focus:outline-none cursor-pointer">
                        <optgroup label="UNPAID — Potong ODR">
                            <?php foreach ($kategoriList as $k): if ($k['tipe'] !== 'UNPAID') continue; ?>
                                <option value="<?= $k['id'] ?>" data-tipe="UNPAID">[UNPAID] <?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="SBWC — Standby With Crew">
                            <?php foreach ($kategoriList as $k): if ($k['tipe'] === 'UNPAID') continue; ?>
                                <option value="<?= $k['id'] ?>" data-tipe="SBWC">[SBWC] <?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <!-- 3. Input Jam + Tombol Cepat (2 Col) -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">3. Durasi (Jam)</label>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="adjustDockHour(1)" class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold npt-stepper-btn cursor-pointer">+1</button>
                            <button type="button" onclick="adjustDockHour(2)" class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold npt-stepper-btn cursor-pointer">+2</button>
                            <button type="button" onclick="setDockHourZero()" class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold npt-stepper-btn text-rose-400 cursor-pointer" title="Kosongkan jam pos ini">0</button>
                        </div>
                    </div>
                    <input type="number" step="0.25" min="0" max="24" id="dockHourInput"
                           placeholder="Contoh: 2.5"
                           onkeydown="if(event.key==='Enter'){event.preventDefault(); applyDockEntry(false);}"
                           class="w-full npt-control-box rounded-lg px-3 py-2 text-xs font-mono font-black text-white text-center focus:outline-none npt-no-spinner">
                </div>

                <!-- 4. Remark / Uraian Kejadian (3 Col) -->
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">4. Remark / Uraian Kejadian Tanggal Ini</label>
                    <input type="text" id="dockRemarkInput"
                           placeholder="Ketik kendala / alasan downtime..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault(); applyDockEntry(false);}"
                           class="w-full npt-control-box rounded-lg px-3 py-2 text-xs text-white focus:outline-none">
                </div>

                <!-- 5. Tombol Aksi Terapkan & Simpan (2 Col) -->
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="button" onclick="applyDockEntry(false)"
                            class="flex-1 py-2 px-2.5 rounded-lg npt-stepper-btn font-extrabold text-xs flex items-center justify-center gap-1.5 cursor-pointer"
                            title="Masukkan ke baris tabel di bawah">
                        <i class="fa-solid fa-plus text-emerald-500 text-[11px]"></i>
                        <span>Isi</span>
                    </button>
                    <button type="button" onclick="applyDockEntry(true)"
                            class="flex-[1.3] py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer"
                            title="Masukkan ke tabel dan langsung Simpan ke Database">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i>
                        <span>Isi &amp; Simpan</span>
                    </button>
                </div>
            </div>

            <!-- Baris Tambahan Otomatis Jika Kategori = 3rd Party (ID 10) -->
            <div id="dock3rdPartyBox" class="hidden pt-2.5 border-t border-slate-800/70">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-handshake text-amber-500"></i>
                        <span>Rincian Jam per Vendor 3rd Party (Otomatis menjumlahkan Durasi 3rd Party):</span>
                    </span>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
                    <?php foreach ($thirdParties as $tp): ?>
                        <div class="npt-sub-surface p-1.5 rounded-lg flex flex-col items-center">
                            <span class="text-[10px] font-bold text-slate-400 mb-1 truncate w-full text-center"><?= esc($tp['nama']) ?></span>
                            <input type="number" step="0.25" min="0" max="24"
                                   id="dockTp_<?= $tp['id'] ?>"
                                   data-tp-id="<?= $tp['id'] ?>"
                                   placeholder="0"
                                   oninput="onDockTpInput()"
                                   class="dock-tp-input w-full npt-control-box rounded px-1.5 py-1 text-center font-mono font-bold text-xs text-white focus:outline-none npt-no-spinner">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════════════ -->
        <!-- C. FORM & TABEL MATRIKS HARIAN (URUTAN KOLOM BARU: REMARK DI KIRI!)      -->
        <!-- ════════════════════════════════════════════════════════════════════════ -->
        <form action="<?= base_url('npt/simpan') ?>" method="POST" id="formNpt">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <input type="hidden" name="payload_json" id="nptPayloadJson">

            <div class="rounded-xl npt-surface-card overflow-hidden flex flex-col">
                <!-- Toolbar Filter Baris & Mode Jumlah Kolom -->
                <div class="p-3 border-b border-slate-800/70 flex flex-col lg:flex-row lg:items-center justify-between gap-2.5" style="background-color: var(--background);">
                    <!-- Kiri: Filter Baris Hari & Toggle Kolom Praktis vs Semua 21 Kolom -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center npt-segmented-group">
                            <button type="button" onclick="setTableFilterMode('all')" id="filterBtn_all"
                                class="filter-pill-btn npt-tab-pill is-active px-2.5 py-1 text-[11px] font-bold flex items-center gap-1.5 cursor-pointer">
                                <span>Semua</span>
                                <span class="font-mono text-[10px] opacity-75">(<?= $daysInMonth ?>)</span>
                            </button>
                            <button type="button" onclick="setTableFilterMode('downtime')" id="filterBtn_downtime"
                                class="filter-pill-btn npt-tab-pill px-2.5 py-1 text-[11px] font-bold flex items-center gap-1.5 cursor-pointer">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Ada NPT</span>
                                <span id="badgeDowntimeDaysCount" class="font-mono text-[10px]"><?= $initDowntimeDays ?></span>
                            </button>
                            <button type="button" onclick="setTableFilterMode('unpaid')" id="filterBtn_unpaid"
                                class="filter-pill-btn npt-tab-pill px-2.5 py-1 text-[11px] font-bold flex items-center gap-1.5 cursor-pointer">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Ada UNPAID</span>
                                <span id="badgeUnpaidDaysCount" class="font-mono text-[10px]"><?= $initUnpaidDays ?></span>
                            </button>
                        </div>

                        <!-- Toggle Mode Kolom: Kolom Praktis vs Semua 21 Kolom -->
                        <div class="flex items-center npt-segmented-group">
                            <button type="button" onclick="setColumnDisplayMode('compact')" id="colModeBtn_compact"
                                class="npt-tab-pill is-active px-2.5 py-1 text-[11px] font-bold flex items-center gap-1.5 cursor-pointer"
                                title="Tampilkan kolom utama & kategori yang memiliki jam aktif saja agar tidak perlu scroll jauh">
                                <i class="fa-solid fa-compress text-[10px] text-emerald-500"></i>
                                <span>Kolom Praktis (<?= count($corePracticalKatIds) ?> Pos)</span>
                            </button>
                            <button type="button" onclick="setColumnDisplayMode('all')" id="colModeBtn_all"
                                class="npt-tab-pill px-2.5 py-1 text-[11px] font-bold flex items-center gap-1.5 cursor-pointer"
                                title="Tampilkan seluruh 21 kolom kategori downtime">
                                <i class="fa-solid fa-table-columns text-[10px]"></i>
                                <span>Semua 21 Kolom</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kanan: Pencarian Cepat & Tombol Simpan Utama -->
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1 sm:w-60">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                            <input type="text" id="nptQuickSearchInput" oninput="onNptTableSearch()"
                                placeholder="Cari tanggal, sumur, remark..."
                                class="w-full npt-control-box rounded-lg pl-7 pr-7 py-1.5 text-xs text-white placeholder-slate-400 focus:outline-none">
                            <button type="button" onclick="clearNptTableSearch()" id="clearSearchBtn" class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>

                        <button type="submit" id="btnSubmitForm" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>

                <!-- ═══ Tabel Matriks Harian (Urutan Baru: Tgl | Well | Total | Remark | UNPAID | SBWC) ═══ -->
                <div class="overflow-x-auto custom-scrollbar relative max-h-[70vh]">
                    <table class="npt-matrix-table is-compact-cols w-full text-left text-xs border-collapse border-spacing-0 select-none" id="nptMainTable">
                        <thead class="npt-table-head font-bold uppercase text-[10px] sticky top-0 z-30">
                            <tr>
                                <!-- 1. Tgl (Sticky Kiri) -->
                                <th class="py-2.5 px-2 text-center w-16 min-w-[64px] border-r sticky left-0 z-40">
                                    <span>Tgl</span>
                                </th>
                                <!-- 2. Well (Sticky Kiri) -->
                                <th class="py-2.5 px-2 text-center w-18 min-w-[70px] border-r sticky left-16 z-40">
                                    <span>Well</span>
                                </th>
                                <!-- 3. Total Jam (Langsung di sebelah Well!) -->
                                <th class="py-2.5 px-2 text-center w-20 min-w-[76px] border-r font-black text-white">
                                    <span>Total DT</span>
                                </th>
                                <!-- 4. Remark / Uraian Kejadian (Langsung di Kiri, Tidak Perlu Geser 21 Kolom!) -->
                                <th class="py-2.5 px-3 text-left min-w-[240px] w-64 border-r font-black text-white">
                                    <div class="flex items-center justify-between">
                                        <span>Remark / Uraian Kejadian NPT</span>
                                        <span class="text-[9px] font-normal text-slate-400 normal-case">Langsung ketik</span>
                                    </div>
                                </th>

                                <!-- 5. Kategori Kolom (UNPAID dulu, lalu SBWC) -->
                                <?php foreach ($kategoriList as $k):
                                    $isUnpaid = ($k['tipe'] === 'UNPAID');
                                    $is3rdParty = ((int)$k['id'] === 10);
                                    $isCoreCol = in_array((int)$k['id'], $corePracticalKatIds);
                                    $extraColClass = $isCoreCol ? '' : 'col-extra-kat';
                                    $colWidth = $is3rdParty ? 'min-w-[115px] w-[120px]' : 'min-w-[90px] w-[94px]';
                                    $shortLabel = $shortKatNames[$k['id']] ?? $k['nama'];
                                ?>
                                    <th id="colHeader_<?= $k['id'] ?>"
                                        class="col-header-cell <?= $extraColClass ?> py-2 px-1.5 text-center <?= $colWidth ?> border-r transition-colors"
                                        title="<?= esc($k['nama']) ?> (<?= $k['tipe'] ?>)">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="px-1.5 py-0.2 rounded text-[8.5px] font-extrabold tracking-wider <?= $isUnpaid ? 'bg-rose-500/15 text-rose-500' : 'bg-amber-500/15 text-amber-500' ?>">
                                                <?= $isUnpaid ? 'UNPAID' : 'SBWC' ?>
                                            </span>
                                            <span class="block text-[10px] font-extrabold text-white leading-tight whitespace-normal"><?= esc($shortLabel) ?></span>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800/60 font-num" id="nptTableBody">
                            <?php
                            $indoHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                            for ($d = 1; $d <= $daysInMonth; $d++):
                                $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                $timestamp = strtotime($dateStr);
                                $wDay = (int)date('w', $timestamp);
                                $namaHari = $indoHari[$wDay] ?? '';
                                $isWeekend = ($wDay === 0 || $wDay === 6);

                                $rowTotal = 0;
                                $rowUnpaid = 0;
                                $rowSbwc = 0;
                                $dayRemark = '';
                                $dayRemarkUnpaid = '';
                                $dayTpSum = 0;
                                if (!empty($tpEntries[$dateStr])) {
                                    foreach ($tpEntries[$dateStr] as $tpVal) {
                                        $dayTpSum += (float)$tpVal;
                                    }
                                }

                                $dailyRow = $dailyLogMap[$dateStr] ?? null;
                                $hasDailySync = !empty($dailyRow);
                                if ($hasDailySync && !empty($dailyRow['remark_unpaid'])) {
                                    $dayRemarkUnpaid = $dailyRow['remark_unpaid'];
                                }

                                foreach ($kategoriList as $k) {
                                    if (isset($existingData[$dateStr][$k['id']])) {
                                        $entry = reset($existingData[$dateStr][$k['id']]);
                                        if ($entry && (float)$entry['jam'] > 0) {
                                            $val = (float)$entry['jam'];
                                            $rowTotal += $val;
                                            if ($k['tipe'] === 'UNPAID') {
                                                $rowUnpaid += $val;
                                                if (!empty($entry['remark']) && empty($dayRemarkUnpaid)) {
                                                    $dayRemarkUnpaid = $entry['remark'];
                                                }
                                            } else {
                                                $rowSbwc += $val;
                                                if (!empty($entry['remark']) && empty($dayRemark)) {
                                                    $dayRemark = $entry['remark'];
                                                }
                                            }
                                        }
                                    }
                                }

                                $hasDowntime = ($rowTotal > 0);
                                $hasUnpaid = ($rowUnpaid > 0);
                                $isOver24 = ($rowTotal > 24);

                                $rowClass = 'npt-matrix-row group transition-colors duration-100 ';
                                if ($isOver24) {
                                    $rowClass .= 'is-over24 border-l-4 border-l-red-500 bg-rose-500/10';
                                } elseif ($hasUnpaid) {
                                    $rowClass .= 'has-unpaid border-l-4 border-l-rose-500 bg-rose-500/5';
                                } elseif ($hasDowntime) {
                                    $rowClass .= 'has-sbwc border-l-4 border-l-amber-500 bg-amber-500/5';
                                } else {
                                    $rowClass .= 'is-zero border-l-4 border-l-transparent hover:bg-slate-800/40';
                                }

                                $searchKeywords = strtolower("tgl $d $dateStr $namaHari " . ($dailyRow['no_well'] ?? '') . " $dayRemark $dayRemarkUnpaid");
                            ?>
                                <tr class="<?= $rowClass ?>"
                                    id="row_<?= $d ?>"
                                    data-day="<?= $d ?>"
                                    data-total="<?= round($rowTotal, 2) ?>"
                                    data-unpaid="<?= round($rowUnpaid, 2) ?>"
                                    data-sbwc="<?= round($rowSbwc, 2) ?>"
                                    data-has-downtime="<?= $hasDowntime ? '1' : '0' ?>"
                                    data-has-unpaid="<?= $hasUnpaid ? '1' : '0' ?>"
                                    data-is-over24="<?= $isOver24 ? '1' : '0' ?>"
                                    data-search="<?= esc($searchKeywords) ?>">

                                    <!-- 1. Sticky Tgl & Hari (Klik untuk muat ke Panel Input Praktis di atas) -->
                                    <td id="dayCell_<?= $d ?>"
                                        onclick="loadDayIntoDock(<?= $d ?>)"
                                        title="Klik untuk memuat Tgl <?= $d ?> ke Panel Input Praktis di atas"
                                        class="npt-sticky-col py-2 px-2 text-center border-r border-slate-800/70 sticky left-0 w-16 min-w-[64px] z-20 cursor-pointer">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-black text-xs <?= $isWeekend ? 'text-rose-500' : 'text-white' ?>"><?= sprintf('%02d', $d) ?></span>
                                            <span class="text-[9px] font-semibold <?= $isWeekend ? 'text-rose-500/80' : 'text-slate-400' ?>"><?= $namaHari ?></span>
                                        </div>
                                    </td>

                                    <!-- 2. Sticky Well -->
                                    <td class="npt-sticky-col py-2 px-1.5 text-center border-r border-slate-800/70 sticky left-16 w-18 min-w-[70px] z-20">
                                        <?php if ($hasDailySync): ?>
                                            <button type="button" onclick="showRigWellsModal(<?= $rigId ?>, <?= (int)($dailyRow['no_well'] ?? 0) ?>)"
                                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-500 font-mono font-bold text-[10px] transition cursor-pointer"
                                                  title="Sumur #<?= esc($dailyRow['no_well'] ?? '-') ?> (<?= esc($dailyRow['nama_lokasi'] ?? '-') ?>)">
                                                <span>W-<?= esc($dailyRow['no_well'] ?? '-') ?></span>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-400 font-mono">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 3. Total Harian (Langsung di Kiri!) -->
                                    <td class="py-2 px-2 text-center border-r border-slate-800/70 font-bold font-mono" id="rowTotal_<?= $d ?>">
                                        <?php if ($rowTotal > 24): ?>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-rose-600 text-white font-black text-xs" title="Total jam melebihi 24 jam!">
                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                <?= number_format($rowTotal, 2) ?>
                                            </span>
                                        <?php elseif ($rowTotal > 0): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded <?= $rowUnpaid > 0 ? 'bg-rose-500/15 text-rose-500 border border-rose-500/30' : 'bg-amber-500/15 text-amber-500 border border-amber-500/30' ?> font-black text-xs">
                                                <?= number_format($rowTotal, 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-400 opacity-50 text-xs">0.00</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 4. Remark / Uraian Kejadian (Langsung di Kiri, Tanpa Perlu Geser 21 Kolom!) -->
                                    <td class="p-1 border-r border-slate-800/70 min-w-[240px]">
                                        <input type="text"
                                               name="remark[<?= $d ?>]"
                                               value="<?= esc($dayRemarkUnpaid ?: $dayRemark) ?>"
                                               placeholder="Ketik uraian kendala / kejadian..."
                                               oninput="markFormDirty(); updateRowSearchText(<?= $d ?>); syncDockIfCurrentDay(<?= $d ?>);"
                                               onfocus="highlightCrosshair(<?= $d ?>, null)"
                                               onblur="clearCrosshair(<?= $d ?>, null)"
                                               id="remark_<?= $d ?>"
                                               class="w-full bg-transparent px-2 py-1 text-xs text-white placeholder-slate-400/50 rounded hover:bg-slate-800/40 focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-500 transition font-sans">
                                    </td>

                                    <!-- 5. Sel Kategori (UNPAID & SBWC) -->
                                    <?php foreach ($kategoriList as $k):
                                        $val = '';
                                        if (isset($existingData[$dateStr][$k['id']])) {
                                            $firstEntry = reset($existingData[$dateStr][$k['id']]);
                                            if ($firstEntry && (float)$firstEntry['jam'] > 0) {
                                                $val = $firstEntry['jam'];
                                            }
                                        }

                                        $isUnpaid = ($k['tipe'] === 'UNPAID');
                                        $is3rdParty = ((int)$k['id'] === 10);
                                        $isCoreCol = in_array((int)$k['id'], $corePracticalKatIds);
                                        $extraColClass = $isCoreCol ? '' : 'col-extra-kat';
                                        $hasTpBreakdown = $is3rdParty && ($dayTpSum > 0 || !empty($tpEntries[$dateStr]));
                                        $numVal = (float)$val;
                                        $isFilled = ($numVal > 0);

                                        $displayVal = '';
                                        if ($isFilled) {
                                            $displayVal = ($numVal == (int)$numVal) ? (string)(int)$numVal : rtrim(rtrim(number_format($numVal, 2, '.', ''), '0'), '.');
                                        }

                                        if ($isFilled) {
                                            $cellInputClass = $isUnpaid
                                                ? 'is-filled bg-rose-500/15 border border-rose-500/60 text-rose-500 font-black'
                                                : 'is-filled bg-amber-500/15 border border-amber-500/60 text-amber-500 font-black';
                                        } else {
                                            $cellInputClass = 'is-empty bg-transparent border border-transparent text-slate-400 placeholder-slate-500/40 hover:border-slate-700 hover:bg-slate-800/30 focus:bg-slate-900 focus:text-white focus:border-slate-500';
                                        }

                                        $colCellWidth = $is3rdParty ? 'min-w-[115px] w-[120px]' : 'min-w-[90px] w-[94px]';
                                    ?>
                                        <td class="p-1 border-r border-slate-800/60 text-center relative <?= $extraColClass ?> <?= $colCellWidth ?>">
                                            <?php if ($is3rdParty): ?>
                                                <div class="flex items-center gap-1">
                                                    <input type="number" step="0.25" min="0" max="24"
                                                           name="npt[<?= $d ?>][<?= $k['id'] ?>]"
                                                           value="<?= $displayVal !== '' ? $displayVal : '' ?>"
                                                           placeholder="&middot;"
                                                           data-day="<?= $d ?>"
                                                           data-kat-id="<?= $k['id'] ?>"
                                                           data-tipe="<?= $k['tipe'] ?>"
                                                           oninput="onCellInput(this, <?= $d ?>, <?= $k['id'] ?>)"
                                                           onkeydown="onCellKeydown(event, this, <?= $d ?>, <?= $k['id'] ?>)"
                                                           onfocus="highlightCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                           onblur="clearCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                           id="cell_<?= $d ?>_<?= $k['id'] ?>"
                                                           class="flex-1 min-w-[56px] text-center font-mono font-black text-xs py-1 px-1 rounded focus:outline-none transition npt-no-spinner npt-input-<?= $d ?> <?= $cellInputClass ?>">
                                                    <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)"
                                                            id="btnTpToggle_<?= $d ?>"
                                                            title="Rincian Vendor 3rd Party Tgl <?= $d ?>"
                                                            class="w-6 h-6 rounded flex items-center justify-center transition flex-shrink-0 cursor-pointer <?= $hasTpBreakdown ? 'bg-amber-500/20 text-amber-500 border border-amber-500/40' : 'npt-stepper-btn' ?>">
                                                        <i class="fa-solid fa-handshake text-[9px]" id="iconTpToggle_<?= $d ?>"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <input type="number" step="0.25" min="0" max="24"
                                                       name="npt[<?= $d ?>][<?= $k['id'] ?>]"
                                                       value="<?= $displayVal !== '' ? $displayVal : '' ?>"
                                                       placeholder="&middot;"
                                                       data-day="<?= $d ?>"
                                                       data-kat-id="<?= $k['id'] ?>"
                                                       data-tipe="<?= $k['tipe'] ?>"
                                                       oninput="onCellInput(this, <?= $d ?>, <?= $k['id'] ?>)"
                                                       onkeydown="onCellKeydown(event, this, <?= $d ?>, <?= $k['id'] ?>)"
                                                       onfocus="highlightCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                       onblur="clearCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                       id="cell_<?= $d ?>_<?= $k['id'] ?>"
                                                       class="w-full text-center font-mono font-black text-xs py-1 px-1 rounded focus:outline-none transition npt-no-spinner npt-input-<?= $d ?> <?= $cellInputClass ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>

                                <!-- ═══ Panel Rincian 3rd Party Inline Drawer (Collapsible) ═══ -->
                                <tr id="row3rdParty_<?= $d ?>" class="hidden border-b border-slate-800" style="background-color: var(--background);">
                                    <td colspan="<?= count($kategoriList) + 4 ?>" class="p-3 pl-4 sm:pl-16">
                                        <div class="max-w-4xl p-3.5 rounded-xl npt-surface-card space-y-2.5">
                                            <div class="flex items-center justify-between border-b border-slate-800/70 pb-2">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-handshake text-amber-500 text-xs"></i>
                                                    <span class="text-xs font-bold text-white">Rincian Vendor 3rd Party &mdash; Tgl <?= $d ?> <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                                                </div>
                                                <div class="flex items-center gap-2.5">
                                                    <span class="text-xs text-slate-400">Total Vendor: <strong id="tpSumLabel_<?= $d ?>" class="font-mono text-amber-500 font-black"><?= number_format($dayTpSum, 2) ?></strong> Jam</span>
                                                    <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" class="px-2.5 py-1 rounded-lg npt-stepper-btn text-[11px] font-bold cursor-pointer">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
                                                <?php foreach ($thirdParties as $tp):
                                                    $tpJam = $tpEntries[$dateStr][$tp['id']] ?? '';
                                                ?>
                                                    <div class="npt-sub-surface p-1.5 rounded-lg flex flex-col items-center">
                                                        <span class="text-[10px] font-bold text-slate-400 mb-1 truncate w-full text-center"><?= esc($tp['nama']) ?></span>
                                                        <input type="number" step="0.25" min="0" max="24"
                                                               name="tp[<?= $d ?>][<?= $tp['id'] ?>]"
                                                               value="<?= $tpJam !== '' ? $tpJam : '' ?>"
                                                               placeholder="0"
                                                               oninput="calc3rdPartyTotal(<?= $d ?>); markFormDirty();"
                                                               class="w-full npt-control-box rounded px-1.5 py-1 text-center font-mono font-bold text-xs text-white focus:outline-none tp-input-<?= $d ?> npt-no-spinner">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endfor; ?>

                            <tr id="emptyFilterRow" class="hidden">
                                <td colspan="<?= count($kategoriList) + 4 ?>" class="py-10 text-center">
                                    <div class="max-w-md mx-auto space-y-2">
                                        <p class="text-xs font-bold text-slate-300" id="emptyFilterMessage">Tidak ada baris yang cocok dengan filter.</p>
                                        <button type="button" onclick="setTableFilterMode('all')" class="px-3 py-1.5 rounded-lg npt-stepper-btn text-xs font-bold cursor-pointer">
                                            Tampilkan Semua Hari
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <!-- ═══ Subtotal Footer Matriks Harian ═══ -->
                        <tfoot class="npt-table-foot font-extrabold text-xs sticky bottom-0 z-20">
                            <tr>
                                <td colspan="2" class="py-2.5 px-2 text-center border-r sticky left-0 z-30 font-black uppercase text-[10px] tracking-wider">
                                    TOTAL BULAN
                                </td>
                                <td id="footerGrandTotal" class="py-2.5 px-2 text-center border-r font-mono text-xs font-black text-white">
                                    <?= number_format($totalDT, 2) ?>
                                </td>
                                <td class="py-2.5 px-3 border-r text-left text-[11px] font-sans">
                                    <span id="footerUnpaidTotal" class="text-rose-500 font-bold mr-3">Unpaid: <?= number_format($totalUnpaid, 2) ?>j</span>
                                    <span id="footerSbwcTotal" class="text-amber-500 font-bold">SBWC: <?= number_format($totalSBWC, 2) ?>j</span>
                                </td>
                                <?php foreach ($kategoriList as $k):
                                    $tot = $totalsPerKat[$k['id']] ?? 0;
                                    $isUnpaid = ($k['tipe'] === 'UNPAID');
                                    $isCoreCol = in_array((int)$k['id'], $corePracticalKatIds);
                                    $extraColClass = $isCoreCol ? '' : 'col-extra-kat';
                                ?>
                                    <td id="colFooter_<?= $k['id'] ?>"
                                        class="py-2.5 px-1 text-center border-r text-xs font-mono <?= $extraColClass ?> <?= $isUnpaid ? 'text-rose-500 font-black' : 'text-amber-500 font-bold' ?>">
                                        <?= $tot > 0 ? number_format($tot, 2) : '&mdash;' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: REKAP BULANAN SELURUH 18 RIG (DOWN TIME RIG BMS)      -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_bulanan" class="hub-tab-content space-y-4 <?= $activeTab === 'bulanan' ? '' : 'hidden' ?>">
        <div class="rounded-xl npt-surface-card overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/70 flex items-center justify-between" style="background-color: var(--background);">
                <span class="text-xs font-extrabold text-white uppercase tracking-wider">DOWN TIME RIG BMS PERIODE <?= strtoupper($bulanList[$bulan] ?? '') ?> <?= $tahun ?></span>
                <span class="text-[11px] font-mono font-bold text-slate-400">18 Unit Armada Rig</span>
            </div>

            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-800 text-xs">
                    <thead class="npt-table-head font-extrabold uppercase text-[10px] tracking-wider text-center select-none">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2 w-10 border">NO</th>
                            <th rowspan="2" class="py-2.5 px-3 border min-w-[95px] text-left">NAME RIG</th>
                            <th colspan="3" class="py-1.5 px-2 border text-rose-500">UNPAID (HRS)</th>
                            <th colspan="5" class="py-1.5 px-2 border text-amber-500">STAND BY WITH CREW &mdash; SBWC (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border min-w-[90px] text-right font-black text-white">TOTAL (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border min-w-[180px] text-left">REMARK UNPAID</th>
                            <th rowspan="2" class="py-2.5 px-2 min-w-[130px] w-36 border text-center">AKSI</th>
                        </tr>
                        <tr class="text-[9.5px]">
                            <th class="py-1.5 px-2 border text-rose-500">Repair Rig</th>
                            <th class="py-1.5 px-2 border text-rose-500">Personnel</th>
                            <th class="py-1.5 px-2 border text-rose-500 font-black">Total Unpaid</th>
                            <th class="py-1.5 px-2 border text-amber-500">SWA Rain</th>
                            <th class="py-1.5 px-2 border text-amber-500">Dry Road/Pad</th>
                            <th class="py-1.5 px-2 border text-amber-500">WO Daylight</th>
                            <th class="py-1.5 px-2 border text-amber-500">3rd Party</th>
                            <th class="py-1.5 px-2 border text-amber-500 font-black">Total SBWC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if (!empty($monthlyAllRigData['rows'])):
                            foreach ($monthlyAllRigData['rows'] as $r):
                                $isCurRig = ($r['rig_id'] == $rigId);
                        ?>
                        <tr class="hover:bg-slate-800/60 transition <?= $isCurRig ? 'bg-amber-500/5' : '' ?>">
                            <td class="py-2 px-2 text-center text-xs text-slate-400 border border-slate-800"><?= $r['no'] ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 font-sans">
                                <button type="button" onclick="showRigWellsModal(<?= $r['rig_id'] ?>)"
                                    class="text-left font-bold text-white hover:text-amber-500 transition flex items-center justify-between w-full cursor-pointer">
                                    <span class="<?= $isCurRig ? 'text-amber-500 font-extrabold' : '' ?>"><?= esc($r['kode']) ?></span>
                                </button>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['repair'] > 0 ? 'text-rose-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['repair'] > 0 ? number_format($r['repair'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['pers'] > 0 ? 'text-rose-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['pers'] > 0 ? number_format($r['pers'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black text-rose-500">
                                <?= $r['tot_unpaid'] > 0 ? number_format($r['tot_unpaid'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['rain'] > 0 ? 'text-amber-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['rain'] > 0 ? number_format($r['rain'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['road'] > 0 ? 'text-amber-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['road'] > 0 ? number_format($r['road'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['day'] > 0 ? 'text-amber-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['day'] > 0 ? number_format($r['day'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['tp'] > 0 ? 'text-amber-500 font-bold' : 'text-slate-500' ?>">
                                <?= $r['tp'] > 0 ? number_format($r['tp'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black text-amber-500">
                                <?= $r['tot_sbwc'] > 0 ? number_format($r['tot_sbwc'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-3 text-right font-black text-xs text-white border border-slate-800">
                                <?= $r['total_hrs'] > 0 ? number_format($r['total_hrs'], 2) : '-' ?>
                            </td>
                            <td class="py-2 px-3 text-left text-xs font-sans text-slate-300 border border-slate-800 truncate max-w-[220px]" title="<?= esc($r['rem_unpaid']) ?>">
                                <?= !empty($r['rem_unpaid']) ? esc($r['rem_unpaid']) : '<span class="text-slate-500">-</span>' ?>
                            </td>
                            <td class="py-1.5 px-2 text-center border border-slate-800">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="showRigWellsModal(<?= $r['rig_id'] ?>)"
                                        class="px-2 py-1 rounded npt-stepper-btn font-bold text-[10.5px] flex items-center gap-1 cursor-pointer">
                                        <span>Sumur</span>
                                    </button>
                                    <button type="button" onclick="jumpToRigHarian(<?= $r['rig_id'] ?>)"
                                        class="px-2 py-1 rounded npt-stepper-btn font-bold text-[10.5px] flex items-center gap-1 cursor-pointer">
                                        <span>Harian</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php $gt = $monthlyAllRigData['grandTotal'] ?? []; ?>
                    <tfoot class="npt-table-foot font-extrabold text-xs">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border font-sans uppercase">
                                TOTAL DOWNTIME ALL RIG
                            </td>
                            <td class="py-2.5 px-2 text-center border text-rose-500"><?= number_format($gt['repair'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-rose-500"><?= number_format($gt['pers'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-rose-500 font-black"><?= number_format($gt['tot_unpaid'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-amber-500"><?= number_format($gt['rain'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-amber-500"><?= number_format($gt['road'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-amber-500"><?= number_format($gt['day'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-amber-500"><?= number_format($gt['tp'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border text-amber-500 font-black"><?= number_format($gt['tot_sbwc'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-3 text-right font-black text-sm text-white border">
                                <?= number_format($gt['total_hrs'] ?? 0, 2) ?>
                            </td>
                            <td colspan="2" class="py-2.5 px-3 border"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: REKAP TAHUNAN GRAND TOTAL & MATRIKS SYS (12 BULAN)    -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_tahunan" class="hub-tab-content space-y-4 <?= $activeTab === 'tahunan' ? '' : 'hidden' ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-xl npt-surface-card">
                <span class="text-[10px] font-bold uppercase text-rose-500 block mb-1">Grand Total Unpaid (Repair Rig)</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black font-mono text-white"><?= number_format((float)($annualNptData['grand_total'][2] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card">
                <span class="text-[10px] font-bold uppercase text-rose-500 block mb-1">Grand Total Unpaid (Personel)</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black font-mono text-white"><?= number_format((float)($annualNptData['grand_total'][3] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card">
                <span class="text-[10px] font-bold uppercase text-amber-500 block mb-1">Grand Total SBWC (Standby)</span>
                <?php
                    $sbwcTotAnnual = 0;
                    for ($c = 4; $c <= 31; $c++) {
                        $sbwcTotAnnual += (float)($annualNptData['grand_total'][$c] ?? 0);
                    }
                ?>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black font-mono text-white"><?= number_format($sbwcTotAnnual, 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl npt-surface-card">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Grand Total NPT Seluruh Armada</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black font-mono text-white"><?= number_format((float)($annualNptData['grand_total'][32] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl npt-surface-card overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/70 flex flex-wrap items-center justify-between gap-2" style="background-color: var(--background);">
                <div>
                    <span class="text-xs font-bold text-white uppercase tracking-wider block">Matriks Rinci Semua Pos Downtime &amp; 10 Vendor 3rd Party (SYS)</span>
                    <span class="text-[11px] text-slate-400">Periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                </div>
                <a href="<?= base_url("export/rekap-npt-tahunan/{$tahun}") ?>" class="px-3 py-1.5 rounded-lg npt-stepper-btn font-bold text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-file-excel text-emerald-500"></i>
                    <span>Export SYS Excel</span>
                </a>
            </div>

            <div class="overflow-x-auto custom-scrollbar max-h-[72vh]">
                <table class="w-full text-center border-collapse text-xs border border-slate-800 select-text">
                    <thead class="npt-table-head sticky top-0 z-20 font-bold">
                        <tr>
                            <th rowspan="3" class="py-2 px-2 border w-10 text-center sticky left-0 z-30">NO</th>
                            <th rowspan="3" class="py-2 px-3 border min-w-[110px] text-left sticky left-10 z-30">NAME RIG</th>
                            <th colspan="2" class="py-1.5 px-2 border text-center text-rose-500">UNPAID</th>
                            <th colspan="28" class="py-1.5 px-2 border text-center text-amber-500">STAND BY WITH CREW ( SBWC )</th>
                            <th rowspan="3" class="py-2 px-3 border min-w-[80px] text-right font-bold">TOTAL (HRS)</th>
                            <th rowspan="3" class="py-2 px-4 border min-w-[200px] text-left">REMARK UNPAID</th>
                        </tr>
                        <tr class="text-[10px]">
                            <th rowspan="2" class="py-2 px-1.5 border text-rose-500 font-bold">Repair Rig</th>
                            <th rowspan="2" class="py-2 px-1.5 border text-rose-500 font-bold">PERSONEL</th>
                            <th rowspan="2" class="py-2 px-1.5 border">SWA Rain</th>
                            <th rowspan="2" class="py-2 px-1.5 border">Dry Road</th>
                            <th rowspan="2" class="py-2 px-1.5 border">Dry Pad</th>
                            <th rowspan="2" class="py-2 px-1.5 border">Daylight</th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][8]['h3'] ?? 'PT. CHAST') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][9]['h3'] ?? 'WO PDC') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border">PHR Well</th>
                            <th rowspan="2" class="py-2 px-1.5 border">ESP</th>
                            <th colspan="10" class="py-1.5 px-1.5 border text-center text-amber-500 font-black tracking-wider">3RD PARTY (10 VENDOR)</th>
                            <th rowspan="2" class="py-2 px-1.5 border">UNISAT</th>
                            <th rowspan="2" class="py-2 px-1.5 border">TRANS</th>
                            <th rowspan="2" class="py-2 px-1.5 border">Foam Unit</th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][25]['h3'] ?? 'WO Decision') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][26]['h3'] ?? 'PEMILU') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][27]['h3'] ?? 'WO OMS') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][28]['h3'] ?? 'PT. PCM') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][29]['h3'] ?? 'WO COSL') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][30]['h3'] ?? 'SAFARI') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border"><?= esc($monthData['cols'][31]['h3'] ?? 'IDUL FITRI') ?></th>
                        </tr>
                        <tr class="text-[9px] font-bold">
                            <th class="py-1 px-1 border">BHI</th>
                            <th class="py-1 px-1 border">HLS</th>
                            <th class="py-1 px-1 border">WI</th>
                            <th class="py-1 px-1 border">HALCO</th>
                            <th class="py-1 px-1 border">EJP</th>
                            <th class="py-1 px-1 border">SCHL</th>
                            <th class="py-1 px-1 border">MGA</th>
                            <th class="py-1 px-1 border">SGN</th>
                            <th class="py-1 px-1 border">BUKAKA</th>
                            <th class="py-1 px-1 border">PESI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if ($monthData && !empty($monthData['rows'])):
                            foreach ($monthData['rows'] as $r):
                                $vals = $r['vals'] ?? [];
                        ?>
                        <tr class="hover:bg-slate-800/60 transition group">
                            <td class="npt-sticky-col py-1.5 px-1.5 border border-slate-800 text-slate-400 font-mono text-center sticky left-0 z-10"><?= $r['no'] ?></td>
                            <td class="npt-sticky-col py-1.5 px-3 border border-slate-800 font-bold text-white text-left whitespace-nowrap font-sans sticky left-10 z-10"><?= esc($r['rig']) ?></td>
                            <?php for ($c = 2; $c <= 31; $c++):
                                $val = $vals[$c] ?? 0;
                                $hasVal = ($val > 0);
                                $isUnpaid = ($c == 2 || $c == 3);
                            ?>
                                <td class="py-1.5 px-1 border border-slate-800 text-center font-mono text-xs <?= $hasVal ? ($isUnpaid ? 'text-rose-500 font-bold' : 'text-amber-500 font-bold') : 'text-slate-500' ?>">
                                    <?= $hasVal ? (fmod($val, 1) !== 0.0 ? number_format($val, 2) : (int)$val) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-1.5 px-2 border border-slate-800 font-bold font-mono text-right text-white">
                                <?= number_format((float)($vals[32] ?? 0), 2) ?>
                            </td>
                            <td class="py-1.5 px-3 border border-slate-800 text-left text-xs text-slate-300 truncate max-w-[200px]" title="<?= esc($vals[33] ?? '') ?>">
                                <?= esc($vals[33] ?? '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="34" class="py-8 text-center text-slate-500">
                                Belum ada data matriks rinci SYS untuk periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if ($monthData && !empty($monthData['totals'])):
                        $totals = $monthData['totals'];
                    ?>
                    <tfoot class="npt-table-foot font-extrabold sticky bottom-0 z-20">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border uppercase tracking-wider text-xs sticky left-0 z-30 font-black">
                                TOTAL
                            </td>
                            <?php for ($c = 2; $c <= 31; $c++):
                                $totVal = $totals[$c] ?? 0;
                                $hasTot = ($totVal > 0);
                                $isUnp = ($c == 2 || $c == 3);
                            ?>
                                <td class="py-2 px-1 border text-center font-mono text-xs <?= $isUnp ? 'text-rose-500' : 'text-amber-500' ?> font-bold">
                                    <?= $hasTot ? (fmod($totVal, 1) !== 0.0 ? number_format($totVal, 2) : (int)$totVal) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-2 px-2 border font-black font-mono text-right text-white">
                                <?= number_format((float)($totals[32] ?? 0), 2) ?>
                            </td>
                            <td class="py-2 px-3 border text-left text-xs text-slate-400">-</td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: LOG KRONOLOGIS DOWNTIME DETAIL                        -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_chrono" class="hub-tab-content space-y-3.5 <?= $activeTab === 'chrono' ? '' : 'hidden' ?>">
        <div class="p-3 rounded-xl npt-surface-card flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-slate-400 mr-2">Pilih Armada Rig:</span>
            <?php foreach ($rigListNames as $rName):
                $isActiveChrono = ($selectedChronoRig === $rName || str_replace('#', ' ', $selectedChronoRig) === $rName);
            ?>
                <button type="button" onclick="selectChronoRig('<?= esc($rName) ?>')"
                   class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer <?= $isActiveChrono ? 'npt-chip-btn is-selected' : 'npt-chip-btn' ?>">
                    <?= esc($rName) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php
            $totalChronoHrs = 0;
            foreach ($chronoEvents as $ev) {
                $totalChronoHrs += (float)($ev['hrs'] ?? 0);
            }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Armada Rig</span>
                    <h4 class="text-lg font-black text-white mt-0.5"><?= esc($selectedChronoRig) ?></h4>
                </div>
            </div>
            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kejadian</span>
                    <h4 class="text-lg font-black text-white mt-0.5"><?= count($chronoEvents) ?> <span class="text-xs text-slate-400 font-normal">Event</span></h4>
                </div>
            </div>
            <div class="p-3.5 rounded-xl npt-surface-card flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Akumulasi Downtime</span>
                    <h4 class="text-lg font-black text-amber-500 mt-0.5 font-num"><?= number_format($totalChronoHrs, 2) ?> <span class="text-xs text-slate-400 font-normal">Jam</span></h4>
                </div>
            </div>
        </div>

        <div class="p-2.5 rounded-xl npt-surface-card flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs pl-2"></i>
            <input type="text" id="chronoSearchInput" onkeyup="filterChronoTable()" placeholder="Cari berdasarkan tanggal, lokasi sumur, atau uraian kejadian..." class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none w-full placeholder-slate-400">
            <span id="chronoMatchCount" class="text-[11px] font-bold text-slate-400 whitespace-nowrap pr-2"><?= count($chronoEvents) ?> baris</span>
        </div>

        <div class="rounded-xl npt-surface-card overflow-hidden">
            <div class="overflow-x-auto max-h-[70vh] custom-scrollbar">
                <table class="w-full text-left text-xs border-collapse border border-slate-800" id="chronoTable">
                    <thead class="npt-table-head font-extrabold uppercase text-[10px] sticky top-0 z-10 tracking-wider text-center">
                        <tr>
                            <th class="py-2.5 px-3 w-12 border">No</th>
                            <th class="py-2.5 px-3 w-28 border">Tanggal</th>
                            <th class="py-2.5 px-3 w-28 border">Rig</th>
                            <th class="py-2.5 px-4 w-44 border text-left">Lokasi / Well</th>
                            <th class="py-2.5 px-4 min-w-[320px] border text-left">Remark / Uraian Detail Kejadian NPT</th>
                            <th class="py-2.5 px-3 w-28 text-right font-num border text-amber-500">Downtime (Jam)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200" id="chronoTableBody">
                        <?php if (!empty($chronoEvents)): ?>
                            <?php foreach ($chronoEvents as $idx => $ev):
                                $rem = $ev['remark'] ?? '';
                                $remUnpaid = $ev['remark_unpaid'] ?? '';
                            ?>
                            <tr class="hover:bg-slate-800/60 transition-colors chrono-row">
                                <td class="py-2 px-3 text-center font-bold text-slate-400 border border-slate-800 font-mono"><?= $ev['no'] ?></td>
                                <td class="py-2 px-3 text-center font-semibold text-slate-300 border border-slate-800 font-num whitespace-nowrap"><?= esc($ev['date']) ?></td>
                                <td class="py-2 px-3 font-bold text-white border border-slate-800 whitespace-nowrap"><?= esc($ev['rig']) ?></td>
                                <td class="py-2 px-4 font-bold text-white border border-slate-800"><?= esc($ev['location']) ?></td>
                                <td class="py-2 px-4 border border-slate-800 leading-relaxed font-sans">
                                    <span class="text-slate-200"><?= esc($rem) ?></span>
                                    <?php if (!empty($remUnpaid)): ?>
                                        <span class="block mt-0.5 text-[10px] text-rose-500 font-semibold italic">[Unpaid: <?= esc($remUnpaid) ?>]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 text-right font-black font-num text-sm text-amber-500 border border-slate-800">
                                    <?= is_numeric($ev['hrs']) ? number_format((float)$ev['hrs'], 2) : esc($ev['hrs']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Tidak ada data kejadian downtime untuk armada <?= esc($selectedChronoRig) ?>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="npt-table-foot font-extrabold sticky bottom-0">
                        <tr>
                            <td colspan="5" class="py-2.5 px-4 text-right uppercase tracking-wider text-xs border">Total Downtime <?= esc($selectedChronoRig) ?>:</td>
                            <td class="py-2.5 px-3 text-right font-black font-num text-sm text-amber-500 border">
                                <?= number_format($totalChronoHrs, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══ FLOATING UNSAVED CHANGES GUARD ═══ -->
<div id="unsavedChangesBanner" class="fixed bottom-6 right-6 z-40 npt-surface-card px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3.5 transition-all duration-300 transform translate-y-28 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2.5">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
        <div>
            <p class="text-xs font-extrabold text-white">Perubahan Belum Disimpan</p>
            <p class="text-[11px] text-slate-400">Klik simpan agar sinkron ke Daily Report.</p>
        </div>
    </div>
    <button type="button" onclick="submitFormDirectly()" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Simpan Sekarang</span>
    </button>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MODAL: DETAIL ARMADA & DAFTAR SUMUR RIG (SMART DOSSIER)     -->
<!-- ════════════════════════════════════════════════════════════ -->
<div id="rigWellsModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 transition-opacity">
    <div class="relative w-full max-w-4xl max-h-[92vh] npt-surface-card rounded-2xl shadow-2xl flex flex-col overflow-hidden text-slate-200">
        <div class="p-4 border-b border-slate-800/70 flex items-center justify-between gap-3" style="background-color: var(--background);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-500 text-sm flex-shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                            <span id="modalRigCode">BMS#07</span>
                            <span class="text-xs font-semibold text-slate-400 font-sans" id="modalRigName">(BMS 07)</span>
                        </h3>
                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[11px] font-bold" id="modalPeriodeLabel">Agustus 2026</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        ODR: <strong class="text-amber-500 font-mono" id="modalRigOdr">Rp 86.197.000</strong> / hari &bull; Target Rev: <span class="text-emerald-500 font-mono font-semibold" id="modalTargetRev">-</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a id="modalInputDailyBtn" href="#" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>Tambah Sumur</span>
                </a>
                <button type="button" onclick="closeRigWellsModal()" class="w-8 h-8 rounded-lg npt-stepper-btn flex items-center justify-center cursor-pointer" title="Tutup Modal (Esc)">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 border-b border-slate-800/70 text-xs" style="background-color: var(--background);">
            <div class="p-2 rounded-lg npt-surface-card flex flex-col">
                <span class="text-[10px] uppercase font-bold text-slate-400">Total Sumur</span>
                <span class="text-sm font-black font-mono text-white mt-0.5" id="kpiTotalWells">0</span>
                <span class="text-[10px] text-slate-500" id="kpiAnnualWells">Tahun: 0</span>
            </div>
            <div class="p-2 rounded-lg npt-surface-card flex flex-col">
                <span class="text-[10px] uppercase font-bold text-emerald-500">Jam Operasi (OPS)</span>
                <span class="text-sm font-black font-mono text-white mt-0.5" id="kpiTotalOps">0.00 j</span>
                <span class="text-[10px] text-slate-500">Produktif</span>
            </div>
            <div class="p-2 rounded-lg npt-surface-card flex flex-col">
                <span class="text-[10px] uppercase font-bold text-emerald-500">Jam MIRU</span>
                <span class="text-sm font-black font-mono text-white mt-0.5" id="kpiTotalMiru">0.00 j</span>
                <span class="text-[10px] text-slate-500">Moving</span>
            </div>
            <div class="p-2 rounded-lg npt-surface-card flex flex-col">
                <span class="text-[10px] uppercase font-bold text-amber-500">Total Downtime</span>
                <span class="text-sm font-black font-mono text-amber-500 mt-0.5" id="kpiTotalDt">0.00 j</span>
                <span class="text-[10px] text-slate-500" id="kpiDtBreakdown">Unpaid: 0j | SBWC: 0j</span>
            </div>
            <div class="col-span-2 sm:col-span-1 p-2 rounded-lg npt-surface-card flex flex-col">
                <span class="text-[10px] uppercase font-bold text-slate-400">Total Waktu</span>
                <span class="text-sm font-black font-mono text-white mt-0.5" id="kpiTotalJam">0.00 j</span>
                <span class="text-[10px] text-slate-500" id="kpiReliability">Reliability: -</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3" id="modalWellsContainer">
            <div class="py-12 text-center text-slate-500 space-y-3" id="modalLoadingState">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-amber-500"></i>
                <p class="text-xs font-semibold text-slate-400">Memuat rincian data sumur...</p>
            </div>
        </div>

        <div class="p-3 border-t border-slate-800/70 flex items-center justify-between text-xs text-slate-400" style="background-color: var(--background);">
            <span>Data tersinkronisasi otomatis antara Daily Report &amp; Rekap Downtime NPT</span>
            <button type="button" onclick="closeRigWellsModal()" class="px-3.5 py-1.5 rounded-lg npt-stepper-btn font-bold text-xs cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ════════════════════════════════════════════════════════════
    // 1. KONTROLER TAB TERPADU & PANEL INPUT PRAKTIS (DOCK)
    // ════════════════════════════════════════════════════════════
    function switchNptHubTab(tabName) {
        document.querySelectorAll('.hub-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.hub-tab-btn').forEach(btn => btn.classList.remove('is-active'));

        const targetContent = document.getElementById(`hubTabContent_${tabName}`);
        const targetBtn = document.getElementById(`hubTabBtn_${tabName}`);
        if (targetContent) targetContent.classList.remove('hidden');
        if (targetBtn) targetBtn.classList.add('is-active');

        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    // Toggle Kolom Praktis vs Semua 21 Kolom
    function setColumnDisplayMode(mode) {
        const table = document.getElementById('nptMainTable');
        const btnCompact = document.getElementById('colModeBtn_compact');
        const btnAll = document.getElementById('colModeBtn_all');
        if (!table) return;

        if (mode === 'all') {
            table.classList.remove('is-compact-cols');
            if (btnAll) btnAll.classList.add('is-active');
            if (btnCompact) btnCompact.classList.remove('is-active');
        } else {
            table.classList.add('is-compact-cols');
            if (btnCompact) btnCompact.classList.add('is-active');
            if (btnAll) btnAll.classList.remove('is-active');
        }
    }

    // Pilih Kategori dari Chip Cepat di Panel Input Praktis
    function selectDockCategory(katId) {
        const katSelect = document.getElementById('dockKatSelect');
        if (katSelect) {
            katSelect.value = String(katId);
            onDockDayOrKatChange();
            const hourInput = document.getElementById('dockHourInput');
            if (hourInput) {
                hourInput.focus();
                hourInput.select();
            }
        }
    }

    // Muat Tanggal Tertentu dari Baris Tabel ke Panel Input Praktis
    function loadDayIntoDock(day) {
        const daySelect = document.getElementById('dockDaySelect');
        if (daySelect) {
            daySelect.value = String(day);
            onDockDayOrKatChange();
            const hourInput = document.getElementById('dockHourInput');
            if (hourInput) {
                hourInput.focus();
                hourInput.select();
            }
        }
    }

    // Sinkronisasi Nilai Saat Tanggal atau Kategori di Panel Input Praktis Berubah
    function onDockDayOrKatChange() {
        const day = parseInt(document.getElementById('dockDaySelect')?.value || '1', 10);
        const katId = parseInt(document.getElementById('dockKatSelect')?.value || '1', 10);

        // Update active state pada chip cepat
        document.querySelectorAll('.dock-kat-chip').forEach(chip => {
            if (chip.id === `dockChip_${katId}`) {
                chip.classList.add('is-selected');
            } else {
                chip.classList.remove('is-selected');
            }
        });

        // Tampilkan / sembunyikan kotak rincian 3rd Party (ID 10)
        const tpBox = document.getElementById('dock3rdPartyBox');
        if (tpBox) {
            if (katId === 10) {
                tpBox.classList.remove('hidden');
                // Muat nilai vendor 3rd party dari baris tabel
                document.querySelectorAll('.dock-tp-input').forEach(tpInp => {
                    const tpId = tpInp.getAttribute('data-tp-id');
                    const mainTp = document.querySelector(`input[name="tp[${day}][${tpId}]"]`);
                    tpInp.value = mainTp && mainTp.value !== '' ? mainTp.value : '';
                });
            } else {
                tpBox.classList.add('hidden');
            }
        }

        // Ambil nilai jam & remark yang sudah ada di baris tabel untuk tanggal & kategori ini
        const mainCell = document.getElementById(`cell_${day}_${katId}`);
        const hourInput = document.getElementById('dockHourInput');
        if (hourInput && mainCell) {
            hourInput.value = mainCell.value !== '' ? mainCell.value : '';
        }

        const mainRemark = document.getElementById(`remark_${day}`);
        const dockRemark = document.getElementById('dockRemarkInput');
        if (dockRemark && mainRemark) {
            dockRemark.value = mainRemark.value || '';
        }

        // Update badge info tanggal/well di header dock
        const badge = document.getElementById('dockCurrentWellBadge');
        const dayOpt = document.querySelector(`#dockDaySelect option[value="${day}"]`);
        if (badge && dayOpt) {
            badge.textContent = dayOpt.textContent;
        }
    }

    function syncDockIfCurrentDay(day) {
        const curDockDay = parseInt(document.getElementById('dockDaySelect')?.value || '0', 10);
        if (curDockDay === day) {
            const mainRemark = document.getElementById(`remark_${day}`);
            const dockRemark = document.getElementById('dockRemarkInput');
            if (dockRemark && mainRemark && document.activeElement !== dockRemark) {
                dockRemark.value = mainRemark.value || '';
            }
        }
    }

    function adjustDockHour(delta) {
        const hourInput = document.getElementById('dockHourInput');
        if (!hourInput) return;
        let val = parseFloat(hourInput.value) || 0;
        val = Math.max(0, Math.min(24, Math.round((val + delta) * 100) / 100));
        hourInput.value = val > 0 ? val : '';
    }

    function setDockHourZero() {
        const hourInput = document.getElementById('dockHourInput');
        if (hourInput) hourInput.value = '';
    }

    function onDockTpInput() {
        let sum = 0;
        document.querySelectorAll('.dock-tp-input').forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v) && v > 0) sum += v;
        });
        const hourInput = document.getElementById('dockHourInput');
        if (hourInput) {
            hourInput.value = sum > 0 ? sum : '';
        }
    }

    // Terapkan Input dari Panel Praktis ke Baris Tabel (dan Opsional Simpan Langsung)
    function applyDockEntry(autoSubmit = false) {
        const day = parseInt(document.getElementById('dockDaySelect')?.value || '1', 10);
        const katId = parseInt(document.getElementById('dockKatSelect')?.value || '1', 10);
        const hourVal = (document.getElementById('dockHourInput')?.value || '').trim();
        const remarkVal = (document.getElementById('dockRemarkInput')?.value || '').trim();

        // Jika kategori adalah 3rd Party (ID 10), salin juga rincian vendornya
        if (katId === 10) {
            document.querySelectorAll('.dock-tp-input').forEach(tpInp => {
                const tpId = tpInp.getAttribute('data-tp-id');
                const mainTp = document.querySelector(`input[name="tp[${day}][${tpId}]"]`);
                if (mainTp) {
                    mainTp.value = tpInp.value.trim();
                }
            });
            calc3rdPartyTotal(day);
        }

        // Pastikan kolom tersebut tampil di tabel jika sebelumnya tersembunyi di mode compact
        const colHeader = document.getElementById(`colHeader_${katId}`);
        if (colHeader && colHeader.classList.contains('col-extra-kat') && parseFloat(hourVal) > 0) {
            document.querySelectorAll(`#colHeader_${katId}, #colFooter_${katId}, input[name$="[${katId}]"]`).forEach(el => {
                const tdOrTh = el.closest('td, th') || el;
                tdOrTh.classList.remove('col-extra-kat');
            });
        }

        const mainCell = document.getElementById(`cell_${day}_${katId}`);
        if (mainCell) {
            mainCell.value = (hourVal !== '' && parseFloat(hourVal) > 0) ? hourVal : '';
            onCellInput(mainCell, day, katId);
        }

        const mainRemark = document.getElementById(`remark_${day}`);
        if (mainRemark && remarkVal !== '') {
            mainRemark.value = remarkVal;
            updateRowSearchText(day);
        }

        recalcRow(day);
        markFormDirty();

        const updatedRow = document.getElementById(`row_${day}`);
        if (updatedRow) {
            updatedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            updatedRow.classList.add('ring-2', 'ring-emerald-500');
            setTimeout(() => updatedRow.classList.remove('ring-2', 'ring-emerald-500'), 1800);
        }

        if (autoSubmit) {
            submitFormDirectly();
        }
    }

    function jumpToRigHarian(rigId) {
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('npt') ?>/${rigId}/${bulan}/${tahun}?tab=harian`;
    }

    function selectChronoRig(rigName) {
        const url = new URL(window.location);
        url.searchParams.set('tab', 'chrono');
        url.searchParams.set('chrono_rig', rigName);
        window.location.href = url.toString();
    }

    // ════════════════════════════════════════════════════════════
    // 2. EXPORT DROPDOWN HANDLER
    // ════════════════════════════════════════════════════════════
    function toggleExportMenu() {
        const menu = document.getElementById('exportMenu');
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('exportDropdownWrapper');
        const menu = document.getElementById('exportMenu');
        if (wrapper && menu && !wrapper.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    // ════════════════════════════════════════════════════════════
    // 3. NAVIGASI TOOLBAR UTAMA
    // ════════════════════════════════════════════════════════════
    function navigateGrid() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        const urlParams = new URLSearchParams(window.location.search);
        const curTab = urlParams.get('tab') || 'harian';
        window.location.href = `<?= base_url('npt') ?>/${rigId}/${bulan}/${tahun}?tab=${curTab}`;
    }
    // ════════════════════════════════════════════════════════════
    // 4. HIGHLIGHTER CROSSHAIR MATRIKS HARIAN
    // ════════════════════════════════════════════════════════════
    function highlightCrosshair(day, katId) {
        const rowEl = document.getElementById(`row_${day}`);
        if (rowEl) {
            rowEl.classList.add('bg-blue-950/70', 'ring-2', 'ring-blue-500/60');
        }
        const dayCell = document.getElementById(`dayCell_${day}`);
        if (dayCell) {
            dayCell.classList.add('!bg-blue-600', '!text-white', 'font-black');
        }
        if (katId) {
            const colHeader = document.getElementById(`colHeader_${katId}`);
            if (colHeader) {
                colHeader.classList.add('!bg-blue-700', '!text-white', 'ring-2', 'ring-blue-400');
            }
        }
    }

    function clearCrosshair(day, katId) {
        const rowEl = document.getElementById(`row_${day}`);
        if (rowEl) {
            rowEl.classList.remove('bg-blue-950/70', 'ring-2', 'ring-blue-500/60');
        }
        const dayCell = document.getElementById(`dayCell_${day}`);
        if (dayCell) {
            dayCell.classList.remove('!bg-blue-600', '!text-white', 'font-black');
        }
        if (katId) {
            const colHeader = document.getElementById(`colHeader_${katId}`);
            if (colHeader) {
                colHeader.classList.remove('!bg-blue-700', '!text-white', 'ring-2', 'ring-blue-400');
            }
        }
    }

    // ════════════════════════════════════════════════════════════
    // 4B. SMART FILTER & VIEW MODE MATRIKS
    // ════════════════════════════════════════════════════════════
    let currentTableFilterMode = 'all';

    function setTableFilterMode(mode) {
        currentTableFilterMode = mode;

        const modes = ['all', 'downtime', 'unpaid', 'over24'];
        modes.forEach(m => {
            const btn = document.getElementById(`filterBtn_${m}`);
            if (btn) btn.classList.remove('is-active');
        });

        const activeBtn = document.getElementById(`filterBtn_${mode}`);
        if (activeBtn) activeBtn.classList.add('is-active');

        applyTableFilters();
    }

    function onNptTableSearch() {
        const input = document.getElementById('nptQuickSearchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        if (input && clearBtn) {
            if (input.value.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        applyTableFilters();
    }

    function clearNptTableSearch() {
        const input = document.getElementById('nptQuickSearchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        applyTableFilters();
    }

    function updateRowSearchText(day) {
        const row = document.getElementById(`row_${day}`);
        if (!row) return;
        const remarkUnpaid = document.getElementById(`remark_unpaid_${day}`)?.value || '';
        const remarkSbwc = document.getElementById(`remark_${day}`)?.value || '';
        let baseSearch = row.getAttribute('data-search') || '';
        row.setAttribute('data-search', `${baseSearch} ${remarkUnpaid.toLowerCase()} ${remarkSbwc.toLowerCase()}`);
    }

    function applyTableFilters() {
        const searchVal = (document.getElementById('nptQuickSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.npt-matrix-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const hasDowntime = row.getAttribute('data-has-downtime') === '1';
            const hasUnpaid = row.getAttribute('data-has-unpaid') === '1';
            const isOver24 = row.getAttribute('data-is-over24') === '1';
            const searchData = row.getAttribute('data-search') || '';

            let passMode = true;
            if (currentTableFilterMode === 'downtime') {
                passMode = hasDowntime;
            } else if (currentTableFilterMode === 'unpaid') {
                passMode = hasUnpaid;
            } else if (currentTableFilterMode === 'over24') {
                passMode = isOver24;
            }

            let passSearch = true;
            if (searchVal !== '') {
                passSearch = searchData.includes(searchVal);
            }

            if (passMode && passSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                const day = row.getAttribute('data-day');
                const tpRow = document.getElementById(`row3rdParty_${day}`);
                if (tpRow) tpRow.classList.add('hidden');
            }
        });

        const emptyRow = document.getElementById('emptyFilterRow');
        const emptyMsg = document.getElementById('emptyFilterMessage');
        if (emptyRow) {
            if (visibleCount === 0) {
                emptyRow.classList.remove('hidden');
                if (emptyMsg) {
                    if (searchVal !== '') {
                        emptyMsg.textContent = `Tidak ditemukan baris yang cocok dengan kata kunci "${searchVal}".`;
                    } else if (currentTableFilterMode === 'downtime') {
                        emptyMsg.textContent = 'Bulan ini 100% Zero Downtime! Seluruh hari beroperasi lancar.';
                    } else if (currentTableFilterMode === 'unpaid') {
                        emptyMsg.textContent = 'Tidak ada downtime bertipe UNPAID pada periode bulan ini.';
                    } else if (currentTableFilterMode === 'over24') {
                        emptyMsg.textContent = 'Tidak ada tanggal yang melebihi batas 24 jam.';
                    }
                }
            } else {
                emptyRow.classList.add('hidden');
            }
        }
    }

    // ════════════════════════════════════════════════════════════
    // 4C. ON-CELL INPUT & KEYBOARD NAVIGATION
    // ════════════════════════════════════════════════════════════
    function onCellInput(inputEl, day, katId) {
        const val = inputEl.value.trim();
        const numVal = parseFloat(val);
        const tipe = inputEl.getAttribute('data-tipe');

        if (!isNaN(numVal) && numVal > 0) {
            inputEl.classList.remove('is-empty', 'bg-transparent', 'border-transparent', 'text-slate-400');
            inputEl.classList.add('is-filled', 'font-black');
            if (tipe === 'UNPAID') {
                inputEl.classList.add('bg-rose-500/15', 'border', 'border-rose-500/60', 'text-rose-500');
            } else {
                inputEl.classList.add('bg-amber-500/15', 'border', 'border-amber-500/60', 'text-amber-500');
            }
        } else {
            inputEl.classList.remove('is-filled', 'font-black', 'bg-rose-500/15', 'border-rose-500/60', 'text-rose-500', 'bg-amber-500/15', 'border-amber-500/60', 'text-amber-500');
            inputEl.classList.add('is-empty', 'bg-transparent', 'border', 'border-transparent', 'text-slate-400');
        }

        // Sinkronkan juga ke input dock jika sedang memilih hari & kategori yang sama
        const curDockDay = parseInt(document.getElementById('dockDaySelect')?.value || '0', 10);
        const curDockKat = parseInt(document.getElementById('dockKatSelect')?.value || '0', 10);
        if (curDockDay === day && curDockKat === katId) {
            const dockHour = document.getElementById('dockHourInput');
            if (dockHour && document.activeElement !== dockHour) {
                dockHour.value = val;
            }
        }

        recalcRow(day);
        markFormDirty();
    }

    function onCellKeydown(e, inputEl, day, katId) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            submitFormDirectly();
            return;
        }
        if (e.key === 'Enter' || e.key === 'ArrowDown') {
            e.preventDefault();
            const nextCell = document.getElementById(`cell_${day + 1}_${katId}`);
            if (nextCell) {
                nextCell.focus();
                nextCell.select();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prevCell = document.getElementById(`cell_${day - 1}_${katId}`);
            if (prevCell) {
                prevCell.focus();
                prevCell.select();
            }
        }
    }

    // ════════════════════════════════════════════════════════════
    // 5. REKAP & VALIDASI MATRIKS FORM
    // ════════════════════════════════════════════════════════════
    let isFormDirty = false;
    let allowSubmitWithoutCheck = false;
    const daysInMonthCount = <?= (int)$daysInMonth ?>;

    function markFormDirty() {
        if (!isFormDirty) {
            isFormDirty = true;
            const banner = document.getElementById('unsavedChangesBanner');
            if (banner) {
                banner.classList.remove('translate-y-28', 'opacity-0', 'pointer-events-none');
                banner.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            }
        }
    }

    window.addEventListener('beforeunload', function (e) {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = 'Ada data jam downtime yang belum Anda simpan. Yakin ingin keluar?';
            return e.returnValue;
        }
    });

    function submitFormDirectly() {
        const form = document.getElementById('formNpt');
        if (form) form.requestSubmit();
    }

    function checkDaysOver24() {
        const overList = [];
        for (let d = 1; d <= daysInMonthCount; d++) {
            const inputs = document.querySelectorAll(`.npt-input-${d}`);
            let sum = 0;
            inputs.forEach(inp => {
                const val = parseFloat(inp.value);
                if (!isNaN(val)) sum += val;
            });
            if (sum > 24) {
                overList.push({ day: d, total: sum });
            }
        }
        return overList;
    }

    function recalcRow(day) {
        const inputs = document.querySelectorAll(`.npt-input-${day}`);
        let sum = 0;
        let unpaidSum = 0;
        let sbwcSum = 0;

        inputs.forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val) && val > 0) {
                sum += val;
                const tipe = inp.getAttribute('data-tipe');
                if (tipe === 'UNPAID') {
                    unpaidSum += val;
                } else {
                    sbwcSum += val;
                }
            }
        });

        const rowEl = document.getElementById(`row_${day}`);
        if (rowEl) {
            rowEl.setAttribute('data-total', sum.toFixed(2));
            rowEl.setAttribute('data-unpaid', unpaidSum.toFixed(2));
            rowEl.setAttribute('data-sbwc', sbwcSum.toFixed(2));
            rowEl.setAttribute('data-has-downtime', sum > 0 ? '1' : '0');
            rowEl.setAttribute('data-has-unpaid', unpaidSum > 0 ? '1' : '0');
            rowEl.setAttribute('data-is-over24', sum > 24 ? '1' : '0');

            rowEl.classList.remove('is-over24', 'has-unpaid', 'has-sbwc', 'is-zero', 'border-l-red-500', 'border-l-rose-500', 'border-l-amber-500', 'border-l-transparent', 'bg-rose-500/10', 'bg-rose-500/5', 'bg-amber-500/5');

            if (sum > 24) {
                rowEl.classList.add('is-over24', 'border-l-4', 'border-l-red-500', 'bg-rose-500/10');
            } else if (unpaidSum > 0) {
                rowEl.classList.add('has-unpaid', 'border-l-4', 'border-l-rose-500', 'bg-rose-500/5');
            } else if (sum > 0) {
                rowEl.classList.add('has-sbwc', 'border-l-4', 'border-l-amber-500', 'bg-amber-500/5');
            } else {
                rowEl.classList.add('is-zero', 'border-l-4', 'border-l-transparent');
            }
        }

        const targetEl = document.getElementById(`rowTotal_${day}`);
        if (targetEl) {
            if (sum > 24) {
                targetEl.innerHTML = `
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-rose-600 text-white font-black text-xs" title="Peringatan: Total jam melebihi 24 jam!">
                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                        ${sum.toFixed(2)}
                    </span>`;
            } else if (sum > 0) {
                const badgeBg = unpaidSum > 0 ? 'bg-rose-500/15 text-rose-500 border border-rose-500/30' : 'bg-amber-500/15 text-amber-500 border border-amber-500/30';
                targetEl.innerHTML = `<span class="inline-flex items-center px-1.5 py-0.5 rounded ${badgeBg} font-black text-xs">${sum.toFixed(2)}</span>`;
            } else {
                targetEl.innerHTML = `<span class="text-slate-400 opacity-50 text-xs">0.00</span>`;
            }
        }

        recalcTableSubtotals();
    }

    function recalcTableSubtotals() {
        let grandTotal = 0;
        let grandUnpaid = 0;
        let grandSbwc = 0;
        let downtimeDaysCount = 0;
        let unpaidDaysCount = 0;
        let over24DaysCount = 0;

        document.querySelectorAll('.col-header-cell').forEach(th => {
            const katId = th.id.replace('colHeader_', '');
            let colSum = 0;
            document.querySelectorAll(`input[name^="npt["][name$="][${katId}]"]`).forEach(inp => {
                const val = parseFloat(inp.value);
                if (!isNaN(val) && val > 0) colSum += val;
            });
            const colFooter = document.getElementById(`colFooter_${katId}`);
            if (colFooter) {
                colFooter.innerHTML = colSum > 0 ? colSum.toFixed(2) : '&mdash;';
            }
        });

        for (let d = 1; d <= daysInMonthCount; d++) {
            const row = document.getElementById(`row_${d}`);
            if (row) {
                const tot = parseFloat(row.getAttribute('data-total')) || 0;
                const unp = parseFloat(row.getAttribute('data-unpaid')) || 0;
                const sbw = parseFloat(row.getAttribute('data-sbwc')) || 0;

                grandTotal += tot;
                grandUnpaid += unp;
                grandSbwc += sbw;

                if (tot > 0) downtimeDaysCount++;
                if (unp > 0) unpaidDaysCount++;
                if (tot > 24) over24DaysCount++;
            }
        }

        // Perbarui Footer & KPI Cards
        const elGrand = document.getElementById('footerGrandTotal');
        const elUnpaid = document.getElementById('footerUnpaidTotal');
        const elSbwc = document.getElementById('footerSbwcTotal');
        if (elGrand) elGrand.textContent = grandTotal.toFixed(2);
        if (elUnpaid) elUnpaid.textContent = `Unpaid: ${grandUnpaid.toFixed(2)}j`;
        if (elSbwc) elSbwc.textContent = `SBWC: ${grandSbwc.toFixed(2)}j`;

        const kpiUnp = document.getElementById('kpiCardUnpaid');
        const kpiSbw = document.getElementById('kpiCardSbwc');
        const kpiTot = document.getElementById('kpiCardTotalDt');
        const kpiDays = document.getElementById('kpiCardActiveDays');
        if (kpiUnp) kpiUnp.textContent = grandUnpaid.toFixed(2);
        if (kpiSbw) kpiSbw.textContent = grandSbwc.toFixed(2);
        if (kpiTot) kpiTot.textContent = grandTotal.toFixed(2);
        if (kpiDays) kpiDays.textContent = `${downtimeDaysCount} / ${daysInMonthCount} Hari`;

        const bDt = document.getElementById('badgeDowntimeDaysCount');
        const bUnp = document.getElementById('badgeUnpaidDaysCount');
        if (bDt) bDt.textContent = downtimeDaysCount;
        if (bUnp) bUnp.textContent = unpaidDaysCount;

        const odrRig = parseFloat("<?= $rig['odr'] ?>") || 0;
        const lossEl = document.getElementById('liveFinancialLoss');
        const relEl = document.getElementById('liveRelDrop');
        if (lossEl) {
            const lossRp = (grandUnpaid / 24) * odrRig;
            lossEl.innerHTML = `-Rp ${lossRp.toLocaleString('id-ID', {maximumFractionDigits: 0})}`;
        }
        if (relEl) {
            const pct = daysInMonthCount > 0 ? (grandUnpaid / (daysInMonthCount * 24)) * 100 : 0;
            relEl.innerHTML = `${pct.toFixed(2)}%`;
        }
    }

    function toggle3rdPartyRow(day) {
        const row = document.getElementById(`row3rdParty_${day}`);
        if (!row) return;
        const isHidden = row.classList.contains('hidden') || row.style.display === 'none';
        if (isHidden) {
            row.classList.remove('hidden');
            row.style.display = '';
        } else {
            row.classList.add('hidden');
            row.style.display = 'none';
        }
    }

    function calc3rdPartyTotal(day) {
        const tpInputs = document.querySelectorAll(`.tp-input-${day}`);
        let tpSum = 0;
        tpInputs.forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val) && val > 0) tpSum += val;
        });

        const label = document.getElementById(`tpSumLabel_${day}`);
        if (label) label.textContent = tpSum.toFixed(2);

        const mainInput3rd = document.querySelector(`input[name="npt[${day}][10]"]`);
        if (mainInput3rd) {
            mainInput3rd.value = tpSum > 0 ? tpSum.toFixed(2) : '';
            onCellInput(mainInput3rd, day, 10);
        }
    }

    // ════════════════════════════════════════════════════════════
    // 7. PENCARIAN FILTER KRONOLOGIS TABLE
    // ════════════════════════════════════════════════════════════
    function filterChronoTable() {
        const input = document.getElementById('chronoSearchInput');
        if (!input) return;
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#chronoTableBody .chrono-row');
        let matchCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (filter === '' || text.includes(filter)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('chronoMatchCount');
        if (badge) badge.textContent = `${matchCount} baris`;
    }

    // ════════════════════════════════════════════════════════════
    // 8. FORM SERIALIZATION SEBELUM SUBMIT
    // ════════════════════════════════════════════════════════════
    const nptForm = document.getElementById('formNpt');
    if (nptForm) {
        nptForm.addEventListener('submit', function(e) {
            // INLINE SOFT GUARD: Tampilkan peringatan visual pada tombol, namun tidak memblokir submit penuh via modal.
            const overList = checkDaysOver24();
            if (overList.length > 0 && !allowSubmitWithoutCheck) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitForm');
                if (btn) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-rose-300 animate-pulse"></i><span>Yakin simpan? (Ditemukan Jam > 24)</span>`;
                    btn.classList.add('bg-rose-600', 'hover:bg-rose-500', 'ring-2', 'ring-rose-500', 'ring-offset-2', 'ring-offset-slate-950');
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-500', 'hover:ring-blue-500');
                    
                    allowSubmitWithoutCheck = true;
                    
                    setTimeout(() => {
                        allowSubmitWithoutCheck = false;
                        btn.innerHTML = originalText;
                        btn.classList.remove('bg-rose-600', 'hover:bg-rose-500', 'ring-2', 'ring-rose-500', 'ring-offset-2', 'ring-offset-slate-950');
                        btn.classList.add('bg-blue-600', 'hover:bg-blue-500', 'hover:ring-blue-500');
                    }, 5000);
                }
                return false;
            }

            isFormDirty = false;

            const payload = {
                npt: {},
                tp: {},
                remark: {},
                remark_unpaid: {}
            };

            document.querySelectorAll('input[name^="npt["]').forEach(inp => {
                const val = inp.value.trim();
                const matches = inp.name.match(/npt\[(\d+)\]\[(\d+)\]/);
                if (matches) {
                    const day = matches[1];
                    const katId = matches[2];
                    if (!payload.npt[day]) payload.npt[day] = {};
                    payload.npt[day][katId] = val !== '' ? val : 0;
                }
            });

            document.querySelectorAll('input[name^="tp["]').forEach(inp => {
                const val = inp.value.trim();
                const matches = inp.name.match(/tp\[(\d+)\]\[(\d+)\]/);
                if (matches) {
                    const day = matches[1];
                    const tpId = matches[2];
                    if (!payload.tp[day]) payload.tp[day] = {};
                    payload.tp[day][tpId] = val !== '' ? val : 0;
                }
            });

            document.querySelectorAll('input[name^="remark["]').forEach(inp => {
                const val = inp.value.trim();
                const matches = inp.name.match(/remark\[(\d+)\]/);
                if (matches) {
                    payload.remark[matches[1]] = val;
                }
            });

            const jsonInput = document.getElementById('nptPayloadJson');
            if (jsonInput) {
                jsonInput.value = JSON.stringify(payload);
            }

            document.querySelectorAll('input[name^="npt["], input[name^="tp["], input[name^="remark["]').forEach(inp => {
                inp.disabled = true;
            });
        });
    }

    // ════════════════════════════════════════════════════════════
    // 9. SMART MODAL: DETAIL SUMUR & ARMADA RIG (DOSSIER)
    // ════════════════════════════════════════════════════════════
    const currentGridBulan = <?= (int)$bulan ?>;
    const currentGridTahun = <?= (int)$tahun ?>;

    function showRigWellsModal(rigId, highlightWellNo = null) {
        const modal = document.getElementById('rigWellsModal');
        const container = document.getElementById('modalWellsContainer');
        const loading = document.getElementById('modalLoadingState');
        if (!modal) return;

        modal.classList.remove('hidden');
        if (loading) loading.classList.remove('hidden');
        
        // Bersihkan kartu sumur sebelumnya
        const existingCards = container.querySelectorAll('.well-job-card, .empty-wells-card');
        existingCards.forEach(c => c.remove());

        fetch(`<?= base_url('npt/get-rig-wells') ?>/${rigId}/${currentGridBulan}/${currentGridTahun}`)
            .then(r => r.json())
            .then(res => {
                if (loading) loading.classList.add('hidden');
                if (res.status !== 'success') {
                    container.innerHTML = `<div class="p-8 text-center text-rose-400 font-bold text-sm">Gagal memuat data sumur.</div>`;
                    return;
                }

                // Perbarui Header Modal
                document.getElementById('modalRigCode').textContent = res.rig.kode;
                document.getElementById('modalRigName').textContent = `(${res.rig.nama_rig})`;
                document.getElementById('modalRigOdr').textContent = res.rig.odr_formatted;
                document.getElementById('modalPeriodeLabel').textContent = `${res.periode.bulan_nama} ${res.periode.tahun}`;
                document.getElementById('modalTargetRev').textContent = res.summary.revenue_target_formatted;
                
                const addBtn = document.getElementById('modalInputDailyBtn');
                if (addBtn) {
                    addBtn.href = `<?= base_url('daily-report/tambah') ?>/${res.rig.id}/${res.periode.bulan}/${res.periode.tahun}`;
                }

                // Perbarui Ribbon KPI
                document.getElementById('kpiTotalWells').textContent = `${res.summary.total_wells} Sumur`;
                document.getElementById('kpiAnnualWells').textContent = `Total Tahun ${res.periode.tahun}: ${res.summary.annual_wells} sumur`;
                document.getElementById('kpiTotalOps').textContent = `${res.summary.total_ops.toFixed(2)} j`;
                document.getElementById('kpiTotalMiru').textContent = `${res.summary.total_miru.toFixed(2)} j`;
                document.getElementById('kpiTotalDt').textContent = `${res.summary.total_dt.toFixed(2)} j`;
                document.getElementById('kpiDtBreakdown').textContent = `Unpaid: ${res.summary.unpaid_dt.toFixed(1)}j | SBWC: ${res.summary.sbwc_dt.toFixed(1)}j`;
                document.getElementById('kpiTotalJam').textContent = `${res.summary.total_jam.toFixed(2)} j`;
                
                if (res.summary.reliability !== null) {
                    document.getElementById('kpiReliability').textContent = `Rel: ${res.summary.reliability}% | Avail: ${res.summary.availability}%`;
                } else {
                    document.getElementById('kpiReliability').textContent = `Total jam tercatat`;
                }

                // Render Kartu Sumur
                if (res.wells.length === 0) {
                    container.innerHTML = `
                        <div class="empty-wells-card p-10 rounded-2xl bg-slate-950/60 border border-dashed border-slate-800 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center mx-auto text-xl">
                                <i class="fa-solid fa-oil-well"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-300">Belum Ada Sumur Tercatat di Bulan Ini</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                Armada <strong class="text-slate-300">${res.rig.kode}</strong> belum memiliki laporan pengerjaan sumur pada periode <strong>${res.periode.bulan_nama} ${res.periode.tahun}</strong>.
                            </p>
                            <a href="<?= base_url('daily-report/tambah') ?>/${res.rig.id}/${res.periode.bulan}/${res.periode.tahun}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition mt-2">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>+ Buat Daily Report Sumur</span>
                            </a>
                        </div>
                    `;
                    return;
                }

                let cardsHtml = '';
                res.wells.forEach(w => {
                    const isHighlighted = (highlightWellNo !== null && parseInt(highlightWellNo, 10) === parseInt(w.no_well, 10));
                    const highlightClass = isHighlighted ? 'ring-2 ring-amber-400 bg-amber-950/20' : 'bg-slate-950/70 hover:bg-slate-950';
                    const isCompleted = (w.status_job || '').toUpperCase().includes('COMPLETE');
                    const statusBadge = isCompleted ? 
                        `<span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>${w.status_job}</span>` :
                        `<span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>${w.status_job}</span>`;

                    // Rincian Breakdown Downtime
                    let dtHtml = '';
                    if (w.dt_breakdown && w.dt_breakdown.length > 0) {
                        dtHtml = `<div class="pt-2.5 border-t border-slate-800/80 flex flex-wrap items-center gap-1.5 text-[10px]">
                            <span class="text-slate-400 font-bold uppercase mr-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation text-amber-400"></i> NPT:</span>`;
                        w.dt_breakdown.forEach(dt => {
                            const isUnp = (dt.tipe === 'UNPAID');
                            dtHtml += `<span class="px-2 py-0.5 rounded-md ${isUnp ? 'bg-rose-950/80 border border-rose-500/40 text-rose-300' : 'bg-amber-950/80 border border-amber-500/40 text-amber-300'} font-mono font-bold">
                                ${dt.nama}: ${parseFloat(dt.jam).toFixed(1)}j
                            </span>`;
                        });
                        dtHtml += `</div>`;
                    }

                    // Remark Operasi
                    let remarkHtml = '';
                    if (w.remark && w.remark.trim() !== '') {
                        remarkHtml = `<div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-xs text-slate-300 italic flex items-start gap-2">
                            <i class="fa-solid fa-comment-dots text-slate-500 mt-0.5 text-[11px] flex-shrink-0"></i>
                            <span>"${w.remark}"</span>
                        </div>`;
                    }

                    cardsHtml += `
                        <div class="well-job-card p-4 rounded-2xl border border-slate-800 transition-all space-y-3 ${highlightClass}" id="wellCard_${w.no_well}">
                            <!-- Top Bar: No Well, Lokasi, Status & Periode -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2.5 border-b border-slate-800">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-2.5 py-1 rounded-xl bg-blue-600 text-white font-mono font-black text-xs shadow-sm">
                                        WELL #${w.no_well}
                                    </span>
                                    <div class="flex items-center gap-1.5 text-white font-bold text-sm">
                                        <i class="fa-solid fa-location-dot text-rose-400 text-xs"></i>
                                        <span>${w.nama_lokasi}</span>
                                    </div>
                                    <span class="text-xs text-slate-400">&bull; Periode: <strong class="text-slate-300 font-sans">${w.tanggal_mulai} &mdash; ${w.tanggal_selesai}</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${statusBadge}
                                </div>
                            </div>

                            <!-- 4 Stat Boxes (MIRU, OPS, DT, Total) -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                <div class="p-2 rounded-xl bg-slate-900/80 border border-slate-800/80 flex flex-col">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Jam Operasi (OPS)</span>
                                    <span class="text-sm font-black font-mono text-sky-300 mt-0.5">${w.ops_jam.toFixed(2)} Jam</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/80 border border-slate-800/80 flex flex-col">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Jam MIRU</span>
                                    <span class="text-sm font-black font-mono text-lime-300 mt-0.5">${w.miru_jam.toFixed(2)} Jam</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/80 border border-slate-800/80 flex flex-col">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Jam Downtime (DT)</span>
                                    <span class="text-sm font-black font-mono ${w.total_dt > 0 ? 'text-amber-400' : 'text-slate-400'} mt-0.5">${w.total_dt.toFixed(2)} Jam</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-900/80 border border-slate-800/80 flex flex-col">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Waktu Sumur</span>
                                    <span class="text-sm font-black font-mono text-emerald-300 mt-0.5">${w.total_jam.toFixed(2)} Jam</span>
                                </div>
                            </div>

                            ${remarkHtml}
                            ${dtHtml}

                            <!-- Action Links -->
                            <div class="flex items-center justify-between pt-1 text-xs">
                                <span class="text-[11px] text-slate-500 font-mono">
                                    <i class="fa-solid fa-calendar-day text-[10px] text-slate-400 mr-1"></i>${w.log_count} hari log harian tercatat
                                </span>
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('daily-report/edit') ?>/${w.id}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-[11px] transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?= base_url('daily-report/detail') ?>/${w.id}" class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-[11px] transition flex items-center gap-1.5 shadow-sm">
                                        <span>Lihat Log Lengkap</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = cardsHtml;

                // Scroll ke sumur yang disorot jika ada
                if (highlightWellNo !== null) {
                    setTimeout(() => {
                        const targetEl = document.getElementById(`wellCard_${highlightWellNo}`);
                        if (targetEl) {
                            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 150);
                }
            })
            .catch(err => {
                if (loading) loading.classList.add('hidden');
                container.innerHTML = `<div class="p-8 text-center text-rose-400 font-bold text-sm">Terjadi kesalahan koneksi saat memuat data sumur.</div>`;
            });
    }

    function closeRigWellsModal() {
        const modal = document.getElementById('rigWellsModal');
        if (modal) modal.classList.add('hidden');
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRigWellsModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && ['harian', 'bulanan', 'tahunan', 'chrono'].includes(tab)) {
            switchNptHubTab(tab);
        }
        onDockDayOrKatChange();
    });
</script>
<?= $this->endSection() ?>
