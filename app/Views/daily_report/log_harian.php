<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- ═══ Callout: Konteks Halaman ════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-cyan-500/8 border border-cyan-500/20">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-info text-cyan-400 text-sm"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-cyan-300 flex items-center gap-2">
                    <span class="px-1.5 py-0.5 rounded bg-cyan-500/20 text-[9px] font-extrabold tracking-widest text-cyan-400">STEP 2</span>
                    Log Harian Operasi
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Pilih tanggal &amp; isi jam MIRU, OPS, dan semua pos downtime per hari. Data <strong class="text-emerald-300">otomatis tersinkron</strong> ke Data Sumur, NPT, dan Rekap Bulanan. Pastikan Data Sumur di <strong class="text-emerald-300">Step 1</strong> sudah terisi terlebih dahulu.</p>
            </div>
        </div>
        <a href="<?= base_url('npt/' . ($rigId ?? 1) . '/' . date('n') . '/' . date('Y')) ?>"
           class="flex-shrink-0 flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md shadow-amber-600/20 transition active:scale-95">
            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
            <span>→ Input Jam Downtime</span>
        </a>
    </div>

    <!-- Filter Navigation Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-800/90 border border-slate-700 shadow">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pen-nib text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white"><?= esc($rig['kode']) ?> - INPUT LOG OPERASI HARIAN</h3>
                <p class="text-[11px] text-slate-400">Pencatatan aktivitas harian per tanggal: MIRU, OPS, Pos SBWC &amp; UNPAID (Otomatis sinkron ke Sumur &amp; NPT)</p>
            </div>
        </div>

        <!-- Selector Rig & Periode -->
        <div class="flex flex-wrap items-center gap-2.5">
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-oil-well text-blue-400 text-xs"></i>
                    <span>Rig:</span>
                </span>
                <select id="selectRig" onchange="navigateLog()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" class="bg-slate-900 text-white" <?= $r['id'] == $rigId ? 'selected' : '' ?>><?= esc($r['kode']) ?> - <?= esc($r['nama_rig']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm">
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

            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm">
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

            <a href="<?= base_url("daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 active:scale-95" title="Kembali ke Tabel Matriks Sumur">
                <i class="fa-solid fa-table-cells text-xs"></i>
                <span>Lihat Matriks Sumur</span>
            </a>
        </div>
    </div>

    <!-- FORM INPUT LOG HARIAN -->
    <div class="rounded-2xl bg-slate-800/90 border border-slate-700 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-700/80 bg-slate-850 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></span>
                <span class="text-sm font-bold text-white uppercase tracking-wider">Form Catatan Harian (00:00 - 24:00)</span>
            </div>
            <span class="text-xs text-slate-400 font-mono">Periode: <?= sprintf('%02d', $bulan) ?>/<?= $tahun ?></span>
        </div>

        <form action="<?= base_url('daily-report/simpan-log') ?>" method="POST" id="formDailyLog" class="p-5 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $tahun ?>">

            <!-- BAGIAN 1: IDENTITAS OPERASI HARI INI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-900/80 border border-slate-750">
                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-cyan-300 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span>Tanggal Laporan</span>
                    </label>
                    <input type="date" name="tanggal" id="inputTanggal" required
                        value="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, min((int)date('j'), cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun))) ?>"
                        min="<?= sprintf('%04d-%02d-01', $tahun, $bulan) ?>"
                        max="<?= sprintf('%04d-%02d-%02d', $tahun, $bulan, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun)) ?>"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-semibold focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                </div>

                <!-- Pilihan Sumur Aktif -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-sky-300 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-oil-well"></i>
                        <span>Pilih Sumur Pekerjaan</span>
                    </label>
                    <select name="daily_report_id" id="selectWell" required class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Pilih Sumur yang Sedang Dikerjakan --</option>
                        <?php foreach ($wells as $w): ?>
                            <option value="<?= $w['id'] ?>">
                                Well #<?= $w['no_well'] ?> - <?= esc($w['nama_lokasi'] ?? 'N/A') ?> (<?= $w['status_job'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Jarak KM -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-road"></i>
                        <span>Distance (KM)</span>
                    </label>
                    <input type="number" step="0.5" min="0" name="jarak" id="inputJarak" value="0" placeholder="0"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-num text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- BAGIAN 2: JAM OPERASI UTAMA (MIRU & OPS) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-lime-950/20 border border-lime-500/40">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-lime-400 uppercase tracking-wide">MIRU (Move In / Rig Up)</label>
                        <span class="text-[10px] text-lime-300/70">Maks. 24 Jam</span>
                    </div>
                    <input type="number" step="0.25" min="0" max="24" name="miru_jam" id="inputMiru" value="0" oninput="calcDailyTotal()" required
                        class="w-full px-3.5 py-2.5 bg-slate-950 border border-lime-500/40 rounded-xl text-lime-300 font-num font-black text-base focus:ring-2 focus:ring-lime-400 focus:outline-none">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi mobilisasi, rig up, persiapan sumur.</p>
                </div>

                <div class="p-4 rounded-xl bg-lime-950/20 border border-lime-500/40">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-lime-400 uppercase tracking-wide">OPS (Jam Operasi Kerja)</label>
                        <span class="text-[10px] text-lime-300/70">Maks. 24 Jam</span>
                    </div>
                    <input type="number" step="0.25" min="0" max="24" name="ops_jam" id="inputOps" value="0" oninput="calcDailyTotal()" required
                        class="w-full px-3.5 py-2.5 bg-slate-950 border border-lime-500/40 rounded-xl text-lime-300 font-num font-black text-base focus:ring-2 focus:ring-lime-400 focus:outline-none">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi pekerjaan workover/pengeboran produktif.</p>
                </div>
            </div>

            <!-- BAGIAN 3: RINCIAN POS DOWNTIME (PERSIS EXCEL) -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-750 space-y-4">
                <div class="border-b border-slate-750 pb-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-yellow-400"></i>
                        <span class="text-xs font-bold text-yellow-400 uppercase">Rincian Pos Downtime Hari Ini (SBWC &amp; UNPAID)</span>
                    </div>
                    <span class="text-xs text-slate-400">Total Downtime: <strong id="labelDtTotal" class="text-orange-300 font-num font-bold">0.00</strong> Jam</span>
                </div>

                <!-- Pos SBWC (Kuning) -->
                <div>
                    <span class="text-[10.5px] font-bold text-yellow-300 uppercase tracking-wider block mb-2">Pos Standby by Weather &amp; Client (SBWC):</span>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="SWA Rain">Rain (U.C)</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_rain" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="Dry Road">Dry Road</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_dry_road" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="Dry Well Pad">Dry Well Pad</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_dry_pad" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="PHR Operator">PHR Operator</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_phr_op" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="Trans Sharing">Trans Sharing</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_trans" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="CE/PE">CE/PE</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_ce_pe" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="3rd Party Total">3 Party</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_3rd_party" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="WO Daylight">WO Daylight</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_daylight" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="PHR Well">PHR Well</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_phr_well" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-slate-950 border border-slate-800">
                            <label class="block text-[10.5px] font-semibold text-slate-300 mb-1 truncate" title="W.O Foam Unit">W.O Foam Unit</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_foam" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-slate-700 rounded text-center text-white font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div class="p-2 rounded-lg bg-yellow-950/20 border border-yellow-500/40">
                            <label class="block text-[10.5px] font-bold text-yellow-300 mb-1 truncate" title="Idul Fitri, Pilkada & Shutdown Resmi">Idul Fitri &amp; Pilkada</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_shutdown" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1 bg-slate-900 border border-yellow-500/50 rounded text-center text-yellow-200 font-mono text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Pos UNPAID (Kerusakan Rig & BMS Tool) -->
                <div>
                    <span class="text-[10.5px] font-bold text-rose-400 uppercase tracking-wider block mb-2">Pos UNPAID (Downtime Tanggungan Kontraktor):</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-2.5 rounded-lg bg-rose-950/20 border border-rose-500/30">
                            <label class="block text-[11px] font-bold text-rose-300 mb-1">Repaire Rig &amp; Equipment (Unpaid)</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_rig" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1.5 bg-slate-950 border border-rose-500/50 rounded text-center text-rose-200 font-mono text-xs focus:ring-1 focus:ring-rose-400 focus:outline-none">
                        </div>
                        <div class="p-2.5 rounded-lg bg-rose-950/20 border border-rose-500/30">
                            <label class="block text-[11px] font-bold text-rose-300 mb-1">BMS Tool / Personnel (Unpaid)</label>
                            <input type="number" step="0.25" min="0" max="24" name="dt_tool" value="0" oninput="calcDailyTotal()" class="dt-input w-full px-2 py-1.5 bg-slate-950 border border-rose-500/50 rounded text-center text-rose-200 font-mono text-xs focus:ring-1 focus:ring-rose-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 4: TOTAL REKAP HARIAN (Maks 24 Jam) -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-slate-300 uppercase block">KONTROL VALIDASI 24 JAM:</span>
                    <span class="text-[11px] text-slate-400">Total Jam Hari Ini = MIRU + OPS + Total Downtime</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[11px] text-slate-400 block">TOTAL JAM HARI INI:</span>
                        <span id="labelGrandTotal" class="font-num font-black text-2xl text-emerald-400">0.00 Jam</span>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 5: REMARK UMUM & REMARK UNPAID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Remark NPT / Keterangan Operasi Umum</label>
                    <textarea name="remark_npt" rows="2" placeholder="Catatan cuaca, operasi, instruksi client..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-rose-400 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Remark UNPAID (Otomatis Sync ke NPT)</span>
                    </label>
                    <textarea name="remark_unpaid" rows="2" placeholder="Cth: Unpaid 1 HR Pump Rusak, Part patah..."
                        class="w-full px-3 py-2 bg-slate-950 border border-rose-500/40 rounded-xl text-rose-200 text-xs focus:ring-2 focus:ring-rose-400 focus:outline-none"></textarea>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-2 flex items-center justify-between border-t border-slate-750">
                <span class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-arrows-rotate text-cyan-400"></i>
                    <span>Setelah disimpan, data hari ini otomatis meng-update sumur terkait dan matriks NPT Harian.</span>
                </span>
                <button type="submit" class="px-7 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-cyan-500/25 transition flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>SIMPAN LOG HARIAN</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL RIWAYAT LOG TERCATAT BULAN INI -->
    <div class="rounded-2xl bg-slate-800/90 border border-slate-700 shadow-md overflow-hidden space-y-3">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between bg-slate-850">
            <h4 class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-list-check text-cyan-400"></i>
                <span>Riwayat Log Harian Tercatat Bulan Ini (<?= count($recentLogs) ?> Hari)</span>
            </h4>
        </div>

        <div class="overflow-x-auto custom-scrollbar p-2">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-950 text-slate-300 font-semibold uppercase text-[10.5px] border-b border-slate-700">
                    <tr>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">Sumur</th>
                        <th class="py-2.5 px-2 text-right">Distance</th>
                        <th class="py-2.5 px-2 text-right text-lime-400">MIRU</th>
                        <th class="py-2.5 px-2 text-right text-lime-400">OPS</th>
                        <th class="py-2.5 px-2 text-right text-orange-300">Total DT</th>
                        <th class="py-2.5 px-2 text-right text-emerald-300">Total HRS</th>
                        <th class="py-2.5 px-3">Remark UNPAID</th>
                        <th class="py-2.5 px-3">Remark Operasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-slate-300 font-num">
                    <?php if (empty($recentLogs)): ?>
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-inbox text-2xl block mb-1 opacity-40"></i>
                            <span>Belum ada catatan log harian bulan ini. Gunakan form di atas untuk mengisi.</span>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $rl): ?>
                        <tr class="hover:bg-slate-750/50 transition">
                            <td class="py-2 px-3 font-bold text-cyan-300 font-sans whitespace-nowrap"><?= date('d-M-y', strtotime($rl['tanggal'])) ?></td>
                            <td class="py-2 px-3 font-semibold text-sky-300 font-sans whitespace-nowrap">Well #<?= $rl['no_well'] ?> - <?= esc($rl['nama_lokasi']) ?></td>
                            <td class="py-2 px-2 text-right"><?= (float)$rl['jarak'] > 0 ? number_format((float)$rl['jarak'], 0) . ' KM' : '-' ?></td>
                            <td class="py-2 px-2 text-right text-lime-400 font-bold"><?= (float)$rl['miru_jam'] > 0 ? number_format((float)$rl['miru_jam'], 2) : '-' ?></td>
                            <td class="py-2 px-2 text-right text-lime-300 font-bold"><?= (float)$rl['ops_jam'] > 0 ? number_format((float)$rl['ops_jam'], 2) : '-' ?></td>
                            <td class="py-2 px-2 text-right text-orange-300 font-bold"><?= (float)$rl['total_dt'] > 0 ? number_format((float)$rl['total_dt'], 2) : '-' ?></td>
                            <td class="py-2 px-2 text-right text-emerald-300 font-bold"><?= (float)$rl['total_hrs'] > 0 ? number_format((float)$rl['total_hrs'], 2) : '-' ?></td>
                            <td class="py-2 px-3 text-[11px] text-rose-300 font-sans truncate max-w-xs" title="<?= esc($rl['remark_unpaid']) ?>">
                                <?= esc($rl['remark_unpaid'] ?: '-') ?>
                            </td>
                            <td class="py-2 px-3 text-[11px] text-slate-400 font-sans truncate max-w-xs" title="<?= esc($rl['remark_npt']) ?>">
                                <?= esc($rl['remark_npt'] ?: '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateLog() {
        const rigId = document.getElementById('selectRig').value;
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('daily-report/log-harian') ?>/${rigId}/${bulan}/${tahun}`;
    }

    function calcDailyTotal() {
        const miru = parseFloat(document.getElementById('inputMiru').value) || 0;
        const ops  = parseFloat(document.getElementById('inputOps').value) || 0;

        let dtSum = 0;
        document.querySelectorAll('.dt-input').forEach(inp => {
            const val = parseFloat(inp.value) || 0;
            dtSum += val;
        });

        const grandTotal = miru + ops + dtSum;

        document.getElementById('labelDtTotal').textContent = dtSum.toFixed(2);
        const grandEl = document.getElementById('labelGrandTotal');

        let colorClass = 'text-emerald-400';
        if (grandTotal > 24) {
            colorClass = 'text-rose-400 animate-pulse';
        } else if (grandTotal === 24) {
            colorClass = 'text-emerald-400 font-black';
        } else {
            colorClass = 'text-blue-400';
        }
        grandEl.className = `font-num font-black text-2xl ${colorClass}`;
        grandEl.textContent = `${grandTotal.toFixed(2)} Jam`;
    }

    // Hitung saat halaman dimuat
    document.addEventListener('DOMContentLoaded', calcDailyTotal);
</script>
<?= $this->endSection() ?>
