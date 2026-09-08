<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-5">
    <!-- Filter Navigation Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 p-4 rounded-xl bg-slate-800/90 border border-slate-700 shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-table-list text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">SUMMARY REPORT OPERATION RIG BMS</h3>
                <p class="text-xs text-slate-400">Periode Pelaporan: <strong class="text-yellow-400 font-mono text-sm"><?= $bulan ?>/<?= $tahun ?></strong></p>
            </div>
        </div>

        <!-- Period Selector Toolbar -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Selector Bulan -->
            <div class="flex items-center bg-slate-900 border border-slate-600 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar text-blue-400 text-xs"></i>
                    <span>Bulan:</span>
                </span>
                <select id="selectBulan" onchange="navigateReport()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
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
                <select id="selectTahun" onchange="navigateReport()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" class="bg-slate-900 text-white" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Tombol Tampilkan Data -->
            <button type="button" onclick="navigateReport()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5 active:scale-95" title="Buka data periode terpilih">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tampilkan</span>
            </button>

            <!-- Pemisah Garis -->
            <div class="hidden sm:block h-7 w-[1px] bg-slate-700 mx-1"></div>

            <!-- Form Hitung Ulang -->
            <form action="<?= base_url('monthly-report/hitung') ?>" method="POST" class="inline">
                <?= csrf_field() ?>
                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 active:scale-95" title="Kalkulasi ulang data bulan ini dari Daily Report & NPT">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    <span>Hitung Ulang</span>
                </button>
            </form>

            <!-- Tombol Export Excel -->
            <a href="<?= base_url("export/monthly-report/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 active:scale-95" title="Download Monthly Report format Excel asli">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-700 gap-2 overflow-x-auto">
        <button onclick="switchTab('summary')" id="tabBtn_summary" class="tab-btn px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 border-yellow-400 text-yellow-300 bg-slate-800 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-table text-xs"></i>
            <span>Summary Operation (RAU, MIRU, OPS, Revenue)</span>
        </button>
        <button onclick="switchTab('charts')" id="tabBtn_charts" class="tab-btn px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-chart-column text-xs text-sky-400"></i>
            <span>Grafik Analisis (NPT, RAU, MIRU, Cycle Time, Well Job)</span>
        </button>
        <button onclick="switchTab('odrTable')" id="tabBtn_odrTable" class="tab-btn px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-money-bill-1-wave text-xs"></i>
            <span>Daftar Nilai Kontrak ODR</span>
        </button>
        <button onclick="switchTab('nptAll')" id="tabBtn_nptAll" class="tab-btn px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
            <span>NPT All Rig (Breakdown Downtime)</span>
        </button>
    </div>

    <!-- TAB 1: SUMMARY OPERATION PERSIS EXCEL ASLI -->
    <div id="tabContent_summary" class="tab-content space-y-4">
        <div class="rounded-xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            
            <!-- Banner Judul Kuning Persis Excel -->
            <div class="bg-yellow-400 text-slate-950 py-2.5 px-4 text-center font-black text-sm uppercase tracking-wider border-b-2 border-yellow-500">
                SUMMARY REPORT OPERATION RIG BMS PERIODE <?= $bulanList[$bulan] ?> <?= $tahun ?>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10.5px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th rowspan="2" class="py-2 px-2.5 w-10 border border-slate-700 bg-[#0B1E4A]">NO</th>
                            <th rowspan="2" class="py-2 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[90px]">NAME RIG</th>
                            <th colspan="3" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66]">RAU</th>
                            <th rowspan="2" class="py-2 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">TOTAL MIRU (HRS)</th>
                            <th rowspan="2" class="py-2 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">TOTAL OPS</th>
                            <th rowspan="2" class="py-2 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">AVERAGE MIRU (HRS)</th>
                            <th rowspan="2" class="py-2 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[85px]">AVERAGE CYCLE TIME (HRS)</th>
                            <th rowspan="2" class="py-2 px-2 border border-slate-700 bg-[#0B1E4A] min-w-[70px]">TOTAL WELL JOB</th>
                            <th colspan="2" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66]">DOWNTIME</th>
                            <th colspan="2" class="py-1.5 px-3 border border-slate-700 bg-[#0E2A66]">REVENUE</th>
                            <th rowspan="2" class="py-2 px-2.5 border border-slate-700 bg-[#0B1E4A] min-w-[75px]">TOTAL HOURS</th>
                            <th rowspan="2" class="py-2 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[110px]">REMARK</th>
                        </tr>
                        <tr class="text-[9.5px] bg-[#0A1A3F] border-t border-slate-700 text-slate-300">
                            <th class="py-1 px-1.5 border border-slate-700 text-sky-300">RELIABILITY (%)</th>
                            <th class="py-1 px-1.5 border border-slate-700 text-sky-300">AVAILABILITY (%)</th>
                            <th class="py-1 px-1.5 border border-slate-700 text-sky-300">UTILIZATION (%)</th>
                            <th class="py-1 px-2 border border-slate-700 text-yellow-300">SBWC (HRS)</th>
                            <th class="py-1 px-2 border border-slate-700 text-rose-300">UNPAID (HRS)</th>
                            <th class="py-1 px-2 border border-slate-700 text-slate-300">INCENTIVE TARGET (Rp)</th>
                            <th class="py-1 px-2 border border-slate-700 text-emerald-300">ACTUAL (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php $no = 1; foreach ($summaries as $s): 
                            $rel = (float)($s['reliability'] ?? 0) * 100;
                            $ava = (float)($s['availability'] ?? 0) * 100;
                            $uti = (float)($s['utilization'] ?? 0) * 100;
                        ?>
                        <tr class="hover:bg-slate-800/80 transition">
                            <td class="py-2 px-2.5 text-center text-xs text-slate-400 border border-slate-800 bg-slate-900/50"><?= $no++ ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 font-sans bg-slate-900/50"><?= esc($s['kode']) ?></td>
                            
                            <!-- Kolom RAU Index (Abu-abu lembut seperti di Excel) -->
                            <td class="py-2 px-1 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold text-emerald-400"><?= number_format($rel, 1) ?>%</span>
                            </td>
                            <td class="py-2 px-1 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold text-emerald-400"><?= number_format($ava, 1) ?>%</span>
                            </td>
                            <td class="py-2 px-1 text-center border border-slate-800 bg-slate-850/60">
                                <span class="text-[13px] font-bold text-indigo-400"><?= number_format($uti, 1) ?>%</span>
                            </td>

                            <!-- Total MIRU & Total OPS -->
                            <td class="py-2 px-2.5 text-right font-medium text-[13px] border border-slate-800 text-slate-200"><?= number_format((float)($s['total_miru'] ?? 0), 2) ?></td>
                            <td class="py-2 px-2.5 text-right font-medium text-[13px] border border-slate-800 text-slate-200"><?= number_format((float)($s['total_ops'] ?? 0), 2) ?></td>

                            <!-- Average MIRU & Cycle Time -->
                            <td class="py-2 px-2.5 text-right text-xs border border-slate-800 text-slate-400"><?= number_format((float)($s['avg_miru'] ?? 0), 2) ?></td>
                            <td class="py-2 px-2.5 text-right text-xs border border-slate-800 text-slate-400"><?= number_format((float)($s['avg_cycle_time'] ?? 0), 2) ?></td>
                            
                            <!-- Total Well Job -->
                            <td class="py-2 px-2 text-center text-[13.5px] font-extrabold text-white border border-slate-800"><?= (int)($s['total_well_job'] ?? 0) ?></td>

                            <!-- Downtime SBWC & UNPAID -->
                            <td class="py-2 px-2.5 text-right text-[13px] font-semibold border border-slate-800 text-yellow-400"><?= number_format((float)($s['sbwc_jam'] ?? 0), 2) ?></td>
                            <td class="py-2 px-2.5 text-right text-[13px] font-semibold border border-slate-800 text-rose-400"><?= number_format((float)($s['unpaid_jam'] ?? 0), 2) ?></td>

                            <!-- Revenue Incentive Target & Actual -->
                            <td class="py-2 px-2.5 text-right text-xs font-medium border border-slate-800 text-slate-300">
                                Rp <?= number_format((float)($s['revenue_target'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td class="py-2 px-2.5 text-right text-[13.5px] font-extrabold border border-slate-800 text-emerald-400">
                                Rp <?= number_format((float)($s['revenue_actual'] ?? 0), 0, ',', '.') ?>
                            </td>

                            <td class="py-2 px-2.5 text-right text-xs border border-slate-800 text-slate-400"><?= number_format((float)($s['total_jam'] ?? 0), 2) ?></td>
                            <td class="py-2 px-3 text-[11px] text-slate-400 font-sans truncate max-w-xs border border-slate-800" title="<?= esc($s['remark'] ?? '') ?>"><?= esc($s['remark'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 font-sans uppercase">AVERAGE / TOTAL</td>
                            <td class="py-2.5 px-1 text-center border border-slate-700 text-emerald-400"><?= number_format($avgReliability * 100, 1) ?>%</td>
                            <td class="py-2.5 px-1 text-center border border-slate-700 text-emerald-400"><?= number_format($avgAvailability * 100, 1) ?>%</td>
                            <td class="py-2.5 px-1 text-center border border-slate-700 text-indigo-300"><?= number_format($avgUtilization * 100, 1) ?>%</td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700"><?= number_format($totMiru, 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700"><?= number_format($totOps, 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-slate-300"><?= number_format($avgMiruAll, 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-slate-300"><?= number_format($avgCycleTimeAll, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-white"><?= $totWell ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-yellow-300"><?= number_format($totSbwc, 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-rose-300"><?= number_format($totUnpaid, 2) ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-slate-300">Rp <?= number_format($totRevTarget, 0, ',', '.') ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700 text-emerald-400 text-[14px]">Rp <?= number_format($totRevActual, 0, ',', '.') ?></td>
                            <td class="py-2.5 px-2.5 text-right border border-slate-700"><?= number_format($totJam, 2) ?></td>
                            <td class="border border-slate-700"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: DAFTAR NILAI KONTRAK ODR PERSIS GAMBAR 2 -->
    <div id="tabContent_odrTable" class="tab-content hidden space-y-4">
        <div class="max-w-md mx-auto rounded-xl bg-slate-900 border-2 border-slate-700 shadow-2xl overflow-hidden">
            <div class="bg-blue-600 text-white py-2 px-4 font-black text-sm uppercase tracking-wider flex items-center justify-between">
                <span>TABEL ODR: OPERATOR DAILY RATE</span>
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <table class="w-full text-left border-collapse border border-slate-700 text-xs font-num">
                <tbody class="divide-y divide-slate-700">
                    <?php 
                    $odrColors = [
                        'BMS#01' => 'bg-slate-850 text-white',
                        'BMS#02' => 'bg-sky-200 text-slate-900',
                        'BMS#17' => 'bg-amber-100 text-slate-900',
                        'BMS#03' => 'bg-sky-300 text-slate-900',
                        'BMS#08' => 'bg-sky-300 text-slate-900',
                        'BMS#03A' => 'bg-sky-400 text-slate-900',
                        'BMS#05' => 'bg-sky-400 text-slate-900',
                        'BMS#06' => 'bg-sky-400 text-slate-900',
                        'BMS#11' => 'bg-sky-400 text-slate-900',
                        'BMS#07' => 'bg-yellow-300 text-slate-900',
                        'BMS#15' => 'bg-yellow-300 text-slate-900',
                        'BMS#18' => 'bg-yellow-300 text-slate-900',
                        'BMS#09' => 'bg-lime-400 text-slate-900',
                        'BMS#10' => 'bg-lime-400 text-slate-900',
                        'BMS#16' => 'bg-lime-400 text-slate-900',
                        'BMS#19' => 'bg-yellow-300 text-slate-900',
                        'BMS#20' => 'bg-yellow-300 text-slate-900',
                        'BMS#21' => 'bg-yellow-300 text-slate-900',
                    ];
                    foreach ($summaries as $s): 
                        $kd = $s['kode'];
                        $rowStyle = $odrColors[$kd] ?? 'bg-slate-800 text-white';
                    ?>
                    <tr class="border-b border-slate-700 font-extrabold">
                        <td class="py-2 px-4 border-r border-slate-700 <?= $rowStyle ?> font-sans w-32">
                            <?= esc($kd) ?>
                        </td>
                        <td class="py-2 px-4 text-right <?= $rowStyle ?>">
                            Rp <?= number_format((float)($s['odr'] ?? 0), 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: NPT ALL RIG -->
    <div id="tabContent_nptAll" class="tab-content hidden space-y-4">
        <div class="rounded-xl bg-slate-900 border border-slate-700 shadow-md overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th class="py-2.5 px-2.5 w-10 border border-slate-700 sticky left-0 bg-[#0B1E4A]">NO</th>
                            <th class="py-2.5 px-3 border border-slate-700 min-w-[90px] sticky left-10 bg-[#0B1E4A]">NAME RIG</th>
                            <?php foreach ($kategoriList as $kat): ?>
                                <th class="py-2 px-2 min-w-[75px] border border-slate-700" title="<?= esc($kat['nama']) ?>">
                                    <span class="block truncate <?= $kat['tipe'] == 'UNPAID' ? 'text-rose-400' : 'text-yellow-300' ?>"><?= esc($kat['nama']) ?></span>
                                    <span class="text-[8px] font-mono opacity-70"><?= $kat['tipe'] ?></span>
                                </th>
                            <?php endforeach; ?>
                            <th class="py-2.5 px-3 text-right min-w-[80px] bg-[#0E2A66] font-bold text-yellow-300 border border-slate-700">TOTAL (HRS)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php 
                        $no = 1; 
                        $grandTotalDT = 0;
                        $colTotals = [];
                        foreach ($summaries as $s): 
                            $rigTotalDT = 0;
                        ?>
                        <tr class="hover:bg-slate-800 transition">
                            <td class="py-2 px-2.5 text-center text-xs text-slate-400 border border-slate-800 sticky left-0 bg-slate-900"><?= $no++ ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 sticky left-10 bg-slate-900 font-sans"><?= esc($s['kode']) ?></td>
                            <?php foreach ($kategoriList as $kat): 
                                $val = (float)($summaryAllRig[$s['rig_id']][$kat['id']] ?? 0);
                                $rigTotalDT += $val;
                                $colTotals[$kat['id']] = ($colTotals[$kat['id']] ?? 0) + $val;
                            ?>
                                <td class="py-2 px-1.5 text-center border border-slate-800 text-[13px] <?= $val > 0 ? ($kat['tipe'] == 'UNPAID' ? 'text-rose-400 font-bold' : 'text-yellow-300 font-bold') : 'text-slate-600' ?>">
                                    <?= $val > 0 ? number_format($val, 2) : '-' ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="py-2 px-3 text-right font-extrabold text-[13.5px] text-yellow-300 bg-[#0E2A66]/40 border border-slate-800">
                                <?= number_format($rigTotalDT, 2) ?>
                            </td>
                        </tr>
                        <?php 
                            $grandTotalDT += $rigTotalDT;
                        endforeach; 
                        ?>
                    </tbody>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 sticky left-0 bg-[#0B1E4A] font-sans">TOTAL DOWNTIME</td>
                            <?php foreach ($kategoriList as $kat): ?>
                                <td class="py-2.5 px-1.5 text-center border border-slate-700 text-emerald-400">
                                    <?= number_format($colTotals[$kat['id']] ?? 0, 2) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="py-2.5 px-3 text-right font-extrabold text-[14px] text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($grandTotalDT, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: GRAFIK ANALISIS RESMI (NPT ALL RIG, RAU ALL RIG, AVERAGE MIRU, AVERAGE CYCLE TIME, TOTAL WELL JOB) -->
    <div id="tabContent_charts" class="tab-content hidden space-y-6">

        <!-- Sub-header & Quick Jump Links -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-slate-800/90 border border-slate-700 shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 border border-sky-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-chart-column text-lg"></i>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">GRAFIK PERFORMA OPERASI RIG BMS</h4>
                    <p class="text-xs text-slate-400">Visualisasi 5 Indikator Kinerja Utama Periode: <strong class="text-yellow-400 font-mono"><?= $bulanList[$bulan] ?> <?= $tahun ?></strong></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <a href="#cardChartNpt" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-amber-400 transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>1. NPT All Rig</span>
                </a>
                <a href="#cardChartRau" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-sky-400 transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    <span>2. RAU All Rig</span>
                </a>
                <a href="#cardChartMiru" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-blue-500 transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>3. Avg MIRU</span>
                </a>
                <a href="#cardChartCt" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-emerald-500 transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>4. Avg Cycle Time</span>
                </a>
                <a href="#cardChartWell" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-indigo-500 transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>5. Total Well Job</span>
                </a>
            </div>
        </div>

        <!-- GRAFIK 1: NPT ALL RIG (SBWC & UNPAID DOWNTIME PER RIG) -->
        <div id="cardChartNpt" class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden scroll-mt-6">
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-white uppercase tracking-wider">NPT ALL RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                        <p class="text-xs text-slate-400">Total Downtime Jam SBWC (Stand By With Crew) &amp; UNPAID per Armada Rig</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-mono font-bold">
                        Unpaid: <?= number_format($totUnpaid, 2) ?> Jam
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-mono font-bold">
                        SBWC: <?= number_format($totSbwc, 2) ?> Jam
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-200 border border-slate-700 text-xs font-mono font-black">
                        Total: <?= number_format($totSbwc + $totUnpaid, 2) ?> Jam
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="h-80 w-full">
                    <canvas id="chartNptAllRig"></canvas>
                </div>
            </div>
        </div>

        <!-- GRAFIK 2: RAU ALL RIG (RELIABILITY & AVAILABILITY DENGAN UTILIZATION) -->
        <div id="cardChartRau" class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden scroll-mt-6">
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        <i class="fa-solid fa-gauge-high text-sm"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-white uppercase tracking-wider">RAU ALL RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                        <p class="text-xs text-slate-400">Indikator Reliabilitas (%), Availability (%), &amp; Utilitas (%) Seluruh Rig</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-mono font-bold">
                        Avg Rel: <?= number_format($avgReliability * 100, 2) ?>%
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-sky-500/20 text-sky-300 border border-sky-500/30 text-xs font-mono font-bold">
                        Avg Avail: <?= number_format($avgAvailability * 100, 2) ?>%
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-mono font-bold">
                        Avg Util: <?= number_format($avgUtilization * 100, 2) ?>%
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="h-80 w-full">
                    <canvas id="chartRauAllRig"></canvas>
                </div>
            </div>
        </div>

        <!-- GRID 2 KOLOM: AVERAGE MIRU & AVERAGE CYCLE TIME -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- GRAFIK 3: AVERAGE MIRU (HRS) -->
            <div id="cardChartMiru" class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden scroll-mt-6">
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 border-b border-slate-700 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            <i class="fa-solid fa-truck-moving text-sm"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-white uppercase tracking-wider">AVERAGE MIRU RIG BMS</h5>
                            <p class="text-xs text-slate-400">Rata-rata Durasi MIRU (Jam / Sumur) Periode <?= $bulanList[$bulan] ?> <?= $tahun ?></p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-blue-600/20 text-blue-300 border border-blue-500/30 text-xs font-mono font-black">
                        Avg: <?= number_format($avgMiruAll, 2) ?> Jam
                    </span>
                </div>
                <div class="p-5">
                    <div class="h-72 w-full">
                        <canvas id="chartAvgMiru"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 4: AVERAGE CYCLE TIME (HRS) -->
            <div id="cardChartCt" class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden scroll-mt-6">
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 border-b border-slate-700 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            <i class="fa-solid fa-arrows-spin text-sm"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-white uppercase tracking-wider">AVERAGE CYCLE TIME RIG BMS</h5>
                            <p class="text-xs text-slate-400">Rata-rata Durasi Operasi / Cycle Time (Jam / Sumur)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 text-xs font-mono font-black">
                        Avg: <?= number_format($avgCycleTimeAll, 2) ?> Jam
                    </span>
                </div>
                <div class="p-5">
                    <div class="h-72 w-full">
                        <canvas id="chartAvgCycleTime"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- GRAFIK 5: TOTAL WELL JOB (SUMUR PER RIG) -->
        <div id="cardChartWell" class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden scroll-mt-6">
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                        <i class="fa-solid fa-oil-well text-sm"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-white uppercase tracking-wider">TOTAL WELL JOB RIG BMS — PERIODE <?= strtoupper($bulanList[$bulan]) ?> <?= $tahun ?></h5>
                        <p class="text-xs text-slate-400">Jumlah Sumur yang Diselesaikan per Rig Periode <?= $bulanList[$bulan] ?> <?= $tahun ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-lg bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 text-xs font-mono font-black">
                        Grand Total: <?= number_format($totWell, 0, ',', '.') ?> Sumur
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="h-80 w-full">
                    <canvas id="chartTotalWellJob"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateReport() {
        const bulan = document.getElementById('selectBulan').value;
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('monthly-report') ?>/${bulan}/${tahun}`;
    }

    let chartsInitialized = false;

    function switchTab(tabKey) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-yellow-400', 'text-yellow-300', 'bg-slate-800');
            btn.classList.add('border-transparent', 'text-slate-400');
        });

        document.getElementById(`tabContent_${tabKey}`).classList.remove('hidden');
        const activeBtn = document.getElementById(`tabBtn_${tabKey}`);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-slate-400');
            activeBtn.classList.add('border-yellow-400', 'text-yellow-300', 'bg-slate-800');
        }

        if (tabKey === 'charts' && !chartsInitialized) {
            initMonthlyCharts();
            chartsInitialized = true;
        }
    }

    // Ekstraksi Data dari PHP
    <?php
    $labelsArray = [];
    $relArray = [];
    $avaArray = [];
    $utiArray = [];
    $miruArray = [];
    $ctArray = [];
    $wellArray = [];
    $sbwcArray = [];
    $unpaidArray = [];

    foreach ($summaries as $s) {
        $labelsArray[] = $s['kode'];
        $relArray[]    = round((float)($s['reliability'] ?? 0) * 100, 2);
        $avaArray[]    = round((float)($s['availability'] ?? 0) * 100, 2);
        $utiArray[]    = round((float)($s['utilization'] ?? 0) * 100, 2);
        $miruArray[]   = round((float)($s['avg_miru'] ?? 0), 2);
        $ctArray[]     = round((float)($s['avg_cycle_time'] ?? 0), 2);
        $wellArray[]   = (int)($s['total_well_job'] ?? 0);
        $sbwcArray[]   = round((float)($s['sbwc_jam'] ?? 0), 2);
        $unpaidArray[] = round((float)($s['unpaid_jam'] ?? 0), 2);
    }
    ?>

    const chartLabels = <?= json_encode($labelsArray) ?>;
    const relData     = <?= json_encode($relArray) ?>;
    const avaData     = <?= json_encode($avaArray) ?>;
    const utiData     = <?= json_encode($utiArray) ?>;
    const miruData    = <?= json_encode($miruArray) ?>;
    const ctData      = <?= json_encode($ctArray) ?>;
    const wellData    = <?= json_encode($wellArray) ?>;
    const sbwcData    = <?= json_encode($sbwcArray) ?>;
    const unpaidData  = <?= json_encode($unpaidArray) ?>;

    const COMMON_GRID = { color: 'rgba(255, 255, 255, 0.08)' };
    const COMMON_TICKS = { color: '#94a3b8', font: { size: 11, family: "'JetBrains Mono', monospace" } };

    function initMonthlyCharts() {
        // 1. CHART NPT ALL RIG (Stacked / Grouped Bar: SBWC & UNPAID)
        const ctxNpt = document.getElementById('chartNptAllRig');
        if (ctxNpt) {
            new Chart(ctxNpt, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'UNPAID (Jam)',
                            data: unpaidData,
                            backgroundColor: '#ef4444', // Red accent
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'SBWC (Jam)',
                            data: sbwcData,
                            backgroundColor: '#f59e0b', // Amber/Yellow accent
                            borderRadius: 4,
                            stack: 'Stack 0'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e2e8f0', font: { size: 12, weight: 'bold' } }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                footer: function(items) {
                                    let sum = 0;
                                    items.forEach(it => sum += it.parsed.y);
                                    return 'Total NPT: ' + sum.toFixed(2) + ' Jam';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: COMMON_TICKS },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: COMMON_GRID,
                            ticks: {
                                ...COMMON_TICKS,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // 2. CHART RAU ALL RIG (Grouped Bar: Reliability, Availability, Utilization %)
        const ctxRau = document.getElementById('chartRauAllRig');
        if (ctxRau) {
            new Chart(ctxRau, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'RELIABILITY (%)',
                            data: relData,
                            backgroundColor: '#f59e0b', // Excel: FFC000 kuning emas
                            borderRadius: 4,
                            barPercentage: 0.85,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'AVAILABILITY (%)',
                            data: avaData,
                            backgroundColor: '#64748b', // Excel: tx2 abu-abu slate
                            borderRadius: 4,
                            barPercentage: 0.85,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'UTILIZATION (%)',
                            data: utiData,
                            backgroundColor: '#0284c7', // Sky blue untuk komplementer
                            borderRadius: 4,
                            barPercentage: 0.85,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e2e8f0', font: { size: 12, weight: 'bold' } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(c) { return ` ${c.dataset.label}: ${c.raw.toFixed(1)}%`; }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: COMMON_TICKS },
                        y: {
                            beginAtZero: true,
                            max: 105,
                            grid: COMMON_GRID,
                            ticks: {
                                ...COMMON_TICKS,
                                callback: function(val) { return val + '%'; }
                            }
                        }
                    }
                }
            });
        }

        // 3. CHART AVERAGE MIRU (HRS)
        const ctxMiru = document.getElementById('chartAvgMiru');
        if (ctxMiru) {
            new Chart(ctxMiru, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'AVERAGE MIRU (HRS)',
                            data: miruData,
                            backgroundColor: '#0070c0', // Warna persis Excel: 0070C0 Blue
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e2e8f0', font: { size: 12, weight: 'bold' } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(c) { return ` Avg MIRU: ${c.raw.toFixed(2)} Jam/Sumur`; }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: COMMON_TICKS },
                        y: {
                            beginAtZero: true,
                            grid: COMMON_GRID,
                            ticks: {
                                ...COMMON_TICKS,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // 4. CHART AVERAGE CYCLE TIME (HRS)
        const ctxCt = document.getElementById('chartAvgCycleTime');
        if (ctxCt) {
            new Chart(ctxCt, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'AVERAGE CYCLE TIME (HRS)',
                            data: ctData,
                            backgroundColor: '#00b050', // Warna persis Excel: 00B050 Green
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e2e8f0', font: { size: 12, weight: 'bold' } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(c) { return ` Avg Cycle Time: ${c.raw.toFixed(2)} Jam/Sumur`; }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: COMMON_TICKS },
                        y: {
                            beginAtZero: true,
                            grid: COMMON_GRID,
                            ticks: {
                                ...COMMON_TICKS,
                                callback: function(val) { return val + ' Jam'; }
                            }
                        }
                    }
                }
            });
        }

        // 5. CHART TOTAL WELL JOB
        const ctxWell = document.getElementById('chartTotalWellJob');
        if (ctxWell) {
            new Chart(ctxWell, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'TOTAL WELL JOB',
                            data: wellData,
                            backgroundColor: '#0070c0', // Warna persis Excel: 0070C0 Blue
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e2e8f0', font: { size: 12, weight: 'bold' } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(c) { return ` Total Well Job: ${c.raw} Sumur`; }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: COMMON_TICKS },
                        y: {
                            beginAtZero: true,
                            grid: COMMON_GRID,
                            ticks: {
                                ...COMMON_TICKS,
                                precision: 0,
                                callback: function(val) { return val + ' Sumur'; }
                            }
                        }
                    }
                }
            });
        }
    }

    // Auto buka tab grafik jika ada hash #tabContent_charts atau query parameter ?tab=charts
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (window.location.hash.includes('charts') || urlParams.get('tab') === 'charts') {
            switchTab('charts');
        }
    });
</script>
<?= $this->endSection() ?>
