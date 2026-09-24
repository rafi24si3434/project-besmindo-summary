<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--operations space-y-5">

    <!-- ═══ 1. NAVIGASI ALUR KERJA (3 LANGKAH) ═══════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Step 1: Rekap Sumur -->
        <a href="<?= base_url("daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" 
           class="p-3 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 hover:border-slate-600 transition flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-slate-700 text-slate-300 font-black text-xs flex items-center justify-center group-hover:bg-blue-500/20 group-hover:text-blue-400 transition">1</span>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 1</div>
                    <div class="text-xs font-bold text-white group-hover:text-blue-300 transition">Rekap Sumur (Well)</div>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition-transform"></i>
        </a>

        <!-- Step 2: Input Daily Report (Aktif) -->
        <div class="p-3 rounded-xl bg-cyan-950/50 border-2 border-cyan-500/80 shadow-lg shadow-cyan-950/40 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-cyan-500 text-slate-950 font-black text-xs flex items-center justify-center">2</span>
                <div>
                    <div class="text-[10px] font-black text-cyan-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span>Tahap 2 (Sedang Aktif)</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    </div>
                    <div class="text-xs font-black text-white">Input Daily Report</div>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 text-[10px] font-bold font-mono">00:00 - 24:00</span>
        </div>

        <!-- Step 3: Input NPT (Downtime) -->
        <a href="<?= base_url("npt/{$rigId}/{$bulan}/{$tahun}") ?>" 
           class="p-3 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 hover:border-slate-600 transition flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-slate-750 text-slate-300 font-black text-xs flex items-center justify-center group-hover:bg-amber-500/20 group-hover:text-amber-400 transition">3</span>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahap 3</div>
                    <div class="text-xs font-bold text-white group-hover:text-amber-300 transition">Input NPT (Downtime)</div>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition-transform"></i>
        </a>
    </div>

    <!-- ═══ 2. KONTROL RIG & PERIODE ══════════════════════════════════════════ -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-800/95 border border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-calendar-day text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-extrabold text-white tracking-wide"><?= esc($rig['kode']) ?> - <?= esc($rig['nama_rig']) ?></h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono">
                        <?= sprintf('%02d', $bulan) ?> / <?= $tahun ?>
                    </span>
                </div>
                <p class="text-[11.5px] text-slate-400">Form input manual log operasi harian per tanggal: MIRU, OPS, dan rincian pos downtime</p>
            </div>
        </div>

        <!-- Filter Selector -->
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 shadow-inner">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-oil-well text-blue-400 text-xs"></i>
                    <span>Rig:</span>
                </span>
                <select id="selectRig" onchange="navigateLog()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" class="bg-slate-900 text-white" <?= $r['id'] == $rigId ? 'selected' : '' ?>>
                            <?= esc($r['kode']) ?> - <?= esc($r['nama_rig']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 shadow-inner">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-blue-400 text-xs"></i>
                    <span>Bulan:</span>
                </span>
                <select id="selectBulan" onchange="navigateLog()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
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

            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 shadow-inner">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-days text-blue-400 text-xs"></i>
                    <span>Tahun:</span>
                </span>
                <select id="selectTahun" onchange="navigateLog()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" class="bg-slate-900 text-white" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <a href="<?= base_url("daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" 
               class="px-3 py-1.5 rounded-lg bg-slate-750 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs border border-slate-650 transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-table-cells text-xs text-cyan-400"></i>
                <span>Matriks Sumur</span>
            </a>
        </div>
    </div>

    <!-- ═══ 3. FORM INPUT DAILY REPORT (ENAK DILIHAT & MANUAL FRIENDLY) ═══════ -->
    <div id="formSection" class="rounded-2xl bg-slate-850 border border-slate-700 shadow-xl overflow-hidden">
        
        <!-- Header Form -->
        <div class="p-4 border-b border-slate-750 bg-slate-900 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                <h2 class="text-sm font-black text-white uppercase tracking-wider">Form Input Daily Report (00:00 - 24:00)</h2>
                <span id="formModeBadge" class="px-2 py-0.5 rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                    Mode: Input Baru
                </span>
            </div>
            <button type="button" onclick="resetFormToDefault()" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-750 border border-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-rotate-right text-[11px]"></i>
                <span>Reset Input Form</span>
            </button>
        </div>

        <form action="<?= base_url('daily-report/simpan-log') ?>" method="POST" id="formDailyLog" class="p-5 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">

            <!-- ── SEKSI 1: IDENTITAS OPERASI & SUMUR ── -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-750 space-y-3">
                <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
                    <i class="fa-solid fa-id-badge text-cyan-400 text-sm"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">1. Identitas Tanggal &amp; Sumur Pekerjaan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <!-- Tanggal -->
                    <div class="md:col-span-4">
                        <label for="inputTanggal" class="block text-xs font-bold text-slate-300 uppercase mb-1.5">
                            Tanggal Laporan <span class="text-rose-400">*</span>
                        </label>
                        <?php 
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                        $defaultDay = min((int)date('j'), $daysInMonth);
                        $defaultDateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $defaultDay);
                        ?>
                        <input type="date" name="tanggal" id="inputTanggal" required
                            value="<?= $defaultDateStr ?>"
                            min="<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>"
                            max="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, $daysInMonth) ?>"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Pilih Sumur -->
                    <div class="md:col-span-5">
                        <label for="selectWell" class="flex items-center justify-between block text-xs font-bold text-slate-300 uppercase mb-1.5">
                            <span>Pilih Sumur <span class="text-rose-400">*</span></span>
                            <button type="button" onclick="openQuickSumurModal()" class="px-2 py-0.5 bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 rounded font-bold text-[10px] transition border border-sky-500/30">+ Sumur Baru</button>
                        </label>
                        <select name="daily_report_id" id="selectWell" required 
                                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            <option value="">-- Pilih Sumur yang Dikerjakan --</option>
                            <?php foreach ($wells as $w): ?>
                                <option value="<?= $w['id'] ?>">
                                    Well #<?= $w['no_well'] ?> - <?= esc($w['nama_lokasi'] ?? 'N/A') ?> (<?= $w['status_job'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Distance (KM) -->
                    <div class="md:col-span-3">
                        <label for="inputJarak" class="block text-xs font-bold text-slate-300 uppercase mb-1.5">
                            Distance (Jarak KM)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" name="jarak" id="inputJarak" value="0"
                                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none pr-10">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold font-mono">KM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SEKSI 2: JAM OPERASI UTAMA (MIRU & OPS) ── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- MIRU Input -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-sky-500/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                            <label for="inputMiru" class="text-xs font-bold text-sky-300 uppercase tracking-wide">
                                MIRU (Move In / Rig Up)
                            </label>
                        </div>
                        <span class="text-[11px] text-slate-400 font-mono">Maks 24 Jam</span>
                    </div>
                    <div class="relative">
                        <input type="number" step="0.25" min="0" max="24" name="miru_jam" id="inputMiru" value="0" oninput="calcDailyTotal()" required
                            class="w-full px-4 py-3 bg-slate-950 border border-sky-500/40 rounded-xl text-sky-300 font-mono font-black text-xl text-center focus:ring-2 focus:ring-sky-400 focus:outline-none shadow-inner">
                        <span class="absolute right-3.5 top-3.5 text-xs text-slate-500 font-bold font-mono">JAM</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Waktu mobilisasi, rig up, dan persiapan sumur.</p>
                </div>

                <!-- OPS Input -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-emerald-500/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <label for="inputOps" class="text-xs font-bold text-emerald-300 uppercase tracking-wide">
                                OPS (Jam Operasi Kerja Efektif)
                            </label>
                        </div>
                        <span class="text-[11px] text-slate-400 font-mono">Maks 24 Jam</span>
                    </div>
                    <div class="relative">
                        <input type="number" step="0.25" min="0" max="24" name="ops_jam" id="inputOps" value="0" oninput="calcDailyTotal()" required
                            class="w-full px-4 py-3 bg-slate-950 border border-emerald-500/40 rounded-xl text-emerald-300 font-mono font-black text-xl text-center focus:ring-2 focus:ring-emerald-400 focus:outline-none shadow-inner">
                        <span class="absolute right-3.5 top-3.5 text-xs text-slate-500 font-bold font-mono">JAM</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Waktu pengerjaan sumur produktif (pengeboran / workover).</p>
                </div>
            </div>

            <!-- ── SEKSI 3: POS DOWNTIME SBWC (11 POS LENGKAP) ── -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-amber-500/30 space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-xs font-bold text-amber-300 uppercase tracking-wide">
                            3. Pos Downtime SBWC (Standby by Weather &amp; Client - Paid)
                        </span>
                    </div>
                    <div class="text-xs text-slate-300 font-mono">
                        Subtotal SBWC: <strong id="labelSbwcTotal" class="text-amber-300 font-bold">0.00</strong> Jam
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <!-- 1. Rain -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_rain" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="Rain (U.C) / SWA Rain">1. Rain (U.C)</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_rain" id="dt_rain" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 2. Dry Road -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_dry_road" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="Dry Road">2. Dry Road</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_dry_road" id="dt_dry_road" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 3. Dry Well Pad -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_dry_pad" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="Dry Well Pad">3. Dry Well Pad</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_dry_pad" id="dt_dry_pad" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 4. PHR Operator -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_phr_op" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="PHR Operator">4. PHR Operator</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_phr_op" id="dt_phr_op" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 5. Trans Sharing -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_trans" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="Trans Sharing">5. Trans Sharing</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_trans" id="dt_trans" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 6. CE/PE -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_ce_pe" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="CE/PE">6. CE/PE</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_ce_pe" id="dt_ce_pe" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 7. 3rd Party (Breakdown Collapsible) -->
                    <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-6 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-indigo-500/50 transition overflow-hidden">
                        <div class="flex items-center justify-between p-2.5 cursor-pointer hover:bg-slate-900/80 transition select-none" onclick="document.getElementById('tpBreakdownContainer').classList.toggle('hidden'); document.getElementById('tpIcon').classList.toggle('rotate-180');">
                            <label class="block text-[11px] font-bold text-slate-300 pointer-events-none">7. 3rd Party Breakdown</label>
                            <div class="flex items-center gap-3 pointer-events-none">
                                <span class="text-[11px] text-amber-300 font-mono font-bold"><span id="label3rdTotal">0.00</span> Jam</span>
                                <i id="tpIcon" class="fa-solid fa-chevron-down text-slate-500 text-[10px] transition-transform duration-200"></i>
                            </div>
                        </div>
                        <div id="tpBreakdownContainer" class="hidden border-t border-slate-800/60 p-3 bg-slate-900/30">
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                                <?php foreach($thirdParties ?? [] as $tp): ?>
                                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                                    <label class="block text-[9px] font-bold text-slate-400 mb-1 truncate"><?= esc($tp['nama']) ?></label>
                                    <input type="number" step="0.25" min="0" max="24" name="tp_jam[<?= $tp['id'] ?>]" value="0" oninput="calc3rdPartyTotal()"
                                        class="dt-tp-input w-full px-1.5 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-[10px] font-bold focus:ring-1 focus:ring-indigo-400 focus:outline-none">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" name="dt_3rd_party" id="dt_3rd_party" value="0" class="dt-input dt-sbwc">
                    </div>

                    <!-- 8. WO Daylight -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_daylight" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="WO Daylight">8. WO Daylight</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_daylight" id="dt_daylight" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 9. PHR Well -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_phr_well" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="PHR Well">9. PHR Well</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_phr_well" id="dt_phr_well" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 10. Foam Unit -->
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 focus-within:border-amber-400/60 transition">
                        <label for="dt_foam" class="block text-[11px] font-bold text-slate-300 mb-1 truncate" title="W.O Foam Unit">10. Foam Unit</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_foam" id="dt_foam" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-center text-white font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>

                    <!-- 11. Idul Fitri & Shutdown -->
                    <div class="p-2.5 rounded-xl bg-amber-950/20 border border-amber-500/40 sm:col-span-2 focus-within:border-amber-400 transition">
                        <label for="dt_shutdown" class="block text-[11px] font-bold text-amber-300 mb-1 truncate" title="Idul Fitri, Pilkada & Shutdown Resmi">11. Idul Fitri &amp; Shutdown</label>
                        <input type="number" step="0.25" min="0" max="24" name="dt_shutdown" id="dt_shutdown" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-sbwc w-full px-2 py-1.5 bg-slate-900 border border-amber-500/40 rounded-lg text-center text-amber-200 font-mono text-xs font-bold focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- ── SEKSI 4: POS DOWNTIME UNPAID ── -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-rose-500/30 space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                        <span class="text-xs font-bold text-rose-300 uppercase tracking-wide">
                            4. Pos UNPAID (Downtime Tanggungan Kontraktor - Memotong Pendapatan)
                        </span>
                    </div>
                    <div class="text-xs text-slate-300 font-mono">
                        Subtotal UNPAID: <strong id="labelUnpaidTotal" class="text-rose-300 font-bold">0.00</strong> Jam
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Repair Rig -->
                    <div class="p-3 rounded-xl bg-slate-950 border border-rose-500/30 focus-within:border-rose-400 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="dt_rig" class="text-xs font-bold text-rose-300">Repair Rig &amp; Equipment (Unpaid)</label>
                            <span class="text-[10.5px] text-rose-400/80 font-mono">Mesin, Winch, Pompa Rig</span>
                        </div>
                        <input type="number" step="0.25" min="0" max="24" name="dt_rig" id="dt_rig" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-unpaid w-full px-3 py-2 bg-slate-900 border border-rose-500/50 rounded-lg text-center text-rose-200 font-mono text-sm font-bold focus:ring-1 focus:ring-rose-400 focus:outline-none">
                    </div>

                    <!-- BMS Tool -->
                    <div class="p-3 rounded-xl bg-slate-950 border border-rose-500/30 focus-within:border-rose-400 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="dt_tool" class="text-xs font-bold text-rose-300">BMS Tool / Personnel (Unpaid)</label>
                            <span class="text-[10.5px] text-rose-400/80 font-mono">Alat BMS / Personel</span>
                        </div>
                        <input type="number" step="0.25" min="0" max="24" name="dt_tool" id="dt_tool" value="0" oninput="calcDailyTotal()"
                            class="dt-input dt-unpaid w-full px-3 py-2 bg-slate-900 border border-rose-500/50 rounded-lg text-center text-rose-200 font-mono text-sm font-bold focus:ring-1 focus:ring-rose-400 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- ── SEKSI 5: RINGKASAN REKAP TOTAL HARI INI ── -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-inner">
                <div>
                    <span class="text-xs font-bold text-slate-300 uppercase block">KONTROL VALIDASI 24 JAM:</span>
                    <span class="text-[11.5px] text-slate-400">
                        MIRU: <strong id="recapMiru" class="text-sky-300 font-mono font-bold">0.00</strong>j + 
                        OPS: <strong id="recapOps" class="text-emerald-300 font-mono font-bold">0.00</strong>j + 
                        Downtime: <strong id="recapDt" class="text-amber-300 font-mono font-bold">0.00</strong>j
                    </span>
                </div>

                <div class="flex items-center gap-4 text-right">
                    <div>
                        <span class="text-[10.5px] text-slate-400 uppercase font-bold block">TOTAL JAM HARI INI:</span>
                        <span id="labelGrandTotal" class="font-mono font-black text-2xl text-emerald-400">0.00 Jam</span>
                    </div>
                    <div id="badgeValidation" class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-bold text-slate-300 font-mono">
                        Belum Diisi
                    </div>
                </div>
            </div>

            <!-- ── SEKSI 6: CATATAN & REMARK ── -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-750 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Remark Operasi Umum -->
                    <div>
                        <label for="remarkNpt" class="block text-xs font-bold text-slate-300 uppercase mb-1.5">
                            Remark Operasi Umum (Cuaca / Instruksi / Progres)
                        </label>
                        <textarea name="remark_npt" id="remarkNpt" rows="2" placeholder="Catatan aktivitas lapangan hari ini..."
                            class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>

                    <!-- Remark UNPAID -->
                    <div>
                        <label for="remarkUnpaid" class="block text-xs font-bold text-rose-300 uppercase mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-xs"></i>
                            <span>Remark UNPAID (Diisi jika ada jam UNPAID)</span>
                        </label>
                        <textarea name="remark_unpaid" id="remarkUnpaid" rows="2" placeholder="Contoh: UNPAID 1.5 HR Perbaikan Pompa..."
                            class="w-full px-3 py-2.5 bg-slate-950 border border-rose-500/40 rounded-xl text-rose-200 text-xs focus:ring-2 focus:ring-rose-400 focus:outline-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- ── SEKSI 7: TOMBOL SIMPAN ── -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-750">
                <span class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-arrows-rotate text-cyan-400"></i>
                    <span>Data yang disimpan otomatis meng-update sumur terkait dan matriks NPT Harian.</span>
                </span>
                <button type="submit" id="btnSubmitDaily" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-cyan-600/25 transition active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>SIMPAN DAILY REPORT</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ═══ 4. TABEL RIWAYAT LOG TERCATAT BULAN INI ════════════════════════════ -->
    <div class="rounded-2xl bg-slate-850 border border-slate-700 shadow-md overflow-hidden space-y-3">
        <div class="p-4 border-b border-slate-750 bg-slate-900 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                    <i class="fa-solid fa-list-check text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-wider">
                        Riwayat Daily Report Bulan Ini (<?= count($recentLogs) ?> Hari)
                    </h4>
                    <p class="text-[11px] text-slate-400">Gunakan tombol "Muat" untuk mengedit kembali log atau tombol tempat sampah untuk menghapus</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar p-2">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-950 text-slate-300 font-bold uppercase text-[10.5px] border-b border-slate-750 tracking-wider">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Sumur</th>
                        <th class="py-3 px-2 text-right">Jarak</th>
                        <th class="py-3 px-2 text-right text-sky-400">MIRU (j)</th>
                        <th class="py-3 px-2 text-right text-emerald-400">OPS (j)</th>
                        <th class="py-3 px-2 text-right text-amber-400">Total DT (j)</th>
                        <th class="py-3 px-2 text-right text-white">Total Jam</th>
                        <th class="py-3 px-3">Remark UNPAID</th>
                        <th class="py-3 px-3">Remark Operasi</th>
                        <th class="py-3 px-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300 font-mono">
                    <?php if (empty($recentLogs)): ?>
                    <tr>
                        <td colspan="11" class="py-10 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-clipboard-list text-3xl block mb-2 opacity-30 text-slate-500"></i>
                            <span class="font-bold block text-sm text-slate-300">Belum ada catatan log harian pada bulan ini</span>
                            <span class="text-xs text-slate-500">Silakan gunakan formulir di atas untuk mengisi.</span>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($recentLogs as $rl): 
                            $totHrs = (float)$rl['total_hrs'];
                            $is24 = abs($totHrs - 24.0) < 0.01;
                        ?>
                        <tr class="hover:bg-slate-800/60 transition group">
                            <td class="py-2.5 px-3 text-center text-slate-500 font-bold"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 font-bold text-cyan-300 font-sans whitespace-nowrap">
                                <?= date('d-M-Y', strtotime($rl['tanggal'])) ?>
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-white font-sans whitespace-nowrap">
                                <span class="px-1.5 py-0.5 rounded bg-slate-800 text-sky-300 text-[10.5px] border border-slate-700 mr-1">
                                    Well #<?= $rl['no_well'] ?>
                                </span>
                                <span class="text-xs text-slate-300"><?= esc($rl['nama_lokasi'] ?? 'N/A') ?></span>
                            </td>
                            <td class="py-2.5 px-2 text-right text-slate-400">
                                <?= (float)$rl['jarak'] > 0 ? number_format((float)$rl['jarak'], 1) . ' KM' : '-' ?>
                            </td>
                            <td class="py-2.5 px-2 text-right text-sky-400 font-bold">
                                <?= (float)$rl['miru_jam'] > 0 ? number_format((float)$rl['miru_jam'], 2) : '-' ?>
                            </td>
                            <td class="py-2.5 px-2 text-right text-emerald-400 font-bold">
                                <?= (float)$rl['ops_jam'] > 0 ? number_format((float)$rl['ops_jam'], 2) : '-' ?>
                            </td>
                            <td class="py-2.5 px-2 text-right text-amber-400 font-bold">
                                <?= (float)$rl['total_dt'] > 0 ? number_format((float)$rl['total_dt'], 2) : '-' ?>
                            </td>
                            <td class="py-2.5 px-2 text-right whitespace-nowrap">
                                <span class="font-black px-1.5 py-0.5 rounded <?= $is24 ? 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20' : 'text-rose-400 bg-rose-500/10 border border-rose-500/20' ?>">
                                    <?= number_format($totHrs, 2) ?>j
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-sans max-w-[160px] truncate text-[11px] text-rose-300" title="<?= esc($rl['remark_unpaid']) ?>">
                                <?= esc($rl['remark_unpaid'] ?: '-') ?>
                            </td>
                            <td class="py-2.5 px-3 font-sans max-w-[180px] truncate text-[11px] text-slate-400" title="<?= esc($rl['remark_npt']) ?>">
                                <?= esc($rl['remark_npt'] ?: '-') ?>
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Muat Button -->
                                    <button type="button" 
                                            onclick='loadLogToForm(<?= json_encode($rl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="px-2.5 py-1 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/30 text-cyan-300 hover:text-white border border-cyan-500/30 text-[11px] font-bold font-sans transition flex items-center gap-1 active:scale-95" 
                                            title="Muat data ke form di atas">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Muat</span>
                                    </button>

                                    <!-- Hapus Button -->
                                    <form action="<?= base_url('daily-report/hapus-log/' . $rl['id']) ?>" method="POST" class="inline" onsubmit="return confirmHapusLog('<?= esc($rl['tanggal']) ?>', 'Well #<?= esc($rl['no_well']) ?>')">
                                        <?= csrf_field() ?>
                                        <button type="submit" 
                                                class="px-2 py-1 rounded-lg bg-rose-500/15 hover:bg-rose-500/30 text-rose-300 hover:text-white border border-rose-500/30 text-[11px] font-bold font-sans transition active:scale-95" 
                                                title="Hapus log ini">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
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
<!-- ═══ MODAL SUMUR BARU CEPAT (AJAX) ═══ -->
<div id="quickWellModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden transition-all duration-200">
    <div class="relative w-full max-w-md flex flex-col rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-800 bg-slate-850 flex items-center justify-between">
            <h3 class="text-lg font-black text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                Tambah Sumur Cepat
            </h3>
            <button type="button" onclick="closeQuickSumurModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 hover:text-rose-400 text-slate-400 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">No. Well <span class="text-rose-400">*</span></label>
                <input type="number" id="qw_no_well" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-1 focus:ring-sky-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Lokasi (Pilih Master Lokasi) <span class="text-rose-400">*</span></label>
                <select id="qw_lokasi_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-1 focus:ring-sky-500 outline-none">
                    <option value="">-- Pilih Lokasi --</option>
                    <?php foreach ($lokasiList as $lok): ?>
                        <option value="<?= $lok['id'] ?>"><?= esc($lok['nama_lokasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex justify-end gap-2">
            <button type="button" onclick="closeQuickSumurModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700">Batal</button>
            <button type="button" onclick="saveQuickSumur()" id="qw_btn_save" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-xs font-bold hover:bg-sky-500">Simpan & Gunakan</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openQuickSumurModal() {
        const modal = document.getElementById('quickWellModal');
        if(modal) modal.classList.remove('hidden');
    }
    
    function closeQuickSumurModal() {
        const modal = document.getElementById('quickWellModal');
        if(modal) modal.classList.add('hidden');
    }
    
    function saveQuickSumur() {
        const noWell = document.getElementById('qw_no_well').value;
        const lokasiId = document.getElementById('qw_lokasi_id').value;
        const btn = document.getElementById('qw_btn_save');
        
        if(!noWell || !lokasiId) {
            alert('Lengkapi No. Well dan Lokasi!');
            return;
        }
        
        btn.innerHTML = 'Menyimpan...';
        btn.disabled = true;
        
        const fd = new FormData();
        fd.append('rig_id', '<?= $rigId ?>');
        fd.append('bulan', '<?= $bulan ?>');
        fd.append('tahun', '<?= $tahun ?>');
        fd.append('no_well', noWell);
        fd.append('lokasi_id', lokasiId);
        
        fetch('<?= base_url('daily-report/simpan-sumur-cepat') ?>', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = 'Simpan & Gunakan';
            btn.disabled = false;
            
            if(data.status === 'success') {
                const sel = document.getElementById('selectWell');
                const opt = document.createElement('option');
                opt.value = data.id;
                opt.text = `Well #${noWell} - ${data.nama_lokasi} (Moving)`;
                sel.add(opt);
                sel.value = data.id;
                closeQuickSumurModal();
            } else {
                alert(data.message || 'Gagal menyimpan sumur baru.');
            }
        })
        .catch(err => {
            console.error(err);
            btn.innerHTML = 'Simpan & Gunakan';
            btn.disabled = false;
            alert('Terjadi kesalahan jaringan.');
        });
    }
    function navigateLog() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('daily-report/log-harian') ?>/${rigId}/${bulan}/${tahun}`;
    }

    function calc3rdPartyTotal() {
        let tpSum = 0;
        document.querySelectorAll('.dt-tp-input').forEach(inp => {
            tpSum += parseFloat(inp.value) || 0;
        });
        document.getElementById('label3rdTotal').textContent = tpSum.toFixed(2);
        document.getElementById('dt_3rd_party').value = tpSum;
        // Trigger the main calculation
        calcDailyTotal();
    }

    function calcDailyTotal() {
        const miru = parseFloat(document.getElementById('inputMiru').value) || 0;
        const ops  = parseFloat(document.getElementById('inputOps').value) || 0;

        let sbwcSum = 0;
        document.querySelectorAll('.dt-sbwc').forEach(inp => {
            sbwcSum += parseFloat(inp.value) || 0;
        });

        let unpaidSum = 0;
        document.querySelectorAll('.dt-unpaid').forEach(inp => {
            unpaidSum += parseFloat(inp.value) || 0;
        });

        const totalDt = sbwcSum + unpaidSum;
        const grandTotal = miru + ops + totalDt;

        // Tampilkan Subtotal
        document.getElementById('labelSbwcTotal').textContent = sbwcSum.toFixed(2);
        document.getElementById('labelUnpaidTotal').textContent = unpaidSum.toFixed(2);

        // Tampilkan Rekap
        document.getElementById('recapMiru').textContent = miru.toFixed(2);
        document.getElementById('recapOps').textContent = ops.toFixed(2);
        document.getElementById('recapDt').textContent = totalDt.toFixed(2);

        // Tampilkan Grand Total & Badge
        const grandEl = document.getElementById('labelGrandTotal');
        const badgeEl = document.getElementById('badgeValidation');
        const btnSubmit = document.getElementById('btnSubmitDaily');

        grandEl.textContent = `${grandTotal.toFixed(2)} Jam`;

        if (Math.abs(grandTotal - 24.0) < 0.01) {
            grandEl.className = 'font-mono font-black text-2xl text-emerald-400';
            badgeEl.className = 'px-3 py-1.5 rounded-lg bg-emerald-950/70 border border-emerald-500/60 text-xs font-bold text-emerald-300 font-mono';
            badgeEl.textContent = '✓ Pas 24.00 Jam (Valid)';
            btnSubmit.disabled = false;
            btnSubmit.className = 'w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-cyan-600/25 transition active:scale-95 flex items-center justify-center gap-2';
            btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk text-sm"></i> <span>SIMPAN DAILY REPORT</span>';
        } else if (grandTotal < 24.0) {
            const sisa = (24.0 - grandTotal).toFixed(2);
            grandEl.className = 'font-mono font-black text-2xl text-sky-400';
            badgeEl.className = 'px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-bold text-amber-300 font-mono';
            badgeEl.textContent = `Sisa ${sisa}j Belum Terisi`;
            btnSubmit.disabled = true;
            btnSubmit.className = 'w-full sm:w-auto px-8 py-3 bg-slate-700 text-slate-400 font-extrabold text-xs rounded-xl flex items-center justify-center gap-2 cursor-not-allowed';
            btnSubmit.innerHTML = `<i class="fa-solid fa-lock text-sm"></i> <span>SIMPAN (KURANG ${sisa}j)</span>`;
        } else {
            const lebih = (grandTotal - 24.0).toFixed(2);
            grandEl.className = 'font-mono font-black text-2xl text-rose-400 animate-pulse';
            badgeEl.className = 'px-3 py-1.5 rounded-lg bg-rose-950/80 border border-rose-500/80 text-xs font-black text-rose-300 font-mono';
            badgeEl.textContent = `⚠️ Lebih ${lebih}j (Melebihi 24j)`;
            btnSubmit.disabled = true;
            btnSubmit.className = 'w-full sm:w-auto px-8 py-3 bg-slate-700 text-rose-400 font-extrabold text-xs rounded-xl flex items-center justify-center gap-2 cursor-not-allowed';
            btnSubmit.innerHTML = `<i class="fa-solid fa-lock text-sm"></i> <span>SIMPAN (LEBIH ${lebih}j)</span>`;
        }
    }

    function resetFormToDefault() {
        document.getElementById('formDailyLog').reset();
        document.getElementById('inputMiru').value = '0';
        document.getElementById('inputOps').value = '0';
        document.getElementById('inputJarak').value = '0';
        document.querySelectorAll('.dt-input').forEach(inp => {
            inp.value = '0';
        });
        const badge = document.getElementById('formModeBadge');
        badge.className = 'px-2 py-0.5 rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold uppercase tracking-wider';
        badge.textContent = 'Mode: Input Baru';
        calcDailyTotal();
    }

    function loadLogToForm(data) {
        const badge = document.getElementById('formModeBadge');
        badge.className = 'px-2 py-0.5 rounded bg-amber-500/20 border border-amber-500/40 text-amber-300 text-[10px] font-bold uppercase tracking-wider';
        badge.textContent = `Mode: Edit Tanggal ${data.tanggal}`;

        document.getElementById('inputTanggal').value = data.tanggal;
        document.getElementById('selectWell').value = data.daily_report_id;
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

        // Clear all tp_jam fields first
        document.querySelectorAll('.dt-tp-input').forEach(inp => inp.value = 0);
        
        // Populate tp_jam fields
        let hasTpData = false;
        if (data.tp_breakdown) {
            for (const [tpId, jam] of Object.entries(data.tp_breakdown)) {
                const el = document.querySelector(`input[name="tp_jam[${tpId}]"]`);
                if (el) {
                    const jamVal = parseFloat(jam) || 0;
                    el.value = jamVal;
                    if (jamVal > 0) hasTpData = true;
                }
            }
        }

        // Auto-expand/collapse
        const tpContainer = document.getElementById('tpBreakdownContainer');
        const tpIcon = document.getElementById('tpIcon');
        if (hasTpData) {
            tpContainer.classList.remove('hidden');
            tpIcon.classList.add('rotate-180');
        } else {
            tpContainer.classList.add('hidden');
            tpIcon.classList.remove('rotate-180');
        }
        
        calc3rdPartyTotal();

        document.getElementById('remarkNpt').value = data.remark_npt || '';
        document.getElementById('remarkUnpaid').value = data.remark_unpaid || '';

        calcDailyTotal();

        const formSec = document.getElementById('formSection');
        formSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        formSec.classList.add('ring-2', 'ring-cyan-400');
        setTimeout(() => {
            formSec.classList.remove('ring-2', 'ring-cyan-400');
        }, 1200);
    }

    function confirmHapusLog(tanggal, wellLabel) {
        return confirm(`Hapus catatan log harian tanggal ${tanggal} (${wellLabel})?\n\nSubtotal sumur dan NPT akan dihitung ulang.`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        calcDailyTotal();
    });
</script>
<?= $this->endSection() ?>
