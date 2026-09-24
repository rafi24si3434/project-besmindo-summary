<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
/* Hilangkan spinner bawaan browser pada input jam matriks NPT agar nominal tidak terpotong */
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
</style>
<div class="ui-screen ui-screen--downtime space-y-5">
    <!-- ═══ Callout: Konteks Halaman ════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-amber-500/8 border border-amber-500/20">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-clock-rotate-left text-amber-400 text-sm"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-300 flex items-center gap-2">
                    <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-[9px] font-extrabold tracking-widest text-amber-400">PUSAT NPT TERPADU</span>
                    NPT (Non-Productive Time) Center
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Pusat rekapitulasi &amp; verifikasi downtime armada rig: <strong class="text-amber-300">Matriks Harian</strong> (per rig), <strong class="text-yellow-300">Rekap Bulanan</strong> (seluruh 18 rig), dan <strong class="text-emerald-300">Rekap Tahunan SYS</strong> (12 bulan). Data tersinkron otomatis timbal balik dengan Daily Report.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="<?= base_url('daily-report/log-harian/' . ($rigId ?? 1) . '/' . $bulan . '/' . $tahun) ?>"
               class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md shadow-cyan-600/20 transition active:scale-95">
                <i class="fa-solid fa-pen-nib text-xs"></i>
                <span>← Input Daily Report (Step 2)</span>
            </a>
        </div>
    </div>

    <!-- ═══ Filter Navigation Bar & Export Dropdown ═══════════════ -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <i class="fa-solid fa-calendar-day text-base"></i>
            </div>
            <div>
                <?php 
                $bulanList = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ]; 
                ?>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span><?= esc($rig['kode']) ?></span>
                    <span class="text-slate-400 font-normal text-xs">&mdash; Periode: <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                </h3>
                <p class="text-xs text-slate-400">Pilih armada rig, bulan, atau tahun untuk melihat dan mengelola data downtime</p>
            </div>
        </div>

        <!-- Toolbar Selector & 1-Click Excel Export -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Selector Rig -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-oil-well text-blue-400 text-xs"></i>
                    <span>Rig:</span>
                </span>
                <select id="selectRig" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" class="bg-slate-900 text-white" <?= $r['id'] == $rigId ? 'selected' : '' ?>><?= esc($r['kode']) ?> - <?= esc($r['nama_rig']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector Bulan -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-blue-400 text-xs"></i>
                    <span>Bulan:</span>
                </span>
                <select id="selectBulan" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($bulanList as $num => $nama): ?>
                        <option value="<?= $num ?>" class="bg-slate-900 text-white" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector Tahun -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-days text-blue-400 text-xs"></i>
                    <span>Tahun:</span>
                </span>
                <select id="selectTahun" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" class="bg-slate-900 text-white" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Tombol Tampilkan Data -->
            <button type="button" onclick="navigateGrid()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 active:scale-95">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tampilkan</span>
            </button>

            <!-- Tombol Cepat: Lihat Detail Sumur Rig Aktif -->
            <button type="button" onclick="showRigWellsModal(<?= $rigId ?>)"
                class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 hover:border-amber-400 text-slate-200 hover:text-white font-bold text-xs shadow-sm transition flex items-center gap-2 active:scale-95 group cursor-pointer"
                title="Lihat daftar sumur & rincian pengerjaan armada <?= esc($rig['kode']) ?>">
                <i class="fa-solid fa-oil-well text-amber-400 group-hover:scale-110 transition-transform text-sm"></i>
                <span>Detail Sumur <?= esc($rig['kode']) ?></span>
                <span class="px-1.5 py-0.2 rounded-md bg-amber-500/20 text-amber-300 font-mono text-[10px] font-black border border-amber-500/30" id="headerWellBadge">
                    <?= count($rigWells ?? []) ?> Sumur
                </span>
            </button>

            <!-- Tombol Input Cepat Hari Ini -->
            <button type="button" onclick="openQuickEntryModal()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-md shadow-amber-500/25 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-bolt text-slate-950 text-xs"></i>
                <span>Input Cepat Hari Ini</span>
            </button>

            <!-- Pemisah Garis -->
            <div class="hidden sm:block h-7 w-[1px] bg-slate-700 mx-1"></div>

            <!-- ═══ DOWNLOAD EXCEL DROPDOWN (1-KLIK FLEKSIBEL) ═══ -->
            <div class="relative inline-block text-left" id="exportDropdownWrapper">
                <button type="button" onclick="toggleExportMenu()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-file-excel text-sm text-emerald-200"></i>
                    <span>Download Excel</span>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-75 ml-0.5"></i>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-72 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl z-50 py-2 divide-y divide-slate-800">
                    <div class="px-3.5 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-download text-emerald-400"></i>
                        <span>Pilih Format Laporan Excel:</span>
                    </div>
                    <div class="py-1">
                        <!-- Opsi 1: Harian Rig Ini -->
                        <a href="<?= base_url("export/npt/{$rigId}/{$bulan}/{$tahun}") ?>" class="flex items-start gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800 hover:text-emerald-300 transition group">
                            <i class="fa-solid fa-calendar-day text-emerald-400 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <div class="font-bold text-white group-hover:text-emerald-300">📥 Matriks Harian Rig Ini (31 Hari)</div>
                                <div class="text-[10px] text-slate-400"><?= esc($rig['kode']) ?> Periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></div>
                            </div>
                        </a>
                        <!-- Opsi 2: Rekap Bulanan Seluruh 18 Rig -->
                        <a href="<?= base_url("export/npt-all/{$bulan}/{$tahun}") ?>" class="flex items-start gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800 hover:text-emerald-300 transition group">
                            <i class="fa-solid fa-table-cells-large text-emerald-400 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <div class="font-bold text-white group-hover:text-emerald-300">📥 Rekap Bulanan Seluruh 18 Rig</div>
                                <div class="text-[10px] text-slate-400">Format Resmi Down Time Rig BMS</div>
                            </div>
                        </a>
                        <!-- Opsi 3: Rekap Tahunan SYS -->
                        <a href="<?= base_url("export/rekap-npt-tahunan/{$tahun}") ?>" class="flex items-start gap-3 px-3.5 py-2.5 text-xs text-slate-200 hover:bg-slate-800 hover:text-emerald-300 transition group">
                            <i class="fa-solid fa-trophy text-emerald-400 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <div class="font-bold text-white group-hover:text-emerald-300">📥 Rekap Tahunan SYS (12 Bulan)</div>
                                <div class="text-[10px] text-slate-400">Akumulasi Lengkap 32 Kolom SYS</div>
                            </div>
                        </a>
                    </div>
                    <div class="pt-1">
                        <!-- Opsi 4: Bundle Package -->
                        <a href="<?= base_url("export/bundle-all/{$bulan}/{$tahun}") ?>" class="flex items-start gap-3 px-3.5 py-2.5 text-xs text-amber-300 hover:bg-slate-800 transition group">
                            <i class="fa-solid fa-box-archive text-amber-400 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <div class="font-bold text-amber-300">📥 Paket Lengkap / Bundle Excel</div>
                                <div class="text-[10px] text-slate-400">Seluruh Sheet (Daily, NPT, Monthly, RAU)</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Segmented Tab Navigation Bar ═════════════════════════ -->
    <div class="flex items-center p-1.5 rounded-2xl bg-slate-950/90 border border-slate-800 shadow-inner gap-1.5 overflow-x-auto custom-scrollbar">
        <!-- Tab 1: Matriks Harian Per Rig -->
        <button type="button" onclick="switchNptHubTab('harian')" id="hubTabBtn_harian"
            class="hub-tab-btn flex-1 min-w-[190px] py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 <?= $activeTab === 'harian' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-850' ?>">
            <i class="fa-solid fa-table-cells text-xs"></i>
            <span>Matriks Harian (Per Rig)</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono <?= $activeTab === 'harian' ? 'bg-amber-600/30 text-slate-950 font-bold' : 'bg-slate-800 text-slate-400' ?>"><?= esc($rig['kode']) ?></span>
        </button>

        <!-- Tab 2: Rekap Bulanan Seluruh 18 Rig -->
        <button type="button" onclick="switchNptHubTab('bulanan')" id="hubTabBtn_bulanan"
            class="hub-tab-btn flex-1 min-w-[210px] py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 <?= $activeTab === 'bulanan' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-850' ?>">
            <i class="fa-solid fa-chart-column text-xs"></i>
            <span>Rekap Bulanan (Seluruh 18 Rig)</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono <?= $activeTab === 'bulanan' ? 'bg-amber-600/30 text-slate-950 font-bold' : 'bg-slate-800 text-slate-400' ?>">18 Unit</span>
        </button>

        <!-- Tab 3: Rekap Tahunan Grand Total & Matriks SYS -->
        <button type="button" onclick="switchNptHubTab('tahunan')" id="hubTabBtn_tahunan"
            class="hub-tab-btn flex-1 min-w-[200px] py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 <?= $activeTab === 'tahunan' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-850' ?>">
            <i class="fa-solid fa-trophy text-xs"></i>
            <span>Rekap Tahunan (Grand Total)</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono <?= $activeTab === 'tahunan' ? 'bg-amber-600/30 text-slate-950 font-bold' : 'bg-slate-800 text-slate-400' ?>"><?= $tahun ?></span>
        </button>

        <!-- Tab 4: Log Kronologis Downtime -->
        <button type="button" onclick="switchNptHubTab('chrono')" id="hubTabBtn_chrono"
            class="hub-tab-btn flex-1 min-w-[190px] py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 <?= $activeTab === 'chrono' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-850' ?>">
            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
            <span>Log Kronologis Downtime</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono <?= $activeTab === 'chrono' ? 'bg-amber-600/30 text-slate-950 font-bold' : 'bg-slate-800 text-slate-400' ?>"><?= count($chronoEvents) ?></span>
        </button>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: MATRIKS HARIAN SPREADSHEET (PER RIG & BULAN)          -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_harian" class="hub-tab-content space-y-5 <?= $activeTab === 'harian' ? '' : 'hidden' ?>">
        <!-- Quick Stat KPI Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-400">Total UNPAID</span>
                    <h4 class="text-2xl font-black text-white mt-1 font-mono"><?= number_format($totalUnpaid, 2) ?> <span class="text-xs font-semibold text-slate-400">Jam</span></h4>
                    <span class="text-[11px] text-rose-400/80 font-medium mt-0.5 block">Potong Tagihan ODR</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 text-xl flex-shrink-0">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Total SBWC</span>
                    <h4 class="text-2xl font-black text-white mt-1 font-mono"><?= number_format($totalSBWC, 2) ?> <span class="text-xs font-semibold text-slate-400">Jam</span></h4>
                    <span class="text-[11px] text-amber-400/80 font-medium mt-0.5 block">Standby With Crew</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl flex-shrink-0">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Total Downtime</span>
                    <h4 class="text-2xl font-black text-white mt-1 font-mono"><?= number_format($totalDT, 2) ?> <span class="text-xs font-semibold text-slate-400">Jam</span></h4>
                    <span class="text-[11px] text-blue-400/80 font-medium mt-0.5 block">Akumulasi Bulan Ini</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center text-blue-400 text-xl flex-shrink-0">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-700 shadow-lg flex items-center justify-between relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-rose-500/5 to-transparent"></div>
                <div class="relative z-10">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Live Financial Impact</span>
                    <h4 class="text-xl font-black text-rose-400 mt-1 font-mono tracking-tight" id="liveFinancialLoss">
                        -Rp <?= number_format(($totalUnpaid / 24) * $rig['odr'], 0, ',', '.') ?>
                    </h4>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-rose-500 text-[10px]"></i>
                        Reliability Drop: <strong id="liveRelDrop" class="text-rose-400"><?= number_format($daysInMonth > 0 ? ($totalUnpaid / ($daysInMonth * 24)) * 100 : 0, 2) ?>%</strong>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-rose-500 text-xl flex-shrink-0 relative z-10 shadow-inner group-hover:scale-110 transition">
                    <i class="fa-solid fa-money-bill-trend-up fa-flip-vertical"></i>
                </div>
            </div>
        </div>

        <!-- ═══ Ringkasan Visual Downtime Bulan Ini (Redesigned & Readable) ═══ -->
        <?php
        $unpaidItems = [];
        $sbwcItems = [];
        $allItems = [];

        foreach ($kategoriList as $k) {
            $kTotal = 0;
            foreach ($existingData as $tglEntries) {
                if (isset($tglEntries[$k['id']])) {
                    foreach ($tglEntries[$k['id']] as $entry) {
                        $kTotal += (float)($entry['jam'] ?? 0);
                    }
                }
            }
            if ($kTotal > 0) {
                $item = [
                    'id'    => $k['id'],
                    'nama'  => $k['nama'],
                    'tipe'  => $k['tipe'],
                    'jam'   => round($kTotal, 2),
                    'pct'   => $totalDT > 0 ? round(($kTotal / $totalDT) * 100, 1) : 0,
                ];
                $allItems[] = $item;
                if ($k['tipe'] === 'UNPAID') {
                    $unpaidItems[] = $item;
                } else {
                    $sbwcItems[] = $item;
                }
            }
        }

        usort($allItems, fn($a, $b) => $b['jam'] <=> $a['jam']);
        usort($unpaidItems, fn($a, $b) => $b['jam'] <=> $a['jam']);
        usort($sbwcItems, fn($a, $b) => $b['jam'] <=> $a['jam']);

        $top3Items = array_slice($allItems, 0, 3);
        $hasChartData = !empty($allItems);
        ?>
        <?php if ($hasChartData): ?>
        <!-- ═══ Compact & Sleek Executive Summary Bar (Simple & Space-Efficient) ═══ -->
        <div class="rounded-2xl bg-slate-900/90 border border-slate-700/70 shadow-sm p-3 sm:p-3.5 space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <!-- Sisi Kiri: Identitas & Total Downtime -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xs flex-shrink-0">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white">Ringkasan Downtime <?= esc($rig['kode']) ?></span>
                            <span class="text-[10px] text-slate-400 font-mono">&bull; <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Total: <strong class="text-white font-mono text-xs"><?= number_format($totalDT, 2) ?> Jam</strong> (<?= count($allItems) ?> kategori downtime aktif)
                        </p>
                    </div>
                </div>

                <!-- Bagian Tengah: Pills UNPAID vs SBWC -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Pill UNPAID -->
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-300">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-[11px] font-bold">UNPAID:</span>
                        <span class="text-xs font-black font-mono text-white"><?= number_format($totalUnpaid, 2) ?> j</span>
                        <span class="text-[10px] text-rose-400 font-mono font-semibold">(<?= $totalDT > 0 ? round(($totalUnpaid / $totalDT) * 100, 1) : 0 ?>%)</span>
                    </div>

                    <!-- Pill SBWC -->
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="text-[11px] font-bold">SBWC:</span>
                        <span class="text-xs font-black font-mono text-white"><?= number_format($totalSBWC, 2) ?> j</span>
                        <span class="text-[10px] text-amber-400 font-mono font-semibold">(<?= $totalDT > 0 ? round(($totalSBWC / $totalDT) * 100, 1) : 0 ?>%)</span>
                    </div>
                </div>

                <!-- Sisi Kanan: Top 3 Mini Tags & Toggle Detail -->
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <?php if (!empty($top3Items)): ?>
                    <div class="hidden sm:flex items-center gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-0.5">Top:</span>
                        <?php foreach ($top3Items as $t): 
                            $isUnp = ($t['tipe'] === 'UNPAID');
                        ?>
                        <span class="px-2 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[11px] text-slate-300 flex items-center gap-1.5" title="<?= esc($t['nama']) ?>: <?= number_format($t['jam'], 2) ?> jam">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isUnp ? 'bg-rose-400' : 'bg-amber-400' ?>"></span>
                            <span class="truncate max-w-[90px]"><?= esc($t['nama']) ?></span>
                            <strong class="font-mono text-white text-[11px]"><?= number_format($t['jam'], 1) ?>j</strong>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <button type="button" 
                            onclick="const d = document.getElementById('nptDetailSummaryDrawer'); d.classList.toggle('hidden'); this.querySelector('i.chevron').classList.toggle('rotate-180');" 
                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-300 flex items-center gap-1.5 transition active:scale-95 border border-slate-700/60 cursor-pointer">
                        <i class="fa-solid fa-list-ul text-[10px] text-slate-400"></i>
                        <span>Rincian</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 chevron transition-transform duration-200"></i>
                    </button>
                </div>
            </div>

            <!-- Drawer Rincian Lengkap (Collapsible, default tersembunyi agar hemat space) -->
            <div id="nptDetailSummaryDrawer" class="hidden pt-3 border-t border-slate-800 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <!-- Rincian UNPAID -->
                    <div class="p-3 rounded-xl bg-slate-950/60 border border-rose-500/20 space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-800">
                            <span class="font-bold text-rose-300 text-xs flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Rincian UNPAID (Potong ODR)</span>
                            </span>
                            <span class="font-mono font-bold text-white text-xs"><?= number_format($totalUnpaid, 2) ?> Jam</span>
                        </div>
                        <?php if (!empty($unpaidItems)): ?>
                            <div class="space-y-1.5">
                                <?php foreach ($unpaidItems as $u): ?>
                                <div class="flex items-center justify-between text-[11px] py-1 px-2 rounded-lg bg-slate-900/80">
                                    <span class="text-slate-300 font-medium"><?= esc($u['nama']) ?></span>
                                    <div class="flex items-center gap-2 font-mono">
                                        <span class="font-bold text-rose-300"><?= number_format($u['jam'], 2) ?> j</span>
                                        <span class="text-slate-500 text-[10px]">(<?= $u['pct'] ?>%)</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-slate-500 text-[11px] italic py-1">Tidak ada downtime UNPAID.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Rincian SBWC -->
                    <div class="p-3 rounded-xl bg-slate-950/60 border border-amber-500/20 space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-800">
                            <span class="font-bold text-amber-300 text-xs flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span>Rincian SBWC (Standby Rate)</span>
                            </span>
                            <span class="font-mono font-bold text-white text-xs"><?= number_format($totalSBWC, 2) ?> Jam</span>
                        </div>
                        <?php if (!empty($sbwcItems)): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                                <?php foreach ($sbwcItems as $s): ?>
                                <div class="flex items-center justify-between text-[11px] py-1 px-2 rounded-lg bg-slate-900/80">
                                    <span class="text-slate-300 truncate max-w-[130px]" title="<?= esc($s['nama']) ?>"><?= esc($s['nama']) ?></span>
                                    <div class="flex items-center gap-1.5 font-mono flex-shrink-0">
                                        <span class="font-bold text-amber-300"><?= number_format($s['jam'], 2) ?> j</span>
                                        <span class="text-slate-500 text-[10px]">(<?= $s['pct'] ?>%)</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-slate-500 text-[11px] italic py-1">Tidak ada downtime SBWC.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- State Tenang & Hemat Ruang Jika Belum Ada Data -->
        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-3 text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-400 text-xs"></i>
                <span>Periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?> belum memiliki catatan downtime (100% Operasi Normal).</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Interactive Spreadsheet Matrix Form (Redesigned: Efficient, Clean, Professional) -->
        <form action="<?= base_url('npt/simpan') ?>" method="POST" id="formNpt">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">
            <input type="hidden" name="payload_json" id="nptPayloadJson">

            <?php
            // Pre-hitung statistik hari awal
            $initDowntimeDays = 0;
            $initUnpaidDays = 0;
            $initOver24Days = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                $dTot = 0;
                $dUnp = 0;
                if (isset($existingData[$dStr])) {
                    foreach ($existingData[$dStr] as $kId => $entries) {
                        foreach ($entries as $en) {
                            $j = (float)($en['jam'] ?? 0);
                            $dTot += $j;
                            if (in_array($kId, [1, 2])) {
                                $dUnp += $j;
                            }
                        }
                    }
                }
                if ($dTot > 0) $initDowntimeDays++;
                if ($dUnp > 0) $initUnpaidDays++;
                if ($dTot > 24) $initOver24Days++;
            }
            $initNormalDays = $daysInMonth - $initDowntimeDays;

            // Pemetaan nama pendek kolom kategori agar tabel rapat & rapi
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

            // Kelompok Kategori:
            // 1. UNPAID: ID 1, 2
            // 2. CUACA & LINGKUNGAN: ID 3, 4, 5
            // 3. 3RD PARTY: ID 10
            // 4. STANDBY OPERASIONAL: Lainnya (ID 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21)
            $unpaidKatIds = [1, 2];
            $weatherKatIds = [3, 4, 5];
            $tpKatIds = [10];
            $opsKatIds = [6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];

            $unpaidColCount = 0;
            $weatherColCount = 0;
            $tpColCount = 0;
            $opsColCount = 0;
            foreach ($kategoriList as $k) {
                if (in_array($k['id'], $unpaidKatIds)) $unpaidColCount++;
                elseif (in_array($k['id'], $weatherKatIds)) $weatherColCount++;
                elseif (in_array($k['id'], $tpKatIds)) $tpColCount++;
                else $opsColCount++;
            }
            ?>

            <div class="rounded-3xl bg-slate-900/95 border border-slate-700/80 shadow-2xl overflow-hidden flex flex-col">
                
                <!-- ═══ Smart Filter & Control Toolbar ═════════════════════ -->
                <div class="p-4 bg-slate-850/90 border-b border-slate-700/80 flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                    
                    <!-- View Mode Selector Pills -->
                    <div class="flex flex-wrap items-center gap-1.5 p-1 rounded-2xl bg-slate-950/80 border border-slate-800">
                        <!-- Mode 1: Tampilkan Semua Hari -->
                        <button type="button" onclick="setTableFilterMode('all')" id="filterBtn_all"
                            class="filter-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-blue-600 text-white shadow-sm shadow-blue-600/30">
                            <i class="fa-solid fa-calendar-days text-xs"></i>
                            <span>Semua Hari</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[10px] bg-white/20 text-white font-mono"><?= $daysInMonth ?></span>
                        </button>

                        <!-- Mode 2: Hanya Hari Ber-Downtime -->
                        <button type="button" onclick="setTableFilterMode('downtime')" id="filterBtn_downtime"
                            class="filter-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-400 hover:text-white hover:bg-slate-800">
                            <i class="fa-solid fa-fire text-amber-400 text-xs"></i>
                            <span>Hanya Hari Ber-Downtime</span>
                            <span id="badgeDowntimeDaysCount" class="px-1.5 py-0.2 rounded-md text-[10px] bg-amber-500/20 text-amber-300 font-mono font-bold">
                                <?= $initDowntimeDays ?> Hari
                            </span>
                        </button>

                        <!-- Mode 3: Hari Ada UNPAID -->
                        <button type="button" onclick="setTableFilterMode('unpaid')" id="filterBtn_unpaid"
                            class="filter-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-400 hover:text-white hover:bg-slate-800">
                            <i class="fa-solid fa-ban text-rose-400 text-xs"></i>
                            <span>Ada UNPAID</span>
                            <span id="badgeUnpaidDaysCount" class="px-1.5 py-0.2 rounded-md text-[10px] bg-rose-500/20 text-rose-300 font-mono font-bold">
                                <?= $initUnpaidDays ?>
                            </span>
                        </button>

                        <!-- Mode 4: Hari Over 24 Jam -->
                        <button type="button" onclick="setTableFilterMode('over24')" id="filterBtn_over24"
                            class="filter-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-400 hover:text-white hover:bg-slate-800">
                            <i class="fa-solid fa-triangle-exclamation text-red-400 text-xs"></i>
                            <span>Over 24 Jam</span>
                            <span id="badgeOver24DaysCount" class="px-1.5 py-0.2 rounded-md text-[10px] bg-slate-800 text-slate-400 font-mono font-bold">
                                <?= $initOver24Days ?>
                            </span>
                        </button>
                    </div>

                    <!-- Search Bar & Summary Chip -->
                    <div class="flex items-center gap-3 flex-1 max-w-lg">
                        <div class="relative w-full">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                            <input type="text" id="nptQuickSearchInput" oninput="onNptTableSearch()"
                                placeholder="Cari tanggal, nomor sumur, atau keyword remark..."
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-8 pr-8 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <button type="button" onclick="clearNptTableSearch()" id="clearSearchBtn" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                        <div class="hidden xl:flex items-center gap-1.5 text-[11px] text-slate-400 flex-shrink-0 bg-slate-950 px-3 py-2 rounded-xl border border-slate-800" id="tableSummaryChip">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span><strong><?= $initDowntimeDays ?></strong> hari ada DT &bull; <strong><?= $initNormalDays ?></strong> normal</span>
                        </div>
                    </div>

                    <!-- Tombol Simpan & Petunjuk Keyboard -->
                    <div class="flex items-center gap-2.5 flex-shrink-0">
                        <span class="text-[11px] text-slate-400 hidden 2xl:inline">
                            <kbd class="px-1.5 py-0.5 rounded bg-slate-950 border border-slate-700 text-[10px] font-mono text-amber-300">Ctrl+S</kbd> simpan cepat
                        </span>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2 active:scale-95">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan &amp; Sinkronkan</span>
                        </button>
                    </div>
                </div>

                <!-- ═══ Interactive Matrix Table Container ══════════════════ -->
                <div class="overflow-x-auto custom-scrollbar relative max-h-[72vh]">
                    <table class="npt-matrix-table w-full text-left text-xs border-collapse border-spacing-0 select-none" id="nptMainTable">
                        
                        <!-- ═══ 2-Tier Hierarchical Header ═════════════════ -->
                        <thead class="bg-slate-950 text-slate-300 font-bold uppercase text-[10px] sticky top-0 z-30 shadow-md">
                            <!-- Tier 1: Group Category Headers -->
                            <tr class="border-b border-slate-800">
                                <!-- Sticky Left Headers -->
                                <th rowspan="2" class="py-2.5 px-2 text-center w-16 min-w-[64px] border-r border-slate-800 sticky left-0 bg-slate-950 z-40">
                                    <span>Tgl</span>
                                </th>
                                <th rowspan="2" class="py-2.5 px-2 text-center min-w-[80px] w-20 border-r border-slate-800 sticky left-16 bg-slate-950 z-40">
                                    <span>Well</span>
                                </th>

                                <!-- Group 1: UNPAID -->
                                <th colspan="<?= $unpaidColCount ?>" class="py-2 px-2 text-center bg-rose-950/70 text-rose-300 border-r border-slate-800 border-b border-rose-800/50">
                                    <div class="flex items-center justify-center gap-1.5 tracking-wider">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>UNPAID &mdash; POTONG REVENUE ODR (<?= $unpaidColCount ?> KOLOM)</span>
                                    </div>
                                </th>

                                <!-- Group 2: CUACA & ACCESS -->
                                <th colspan="<?= $weatherColCount ?>" class="py-2 px-2 text-center bg-amber-950/60 text-amber-300 border-r border-slate-800 border-b border-amber-800/50">
                                    <div class="flex items-center justify-center gap-1.5 tracking-wider">
                                        <i class="fa-solid fa-cloud-showers-heavy text-[10px]"></i>
                                        <span>SBWC &mdash; CUACA &amp; LINGKUNGAN</span>
                                    </div>
                                </th>

                                <!-- Group 3: 3RD PARTY -->
                                <th colspan="<?= $tpColCount ?>" class="py-2 px-2 text-center bg-indigo-950/60 text-indigo-300 border-r border-slate-800 border-b border-indigo-800/50">
                                    <div class="flex items-center justify-center gap-1.5 tracking-wider">
                                        <i class="fa-solid fa-handshake text-[10px]"></i>
                                        <span>SBWC 3RD PARTY</span>
                                    </div>
                                </th>

                                <!-- Group 4: STANDBY OPERASIONAL -->
                                <th colspan="<?= $opsColCount ?>" class="py-2 px-2 text-center bg-slate-900/90 text-cyan-300 border-r border-slate-800 border-b border-slate-700/60">
                                    <div class="flex items-center justify-center gap-1.5 tracking-wider">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                                        <span>SBWC &mdash; STANDBY OPERASIONAL &amp; CLIENT (<?= $opsColCount ?> POS)</span>
                                    </div>
                                </th>

                                <!-- Group 5: REKAP & REMARKS -->
                                <th rowspan="2" class="py-2.5 px-3 text-center min-w-[90px] border-r border-slate-800 bg-slate-900 text-blue-400 font-black">
                                    <span>Total (Jam)</span>
                                </th>
                                <th rowspan="2" class="py-2.5 px-3 text-left min-w-[280px] bg-slate-900/50 text-slate-300 font-black">
                                    <span>Remark / Uraian Detail Kejadian NPT</span>
                                </th>
                            </tr>

                            <!-- Tier 2: Sub-Column Headers (Full Name & Proporsional Rapi) -->
                            <tr class="border-b-2 border-slate-700 text-[10px] font-black tracking-normal">
                                <?php foreach ($kategoriList as $k): 
                                    $isUnpaid = ($k['tipe'] == 'UNPAID');
                                    $isWeather = in_array($k['id'], $weatherKatIds);
                                    $isTp = in_array($k['id'], $tpKatIds);
                                    $is3rdParty = ($k['id'] == 10);

                                    $headerColor = $isUnpaid ? 'text-rose-200 bg-rose-950/50 hover:bg-rose-900/60' : 
                                                   ($isWeather ? 'text-amber-200 bg-amber-950/40 hover:bg-amber-900/50' : 
                                                   ($isTp ? 'text-indigo-200 bg-indigo-950/40 hover:bg-indigo-900/50' : 'text-slate-200 bg-slate-900/90 hover:bg-slate-800'));
                                    $colWidth = $is3rdParty ? 'min-w-[130px] w-[135px]' : 'min-w-[105px] w-[110px]';
                                ?>
                                    <th id="colHeader_<?= $k['id'] ?>"
                                        class="col-header-cell py-2.5 px-1.5 text-center <?= $colWidth ?> border-r border-slate-800/80 transition-colors <?= $headerColor ?>"
                                        title="<?= esc($k['nama']) ?> (<?= $k['tipe'] ?>)">
                                        <span class="block text-[10px] font-extrabold leading-snug whitespace-normal break-words px-0.5"><?= esc($k['nama']) ?></span>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <!-- ═══ Table Body Rows ═══════════════════════════ -->
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

                                // Pre-calculate row sums
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

                                // Row Base Styling:
                                // Normal operational day (0.00 DT) is calm and unobtrusive!
                                // Downtime days POP OUT with thick colored left border!
                                $rowClass = 'npt-matrix-row group transition-colors duration-100 ';
                                if ($isOver24) {
                                    $rowClass .= 'is-over24 border-l-4 border-l-red-500 bg-red-950/25';
                                } elseif ($hasUnpaid) {
                                    $rowClass .= 'has-unpaid border-l-4 border-l-rose-500 bg-rose-950/15';
                                } elseif ($hasDowntime) {
                                    $rowClass .= 'has-sbwc border-l-4 border-l-amber-500 bg-amber-950/10';
                                } else {
                                    $rowClass .= 'is-zero border-l-4 border-l-transparent bg-slate-900/30 opacity-80 hover:opacity-100 hover:bg-slate-800/50';
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
                                    
                                    <!-- 1. Sticky Tgl & Hari -->
                                    <td id="dayCell_<?= $d ?>"
                                        class="py-2.5 px-2 text-center border-r border-slate-800 sticky left-0 w-16 min-w-[64px] bg-slate-950 group-hover:bg-slate-850 z-20 transition-colors">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-black text-xs <?= $isWeekend ? 'text-rose-400' : 'text-white' ?>"><?= $d ?></span>
                                            <span class="text-[9px] font-semibold tracking-wider <?= $isWeekend ? 'text-rose-400/80' : 'text-slate-400' ?>"><?= $namaHari ?></span>
                                        </div>
                                    </td>

                                    <!-- 2. Sticky Well / Job (Klik untuk Rincian Sumur) -->
                                    <td class="py-2.5 px-1 text-center border-r border-slate-800 sticky left-16 min-w-[80px] w-20 bg-slate-950 group-hover:bg-slate-850 z-20 transition-colors">
                                        <?php if ($hasDailySync): ?>
                                            <button type="button" onclick="showRigWellsModal(<?= $rigId ?>, <?= (int)($dailyRow['no_well'] ?? 0) ?>)"
                                                  class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-500/15 hover:bg-emerald-500/30 border border-emerald-500/30 text-emerald-300 font-mono font-bold text-[10px] transition cursor-pointer"
                                                  title="Klik untuk melihat rincian pengerjaan Sumur #<?= esc($dailyRow['no_well'] ?? '-') ?> (Lokasi: <?= esc($dailyRow['nama_lokasi'] ?? '-') ?>)">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                <span>W-<?= esc($dailyRow['no_well'] ?? '-') ?></span>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-500 font-mono">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 3. Kategori Cells (21 Kolom - Lebar Proporsional & Nilai Nominal Jelas) -->
                                    <?php foreach ($kategoriList as $k): 
                                        $val = '';
                                        if (isset($existingData[$dateStr][$k['id']])) {
                                            $firstEntry = reset($existingData[$dateStr][$k['id']]);
                                            if ($firstEntry && (float)$firstEntry['jam'] > 0) {
                                                $val = $firstEntry['jam'];
                                            }
                                        }

                                        $isUnpaid = ($k['tipe'] == 'UNPAID');
                                        $is3rdParty = ($k['id'] == 10);
                                        $hasTpBreakdown = $is3rdParty && ($dayTpSum > 0 || !empty($tpEntries[$dateStr]));
                                        $numVal = (float)$val;
                                        $isFilled = ($numVal > 0);

                                        // Format nilai agar nominal selalu utuh tanpa terpotong
                                        $displayVal = '';
                                        if ($isFilled) {
                                            $displayVal = ($numVal == (int)$numVal) ? (string)(int)$numVal : rtrim(rtrim(number_format($numVal, 2, '.', ''), '0'), '.');
                                        }

                                        // Styling Sel:
                                        // Sel Kosong: tenang, border transparan, baru aktif saat disentuh kursor.
                                        // Sel Berisi: POP-OUT MENONJOL (Rose untuk Unpaid, Amber untuk SBWC)
                                        if ($isFilled) {
                                            if ($isUnpaid) {
                                                $cellInputClass = 'is-filled bg-rose-500/25 border border-rose-500/80 text-rose-200 font-black shadow-sm shadow-rose-950/50';
                                            } else {
                                                $cellInputClass = 'is-filled bg-amber-500/20 border border-amber-500/80 text-amber-300 font-black shadow-sm shadow-amber-950/50';
                                            }
                                        } else {
                                            $cellInputClass = 'is-empty bg-transparent border border-transparent text-slate-400 placeholder-slate-600 hover:border-slate-700/80 hover:bg-slate-800/60 focus:bg-slate-950 focus:text-white focus:border-blue-500';
                                        }

                                        $colCellWidth = $is3rdParty ? 'min-w-[130px] w-[135px]' : 'min-w-[105px] w-[110px]';
                                    ?>
                                        <td class="p-1.5 border-r border-slate-800/70 text-center relative <?= $colCellWidth ?> <?= $isUnpaid ? 'bg-rose-950/5' : '' ?>">
                                            <?php if ($is3rdParty): ?>
                                                <div class="flex items-center gap-1.5 px-0.5">
                                                    <input type="number" step="0.25" min="0" max="24"
                                                           name="npt[<?= $d ?>][<?= $k['id'] ?>]"
                                                           value="<?= $displayVal !== '' ? $displayVal : '' ?>"
                                                           placeholder="&mdash;"
                                                           data-day="<?= $d ?>"
                                                           data-kat-id="<?= $k['id'] ?>"
                                                           data-tipe="<?= $k['tipe'] ?>"
                                                           oninput="onCellInput(this, <?= $d ?>, <?= $k['id'] ?>)"
                                                           onkeydown="onCellKeydown(event, this, <?= $d ?>, <?= $k['id'] ?>)"
                                                           onfocus="highlightCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                           onblur="clearCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                           id="cell_<?= $d ?>_<?= $k['id'] ?>"
                                                           class="flex-1 min-w-[65px] text-center font-mono font-black text-xs py-1.5 px-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-400 transition npt-no-spinner npt-input-<?= $d ?> <?= $cellInputClass ?>">
                                                    <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" 
                                                            id="btnTpToggle_<?= $d ?>"
                                                            title="<?= $hasTpBreakdown ? 'Buka / Minimize Rincian Vendor 3rd Party (' . number_format($dayTpSum, 2) . ' Jam)' : 'Input Rincian Vendor 3rd Party' ?>" 
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer <?= $hasTpBreakdown ? 'bg-indigo-600/80 hover:bg-indigo-500 text-white shadow-sm ring-1 ring-indigo-400/60' : 'bg-slate-800 text-slate-400 hover:text-indigo-300 hover:bg-slate-700' ?>">
                                                        <i class="fa-solid <?= $hasTpBreakdown ? 'fa-handshake' : 'fa-list-check' ?> text-[10px]" id="iconTpToggle_<?= $d ?>"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <input type="number" step="0.25" min="0" max="24"
                                                       name="npt[<?= $d ?>][<?= $k['id'] ?>]"
                                                       value="<?= $displayVal !== '' ? $displayVal : '' ?>"
                                                       placeholder="&mdash;"
                                                       data-day="<?= $d ?>"
                                                       data-kat-id="<?= $k['id'] ?>"
                                                       data-tipe="<?= $k['tipe'] ?>"
                                                       oninput="onCellInput(this, <?= $d ?>, <?= $k['id'] ?>)"
                                                       onkeydown="onCellKeydown(event, this, <?= $d ?>, <?= $k['id'] ?>)"
                                                       onfocus="highlightCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                       onblur="clearCrosshair(<?= $d ?>, <?= $k['id'] ?>)"
                                                       id="cell_<?= $d ?>_<?= $k['id'] ?>"
                                                       class="w-full text-center font-mono font-black text-xs py-1.5 px-1 rounded-lg focus:outline-none focus:ring-1 <?= $isUnpaid ? 'focus:ring-rose-500' : 'focus:ring-blue-500' ?> transition npt-no-spinner npt-input-<?= $d ?> <?= $cellInputClass ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <!-- 4. Total Harian -->
                                    <td class="py-2 px-2 text-center border-r border-slate-800 bg-slate-900/60 font-bold font-mono" id="rowTotal_<?= $d ?>">
                                        <?php if ($rowTotal > 24): ?>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-600 text-white font-black text-xs animate-pulse" title="Total jam melebihi 24 jam!">
                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                <?= number_format($rowTotal, 2) ?>
                                            </span>
                                        <?php elseif ($rowTotal > 0): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md <?= $rowUnpaid > 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40' ?> font-black text-xs">
                                                <?= number_format($rowTotal, 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-600 text-xs">0.00</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Smart Contextual Remark -->
                                    <td class="p-1 bg-slate-900/30">
                                        <input type="text"
                                               name="remark[<?= $d ?>]"
                                               value="<?= esc($dayRemarkUnpaid ?: $dayRemark) ?>"
                                               placeholder="Uraian kejadian, kendala, atau aktivitas NPT..."
                                               oninput="markFormDirty(); updateRowSearchText(<?= $d ?>);"
                                               onfocus="highlightCrosshair(<?= $d ?>, null)"
                                               onblur="clearCrosshair(<?= $d ?>, null)"
                                               id="remark_<?= $d ?>"
                                               class="w-full bg-transparent px-2 py-1 text-xs text-slate-200 placeholder-slate-500/40 rounded-lg hover:bg-slate-700/40 focus:bg-slate-950 focus:outline-none focus:ring-1 focus:ring-blue-400 transition font-sans">
                                    </td>
                                </tr>

                                <!-- ═══ Panel Rincian 3rd Party Inline Drawer (Collapsible & Bounded) ═══ -->
                                <tr id="row3rdParty_<?= $d ?>" class="hidden bg-slate-950/95 border-b-2 border-indigo-500/30">
                                    <td colspan="<?= count($kategoriList) + 4 ?>" class="p-3.5 pl-4 sm:pl-14">
                                        <div class="sticky left-14 max-w-4xl p-4 rounded-2xl bg-slate-900/95 backdrop-blur-md border border-indigo-500/40 shadow-2xl space-y-3">
                                            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5 flex-wrap gap-2">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-xs flex-shrink-0">
                                                        <i class="fa-solid fa-handshake"></i>
                                                    </span>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-bold text-white">Rincian Vendor 3rd Party</span>
                                                            <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-mono text-[10px] font-bold border border-indigo-500/30">Tgl <?= $d ?> <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400">Rincian jam downtime pihak ketiga (terkalkulasi otomatis ke kolom 3rd Party)</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 flex items-center gap-1.5 text-xs text-slate-300">
                                                        <span class="text-[11px] text-slate-400">Total Rincian:</span>
                                                        <strong id="tpSumLabel_<?= $d ?>" class="font-mono text-indigo-300 text-sm font-black"><?= number_format($dayTpSum, 2) ?></strong>
                                                        <span class="text-[10px] text-slate-400">Jam</span>
                                                    </div>
                                                    <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" 
                                                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center gap-1.5 shadow border border-slate-700 hover:border-rose-500 cursor-pointer"
                                                            title="Sembunyikan / Minimize panel rincian vendor ini">
                                                        <i class="fa-solid fa-chevron-up text-[10px]"></i>
                                                        <span>Tutup / Minimize</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-9 gap-2.5 pt-1">
                                                <?php foreach ($thirdParties as $tp): 
                                                    $tpJam = $tpEntries[$dateStr][$tp['id']] ?? '';
                                                ?>
                                                    <div class="bg-slate-950 p-2 rounded-xl border border-slate-800 flex flex-col items-center hover:border-indigo-500/40 transition">
                                                        <span class="text-[10px] font-bold text-slate-400 mb-1 truncate w-full text-center" title="<?= esc($tp['nama']) ?>"><?= esc($tp['nama']) ?></span>
                                                        <input type="number" step="0.25" min="0" max="24"
                                                               name="tp[<?= $d ?>][<?= $tp['id'] ?>]"
                                                               value="<?= $tpJam !== '' ? $tpJam : '' ?>"
                                                               placeholder="0"
                                                               oninput="calc3rdPartyTotal(<?= $d ?>); markFormDirty();"
                                                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-center font-mono font-bold text-xs text-indigo-300 focus:outline-none focus:border-indigo-400 tp-input-<?= $d ?> npt-no-spinner">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <!-- Footer drawer cepat untuk minimize -->
                                            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80 text-[11px] text-slate-400">
                                                <span class="italic"><i class="fa-solid fa-lightbulb text-amber-400 mr-1"></i>Input jam vendor otomatis menjumlahkan nilai pada sel 3rd Party tanggal <?= $d ?></span>
                                                <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer">
                                                    <i class="fa-solid fa-chevron-up text-[9px]"></i>
                                                    <span>Tutup Panel</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endfor; ?>

                            <!-- Baris Kosong Jika Filter Pencarian / Mode Tidak Menemukan Hasil -->
                            <tr id="emptyFilterRow" class="hidden">
                                <td colspan="<?= count($kategoriList) + 5 ?>" class="py-12 text-center">
                                    <div class="max-w-md mx-auto space-y-2">
                                        <i class="fa-solid fa-filter-circle-xmark text-3xl text-slate-600"></i>
                                        <h4 class="text-sm font-bold text-slate-300">Tidak Ada Baris yang Cocok</h4>
                                        <p class="text-xs text-slate-500" id="emptyFilterMessage">
                                            Seluruh hari pada periode ini memiliki downtime 0.00 (Operasi normal 100%).
                                        </p>
                                        <button type="button" onclick="setTableFilterMode('all')" class="mt-2 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 transition">
                                            Tampilkan Semua Hari
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <!-- ═══ Subtotal Footer Matriks Harian ═══════════════ -->
                        <tfoot class="bg-slate-950 font-extrabold text-slate-200 border-t-2 border-slate-700 sticky bottom-0 z-20 shadow-lg">
                            <tr>
                                <td colspan="2" class="py-3 px-3 text-center border-r border-slate-800 sticky left-0 w-36 min-w-[144px] bg-slate-950 font-black uppercase text-[10px] tracking-wider z-30">
                                    TOTAL BULAN INI
                                </td>
                                <?php foreach ($kategoriList as $k): 
                                    $tot = $totalsPerKat[$k['id']] ?? 0;
                                    $isUnpaid = ($k['tipe'] == 'UNPAID');
                                    $is3rdParty = ($k['id'] == 10);
                                    $colFootWidth = $is3rdParty ? 'min-w-[130px] w-[135px]' : 'min-w-[105px] w-[110px]';
                                ?>
                                    <td id="colFooter_<?= $k['id'] ?>"
                                        class="py-3 px-1 text-center border-r border-slate-800 text-xs font-mono <?= $colFootWidth ?> <?= $isUnpaid ? 'text-rose-400 font-black' : 'text-amber-400 font-bold' ?>">
                                        <?= $tot > 0 ? number_format($tot, 2) : '&mdash;' ?>
                                    </td>
                                <?php endforeach; ?>
                                <td id="footerGrandTotal" class="py-3 px-2 text-center border-r border-slate-800 bg-slate-900 text-blue-400 font-mono text-sm font-black min-w-[90px]">
                                    <?= number_format($totalDT, 2) ?>
                                </td>
                                <td id="footerUnpaidTotal" class="py-3 px-3 border-r border-slate-800 text-left text-xs font-sans text-rose-300 font-bold min-w-[190px]">
                                    Unpaid: <?= number_format($totalUnpaid, 2) ?> j
                                </td>
                                <td id="footerSbwcTotal" class="py-3 px-3 text-left text-xs font-sans text-amber-300 font-bold min-w-[220px]">
                                    SBWC: <?= number_format($totalSBWC, 2) ?> j
                                </td>
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
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <!-- Banner Kuning Resmi Identik Monthly Report -->
            <div class="bg-yellow-400 text-slate-950 py-3 px-4 text-center font-black text-sm uppercase tracking-wider border-b-2 border-yellow-500 flex items-center justify-between">
                <span class="w-16"></span>
                <span>DOWN TIME RIG BMS PERIODE <?= strtoupper($bulanList[$bulan] ?? '') ?> <?= $tahun ?></span>
                <span class="text-xs bg-slate-950 text-yellow-300 font-mono px-3 py-1 rounded-full font-bold">18 Unit Rig</span>
            </div>

            <!-- Tabel Komparasi Seluruh 18 Rig -->
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10.5px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2 w-10 border border-slate-700 bg-[#0B1E4A]">NO</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[95px] text-left">NAME RIG</th>
                            <th colspan="3" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66] text-rose-300">UNPAID (HRS)</th>
                            <th colspan="5" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66] text-amber-300">STAND BY WITH CREW — SBWC (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0E2A66] min-w-[90px] text-right font-black text-yellow-300">TOTAL (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[180px] text-left">REMARK UNPAID</th>
                            <th rowspan="2" class="py-2.5 px-2 min-w-[130px] w-36 border border-slate-700 bg-[#0B1E4A] text-center">AKSI</th>
                        </tr>
                        <tr class="text-[9.5px] bg-[#0A1A3F] border-t border-slate-700 text-slate-300">
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-300">Repair Rig</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-300">Personnel</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-400 font-bold bg-rose-950/30">Total Unpaid</th>
                            
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">SWA Rain</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">Dry Road/Pad</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">WO Daylight</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">3rd Party</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-400 font-bold bg-amber-950/30">Total SBWC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if (!empty($monthlyAllRigData['rows'])): 
                            foreach ($monthlyAllRigData['rows'] as $r):
                                $isCurRig = ($r['rig_id'] == $rigId);
                        ?>
                        <tr class="hover:bg-slate-800/80 transition <?= $isCurRig ? 'bg-blue-950/30' : '' ?>">
                            <td class="py-2 px-2 text-center text-xs text-slate-400 border border-slate-800 bg-slate-900/50"><?= $r['no'] ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 font-sans bg-slate-900/50">
                                <button type="button" onclick="showRigWellsModal(<?= $r['rig_id'] ?>)" 
                                    class="text-left font-bold text-white hover:text-amber-400 transition flex items-center justify-between w-full cursor-pointer group"
                                    title="Lihat rincian sumur-sumur yang dikerjakan <?= esc($r['kode']) ?>">
                                    <span class="<?= $isCurRig ? 'text-amber-400 font-extrabold' : 'group-hover:text-amber-300' ?>"><?= esc($r['kode']) ?></span>
                                    <i class="fa-solid fa-circle-info text-[10px] text-slate-500 group-hover:text-amber-400 transition opacity-0 group-hover:opacity-100"></i>
                                </button>
                            </td>
                            
                            <!-- UNPAID: Repair Rig -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['repair'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-600' ?>">
                                <?= $r['repair'] > 0 ? number_format($r['repair'], 2) : '-' ?>
                            </td>
                            <!-- UNPAID: Personnel -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['pers'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-600' ?>">
                                <?= $r['pers'] > 0 ? number_format($r['pers'], 2) : '-' ?>
                            </td>
                            <!-- TOTAL UNPAID -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black bg-rose-950/20 text-rose-300">
                                <?= $r['tot_unpaid'] > 0 ? number_format($r['tot_unpaid'], 2) : '-' ?>
                            </td>

                            <!-- SBWC: Rain -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['rain'] > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $r['rain'] > 0 ? number_format($r['rain'], 2) : '-' ?>
                            </td>
                            <!-- SBWC: Road & Pad -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['road'] > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $r['road'] > 0 ? number_format($r['road'], 2) : '-' ?>
                            </td>
                            <!-- SBWC: Daylight -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['day'] > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $r['day'] > 0 ? number_format($r['day'], 2) : '-' ?>
                            </td>
                            <!-- SBWC: 3rd Party -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $r['tp'] > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $r['tp'] > 0 ? number_format($r['tp'], 2) : '-' ?>
                            </td>
                            <!-- TOTAL SBWC -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black bg-amber-950/20 text-yellow-300">
                                <?= $r['tot_sbwc'] > 0 ? number_format($r['tot_sbwc'], 2) : '-' ?>
                            </td>

                            <!-- TOTAL DOWNTIME -->
                            <td class="py-2 px-3 text-right font-black text-sm text-yellow-300 bg-[#0E2A66]/40 border border-slate-800">
                                <?= $r['total_hrs'] > 0 ? number_format($r['total_hrs'], 2) : '-' ?>
                            </td>

                            <!-- REMARK UNPAID -->
                            <td class="py-2 px-3 text-left text-xs font-sans text-slate-300 border border-slate-800 truncate max-w-[220px]" title="<?= esc($r['rem_unpaid']) ?>">
                                <?= !empty($r['rem_unpaid']) ? esc($r['rem_unpaid']) : '<span class="text-slate-600">-</span>' ?>
                            </td>

                            <!-- AKSI CEPAT: RINCIAN SUMUR & BUKA HARIAN -->
                            <td class="py-1.5 px-2 text-center border border-slate-800">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="showRigWellsModal(<?= $r['rig_id'] ?>)" 
                                        class="px-2 py-1 rounded-lg bg-indigo-950/80 hover:bg-indigo-600 text-indigo-300 hover:text-white font-bold text-[10.5px] border border-indigo-500/30 transition flex items-center gap-1 cursor-pointer"
                                        title="Lihat rincian sumur & pekerjaan <?= esc($r['kode']) ?>">
                                        <i class="fa-solid fa-oil-well text-[10px]"></i>
                                        <span>Sumur</span>
                                    </button>
                                    <button type="button" onclick="jumpToRigHarian(<?= $r['rig_id'] ?>)" 
                                        class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-blue-600 text-slate-300 hover:text-white font-bold text-[10.5px] transition flex items-center gap-1 cursor-pointer"
                                        title="Buka Matriks Harian untuk <?= esc($r['kode']) ?>">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Harian</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <!-- TOTAL FOOTER ROW -->
                    <?php $gt = $monthlyAllRigData['grandTotal'] ?? []; ?>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 bg-[#0B1E4A] font-sans text-amber-300 uppercase">
                                TOTAL DOWNTIME ALL RIG
                            </td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-400"><?= number_format($gt['repair'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-400"><?= number_format($gt['pers'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-300 font-black bg-rose-950/40"><?= number_format($gt['tot_unpaid'] ?? 0, 2) ?></td>
                            
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($gt['rain'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($gt['road'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($gt['day'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($gt['tp'] ?? 0, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300 font-black bg-amber-950/40"><?= number_format($gt['tot_sbwc'] ?? 0, 2) ?></td>

                            <td class="py-2.5 px-3 text-right font-black text-sm text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($gt['total_hrs'] ?? 0, 2) ?>
                            </td>
                            <td colspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A]"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: REKAP TAHUNAN GRAND TOTAL & MATRIKS SYS (12 BULAN)    -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div id="hubTabContent_tahunan" class="hub-tab-content space-y-5 <?= $activeTab === 'tahunan' ? '' : 'hidden' ?>">
        <!-- 4 Grand Total KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-slate-900 border border-rose-500/30 shadow-lg">
                <span class="text-xs font-bold uppercase text-rose-400 block mb-1">Grand Total Unpaid (Repair Rig)</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black font-mono text-rose-400"><?= number_format((float)($annualNptData['grand_total'][2] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-amber-500/30 shadow-lg">
                <span class="text-xs font-bold uppercase text-amber-400 block mb-1">Grand Total Unpaid (Personel)</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black font-mono text-amber-400"><?= number_format((float)($annualNptData['grand_total'][3] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-blue-500/30 shadow-lg">
                <span class="text-xs font-bold uppercase text-blue-400 block mb-1">Grand Total SBWC (Standby)</span>
                <?php 
                    $sbwcTotAnnual = 0;
                    for ($c = 4; $c <= 31; $c++) {
                        $sbwcTotAnnual += (float)($annualNptData['grand_total'][$c] ?? 0);
                    }
                ?>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black font-mono text-blue-400"><?= number_format($sbwcTotAnnual, 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-yellow-500/30 shadow-lg">
                <span class="text-xs font-bold uppercase text-yellow-300 block mb-1">Grand Total NPT Seluruh Armada</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black font-mono text-yellow-300"><?= number_format((float)($annualNptData['grand_total'][32] ?? 0), 2) ?></span>
                    <span class="text-xs text-slate-400">Jam / Tahun</span>
                </div>
            </div>
        </div>

        <!-- Tabel Matriks Lengkap SYS (32+ Kolom) -->
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <div class="bg-[#0B1E4A] text-white p-3.5 border-b border-slate-700 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-bold text-yellow-300 uppercase tracking-wider block">Matriks Rinci Semua Pos Downtime &amp; 10 Vendor 3rd Party (SYS)</span>
                    <span class="text-[11px] text-slate-300">Format Lembar Kerja Excel Asli &mdash; Periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                </div>
                <a href="<?= base_url("export/rekap-npt-tahunan/{$tahun}") ?>" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Export SYS Excel</span>
                </a>
            </div>

            <div class="overflow-x-auto custom-scrollbar max-h-[72vh]">
                <table class="w-full text-center border-collapse text-xs border border-slate-700 select-text">
                    <thead class="sticky top-0 z-20 font-bold text-white shadow-sm bg-[#002060]">
                        <tr class="border-b border-blue-900">
                            <th rowspan="3" class="py-2.5 px-2 border border-blue-900 bg-[#002060] w-10 text-center text-white sticky left-0 z-30">NO</th>
                            <th rowspan="3" class="py-2.5 px-3 border border-blue-900 bg-[#002060] min-w-[110px] text-left text-white sticky left-10 z-30">NAME RIG</th>
                            <th colspan="2" class="py-1.5 px-2 border border-blue-900 bg-[#002060] text-center text-rose-300">UNPAID</th>
                            <th colspan="28" class="py-1.5 px-2 border border-blue-900 bg-[#002060] text-center text-yellow-300">STAND BY WITH CREW ( SBWC )</th>
                            <th rowspan="3" class="py-2.5 px-3 border border-blue-900 bg-[#002060] min-w-[80px] text-right text-yellow-300 font-bold">TOTAL (HRS)</th>
                            <th rowspan="3" class="py-2.5 px-4 border border-blue-900 bg-[#002060] min-w-[200px] text-left text-white">REMARK UNPAID</th>
                        </tr>
                        <tr class="bg-[#002060] border-b border-blue-900 text-[10px]">
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 bg-rose-950/30 text-rose-300 font-bold">Repaire Rig</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 bg-rose-950/30 text-rose-300 font-bold">PERSONEL</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">SWA Rain</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Dry Road</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Dry Pad</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Daylight</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][8]['h3'] ?? 'PT. CHAST') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][9]['h3'] ?? 'WO PDC') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">PHR Well</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">ESP</th>
                            <th colspan="10" class="py-1.5 px-1.5 border-x-2 border-y border-indigo-400 text-center bg-indigo-950 text-amber-300 font-black tracking-wider shadow-inner">
                                <div class="flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-handshake text-xs"></i>
                                    <span>3RD PARTY (10 VENDOR)</span>
                                </div>
                            </th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">UNISAT</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">TRANS</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Foam Unit</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][25]['h3'] ?? 'WO Decision') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][26]['h3'] ?? 'PEMILU') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][27]['h3'] ?? 'WO OMS') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][28]['h3'] ?? 'PT. PCM') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][29]['h3'] ?? 'WO COSL') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][30]['h3'] ?? 'SAFARI') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][31]['h3'] ?? 'IDUL FITRI') ?></th>
                        </tr>
                        <tr class="bg-indigo-950/90 border-b border-indigo-700/60 text-[9px] text-amber-200 font-bold">
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">BHI</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">HLS</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">WI</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">HALCO</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">EJP</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">SCHL</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">MGA</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">SGN</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">BUKAKA</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">PESI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if ($monthData && !empty($monthData['rows'])): 
                            foreach ($monthData['rows'] as $r):
                                $vals = $r['vals'] ?? [];
                        ?>
                        <tr class="hover:bg-slate-800/80 transition group">
                            <td class="py-1.5 px-1.5 border border-slate-800 bg-slate-900/90 text-slate-400 font-mono text-center sticky left-0 z-10 group-hover:bg-slate-800"><?= $r['no'] ?></td>
                            <td class="py-1.5 px-3 border border-slate-800 bg-slate-900/90 font-bold text-white text-left whitespace-nowrap font-sans sticky left-10 z-10 group-hover:bg-slate-800"><?= esc($r['rig']) ?></td>
                            <?php for ($c = 2; $c <= 31; $c++): 
                                $val = $vals[$c] ?? 0;
                                $hasVal = ($val > 0);
                                $isUnpaid = ($c == 2 || $c == 3);
                                $isTp = ($c >= 12 && $c <= 21);
                            ?>
                                <td class="py-1.5 px-1 border border-slate-800 text-center font-mono text-xs <?= $hasVal ? ($isUnpaid ? 'text-rose-400 font-bold bg-rose-950/25' : ($isTp ? 'text-amber-300 font-bold bg-indigo-950/30' : 'text-yellow-300 font-bold bg-amber-950/20')) : ($isTp ? 'text-slate-600 bg-indigo-950/10' : 'text-slate-600') ?>">
                                    <?= $hasVal ? (fmod($val, 1) !== 0.0 ? number_format($val, 2) : (int)$val) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-1.5 px-2 border border-slate-800 font-bold font-mono text-right bg-[#0E2A66]/40 text-yellow-300">
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
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 sticky bottom-0 z-20 shadow-lg">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 uppercase tracking-wider text-xs sticky left-0 bg-[#0B1E4A] z-30 font-black text-yellow-300">
                                TOTAL
                            </td>
                            <?php for ($c = 2; $c <= 31; $c++): 
                                $totVal = $totals[$c] ?? 0;
                                $hasTot = ($totVal > 0);
                                $isUnp = ($c == 2 || $c == 3);
                                $isTp = ($c >= 12 && $c <= 21);
                            ?>
                                <td class="py-2 px-1 border border-slate-700 text-center font-mono text-xs <?= $isUnp ? 'text-rose-300' : ($isTp ? 'text-amber-300 font-black bg-indigo-950/40' : 'text-yellow-300') ?> font-bold">
                                    <?= $hasTot ? (fmod($totVal, 1) !== 0.0 ? number_format($totVal, 2) : (int)$totVal) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-2 px-2 border border-slate-700 font-black font-mono text-right text-yellow-300 bg-[#0E2A66]">
                                <?= number_format((float)($totals[32] ?? 0), 2) ?>
                            </td>
                            <td class="py-2 px-3 border border-slate-700 text-left text-xs text-slate-400">
                                -
                            </td>
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
    <div id="hubTabContent_chrono" class="hub-tab-content space-y-4 <?= $activeTab === 'chrono' ? '' : 'hidden' ?>">
        <!-- Rig Selector Pills -->
        <div class="p-3 bg-slate-900 rounded-2xl border border-slate-800 flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-slate-400 mr-2 flex items-center gap-1">
                <i class="fa-solid fa-oil-well text-amber-400"></i>
                <span>Pilih Armada Rig:</span>
            </span>
            <?php foreach ($rigListNames as $rName): 
                $isActiveChrono = ($selectedChronoRig === $rName || str_replace('#', ' ', $selectedChronoRig) === $rName);
            ?>
                <button type="button" onclick="selectChronoRig('<?= esc($rName) ?>')"
                   class="px-3 py-1 rounded-xl text-xs font-bold transition <?= $isActiveChrono ? 'bg-amber-400 text-slate-950 shadow-md font-black' : 'bg-slate-950 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' ?>">
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

        <!-- KPI Cards Chrono -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Armada Rig</span>
                    <h4 class="text-xl font-black text-amber-400 mt-0.5"><?= esc($selectedChronoRig) ?></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-lg">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Kejadian</span>
                    <h4 class="text-xl font-black text-white mt-0.5"><?= count($chronoEvents) ?> <span class="text-xs text-slate-400 font-normal">Event</span></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center text-blue-400 text-lg">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akumulasi Downtime</span>
                    <h4 class="text-xl font-black text-yellow-300 mt-0.5 font-num"><?= number_format($totalChronoHrs, 2) ?> <span class="text-xs text-slate-400 font-normal">Jam</span></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-yellow-500/15 border border-yellow-500/30 flex items-center justify-center text-yellow-400 text-lg">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="p-3 bg-slate-900 rounded-2xl border border-slate-800 flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm pl-2"></i>
            <input type="text" id="chronoSearchInput" onkeyup="filterChronoTable()" placeholder="Cari berdasarkan tanggal, lokasi sumur, atau uraian kejadian / remark (misal: SWA, shutdown, pump)..." class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none w-full placeholder-slate-500">
            <span id="chronoMatchCount" class="text-[11px] font-bold text-slate-400 whitespace-nowrap pr-2"><?= count($chronoEvents) ?> baris</span>
        </div>

        <!-- Chronological Table -->
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <div class="p-3 bg-[#0B1E4A] border-b border-slate-700 flex items-center justify-between">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-yellow-400"></i>
                    <span>Daftar Kejadian Downtime <?= esc($selectedChronoRig) ?> (Tahun <?= $tahun ?>)</span>
                </h4>
                <span class="text-[11px] text-slate-300 font-medium">100% Sinkron dengan Database &amp; Daily Report</span>
            </div>

            <div class="overflow-x-auto max-h-[70vh] custom-scrollbar">
                <table class="w-full text-left text-xs border-collapse border border-slate-700" id="chronoTable">
                    <thead class="bg-[#0A1A3F] text-white font-extrabold uppercase text-[10px] border-b border-slate-700 sticky top-0 z-10 tracking-wider text-center">
                        <tr>
                            <th class="py-2.5 px-3 w-12 border border-slate-700 bg-[#0A1A3F]">No</th>
                            <th class="py-2.5 px-3 w-28 border border-slate-700 bg-[#0A1A3F]">Tanggal</th>
                            <th class="py-2.5 px-3 w-28 border border-slate-700 bg-[#0A1A3F]">Rig</th>
                            <th class="py-2.5 px-4 w-44 border border-slate-700 bg-[#0A1A3F] text-left">Lokasi / Well</th>
                            <th class="py-2.5 px-4 min-w-[320px] border border-slate-700 bg-[#0A1A3F] text-left">Remark / Uraian Detail Kejadian NPT</th>
                            <th class="py-2.5 px-3 w-28 text-right font-num border border-slate-700 bg-[#0E2A66] text-yellow-300">Downtime (Jam)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200" id="chronoTableBody">
                        <?php if (!empty($chronoEvents)): ?>
                            <?php foreach ($chronoEvents as $idx => $ev): 
                                $rem = $ev['remark'] ?? '';
                                $remUnpaid = $ev['remark_unpaid'] ?? '';
                                $isSwa = stripos($rem, 'SWA') !== false;
                                $isShutdown = stripos($rem, 'shutdown') !== false || stripos($rem, 'standby for step') !== false;
                                $isRepair = stripos($rem, 'repair') !== false;
                            ?>
                            <tr class="hover:bg-slate-800/80 transition-colors chrono-row">
                                <td class="py-2.5 px-3 text-center font-bold text-slate-400 border border-slate-800 font-mono bg-slate-900/50"><?= $ev['no'] ?></td>
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-300 border border-slate-800 font-num whitespace-nowrap"><?= esc($ev['date']) ?></td>
                                <td class="py-2.5 px-3 font-bold text-yellow-400 border border-slate-800 whitespace-nowrap"><?= esc($ev['rig']) ?></td>
                                <td class="py-2.5 px-4 font-bold text-white border border-slate-800"><?= esc($ev['location']) ?></td>
                                <td class="py-2.5 px-4 border border-slate-800 leading-relaxed font-sans">
                                    <span class="<?= $isShutdown ? 'text-rose-300' : ($isSwa ? 'text-amber-300' : ($isRepair ? 'text-cyan-300' : 'text-slate-200')) ?>">
                                        <?= esc($rem) ?>
                                    </span>
                                    <?php if (!empty($remUnpaid)): ?>
                                        <span class="block mt-0.5 text-[10px] text-rose-400 font-semibold italic">
                                            [Unpaid: <?= esc($remUnpaid) ?>]
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-3 text-right font-black font-num text-sm text-yellow-300 bg-[#0E2A66]/30 border border-slate-800">
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
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 sticky bottom-0">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider text-xs border border-slate-700">Total Downtime <?= esc($selectedChronoRig) ?>:</td>
                            <td class="py-3 px-3 text-right font-black font-num text-base text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($totalChronoHrs, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══ FLOATING UNSAVED CHANGES GUARD ═══════════════════════════ -->
<div id="unsavedChangesBanner" class="fixed bottom-6 right-6 z-40 bg-amber-400 text-slate-950 px-5 py-3 rounded-2xl shadow-2xl border-2 border-amber-300 flex items-center gap-4 transition-all duration-300 transform translate-y-28 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-slate-950 text-lg animate-bounce"></i>
        <div>
            <p class="text-xs font-black uppercase tracking-wider">Perubahan Belum Disimpan</p>
            <p class="text-[11px] font-semibold text-slate-900">Jangan lupa simpan sebelum keluar agar data tidak hilang.</p>
        </div>
    </div>
    <button type="button" onclick="submitFormDirectly()" class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-slate-900 text-amber-400 hover:text-amber-300 font-black text-xs shadow-lg transition active:scale-95 flex items-center gap-2">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Simpan Sekarang</span>
    </button>
</div>

<!-- ═══ MODAL INPUT CEPAT HARIAN / QUICK ENTRY ═══════════════════ -->
<div id="quickEntryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden transition-all duration-200">
    <div class="relative w-full max-w-3xl max-h-[90vh] flex flex-col rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-800 bg-slate-850 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl flex-shrink-0">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-white tracking-tight flex items-center gap-2">
                        <span>Input Cepat Harian (Quick Entry)</span>
                        <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold">OPERATOR MODE</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pilih satu tanggal, isi jam downtime yang terjadi, lalu terapkan langsung ke tabel tanpa pusing melihat matriks penuh.
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeQuickEntryModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition" title="Tutup (Esc)">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Toolbar Selector Tanggal & Ringkasan Jam -->
        <div class="p-4 bg-slate-950 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-day text-amber-400"></i>
                    <span>Pilih Tanggal:</span>
                </span>
                <select id="quickDateSelect" onchange="onQuickDateChange()" class="bg-slate-900 border-2 border-amber-500/60 rounded-xl px-3 py-1.5 text-sm font-extrabold text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400 cursor-pointer">
                    <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                        <option value="<?= $d ?>">Tanggal <?= $d ?> (<?= sprintf('%02d', $d) ?>/<?= sprintf('%02d', $bulan) ?>/<?= $tahun ?>)</option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Total Jam Tanggal Ini:</span>
                    <span id="quickDayTotalDisplay" class="text-base font-black font-mono text-blue-400">0.00 Jam</span>
                </div>
                <div id="quickDayStatusBadge" class="px-2.5 py-1 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    <span>Normal (&le; 24 Jam)</span>
                </div>
            </div>
        </div>

        <!-- Body Form Kartu Kategori (Scrollable) -->
        <div class="p-5 overflow-y-auto custom-scrollbar flex-1 space-y-5">
            <!-- Grid Kategori Downtime -->
            <div>
                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-indigo-400"></i>
                    <span>Jam Downtime Berdasarkan Kategori</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach ($kategoriList as $k): ?>
                        <div class="p-3 rounded-2xl bg-slate-800/90 border border-slate-700/80 hover:border-slate-500 transition-all space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-bold text-slate-200 leading-tight block"><?= esc($k['nama']) ?></span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold <?= $k['tipe'] === 'UNPAID' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300' ?>">
                                    <?= $k['tipe'] ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="adjustQuickInput(<?= $k['id'] ?>, -0.5)" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs flex items-center justify-center transition border border-slate-700 active:scale-95">-0.5</button>
                                <input type="number" step="0.25" min="0" max="24"
                                    id="quick_kat_<?= $k['id'] ?>"
                                    data-kat-id="<?= $k['id'] ?>"
                                    oninput="recalcQuickDayTotal()"
                                    placeholder="0"
                                    class="quick-kat-input flex-1 px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-xl font-mono text-center font-bold text-sm text-white focus:border-amber-400 focus:ring-1 focus:ring-amber-400 focus:outline-none">
                                <button type="button" onclick="adjustQuickInput(<?= $k['id'] ?>, 0.5)" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs flex items-center justify-center transition border border-slate-700 active:scale-95">+0.5</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Remarks Khusus Hari Ini (Manual Input) -->
            <div class="pt-2 border-t border-slate-800">
                <div class="p-3.5 rounded-2xl bg-slate-850 border border-slate-700 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-comment-dots text-amber-400"></i>
                        <span>Remark / Uraian Detail Kejadian NPT:</span>
                    </label>
                    <input type="text" id="quick_remark" placeholder="Contoh: Mud pump piston pecah / Hujan lebat..." class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-1 focus:ring-blue-400">
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-850 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3 flex-shrink-0">
            <button type="button" onclick="closeQuickEntryModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition">
                Tutup
            </button>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="applyQuickEntry(false)" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white font-bold text-xs shadow transition active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-check text-emerald-400"></i>
                    <span>Terapkan ke Tabel</span>
                </button>
                <button type="button" onclick="applyQuickEntry(true)" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs shadow-lg shadow-blue-500/25 transition active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Terapkan &amp; Simpan Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal over24Modal removed in favor of Inline Soft Guard -->

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MODAL: DETAIL ARMADA & DAFTAR SUMUR RIG (SMART DOSSIER)     -->
<!-- ════════════════════════════════════════════════════════════ -->
<div id="rigWellsModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 transition-opacity">
    <div class="relative w-full max-w-4xl max-h-[92vh] bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl flex flex-col overflow-hidden text-slate-200 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- 1. Header Modal -->
        <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-base shadow-inner flex-shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base sm:text-lg font-black text-white tracking-tight flex items-center gap-2">
                            <span id="modalRigCode">BMS#07</span>
                            <span class="text-xs font-semibold text-slate-400 font-sans" id="modalRigName">(BMS 07)</span>
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-300 font-mono text-[11px] font-bold" id="modalPeriodeLabel">Agustus 2026</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        ODR: <strong class="text-amber-300 font-mono" id="modalRigOdr">Rp 86.197.000</strong> / hari &bull; Target Rev: <span class="text-emerald-400 font-mono font-semibold" id="modalTargetRev">-</span>
                    </p>
                </div>
            </div>

            <!-- Header Right: Tombol Tambah & Close -->
            <div class="flex items-center gap-2">
                <a id="modalInputDailyBtn" href="#" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>Tambah Sumur</span>
                </a>
                <button type="button" onclick="closeRigWellsModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition cursor-pointer" title="Tutup Modal (Esc)">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- 2. KPI Metrics Ribbon -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-slate-950/70 border-b border-slate-800 text-xs">
            <!-- Total Sumur -->
            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800 flex flex-col">
                <span class="text-[10px] uppercase font-bold text-slate-400">Total Sumur</span>
                <span class="text-base font-black font-mono text-white mt-0.5" id="kpiTotalWells">0</span>
                <span class="text-[10px] text-slate-500" id="kpiAnnualWells">Tahun: 0</span>
            </div>
            <!-- Jam Operasi (OPS) -->
            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800 flex flex-col">
                <span class="text-[10px] uppercase font-bold text-sky-400">Jam Operasi (OPS)</span>
                <span class="text-base font-black font-mono text-sky-300 mt-0.5" id="kpiTotalOps">0.00 j</span>
                <span class="text-[10px] text-slate-500">Produktif</span>
            </div>
            <!-- Jam MIRU -->
            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800 flex flex-col">
                <span class="text-[10px] uppercase font-bold text-lime-400">Jam MIRU / Moving</span>
                <span class="text-base font-black font-mono text-lime-300 mt-0.5" id="kpiTotalMiru">0.00 j</span>
                <span class="text-[10px] text-slate-500">Pindah Lokasi</span>
            </div>
            <!-- Jam Downtime (NPT) -->
            <div class="p-2 rounded-xl bg-slate-900 border border-slate-800 flex flex-col">
                <span class="text-[10px] uppercase font-bold text-amber-400">Total Downtime (DT)</span>
                <span class="text-base font-black font-mono text-amber-300 mt-0.5" id="kpiTotalDt">0.00 j</span>
                <span class="text-[10px] text-slate-500" id="kpiDtBreakdown">Unpaid: 0j | SBWC: 0j</span>
            </div>
            <!-- Total Jam Kerja -->
            <div class="col-span-2 sm:col-span-1 p-2 rounded-xl bg-slate-900 border border-slate-800 flex flex-col">
                <span class="text-[10px] uppercase font-bold text-emerald-400">Total Waktu Tercatat</span>
                <span class="text-base font-black font-mono text-emerald-300 mt-0.5" id="kpiTotalJam">0.00 j</span>
                <span class="text-[10px] text-slate-500" id="kpiReliability">Reliability: -</span>
            </div>
        </div>

        <!-- 3. Daftar Kartu Sumur (Scrollable) -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-5 space-y-3.5" id="modalWellsContainer">
            <!-- Loading Spinner State -->
            <div class="py-12 text-center text-slate-500 space-y-3" id="modalLoadingState">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-amber-400"></i>
                <p class="text-xs font-semibold text-slate-400">Memuat rincian data sumur...</p>
            </div>
        </div>

        <!-- 4. Footer Modal -->
        <div class="p-3.5 bg-slate-950 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-info-circle text-blue-400"></i>
                <span>Data tersinkronisasi otomatis antara Daily Report &amp; Rekap Downtime NPT</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeRigWellsModal()" class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ════════════════════════════════════════════════════════════
    // 1. KONTROLER TAB TERPADU (HARIAN, BULANAN, TAHUNAN, CHRONO)
    // ════════════════════════════════════════════════════════════
    function switchNptHubTab(tabName) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.hub-tab-content').forEach(el => el.classList.add('hidden'));

        // Reset state tombol tab
        document.querySelectorAll('.hub-tab-btn').forEach(btn => {
            btn.classList.remove('bg-amber-500', 'bg-blue-600', 'text-slate-950', 'text-white', 'shadow-md', 'shadow-amber-500/30', 'shadow-blue-600/30');
            btn.classList.add('text-slate-400', 'hover:text-white', 'hover:bg-slate-850');
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.className = 'text-[10px] px-1.5 py-0.5 rounded font-mono bg-slate-800 text-slate-400';
            }
        });

        // Aktifkan tab yang dipilih
        const targetContent = document.getElementById(`hubTabContent_${tabName}`);
        const targetBtn = document.getElementById(`hubTabBtn_${tabName}`);

        if (targetContent) targetContent.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.remove('text-slate-400', 'hover:text-white', 'hover:bg-slate-850');
            targetBtn.classList.add('bg-amber-500', 'text-slate-950', 'shadow-md', 'shadow-amber-500/30');
            const badge = targetBtn.querySelector('span:last-child');
            if (badge) {
                badge.className = 'text-[10px] px-1.5 py-0.5 rounded font-mono bg-amber-600/30 text-slate-950 font-bold';
            }
        }

        // Sinkronkan URL search query tanpa reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
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

        // Reset semua pill buttons
        const modes = ['all', 'downtime', 'unpaid', 'over24'];
        modes.forEach(m => {
            const btn = document.getElementById(`filterBtn_${m}`);
            if (btn) {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
                btn.classList.add('text-slate-400');
            }
        });

        // Highlight tombol terpilih
        const activeBtn = document.getElementById(`filterBtn_${mode}`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-600/30');
            activeBtn.classList.remove('text-slate-400');
        }

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
                // Sembunyikan drawer 3rd party jika baris disembunyikan
                const day = row.getAttribute('data-day');
                const tpRow = document.getElementById(`row3rdParty_${day}`);
                if (tpRow) tpRow.classList.add('hidden');
            }
        });

        // Banner jika tidak ada hasil
        const emptyRow = document.getElementById('emptyFilterRow');
        const emptyMsg = document.getElementById('emptyFilterMessage');
        if (emptyRow) {
            if (visibleCount === 0) {
                emptyRow.classList.remove('hidden');
                if (emptyMsg) {
                    if (searchVal !== '') {
                        emptyMsg.textContent = `Tidak ditemukan baris yang cocok dengan kata kunci "${searchVal}".`;
                    } else if (currentTableFilterMode === 'downtime') {
                        emptyMsg.textContent = 'Bulan ini 100% Zero Downtime! Seluruh hari beroperasi lancar tanpa hambatan.';
                    } else if (currentTableFilterMode === 'unpaid') {
                        emptyMsg.textContent = 'Tidak ada downtime bertipe UNPAID pada periode bulan ini.';
                    } else if (currentTableFilterMode === 'over24') {
                        emptyMsg.textContent = 'Sempurna! Tidak ada tanggal yang melebihi batas 24 jam.';
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

        // Dynamic styling: kosong vs berisi
        if (!isNaN(numVal) && numVal > 0) {
            inputEl.classList.remove('is-empty', 'bg-transparent', 'border-transparent', 'text-slate-400');
            inputEl.classList.add('is-filled', 'font-black');
            if (tipe === 'UNPAID') {
                inputEl.classList.add('bg-rose-500/25', 'border', 'border-rose-500/80', 'text-rose-200', 'shadow-sm', 'shadow-rose-950/50');
            } else {
                inputEl.classList.add('bg-amber-500/20', 'border', 'border-amber-500/80', 'text-amber-300', 'shadow-sm', 'shadow-amber-950/50');
            }
        } else {
            inputEl.classList.remove('is-filled', 'font-black', 'bg-rose-500/25', 'border-rose-500/80', 'text-rose-200', 'bg-amber-500/20', 'border-amber-500/80', 'text-amber-300', 'shadow-sm');
            inputEl.classList.add('is-empty', 'bg-transparent', 'border', 'border-transparent', 'text-slate-400');
        }

        recalcRow(day);
        markFormDirty();
    }

    function onCellKeydown(e, inputEl, day, katId) {
        // Ctrl+S untuk simpan langsung
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            submitFormDirectly();
            return;
        }

        // Enter atau ArrowDown -> Pindah ke baris tanggal berikutnya di kolom yang sama
        if (e.key === 'Enter' || e.key === 'ArrowDown') {
            e.preventDefault();
            const nextCell = document.getElementById(`cell_${day + 1}_${katId}`);
            if (nextCell) {
                nextCell.focus();
                nextCell.select();
            }
        }
        // ArrowUp -> Pindah ke baris tanggal sebelumnya di kolom yang sama
        else if (e.key === 'ArrowUp') {
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

    // showOver24Modal removed in favor of Inline Soft Guard
    // closeOver24ModalAndFocus removed
    // proceedSubmitOver24 removed


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

            // Update row border & contrast styling
            rowEl.classList.remove('is-over24', 'has-unpaid', 'has-sbwc', 'is-zero', 'border-l-red-500', 'border-l-rose-500', 'border-l-amber-500', 'border-l-transparent', 'bg-red-950/25', 'bg-rose-950/15', 'bg-amber-950/10', 'bg-slate-900/30', 'opacity-80');

            if (sum > 24) {
                rowEl.classList.add('is-over24', 'border-l-4', 'border-l-red-500', 'bg-red-950/25');
            } else if (unpaidSum > 0) {
                rowEl.classList.add('has-unpaid', 'border-l-4', 'border-l-rose-500', 'bg-rose-950/15');
            } else if (sum > 0) {
                rowEl.classList.add('has-sbwc', 'border-l-4', 'border-l-amber-500', 'bg-amber-950/10');
            } else {
                rowEl.classList.add('is-zero', 'border-l-4', 'border-l-transparent', 'bg-slate-900/30', 'opacity-80');
            }
        }

        const targetEl = document.getElementById(`rowTotal_${day}`);
        if (targetEl) {
            if (sum > 24) {
                targetEl.innerHTML = `
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-600 text-white font-black text-xs animate-pulse" title="Peringatan: Total jam melebihi 24 jam!">
                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                        ${sum.toFixed(2)}
                    </span>`;
            } else if (sum > 0) {
                const badgeBg = unpaidSum > 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40';
                targetEl.innerHTML = `<span class="inline-flex items-center px-1.5 py-0.5 rounded-md ${badgeBg} font-black text-xs">${sum.toFixed(2)}</span>`;
            } else {
                targetEl.innerHTML = `<span class="text-slate-600 text-xs">0.00</span>`;
            }
        }

        // Perbarui footer subtotal & toolbar badges
        recalcTableSubtotals();
    }

    function recalcTableSubtotals() {
        let grandTotal = 0;
        let grandUnpaid = 0;
        let grandSbwc = 0;
        let downtimeDaysCount = 0;
        let unpaidDaysCount = 0;
        let over24DaysCount = 0;

        // Hitung total kolom per kategori
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

        // Hitung hari dan grand totals
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

        // Perbarui Footer
        const elGrand = document.getElementById('footerGrandTotal');
        const elUnpaid = document.getElementById('footerUnpaidTotal');
        const elSbwc = document.getElementById('footerSbwcTotal');
        if (elGrand) elGrand.textContent = grandTotal.toFixed(2);
        if (elUnpaid) elUnpaid.textContent = `Unpaid: ${grandUnpaid.toFixed(2)} j`;
        if (elSbwc) elSbwc.textContent = `SBWC: ${grandSbwc.toFixed(2)} j`;

        // Perbarui Toolbar Badge
        const bDt = document.getElementById('badgeDowntimeDaysCount');
        const bUnp = document.getElementById('badgeUnpaidDaysCount');
        const bOv24 = document.getElementById('badgeOver24DaysCount');
        const chip = document.getElementById('tableSummaryChip');

        if (bDt) bDt.textContent = `${downtimeDaysCount} Hari`;
        if (bUnp) bUnp.textContent = unpaidDaysCount;
        if (bOv24) bOv24.textContent = over24DaysCount;

        // LIVE FINANCIAL IMPACT UPDATE
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

        if (chip) {
            const normalDays = daysInMonthCount - downtimeDaysCount;
            chip.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-400"></span><span><strong>${downtimeDaysCount}</strong> hari ada DT &bull; <strong>${normalDays}</strong> normal</span>`;
        }
    }

    function toggle3rdPartyRow(day) {
        const row = document.getElementById(`row3rdParty_${day}`);
        if (!row) return;

        const btn = document.getElementById(`btnTpToggle_${day}`);
        const icon = document.getElementById(`iconTpToggle_${day}`);
        const isHidden = row.classList.contains('hidden') || row.style.display === 'none';

        if (isHidden) {
            // Tampilkan / Buka Drawer
            row.classList.remove('hidden');
            row.style.display = '';
            if (btn) {
                btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-400';
                btn.title = 'Tutup / Minimize Rincian 3rd Party';
            }
            if (icon) {
                icon.className = 'fa-solid fa-chevron-up text-[10px]';
            }
        } else {
            // Sembunyikan / Minimize Drawer
            row.classList.add('hidden');
            row.style.display = 'none';

            // Cek apakah ada nilai pada input vendor untuk styling tombol sel
            const tpInputs = document.querySelectorAll(`.tp-input-${day}`);
            let tpSum = 0;
            tpInputs.forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && v > 0) tpSum += v;
            });

            if (btn) {
                if (tpSum > 0) {
                    btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-indigo-600/80 hover:bg-indigo-500 text-white shadow-sm ring-1 ring-indigo-400/60';
                    btn.title = `Buka / Minimize Rincian Vendor 3rd Party (${tpSum.toFixed(2)} Jam)`;
                } else {
                    btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-slate-800 text-slate-400 hover:text-indigo-300 hover:bg-slate-700';
                    btn.title = 'Input Rincian Vendor 3rd Party';
                }
            }
            if (icon) {
                icon.className = tpSum > 0 ? 'fa-solid fa-handshake text-[10px]' : 'fa-solid fa-list-check text-[10px]';
            }
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

        // Sinkronisasi status tombol sel
        const btn = document.getElementById(`btnTpToggle_${day}`);
        const icon = document.getElementById(`iconTpToggle_${day}`);
        const row = document.getElementById(`row3rdParty_${day}`);
        const isRowOpen = row && !row.classList.contains('hidden') && row.style.display !== 'none';

        if (btn) {
            if (isRowOpen) {
                btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-400';
                btn.title = 'Tutup / Minimize Rincian 3rd Party';
            } else if (tpSum > 0) {
                btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-indigo-600/80 hover:bg-indigo-500 text-white shadow-sm ring-1 ring-indigo-400/60';
                btn.title = `Buka / Minimize Rincian Vendor 3rd Party (${tpSum.toFixed(2)} Jam)`;
            } else {
                btn.className = 'w-7 h-7 rounded-lg flex items-center justify-center transition flex-shrink-0 cursor-pointer bg-slate-800 text-slate-400 hover:text-indigo-300 hover:bg-slate-700';
                btn.title = 'Input Rincian Vendor 3rd Party';
            }
        }
        if (icon) {
            if (isRowOpen) {
                icon.className = 'fa-solid fa-chevron-up text-[10px]';
            } else {
                icon.className = tpSum > 0 ? 'fa-solid fa-handshake text-[10px]' : 'fa-solid fa-list-check text-[10px]';
            }
        }
    }

    // ════════════════════════════════════════════════════════════
    // 6. QUICK ENTRY MODAL (INPUT CEPAT HARIAN)
    // ════════════════════════════════════════════════════════════
    function openQuickEntryModal() {
        const modal = document.getElementById('quickEntryModal');
        if (!modal) return;

        const now = new Date();
        const curMonth = now.getMonth() + 1;
        const curYear = now.getFullYear();
        let targetDay = 1;
        if (curMonth === <?= (int)$bulan ?> && curYear === <?= (int)$tahun ?>) {
            targetDay = Math.min(now.getDate(), daysInMonthCount);
        }

        const select = document.getElementById('quickDateSelect');
        if (select) select.value = targetDay;

        loadQuickDayData(targetDay);
        modal.classList.remove('hidden');
    }

    function closeQuickEntryModal() {
        const modal = document.getElementById('quickEntryModal');
        if (modal) modal.classList.add('hidden');
    }

    function onQuickDateChange() {
        const select = document.getElementById('quickDateSelect');
        if (select) {
            loadQuickDayData(parseInt(select.value, 10));
        }
    }

    function loadQuickDayData(day) {
        document.querySelectorAll('.quick-kat-input').forEach(input => {
            const katId = input.getAttribute('data-kat-id');
            const mainCell = document.getElementById(`cell_${day}_${katId}`);
            if (mainCell) {
                input.value = mainCell.value !== '' ? mainCell.value : '';
            } else {
                input.value = '';
            }
        });

        const mainRemark = document.getElementById(
emark_);
        const quickRemark = document.getElementById('quick_remark');
        if (quickRemark && mainRemark) {
            quickRemark.value = mainRemark.value || '';
        }

        recalcQuickDayTotal();
    }

    function adjustQuickInput(katId, delta) {
        const input = document.getElementById(`quick_kat_${katId}`);
        if (!input) return;
        let val = parseFloat(input.value) || 0;
        val = Math.max(0, Math.min(24, Math.round((val + delta) * 100) / 100));
        input.value = val > 0 ? val : '';
        recalcQuickDayTotal();
    }

    function recalcQuickDayTotal() {
        let total = 0;
        document.querySelectorAll('.quick-kat-input').forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) total += val;
        });

        const display = document.getElementById('quickDayTotalDisplay');
        const badge = document.getElementById('quickDayStatusBadge');
        if (display) display.textContent = `${total.toFixed(2)} Jam`;

        if (badge) {
            if (total > 24) {
                badge.className = 'px-2.5 py-1 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold flex items-center gap-1.5 animate-pulse';
                badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-xs"></i><span>Over (Lebih dari 24 Jam)</span>';
            } else {
                badge.className = 'px-2.5 py-1 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-1.5';
                badge.innerHTML = '<i class="fa-solid fa-circle-check text-xs"></i><span>Normal (&le; 24 Jam)</span>';
            }
        }
    }

    function applyQuickEntry(autoSubmit = false) {
        const select = document.getElementById('quickDateSelect');
        if (!select) return;
        const day = parseInt(select.value, 10);

        document.querySelectorAll('.quick-kat-input').forEach(input => {
            const katId = input.getAttribute('data-kat-id');
            const mainCell = document.getElementById(`cell_${day}_${katId}`);
            if (mainCell) {
                mainCell.value = input.value.trim() !== '' ? input.value.trim() : '';
                onCellInput(mainCell, day, katId);
            }
        });

        const quickRemark = document.getElementById('quick_remark');
        const mainRemark = document.getElementById(
emark_);
        if (quickRemark && mainRemark) {
            mainRemark.value = quickRemark.value.trim();
        }

        recalcRow(day);
        markFormDirty();
        closeQuickEntryModal();

        const updatedRow = document.getElementById(`row_${day}`);
        if (updatedRow) {
            updatedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            updatedRow.classList.add('bg-emerald-950/60', 'ring-2', 'ring-emerald-500');
            setTimeout(() => {
                updatedRow.classList.remove('bg-emerald-950/60', 'ring-2', 'ring-emerald-500');
            }, 3000);
        }

        if (autoSubmit) {
            submitFormDirectly();
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

    // Inisialisasi Tab dari URL query param saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && ['harian', 'bulanan', 'tahunan', 'chrono'].includes(tab)) {
            switchNptHubTab(tab);
        }
    });
</script>
<?= $this->endSection() ?>
