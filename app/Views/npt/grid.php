<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- ═══ Callout: Konteks Halaman ════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-amber-500/8 border border-amber-500/20">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-info text-amber-400 text-sm"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-300 flex items-center gap-2">
                    <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-[9px] font-extrabold tracking-widest text-amber-400">STEP 3</span>
                    Input Jam Downtime (NPT)
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Halaman ini untuk <strong class="text-amber-300">verifikasi &amp; koreksi</strong> jam downtime per kategori (SBWC &amp; UNPAID) dalam format spreadsheet. Data dari <strong class="text-cyan-300">Log Harian (Step 2)</strong> sudah otomatis terisi di sini — edit jika perlu, lalu simpan untuk sinkronkan.</p>
            </div>
        </div>
        <a href="<?= base_url('daily-report/log-harian/' . ($rigId ?? 1) . '/' . $bulan . '/' . $tahun) ?>"
           class="flex-shrink-0 flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md shadow-cyan-600/20 transition active:scale-95">
            <i class="fa-solid fa-pen-nib text-xs"></i>
            <span>← Log Harian Operasi</span>
        </a>
    </div>

    <!-- Filter Navigation Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <i class="fa-solid fa-calendar-day text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-white"><?= esc($rig['kode']) ?> — Input Jam Downtime (NPT)</h3>
                <p class="text-xs text-slate-400">Verifikasi &amp; koreksi jam downtime per kategori SBWC/UNPAID · Data otomatis terisi dari Log Harian &amp; tersinkron ke Monthly Report</p>
            </div>
        </div>

        <!-- Rig & Period Selector Toolbar -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Selector Rig -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
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
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-blue-400 text-xs"></i>
                    <span>Bulan:</span>
                </span>
                <select id="selectBulan" onchange="navigateGrid()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php 
                    $bulanList = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    foreach ($bulanList as $num => $nama): ?>
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

            <!-- Pemisah Garis -->
            <div class="hidden sm:block h-7 w-[1px] bg-slate-700 mx-1"></div>

            <!-- Tombol Export Excel -->
            <a href="<?= base_url("export/npt/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 active:scale-95">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- Quick Stat KPI Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-rose-400">Total Unpaid</span>
                <h4 class="text-xl font-bold text-white mt-1"><?= number_format($totalUnpaid, 2) ?> <span class="text-xs font-normal text-slate-400">Jam</span></h4>
            </div>
            <i class="fa-solid fa-ban text-2xl text-rose-400/40"></i>
        </div>

        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-400">Total SBWC</span>
                <h4 class="text-xl font-bold text-white mt-1"><?= number_format($totalSBWC, 2) ?> <span class="text-xs font-normal text-slate-400">Jam</span></h4>
            </div>
            <i class="fa-solid fa-people-group text-2xl text-amber-400/40"></i>
        </div>

        <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-400">Total Jam Downtime</span>
                <h4 class="text-xl font-bold text-white mt-1"><?= number_format($totalDT, 2) ?> <span class="text-xs font-normal text-slate-400">Jam</span></h4>
            </div>
            <i class="fa-solid fa-clock-rotate-left text-2xl text-blue-400/40"></i>
        </div>

        <div class="p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-400">Sinkronisasi Daily</span>
                <h4 class="text-xs font-bold text-indigo-200 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Terkoneksi Aktif</span>
                </h4>
            </div>
            <i class="fa-solid fa-link text-2xl text-indigo-400/40"></i>
        </div>
    </div>

    <!-- ═══ Ringkasan Visual Downtime Bulan Ini ═══════════════════ -->
    <?php
    // Hitung total per kategori dari existingData untuk chart
    $chartLabels = [];
    $chartData   = [];
    $chartColors = [];
    $colorMap = [
        'UNPAID' => ['rgba(239,68,68,0.85)',  'rgba(239,68,68,0.3)'],
        'SBWC'   => ['rgba(245,158,11,0.85)', 'rgba(245,158,11,0.3)'],
    ];
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
            $chartLabels[] = $k['nama'];
            $chartData[]   = round($kTotal, 2);
            $chartColors[] = $k['tipe'] === 'UNPAID' ? 'rgba(239,68,68,0.8)' : 'rgba(245,158,11,0.8)';
        }
    }
    $hasChartData = !empty($chartData);
    ?>
    <?php if ($hasChartData): ?>
    <div class="rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-700/80 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-amber-400"></i>
                    Ringkasan Downtime Bulan <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?>
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Total jam downtime per kategori (dari data yang sudah tersimpan)</p>
            </div>
            <div class="flex items-center gap-3 text-[10px] font-semibold">
                <span class="flex items-center gap-1.5 text-rose-400"><span class="w-3 h-2 rounded-sm bg-rose-500/70 inline-block"></span> UNPAID</span>
                <span class="flex items-center gap-1.5 text-amber-400"><span class="w-3 h-2 rounded-sm bg-amber-500/70 inline-block"></span> SBWC</span>
            </div>
        </div>
        <div class="p-5">
            <div class="space-y-2.5">
                <?php
                $maxVal = max($chartData);
                foreach ($chartLabels as $i => $label):
                    $val   = $chartData[$i];
                    $color = $chartColors[$i];
                    $pct   = $maxVal > 0 ? round(($val / $maxVal) * 100) : 0;
                    $totalPct = $totalDT > 0 ? round(($val / $totalDT) * 100, 1) : 0;
                    $isUnpaid = str_contains($color, '239,68');
                ?>
                <div class="flex items-center gap-3">
                    <div class="w-36 flex-shrink-0 text-[11px] font-semibold text-slate-300 truncate" title="<?= esc($label) ?>"><?= esc($label) ?></div>
                    <div class="flex-1 bg-slate-700/50 rounded-full h-5 overflow-hidden">
                        <div class="h-full rounded-full flex items-center px-2 transition-all duration-500"
                             style="width:<?= $pct ?>%; background:<?= $color ?>; min-width:<?= $val > 0 ? '2rem' : '0' ?>">
                            <span class="text-[10px] font-bold text-white whitespace-nowrap"><?= number_format($val, 1) ?> jam</span>
                        </div>
                    </div>
                    <div class="w-14 flex-shrink-0 text-right text-[10px] font-bold <?= $isUnpaid ? 'text-rose-400' : 'text-amber-400' ?>"><?= $totalPct ?>%</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="rounded-2xl bg-slate-800/60 border border-dashed border-slate-600 p-6 text-center">
        <i class="fa-solid fa-chart-bar text-3xl text-slate-600 mb-3"></i>
        <p class="text-sm font-semibold text-slate-400">Belum Ada Data Downtime Tersimpan</p>
        <p class="text-[11px] text-slate-500 mt-1">Isi jam downtime di tabel bawah, lalu klik <strong class="text-blue-400">Simpan &amp; Sinkronkan</strong>. Atau input melalui <a href="<?= base_url('daily-report/log-harian/' . $rigId . '/' . $bulan . '/' . $tahun) ?>" class="text-cyan-400 underline">Log Harian Operasi</a> terlebih dahulu.</p>
    </div>
    <?php endif; ?>

    <!-- Interactive Spreadsheet Matrix Form -->
    <form action="<?= base_url('npt/simpan') ?>" method="POST" id="formNpt">
        <?= csrf_field() ?>
        <input type="hidden" name="rig_id" value="<?= $rigId ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">

        <div class="rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-700/80 flex items-center justify-between bg-slate-850">
                <div class="flex items-center gap-3 text-xs text-slate-300 font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span>Setiap perubahan jam atau remark di sini akan <strong>otomatis tersinkron timbal balik</strong> dengan Daily Report.</span>
                </div>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan &amp; Sinkronkan</span>
                </button>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-950 text-slate-300 font-semibold uppercase text-[10px] border-b border-slate-700 sticky top-0 z-10">
                        <tr>
                            <th class="py-3 px-3 w-12 text-center border-r border-slate-800 sticky left-0 bg-slate-950 z-20">Tgl</th>
                            <?php foreach ($kategoriList as $k): ?>
                                <th class="py-2.5 px-2 text-center min-w-[85px] max-w-[120px] border-r border-slate-800/70" title="<?= esc($k['nama']) ?>">
                                    <span class="block truncate <?= $k['tipe'] == 'UNPAID' ? 'text-rose-400 font-bold' : 'text-amber-400' ?>"><?= esc($k['nama']) ?></span>
                                    <span class="text-[8px] font-mono opacity-60"><?= $k['tipe'] ?></span>
                                </th>
                            <?php endforeach; ?>

                            <th class="py-3 px-3 text-center min-w-[70px] border-r border-slate-800 bg-slate-900 font-bold text-white">Total Jam</th>
                            <th class="py-3 px-3 min-w-[180px] text-rose-300 border-r border-slate-800 bg-rose-950/20">Remark UNPAID (Sync Daily)</th>
                            <th class="py-3 px-3 min-w-[180px]">Remark Umum / SBWC</th>
                            <th class="py-3 px-2 w-16 text-center">3rd Party Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40 text-slate-300">
                        <?php for ($d = 1; $d <= $daysInMonth; $d++): 
                            $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                            $dailyLog = $dailyLogMap[$tglStr] ?? null;
                            $dayTotal = 0;
                            
                            // Remark Unpaid: prioritaskan dari daily_report_log jika ada
                            $remarkUnpaidVal = '';
                            if ($dailyLog && !empty($dailyLog['remark_unpaid'])) {
                                $remarkUnpaidVal = $dailyLog['remark_unpaid'];
                            } elseif (isset($existingData[$tglStr][1][0]['remark']) && !empty($existingData[$tglStr][1][0]['remark'])) {
                                $remarkUnpaidVal = $existingData[$tglStr][1][0]['remark'];
                            } elseif (isset($existingData[$tglStr][2][0]['remark']) && !empty($existingData[$tglStr][2][0]['remark'])) {
                                $remarkUnpaidVal = $existingData[$tglStr][2][0]['remark'];
                            }

                            // Remark Umum: dari daily_report_log remark_npt atau dari kategori SBWC
                            $remarkUmumVal = '';
                            if ($dailyLog && !empty($dailyLog['remark_npt'])) {
                                $remarkUmumVal = $dailyLog['remark_npt'];
                            } else {
                                foreach ($kategoriList as $k) {
                                    if ($k['tipe'] === 'SBWC' && !empty($existingData[$tglStr][$k['id']][0]['remark'])) {
                                        $remarkUmumVal = $existingData[$tglStr][$k['id']][0]['remark'];
                                        break;
                                    }
                                }
                            }

                            // Cek apakah ada rincian 3rd party
                            $has3rdPartyDetail = !empty($tpEntries[$tglStr]);
                            $total3rdPartyFromDetail = $has3rdPartyDetail ? array_sum($tpEntries[$tglStr]) : 0;
                        ?>
                        <tr class="hover:bg-slate-750/50 transition" data-day="<?= $d ?>">
                            <td class="py-2 px-3 text-center font-mono font-bold text-white border-r border-slate-800 sticky left-0 bg-slate-850 z-10">
                                <?= $d ?>
                            </td>

                            <?php foreach ($kategoriList as $k): 
                                $kId = $k['id'];
                                $existingVal = 0;

                                // Khusus kategori 3rd Party (ID 10): jika ada rincian per company, jumlahkan
                                if ($kId === 10 && $has3rdPartyDetail) {
                                    $existingVal = $total3rdPartyFromDetail;
                                } else {
                                    $existingVal = $existingData[$tglStr][$kId][0]['jam'] ?? 0;
                                }

                                if ($existingVal > 0) {
                                    $dayTotal += $existingVal;
                                }
                            ?>
                                <td class="p-1 border-r border-slate-800/70 text-center">
                                    <input type="number" step="0.25" min="0" max="24"
                                        name="npt[<?= $d ?>][<?= $kId ?>]"
                                        value="<?= $existingVal > 0 ? $existingVal : '' ?>"
                                        placeholder="-"
                                        oninput="recalcRow(<?= $d ?>)"
                                        class="npt-input-<?= $d ?> w-full px-1.5 py-1 text-center bg-slate-900/90 border <?= $k['tipe'] == 'UNPAID' ? 'border-rose-500/20 text-rose-200' : 'border-transparent text-white' ?> hover:border-slate-600 focus:border-blue-500 rounded-lg font-mono text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                </td>
                            <?php endforeach; ?>

                            <!-- Kolom Total Jam Hari Ini -->
                            <td class="py-2 px-3 text-center font-mono font-bold border-r border-slate-800 bg-slate-900/60" id="rowTotal_<?= $d ?>">
                                <span class="<?= $dayTotal > 0 ? ($dayTotal > 24 ? 'text-rose-400 font-extrabold' : 'text-blue-400') : 'text-slate-500' ?>">
                                    <?= number_format($dayTotal, 2) ?>
                                </span>
                            </td>

                            <!-- Remark UNPAID (Terhubung ke Daily) -->
                            <td class="p-1 border-r border-slate-800 bg-rose-950/10">
                                <input type="text" name="remark_unpaid[<?= $d ?>]" value="<?= esc($remarkUnpaidVal) ?>" placeholder="Remark UNPAID (Pump rusak, part, dll.)"
                                    class="w-full px-2.5 py-1 bg-slate-900/90 border border-rose-500/30 hover:border-rose-500 focus:border-rose-400 rounded-lg text-rose-200 text-xs focus:ring-1 focus:ring-rose-400 focus:outline-none transition placeholder:text-slate-600">
                            </td>

                            <!-- Remark Umum / SBWC -->
                            <td class="p-1 border-r border-slate-800">
                                <input type="text" name="remark[<?= $d ?>]" value="<?= esc($remarkUmumVal) ?>" placeholder="Remark umum/SBWC (hujan, sharing, dll.)"
                                    class="w-full px-2.5 py-1 bg-slate-900/90 border border-transparent hover:border-slate-600 focus:border-blue-500 rounded-lg text-white text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder:text-slate-600">
                            </td>

                            <!-- Tombol Modal / Expand 3rd Party Vendor Breakdown -->
                            <td class="p-1 text-center">
                                <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" 
                                    class="w-7 h-7 rounded-lg <?= $has3rdPartyDetail ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-slate-800 text-slate-400 border border-slate-700' ?> hover:bg-amber-500 hover:text-slate-900 transition flex items-center justify-center mx-auto" 
                                    title="Rincian 3rd Party per Perusahaan (BHI, HLS, BUKAKA, dll.)">
                                    <i class="fa-solid fa-building-flag text-[11px]"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Sub-baris Rincian 3rd Party per Perusahaan (Hidden by default) -->
                        <tr id="row3rdParty_<?= $d ?>" class="hidden bg-slate-900/95 border-y border-amber-500/30">
                            <td colspan="<?= count($kategoriList) + 5 ?>" class="p-3">
                                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-building-flag text-amber-400 text-sm"></i>
                                            <span class="font-bold text-white text-xs">RINCIAN DOWNTIME 3RD PARTY PER PERUSAHAAN (TGL <?= $d ?>)</span>
                                            <span class="text-[10px] text-slate-400">(Total jam di bawah ini akan otomatis menjadi nilai 3rd Party untuk tanggal ini)</span>
                                        </div>
                                        <button type="button" onclick="toggle3rdPartyRow(<?= $d ?>)" class="text-xs text-slate-400 hover:text-white">
                                            <i class="fa-solid fa-xmark"></i> Tutup
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-7 gap-2.5">
                                        <?php foreach ($thirdParties as $tp): 
                                            $tpVal = $tpEntries[$tglStr][$tp['id']] ?? 0;
                                        ?>
                                        <div class="p-2 rounded-lg bg-slate-900 border border-slate-800">
                                            <label class="block text-[10.5px] font-bold text-amber-300 truncate mb-1" title="<?= esc($tp['nama']) ?>">
                                                <?= esc($tp['nama']) ?>
                                            </label>
                                            <input type="number" step="0.25" min="0" max="24"
                                                name="tp[<?= $d ?>][<?= $tp['id'] ?>]"
                                                value="<?= $tpVal > 0 ? $tpVal : '' ?>"
                                                placeholder="0"
                                                oninput="calc3rdPartyTotal(<?= $d ?>)"
                                                class="tp-input-<?= $d ?> w-full px-2 py-1 bg-slate-950 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="flex items-center justify-between text-xs pt-1 text-slate-400">
                                        <span>Total Jam 3rd Party Tgl <?= $d ?>: <strong id="tpSumLabel_<?= $d ?>" class="text-amber-400 font-mono font-bold"><?= number_format($total3rdPartyFromDetail, 2) ?></strong> Jam</span>
                                        <span class="text-[10px] italic text-slate-500">Nilai total ini akan tersinkron otomatis ke Daily Report pos 3rd Party.</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot class="bg-slate-950 font-bold text-white border-t border-slate-700 sticky bottom-0 z-10 text-[11px]">
                        <tr>
                            <td class="py-3 px-3 text-center border-r border-slate-800 sticky left-0 bg-slate-950 z-20">TOTAL</td>
                            <?php foreach ($kategoriList as $k): 
                                $catSum = $totalsPerKat[$k['id']] ?? 0;
                            ?>
                                <td class="py-3 px-2 text-center border-r border-slate-800 font-mono text-emerald-400">
                                    <?= number_format($catSum, 2) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="py-3 px-3 text-center border-r border-slate-800 font-mono text-blue-400 bg-slate-900">
                                <?= number_format($totalDT, 2) ?>
                            </td>
                            <td class="py-3 px-3 text-xs text-rose-300 font-normal">Tersinkron ke Daily Report</td>
                            <td class="py-3 px-3 text-xs text-slate-400 font-normal" colspan="2">Total Akumulasi Periode Ini</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-4 border-t border-slate-700/80 flex items-center justify-between bg-slate-850">
                <span class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-arrows-rotate text-blue-400"></i>
                    <span>Sinkronisasi otomatis aktif: data UNPAID, 3rd Party, dan Remark saling terhubung.</span>
                </span>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan NPT Harian &amp; Sinkronkan</span>
                </button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateGrid() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('npt') ?>/${rigId}/${bulan}/${tahun}`;
    }

    function recalcRow(day) {
        const inputs = document.querySelectorAll(`.npt-input-${day}`);
        let sum = 0;
        inputs.forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) sum += val;
        });

        const targetEl = document.getElementById(`rowTotal_${day}`);
        if (targetEl) {
            let colorClass = sum > 0 ? (sum > 24 ? 'text-rose-400 font-extrabold' : 'text-blue-400') : 'text-slate-500';
            targetEl.innerHTML = `<span class="${colorClass}">${sum.toFixed(2)}</span>`;
        }
    }

    function toggle3rdPartyRow(day) {
        const row = document.getElementById(`row3rdParty_${day}`);
        if (row) {
            row.classList.toggle('hidden');
        }
    }

    function calc3rdPartyTotal(day) {
        const tpInputs = document.querySelectorAll(`.tp-input-${day}`);
        let tpSum = 0;
        tpInputs.forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) tpSum += val;
        });

        const label = document.getElementById(`tpSumLabel_${day}`);
        if (label) label.textContent = tpSum.toFixed(2);

        // Update juga cell kategori 3rd Party (ID 10) di baris utama
        const mainInput3rd = document.querySelector(`input[name="npt[${day}][10]"]`);
        if (mainInput3rd) {
            mainInput3rd.value = tpSum > 0 ? tpSum.toFixed(2) : '';
            recalcRow(day);
        }
    }
</script>
<?= $this->endSection() ?>
