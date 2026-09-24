<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--operations space-y-5 pb-12">

    <!-- ═══ 1. HERO HEADER & WORKFLOW GUIDE (FAMILY-FRIENDLY & INTUITIF) ══════ -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-[#0A1835] border border-slate-700/80 shadow-2xl p-5 sm:p-6">
        <!-- Ambient Glow Effects -->
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
            <!-- Rig Info & Title -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-500/30 border border-white/10">
                    <i class="fa-solid fa-oil-well text-2xl"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-black uppercase tracking-wider border border-emerald-500/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            STEP 1 · REKAP SUMUR (WELL)
                        </span>
                        <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                            <i class="fa-regular fa-calendar text-slate-500"></i>
                            <?= [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'][$bulan] ?? '' ?> <?= $tahun ?>
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                        <span class="bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent"><?= esc($rig['kode']) ?></span>
                        <span class="text-slate-400 font-normal text-base hidden sm:inline">| <?= esc($rig['nama_rig']) ?></span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pusat ringkasan pekerjaan seluruh sumur, durasi operasi, MIRU moving, dan pos downtime.
                    </p>
                </div>
            </div>

            <!-- Workflow Step Shortcut Buttons (Family-Friendly Stepper) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="<?= base_url('daily-report/log-harian/' . $rigId . '/' . $bulan . '/' . $tahun) ?>"
                   class="group px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 font-bold text-xs shadow-md transition-all duration-200 flex items-center gap-2 active:scale-95">
                    <span class="w-5 h-5 rounded-lg bg-cyan-500/30 group-hover:bg-white/20 flex items-center justify-center text-[10px] font-mono font-black">2</span>
                    <span>Input Daily Report</span>
                    <i class="fa-solid fa-arrow-right text-[10px] opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
                <a href="<?= base_url('npt/' . $rigId . '/' . $bulan . '/' . $tahun) ?>"
                   class="group px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-amber-600 text-amber-300 hover:text-white border border-amber-500/30 font-bold text-xs shadow-md transition-all duration-200 flex items-center gap-2 active:scale-95">
                    <span class="w-5 h-5 rounded-lg bg-amber-500/30 group-hover:bg-white/20 flex items-center justify-center text-[10px] font-mono font-black">3</span>
                    <span>Input NPT (Downtime)</span>
                    <i class="fa-solid fa-arrow-right text-[10px] opacity-70 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
                <a href="<?= base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}") ?>" 
                   class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs shadow-lg shadow-blue-600/30 border border-blue-400/30 transition-all duration-200 flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-circle-plus text-sm"></i>
                    <span>+ Tambah Sumur</span>
                </a>
            </div>
        </div>

        <!-- 3-Step Guided Path Banner (Very Friendly for Beginners) -->
        <div class="mt-4 pt-3.5 border-t border-slate-700/60 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div class="flex items-center gap-2.5 p-2 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-200">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[11px] font-black">1</span>
                <div>
                    <span class="font-black text-white block">Step 1: Rekap Sumur (Well)</span>
                    <span class="text-[10px] text-blue-300/80">Daftarkan sumur baru &amp; cek ringkasan total</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-800/60 border border-slate-700/60 text-slate-300">
                <span class="w-6 h-6 rounded-full bg-slate-700 text-slate-300 flex items-center justify-center text-[11px] font-bold">2</span>
                <div>
                    <span class="font-bold text-slate-200 block">Step 2: Input Daily Report</span>
                    <span class="text-[10px] text-slate-400">Catat laporan aktivitas per tanggal</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-800/60 border border-slate-700/60 text-slate-300">
                <span class="w-6 h-6 rounded-full bg-slate-700 text-slate-300 flex items-center justify-center text-[11px] font-bold">3</span>
                <div>
                    <span class="font-bold text-slate-200 block">Step 3: Input NPT (Downtime)</span>
                    <span class="text-[10px] text-slate-400">Koreksi jam SBWC, UNPAID &amp; 3rd Party</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 2. 5 INTERACTIVE KPI DASHBOARD STAT CARDS ════════════════════════ -->
    <?php
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
        <div class="p-4 rounded-2xl bg-slate-900/95 border border-slate-700/80 shadow-lg relative overflow-hidden group hover:border-blue-500/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Sumur</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black font-num text-white"><?= $wellJobCount ?></span>
                <span class="text-xs text-slate-400 font-semibold">Sumur</span>
            </div>
            <!-- Progress Mini Bar -->
            <div class="mt-2.5">
                <div class="flex items-center justify-between text-[10px] font-bold mb-1">
                    <span class="text-emerald-400"><?= $completedCount ?> Selesai</span>
                    <span class="text-amber-400"><?= $progressCount ?> On Progress</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden flex">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: <?= $completionPct ?>%"></div>
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: <?= 100 - $completionPct ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Jam Operasi (OPS) -->
        <div class="p-4 rounded-2xl bg-slate-900/95 border border-slate-700/80 shadow-lg relative overflow-hidden group hover:border-emerald-500/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jam Operasi (OPS)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-gears"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black font-num text-emerald-400"><?= number_format($totalOps, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-400">
                <span>Rata-rata: <strong class="text-emerald-300"><?= $wellJobCount > 0 ? number_format($totalOps / $wellJobCount, 1) : 0 ?>h/sumur</strong></span>
                <span class="font-mono text-emerald-400 font-bold"><?= $opsShare ?>% Porsi</span>
            </div>
        </div>

        <!-- Card 3: Jam Moving & MIRU -->
        <div class="p-4 rounded-2xl bg-slate-900/95 border border-slate-700/80 shadow-lg relative overflow-hidden group hover:border-lime-500/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jam MIRU / Moving</span>
                <div class="w-8 h-8 rounded-xl bg-lime-500/10 text-lime-400 border border-lime-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black font-num text-lime-400"><?= number_format($totalMiru, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-400">
                <span>Total Jarak: <strong class="text-lime-300"><?= number_format($totalDistance, 0) ?> KM</strong></span>
                <span class="font-mono text-lime-400 font-bold"><?= $miruShare ?>% Porsi</span>
            </div>
        </div>

        <!-- Card 4: Total Downtime -->
        <div class="p-4 rounded-2xl bg-slate-900/95 border border-slate-700/80 shadow-lg relative overflow-hidden group hover:border-amber-500/60 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Downtime</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black font-num text-yellow-300"><?= number_format($grandTotalDt, 1) ?></span>
                <span class="text-xs text-slate-400 font-semibold">Jam</span>
            </div>
            <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-400">
                <span>Akumulasi Kendala</span>
                <span class="font-mono text-yellow-300 font-bold"><?= $dtShare ?>% Total</span>
            </div>
        </div>

        <!-- Card 5: Nilai Kontrak ODR -->
        <div class="p-4 rounded-2xl bg-slate-900/95 border border-slate-700/80 shadow-lg relative overflow-hidden group hover:border-purple-500/60 transition-all duration-200 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tarif ODR Kontrak</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-xs text-slate-400 font-bold">Rp</span>
                <span class="text-xl font-black font-num text-purple-300"><?= number_format($odr, 0, ',', '.') ?></span>
            </div>
            <div class="mt-2.5 flex items-center justify-between text-[10px] text-purple-400 font-semibold">
                <span>Per Hari Operasi</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded bg-purple-500/20 border border-purple-500/30">Contract Rate</span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. FILTER, SEARCH & VIEW MODE TOOLBAR (INTERAKTIF & TACTILE) ══════ -->
    <style>
        .dr-toolbar-card {
            background: linear-gradient(135deg, rgba(17, 29, 46, 0.97) 0%, rgba(13, 20, 34, 0.98) 100%);
            border: 1px solid rgba(37, 48, 64, 0.9);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.45);
        }
        .dr-control-box {
            background: #091322;
            border: 1px solid #1e2f47;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dr-control-box:hover {
            border-color: #3b5375;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        .dr-control-box:focus-within {
            border-color: #d4a820;
            box-shadow: 0 0 0 3px rgba(212, 168, 32, 0.18);
        }
        .dr-stepper-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #0f1c30;
            border: 1px solid #20334e;
            color: #8fa0b5;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .dr-stepper-btn:hover {
            background: #1e3355;
            color: #f8fafc;
            border-color: #4a77b5;
            transform: translateY(-1px);
        }
        .dr-stepper-btn:active {
            transform: scale(0.92);
        }
        /* Segmented View Switcher */
        .dr-segmented-group {
            background: #091322;
            border: 1px solid #1e2f47;
            padding: 4px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .dr-view-pill {
            padding: 7px 15px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #7e93ab;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .dr-view-pill:hover {
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-1px);
        }
        .dr-view-pill:active {
            transform: scale(0.95);
        }
        .dr-view-pill.active {
            background: linear-gradient(135deg, #1b2e4b 0%, #15243b 100%) !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }
        .dr-view-pill.active i {
            color: #d4a820 !important;
        }
        /* Filter Chips */
        .dr-chip-btn {
            padding: 7px 13px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #091322;
            border: 1px solid #1e2f47;
            color: #7e93ab;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .dr-chip-btn:hover {
            background: #112036;
            color: #e2e8f0;
            border-color: #2b4363;
            transform: translateY(-1px);
        }
        .dr-chip-btn:active {
            transform: scale(0.95);
        }
        .dr-chip-btn.active-all {
            background: linear-gradient(135deg, #d4a820 0%, #b38813 100%) !important;
            color: #0f172a !important;
            border-color: #facc15 !important;
            box-shadow: 0 4px 14px rgba(212, 168, 32, 0.35);
        }
        .dr-chip-btn.active-completed {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            color: #ffffff !important;
            border-color: #34d399 !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }
        .dr-chip-btn.active-progress {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            color: #ffffff !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        }
        /* Export Button */
        .dr-export-action {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: 1px solid #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dr-export-action:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
            transform: translateY(-1px);
        }
        .dr-export-action:active {
            transform: scale(0.95);
        }
    </style>

    <div class="dr-toolbar-card p-4 rounded-3xl space-y-3.5">
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

        <!-- Baris 1: Navigasi Periode, Armada & Pilihan View Mode -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3">
            <!-- Left: Rig, Month Stepper, Year -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Rig Selector -->
                <div class="dr-control-box flex items-center gap-2 px-3 py-2 rounded-xl shadow-inner">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-oil-well text-amber-400"></i>
                        <span>Rig:</span>
                    </span>
                    <select id="selectRig" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-black text-white focus:outline-none cursor-pointer pr-1">
                        <?php foreach ($allRigs as $r): ?>
                            <option value="<?= $r['id'] ?>" class="bg-slate-900 text-white" <?= $r['id'] == $rigId ? 'selected' : '' ?>><?= esc($r['kode']) ?> - <?= esc($r['nama_rig']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Month Quick Stepper -->
                <div class="dr-control-box flex items-center p-1 rounded-xl shadow-inner gap-1">
                    <a href="<?= base_url("daily-report/{$rigId}/{$prevBulan}/{$prevTahun}") ?>" 
                       class="dr-stepper-btn" title="Bulan Sebelumnya (<?= $bulanList[$prevBulan] ?? '' ?>)">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                    <div class="flex items-center px-2.5 py-1 gap-1.5">
                        <i class="fa-regular fa-calendar text-amber-400 text-xs"></i>
                        <select id="selectBulan" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-black text-white focus:outline-none cursor-pointer">
                            <?php foreach ($bulanList as $num => $nama): ?>
                                <option value="<?= $num ?>" class="bg-slate-900 text-white" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <a href="<?= base_url("daily-report/{$rigId}/{$nextBulan}/{$nextTahun}") ?>" 
                       class="dr-stepper-btn" title="Bulan Berikutnya (<?= $bulanList[$nextBulan] ?? '' ?>)">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>

                <!-- Selector Tahun -->
                <div class="dr-control-box flex items-center gap-2 px-3 py-2 rounded-xl shadow-inner">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-days text-sky-400 text-xs"></i>
                        <span>Tahun:</span>
                    </span>
                    <select id="selectTahun" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-black text-white focus:outline-none cursor-pointer pr-1">
                        <?php for ($y = 2024; $y <= 2028; $y++): ?>
                            <option value="<?= $y ?>" class="bg-slate-900 text-white" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Penjelasan Singkat Sesuai Konteks Proyek SIMOR BMS -->
                <div class="dr-control-box hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl shadow-inner bg-slate-900/60 border border-slate-750 text-slate-300">
                    <i class="fa-solid fa-circle-info text-cyan-400 text-xs flex-shrink-0"></i>
                    <span class="text-[11px] text-slate-300 leading-tight">
                        Filter aktif: Rekapitulasi operasional sumur <strong class="text-white"><?= esc($rig['kode']) ?></strong> periode <strong class="text-cyan-300"><?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?></strong>.
                    </span>
                </div>
            </div>

            <!-- Right: View Mode Segmented Switcher & Export -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- View Mode Segmented Control -->
                <div class="dr-segmented-group">
                    <button type="button" onclick="switchViewMode('compact')" id="btnView_compact" class="dr-view-pill active" title="Tampilan Ringkasan Pintar (Tabel + Log Harian)">
                        <i class="fa-solid fa-table-list text-xs"></i>
                        <span>Ringkasan</span>
                    </button>
                    <button type="button" onclick="switchViewMode('cards')" id="btnView_cards" class="dr-view-pill" title="Tampilan Kartu Visual per Sumur">
                        <i class="fa-solid fa-grip text-xs"></i>
                        <span>Kartu Sumur</span>
                    </button>
                    <button type="button" onclick="switchViewMode('full')" id="btnView_full" class="dr-view-pill" title="Tampilan Matriks Lengkap SYS (30+ Kolom)">
                        <i class="fa-solid fa-table-cells text-xs"></i>
                        <span>Matriks SYS</span>
                    </button>
                </div>

                <!-- Export Button (Membuka Dialog Pilihan Lengkap) -->
                <button type="button" onclick="openExportModal()"
                   class="dr-export-action px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 active:scale-95 shadow-lg shadow-emerald-600/25" title="Buka Menu Pilihan Download Excel">
                    <i class="fa-solid fa-file-excel text-base text-emerald-200"></i>
                    <span class="text-white">Export Excel</span>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-80 ml-0.5"></i>
                </button>
            </div>
        </div>

        <!-- Baris 2: Pencarian Cepat & Filter Status Interaktif -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-3 border-t border-slate-700/60">
            <!-- Search Bar -->
            <div class="dr-control-box flex items-center px-3.5 py-2 rounded-xl shadow-inner flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2.5"></i>
                <input type="text" id="searchWellInput" onkeyup="filterWellsTable()" 
                       placeholder="Ketik no sumur, nama lokasi, atau status..." 
                       class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none w-full placeholder-slate-500">
                <button type="button" onclick="clearSearch()" id="btnClearSearch" class="text-slate-400 hover:text-white text-xs px-1.5 py-0.5 rounded-md hover:bg-slate-700/50 transition hidden" title="Hapus pencarian (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <span class="text-[10px] font-mono font-bold text-slate-500 border border-slate-700/80 rounded px-1.5 py-0.5 ml-1 hidden sm:inline">ESC</span>
            </div>

            <!-- Status Quick Filter Chips -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 mr-1 hidden sm:inline-flex items-center gap-1">
                    <i class="fa-solid fa-filter text-[10px] text-amber-400"></i> Status:
                </span>
                <button type="button" onclick="setStatusFilter('ALL')" id="btnFilter_ALL" class="dr-chip-btn active-all" title="Tampilkan Semua Status Pekerjaan">
                    <i class="fa-solid fa-layer-group text-[10px]"></i>
                    <span>Semua</span>
                    <span class="px-2 py-0.5 rounded-full bg-black/25 text-[10px] font-mono font-black"><?= $wellJobCount ?></span>
                </button>
                <button type="button" onclick="setStatusFilter('COMPLETED')" id="btnFilter_COMPLETED" class="dr-chip-btn" title="Filter Hanya Sumur yang Sudah Selesai">
                    <i class="fa-solid fa-circle-check text-[10px] text-emerald-400"></i>
                    <span>Completed</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-mono font-black"><?= $completedCount ?></span>
                </button>
                <button type="button" onclick="setStatusFilter('PROGRESS')" id="btnFilter_PROGRESS" class="dr-chip-btn" title="Filter Sumur yang Masih Sedang Berjalan">
                    <i class="fa-solid fa-spinner fa-spin-pulse text-[10px] text-amber-400"></i>
                    <span>Progress</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-mono font-black"><?= $progressCount ?></span>
                </button>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 1: RINGKASAN PINTAR (COMPACT NO-SCROLL TABLE + ACCORDION LOGS)     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="viewContainer_compact" class="space-y-4">
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            
            <!-- Table Header Banner (High Contrast & Clear) -->
            <div class="bg-slate-850 border-b-2 border-amber-500/40 py-3 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-xs bg-amber-500/20 text-amber-300 font-mono px-3 py-1 rounded-full font-bold border border-amber-500/40">
                        <span id="renderedWellCount"><?= count($reports) ?></span> Sumur
                    </span>
                    <span class="font-black text-xs sm:text-sm uppercase tracking-wider text-white">
                        RINGKASAN OPERASI SUMUR <span class="text-amber-400 font-extrabold"><?= esc($rig['kode']) ?></span> — PERIODE <span class="text-sky-300 font-extrabold"><?= strtoupper($bulanList[$bulan] ?? '') ?> <?= $tahun ?></span>
                    </span>
                </div>
                <!-- Accordion Quick Triggers -->
                <div class="flex items-center gap-2 text-xs font-sans font-bold">
                    <button type="button" onclick="expandAllCompactLogs()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 transition flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-angles-down text-amber-400"></i>
                        <span>Buka Semua Log</span>
                    </button>
                    <button type="button" onclick="collapseAllCompactLogs()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 transition flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-angles-up text-amber-400"></i>
                        <span>Tutup Semua</span>
                    </button>
                </div>
            </div>

            <!-- Table Body -->
            <div class="w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700" id="compactWellTable">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10.5px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th class="py-2.5 px-2 w-12 border border-slate-700 bg-[#0B1E4A]">No</th>
                            <th class="py-2.5 px-4 border border-slate-700 bg-[#0B1E4A] min-w-[170px] text-left">
                                <div class="flex items-center gap-1.5 justify-start">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-xs"></i>
                                    <span>Tempat / Nama Lokasi Sumur</span>
                                </div>
                            </th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-32 text-center">
                                <div class="flex items-center gap-1.5 justify-center">
                                    <i class="fa-solid fa-calendar-days text-sky-400 text-xs"></i>
                                    <span>Periode Tanggal</span>
                                </div>
                            </th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-20 text-right">Jarak</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-24 text-right text-lime-400">MIRU (Jam)</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-24 text-right text-lime-300">OPS (Jam)</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0E2A66] w-24 text-right text-yellow-300">SBWC (Jam)</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0E2A66] w-24 text-right text-rose-400">UNPAID (Jam)</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0E2A66] w-24 text-right text-emerald-400 font-black">Total (Jam)</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-28 text-center">Status</th>
                            <th class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num" id="compactTableBody">
                        <?php if (empty($reports)): ?>
                        <tr id="emptyRowCompact">
                            <td colspan="11" class="py-16 text-center text-slate-500 font-sans">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto text-slate-400 text-xl shadow-inner">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-400">Belum ada pekerjaan sumur untuk periode ini</p>
                                    <p class="text-xs text-slate-500">Klik tombol di bawah untuk mendaftarkan pekerjaan sumur baru.</p>
                                    <a href="<?= base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}") ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span>Tambah Sumur Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $idx => $rep): 
                                $repId   = $rep['id'];
                                $dt      = $dtDetails[$repId] ?? [];
                                $logs    = $dailyLogs[$repId] ?? [];
                                $hasLogs = !empty($logs);
                                $isCompleted = ($rep['status_job'] === 'JOB COMPLETED');

                                // Unpaid sum (Rig Downtime & Tool Downtime)
                                $unpaidSum = (float)($dt[1] ?? 0) + (float)($dt[2] ?? 0);
                                // SBWC sum
                                $sbwcSum = 0;
                                foreach ($kategoriList as $k) {
                                    if ($k['tipe'] === 'SBWC') {
                                        $sbwcSum += (float)($dt[$k['id']] ?? 0);
                                    }
                                }
                                if ($sbwcSum == 0 && (float)$rep['total_dt'] > $unpaidSum) {
                                    $sbwcSum = (float)$rep['total_dt'] - $unpaidSum;
                                }
                            ?>
                            <!-- PARENT WELL ROW -->
                            <tr class="hover:bg-slate-850/90 transition-all cursor-pointer well-item-row" 
                                id="well-row-<?= $repId ?>"
                                data-status="<?= $isCompleted ? 'COMPLETED' : 'PROGRESS' ?>"
                                data-wellname="<?= strtolower(esc($rep['nama_lokasi'] ?? '')) ?>"
                                onclick="toggleCompactWell(<?= $repId ?>)">
                                <td class="py-2.5 px-2 text-center border border-slate-800 bg-slate-900/60">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="font-bold text-slate-400 text-xs"><?= $rep['no_well'] ?></span>
                                        <?php if ($hasLogs): ?>
                                            <i id="compact-icon-<?= $repId ?>" class="fa-solid fa-chevron-down text-[9px] text-blue-400 transition-transform duration-200"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 font-bold text-white text-xs border border-slate-800 font-sans">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 <?= $isCompleted ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.5)]' : 'bg-amber-400 animate-ping' ?>"></span>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('daily-report/detail/' . $repId) ?>" 
                                               onclick="event.stopPropagation()"
                                               class="group/link inline-flex items-center gap-1.5 text-sky-300 hover:text-amber-300 transition-colors text-sm font-black"
                                               title="Buka Halaman Rincian Penuh Sumur <?= esc($rep['nama_lokasi'] ?? '') ?>">
                                                <span><?= esc($rep['nama_lokasi'] ?? 'Sumur #' . $rep['no_well']) ?></span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-60 group-hover/link:opacity-100 group-hover/link:translate-x-0.5 transition-all text-amber-400"></i>
                                            </a>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-sans font-medium">
                                                <i class="fa-solid fa-map-pin text-rose-400 text-[10px]"></i>
                                                <span>Tempat: <strong class="text-slate-200"><?= esc($rep['nama_lokasi'] ?? '-') ?></strong></span>
                                            </div>
                                        </div>
                                        <?php if ($hasLogs): ?>
                                            <a href="<?= base_url('daily-report/detail/' . $repId) ?>"
                                               onclick="event.stopPropagation()"
                                               class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold transition flex items-center gap-1 ml-auto flex-shrink-0"
                                               title="Buka rincian <?= count($logs) ?> catatan log harian">
                                                <i class="fa-solid fa-calendar-day text-[9px] text-amber-400"></i>
                                                <span><?= count($logs) ?> hari</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-center text-xs text-slate-300 border border-slate-800 font-sans">
                                    <div class="font-bold text-sky-200 tracking-wide text-xs">
                                        <i class="fa-regular fa-calendar-check text-[10px] text-sky-400 mr-1"></i><?= $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-' ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        s/d <?= $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '<span class="text-amber-400 font-bold">Sekarang</span>' ?>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-right text-xs text-slate-300 border border-slate-800">
                                    <?= (float)$rep['jarak'] > 0 ? number_format((float)$rep['jarak'], 0) . ' KM' : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-xs text-lime-400 border border-slate-800 bg-lime-950/10">
                                    <?= (float)$rep['miru_jam'] > 0 ? number_format((float)$rep['miru_jam'], 2) : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-xs text-lime-300 border border-slate-800 bg-lime-950/10">
                                    <?= (float)$rep['ops_jam'] > 0 ? number_format((float)$rep['ops_jam'], 2) : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-right text-xs font-bold text-yellow-300 border border-slate-800 bg-amber-950/10">
                                    <?= $sbwcSum > 0 ? number_format($sbwcSum, 2) : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-right text-xs font-bold text-rose-400 border border-slate-800 bg-rose-950/10">
                                    <?= $unpaidSum > 0 ? number_format($unpaidSum, 2) : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-right font-black text-sm text-emerald-400 border border-slate-800 bg-emerald-950/20">
                                    <?= (float)$rep['total_jam'] > 0 ? number_format((float)$rep['total_jam'], 2) : '-' ?>
                                </td>
                                <td class="py-2.5 px-3 text-center border border-slate-800 font-sans whitespace-nowrap">
                                    <?php if ($isCompleted): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> COMPLETED
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/15 text-amber-400 border border-amber-500/30 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> PROGRESS
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-3 text-center border border-slate-800 whitespace-nowrap" onclick="event.stopPropagation()">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Halaman Detail Sumur Penuh -->
                                        <a href="<?= base_url('daily-report/detail/' . $rep['id']) ?>" class="w-7 h-7 rounded-lg bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-500/40 flex items-center justify-center transition shadow-sm" title="Buka Halaman Rincian Lengkap Sumur">
                                            <i class="fa-solid fa-up-right-from-square text-xs"></i>
                                        </a>
                                        <!-- Quick Modal Look -->
                                        <button type="button" onclick="openWellQuickLook(<?= htmlspecialchars(json_encode([
                                            'no_well' => $rep['no_well'],
                                            'nama_lokasi' => $rep['nama_lokasi'] ?? '',
                                            'tanggal_mulai' => $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-',
                                            'tanggal_selesai' => $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '-',
                                            'jarak' => (float)$rep['jarak'],
                                            'miru_jam' => (float)$rep['miru_jam'],
                                            'ops_jam' => (float)$rep['ops_jam'],
                                            'sbwc_jam' => $sbwcSum,
                                            'unpaid_jam' => $unpaidSum,
                                            'total_jam' => (float)$rep['total_jam'],
                                            'status_job' => $rep['status_job'],
                                            'remark' => $rep['remark'] ?? '',
                                            'edit_url' => base_url('daily-report/edit/' . $rep['id']),
                                            'log_count' => count($logs)
                                        ]), ENT_QUOTES, 'UTF-8') ?>)" class="w-7 h-7 rounded-lg bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 flex items-center justify-center transition" title="Quick Look">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                        <!-- Edit -->
                                        <a href="<?= base_url('daily-report/edit/' . $rep['id']) ?>" class="w-7 h-7 rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 flex items-center justify-center transition" title="Edit Data Sumur">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <!-- Log Harian Direct Link -->
                                        <a href="<?= base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}") ?>" class="w-7 h-7 rounded-lg bg-cyan-600/20 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/30 flex items-center justify-center transition" title="Input Log Harian">
                                            <i class="fa-solid fa-calendar-plus text-xs"></i>
                                        </a>
                                        <!-- Delete -->
                                        <form action="<?= base_url('daily-report/hapus/' . $rep['id']) ?>" method="POST" onsubmit="return confirm('Hapus data sumur <?= esc($rep['nama_lokasi'] ?? '') ?>?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 flex items-center justify-center transition" title="Hapus Sumur">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- NESTED LOG HARIAN ACCORDION DRAWER -->
                            <?php if ($hasLogs): ?>
                            <tr id="compact-logs-<?= $repId ?>" class="hidden bg-slate-950/80 border-t border-b border-blue-900/40">
                                <td colspan="11" class="p-3 pl-8">
                                    <div class="rounded-2xl bg-slate-900/90 border border-slate-700/80 p-3.5 space-y-2.5 shadow-inner">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs pb-2.5 border-b border-slate-800">
                                            <div class="flex items-center gap-2 font-bold text-white">
                                                <i class="fa-solid fa-timeline text-blue-400"></i>
                                                <span>Rincian Log Harian: <strong class="text-yellow-300"><?= esc($rep['nama_lokasi'] ?? '') ?></strong> (<?= count($logs) ?> Hari Operasi)</span>
                                            </div>
                                            <a href="<?= base_url("daily-report/log-harian/{$rigId}/{$bulan}/{$tahun}") ?>" class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5 bg-cyan-950/30 px-2.5 py-1 rounded-lg border border-cyan-800/40">
                                                <span>+ Tambah / Edit Log Hari Ini</span>
                                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>

                                        <table class="w-full text-xs text-left border-collapse border border-slate-800 font-num">
                                            <thead class="bg-[#0A1A3F] text-slate-300 text-[10px] uppercase font-extrabold text-center border-b border-slate-700">
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
                                                    <th class="py-1.5 px-2 border border-slate-800 text-center text-rose-400">UNPAID Rig</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right font-black text-yellow-300">Total DT</th>
                                                    <th class="py-1.5 px-2 border border-slate-800 text-right font-black text-emerald-400">Total Jam</th>
                                                    <th class="py-1.5 px-3 border border-slate-800 text-left font-sans min-w-[200px]">Remark Uraian</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-800/60 text-slate-300 text-[11px]">
                                                <?php foreach ($logs as $li => $lg): 
                                                    $lgDt = (float)$lg['total_dt'];
                                                    $lgHrs = (float)$lg['total_hrs'];
                                                ?>
                                                <tr class="hover:bg-slate-800/40 transition">
                                                    <td class="py-1.5 px-2 text-center text-slate-500 font-mono border border-slate-800 bg-slate-900/50"><?= $li + 1 ?></td>
                                                    <td class="py-1.5 px-3 text-center text-sky-300 font-sans border border-slate-800 font-bold"><?= date('d/m/Y', strtotime($lg['tanggal'])) ?></td>
                                                    <td class="py-1.5 px-3 text-left text-slate-200 font-sans border border-slate-800 font-bold">
                                                        <i class="fa-solid fa-map-pin text-[9px] text-rose-400 mr-1"></i><?= esc($rep['nama_lokasi'] ?? '-') ?>
                                                    </td>
                                                    <td class="py-1.5 px-2 text-right text-lime-400 border border-slate-800"><?= (float)$lg['miru_jam'] > 0 ? number_format((float)$lg['miru_jam'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right text-lime-300 border border-slate-800"><?= (float)$lg['ops_jam'] > 0 ? number_format((float)$lg['ops_jam'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_rain'] > 0 ? 'text-yellow-300 font-bold' : 'text-slate-600' ?>"><?= (float)$lg['dt_rain'] > 0 ? number_format((float)$lg['dt_rain'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= ((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad']) > 0 ? 'text-yellow-300 font-bold' : 'text-slate-600' ?>"><?= ((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad']) > 0 ? number_format((float)$lg['dt_dry_road'] + (float)$lg['dt_dry_pad'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_daylight'] > 0 ? 'text-yellow-300 font-bold' : 'text-slate-600' ?>"><?= (float)$lg['dt_daylight'] > 0 ? number_format((float)$lg['dt_daylight'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_3rd_party'] > 0 ? 'text-yellow-300 font-bold' : 'text-slate-600' ?>"><?= (float)$lg['dt_3rd_party'] > 0 ? number_format((float)$lg['dt_3rd_party'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-center border border-slate-800 <?= (float)$lg['dt_rig'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-600' ?>"><?= (float)$lg['dt_rig'] > 0 ? number_format((float)$lg['dt_rig'], 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right font-bold text-yellow-300 border border-slate-800"><?= $lgDt > 0 ? number_format($lgDt, 2) : '-' ?></td>
                                                    <td class="py-1.5 px-2 text-right font-bold text-emerald-400 border border-slate-800"><?= $lgHrs > 0 ? number_format($lgHrs, 2) : '-' ?></td>
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
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs text-center">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-center font-sans font-black uppercase text-amber-300 border border-slate-700">
                                TOTAL AKUMULASI OPERASI (<span id="footerWellCount"><?= count($reports) ?></span> SUMUR)
                            </td>
                            <td class="py-3 px-3 text-right font-black text-lime-400 border border-slate-700"><?= number_format($totalMiru, 2) ?></td>
                            <td class="py-3 px-3 text-right font-black text-lime-300 border border-slate-700"><?= number_format($totalOps, 2) ?></td>
                            <td colspan="2" class="py-3 px-3 text-center border border-slate-700 text-yellow-300 font-sans text-xs">Total Downtime: <strong class="text-white"><?= number_format($grandTotalDt, 2) ?>h</strong></td>
                            <td class="py-3 px-3 text-right font-black text-sm text-emerald-300 bg-[#0E2A66] border border-slate-700"><?= number_format($totalJam, 2) ?></td>
                            <td colspan="2" class="border border-slate-700 bg-[#0B1E4A]"></td>
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
                $wDt = (float)$rep['total_dt'];
                $wTotal = (float)$rep['total_jam'] > 0 ? (float)$rep['total_jam'] : ($wOps + $wMiru + $wDt);
                $wOpsPct = $wTotal > 0 ? round(($wOps / $wTotal) * 100) : 0;
            ?>
            <div class="p-5 rounded-3xl bg-slate-900 border border-slate-700/80 shadow-xl space-y-3.5 relative overflow-hidden group hover:border-blue-500/60 hover:shadow-2xl transition-all duration-300 well-card-item" 
                 data-status="<?= $isComp ? 'COMPLETED' : 'PROGRESS' ?>"
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
                    <span class="px-3 py-1 rounded-full text-[10.5px] font-black <?= $isComp ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/15 text-amber-400 border border-amber-500/30 animate-pulse' ?>">
                        <?= $isComp ? 'COMPLETED' : 'ON PROGRESS' ?>
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

                <!-- Stats 3-Grid -->
                <div class="grid grid-cols-3 gap-2 p-3 rounded-2xl bg-slate-950/80 border border-slate-800 text-center font-num">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">OPS</span>
                        <span class="text-sm font-black text-lime-300"><?= number_format($wOps, 1) ?>h</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">MIRU</span>
                        <span class="text-sm font-black text-lime-400"><?= number_format($wMiru, 1) ?>h</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Downtime</span>
                        <span class="text-sm font-black text-yellow-300"><?= number_format($wDt, 1) ?>h</span>
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
                        <i class="fa-solid fa-calendar-day mr-1 text-cyan-400"></i> <?= count($logs) ?> Hari Log
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openWellQuickLook(<?= htmlspecialchars(json_encode([
                            'no_well' => $rep['no_well'],
                            'nama_lokasi' => $rep['nama_lokasi'] ?? '',
                            'tanggal_mulai' => $rep['tanggal_mulai'] ? date('d/m/Y', strtotime($rep['tanggal_mulai'])) : '-',
                            'tanggal_selesai' => $rep['tanggal_selesai'] ? date('d/m/Y', strtotime($rep['tanggal_selesai'])) : '-',
                            'jarak' => (float)$rep['jarak'],
                            'miru_jam' => (float)$rep['miru_jam'],
                            'ops_jam' => (float)$rep['ops_jam'],
                            'sbwc_jam' => $wDt,
                            'unpaid_jam' => 0,
                            'total_jam' => (float)$rep['total_jam'],
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
    <!-- VIEW 3: MATRIKS DETAIL LENGKAP (FULL 24 KOLOM PERSIS FORMAT SYS)        -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="viewContainer_full" class="hidden space-y-4">
        <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <div class="bg-[#0B1E4A] text-white p-3 border-b border-slate-700 flex items-center justify-between">
                <span class="text-xs font-bold text-yellow-300 uppercase tracking-wider">Lembar Kerja Lengkap 24 Kolom (MIRU, OPS, 11 Pos SBWC, 2 Pos UNPAID)</span>
                <span class="text-xs text-slate-400">Format Lembar Kerja Excel Asli SYS</span>
            </div>

            <div class="overflow-x-auto custom-scrollbar max-h-[75vh]">
                <table class="w-full text-left border-collapse border border-slate-700 text-xs">
                    <thead class="bg-[#0A1A3F] text-white font-extrabold uppercase text-[10px] tracking-wider text-center select-none sticky top-0 z-10">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2 w-10 border border-slate-700 bg-[#0B1E4A]">No</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[100px] text-left">Location</th>
                            <th rowspan="2" class="py-2.5 px-2.5 border border-slate-700 bg-[#0B1E4A] w-20">Date</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0B1E4A] w-16">Distance</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0E2A66] text-lime-400 w-16">MIRU</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0E2A66] text-lime-300 w-16">OPS</th>
                            <th colspan="11" class="py-1 px-2 border border-slate-700 bg-[#0B1E4A] text-yellow-300">SBWC</th>
                            <th colspan="2" class="py-1 px-2 border border-slate-700 bg-[#0B1E4A] text-rose-300">UNPAID</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0E2A66] text-yellow-300 w-20">Total DT</th>
                            <th rowspan="2" class="py-2.5 px-2 border border-slate-700 bg-[#0E2A66] text-emerald-400 w-20 font-black">Total HRS</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[140px] text-left">Remark NPT</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] w-24 text-center">Status</th>
                        </tr>
                        <tr class="text-[9px] bg-[#001745] text-slate-300">
                            <th class="py-1.5 px-1 border border-slate-700">Rain</th>
                            <th class="py-1.5 px-1 border border-slate-700">Road</th>
                            <th class="py-1.5 px-1 border border-slate-700">Pad</th>
                            <th class="py-1.5 px-1 border border-slate-700">PHR Op</th>
                            <th class="py-1.5 px-1 border border-slate-700">Trans</th>
                            <th class="py-1.5 px-1 border border-slate-700">CE/PE</th>
                            <th class="py-1.5 px-1 border border-slate-700">3rd Party</th>
                            <th class="py-1.5 px-1 border border-slate-700">Daylight</th>
                            <th class="py-1.5 px-1 border border-slate-700">PHR Well</th>
                            <th class="py-1.5 px-1 border border-slate-700">Foam</th>
                            <th class="py-1.5 px-1 border border-slate-700">Idul Fitri</th>
                            <th class="py-1.5 px-1 border border-slate-700 text-rose-300">Rig</th>
                            <th class="py-1.5 px-1 border border-slate-700 text-rose-300">Tool</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php foreach ($reports as $rep): 
                            $dt = $dtDetails[$rep['id']] ?? [];
                            $fmt = fn($v) => $v > 0 ? (fmod($v, 1) !== 0.0 ? number_format($v, 2) : (int)$v) : '-';
                        ?>
                        <tr class="hover:bg-slate-800/80 transition">
                            <td class="py-2 px-2 text-center border border-slate-800 bg-slate-900/50 text-slate-400 font-mono"><?= $rep['no_well'] ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 font-sans"><?= esc($rep['nama_lokasi'] ?? '') ?></td>
                            <td class="py-2 px-2 text-center text-slate-400 border border-slate-800"><?= $rep['tanggal_mulai'] ? date('d/m/y', strtotime($rep['tanggal_mulai'])) : '-' ?></td>
                            <td class="py-2 px-2 text-right text-slate-400 border border-slate-800"><?= (float)$rep['jarak'] > 0 ? (int)$rep['jarak'] : '-' ?></td>
                            <td class="py-2 px-2 text-right font-bold text-lime-400 border border-slate-800 bg-lime-950/10"><?= $fmt((float)$rep['miru_jam']) ?></td>
                            <td class="py-2 px-2 text-right font-bold text-lime-300 border border-slate-800 bg-lime-950/10"><?= $fmt((float)$rep['ops_jam']) ?></td>
                            
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[3] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[4] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[5] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[13] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[11] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[14] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[10] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[6] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[8] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[12] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800"><?= $fmt((float)($dt[15] ?? 0)) ?></td>
                            
                            <td class="py-2 px-1 text-center border border-slate-800 text-rose-400 font-bold"><?= $fmt((float)($dt[1] ?? 0)) ?></td>
                            <td class="py-2 px-1 text-center border border-slate-800 text-rose-400 font-bold"><?= $fmt((float)($dt[2] ?? 0)) ?></td>

                            <td class="py-2 px-2 text-right font-bold text-yellow-300 border border-slate-800 bg-[#0E2A66]/30"><?= $fmt((float)$rep['total_dt']) ?></td>
                            <td class="py-2 px-2 text-right font-black text-emerald-400 border border-slate-800 bg-[#0E2A66]/30"><?= $fmt((float)$rep['total_jam']) ?></td>
                            <td class="py-2 px-3 text-left font-sans text-slate-300 border border-slate-800 truncate max-w-xs" title="<?= esc($rep['remark'] ?? '') ?>"><?= esc($rep['remark'] ?: '-') ?></td>
                            <td class="py-2 px-2 text-center border border-slate-800 font-sans text-[10px]">
                                <span class="px-2 py-0.5 rounded-full font-bold <?= $rep['status_job'] === 'JOB COMPLETED' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' ?>">
                                    <?= $rep['status_job'] === 'JOB COMPLETED' ? 'COMPLETED' : 'PROGRESS' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
</script>
<?= $this->endSection() ?>
