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
    <div class="flex border-b border-slate-700 gap-2">
        <a href="<?= base_url("rekap-tahunan/{$tahun}") ?>" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-yellow-400 text-yellow-300 bg-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-oil-well text-xs"></i>
            <span>Rekapitulasi Operasi &amp; Revenue</span>
        </a>
        <a href="<?= base_url("rekap-tahunan/npt/{$tahun}") ?>" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 flex items-center gap-2">
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
                <col style="width:9%">
                <col style="width:17%">
                <col style="width:9%">
                <col style="width:9%">
                <col style="width:10%">
                <col style="width:10%">
                <col style="width:24%">
                <col style="width:8%">
            </colgroup>
            <thead>
                <tr class="bg-slate-900/70 text-slate-300 uppercase tracking-wide border-b border-slate-700 text-center text-xs">
                    <th class="px-3 py-3.5 font-bold">No</th>
                    <th class="px-3 py-3.5 font-bold">Rig</th>
                    <th class="px-3 py-3.5 text-left font-bold">Nama Rig</th>
                    <th class="px-3 py-3.5 font-bold">Well<br>Job</th>
                    <th class="px-3 py-3.5 font-bold">Avg<br>Util</th>
                    <th class="px-3 py-3.5 font-bold">Jam<br>OPS</th>
                    <th class="px-3 py-3.5 font-bold">Down-<br>time</th>
                    <th class="px-3 py-3.5 font-bold">Revenue Akumulasi <?= $tahun ?></th>
                    <th class="px-3 py-3.5 font-bold">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableRows as $no => $row):
                    $r = $row['r'];
                    $isEven = ($no % 2 == 1);
                    $utilClass = $row['avgUtil'] >= 50 ? 'text-emerald-400' : ($row['avgUtil'] >= 20 ? 'text-yellow-400' : 'text-rose-400');
                ?>
                <tr class="border-b border-slate-700/40 hover:bg-blue-500/5 transition <?= $isEven ? 'bg-slate-800/20' : '' ?>">
                    <td class="px-3 py-3 text-center text-slate-400 font-mono text-sm"><?= $no + 1 ?></td>
                    <td class="px-3 py-3 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-300 font-bold text-sm">
                            <i class="fa-solid fa-oil-well text-xs"></i>
                            <?= esc($r['kode']) ?>
                        </span>
                    </td>
                    <td class="px-3 py-3">
                        <span class="block truncate text-slate-200 text-sm font-medium" title="<?= esc($r['nama_rig']) ?>"><?= esc($r['nama_rig']) ?></span>
                        <span class="text-xs text-slate-500 font-mono">ODR: Rp <?= number_format($r['odr']/1000000, 0) ?> Jt</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono font-bold text-white text-base"><?= $row['sumWellJob'] ?></span>
                        <span class="text-slate-400 text-xs block">Sumur</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono font-bold text-base <?= $utilClass ?>"><?= number_format($row['avgUtil'], 1) ?>%</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono text-slate-200 text-base"><?= number_format($row['sumOps'], 1) ?></span>
                        <span class="text-slate-400 text-xs block">Jam</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono text-rose-400 text-base"><?= number_format($row['sumDt'], 1) ?></span>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateTahun() {
        const tahun = document.getElementById('selectTahun').value;
        window.location.href = `<?= base_url('rekap-tahunan') ?>/${tahun}`;
    }
</script>
<?= $this->endSection() ?>
