<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4">

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <i class="fa-solid fa-chart-line text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-white">Rekapitulasi Tahunan Seluruh Armada</h3>
                <p class="text-xs text-slate-400">Ringkasan kinerja operasi seluruh rig — Tahun <span class="text-yellow-300 font-bold"><?= $tahun ?></span></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <select id="selectTahun" onchange="navigateTahun()" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-blue-500">
                <?php for ($y = 2024; $y <= 2028; $y++): ?>
                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <a href="<?= base_url("export/rekap-tahunan/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-slate-700 gap-2 overflow-x-auto">
        <button type="button" onclick="switchRekapTab('table')" id="rekapTabBtn_table" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-yellow-400 text-yellow-300 bg-slate-800 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-oil-well text-xs"></i>
            <span>Rekapitulasi Operasi &amp; Revenue</span>
        </button>
        <button type="button" onclick="switchRekapTab('charts')" id="rekapTabBtn_charts" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-chart-column text-xs text-sky-400"></i>
            <span>Grafik Analisis Kinerja Armada</span>
        </button>
        <a href="<?= base_url("rekap-tahunan/npt/{$tahun}") ?>" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clock-rotate-left text-xs text-rose-400"></i>
            <span>Rekapitulasi NPT (Downtime)</span>
        </a>
    </div>

    <?php
        /* Pre-compute all rows + grand totals */
        $grandWellJob = 0; $grandRevenue = 0; $grandOps = 0; $grandDt = 0; $rigCount = 0;
        $tableRows = [];
        foreach ($annualData as $rigId => $item) {
            $r = $item['rig'];
            $months = $item['months'];
            $sumWellJob = 0; $sumRevenue = 0; $sumOps = 0; $sumDt = 0; $sumUtil = 0; $mCount = 0;
            foreach ($months as $m) {
                $sumWellJob += (int)($m['total_well_job'] ?? 0);
                $sumRevenue += (float)($m['revenue_actual'] ?? 0);
                $sumOps    += (float)($m['total_ops'] ?? 0);
                $sumDt     += ((float)($m['sbwc_jam'] ?? 0) + (float)($m['unpaid_jam'] ?? 0));
                $sumUtil   += (float)($m['utilization'] ?? 0);
                $mCount++;
            }
            $avgUtil = $mCount > 0 ? ($sumUtil / $mCount) * 100 : 0;
            $grandWellJob += $sumWellJob;
            $grandRevenue += $sumRevenue;
            $grandOps     += $sumOps;
            $grandDt      += $sumDt;
            $rigCount++;
            $tableRows[] = compact('r','sumWellJob','sumRevenue','sumOps','sumDt','avgUtil');
        }
    ?>

    <!-- TAB CONTENT 1: TABEL RINGKASAN REKAPITULASI -->
    <div id="rekapTab_table" class="space-y-4">
        <!-- KPI Summary Strip (4 tiles, no scroll) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-xl bg-blue-500/10 border border-blue-500/20 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Total Well Job</p>
                    <p class="text-lg font-bold text-white font-mono"><?= $grandWellJob ?> <span class="text-xs font-normal text-slate-400">Sumur</span></p>
                </div>
            </div>
            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Total Revenue <?= $tahun ?></p>
                    <p class="text-sm font-bold text-emerald-400 font-mono">Rp <?= number_format($grandRevenue, 0, ',', '.') ?></p>
                </div>
            </div>
            <div class="rounded-xl bg-indigo-500/10 border border-indigo-500/20 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Total Jam OPS</p>
                    <p class="text-lg font-bold text-white font-mono"><?= number_format($grandOps, 1) ?> <span class="text-xs font-normal text-slate-400">Jam</span></p>
                </div>
            </div>
            <div class="rounded-xl bg-rose-500/10 border border-rose-500/20 p-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Total Downtime</p>
                    <p class="text-lg font-bold text-white font-mono"><?= number_format($grandDt, 1) ?> <span class="text-xs font-normal text-slate-400">Jam</span></p>
                </div>
            </div>
        </div>

        <!-- Main Table — NO HORIZONTAL SCROLL, fixed layout fills 100% width -->
        <div class="rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-blue-400 text-sm"></i>
                <span class="text-sm font-bold text-slate-300">Tabel Ringkasan Per Rig — <?= $rigCount ?> Unit Aktif</span>
            </div>
            <table class="w-full text-sm" style="table-layout:fixed">
                <colgroup>
                    <col style="width:4%">
                    <col style="width:8%">
                    <col style="width:16%">
                    <col style="width:11%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:12%">
                    <col style="width:17%">
                    <col style="width:8%">
                </colgroup>
                <thead class="bg-slate-950 text-slate-300 font-bold uppercase text-xs border-b border-slate-700">
                    <tr class="text-center">
                        <th class="py-3 px-2">No</th>
                        <th class="py-3 px-2">Rig</th>
                        <th class="py-3 px-3 text-left">Nama Rig</th>
                        <th class="py-3 px-3">Well Job</th>
                        <th class="py-3 px-3">Avg Util</th>
                        <th class="py-3 px-3">Jam OPS</th>
                        <th class="py-3 px-3">Downtime</th>
                        <th class="py-3 px-3">Revenue Akumulasi</th>
                        <th class="py-3 px-2">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    <?php $no = 1; foreach ($tableRows as $row):
                        $r = $row['r'];
                        $utilColor = $row['avgUtil'] >= 50 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($row['avgUtil'] >= 20 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30');
                    ?>
                    <tr class="hover:bg-slate-700/30 transition <?= $no % 2 == 0 ? 'bg-slate-800/30' : '' ?>">
                        <td class="px-2 py-3 text-center text-slate-400 font-mono text-xs"><?= $no++ ?></td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-block px-2 py-1 rounded bg-blue-500/20 text-blue-300 font-mono text-sm font-bold border border-blue-500/30">
                                <?= esc($r['kode']) ?>
                            </span>
                        </td>
                        <td class="px-3 py-3 text-left">
                            <span class="font-medium text-white text-sm"><?= esc($r['nama_rig']) ?></span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-mono font-bold text-white text-base"><?= $row['sumWellJob'] ?></span>
                            <span class="text-slate-400 text-xs block">Sumur</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold font-mono <?= $utilColor ?>">
                                <?= number_format($row['avgUtil'], 1) ?>%
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-mono text-white text-base font-semibold"><?= number_format($row['sumOps'], 1) ?></span>
                            <span class="text-slate-400 text-xs block">Jam</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-mono text-rose-400 text-base font-semibold"><?= number_format($row['sumDt'], 1) ?></span>
                            <span class="text-slate-400 text-xs block">Jam</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-mono font-bold text-emerald-400 text-sm">Rp <?= number_format($row['sumRevenue'], 0, ',', '.') ?></span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <a href="<?= base_url("rekap-tahunan/rig/{$r['id']}/{$tahun}") ?>"
                               title="Lihat Rincian 12 Bulan <?= esc($r['kode']) ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <!-- Grand Total Footer -->
                <tfoot>
                    <tr class="bg-slate-900/80 border-t-2 border-slate-600 font-bold text-center">
                        <td colspan="3" class="px-4 py-3.5 text-right text-xs text-slate-400 uppercase tracking-wide font-semibold">
                            Grand Total &nbsp;(<?= $rigCount ?> Rig)
                        </td>
                        <td class="px-3 py-3.5">
                            <span class="font-mono text-white text-base"><?= $grandWellJob ?></span>
                            <span class="text-slate-400 text-xs block">Sumur</span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-500">—</td>
                        <td class="px-3 py-3.5">
                            <span class="font-mono text-white text-base"><?= number_format($grandOps, 1) ?></span>
                            <span class="text-slate-400 text-xs block">Jam</span>
                        </td>
                        <td class="px-3 py-3.5">
                            <span class="font-mono text-rose-300 text-base"><?= number_format($grandDt, 1) ?></span>
                            <span class="text-slate-400 text-xs block">Jam</span>
                        </td>
                        <td class="px-3 py-3.5">
                            <span class="font-mono font-bold text-emerald-300 text-sm">Rp <?= number_format($grandRevenue, 0, ',', '.') ?></span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- TAB CONTENT 2: GRAFIK ANALISIS REKAP TAHUNAN -->
    <div id="rekapTab_charts" class="hidden space-y-6">
        
        <!-- Header Banner Grafik -->
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 border border-sky-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-chart-column text-lg"></i>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">GRAFIK PERFORMA OPERASI SELURUH ARMADA TAHUN <?= $tahun ?></h4>
                    <p class="text-xs text-slate-400">Visualisasi komparasi performa Well Job, Revenue, Jam Operasi, dan Utilitas seluruh rig</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-300 border border-blue-500/30 text-xs font-mono font-bold">
                    <?= $rigCount ?> Armada Rig Aktif
                </span>
            </div>
        </div>

        <!-- Grid 2 Kolom Grafik Utama: Total Well Job & Revenue Akumulasi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- GRAFIK A: Total Well Job per Rig -->
            <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
                <div class="p-4 bg-slate-950/80 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-oil-well text-blue-400"></i>
                        <h5 class="text-xs font-bold text-white uppercase tracking-wider">TOTAL WELL JOB (SUMUR) PER RIG</h5>
                    </div>
                    <span class="text-xs font-mono font-bold text-blue-400">Grand Total: <?= $grandWellJob ?> Sumur</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualWell"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRAFIK B: Revenue Akumulasi per Rig -->
            <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
                <div class="p-4 bg-slate-950/80 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-sack-dollar text-emerald-400"></i>
                        <h5 class="text-xs font-bold text-white uppercase tracking-wider">REVENUE AKUMULASI (JUTA RP)</h5>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-400">Total: Rp <?= number_format($grandRevenue, 0, ',', '.') ?></span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualRevenue"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- Grid 2 Kolom Grafik Operasional: Jam OPS vs Downtime & Rata-rata Utilitas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- GRAFIK C: Jam OPS vs Downtime -->
            <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
                <div class="p-4 bg-slate-950/80 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                        <h5 class="text-xs font-bold text-white uppercase tracking-wider">JAM OPS VS DOWNTIME PER RIG</h5>
                    </div>
                    <span class="text-xs font-mono text-slate-400">Satuan Jam</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualOpsDt"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRAFIK D: Rata-rata Utilitas (%) -->
            <div class="rounded-2xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
                <div class="p-4 bg-slate-950/80 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-gauge-high text-cyan-400"></i>
                        <h5 class="text-xs font-bold text-white uppercase tracking-wider">RATA-RATA UTILITAS ARMADA (%)</h5>
                    </div>
                    <span class="text-xs font-mono text-cyan-400">Target > 50%</span>
                </div>
                <div class="p-4">
                    <div class="h-72 w-full">
                        <canvas id="chartAnnualUtil"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function navigateTahun() {
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('rekap-tahunan') ?>/${tahun}`;
    }

    let rekapChartsInited = false;

    function switchRekapTab(tabKey) {
        if (tabKey === 'table') {
            document.getElementById('rekapTab_table').classList.remove('hidden');
            document.getElementById('rekapTab_charts').classList.add('hidden');
            document.getElementById('rekapTabBtn_table').className = 'px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-yellow-400 text-yellow-300 bg-slate-800 flex items-center gap-2 whitespace-nowrap';
            document.getElementById('rekapTabBtn_charts').className = 'px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap';
        } else {
            document.getElementById('rekapTab_table').classList.add('hidden');
            document.getElementById('rekapTab_charts').classList.remove('hidden');
            document.getElementById('rekapTabBtn_charts').className = 'px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-yellow-400 text-yellow-300 bg-slate-800 flex items-center gap-2 whitespace-nowrap';
            document.getElementById('rekapTabBtn_table').className = 'px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2 whitespace-nowrap';
            if (!rekapChartsInited) {
                initAnnualCharts();
                rekapChartsInited = true;
            }
        }
    }

    <?php
    $rLabels = [];
    $rWell = [];
    $rRev = [];
    $rOps = [];
    $rDt = [];
    $rUtil = [];

    foreach ($tableRows as $row) {
        $rLabels[] = $row['r']['kode'];
        $rWell[]   = (int)$row['sumWellJob'];
        $rRev[]    = round($row['sumRevenue'] / 1000000, 2); // dalam juta rupiah
        $rOps[]    = round($row['sumOps'], 1);
        $rDt[]     = round($row['sumDt'], 1);
        $rUtil[]   = round($row['avgUtil'], 1);
    }
    ?>

    const annualLabels = <?= json_encode($rLabels) ?>;
    const annualWell   = <?= json_encode($rWell) ?>;
    const annualRev    = <?= json_encode($rRev) ?>;
    const annualOps    = <?= json_encode($rOps) ?>;
    const annualDt     = <?= json_encode($rDt) ?>;
    const annualUtil   = <?= json_encode($rUtil) ?>;

    const CH_GRID = { color: 'rgba(255, 255, 255, 0.08)' };
    const CH_TICKS = { color: '#94a3b8', font: { size: 11, family: "'JetBrains Mono', monospace" } };

    function initAnnualCharts() {
        // 1. Chart Well Job
        const ctxWell = document.getElementById('chartAnnualWell');
        if (ctxWell) {
            new Chart(ctxWell, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [{
                        label: 'Total Well Job (Sumur)',
                        data: annualWell,
                        backgroundColor: '#0070c0',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, precision: 0 } }
                    }
                }
            });
        }

        // 2. Chart Revenue
        const ctxRev = document.getElementById('chartAnnualRevenue');
        if (ctxRev) {
            new Chart(ctxRev, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [{
                        label: 'Revenue (Juta Rp)',
                        data: annualRev,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => ' Rp ' + Number(c.raw).toLocaleString('id-ID') + ' Juta' } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => 'Rp ' + v + 'M' } }
                    }
                }
            });
        }

        // 3. Chart Jam OPS vs Downtime
        const ctxOps = document.getElementById('chartAnnualOpsDt');
        if (ctxOps) {
            new Chart(ctxOps, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [
                        { label: 'Jam OPS', data: annualOps, backgroundColor: '#0284c7', borderRadius: 4 },
                        { label: 'Downtime (Jam)', data: annualDt, backgroundColor: '#f43f5e', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#e2e8f0', font: { size: 11, weight: 'bold' } } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => v + ' Jam' } }
                    }
                }
            });
        }

        // 4. Chart Utilitas %
        const ctxUtil = document.getElementById('chartAnnualUtil');
        if (ctxUtil) {
            new Chart(ctxUtil, {
                type: 'bar',
                data: {
                    labels: annualLabels,
                    datasets: [{
                        label: 'Utilitas Rata-rata (%)',
                        data: annualUtil,
                        backgroundColor: annualUtil.map(v => v >= 50 ? '#10b981' : (v >= 20 ? '#f59e0b' : '#ef4444')),
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => ' ' + c.raw + '%' } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: CH_TICKS },
                        y: { beginAtZero: true, max: 100, grid: CH_GRID, ticks: { ...CH_TICKS, callback: v => v + '%' } }
                    }
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
