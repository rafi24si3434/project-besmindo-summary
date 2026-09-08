<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4">
    <!-- Filter Navigation Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-800/90 border border-slate-700 shadow">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-file-waveform text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white"><?= esc($rig['kode']) ?> - SUMMARY REPORT PER WELL</h3>
                <p class="text-[11px] text-slate-400">Monitoring MIRU, OPS, Pos Downtime SBWC, UNPAID & Status Job</p>
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
            <button type="button" onclick="navigateGrid()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 active:scale-95" title="Buka data rig dan periode terpilih">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tampilkan</span>
            </button>

            <!-- Pemisah Garis -->
            <div class="hidden sm:block h-7 w-[1px] bg-slate-700 mx-1"></div>

            <!-- Tombol Tambah Sumur -->
            <a href="<?= base_url("daily-report/tambah/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 active:scale-95" title="Tambah sumur baru pada rig ini">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Sumur</span>
            </a>

            <!-- Tombol Export Excel -->
            <a href="<?= base_url("export/daily-report/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 active:scale-95" title="Download data dalam format Excel">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- Matriks Tabel Persis Format Asli Excel Gambar User -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700 shadow-md overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse border border-slate-700">
                <!-- Baris 1: Header Utama Grup Warna Persis Excel -->
                <thead class="text-slate-900 font-extrabold uppercase text-[10.5px] tracking-wider text-center select-none">
                    <tr>
                        <th rowspan="2" class="py-2.5 px-2 w-10 border border-slate-600 bg-sky-300 text-slate-900">No</th>
                        <th rowspan="2" class="py-2.5 px-3 border border-slate-600 bg-sky-300 text-slate-900 min-w-[90px]">Location</th>
                        <th rowspan="2" class="py-2.5 px-2.5 border border-slate-600 bg-sky-300 text-slate-900 min-w-[80px]">Date</th>
                        <th rowspan="2" class="py-2.5 px-2.5 border border-slate-600 bg-sky-300 text-slate-900 min-w-[85px]">Distance</th>
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-lime-400 text-slate-900 min-w-[65px]">MIRU</th>
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-lime-400 text-slate-900 min-w-[65px]">OPS</th>
                        
                        <!-- Grup SBWC (Kuning Terang Persis Excel) -->
                        <th colspan="10" class="py-1 px-2 border border-slate-600 bg-yellow-300 text-slate-900 text-center">SBWC</th>
                        
                        <!-- Grup UNPAID (Orange/Peach Persis Excel) -->
                        <th colspan="2" class="py-1 px-2 border border-slate-600 bg-amber-400 text-slate-900 text-center">UNPAID</th>
                        
                        <!-- Grup SHUTDOWN (Merah Persis Excel) -->
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-red-600 text-white min-w-[60px]">Shut Down</th>

                        <!-- Kolom Total Jam (Peach Pastel Persis Excel) -->
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-orange-200 text-slate-900 min-w-[65px]">Total DT</th>
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-emerald-200 text-slate-900 min-w-[65px]">Total HRS</th>
                        
                        <th rowspan="2" class="py-2.5 px-3 border border-slate-600 bg-sky-300 text-slate-900 min-w-[130px]">Remark NPT</th>
                        <th rowspan="2" class="py-2.5 px-3 border border-slate-600 bg-pink-200 text-slate-900 min-w-[110px]">Job</th>
                        <th rowspan="2" class="py-2.5 px-2 border border-slate-600 bg-slate-800 text-white w-14">Aksi</th>
                    </tr>

                    <!-- Baris 2: Sub-pos Kategori Persis Kolom Excel Asli -->
                    <tr class="text-[9.5px]">
                        <!-- Sub SBWC -->
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="SWA Rain">Rain (U.C)</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="Dry Road">Dry Road</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="Dry Well Pad">Dry Well Pad</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="PHR Operator">PHR Operator</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="Trans Sharing">Trans Sharing</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="CE/PE">CE/PE</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="3rd Party">3 Party</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="WO Daylight">WO Daylight</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="PHR Well & Accessories">PHR Well</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-yellow-200 text-slate-900" title="W.O Foam Unit">W.O Foam Unit</th>

                        <!-- Sub UNPAID -->
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-amber-300 text-slate-900" title="Repaire Rig & Equipment">Rig</th>
                        <th class="py-1.5 px-1.5 border border-slate-600 bg-amber-300 text-slate-900" title="BMS Tool / Personnel">BMS Tool</th>
                    </tr>

                    <!-- Baris 3: Tarif Rate (Baris Merah Nilai Kontrak Persis Excel) -->
                    <tr class="bg-slate-900 text-rose-400 font-num font-bold text-[10.5px] border-b-2 border-slate-600">
                        <td class="border border-slate-700 bg-slate-950"></td>
                        <td class="border border-slate-700 bg-slate-950"></td>
                        <td class="border border-slate-700 bg-slate-950 text-right pr-2">Rp</td>
                        <td class="border border-slate-700 bg-slate-950 text-right pr-2 font-extrabold text-white"><?= number_format($odr, 0, ',', '.') ?></td>
                        <td class="border border-slate-700 bg-slate-950 text-right pr-1"><?= number_format($rateMiru, 0, ',', '.') ?></td>
                        <td class="border border-slate-700 bg-slate-950 text-right pr-1"><?= number_format($rateOps, 0, ',', '.') ?></td>
                        <td class="border border-slate-700 bg-slate-950 text-right pr-1"><?= number_format($rateSbwc, 0, ',', '.') ?></td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950 text-center">-</td>
                        <td class="border border-slate-700 bg-slate-950"></td>
                        <td class="border border-slate-700 bg-slate-950"></td>
                        <td class="border border-slate-700 bg-slate-950"></td>
                    </tr>
                </thead>

                <!-- Body Data Baris Sumur -->
                <tbody class="text-slate-200 divide-y divide-slate-700 font-num">
                    <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="24" class="py-8 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-inbox text-2xl block mb-1 opacity-50"></i>
                            <span>Belum ada data sumur untuk bulan ini. Klik tombol "Tambah Sumur" di atas.</span>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $rep): 
                            $repId = $rep['id'];
                            $dt = $dtDetails[$repId] ?? [];
                            
                            // Map pos kategori
                            $dtRain      = (float)($dt[3] ?? 0);
                            $dtDryRoad   = (float)($dt[4] ?? 0);
                            $dtDryPad    = (float)($dt[5] ?? 0);
                            $dtPhrOp     = (float)($dt[13] ?? 0);
                            $dtSharing   = (float)($dt[11] ?? 0);
                            $dtCePe      = (float)($dt[14] ?? 0);
                            $dt3rdParty  = (float)($dt[10] ?? 0);
                            $dtDaylight  = (float)($dt[6] ?? 0);
                            $dtPhrWell   = (float)($dt[8] ?? 0);
                            $dtFoam      = (float)($dt[12] ?? 0);
                            $dtRig       = (float)($dt[1] ?? 0);
                            $dtTool      = (float)($dt[2] ?? 0);
                            $dtShutdown  = (float)($dt[15] ?? 0);

                            $sumDtRow = $rep['total_dt'];
                            $sumHrsRow = $rep['total_jam'];
                        ?>
                        <tr class="hover:bg-slate-750/70 transition">
                            <td class="py-2 px-2 text-center font-bold text-white border border-slate-700/80 bg-slate-850"><?= $rep['no_well'] ?></td>
                            <td class="py-2 px-3 font-bold text-sky-300 border border-slate-700/80 font-sans text-xs"><?= esc($rep['nama_lokasi'] ?? 'N/A') ?></td>
                            <td class="py-2 px-2.5 text-center text-xs text-slate-300 border border-slate-700/80">
                                <?= $rep['tanggal_mulai'] ? date('d/m/y', strtotime($rep['tanggal_mulai'])) : '-' ?>
                            </td>
                            <td class="py-2 px-2 text-right text-xs text-slate-300 border border-slate-700/80">
                                <?= $rep['jarak'] > 0 ? number_format($rep['jarak'], 0, ',', '.') : '-' ?>
                            </td>
                            
                            <!-- Jam MIRU & Jam OPS -->
                            <td class="py-2 px-2 text-right font-bold text-[14px] font-num text-lime-400 border border-slate-700/80 bg-lime-950/10"><?= number_format($rep['miru_jam'], 2) ?></td>
                            <td class="py-2 px-2 text-right font-bold text-[14px] font-num text-lime-400 border border-slate-700/80 bg-lime-950/10"><?= number_format($rep['ops_jam'], 2) ?></td>

                            <!-- 9 Pos SBWC -->
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtRain > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtRain > 0 ? number_format($dtRain, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtDryRoad > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtDryRoad > 0 ? number_format($dtDryRoad, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtDryPad > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtDryPad > 0 ? number_format($dtDryPad, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtPhrOp > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtPhrOp > 0 ? number_format($dtPhrOp, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtSharing > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtSharing > 0 ? number_format($dtSharing, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtCePe > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtCePe > 0 ? number_format($dtCePe, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dt3rdParty > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dt3rdParty > 0 ? number_format($dt3rdParty, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtDaylight > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtDaylight > 0 ? number_format($dtDaylight, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtPhrWell > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtPhrWell > 0 ? number_format($dtPhrWell, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtFoam > 0 ? 'font-bold text-yellow-300' : 'text-slate-600' ?>"><?= $dtFoam > 0 ? number_format($dtFoam, 2) : '-' ?></td>

                            <!-- 2 Pos UNPAID -->
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtRig > 0 ? 'font-bold text-rose-400' : 'text-slate-600' ?>"><?= $dtRig > 0 ? number_format($dtRig, 2) : '-' ?></td>
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtTool > 0 ? 'font-bold text-rose-400' : 'text-slate-600' ?>"><?= $dtTool > 0 ? number_format($dtTool, 2) : '-' ?></td>

                            <!-- Shutdown -->
                            <td class="py-2 px-1 text-center font-num text-[13px] border border-slate-700/80 <?= $dtShutdown > 0 ? 'font-bold text-red-400' : 'text-slate-600' ?>"><?= $dtShutdown > 0 ? number_format($dtShutdown, 2) : '-' ?></td>

                            <!-- Kolom Total DT & Total HRS Berwarna Persis Excel -->
                            <td class="py-2 px-2 text-right font-extrabold font-num text-[14px] border border-slate-700/80 bg-orange-950/30 text-orange-300">
                                <?= number_format($sumDtRow, 2) ?>
                            </td>
                            <td class="py-2 px-2 text-right font-extrabold font-num text-[14.5px] border border-slate-700/80 bg-emerald-950/30 text-emerald-300">
                                <?= number_format($sumHrsRow, 2) ?>
                            </td>

                            <!-- Remark NPT & Status Job -->
                            <td class="py-2 px-3 text-xs text-slate-400 border border-slate-700/80 font-sans truncate max-w-xs" title="<?= esc($rep['remark']) ?>">
                                <?= esc($rep['remark'] ?: '-') ?>
                            </td>
                            <td class="py-2.5 px-3 text-center border border-slate-700/80 font-sans whitespace-nowrap">
                                <?php if ($rep['status_job'] == 'JOB COMPLETED'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide bg-emerald-500/15 text-emerald-300 border border-emerald-500/40 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>COMPLETED</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide bg-amber-500/15 text-amber-300 border border-amber-500/40 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        <span>PROGRESS</span>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Tombol Aksi yang Elegan & Mudah Diklik -->
                            <td class="py-2.5 px-2 text-center border border-slate-700/80 font-sans whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="<?= base_url('daily-report/edit/' . $rep['id']) ?>" title="Edit Data Sumur" 
                                        class="w-7 h-7 rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 flex items-center justify-center transition-all shadow-sm active:scale-95">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    </a>
                                    <form action="<?= base_url('daily-report/hapus/' . $rep['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sumur ini?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" title="Hapus Data Sumur" 
                                            class="w-7 h-7 rounded-lg bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 flex items-center justify-center transition-all shadow-sm active:scale-95">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Total Akumulasi -->
                <tfoot class="bg-slate-950 font-bold text-white border-t-2 border-slate-600 text-xs font-num text-center">
                    <tr>
                        <td colspan="4" class="py-3 px-3 border border-slate-700 text-center font-sans font-extrabold uppercase bg-slate-900">
                            TOTAL AKUMULASI (HRS)
                        </td>
                        <td class="py-3 px-2 text-right border border-slate-700 text-lime-400 font-extrabold text-[13px] bg-slate-900"><?= number_format($totalMiru, 2) ?></td>
                        <td class="py-3 px-2 text-right border border-slate-700 text-lime-400 font-extrabold text-[13px] bg-slate-900"><?= number_format($totalOps, 2) ?></td>
                        
                        <!-- Total Pos Downtime -->
                        <td colspan="10" class="py-3 px-2 border border-slate-700 text-center font-sans text-xs text-yellow-300 bg-slate-900">
                            Rincian SBWC
                        </td>
                        <td colspan="2" class="py-3 px-2 border border-slate-700 text-center font-sans text-xs text-rose-300 bg-slate-900">
                            Rincian UNPAID
                        </td>
                        <td class="border border-slate-700 bg-slate-900">-</td>

                        <!-- Grand Total DT & HRS -->
                        <td class="py-3 px-2 text-right border border-slate-700 text-orange-300 font-black text-[14px] bg-orange-950/60">
                            <?= number_format($grandTotalDt, 2) ?>
                        </td>
                        <td class="py-3 px-2 text-right border border-slate-700 text-emerald-300 font-black text-[14px] bg-emerald-950/60">
                            <?= number_format($totalJam, 2) ?>
                        </td>

                        <td colspan="3" class="border border-slate-700 bg-slate-900"></td>
                    </tr>
                </tfoot>
            </table>
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
        window.location.href = `<?= base_url('daily-report') ?>/${rigId}/${bulan}/${tahun}`;
    }
</script>
<?= $this->endSection() ?>
