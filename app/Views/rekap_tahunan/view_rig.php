<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--report space-y-6">
    <!-- Header / Toolbar -->
    <div class="ui-toolbar flex-wrap justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= base_url("rekap-tahunan/{$tahun}") ?>"
                class="w-8 h-8 rounded-md flex items-center justify-center transition"
                style="background:var(--secondary);color:var(--muted-foreground)"
                onmouseover="this.style.background='var(--accent)';this.style.color='var(--foreground)'"
                onmouseout="this.style.background='var(--secondary)';this.style.color='var(--muted-foreground)'">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h3 class="text-sm font-bold" style="color:var(--foreground)">
                    <?= esc($rig['kode']) ?>
                    <span style="color:var(--muted-foreground);font-weight:500">(<?= esc($rig['nama_rig']) ?>)</span>
                    — Rekap 12 Bulan
                </h3>
                <p class="text-xs" style="color:var(--muted-foreground)">Performa bulanan &amp; RAU Tahun
                    <strong class="font-mono" style="color:var(--primary)"><?= $tahun ?></strong>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="ui-badge ui-badge--info font-mono">
                <i class="fa-solid fa-money-bill-wave text-xs"></i>
                ODR: Rp <?= number_format($rig['odr'], 0, ',', '.') ?>/Hari
            </span>
        </div>
    </div>

    <!-- 12 Months Detailed Table -->
    <div class="ui-card overflow-hidden">
        <div class="ui-card-header">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-sm" style="color:var(--primary)"></i>
                <span class="ui-card-header-title">Rincian Performa Per Bulan — <?= $tahun ?></span>
            </div>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-950 text-slate-300 font-bold uppercase text-xs border-b border-slate-700">
                    <tr>
                        <th class="py-4 px-5">Bulan</th>
                        <th class="py-4 px-4 text-center">Reliability</th>
                        <th class="py-4 px-4 text-center">Availability</th>
                        <th class="py-4 px-4 text-center">Utilization</th>
                        <th class="py-4 px-4 text-right">MIRU (Jam)</th>
                        <th class="py-4 px-4 text-right">OPS (Jam)</th>
                        <th class="py-4 px-4 text-center">Well Job</th>
                        <th class="py-4 px-4 text-right">SBWC (Jam)</th>
                        <th class="py-4 px-4 text-right">Unpaid (Jam)</th>
                        <th class="py-4 px-5 text-right">Revenue Aktual</th>
                        <th class="py-4 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-slate-300">
                    <?php 
                    $bulanNames = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];

                    $indexedMonths = [];
                    foreach ($months as $m) {
                        $indexedMonths[$m['bulan']] = $m;
                    }

                    $totMiru = 0; $totOps = 0; $totWell = 0; $totSbwc = 0; $totUnpaid = 0; $totRev = 0;

                    for ($bln = 1; $bln <= 12; $bln++): 
                        $m = $indexedMonths[$bln] ?? null;
                        $rel = $m ? ((float)$m['reliability'] * 100) : 0;
                        $ava = $m ? ((float)$m['availability'] * 100) : 0;
                        $uti = $m ? ((float)$m['utilization'] * 100) : 0;
                        if ($m) {
                            $totMiru   += (float)$m['total_miru'];
                            $totOps    += (float)$m['total_ops'];
                            $totWell   += (int)$m['total_well_job'];
                            $totSbwc   += (float)$m['sbwc_jam'];
                            $totUnpaid += (float)$m['unpaid_jam'];
                            $totRev    += (float)$m['revenue_actual'];
                        }
                        $isEven = ($bln % 2 == 0);
                    ?>
                    <tr class="hover:bg-slate-700/30 transition <?= $isEven ? 'bg-slate-800/20' : '' ?>">
                        <td class="py-3.5 px-5 font-bold text-white text-sm"><?= $bulanNames[$bln] ?></td>
                        <td class="py-3.5 px-4 text-center font-mono">
                            <span class="px-2.5 py-1 rounded-lg text-sm font-semibold <?= $rel >= 90 ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-400' ?>">
                                <?= number_format($rel, 1) ?>%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono">
                            <span class="px-2.5 py-1 rounded-lg text-sm font-semibold <?= $ava >= 90 ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-400' ?>">
                                <?= number_format($ava, 1) ?>%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono">
                            <span class="px-2.5 py-1 rounded-lg text-sm font-semibold <?= $uti >= 70 ? 'text-indigo-300 bg-indigo-500/10' : 'text-slate-400' ?>">
                                <?= number_format($uti, 1) ?>%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-slate-200 text-sm"><?= $m ? number_format($m['total_miru'], 1) : '<span class="text-slate-600">—</span>' ?></td>
                        <td class="py-3.5 px-4 text-right font-mono text-slate-200 text-sm"><?= $m ? number_format($m['total_ops'], 1) : '<span class="text-slate-600">—</span>' ?></td>
                        <td class="py-3.5 px-4 text-center font-bold text-white text-base"><?= $m ? $m['total_well_job'] : '<span class="text-slate-600 font-normal text-sm">—</span>' ?></td>
                        <td class="py-3.5 px-4 text-right font-mono text-amber-400 text-sm"><?= $m ? number_format($m['sbwc_jam'], 1) : '<span class="text-slate-600">—</span>' ?></td>
                        <td class="py-3.5 px-4 text-right font-mono text-rose-400 text-sm"><?= $m ? number_format($m['unpaid_jam'], 1) : '<span class="text-slate-600">—</span>' ?></td>
                        <td class="py-3.5 px-5 text-right font-mono font-semibold text-emerald-400 text-sm">
                            <?= $m ? 'Rp ' . number_format($m['revenue_actual'], 0, ',', '.') : '<span class="text-slate-600">—</span>' ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="<?= base_url("monthly-report/{$bln}/{$tahun}") ?>" class="px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-600 hover:text-white text-xs font-semibold transition border border-blue-500/20">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <!-- Totals Footer -->
                <tfoot>
                    <tr class="bg-slate-900 border-t-2 border-slate-600 font-bold">
                        <td colspan="4" class="py-4 px-5 text-right text-xs text-slate-400 uppercase tracking-wide font-semibold">Total Akumulasi</td>
                        <td class="py-4 px-4 text-right font-mono text-white text-sm"><?= number_format($totMiru, 1) ?></td>
                        <td class="py-4 px-4 text-right font-mono text-white text-sm"><?= number_format($totOps, 1) ?></td>
                        <td class="py-4 px-4 text-center font-mono text-white text-base"><?= $totWell ?></td>
                        <td class="py-4 px-4 text-right font-mono text-amber-300 text-sm"><?= number_format($totSbwc, 1) ?></td>
                        <td class="py-4 px-4 text-right font-mono text-rose-300 text-sm"><?= number_format($totUnpaid, 1) ?></td>
                        <td class="py-4 px-5 text-right font-mono font-bold text-emerald-300 text-sm">Rp <?= number_format($totRev, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
