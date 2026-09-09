<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- ═══ Panduan Alur Kerja Sistem ══════════════════════════════ -->
    <div class="rounded-2xl overflow-hidden shadow-xl border border-blue-500/20 bg-gradient-to-br from-slate-800 via-slate-800 to-blue-950/40">
        <div class="px-5 pt-4 pb-3 border-b border-blue-500/20 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-route text-blue-400 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Panduan Alur Kerja Sistem</h3>
                    <p class="text-[10px] text-blue-300/70">Ikuti 3 langkah ini setiap kali ada pekerjaan sumur baru</p>
                </div>
            </div>
            <span class="hidden sm:flex items-center gap-1.5 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-700/60">

            <!-- LANGKAH 1 -->
            <a href="<?= base_url('daily-report') ?>" class="group flex items-start gap-4 p-4 hover:bg-blue-600/10 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center group-hover:bg-emerald-500/25 transition-colors">
                    <i class="fa-solid fa-file-waveform text-emerald-400 text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-extrabold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2 py-0.5 rounded">Langkah 1</span>
                    </div>
                    <p class="text-sm font-bold text-white">Data Pekerjaan Sumur</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Daftarkan setiap sumur yang dikerjakan rig: tanggal, lokasi, jarak, status pekerjaan.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-emerald-400 group-hover:gap-2 transition-all">
                        Buka Halaman <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

            <!-- LANGKAH 2 -->
            <a href="<?= base_url('daily-report/log-harian/1/' . date('n') . '/' . date('Y')) ?>" class="group flex items-start gap-4 p-4 hover:bg-blue-600/10 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-cyan-500/15 border border-cyan-500/25 flex items-center justify-center group-hover:bg-cyan-500/25 transition-colors">
                    <i class="fa-solid fa-pen-nib text-cyan-400 text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-extrabold text-cyan-400 uppercase tracking-widest bg-cyan-500/10 px-2 py-0.5 rounded">Langkah 2</span>
                    </div>
                    <p class="text-sm font-bold text-white">Log Harian Operasi</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Catat jam MIRU, OPS, dan semua pos downtime setiap hari. Data otomatis sinkron ke rekap.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-cyan-400 group-hover:gap-2 transition-all">
                        Buka Halaman <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

            <!-- LANGKAH 3 -->
            <a href="<?= base_url('npt') ?>" class="group flex items-start gap-4 p-4 hover:bg-blue-600/10 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center group-hover:bg-amber-500/25 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400 text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-widest bg-amber-500/10 px-2 py-0.5 rounded">Langkah 3</span>
                    </div>
                    <p class="text-sm font-bold text-white">Input Jam Downtime (NPT)</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">Verifikasi atau koreksi jam downtime per kategori (SBWC/UNPAID) dalam format spreadsheet detail.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-amber-400 group-hover:gap-2 transition-all">
                        Buka Halaman <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

        </div>

        <!-- Footer Hasil Rekap -->
        <div class="px-5 py-2.5 bg-slate-900/50 border-t border-slate-700/50 flex flex-wrap items-center gap-3">
            <span class="text-[10px] text-slate-500 font-medium">Data terinput otomatis masuk ke:</span>
            <a href="<?= base_url('monthly-report') ?>" class="flex items-center gap-1.5 text-[10px] font-semibold text-purple-400 hover:text-purple-300 transition">
                <i class="fa-solid fa-calendar-days text-[9px]"></i> Laporan Bulanan
            </a>
            <span class="text-slate-700">·</span>
            <a href="<?= base_url('rekap-tahunan') ?>" class="flex items-center gap-1.5 text-[10px] font-semibold text-cyan-400 hover:text-cyan-300 transition">
                <i class="fa-solid fa-chart-line text-[9px]"></i> Rekap Tahunan
            </a>
            <span class="text-slate-700">·</span>
            <a href="<?= base_url('rekap-tahunan/npt/2026') ?>" class="flex items-center gap-1.5 text-[10px] font-semibold text-rose-400 hover:text-rose-300 transition">
                <i class="fa-solid fa-business-time text-[9px]"></i> Rekap NPT Tahunan
            </a>
        </div>
    </div>

    <!-- ═══ Filter Periode ═══════════════════════════════════ -->

    <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-calendar-check text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Filter Periode Bulan &amp; Tahun</h3>
                <p class="text-xs text-slate-400">Pilih periode laporan armada</p>
            </div>
        </div>

        <form method="GET" action="<?= base_url('dashboard') ?>" class="flex flex-wrap items-center gap-2.5">
            <?php
            $bulanList = [
                1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
            ];
            ?>
            <!-- Selector Bulan -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-blue-400 text-xs"></i>
                    <span>Bulan:</span>
                </span>
                <select name="bulan" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($bulanList as $num => $nama): ?>
                        <option value="<?= $num ?>" class="bg-slate-900 text-white" <?= $bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector Tahun -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-days text-blue-400 text-xs"></i>
                    <span>Tahun:</span>
                </span>
                <select name="tahun" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" class="bg-slate-900 text-white" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Tombol Tampilkan -->
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 active:scale-95">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tampilkan</span>
            </button>
        </form>
    </div>

    <!-- ═══ 6 KPI Cards ══════════════════════════════════════ -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Well Job -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Well Job</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-bore-hole"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-3xl font-black text-white font-num tracking-tight"><?= number_format($totalWellJob, 0, ',', '.') ?></h4>
                <p class="text-[10px] text-blue-400 mt-0.5">Sumur dikerjakan</p>
            </div>
        </div>

        <!-- Rata Utilitas -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata Utilitas</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/15 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-3xl font-black text-indigo-400 font-num tracking-tight"><?= number_format($avgUtil, 1, ',', '.') ?><span class="text-lg">%</span></h4>
                <p class="text-[10px] text-slate-400 mt-0.5">OPS / Jam Kalender</p>
            </div>
        </div>

        <!-- Total NPT -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total NPT</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/15 text-rose-400 border border-rose-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-3xl font-black text-rose-400 font-num tracking-tight"><?= number_format($totalDowntime, 0, ',', '.') ?></h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Jam SBWC + UNPAID</p>
            </div>
        </div>

        <!-- MIRU -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total MIRU</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-3xl font-black text-amber-400 font-num tracking-tight"><?= number_format($totalMiru, 0, ',', '.') ?></h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Jam mobilisasi</p>
            </div>
        </div>

        <!-- OPS -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total OPS</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-3xl font-black text-emerald-400 font-num tracking-tight"><?= number_format($totalOps, 0, ',', '.') ?></h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Jam operasi aktif</p>
            </div>
        </div>

        <!-- Revenue -->
        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Realisasi Revenue</span>
                <div class="w-8 h-8 rounded-lg bg-teal-500/15 text-teal-400 border border-teal-500/30 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="mt-3">
                <h4 class="text-lg font-black text-teal-400 font-num tracking-tight">Rp <?= number_format($totalRevenueActual / 1_000_000_000, 1, ',', '.') ?>M</h4>
                <p class="text-[10px] text-slate-400 mt-0.5 font-num">Target: Rp <?= number_format($totalRevenueTarget / 1_000_000_000, 1, ',', '.') ?>M</p>
            </div>
        </div>
    </div>

    <!-- ═══ ROW 1: Bar Utilitas + Doughnut Downtime ══════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Bar Utilitas -->
        <div class="lg:col-span-2 p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="flex items-center justify-between mb-3 border-b border-slate-700 pb-2">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                        <i class="fa-solid fa-chart-bar text-blue-400 mr-1.5"></i>Utilitas per Rig (%)
                    </h3>
                    <p class="text-[11px] text-slate-400">OPS / Total jam kalender seluruh armada</p>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-700 text-[10px] font-bold text-slate-300"><?= count($summaries) ?> Unit Rig</span>
            </div>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="chartUtil"></canvas>
            </div>
        </div>

        <!-- Doughnut Downtime -->
        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="mb-3 border-b border-slate-700 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    <i class="fa-solid fa-circle-notch text-rose-400 mr-1.5"></i>Komposisi Downtime
                </h3>
                <p class="text-[11px] text-slate-400">Penyebab jam berhenti rig (NPT)</p>
            </div>
            <div class="relative flex-1 min-h-[260px] flex items-center justify-center">
                <canvas id="chartDowntime"></canvas>
            </div>
        </div>
    </div>

    <!-- ═══ ROW 2: Line NPT 2026 + Bar MIRU vs OPS ══════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Line: Tren NPT Bulanan 2026 -->
        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="mb-3 border-b border-slate-700 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    <i class="fa-solid fa-chart-line text-rose-400 mr-1.5"></i>Tren NPT Bulanan 2026
                </h3>
                <p class="text-[11px] text-slate-400">Total jam downtime per bulan (Jan–Ags 2026, sumber: Rekap NPT)</p>
            </div>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="chartNptTren"></canvas>
            </div>
        </div>

        <!-- Grouped Bar: MIRU vs OPS per Rig -->
        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="mb-3 border-b border-slate-700 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    <i class="fa-solid fa-chart-column text-amber-400 mr-1.5"></i>MIRU vs OPS per Rig (Jam)
                </h3>
                <p class="text-[11px] text-slate-400">Perbandingan jam mobilisasi dan operasi per rig</p>
            </div>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="chartMiruOps"></canvas>
            </div>
        </div>
    </div>

    <!-- ═══ ROW 3: Stacked Bar Revenue + Horizontal Well Job ══ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Stacked Bar: Revenue Target vs Aktual -->
        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="mb-3 border-b border-slate-700 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    <i class="fa-solid fa-sack-dollar text-teal-400 mr-1.5"></i>Revenue Target vs Aktual per Rig (Juta Rp)
                </h3>
                <p class="text-[11px] text-slate-400">Perbandingan target dan realisasi pendapatan per unit rig</p>
            </div>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>

        <!-- Horizontal Bar: Total Well Job per Rig -->
        <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
            <div class="mb-3 border-b border-slate-700 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    <i class="fa-solid fa-ranking-star text-emerald-400 mr-1.5"></i>Total Sumur per Rig
                </h3>
                <p class="text-[11px] text-slate-400">Jumlah well job tiap rig periode yang dipilih</p>
            </div>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="chartWell"></canvas>
            </div>
        </div>
    </div>

    <!-- ═══ ROW 4: SBWC vs UNPAID per Rig ══════════════════ -->
    <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 shadow flex flex-col">
        <div class="mb-3 border-b border-slate-700 pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                <i class="fa-solid fa-chart-area text-orange-400 mr-1.5"></i>Rincian NPT per Rig — SBWC vs UNPAID (Jam)
            </h3>
            <p class="text-[11px] text-slate-400">Detail jam downtime terklasifikasi per unit rig untuk periode yang dipilih</p>
        </div>
        <div class="relative min-h-[240px]">
            <canvas id="chartSbwcUnpaid"></canvas>
        </div>
    </div>

    <!-- ═══ Tabel Ringkasan ══════════════════════════════════ -->
    <div class="rounded-2xl bg-slate-800 border border-slate-700 shadow overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-850">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Tabel Ringkasan Seluruh Armada Rig</h3>
                <p class="text-[11px] text-slate-400">KPI: Reliabilitas, Ketersediaan, Utilisasi, Revenue</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('monthly-report/' . $bulan . '/' . $tahun) ?>" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-table"></i><span>Monthly Report</span>
                </a>
                <a href="<?= base_url('export/monthly-report/' . $bulan . '/' . $tahun) ?>" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-excel"></i><span>Export Excel</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead class="bg-slate-950 text-slate-300 font-bold uppercase text-[10px] border-b border-slate-700 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-4">Nama Rig</th>
                        <th class="py-2.5 px-2 text-center">Reliability</th>
                        <th class="py-2.5 px-2 text-center">Availability</th>
                        <th class="py-2.5 px-2 text-center">Utilization</th>
                        <th class="py-2.5 px-3 text-center">Sumur</th>
                        <th class="py-2.5 px-3 text-right">MIRU (Jam)</th>
                        <th class="py-2.5 px-3 text-right">OPS (Jam)</th>
                        <th class="py-2.5 px-3 text-right">SBWC (Jam)</th>
                        <th class="py-2.5 px-3 text-right">UNPAID (Jam)</th>
                        <th class="py-2.5 px-4 text-right">Revenue Aktual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-slate-200">
                    <?php foreach ($summaries as $s):
                        $rel = (float)($s['reliability'] ?? 0) * 100;
                        $ava = (float)($s['availability'] ?? 0) * 100;
                        $uti = (float)($s['utilization'] ?? 0) * 100;
                    ?>
                    <tr class="hover:bg-slate-750/60 transition">
                        <td class="py-2 px-4 font-bold text-white flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full <?= $uti > 75 ? 'bg-emerald-400' : ($uti > 40 ? 'bg-amber-400' : 'bg-slate-500') ?>"></span>
                            <span><?= esc($s['kode']) ?></span>
                        </td>
                        <td class="py-2 px-2 text-center font-num text-[13px] font-bold">
                            <span class="<?= $rel >= 90 ? 'text-emerald-400' : 'text-rose-400' ?>"><?= number_format($rel, 1) ?>%</span>
                        </td>
                        <td class="py-2 px-2 text-center font-num text-[13px] font-bold">
                            <span class="<?= $ava >= 90 ? 'text-emerald-400' : 'text-rose-400' ?>"><?= number_format($ava, 1) ?>%</span>
                        </td>
                        <td class="py-2 px-2 text-center font-num text-[13px] font-bold text-indigo-400">
                            <?= number_format($uti, 1) ?>%
                        </td>
                        <td class="py-2 px-3 text-center font-num font-bold text-white text-[13.5px]"><?= (int)($s['total_well_job'] ?? 0) ?></td>
                        <td class="py-2 px-3 text-right font-num font-semibold text-amber-400 text-[13.5px]"><?= number_format((float)($s['total_miru'] ?? 0), 1) ?></td>
                        <td class="py-2 px-3 text-right font-num font-semibold text-emerald-400 text-[13.5px]"><?= number_format((float)($s['total_ops'] ?? 0), 1) ?></td>
                        <td class="py-2 px-3 text-right font-num font-semibold text-orange-400 text-[13.5px]"><?= number_format((float)($s['sbwc_jam'] ?? 0), 1) ?></td>
                        <td class="py-2 px-3 text-right font-num font-semibold text-rose-400 text-[13.5px]"><?= number_format((float)($s['unpaid_jam'] ?? 0), 1) ?></td>
                        <td class="py-2 px-4 text-right font-num font-extrabold text-teal-400 text-[14px]">
                            Rp <?= number_format((float)($s['revenue_actual'] ?? 0), 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Shared palette ──────────────────────────────────────────────
    const PALETTE = [
        '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
        '#06b6d4','#ec4899','#84cc16','#14b8a6','#f97316',
        '#a78bfa','#34d399','#fbbf24','#f87171','#60a5fa',
        '#4ade80','#fb923c','#e879f9'
    ];
    const GRID_COLOR  = 'rgba(51,65,85,0.5)';
    const TICK_STYLE  = { color: '#94a3b8', font: { size: 10, weight: 'bold' } };

    // ── Data from PHP ───────────────────────────────────────────────
    const rigLabels    = <?= $rigLabels ?>;
    const rigUtilData  = <?= $rigUtilData ?>;
    const rigRevTarget = <?= $rigRevTarget ?>;
    const rigRevActual = <?= $rigRevActual ?>;
    const rigMiruData  = <?= $rigMiruData ?>;
    const rigOpsData   = <?= $rigOpsData ?>;
    const rigSbwcData  = <?= $rigSbwcData ?>;
    const rigUnpaidData= <?= $rigUnpaidData ?>;
    const rigWellData  = <?= $rigWellData ?>;
    const catLabels    = <?= $catLabels ?>;
    const catData      = <?= $catData ?>;
    const nptMonthLabels = <?= $nptMonthLabels ?>;
    const nptUnpaidTrend = <?= $nptUnpaidTrend ?>;
    const nptSbwcTrend   = <?= $nptSbwcTrend ?>;
    const nptTotalTrend  = <?= $nptTotalTrend ?>;

    // ══════════════════════════════════════════════════════
    // CHART 1 — Bar Utilitas per Rig
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartUtil'), {
        type: 'bar',
        data: {
            labels: rigLabels,
            datasets: [{
                label: 'Utilitas (%)',
                data: rigUtilData,
                backgroundColor: rigUtilData.map(v =>
                    v >= 75 ? 'rgba(59,130,246,0.9)'
                    : v >= 40 ? 'rgba(99,102,241,0.85)'
                    : 'rgba(148,163,184,0.55)'
                ),
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => ` Utilitas: ${c.raw}%`,
                        afterLabel: c => {
                            const v = c.raw;
                            return v >= 75 ? '✅ Baik' : v >= 40 ? '⚠️ Cukup' : '🔴 Rendah';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true, max: 100,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => v + '%' }
                },
                x: { grid: { display: false }, ticks: TICK_STYLE }
            }
        }
    });

    // ══════════════════════════════════════════════════════
    // CHART 2 — Doughnut Downtime
    // ══════════════════════════════════════════════════════
    const ctxDt = document.getElementById('chartDowntime');
    if (catData.length === 0) {
        const ctx2 = ctxDt.getContext('2d');
        ctx2.font = '12px sans-serif';
        ctx2.fillStyle = '#64748b';
        ctx2.textAlign = 'center';
        ctx2.fillText('Belum ada data downtime', ctxDt.width/2, ctxDt.height/2);
    } else {
        new Chart(ctxDt, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: PALETTE,
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#cbd5e1', font: { size: 10 }, boxWidth: 10, padding: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: c => {
                                const total = c.dataset.data.reduce((a,b)=>a+b,0);
                                return ` ${c.label}: ${c.raw} jam (${((c.raw/total)*100).toFixed(1)}%)`;
                            }
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // CHART 3 — Line: Tren NPT Bulanan 2026
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartNptTren'), {
        type: 'line',
        data: {
            labels: nptMonthLabels,
            datasets: [
                {
                    label: 'Total NPT (Jam)',
                    data: nptTotalTrend,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.12)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 5,
                    borderWidth: 2.5,
                },
                {
                    label: 'SBWC (Jam)',
                    data: nptSbwcTrend,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.10)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 4,
                    borderWidth: 2,
                },
                {
                    label: 'UNPAID (Jam)',
                    data: nptUnpaidTrend,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.10)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#8b5cf6',
                    pointRadius: 4,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' }, boxWidth: 12, padding: 12 }
                },
                tooltip: { callbacks: { label: c => ` ${c.dataset.label}: ${c.raw} jam` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => v + ' jam' }
                },
                x: { grid: { color: GRID_COLOR }, ticks: TICK_STYLE }
            }
        }
    });

    // ══════════════════════════════════════════════════════
    // CHART 4 — Grouped Bar: MIRU vs OPS per Rig
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartMiruOps'), {
        type: 'bar',
        data: {
            labels: rigLabels,
            datasets: [
                {
                    label: 'MIRU (Jam)',
                    data: rigMiruData,
                    backgroundColor: 'rgba(245,158,11,0.85)',
                    borderRadius: 4,
                },
                {
                    label: 'OPS (Jam)',
                    data: rigOpsData,
                    backgroundColor: 'rgba(16,185,129,0.85)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' }, boxWidth: 12, padding: 10 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => v + ' jam' }
                },
                x: { grid: { display: false }, ticks: { ...TICK_STYLE, maxRotation: 45 } }
            }
        }
    });

    // ══════════════════════════════════════════════════════
    // CHART 5 — Bar: Revenue Target vs Aktual (Juta Rp)
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartRevenue'), {
        type: 'bar',
        data: {
            labels: rigLabels,
            datasets: [
                {
                    label: 'Target (Juta Rp)',
                    data: rigRevTarget,
                    backgroundColor: 'rgba(99,102,241,0.75)',
                    borderRadius: 4,
                },
                {
                    label: 'Aktual (Juta Rp)',
                    data: rigRevActual,
                    backgroundColor: 'rgba(20,184,166,0.85)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' }, boxWidth: 12, padding: 10 }
                },
                tooltip: { callbacks: { label: c => ` ${c.dataset.label}: Rp ${c.raw.toFixed(1)} Juta` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => 'Rp' + v + 'Jt' }
                },
                x: { grid: { display: false }, ticks: { ...TICK_STYLE, maxRotation: 45 } }
            }
        }
    });

    // ══════════════════════════════════════════════════════
    // CHART 6 — Horizontal Bar: Total Well Job per Rig
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartWell'), {
        type: 'bar',
        data: {
            labels: rigLabels,
            datasets: [{
                label: 'Total Sumur',
                data: rigWellData,
                backgroundColor: rigWellData.map((v, i) => PALETTE[i % PALETTE.length]),
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ` ${c.raw} sumur` } }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => v + ' sumur' }
                },
                y: { grid: { display: false }, ticks: TICK_STYLE }
            }
        }
    });

    // ══════════════════════════════════════════════════════
    // CHART 7 — Stacked Bar: SBWC vs UNPAID per Rig
    // ══════════════════════════════════════════════════════
    new Chart(document.getElementById('chartSbwcUnpaid'), {
        type: 'bar',
        data: {
            labels: rigLabels,
            datasets: [
                {
                    label: 'SBWC (Jam)',
                    data: rigSbwcData,
                    backgroundColor: 'rgba(249,115,22,0.85)',
                    borderRadius: 3,
                    stack: 'npt',
                },
                {
                    label: 'UNPAID (Jam)',
                    data: rigUnpaidData,
                    backgroundColor: 'rgba(239,68,68,0.85)',
                    borderRadius: 3,
                    stack: 'npt',
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' }, boxWidth: 12, padding: 10 }
                },
                tooltip: { callbacks: { label: c => ` ${c.dataset.label}: ${c.raw} jam` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    grid: { color: GRID_COLOR },
                    ticks: { ...TICK_STYLE, callback: v => v + ' jam' }
                },
                x: { stacked: true, grid: { display: false }, ticks: TICK_STYLE }
            }
        }
    });

});
</script>
<?= $this->endSection() ?>
