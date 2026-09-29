<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--operations space-y-5 pb-12">

    <!-- ═══ 1. HEADER UTAMA (BESAR, JELAS & RAMAH PENGGUNA SENIOR) ═════════════════════ -->
    <div class="bms-bezel-shell">
        <div class="bms-bezel-core px-5 py-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <!-- Rig Info & Title -->
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-blue-600/15 text-blue-400 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bore-hole text-lg"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-blue-400">
                            DAFTAR PEKERJAAN SUMUR (DAILY REPORT)
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-white/[0.06] text-slate-200 border border-white/[0.12]">
                            Periode: <?= [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'][$bulan] ?? '' ?> <?= $tahun ?>
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex flex-wrap items-center gap-2">
                        <span>Rig <?= esc($rig['kode']) ?></span>
                        <span class="text-slate-400 font-semibold text-base">— <?= esc($rig['nama_rig']) ?></span>
                    </h1>
                </div>
            </div>

            <!-- Action Buttons (Ukuran Besar & Teks Jelas) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}") ?>" 
                   class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-sm"></i>
                    <span>+ Tambah Sumur Baru</span>
                </a>
                <a href="<?= base_url('daily-report/log-harian/' . $rigId . '/' . $bulan . '/' . $tahun) ?>"
                   class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-sm"></i>
                    <span>Input Log Harian (24 Jam)</span>
                </a>
                <a href="<?= base_url('npt/' . $rigId . '/' . $bulan . '/' . $tahun) ?>"
                   class="px-4 py-2.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.1] text-slate-200 border border-white/[0.14] font-bold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-table-cells text-amber-400 text-sm"></i>
                    <span>Lihat Tabel NPT</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══ 2. 5 INTERACTIVE KPI DASHBOARD STAT CARDS ════════════════════════ -->
    <?php
        $fmtKm = static fn($v) => (float)$v > 0 ? rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.') : '0';
        $completedCount = 0;
        $progressCount = 0;
        $totalDistance = 0;
        foreach ($reports as $r) {
            if ($r['status_job'] === 'JOB COMPLETED') $completedCount++;
            else $progressCount++;
            $totalDistance += (float)($r['jarak'] ?? 0);
        }
        $completionPct = $wellJobCount > 0 ? round(($completedCount / $wellJobCount) * 100) : 0;
        $totalHoursAll = (float)$totalJam > 0 ? (float)$totalJam : ((float)$totalOps + (float)$totalMiru + (float)$grandTotalDt);
        $opsShare = $totalHoursAll > 0 ? round(((float)$totalOps / $totalHoursAll) * 100) : 0;
        $miruShare = $totalHoursAll > 0 ? round(((float)$totalMiru / $totalHoursAll) * 100) : 0;
        $dtShare = $totalHoursAll > 0 ? round(((float)$grandTotalDt / $totalHoursAll) * 100) : 0;
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        <!-- Card 1: Total Sumur & Progress -->
        <div class="dr-surface-card p-4 rounded-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Sumur</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-extrabold font-num text-white"><?= $wellJobCount ?></span>
                <span class="text-xs text-slate-400 font-semibold">Sumur</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80">
                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                    <span class="text-emerald-400"><?= $completedCount ?> Selesai</span>
                    <span class="text-amber-400"><?= $progressCount ?> Berjalan</span>
                </div>
                <div class="w-full bg-slate-950 rounded-full h-2 overflow-hidden flex border border-slate-800">
                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: <?= $completionPct ?>%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: <?= 100 - $completionPct ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Jam Operasi (OPS) -->
        <div class="dr-surface-card p-4 rounded-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jam Operasi (OPS)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-gears"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-extrabold font-num text-white"><?= number_format($totalOps, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <span>Rata-rata: <strong class="text-slate-200 font-mono"><?= $wellJobCount > 0 ? number_format($totalOps / $wellJobCount, 1) : 0 ?>j/sumur</strong></span>
                <span class="font-mono text-emerald-400 font-bold px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20"><?= $opsShare ?>%</span>
            </div>
        </div>

        <!-- Card 3: Jam Moving & MIRU -->
        <div class="dr-surface-card p-4 rounded-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jam MIRU / Moving</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-extrabold font-num text-white"><?= number_format($totalMiru, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <span>Total Jarak: <strong class="text-slate-200 font-mono"><?= $fmtKm($totalDistance) ?> KM</strong></span>
                <span class="font-mono text-blue-400 font-bold px-1.5 py-0.5 rounded bg-blue-500/10 border border-blue-500/20"><?= $miruShare ?>%</span>
            </div>
        </div>

        <!-- Card 4: Total Downtime -->
        <div class="dr-surface-card p-4 rounded-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Downtime</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-extrabold font-num text-white"><?= number_format($grandTotalDt, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <span>Akumulasi NPT</span>
                <span class="font-mono text-amber-400 font-bold px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/20"><?= $dtShare ?>%</span>
            </div>
        </div>

        <!-- Card 5: Nilai Kontrak ODR -->
        <div class="dr-surface-card p-4 rounded-xl flex flex-col justify-between col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tarif ODR Kontrak</span>
                <div class="w-8 h-8 rounded-lg bg-white/[0.05] text-slate-300 border border-slate-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-xs text-slate-400 font-bold">Rp</span>
                <span class="text-xl font-extrabold font-num text-white"><?= number_format($odr, 0, ',', '.') ?></span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <span>Operating Day Rate</span>
                <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-white/[0.04] border border-slate-700 text-slate-300">24 Jam/Hari</span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. PANEL FILTER & NAVIGASI PERIODE (BESAR, JELAS, MUDAH DIGUNAKAN) ══════ -->
    <style>
        .dr-surface-card {
            background-color: var(--card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }
        .dr-surface-card:hover {
            border-color: var(--border-strong);
        }
        .dr-toolbar-card {
            background-color: var(--card);
            border: 1px solid var(--border-strong);
            box-shadow: var(--shadow-sm);
        }
        .dr-senior-select,
        .dr-senior-input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border-radius: 12px;
            background-color: var(--background);
            border: 1.5px solid var(--border-strong) !important;
            color: var(--foreground) !important;
            font-size: 14px;
            font-weight: 800;
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }
        .dr-senior-select:hover,
        .dr-senior-input:hover {
            border-color: #3b82f6 !important;
        }
        .dr-senior-select:focus,
        .dr-senior-input:focus {
            outline: none;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18) !important;
        }
        .dr-month-step-btn {
            height: 44px;
            padding: 0 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 12px;
            background-color: var(--background);
            border: 1.5px solid var(--border-strong);
            color: var(--foreground);
            font-size: 12.5px;
            font-weight: 800;
            white-space: nowrap;
            transition: all 150ms ease;
            cursor: pointer;
            text-decoration: none;
        }
        .dr-month-step-btn:hover {
            border-color: #2563eb;
            background-color: rgba(37, 99, 235, 0.08);
            color: #2563eb;
        }
        /* Segmented View Switcher */
        .dr-segmented-group {
            background-color: var(--background);
            border: 1.5px solid var(--border-strong);
            padding: 4px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .dr-view-pill {
            padding: 8px 14px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted-foreground);
            background: transparent;
            border: 1px solid transparent;
            transition: all 150ms ease;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .dr-view-pill:hover {
            color: var(--foreground);
        }
        .dr-view-pill.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #1d4ed8 !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        }
        .dr-view-pill.active i {
            color: #ffffff !important;
        }
        /* Filter Chips */
        .dr-chip-btn {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background-color: var(--background);
            border: 1.5px solid var(--border-strong);
            color: var(--muted-foreground);
            transition: all 150ms ease;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .dr-chip-btn:hover {
            color: var(--foreground);
            border-color: var(--muted-foreground);
        }
        .dr-chip-btn.active-all {
            background-color: var(--foreground) !important;
            color: var(--background) !important;
            border-color: var(--foreground) !important;
        }
        .dr-chip-btn.active-all span {
            background-color: rgba(128, 128, 128, 0.25) !important;
            color: var(--background) !important;
        }
        .dr-chip-btn.active-completed {
            background-color: var(--success-bg) !important;
            color: var(--success-fg) !important;
            border-color: var(--success-border) !important;
        }
        .dr-chip-btn.active-progress {
            background-color: var(--warning-bg) !important;
            color: var(--warning-fg) !important;
            border-color: var(--warning-border) !important;
        }
        /* Export Button */
        .dr-export-action {
            height: 44px;
            background-color: #059669;
            border: 1.5px solid #047857;
            color: #ffffff !important;
            transition: all 150ms ease;
        }
        .dr-export-action:hover {
            background-color: #047857;
        }
        /* Table Structural Headers & Footers */
        .dr-table-head th {
            background-color: #131824 !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        html[data-theme="light"] .dr-table-head th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
        }
        .dr-table-foot td {
            background-color: #141a26 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        html[data-theme="light"] .dr-table-foot td {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        /* Clear Labeled Row Action Buttons */
        .dr-row-action-btn {
            height: 32px;
            padding: 0 10px;
            border-radius: 8px;
            background-color: var(--background);
            border: 1px solid var(--border-strong);
            color: var(--foreground);
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 140ms ease;
            text-decoration: none;
        }
        .dr-row-action-btn:hover {
            background-color: var(--muted);
            border-color: #2563eb;
            color: #2563eb;
        }
        .dr-row-action-btn--primary {
            background-color: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.35);
            color: #059669;
        }
        html:not([data-theme="light"]) .dr-row-action-btn--primary {
            color: #34d399;
        }
        .dr-row-action-btn--primary:hover {
            background-color: #059669;
            border-color: #059669;
            color: #ffffff !important;
        }
        .dr-row-action-btn--danger:hover {
            background-color: var(--danger-bg);
            color: var(--danger-fg);
            border-color: var(--danger-border);
        }
    </style>

    <div class="dr-toolbar-card p-5 rounded-2xl space-y-4">
        <?php
            $bulanList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $prevBulan = $bulan > 1 ? $bulan - 1 : 12;
            $prevTahun = $bulan > 1 ? $tahun : $tahun - 1;
            $nextBulan = $bulan < 12 ? $bulan + 1 : 1;
            $nextTahun = $bulan < 12 ? $tahun : $tahun + 1;
        ?>

        <!-- BARIS 1: PILIH RIG, BULAN (DENGAN TOMBOL BULAN LALU/DEPAN), TAHUN & DOWNLOAD EXCEL -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

            <!-- 1. Pilih Unit Rig (4 Kolom) -->
            <div class="md:col-span-4">
                <label for="selectRig" class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-oil-well text-amber-500"></i>
                    <span>1. Pilih Unit Rig</span>
                </label>
                <select id="selectRig" onchange="navigateGrid()" class="dr-senior-select cursor-pointer">
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $rigId ? 'selected' : '' ?>>
                            <?= esc($r['kode']) ?> — <?= esc($r['nama_rig']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Pilih Bulan Operasi + Tombol Ganti Bulan Cepat (4 Kolom) -->
            <div class="md:col-span-4">
                <label for="selectBulan" class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar-check text-blue-500"></i>
                    <span>2. Pilih Bulan Operasi</span>
                </label>
                <div class="flex items-center gap-1.5">
                    <a href="<?= base_url("daily-report/{$rigId}/{$prevBulan}/{$prevTahun}") ?>" 
                       class="dr-month-step-btn" title="Pindah ke Bulan <?= $bulanList[$prevBulan] ?? '' ?> <?= $prevTahun ?>">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span class="hidden sm:inline"><?= substr($bulanList[$prevBulan] ?? '', 0, 3) ?></span>
                    </a>
                    <select id="selectBulan" onchange="navigateGrid()" class="dr-senior-select cursor-pointer flex-1">
                        <?php foreach ($bulanList as $num => $nama): ?>
                            <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?= base_url("daily-report/{$rigId}/{$nextBulan}/{$nextTahun}") ?>" 
                       class="dr-month-step-btn" title="Pindah ke Bulan <?= $bulanList[$nextBulan] ?? '' ?> <?= $nextTahun ?>">
                        <span class="hidden sm:inline"><?= substr($bulanList[$nextBulan] ?? '', 0, 3) ?></span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Pilih Tahun (2 Kolom) -->
            <div class="md:col-span-2">
                <label for="selectTahun" class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-days text-sky-500"></i>
                    <span>3. Tahun</span>
                </label>
                <select id="selectTahun" onchange="navigateGrid()" class="dr-senior-select cursor-pointer font-mono">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- 4. Tombol Download Excel (2 Kolom) -->
            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">
                    <span>4. Unduh Laporan</span>
                </label>
                <button type="button" onclick="openExportModal()"
                   class="dr-export-action w-full px-4 rounded-xl text-sm font-extrabold flex items-center justify-center gap-2 cursor-pointer shadow-md" title="Buka Menu Pilihan Download Excel">
                    <i class="fa-solid fa-file-excel text-base"></i>
                    <span>Download Excel</span>
                </button>
            </div>
        </div>

        <!-- BARIS 2: PENCARIAN SUMUR, FILTER STATUS & PILIHAN TAMPILAN -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3.5 pt-4 border-t border-slate-700/50">
            <!-- Pencarian Cepat -->
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" id="searchWellInput" onkeyup="filterWellsTable()" 
                       placeholder="Cari nomor sumur atau nama lokasi (contoh: 5T-52B)..." 
                       class="dr-senior-input pl-10 pr-9 !font-semibold !text-sm">
                <button type="button" onclick="clearSearch()" id="btnClearSearch"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white text-xs px-2 py-1 rounded-md hidden" title="Hapus pencarian (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Filter Status & Pilihan Mode Tampilan -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Status Quick Filter Chips -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" onclick="setStatusFilter('ALL')" id="btnFilter_ALL" class="dr-chip-btn active-all" title="Tampilkan Semua Sumur">
                        <span>Semua Sumur</span>
                        <span class="px-2 py-0.5 rounded-full bg-black/25 text-xs font-mono font-black"><?= $wellJobCount ?></span>
                    </button>
                    <button type="button" onclick="setStatusFilter('COMPLETED')" id="btnFilter_COMPLETED" class="dr-chip-btn" title="Hanya Sumur yang Sudah Selesai">
                        <i class="fa-solid fa-circle-check text-xs text-emerald-500"></i>
                        <span>Selesai</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-mono font-black"><?= $completedCount ?></span>
                    </button>
                    <button type="button" onclick="setStatusFilter('PROGRESS')" id="btnFilter_PROGRESS" class="dr-chip-btn" title="Sumur yang Sedang Dikerjakan">
                        <i class="fa-solid fa-spinner text-xs text-amber-500"></i>
                        <span>Berjalan</span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-xs font-mono font-black"><?= $progressCount ?></span>
                    </button>
                </div>

                <!-- View Mode Segmented Control -->
                <div class="dr-segmented-group">
                    <button type="button" onclick="switchViewMode('full')" id="btnView_full" class="dr-view-pill active" title="Tampilan Laporan Per Well Persis Template Excel">
                        <i class="fa-solid fa-file-excel text-xs"></i>
                        <span>Laporan Per Well (Excel)</span>
                    </button>
                    <button type="button" onclick="switchViewMode('compact')" id="btnView_compact" class="dr-view-pill" title="Tampilan Tabel Ringkas + Rincian Log">
                        <i class="fa-solid fa-table-list text-xs"></i>
                        <span>Tabel Ringkas</span>
                    </button>
                    <button type="button" onclick="switchViewMode('cards')" id="btnView_cards" class="dr-view-pill" title="Tampilan Kartu Besar per Sumur">
                        <i class="fa-solid fa-grip text-xs"></i>
                        <span>Kartu Sumur</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 1: RINGKASAN PINTAR (COMPACT NO-SCROLL TABLE + ACCORDION LOGS)     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="viewContainer_compact" class="hidden space-y-4">
        <div class="dr-surface-card rounded-2xl overflow-hidden">
            
            <!-- Table Header Banner -->
            <div class="bg-slate-900 border-b border-slate-800 py-3.5 px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-xs bg-blue-500/15 text-blue-400 font-mono px-3 py-1 rounded-lg font-extrabold border border-blue-500/30">
                        <span id="renderedWellCount"><?= count($reports) ?></span> Sumur Terdaftar
                    </span>
                    <span class="font-extrabold text-sm sm:text-base tracking-tight text-white">
                        Daftar Pekerjaan Sumur <span class="text-blue-400"><?= esc($rig['kode']) ?></span> — <span class="text-slate-300"><?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></span>
                    </span>
                </div>
                <!-- Accordion Quick Triggers -->
                <div class="flex items-center gap-2 text-xs font-sans font-bold">
                    <button type="button" onclick="expandAllCompactLogs()" class="px-3 py-1.5 rounded-lg bg-slate-950 hover:bg-slate-800 text-slate-200 border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-angles-down text-blue-400 text-xs"></i>
                        <span>Buka Semua Rincian Harian</span>
                    </button>
                    <button type="button" onclick="collapseAllCompactLogs()" class="px-3 py-1.5 rounded-lg bg-slate-950 hover:bg-slate-800 text-slate-200 border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-angles-up text-slate-400 text-xs"></i>
                        <span>Tutup Rincian</span>
                    </button>
                </div>
            </div>

            <!-- Table Body -->
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="compactWellTable">
                    <thead class="dr-table-head font-extrabold uppercase text-xs border-b border-slate-800 tracking-wider text-center select-none">
                        <tr>
                            <th class="py-3 px-3 w-14 border-r border-slate-800">Well</th>
                            <th class="py-3 px-4 border-r border-slate-800 min-w-[190px] text-left">Nama Lokasi Sumur</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-36 text-center">Jadwal Tanggal</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-24 text-right">Jarak</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-24 text-right">MIRU (Jam)</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-24 text-right">OPS (Jam)</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-24 text-right">SBWC (Jam)</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-24 text-right">UNPAID (Jam)</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-28 text-right font-extrabold">Total Jam</th>
                            <th class="py-3 px-3 border-r border-slate-800 w-32 text-center">Status</th>
                            <th class="py-3 px-3 w-52 text-center">Menu Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num" id="compactTableBody">
                        <?php 
                        $footerTotalSbwc = 0.0;
                        $footerTotalUnpaid = 0.0;
                        if (empty($reports)): ?>
                        <tr id="emptyRowCompact">
                            <td colspan="11" class="py-16 text-center text-slate-500 font-sans">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto text-slate-400 text-xl shadow-inner">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="text-base font-bold text-slate-400">Belum ada pekerjaan sumur untuk periode ini</p>
                                    <p class="text-xs text-slate-500">Klik tombol di bawah untuk mendaftarkan pekerjaan sumur baru.</p>
                                    <a href="<?= base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}") ?>" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-md transition">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span>+ Tambah Sumur Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            foreach ($reports as $idx => $rep): 
                                $repId   = $rep['id'];
                                $dt      = $dtDetails[$repId] ?? [];
                                $logs    = $dailyLogs[$repId] ?? [];
                                $hasLogs = !empty($logs);
                                $isCompleted = ($rep['status_job'] === 'JOB COMPLETED');

                                // Unpaid sum (Rig Downtime & Tool Downtime)
                                $unpaidSum = (float)($dt[1] ?? 0) + (float)($dt[2] ?? 0);
                                // SBWC sum
                                $sbwcSum = 0.0;
                                foreach ($kategoriList as $k) {
                                    if ($k['tipe'] === 'SBWC') {
                                        $sbwcSum += (float)($dt[$k['id']] ?? 0);
                                    }
                                }

                                // Jika ada log harian, pastikan angka SBWC & UNPAID terhitung langsung dari log jika dt belum terisi
                                if ($hasLogs && ($sbwcSum + $unpaidSum) == 0) {
                                    foreach ($logs as $lgRow) {
                                        $unpaidSum += (float)($lgRow['dt_rig'] ?? 0) + (float)($lgRow['dt_tool'] ?? 0);
                                        $sbwcSum   += (float)($lgRow['dt_rain'] ?? 0) + (float)($lgRow['dt_dry_road'] ?? 0)
                                                    + (float)($lgRow['dt_dry_pad'] ?? 0) + (float)($lgRow['dt_phr_op'] ?? 0)
                                                    + (float)($lgRow['dt_trans'] ?? 0) + (float)($lgRow['dt_ce_pe'] ?? 0)
                                                    + (float)($lgRow['dt_3rd_party'] ?? 0) + (float)($lgRow['dt_daylight'] ?? 0)
                                                    + (float)($lgRow['dt_phr_well'] ?? 0) + (float)($lgRow['dt_foam'] ?? 0)
                                                    + (float)($lgRow['dt_shutdown'] ?? 0);
                                    }
                                }

                                if ($sbwcSum == 0 && (float)$rep['total_dt'] > $unpaidSum) {
                                    $sbwcSum = (float)$rep['total_dt'] - $unpaidSum;
                                }

                                $miruRowVal  = (float)$rep['miru_jam'];
                                $opsRowVal   = (float)$rep['ops_jam'];
                                $totalRowVal = $miruRowVal + $opsRowVal + $sbwcSum + $unpaidSum;
                                if ((float)$rep['total_jam'] > $totalRowVal) {
                                    $totalRowVal = (float)$rep['total_jam'];
                                }

                                $footerTotalSbwc   += $sbwcSum;
                                $footerTotalUnpaid += $unpaidSum;
                            ?>
                            <!-- PARENT WELL ROW -->
                            <tr class="hover:bg-slate-850/90 transition-all cursor-pointer well-item-row" 
                                id="well-row-<?= $repId ?>"
                                data-status="<?= $isCompleted ? 'COMPLETED' : 'PROGRESS' ?>"
                                data-wellname="<?= strtolower(esc($rep['nama_lokasi'] ?? '')) ?>"
                                onclick="toggleCompactWell(<?= $repId ?>)">
                                <td class="py-3.5 px-3 text-center border border-slate-800 bg-slate-900/60">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="font-extrabold text-white text-sm">#<?= $rep['no_well'] ?></span>
                                        <?php if ($hasLogs): ?>
                                            <i id="compact-icon-<?= $repId ?>" class="fa-solid fa-chevron-down text-[10px] text-blue-400 transition-transform duration-200"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-white text-sm border border-slate-800 font-sans">
                                    <?php $isSuspendRow = (strtoupper(trim((string)($rep['status_job'] ?? ''))) === 'JOB SUSPEND'); ?>
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 <?= $isCompleted ? 'bg-emerald-500' : ($isSuspendRow ? 'bg-rose-500' : 'bg-amber-500') ?>"></span>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('daily-report/detail/' . $repId) ?>" 
                                               onclick="event.stopPropagation()"
                                               class="group/link inline-flex items-center gap-1.5 text-white hover:text-blue-400 transition-colors text-sm sm:text-base font-extrabold"
                                               title="Buka Halaman Rincian Penuh Sumur <?= esc($rep['nama_lokasi'] ?? '') ?>">
                                                <span><?= esc($rep['nama_lokasi'] ?? 'Sumur #' . $rep['no_well']) ?></span>
                                            </a>
                                            <div class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5 font-sans font-medium">
                                                <span>Sumur #<?= $rep['no_well'] ?></span>
                                                <?php if ($hasLogs): ?>
                                                    <span>·</span>
                                                    <span class="text-emerald-400 font-bold"><?= count($logs) ?> Hari Terisi</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center text-xs text-slate-300 border border-slate-800 font-num">
                                    <div class="font-bold text-white text-xs sm:text-sm">
                                        <?= $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-' ?>
                                    </div>
                                    <div class="text-xs text-slate-400 font-medium mt-0.5">
                                        s/d <?= $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '<span class="text-amber-400 font-bold">Sekarang</span>' ?>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-right text-sm font-bold text-slate-300 border border-slate-800">
                                    <?= $fmtKm($rep['jarak']) ?> KM
                                </td>
                                <td class="py-3.5 px-3 text-right font-bold text-sm <?= $miruRowVal > 0 ? 'text-blue-400' : 'text-slate-500' ?> border border-slate-800">
                                    <?= number_format($miruRowVal, 2) ?>
                                </td>
                                <td class="py-3.5 px-3 text-right font-bold text-sm <?= $opsRowVal > 0 ? 'text-emerald-400' : 'text-slate-500' ?> border border-slate-800">
                                    <?= number_format($opsRowVal, 2) ?>
                                </td>
                                <td class="py-3.5 px-3 text-right text-sm font-bold <?= $sbwcSum > 0 ? 'text-amber-400' : 'text-slate-500' ?> border border-slate-800">
                                    <?= number_format($sbwcSum, 2) ?>
                                </td>
                                <td class="py-3.5 px-3 text-right text-sm font-bold <?= $unpaidSum > 0 ? 'text-rose-400' : 'text-slate-500' ?> border border-slate-800">
                                    <?= number_format($unpaidSum, 2) ?>
                                </td>
                                <td class="py-3.5 px-3 text-right font-extrabold text-base text-white border border-slate-800 bg-slate-950/50">
                                    <?= number_format($totalRowVal, 2) ?>
                                </td>
                                <td class="py-3.5 px-3 text-center border border-slate-800 font-sans whitespace-nowrap">
                                    <?php $isSuspend = (strtoupper(trim((string)($rep['status_job'] ?? ''))) === 'JOB SUSPEND'); ?>
                                    <?php if ($isCompleted): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> SELESAI
                                        </span>
                                    <?php elseif ($isSuspend): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> SUSPEND
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> BERJALAN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-3 text-center border border-slate-800 whitespace-nowrap font-sans" onclick="event.stopPropagation()">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Langsung Input Log Harian untuk Sumur Ini -->
                                        <a href="<?= base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$rep['id']}") ?>"
                                           class="dr-row-action-btn dr-row-action-btn--primary" title="Isi atau Edit Jam Kerja Harian Sumur Ini">
                                            <i class="fa-solid fa-calendar-plus text-xs"></i>
                                            <span>Isi Log</span>
                                        </a>
                                        <!-- Edit Sumur -->
                                        <a href="<?= base_url('daily-report/edit/' . $rep['id']) ?>"
                                           class="dr-row-action-btn" title="Ubah Lokasi / Jadwal Sumur">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                            <span>Edit</span>
                                        </a>
                                        <!-- Detail Lengkap -->
                                        <a href="<?= base_url('daily-report/detail/' . $rep['id']) ?>"
                                           class="dr-row-action-btn" title="Lihat Rincian Lengkap Sumur">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <!-- Hapus Sumur -->
                                        <form action="<?= base_url('daily-report/hapus/' . $rep['id']) ?>" method="POST" onsubmit="return confirm('Hapus data sumur <?= esc($rep['nama_lokasi'] ?? '') ?>?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dr-row-action-btn dr-row-action-btn--danger" title="Hapus Sumur">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- NESTED LOG HARIAN ACCORDION DRAWER -->
                            <?php if ($hasLogs): ?>
                            <tr id="compact-logs-<?= $repId ?>" class="hidden bg-slate-950/70 border-t border-b border-slate-800">
                                <td colspan="11" class="p-3 pl-6">
                                    <div class="rounded-xl bg-slate-900 border border-slate-800 p-3.5 space-y-2.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs pb-2.5 border-b border-slate-800">
                                            <div class="flex items-center gap-2 font-semibold text-white">
                                                <i class="fa-solid fa-timeline text-blue-400"></i>
                                                <span>Rincian Log Harian: <strong><?= esc($rep['nama_lokasi'] ?? '') ?></strong> (<?= count($logs) ?> Hari Operasi)</span>
                                            </div>
                                            <a href="<?= base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$rep['id']}") ?>" class="text-[11px] font-semibold text-blue-400 hover:underline flex items-center gap-1.5">
                                                <span>+ Tambah / Edit Log Sumur Ini</span>
                                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>

                                        <table class="w-full text-xs text-left border-collapse border border-slate-800 font-num">
                                            <thead class="dr-table-head text-[10px] uppercase font-bold text-center border-b border-slate-800">
                                                <tr>
                                                    <th class="py-1.5 px-2 w-10 border border-slate-800">Hari</th>
                                                    <th class="py-1.5 px-3 border border-slate-800 w-28 text-center">Tanggal</th>
                                                    <th class="py-1.5 px-3 border border-slate-800 w-36 text-left">Tempat / Lokasi</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right">MIRU</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right">OPS</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center">SBWC Rain</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center">SBWC Road/Pad</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center">SBWC Daylight</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center">SBWC 3rd Party</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center">UNPAID Rig</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right font-bold">Total DT</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right font-bold">Total Jam</th>
                                                    <th class="py-1.5 px-3 border border-slate-800 text-left font-sans min-w-[200px]">Remark Uraian</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-800 text-slate-300 text-[11px]">
                                                <?php foreach ($logs as $li => $lg): 
                                                    $lgDt = (float)$lg['total_dt'];
                                                    $lgHrs = (float)$lg['total_hrs'];
                                                ?>
                                                <tr class="hover:bg-slate-800/40 transition">
                                                    <td class="py-1.5 px-2 text-center text-slate-400 font-mono border border-slate-800"><?= $li + 1 ?></td>
                                                    <td class="py-1.5 px-3 text-center text-white font-sans border border-slate-800 font-semibold"><?= date('d/m/Y', strtotime($lg['tanggal'])) ?></td>
                                                    <td class="py-1.5 px-3 text-left text-slate-200 font-sans border border-slate-800 font-medium">
                                                        <?= esc($rep['nama_lokasi'] ?? '-') ?>
                                                    </td>
                                                    <td class="py-1.5 px-2 text-right text-blue-400 border border-slate-800"><?= (float)$lg['miru_jam'] > 0 ? number_format((float)$lg['miru_jam'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right text-emerald-400 border border-slate-800"><?= (float)$lg['ops_jam'] > 0 ? number_format((float)$lg['ops_jam'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_rain'] > 0 ? 'text-amber-400 font-semibold' : 'text-slate-500' ?>"><?= (float)$lg['dt_rain'] > 0 ? number_format((float)$lg['dt_rain'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= ((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad']) > 0 ? 'text-amber-400 font-semibold' : 'text-slate-500' ?>"><?= ((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad']) > 0 ? number_format((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_daylight'] > 0 ? 'text-amber-400 font-semibold' : 'text-slate-500' ?>"><?= (float)$lg['dt_daylight'] > 0 ? number_format((float)$lg['dt_daylight'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_3rd_party'] > 0 ? 'text-amber-400 font-semibold' : 'text-slate-500' ?>"><?= (float)$lg['dt_3rd_party'] > 0 ? number_format((float)$lg['dt_3rd_party'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_rig'] > 0 ? 'text-rose-400 font-semibold' : 'text-slate-500' ?>"><?= (float)$lg['dt_rig'] > 0 ? number_format((float)$lg['dt_rig'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right font-semibold text-amber-400 border border-slate-800"><?= $lgDt > 0 ? number_format($lgDt, 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right font-bold text-white border border-slate-800"><?= $lgHrs > 0 ? number_format($lgHrs, 2) : '-' ?></td>
                                                    <td class="py-1.5 px-3 text-left font-sans text-slate-300 border border-slate-800 truncate max-w-xs" title="<?= esc($lg['remark_npt'] ?? '') ?>">
                                                        <?= esc($lg['remark_npt'] ?: '-') ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <!-- TOTAL FOOTER -->
                    <tfoot class="dr-table-foot font-bold text-white border-t-2 border-slate-700 text-xs text-center">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-left font-sans font-bold uppercase text-slate-300 border border-slate-800">
                                Total Akumulasi Operasi (<span id="footerWellCount"><?= count($reports) ?></span> Sumur)
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-blue-400 border border-slate-800"><?= number_format($totalMiru, 2) ?></td>
                            <td class="py-3 px-3 text-right font-bold text-emerald-400 border border-slate-800"><?= number_format($totalOps, 2) ?></td>
                            <td class="py-3 px-3 text-right font-bold text-amber-400 border border-slate-800" title="Total SBWC"><?= number_format($footerTotalSbwc, 2) ?></td>
                            <td class="py-3 px-3 text-right font-bold text-rose-400 border border-slate-800" title="Total Unpaid"><?= number_format($footerTotalUnpaid, 2) ?></td>
                            <td class="py-3 px-3 text-right font-extrabold text-sm text-white border border-slate-800"><?= number_format($totalJam, 2) ?></td>
                            <td colspan="2" class="py-3 px-3 text-center border border-slate-800 text-slate-300 font-sans text-xs">
                                Total DT: <strong class="text-amber-400 font-mono"><?= number_format($footerTotalSbwc + $footerTotalUnpaid, 2) ?>h</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 2: KARTU VISUAL SUMUR (INTERACTIVE GRID CARDS)                     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="viewContainer_cards" class="hidden space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="cardsGrid">
            <?php foreach ($reports as $rep): 
                $dt = $dtDetails[$rep['id']] ?? [];
                $logs = $dailyLogs[$rep['id']] ?? [];
                $isComp = ($rep['status_job'] === 'JOB COMPLETED');
                $wOps = (float)$rep['ops_jam'];
                $wMiru = (float)$rep['miru_jam'];
                $wUnpaid = (float)($dt[1] ?? 0) + (float)($dt[2] ?? 0);
                $wSbwc = 0.0;
                foreach ($kategoriList as $k) {
                    if ($k['tipe'] === 'SBWC') {
                        $wSbwc += (float)($dt[$k['id']] ?? 0);
                    }
                }
                if (!empty($logs) && ($wSbwc + $wUnpaid) == 0) {
                    foreach ($logs as $lgRow) {
                        $wUnpaid += (float)($lgRow['dt_rig'] ?? 0) + (float)($lgRow['dt_tool'] ?? 0);
                        $wSbwc   += (float)($lgRow['dt_rain'] ?? 0) + (float)($lgRow['dt_dry_road'] ?? 0)
                                  + (float)($lgRow['dt_dry_pad'] ?? 0) + (float)($lgRow['dt_phr_op'] ?? 0)
                                  + (float)($lgRow['dt_trans'] ?? 0) + (float)($lgRow['dt_ce_pe'] ?? 0)
                                  + (float)($lgRow['dt_3rd_party'] ?? 0) + (float)($lgRow['dt_daylight'] ?? 0)
                                  + (float)($lgRow['dt_phr_well'] ?? 0) + (float)($lgRow['dt_foam'] ?? 0)
                                  + (float)($lgRow['dt_shutdown'] ?? 0);
                    }
                }
                $wDt = ($wSbwc + $wUnpaid) > 0 ? ($wSbwc + $wUnpaid) : (float)$rep['total_dt'];
                $wTotal = (float)$rep['total_jam'] > 0 ? (float)$rep['total_jam'] : ($wOps + $wMiru + $wDt);
                $wOpsPct = $wTotal > 0 ? round(($wOps / $wTotal) * 100) : 0;
                $isSuspendCard = (strtoupper(trim((string)($rep['status_job'] ?? ''))) === 'JOB SUSPEND');
                $cardStatusKey = $isComp ? 'COMPLETED' : ($isSuspendCard ? 'SUSPEND' : 'PROGRESS');
            ?>
            <div class="p-5 rounded-3xl bg-slate-900 border border-slate-700/80 shadow-xl space-y-3.5 relative overflow-hidden group hover:border-blue-500/60 hover:shadow-2xl transition-all duration-300 well-card-item" 
                 data-status="<?= $cardStatusKey ?>"
                 data-wellname="<?= strtolower(esc($rep['nama_lokasi'] ?? '')) ?>">
                
                <!-- Card Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg bg-blue-600/20 text-blue-400 text-[11px] font-black font-mono border border-blue-500/30">
                                WELL #<?= $rep['no_well'] ?>
                            </span>
                            <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                                <i class="fa-regular fa-calendar-check text-slate-500"></i>
                                <?= $rep['tanggal_mulai'] ? date('d M Y', strtotime($rep['tanggal_mulai'])) : '-' ?>
                            </span>
                        </div>
                        <h3 class="text-base font-black text-white mt-1.5 group-hover:text-yellow-300 transition-colors">
                            <?= esc($rep['nama_lokasi'] ?? 'Lokasi Sumur') ?>
                        </h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10.5px] font-black <?= $isComp ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : ($isSuspendCard ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'bg-amber-500/15 text-amber-400 border border-amber-500/30 animate-pulse') ?>">
                        <?= $isComp ? 'COMPLETED' : ($isSuspendCard ? 'JOB SUSPEND' : 'ON PROGRESS') ?>
                    </span>
                </div>

                <!-- Visual Operational Distribution Bar -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold">
                        <span>Porsi Efisiensi Operasi</span>
                        <span class="text-emerald-400 font-mono"><?= $wOpsPct ?>% OPS</span>
                    </div>
                    <div class="w-full bg-slate-950 rounded-full h-2 overflow-hidden flex border border-slate-800">
                        <div class="bg-lime-500 h-full" style="width: <?= $wTotal > 0 ? round(($wMiru / $wTotal)*100) : 0 ?>%" title="MIRU: <?= number_format($wMiru, 1) ?>h"></div>
                        <div class="bg-emerald-500 h-full" style="width: <?= $wOpsPct ?>%" title="OPS: <?= number_format($wOps, 1) ?>h"></div>
                        <div class="bg-amber-500 h-full" style="width: <?= $wTotal > 0 ? round(($wDt / $wTotal)*100) : 0 ?>%" title="DT: <?= number_format($wDt, 1) ?>h"></div>
                    </div>
                </div>

                <!-- Stats 4-Grid -->
                <div class="grid grid-cols-4 gap-1.5 p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-center font-num">
                    <div>
                        <span class="text-[9px] font-bold uppercase text-slate-500 block">MIRU</span>
                        <span class="text-xs font-black text-lime-400"><?= number_format($wMiru, 2) ?>h</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold uppercase text-slate-500 block">OPS</span>
                        <span class="text-xs font-black text-lime-300"><?= number_format($wOps, 2) ?>h</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold uppercase text-slate-500 block">SBWC</span>
                        <span class="text-xs font-black text-yellow-300"><?= number_format($wSbwc, 2) ?>h</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold uppercase text-slate-500 block">UNPAID</span>
                        <span class="text-xs font-black text-rose-400"><?= number_format($wUnpaid, 2) ?>h</span>
                    </div>
                </div>

                <!-- Remark Note -->
                <?php if (!empty($rep['remark'])): ?>
                <div class="text-xs text-slate-300 bg-slate-850/60 p-2.5 rounded-xl border border-slate-800 leading-relaxed font-sans">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Catatan Operasi:</span>
                    <p class="truncate" title="<?= esc($rep['remark']) ?>"><?= esc($rep['remark']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Card Footer Actions -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs">
                    <span class="text-[11px] text-slate-400 font-mono font-bold">
                        <i class="fa-solid fa-calendar-day mr-1 text-cyan-400"></i> <?= count($logs) ?> Hari Log · DT: <strong class="text-yellow-300"><?= number_format($wDt, 2) ?>h</strong>
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openWellQuickLook(<?= htmlspecialchars(json_encode([
                            'no_well' => $rep['no_well'],
                            'nama_lokasi' => $rep['nama_lokasi'] ?? '',
                            'tanggal_mulai' => $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-',
                            'tanggal_selesai' => $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '-',
                            'jarak' => (float)$rep['jarak'],
                            'miru_jam' => $wMiru,
                            'ops_jam' => $wOps,
                            'sbwc_jam' => $wSbwc,
                            'unpaid_jam' => $wUnpaid,
                            'total_jam' => $wTotal,
                            'status_job' => $rep['status_job'],
                            'remark' => $rep['remark'] ?? '',
                            'edit_url' => base_url('daily-report/edit/' . $rep['id']),
                            'log_count' => count($logs)
                        ]), ENT_QUOTES, 'UTF-8') ?>)" class="px-2.5 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-bold transition">
                            Detail
                        </button>
                        <a href="<?= base_url('daily-report/edit/' . $rep['id']) ?>" class="px-2.5 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 font-bold transition">
                            Edit
                        </a>
                        <a href="<?= base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-2.5 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-bold transition shadow-sm">
                            Log Harian
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 3: SUMMARY REPORT PER WELL (REPLIKA PERSIS TEMPLATE EXCEL SYS)      -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <?php
        $rateOdr  = (float)($odr ?? $rig['odr'] ?? 0);
        $rateOps  = $rateOdr > 0 ? round($rateOdr / 24) : 0;
        $rateMiru = $rateOdr > 0 ? round(($rateOdr / 24) * 0.75) : 0;
        $rateSbwc = $rateOdr > 0 ? round(($rateOdr / 24) * 0.65) : 0;
        $fmtRpExcel = static fn($v) => $v > 0 ? number_format((float)$v, 0, ',', '.') : '-';
        $fmtNumComma = static fn($v) => number_format((float)$v, 2, ',', '.');
        $fmtBlankOrComma = static fn($v) => (float)$v > 0 ? number_format((float)$v, 2, ',', '.') : '';
        $fmtJarakExcel = static function($v) {
            $val = (float)$v;
            if ($val <= 0) return '';
            $formatted = rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',');
            return $formatted . ' KM';
        };
        $fmtDateExcel = static function($dStr) {
            if (empty($dStr)) return '-';
            return date('d-M-y', strtotime($dStr));
        };
    ?>
    <style>
        .xl-sheet-wrap {
            background: #ffffff !important;
            color: #000000 !important;
            border: 2px solid #1e293b;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
            overflow: hidden;
        }
        .xl-report-table {
            width: 100%;
            border-collapse: collapse !important;
            font-family: 'Calibri', 'Plus Jakarta Sans', Arial, sans-serif !important;
            font-size: 12px !important;
            background: #ffffff !important;
            color: #000000 !important;
        }
        .xl-report-table th,
        .xl-report-table td {
            border: 1px solid #000000 !important;
            padding: 4px 6px !important;
            line-height: 1.25 !important;
            color: #000000 !important;
            vertical-align: middle;
        }
        .xl-title-cell {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            text-align: center !important;
            padding: 8px 12px !important;
            letter-spacing: 0.02em;
        }
        .xl-bg-sky     { background-color: #92CDDC !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-green   { background-color: #92D050 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-yellow  { background-color: #FFFF00 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-orange  { background-color: #FFC000 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-red     { background-color: #FF0000 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-peach   { background-color: #F4B084 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-sage    { background-color: #A9D08E !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-pink    { background-color: #E6B8B7 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-dist    { background-color: #DDD9C4 !important; font-weight: 700 !important; text-align: center; }
        .xl-bg-sub     { background-color: #D9E1F2 !important; font-weight: 700 !important; }
        .xl-bg-grand   { background-color: #FFFF00 !important; font-weight: 800 !important; }
        .xl-text-red   { color: #FF0000 !important; font-weight: 700 !important; }
        .xl-row-hover:hover td.xl-white-cell {
            background-color: #f0f9ff !important;
        }
    </style>

    <div id="viewContainer_full" class="space-y-4">
        <div class="xl-sheet-wrap">
            <!-- Top Action Ribbon above Excel Sheet -->
            <div class="px-5 py-3 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-700">
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-extrabold flex items-center gap-1.5">
                        <i class="fa-solid fa-table"></i>
                        <span>TEMPLATE EXCEL ASLI</span>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-200">
                        Klik pada baris tanggal atau nama lokasi sumur untuk langsung mengedit Log Harian sumur tersebut.
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= base_url("export/daily-report/{$rigId}/{$bulan}/{$tahun}") ?>"
                       class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow transition">
                        <i class="fa-solid fa-download"></i>
                        <span>Download File Excel (.xlsx) Ini</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="xl-report-table">
                    <thead>
                        <!-- BARIS 1: JUDUL UTAMA LAPORAN -->
                        <tr>
                            <th colspan="23" class="xl-title-cell">
                                SUMMARY REPORT PER WELL <?= strtoupper(esc($rig['kode'])) ?> <?= strtoupper($bulanList[$bulan] ?? '') ?> <?= $tahun ?>
                            </th>
                        </tr>

                        <!-- BARIS 2 & 3: HEADER KOLOM STANDAR EXCEL BESMINDO -->
                        <tr>
                            <th rowspan="2" class="xl-bg-sky w-9">No</th>
                            <th rowspan="2" class="xl-bg-sky min-w-[120px]">Location</th>
                            <th rowspan="2" class="xl-bg-sky min-w-[82px]">Date</th>
                            <th rowspan="2" class="xl-bg-sky w-20">DISTANCE</th>
                            <th rowspan="2" class="xl-bg-green w-20">MIRU</th>
                            <th rowspan="2" class="xl-bg-green w-20">OPS</th>
                            <th colspan="10" class="xl-bg-yellow">SBWC</th>
                            <th colspan="2" class="xl-bg-orange">UNPAID</th>
                            <th rowspan="2" class="xl-bg-red w-16">SHUT<br>DOWN</th>
                            <th rowspan="2" class="xl-bg-peach w-16">TOTAL<br>DT</th>
                            <th rowspan="2" class="xl-bg-sage w-16">TOTAL<br>HRS</th>
                            <th rowspan="2" class="xl-bg-sky min-w-[210px]">REMARK NPT</th>
                            <th rowspan="2" class="xl-bg-pink min-w-[100px]">JOB</th>
                        </tr>
                        <tr class="text-[11px]">
                            <th class="xl-bg-yellow w-14">Rain<br>(U.C)</th>
                            <th class="xl-bg-yellow w-14">DRY ROAD</th>
                            <th class="xl-bg-yellow w-14">DRY<br>WELL<br>PAD</th>
                            <th class="xl-bg-yellow w-16">PHR<br>Operator</th>
                            <th class="xl-bg-yellow w-14">Trans<br>Sharing</th>
                            <th class="xl-bg-yellow w-12">CE/PE</th>
                            <th class="xl-bg-yellow w-14">3 Party</th>
                            <th class="xl-bg-yellow w-16">WO<br>DAYLIGHT</th>
                            <th class="xl-bg-yellow w-18">PHR Well &amp;<br>Accessories</th>
                            <th class="xl-bg-yellow w-14">W.O<br>FOAM<br>UNIT</th>
                            <th class="xl-bg-orange w-14">RIG</th>
                            <th class="xl-bg-orange w-14">BMS<br>TOOL</th>
                        </tr>

                        <!-- BARIS 4: BARIS TARIF KONTRAK (RP MERah) -->
                        <tr class="bg-white">
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white xl-text-red text-right whitespace-nowrap font-mono text-[11px]">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Rp</span>
                                    <span><?= $fmtRpExcel($rateOdr) ?></span>
                                </div>
                            </th>
                            <th class="bg-white"></th>
                            <th class="bg-white xl-text-red text-right whitespace-nowrap font-mono text-[11px]">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Rp</span>
                                    <span><?= $fmtRpExcel($rateMiru) ?></span>
                                </div>
                            </th>
                            <th class="bg-white xl-text-red text-right whitespace-nowrap font-mono text-[11px]">
                                <div class="flex items-center justify-between gap-1">
                                    <span>Rp</span>
                                    <span><?= $fmtRpExcel($rateOps) ?></span>
                                </div>
                            </th>
                            <th colspan="10" class="bg-white xl-text-red text-right whitespace-nowrap font-mono text-[11px]">
                                <div class="flex items-center justify-between px-1">
                                    <span>Rp</span>
                                    <span><?= $fmtRpExcel($rateSbwc) ?></span>
                                </div>
                            </th>
                            <th colspan="2" class="bg-white xl-text-red text-center whitespace-nowrap font-mono text-[11px]">
                                Rp &nbsp;&nbsp;&nbsp; -
                            </th>
                            <th class="bg-white xl-text-red text-center whitespace-nowrap font-mono text-[11px]">
                                Rp &nbsp; -
                            </th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                        </tr>
                    </thead>

                    <?php
                        $scheduleMtc = (float)($reportMeta['schedule_mtc'] ?? 0.0);
                        $reportNotes = $reportMeta['notes'] ?? [];
                    ?>
                    <tbody>
                        <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="23" class="py-10 text-center font-bold text-slate-500">
                                Belum ada data sumur (Well) pada periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?>. Klik "+ Tambah Sumur Baru" di atas untuk memulai.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php
                            // Grand Total Accumulators across all wells
                            $grandTotals = [
                                'miru' => 0.0, 'ops' => 0.0,
                                'rain' => 0.0, 'road' => 0.0, 'pad' => 0.0, 'phr_op' => 0.0,
                                'trans' => 0.0, 'ce_pe' => 0.0, 'tp' => 0.0, 'daylight' => 0.0,
                                'phr_well' => 0.0, 'foam' => 0.0,
                                'rig' => 0.0, 'tool' => 0.0, 'shutdown' => 0.0,
                                'total_dt' => 0.0, 'total_hrs' => 0.0,
                            ];

                            foreach ($reports as $rep):
                                $repId = (int)$rep['id'];
                                $wellLogs = $dailyLogs[$repId] ?? [];
                                $dt = $dtDetails[$repId] ?? [];

                                // Jika belum ada daily_report_log, bangun minimal 1 baris dari data parent well supaya tetap tampil lengkap
                                if (empty($wellLogs)) {
                                    $fallbackDt = (float)($rep['total_dt'] ?? 0);
                                    $fallbackHrs = (float)($rep['total_jam'] ?? ((float)$rep['miru_jam'] + (float)$rep['ops_jam'] + $fallbackDt));
                                    $wellLogs = [[
                                        'tanggal'       => $rep['tanggal_mulai'] ?: sprintf('%04d-%02d-01', $tahun, $bulan),
                                        'miru_jam'      => (float)($rep['miru_jam'] ?? 0),
                                        'ops_jam'       => (float)($rep['ops_jam'] ?? 0),
                                        'dt_rain'       => (float)($dt[3] ?? 0),
                                        'dt_dry_road'   => (float)($dt[4] ?? 0),
                                        'dt_dry_pad'    => (float)($dt[5] ?? 0),
                                        'dt_phr_op'     => (float)($dt[13] ?? 0),
                                        'dt_trans'      => (float)($dt[11] ?? 0),
                                        'dt_ce_pe'      => (float)($dt[14] ?? 0),
                                        'dt_3rd_party'  => (float)($dt[10] ?? 0),
                                        'dt_daylight'   => (float)($dt[6] ?? 0),
                                        'dt_phr_well'   => (float)($dt[8] ?? 0),
                                        'dt_foam'       => (float)($dt[12] ?? 0),
                                        'dt_rig'        => (float)($dt[1] ?? 0),
                                        'dt_tool'       => (float)($dt[2] ?? 0),
                                        'dt_shutdown'   => (float)($dt[15] ?? 0),
                                        'total_dt'      => $fallbackDt,
                                        'total_hrs'     => $fallbackHrs,
                                        'remark_npt'    => $rep['remark'] ?? '',
                                        'remark_unpaid' => '',
                                    ]];
                                }

                                $rowCount = count($wellLogs);
                                $wSum = [
                                    'miru' => 0.0, 'ops' => 0.0,
                                    'rain' => 0.0, 'road' => 0.0, 'pad' => 0.0, 'phr_op' => 0.0,
                                    'trans' => 0.0, 'ce_pe' => 0.0, 'tp' => 0.0, 'daylight' => 0.0,
                                    'phr_well' => 0.0, 'foam' => 0.0,
                                    'rig' => 0.0, 'tool' => 0.0, 'shutdown' => 0.0,
                                    'total_dt' => 0.0, 'total_hrs' => 0.0,
                                ];

                                foreach ($wellLogs as $dIdx => $dRow):
                                    $vMiru     = (float)($dRow['miru_jam'] ?? 0);
                                    $vOps      = (float)($dRow['ops_jam'] ?? 0);
                                    $vRain     = (float)($dRow['dt_rain'] ?? 0);
                                    $vRoad     = (float)($dRow['dt_dry_road'] ?? 0);
                                    $vPad      = (float)($dRow['dt_dry_pad'] ?? 0);
                                    $vPhrOp    = (float)($dRow['dt_phr_op'] ?? 0);
                                    $vTrans    = (float)($dRow['dt_trans'] ?? 0);
                                    $vCePe     = (float)($dRow['dt_ce_pe'] ?? 0);
                                    $vTp       = (float)($dRow['dt_3rd_party'] ?? 0);
                                    $vDaylight = (float)($dRow['dt_daylight'] ?? 0);
                                    $vPhrWell  = (float)($dRow['dt_phr_well'] ?? 0);
                                    $vFoam     = (float)($dRow['dt_foam'] ?? 0);
                                    $vRig      = (float)($dRow['dt_rig'] ?? 0);
                                    $vTool     = (float)($dRow['dt_tool'] ?? 0);
                                    $vShutdown = (float)($dRow['dt_shutdown'] ?? 0);

                                    $vTotDt  = $vRain + $vRoad + $vPad + $vPhrOp + $vTrans + $vCePe + $vTp + $vDaylight + $vPhrWell + $vFoam + $vRig + $vTool + $vShutdown;
                                    if ($vTotDt == 0 && (float)($dRow['total_dt'] ?? 0) > 0) {
                                        $vTotDt = (float)$dRow['total_dt'];
                                    }
                                    $vTotHrs = $vMiru + $vOps + $vTotDt;
                                    if ($vTotHrs == 0 && (float)($dRow['total_hrs'] ?? 0) > 0) {
                                        $vTotHrs = (float)$dRow['total_hrs'];
                                    }

                                    $wSum['miru']     += $vMiru;
                                    $wSum['ops']      += $vOps;
                                    $wSum['rain']     += $vRain;
                                    $wSum['road']     += $vRoad;
                                    $wSum['pad']      += $vPad;
                                    $wSum['phr_op']   += $vPhrOp;
                                    $wSum['trans']    += $vTrans;
                                    $wSum['ce_pe']    += $vCePe;
                                    $wSum['tp']       += $vTp;
                                    $wSum['daylight'] += $vDaylight;
                                    $wSum['phr_well'] += $vPhrWell;
                                    $wSum['foam']     += $vFoam;
                                    $wSum['rig']      += $vRig;
                                    $wSum['tool']     += $vTool;
                                    $wSum['shutdown'] += $vShutdown;
                                    $wSum['total_dt'] += $vTotDt;
                                    $wSum['total_hrs']+= $vTotHrs;

                                    $remParts = [];
                                    if (!empty($dRow['remark_npt']))    $remParts[] = trim($dRow['remark_npt']);
                                    if (!empty($dRow['remark_unpaid'])) $remParts[] = trim($dRow['remark_unpaid']);
                                    $remText = implode(' | ', array_unique($remParts));
                                    $hasUnpaidDay = ($vRig > 0 || $vTool > 0 || !empty($dRow['remark_unpaid']));
                                    $editDayUrl = base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}?well_id={$repId}&tanggal=" . urlencode($dRow['tanggal'] ?? ''));
                            ?>
                            <tr class="xl-row-hover cursor-pointer" onclick="window.location.href='<?= $editDayUrl ?>'" title="Klik untuk mengedit Log Harian tanggal <?= $fmtDateExcel($dRow['tanggal'] ?? '') ?> pada <?= esc($rep['nama_lokasi'] ?? '') ?>">
                                <?php if ($dIdx === 0): ?>
                                    <td rowspan="<?= $rowCount ?>" class="text-center font-bold bg-white">
                                        <?= (int)$rep['no_well'] ?>
                                    </td>
                                    <td rowspan="<?= $rowCount ?>" class="text-center font-bold bg-white">
                                        <?= esc($rep['nama_lokasi'] ?? ('WELL #' . $rep['no_well'])) ?>
                                    </td>
                                <?php endif; ?>

                                <td class="xl-white-cell text-center whitespace-nowrap font-semibold">
                                    <?= $fmtDateExcel($dRow['tanggal'] ?? '') ?>
                                </td>

                                <?php if ($dIdx === 0): ?>
                                    <td rowspan="<?= $rowCount ?>" class="xl-bg-dist whitespace-nowrap">
                                        <?= $fmtJarakExcel($rep['jarak'] ?? 0) ?>
                                    </td>
                                <?php endif; ?>

                                <td class="xl-white-cell text-right font-semibold"><?= $fmtBlankOrComma($vMiru) ?></td>
                                <td class="xl-white-cell text-right font-semibold"><?= $fmtBlankOrComma($vOps) ?></td>

                                <!-- 10 SBWC Columns -->
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vRain) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vRoad) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vPad) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vPhrOp) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vTrans) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vCePe) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vTp) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vDaylight) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vPhrWell) ?></td>
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vFoam) ?></td>

                                <!-- 2 UNPAID Columns (Red if > 0) -->
                                <td class="xl-white-cell text-right <?= $vRig > 0 ? 'xl-text-red' : '' ?>"><?= $fmtBlankOrComma($vRig) ?></td>
                                <td class="xl-white-cell text-right <?= $vTool > 0 ? 'xl-text-red' : '' ?>"><?= $fmtBlankOrComma($vTool) ?></td>

                                <!-- SHUT DOWN -->
                                <td class="xl-white-cell text-right"><?= $fmtBlankOrComma($vShutdown) ?></td>

                                <!-- TOTAL DT (Peach) -->
                                <td class="xl-bg-peach text-right"><?= $fmtNumComma($vTotDt) ?></td>

                                <!-- TOTAL HRS -->
                                <td class="xl-white-cell text-right font-bold"><?= $fmtNumComma($vTotHrs) ?></td>

                                <!-- REMARK NPT -->
                                <td class="xl-white-cell text-left <?= $hasUnpaidDay ? 'xl-text-red' : 'font-semibold' ?>">
                                    <?= esc($remText) ?>
                                </td>

                                <?php if ($dIdx === 0): ?>
                                    <td rowspan="<?= $rowCount ?>" class="xl-bg-pink text-center font-extrabold text-[11px] leading-tight">
                                        <?= nl2br(esc(str_replace(' ', "\n", trim($rep['status_job'] ?: 'JOB PROGRESS')))) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>

                            <!-- BARIS SUBTOTAL PER SUMUR (BIRU MUDA #D9E1F2 PERSIS EXCEL) -->
                            <tr class="xl-bg-sub">
                                <td class="xl-bg-sub"></td>
                                <td class="xl-bg-sub"></td>
                                <td class="xl-bg-sub"></td>
                                <td class="xl-bg-sub"></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['miru']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['ops']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['rain']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['road']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['pad']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['phr_op']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['trans']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['ce_pe']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['tp']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['daylight']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['phr_well']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['foam']) ?></td>
                                <td class="xl-bg-sub text-right <?= $wSum['rig'] > 0 ? 'xl-text-red' : '' ?>"><?= $fmtNumComma($wSum['rig']) ?></td>
                                <td class="xl-bg-sub text-right <?= $wSum['tool'] > 0 ? 'xl-text-red' : '' ?>"><?= $fmtNumComma($wSum['tool']) ?></td>
                                <td class="xl-bg-sub text-right"><?= $fmtNumComma($wSum['shutdown']) ?></td>
                                <td class="xl-bg-peach text-right"><?= $fmtNumComma($wSum['total_dt']) ?></td>
                                <td class="xl-bg-sub text-right font-extrabold"><?= $fmtNumComma($wSum['total_hrs']) ?></td>
                                <td class="xl-bg-sub"></td>
                                <td class="xl-bg-sub"></td>
                            </tr>
                            <?php
                                foreach ($grandTotals as $gk => $gv) {
                                    $grandTotals[$gk] += $wSum[$gk];
                                }
                            endforeach;
                            ?>

                            <?php
                                $gtMiru        = (float)$grandTotals['miru'];
                                $gtOps         = (float)$grandTotals['ops'];
                                $sumWeatherRd  = (float)$grandTotals['rain'] + (float)$grandTotals['road'];
                                $sumOtherSbwc  = (float)$grandTotals['pad'] + (float)$grandTotals['phr_op']
                                               + (float)$grandTotals['trans'] + (float)$grandTotals['ce_pe']
                                               + (float)$grandTotals['tp'] + (float)$grandTotals['daylight']
                                               + (float)$grandTotals['phr_well'] + (float)$grandTotals['foam'];
                                $sumUnpaid     = (float)$grandTotals['rig'] + (float)$grandTotals['tool'];
                                $sumShutdown   = (float)$grandTotals['shutdown'];
                                $gtDt          = (float)$grandTotals['total_dt'];
                                $gtHrs         = (float)$grandTotals['total_hrs'];

                                $pctMiru       = $gtHrs > 0 ? ($gtMiru / $gtHrs) * 100.0 : 0.0;
                                $pctOps        = $gtHrs > 0 ? ($gtOps / $gtHrs) * 100.0 : 0.0;
                                $pctWeatherRd  = $gtHrs > 0 ? ($sumWeatherRd / $gtHrs) * 100.0 : 0.0;
                                $pctOtherSbwc  = $gtHrs > 0 ? ($sumOtherSbwc / $gtHrs) * 100.0 : 0.0;
                                $pctUnpaid     = $gtHrs > 0 ? ($sumUnpaid / $gtHrs) * 100.0 : 0.0;
                                $pctShutdown   = $gtHrs > 0 ? ($sumShutdown / $gtHrs) * 100.0 : 0.0;
                                $pctTotal      = $gtHrs > 0 ? 100.0 : 0.0;

                                $revMiru       = round($gtMiru * ($rateOdr / 24.0 * 0.75));
                                $revOps        = round($gtOps * ($rateOdr / 24.0));
                                $revWeatherRd  = round($sumWeatherRd * ($rateOdr / 24.0 * 0.65));
                                $revOtherSbwc  = round($sumOtherSbwc * ($rateOdr / 24.0 * 0.65));
                                $revTotal      = $revMiru + $revOps + $revWeatherRd + $revOtherSbwc;

                                $scheduleMtc   = (float)($reportMeta['schedule_mtc'] ?? 0.0);
                                $reportNotes   = $reportMeta['notes'] ?? [];

                                $kpiRel        = $gtHrs > 0 ? max(0.0, (($gtHrs - $sumUnpaid) / $gtHrs) * 100.0) : 0.0;
                                $kpiAvail      = $gtHrs > 0 ? max(0.0, (($gtHrs - $sumUnpaid - $scheduleMtc) / $gtHrs) * 100.0) : 0.0;
                                $kpiUtil       = $gtHrs > 0 ? ((($gtMiru + $gtOps) / $gtHrs) * 100.0) : 0.0;

                                $miruWellCnt = 0;
                                $compWellCnt = 0;
                                foreach ($reports as $rItem) {
                                    if ((float)($rItem['miru_jam'] ?? 0) > 0) $miruWellCnt++;
                                    if (strtoupper(trim((string)($rItem['status_job'] ?? ''))) === 'JOB COMPLETED') $compWellCnt++;
                                }
                                $kpiAvgMiru    = $miruWellCnt > 0 ? ($gtMiru / $miruWellCnt) : $gtMiru;
                                $kpiCycleTime  = $compWellCnt > 0 ? ($gtHrs / $compWellCnt) : $gtHrs;
                                $incentiveTarget = round(($rateOdr * ($gtHrs / 24.0)) * 0.92);

                                $fmtPctComma = static fn($v) => number_format((float)$v, 2, ',', '.') . '%';
                                $fmtRpCell   = static function($v) {
                                    $val = round((float)$v);
                                    if ($val <= 0) {
                                        return '<div class="flex items-center justify-between px-1"><span>Rp</span><span>-</span></div>';
                                    }
                                    return '<div class="flex items-center justify-between px-1"><span>Rp</span><span>' . number_format($val, 0, ',', '.') . '</span></div>';
                                };
                            ?>

                            <!-- ═══ BARIS 1 FOOTER: TOTAL AKUMULASI (PERSIS FOTO 1) ═══ -->
                            <tr>
                                <td colspan="3" class="xl-bg-sky text-center font-extrabold">TOTAL</td>
                                <td class="xl-bg-sky"></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($gtMiru) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($gtOps) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['rain']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['road']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['pad']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['phr_op']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['trans']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['ce_pe']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['tp']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['daylight']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['phr_well']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['foam']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold <?= $grandTotals['rig'] > 0 ? 'xl-text-red' : '' ?>"><?= $fmtNumComma($grandTotals['rig']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold <?= $grandTotals['tool'] > 0 ? 'xl-text-red' : '' ?>"><?= $fmtNumComma($grandTotals['tool']) ?></td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($grandTotals['shutdown']) ?></td>
                                <td rowspan="2" class="xl-bg-orange text-right font-extrabold"><?= $fmtNumComma($gtDt) ?></td>
                                <td rowspan="2" class="xl-bg-sage text-right font-extrabold"><?= $fmtNumComma($gtHrs) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS 2 FOOTER: SUBTOTAL KELOMPOK DT (RAIN+ROAD | OTHER SBWC | UNPAID | SHUTDOWN) ═══ -->
                            <tr>
                                <td colspan="6" class="!border-0 bg-white"></td>
                                <td colspan="2" class="xl-bg-yellow text-center font-extrabold"><?= $fmtNumComma($sumWeatherRd) ?></td>
                                <td colspan="8" class="xl-bg-yellow text-center font-extrabold"><?= $fmtNumComma($sumOtherSbwc) ?></td>
                                <td colspan="2" class="xl-bg-orange text-center font-extrabold"><?= $fmtNumComma($sumUnpaid) ?></td>
                                <td class="xl-bg-red text-center font-extrabold"><?= $fmtNumComma($sumShutdown) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS 3 FOOTER: ANGKA PEMBULATAN 1 DESIMAL DI BAWAH KELOMPOK DT ═══ -->
                            <tr>
                                <td colspan="6" class="!border-0 bg-white"></td>
                                <td colspan="2" class="!border-0 bg-white text-center font-extrabold"><?= number_format($sumWeatherRd, 1, ',', '.') ?></td>
                                <td colspan="8" class="!border-0 bg-white text-center font-extrabold"><?= $fmtNumComma($sumOtherSbwc) ?></td>
                                <td colspan="2" class="!border-0 bg-white text-center font-extrabold"><?= number_format($sumUnpaid, 1, ',', '.') ?></td>
                                <td class="!border-0 bg-white"></td>
                                <td class="!border-0 bg-white"></td>
                                <td class="!border-0 bg-white text-right font-extrabold"><?= $fmtNumComma($gtHrs) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- SPACER ROW -->
                            <tr>
                                <td colspan="23" class="!border-0 bg-white py-1.5"></td>
                            </tr>

                            <!-- ═══ BARIS PRESENTASE (KUNING #FFFF00) ═══ -->
                            <tr>
                                <td colspan="3" class="xl-bg-yellow !text-left font-extrabold">PRESENTASE</td>
                                <td class="xl-bg-yellow"></td>
                                <td class="xl-bg-yellow text-right font-extrabold"><?= $fmtPctComma($pctMiru) ?></td>
                                <td class="xl-bg-yellow text-right font-extrabold"><?= $fmtPctComma($pctOps) ?></td>
                                <td colspan="2" class="xl-bg-yellow text-center font-extrabold"><?= $fmtPctComma($pctWeatherRd) ?></td>
                                <td colspan="8" class="xl-bg-yellow text-center font-extrabold"><?= $fmtPctComma($pctOtherSbwc) ?></td>
                                <td colspan="2" class="xl-bg-yellow text-center font-extrabold"><?= $fmtPctComma($pctUnpaid) ?></td>
                                <td class="xl-bg-yellow text-center font-extrabold"><?= $fmtPctComma($pctShutdown) ?></td>
                                <td colspan="2" class="xl-bg-yellow text-right font-extrabold"><?= $fmtPctComma($pctTotal) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS REVENUE (PEACH #F8CBAD) ═══ -->
                            <tr style="background-color: #F8CBAD !important;">
                                <td colspan="3" style="background-color: #F8CBAD !important;" class="font-extrabold text-left">REVENUE</td>
                                <td colspan="2" style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell($revMiru) ?></td>
                                <td style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell($revOps) ?></td>
                                <td colspan="2" style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell($revWeatherRd) ?></td>
                                <td colspan="8" style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell($revOtherSbwc) ?></td>
                                <td colspan="2" style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell(0) ?></td>
                                <td style="background-color: #F8CBAD !important;" class="font-semibold"><?= $fmtRpCell(0) ?></td>
                                <td colspan="2" style="background-color: #F8CBAD !important;" class="font-extrabold"><?= $fmtRpCell($revTotal) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS KPI 1: REALIBILITY ═══ -->
                            <tr>
                                <td colspan="3" style="background-color: #B1A0C7 !important;" class="font-semibold text-left">REALIBILITY</td>
                                <td style="background-color: #B1A0C7 !important;" class="font-extrabold text-right"><?= $fmtPctComma($kpiRel) ?></td>
                                <td colspan="15" class="!border-0 bg-white"></td>
                                <td colspan="2" class="!border-0 bg-white font-semibold"><?= $fmtRpCell($revTotal) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS KPI 2: AVAILIBILITY + SCHEDULE MTC ═══ -->
                            <tr>
                                <td colspan="3" style="background-color: #B1A0C7 !important;" class="font-semibold text-left">AVAILIBILITY</td>
                                <td style="background-color: #B1A0C7 !important;" class="font-extrabold text-right"><?= $fmtPctComma($kpiAvail) ?></td>
                                <td class="bg-white text-center font-extrabold">SCHEDULE MTC</td>
                                <td class="bg-white text-center font-extrabold cursor-pointer hover:bg-sky-50"
                                    onclick="document.getElementById('noteEditorPanel').scrollIntoView({behavior:'smooth'}); document.getElementById('inputScheduleMtc').focus();"
                                    title="Klik untuk mengubah jam Schedule MTC">
                                    <?= number_format($scheduleMtc, 0, ',', '.') ?>
                                </td>
                                <td colspan="17" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS KPI 3: UTILITITAION + INCENTIVE TARGET ═══ -->
                            <tr>
                                <td colspan="3" style="background-color: #B1A0C7 !important;" class="font-semibold text-left">UTILITITAION</td>
                                <td style="background-color: #B1A0C7 !important;" class="font-extrabold text-right"><?= $fmtPctComma($kpiUtil) ?></td>
                                <td colspan="12" class="!border-0 bg-white"></td>
                                <td colspan="3" class="xl-bg-orange text-center font-extrabold text-sm">INCENTIVE TARGET</td>
                                <td colspan="2" class="xl-bg-orange font-extrabold text-sm"><?= $fmtRpCell($incentiveTarget) ?></td>
                                <td colspan="2" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS KPI 4: AVERANGE MIRU ═══ -->
                            <tr>
                                <td colspan="3" class="xl-bg-green !text-left font-semibold">AVERANGE MIRU</td>
                                <td class="xl-bg-green text-right font-extrabold"><?= $fmtNumComma($kpiAvgMiru) ?></td>
                                <td colspan="19" class="!border-0 bg-white"></td>
                            </tr>

                            <!-- ═══ BARIS KPI 5: CYCLE TIME ═══ -->
                            <tr>
                                <td colspan="3" class="xl-bg-yellow !text-left font-semibold">CYCLE TIME</td>
                                <td class="xl-bg-yellow text-right font-extrabold"><?= $fmtNumComma($kpiCycleTime) ?></td>
                                <td colspan="19" class="!border-0 bg-white"></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ════════════════════════════════════════════════════════════════════════
                 BAGIAN BAWAH LAPORAN: NOTE / CATATAN LAPORAN PERSIS FOTO 2
                 (Contoh: 5H-0310A   28-Sep   8 Hrs Preventive maintenance Check and service...)
                 ════════════════════════════════════════════════════════════════════════ -->
            <div class="bg-white border-t border-slate-300 px-6 py-5 text-black" style="font-family: 'Calibri', 'Plus Jakarta Sans', Arial, sans-serif;">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded bg-amber-100 text-amber-900 border border-amber-300 text-xs font-extrabold uppercase tracking-wider">
                            <i class="fa-solid fa-note-sticky mr-1"></i> Note / Catatan Tambahan Laporan
                        </span>
                        <span class="text-xs text-slate-600 font-semibold">
                            Tampil di bagian bawah laporan &amp; otomatis ikut tercetak di Excel (.xlsx)
                        </span>
                    </div>
                    <button type="button" onclick="toggleNoteEditorForm()"
                        class="px-3.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>+ Buat / Edit Note di Laporan</span>
                    </button>
                </div>

                <!-- Daftar Note Tampil Persis Seperti Foto 2 Excel -->
                <?php if (!empty($reportNotes)): ?>
                <div class="space-y-4 mb-4">
                    <?php foreach ($reportNotes as $nItem): ?>
                    <div class="group relative rounded-xl p-3.5 bg-white hover:bg-slate-50 border border-transparent hover:border-slate-300 transition">
                        <!-- Baris 1: Kode Lokasi / Sumur + Tanggal (Persis Foto 2) -->
                        <div class="flex flex-wrap items-center gap-16 sm:gap-28 font-extrabold text-[15px] text-black leading-snug">
                            <span><?= esc($nItem['lokasi'] ?? '') ?></span>
                            <span><?= esc($nItem['tanggal'] ?? '') ?></span>
                        </div>
                        <!-- Baris 2: Isi Note / Preventive Maintenance -->
                        <div class="font-extrabold text-[15px] text-black leading-snug whitespace-pre-line mt-0.5 max-w-4xl"><?= esc($nItem['isi'] ?? '') ?></div>
                        <!-- Baris 3: Rentang Waktu [09:00 am - 17:00 pm] -->
                        <?php if (!empty($nItem['waktu'])): ?>
                        <div class="font-extrabold text-[15px] text-black leading-snug mt-0.5"><?= esc($nItem['waktu']) ?></div>
                        <?php endif; ?>

                        <!-- Tombol Edit & Hapus Note -->
                        <div class="mt-2.5 flex items-center gap-2">
                            <button type="button"
                                onclick='editReportNote(<?= json_encode($nItem, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                class="px-2.5 py-1 rounded bg-sky-100 hover:bg-sky-200 text-sky-800 text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-pen mr-1"></i> Edit Note
                            </button>
                            <form method="POST" action="<?= base_url('daily-report/hapus-catatan') ?>"
                                onsubmit="return confirm('Hapus catatan laporan ini?')" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="rig_id" value="<?= $rigId ?>">
                                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                                <input type="hidden" name="note_id" value="<?= esc($nItem['id'] ?? '') ?>">
                                <button type="submit"
                                    class="px-2.5 py-1 rounded bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition cursor-pointer">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="py-3 text-xs text-slate-500 italic mb-3">
                    Belum ada catatan / note khusus pada laporan bulan ini. Klik <strong>"+ Buat / Edit Note di Laporan"</strong> untuk menambahkan catatan (seperti Preventive Maintenance, dll.).
                </div>
                <?php endif; ?>

                <!-- FORM INPUT / EDIT NOTE LAPORAN & SCHEDULE MTC -->
                <div id="noteEditorPanel" class="rounded-xl bg-slate-100 border border-slate-300 p-4 mt-2">
                    <form method="POST" action="<?= base_url('daily-report/simpan-catatan') ?>" class="space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="rig_id" value="<?= $rigId ?>">
                        <input type="hidden" name="bulan" value="<?= $bulan ?>">
                        <input type="hidden" name="tahun" value="<?= $tahun ?>">
                        <input type="hidden" name="note_id" id="inputNoteId" value="">

                        <div class="flex items-center justify-between border-b border-slate-300 pb-2">
                            <h4 id="noteFormTitle" class="text-xs font-extrabold uppercase tracking-wider text-slate-800">
                                Tambah Note Laporan Baru &amp; Pengaturan Schedule MTC
                            </h4>
                            <button type="button" onclick="resetReportNoteForm()" class="text-xs font-bold text-sky-700 hover:underline cursor-pointer">
                                Reset Form Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1">
                                    Kode Lokasi / Sumur (Baris Atas Kiri)
                                </label>
                                <input type="text" name="note_lokasi" id="inputNoteLokasi"
                                    placeholder="Contoh: 5H-0310A"
                                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold text-slate-900">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1">
                                    Tanggal Note (Baris Atas Kanan)
                                </label>
                                <input type="text" name="note_tanggal" id="inputNoteTanggal"
                                    placeholder="Contoh: 28-Sep"
                                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold text-slate-900">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1">
                                    Rentang Jam (Baris Bawah)
                                </label>
                                <input type="text" name="note_waktu" id="inputNoteWaktu"
                                    placeholder="Contoh: [09:00 am - 17:00 pm]"
                                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold text-slate-900">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1">
                                    Schedule MTC (Jam)
                                </label>
                                <input type="number" step="0.5" min="0" name="schedule_mtc" id="inputScheduleMtc"
                                    value="<?= esc((string)$scheduleMtc) ?>"
                                    class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-extrabold text-right text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase text-slate-700 mb-1">
                                Isi Keterangan Note (Tampil Tebal di Bawah Kode &amp; Tanggal)
                            </label>
                            <textarea name="note_isi" id="inputNoteIsi" rows="2"
                                placeholder="Contoh: 8 Hrs Preventive maintenance Check and service: Rig carrier, Mast Rig, office caravan, genset & accumulator, Mud pump, lighting, genset moving"
                                class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold text-slate-900"></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow transition cursor-pointer">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Note &amp; Schedule MTC ke Laporan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ 4. INTERACTIVE MODAL QUICK LOOK ═══════════════════════════════════════ -->
<div id="quickLookModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
        <!-- Close Button -->
        <button type="button" onclick="closeWellQuickLook()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-black">
                <i class="fa-solid fa-oil-well text-lg"></i>
            </div>
            <div>
                <span id="modalWellNo" class="text-[10px] font-mono font-bold uppercase text-blue-400"></span>
                <h3 id="modalWellName" class="text-base font-black text-white"></h3>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-2.5 p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-xs font-num">
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Tanggal Mulai - Selesai</span>
                <span id="modalWellDates" class="text-white font-sans font-bold"></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Jarak Mobilisasi</span>
                <span id="modalWellDistance" class="text-white font-bold"></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Status Pekerjaan</span>
                <span id="modalWellStatus" class="font-sans font-bold"></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Jumlah Hari Log</span>
                <span id="modalWellLogs" class="text-cyan-400 font-bold font-sans"></span>
            </div>
        </div>

        <!-- Hours Breakdown -->
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="p-2.5 rounded-xl bg-lime-950/30 border border-lime-500/30">
                <span class="text-[10px] uppercase font-bold text-lime-400 block">OPS</span>
                <span id="modalOps" class="text-sm font-black text-lime-300 font-num"></span>
            </div>
            <div class="p-2.5 rounded-xl bg-lime-950/30 border border-lime-500/30">
                <span class="text-[10px] uppercase font-bold text-lime-400 block">MIRU</span>
                <span id="modalMiru" class="text-sm font-black text-lime-300 font-num"></span>
            </div>
            <div class="p-2.5 rounded-xl bg-amber-950/30 border border-amber-500/30">
                <span class="text-[10px] uppercase font-bold text-amber-400 block">Downtime</span>
                <span id="modalDt" class="text-sm font-black text-yellow-300 font-num"></span>
            </div>
        </div>

        <!-- Remark -->
        <div class="text-xs text-slate-300 bg-slate-850 p-3 rounded-xl border border-slate-800">
            <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Catatan Operasi:</span>
            <p id="modalRemark" class="font-sans italic text-slate-300"></p>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
            <button type="button" onclick="closeWellQuickLook()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition">
                Tutup
            </button>
            <a id="modalEditLink" href="#" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow transition">
                Edit Sumur
            </a>
        </div>
    </div>
</div>

<!-- ═══ MODAL PILIHAN EXPORT EXCEL (SENIOR-FRIENDLY & INTUITIF) ══════ -->
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
                        Periode Aktif: <strong class="text-amber-400 font-semibold"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong> — Rig Aktif: <strong class="text-cyan-400 font-semibold"><?= esc($rig['kode']) ?></strong>
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeExportModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition" title="Tutup (Esc)">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Opsi Pilihan Download (Kartu Besar, Kontras Tinggi, Sangat Mudah Diklik) -->
        <div class="space-y-3">
            <!-- OPSI 1: Rig Ini Saja (Bulan Ini) -->
            <a href="<?= base_url("export/daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" onclick="closeExportModal()"
               class="group flex items-center justify-between p-4 rounded-2xl bg-slate-850 hover:bg-slate-800 border-2 border-slate-700 hover:border-emerald-500 transition-all duration-200 shadow-sm active:scale-[0.99]">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-200 flex-shrink-0">
                        <i class="fa-solid fa-oil-well"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white group-hover:text-emerald-300 transition">Rig Ini Saja (<?= esc($rig['kode']) ?>)</span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 border border-emerald-500/30 text-[10px] font-extrabold text-emerald-400 uppercase">Per Rig</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                            Download ringkasan pekerjaan sumur khusus armada <strong class="text-slate-200"><?= esc($rig['kode']) ?></strong> untuk bulan <strong class="text-slate-200"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong>.
                        </p>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-slate-800 group-hover:bg-emerald-500/20 text-slate-400 group-hover:text-emerald-400 flex items-center justify-center transition flex-shrink-0 ml-3">
                    <i class="fa-solid fa-download text-sm"></i>
                </div>
            </a>

            <!-- OPSI 2: Seluruh Rig Sekaligus (All Rigs Bulan Ini) -->
            <a href="<?= base_url("export/daily-report-all/{$bulan}/{$tahun}") ?>" onclick="closeExportModal()"
               class="group flex items-center justify-between p-4 rounded-2xl bg-slate-850 hover:bg-slate-800 border-2 border-slate-700 hover:border-cyan-500 transition-all duration-200 shadow-sm active:scale-[0.99]">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-xl group-hover:bg-cyan-500 group-hover:text-white transition-all duration-200 flex-shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white group-hover:text-cyan-300 transition">Seluruh Rig Sekaligus (All Rigs)</span>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/15 border border-cyan-500/30 text-[10px] font-extrabold text-cyan-400 uppercase">Multi-Sheet</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                            1 file Excel berisi sheet terpisah untuk setiap armada Rig + 1 lembar rekapitulasi semua sumur bulan <strong class="text-slate-200"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong>.
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
    function navigateGrid() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        try {
            localStorage.setItem('dr_last_rig', rigId);
            localStorage.setItem('dr_last_bulan', bulan);
            localStorage.setItem('dr_last_tahun', tahun);
        } catch (e) {}
        window.location.href = `<?= base_url('daily-report') ?>/${rigId}/${bulan}/${tahun}`;
    }

    let activeStatusFilter = 'ALL';

    function setStatusFilter(status) {
        activeStatusFilter = status;
        try {
            sessionStorage.setItem('dr_status_filter', status);
        } catch (e) {}
        const allBtn = document.getElementById('btnFilter_ALL');
        const compBtn = document.getElementById('btnFilter_COMPLETED');
        const progBtn = document.getElementById('btnFilter_PROGRESS');

        if (allBtn) allBtn.classList.remove('active-all');
        if (compBtn) compBtn.classList.remove('active-completed');
        if (progBtn) progBtn.classList.remove('active-progress');

        if (status === 'ALL' && allBtn) {
            allBtn.classList.add('active-all');
        } else if (status === 'COMPLETED' && compBtn) {
            compBtn.classList.add('active-completed');
        } else if (status === 'PROGRESS' && progBtn) {
            progBtn.classList.add('active-progress');
        }

        filterWellsTable();
    }

    function switchViewMode(mode) {
        ['compact', 'full', 'cards'].forEach(m => {
            const container = document.getElementById(`viewContainer_${m}`);
            const btn = document.getElementById(`btnView_${m}`);
            if (container) container.classList.add('hidden');
            if (btn) {
                btn.classList.remove('active');
            }
        });

        const activeContainer = document.getElementById(`viewContainer_${mode}`);
        const activeBtn = document.getElementById(`btnView_${mode}`);
        if (activeContainer) activeContainer.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    }

    const expandedCompactWells = new Set();
    function toggleCompactWell(repId) {
        const row = document.getElementById(`compact-logs-${repId}`);
        const icon = document.getElementById(`compact-icon-${repId}`);
        if (!row) return;

        if (expandedCompactWells.has(repId)) {
            row.classList.add('hidden');
            if (icon) icon.style.transform = 'rotate(0deg)';
            expandedCompactWells.delete(repId);
        } else {
            row.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(180deg)';
            expandedCompactWells.add(repId);
        }
    }

    function expandAllCompactLogs() {
        document.querySelectorAll('[id^="compact-logs-"]').forEach(row => {
            row.classList.remove('hidden');
            const repId = parseInt(row.id.replace('compact-logs-', ''));
            expandedCompactWells.add(repId);
            const icon = document.getElementById(`compact-icon-${repId}`);
            if (icon) icon.style.transform = 'rotate(180deg)';
        });
    }

    function collapseAllCompactLogs() {
        document.querySelectorAll('[id^="compact-logs-"]').forEach(row => {
            row.classList.add('hidden');
            const repId = parseInt(row.id.replace('compact-logs-', ''));
            expandedCompactWells.delete(repId);
            const icon = document.getElementById(`compact-icon-${repId}`);
            if (icon) icon.style.transform = 'rotate(0deg)';
        });
    }

    function clearSearch() {
        const input = document.getElementById('searchWellInput');
        input.value = '';
        document.getElementById('btnClearSearch').classList.add('hidden');
        try {
            sessionStorage.removeItem('dr_search_query');
        } catch (e) {}
        filterWellsTable();
    }

    function filterWellsTable() {
        const input = document.getElementById('searchWellInput');
        const filter = input.value.toLowerCase().trim();
        try {
            if (filter !== '') {
                sessionStorage.setItem('dr_search_query', input.value);
            } else {
                sessionStorage.removeItem('dr_search_query');
            }
        } catch (e) {}
        const btnClear = document.getElementById('btnClearSearch');
        if (btnClear) {
            if (filter !== '') btnClear.classList.remove('hidden');
            else btnClear.classList.add('hidden');
        }

        let visibleCount = 0;

        // Filter Compact Table Rows
        const rows = document.querySelectorAll('#compactTableBody .well-item-row');
        rows.forEach(row => {
            const wellName = row.getAttribute('data-wellname') || row.textContent.toLowerCase();
            const status = row.getAttribute('data-status') || '';
            const matchSearch = (filter === '' || wellName.includes(filter));
            const matchStatus = (activeStatusFilter === 'ALL' || status === activeStatusFilter);

            const repId = row.id.replace('well-row-', '');
            const logRow = document.getElementById(`compact-logs-${repId}`);

            if (matchSearch && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                if (logRow) logRow.classList.add('hidden');
            }
        });

        // Filter Grid Cards
        const cards = document.querySelectorAll('#cardsGrid .well-card-item');
        cards.forEach(card => {
            const wellName = card.getAttribute('data-wellname') || card.textContent.toLowerCase();
            const status = card.getAttribute('data-status') || '';
            const matchSearch = (filter === '' || wellName.includes(filter));
            const matchStatus = (activeStatusFilter === 'ALL' || status === activeStatusFilter);

            card.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });

        // Update counts
        const renderedCountEl = document.getElementById('renderedWellCount');
        const footerCountEl = document.getElementById('footerWellCount');
        if (renderedCountEl) renderedCountEl.textContent = visibleCount;
        if (footerCountEl) footerCountEl.textContent = visibleCount;
    }

    // Modal Quick Look Handler
    function openWellQuickLook(data) {
        document.getElementById('modalWellNo').textContent = `NO. WELL #${data.no_well}`;
        document.getElementById('modalWellName').textContent = data.nama_lokasi || 'Lokasi Sumur';
        document.getElementById('modalWellDates').textContent = `${data.tanggal_mulai} s/d ${data.tanggal_selesai}`;
        document.getElementById('modalWellDistance').textContent = `${data.jarak} KM`;
        document.getElementById('modalWellStatus').textContent = data.status_job;
        document.getElementById('modalWellLogs').textContent = `${data.log_count} Hari Tercatat`;
        document.getElementById('modalOps').textContent = `${data.ops_jam.toFixed(2)} Jam`;
        document.getElementById('modalMiru').textContent = `${data.miru_jam.toFixed(2)} Jam`;
        document.getElementById('modalDt').textContent = `${(data.sbwc_jam + data.unpaid_jam).toFixed(2)} Jam`;
        document.getElementById('modalRemark').textContent = data.remark || 'Tidak ada catatan khusus.';
        document.getElementById('modalEditLink').href = data.edit_url;

        document.getElementById('quickLookModal').classList.remove('hidden');
    }

    function closeWellQuickLook() {
        document.getElementById('quickLookModal').classList.add('hidden');
    }

    // Export Modal Handlers
    function openExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) modal.classList.add('hidden');
    }

    // Close on Escape or click outside
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeWellQuickLook();
            closeExportModal();
            const searchInput = document.getElementById('searchWellInput');
            if (searchInput && (document.activeElement === searchInput || searchInput.value !== '')) {
                clearSearch();
                searchInput.blur();
            }
        }
    });

    // Restore search and filter state on page load
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const savedQuery = sessionStorage.getItem('dr_search_query');
            const savedStatus = sessionStorage.getItem('dr_status_filter');

            const searchInput = document.getElementById('searchWellInput');
            if (savedQuery && searchInput) {
                searchInput.value = savedQuery;
                const btnClear = document.getElementById('btnClearSearch');
                if (btnClear) btnClear.classList.remove('hidden');
            }

            if (savedStatus && ['ALL', 'COMPLETED', 'PROGRESS'].includes(savedStatus)) {
                setStatusFilter(savedStatus);
            } else if (savedQuery) {
                filterWellsTable();
            }
        } catch (e) {
            console.error(e);
        }
    });

    function toggleNoteEditorForm() {
        const panel = document.getElementById('noteEditorPanel');
        if (!panel) return;
        panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const lokInput = document.getElementById('inputNoteLokasi');
        if (lokInput) lokInput.focus();
    }

    function editReportNote(note) {
        if (!note) return;
        document.getElementById('inputNoteId').value = note.id || '';
        document.getElementById('inputNoteLokasi').value = note.lokasi || '';
        document.getElementById('inputNoteTanggal').value = note.tanggal || '';
        document.getElementById('inputNoteWaktu').value = note.waktu || '';
        document.getElementById('inputNoteIsi').value = note.isi || '';
        const titleEl = document.getElementById('noteFormTitle');
        if (titleEl) {
            titleEl.textContent = `Edit Note Laporan: ${note.lokasi || ''} (${note.tanggal || ''})`;
        }
        toggleNoteEditorForm();
    }

    function resetReportNoteForm() {
        document.getElementById('inputNoteId').value = '';
        document.getElementById('inputNoteLokasi').value = '';
        document.getElementById('inputNoteTanggal').value = '';
        document.getElementById('inputNoteWaktu').value = '';
        document.getElementById('inputNoteIsi').value = '';
        const titleEl = document.getElementById('noteFormTitle');
        if (titleEl) {
            titleEl.textContent = 'Tambah Note Laporan Baru & Pengaturan Schedule MTC';
        }
    }
</script>
<?= $this->endSection() ?>
