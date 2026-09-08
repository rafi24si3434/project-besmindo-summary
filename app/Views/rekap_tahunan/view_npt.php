<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-4">
    <!-- Top Action & Month Bar -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 p-4 rounded-xl bg-slate-800/90 border border-slate-700 shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <span>REKAP NPT RIG BMS</span>
                    <span class="px-2.5 py-1 rounded text-sm bg-blue-600 text-white font-mono">TAHUN <?= $tahun ?></span>
                </h3>
                <p class="text-sm text-slate-400">Rekapitulasi Downtime (SBWC &amp; UNPAID) Seluruh Rig Periode <?= $tahun ?></p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-lg p-1.5">
                <span class="px-2 text-sm font-semibold text-slate-400">Tahun:</span>
                <select id="selectTahunNpt" onchange="navigateNptYear()" class="bg-transparent text-sm font-bold text-white focus:outline-none pr-2">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" class="bg-slate-900" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <a href="<?= base_url('export/rekap-npt-tahunan/' . $tahun) ?>" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel Asli (SYS)</span>
            </a>
        </div>
    </div>

    <!-- Month Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 p-2.5 bg-slate-800/80 rounded-xl border border-slate-700/80 overflow-x-auto">
        <?php 
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus'
        ];
        foreach ($monthNames as $mNum => $mLabel): 
            $isActive = ($activeMonth == $mNum);
        ?>
            <a href="<?= base_url('rekap-tahunan/npt/' . $tahun . '?bulan=' . $mNum) ?>" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-1.5 <?= $isActive ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
                <i class="fa-regular fa-calendar-check text-xs <?= $isActive ? 'text-slate-950' : 'text-amber-400' ?>"></i>
                <span><?= $mLabel ?></span>
            </a>
        <?php endforeach; ?>

        <button onclick="scrollToGrandTotal()" id="btnGrandTotal" class="ml-auto px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 bg-blue-700 hover:bg-blue-600 text-white shadow-md shadow-blue-700/30">
            <i class="fa-solid fa-calculator text-xs"></i>
            <span>Lihat Grand Total <?= $tahun ?></span>
        </button>
    </div>

    <?php if ($monthData): ?>
    <!-- Excel Table Banner -->
    <div class="rounded-t-xl bg-[#002060] text-white p-4 border-b-2 border-amber-400 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
        <div class="flex items-center gap-3">
            <span class="w-3.5 h-3.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>
            <h2 class="font-black text-base md:text-lg tracking-wide uppercase font-mono text-amber-300">
                <?= esc($monthData['title']) ?>
            </h2>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="px-3 py-1.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">
                UNPAID: <strong class="text-white font-mono"><?= number_format((float)($monthData['totals'][2] ?? 0), 2, ',', '.') ?> Jam</strong>
            </span>
            <span class="px-3 py-1.5 rounded bg-amber-400/20 text-amber-300 border border-amber-400/30 font-bold">
                TOTAL: <strong class="text-white font-mono"><?= number_format((float)($monthData['totals'][32] ?? 0), 2, ',', '.') ?> Jam</strong>
            </span>
        </div>
    </div>

    <!-- Excel Viewport Container -->
    <div class="bg-white rounded-b-xl shadow-2xl border border-slate-300 overflow-hidden">
        <div class="overflow-x-auto max-h-[72vh]">
            <table class="w-full text-center border-collapse text-sm select-text">
                <!-- Excel Headers -->
                <thead class="sticky top-0 z-20 font-bold text-white shadow-sm">
                    <!-- Row 1: Group Headers -->
                    <tr class="bg-[#002060] border-b border-blue-900">
                        <th rowspan="3" class="py-3 px-3 border border-blue-900 bg-[#002060] min-w-[42px] w-[42px] text-center text-white text-sm">NO</th>
                        <th rowspan="3" class="py-3 px-4 border border-blue-900 bg-[#002060] min-w-[110px] text-left text-white text-sm">NAME RIG</th>
                        <th colspan="2" class="py-2 px-3 border border-blue-900 bg-[#002060] text-center text-white text-sm">UNPAID</th>
                        <th colspan="28" class="py-2 px-3 border border-blue-900 bg-[#002060] text-center text-white text-sm">STAND BY WITH CREW ( SBWC )</th>
                        <th rowspan="3" class="py-3 px-3 border border-blue-900 bg-[#002060] min-w-[90px] text-center text-white text-sm">TOTAL (HRS)</th>
                        <th rowspan="3" class="py-3 px-4 border border-blue-900 bg-[#002060] min-w-[260px] text-left text-white text-sm">REMARK UNPAID</th>
                    </tr>

                    <!-- Row 2: Sub-categories -->
                    <tr class="bg-[#002060] border-b border-blue-900 text-xs">
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[85px]">Repaire Rig &amp; Equpt</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[75px]">PERSONEL</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]">SWA Rain</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[85px]">Dry Road &amp; Public Road</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]">Dry Well Pad</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[75px]">WO Daylight</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]"><?= esc($monthData['cols'][8]['h3'] ?? 'PT. CHAST') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]"><?= esc($monthData['cols'][9]['h3'] ?? 'WO PDC') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[90px]">PHR Well &amp; Accessories</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[60px]">ESP</th>
                        <!-- 3rd Party Parent Header (10 columns) -->
                        <th colspan="10" class="py-1.5 px-2 border border-blue-900 text-center bg-[#001745] text-sm">3rd Party</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]">UNISAT</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[80px]">SHARING Units Trans</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]">Foam Unit</th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[90px]"><?= esc($monthData['cols'][25]['h3'] ?? 'WO Decision') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[65px]"><?= esc($monthData['cols'][26]['h3'] ?? 'PEMILU') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[65px]"><?= esc($monthData['cols'][27]['h3'] ?? 'WO OMS') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[65px]"><?= esc($monthData['cols'][28]['h3'] ?? 'PT. PCM') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[65px]"><?= esc($monthData['cols'][29]['h3'] ?? 'WO COSL') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[80px]"><?= esc($monthData['cols'][30]['h3'] ?? 'SAFARI RAMADHAN') ?></th>
                        <th class="py-2 px-2 border border-blue-900 text-center min-w-[70px]"><?= esc($monthData['cols'][31]['h3'] ?? 'IDUL FITRI') ?></th>
                    </tr>

                    <!-- Row 3: 3rd Party sub-columns -->
                    <tr class="bg-[#001745] border-b border-blue-900 text-xs">
                        <!-- 10 vendors under 3rd Party -->
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">BHI</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">HLS</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">WI</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">HALCO</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">EJP</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">SCHL</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">MGA</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">SGN</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">BUKAKA</th>
                        <th class="py-1.5 px-2 border border-blue-900 min-w-[52px]">PESI</th>
                    </tr>
                </thead>

                <!-- Body Data Rows -->
                <tbody class="text-slate-800 divide-y divide-slate-200 font-sans">
                    <?php foreach ($monthData['rows'] as $r): 
                        $no = $r['no'];
                        $rigName = $r['rig'];
                        $vals = $r['vals'];
                        $unpaidVal = $vals[2];
                        $totHrs = $vals[32];
                        $remark = $vals[33];
                    ?>
                    <tr class="hover:bg-amber-50/60 transition-colors">
                        <!-- NO (Peach background) -->
                        <td class="py-2.5 px-2 border border-slate-300 bg-[#FCE4D6] font-bold text-slate-800 text-center text-sm">
                            <?= $no ?>
                        </td>

                        <!-- NAME RIG (Peach background) -->
                        <td class="py-2.5 px-3 border border-slate-300 bg-[#FCE4D6] font-bold text-slate-900 text-left whitespace-nowrap text-sm">
                            <?= esc($rigName) ?>
                        </td>

                        <!-- UNPAID: Repaire Rig & Equpt (RED TEXT) -->
                        <td class="py-2.5 px-2 border border-slate-300 font-mono font-bold text-sm <?= (is_numeric($unpaidVal) && (float)$unpaidVal > 0) ? 'text-[#FF0000]' : 'text-slate-400' ?>">
                            <?= is_numeric($unpaidVal) ? ((float)$unpaidVal == 0 ? '-' : number_format((float)$unpaidVal, 2, ',', '.')) : esc($unpaidVal ?? '-') ?>
                        </td>

                        <!-- UNPAID: PERSONEL -->
                        <td class="py-2.5 px-2 border border-slate-300 font-mono text-sm <?= (is_numeric($vals[3]) && (float)$vals[3] > 0) ? 'text-[#FF0000] font-bold' : 'text-slate-400' ?>">
                            <?= is_numeric($vals[3]) ? ((float)$vals[3] == 0 ? '-' : number_format((float)$vals[3], 2, ',', '.')) : esc($vals[3] ?? '-') ?>
                        </td>

                        <!-- SBWC Columns: cols 4 to 31 -->
                        <?php for ($c = 4; $c <= 31; $c++): 
                            $v = $vals[$c] ?? null;
                            $hasVal = (is_numeric($v) && (float)$v > 0);
                        ?>
                            <td class="py-2.5 px-2 border border-slate-300 font-mono text-sm <?= $hasVal ? 'font-bold text-slate-900 bg-blue-50/40' : 'text-slate-400' ?>">
                                <?= is_numeric($v) ? ((float)$v == 0 ? '-' : number_format((float)$v, 2, ',', '.')) : ($v === '-' ? '-' : esc($v ?? '-')) ?>
                            </td>
                        <?php endfor; ?>

                        <!-- TOTAL (HRS) (Peach background) -->
                        <td class="py-2.5 px-3 border border-slate-300 bg-[#FCE4D6] font-mono font-black text-slate-950 text-right text-sm <?= (is_numeric($totHrs) && (float)$totHrs > 0) ? 'text-base' : '' ?>">
                            <?= is_numeric($totHrs) ? ((float)$totHrs == 0 ? '-' : number_format((float)$totHrs, 2, ',', '.')) : esc($totHrs ?? '-') ?>
                        </td>

                        <!-- REMARK UNPAID -->
                        <td class="py-2.5 px-3 border border-slate-300 text-left text-xs text-slate-700 max-w-[300px] truncate hover:whitespace-normal" title="<?= esc($remark ?? '') ?>">
                            <?= esc($remark ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <!-- Footer Total Row (Yellow background #FFFF00) -->
                <tfoot class="sticky bottom-0 z-20 font-mono font-black text-slate-950 text-sm border-t-2 border-slate-400">
                    <tr class="bg-[#FFFF00] border-b border-slate-400 shadow-md">
                        <td class="py-2.5 px-2 border border-slate-400 text-center bg-[#FFFF00]"></td>
                        <td class="py-2.5 px-3 border border-slate-400 text-left uppercase tracking-wider bg-[#FFFF00] font-sans font-black text-sm">
                            TOTAL
                        </td>

                        <!-- Col C: Total Repaire Rig (Red Bold) -->
                        <td class="py-2.5 px-2 border border-slate-400 bg-[#FFFF00] font-black text-base text-[#FF0000]">
                            <?= is_numeric($monthData['totals'][2] ?? null) ? number_format((float)$monthData['totals'][2], 2, ',', '.') : '-' ?>
                        </td>

                        <!-- Col D: Total Personel -->
                        <td class="py-2.5 px-2 border border-slate-400 bg-[#FFFF00] font-black text-base <?= (isset($monthData['totals'][3]) && (float)$monthData['totals'][3] > 0) ? 'text-[#FF0000]' : 'text-slate-900' ?>">
                            <?= is_numeric($monthData['totals'][3] ?? null) ? ((float)$monthData['totals'][3] == 0 ? '-' : number_format((float)$monthData['totals'][3], 2, ',', '.')) : '-' ?>
                        </td>

                        <!-- Cols 4..31: SBWC Totals -->
                        <?php for ($c = 4; $c <= 31; $c++): 
                            $tv = $monthData['totals'][$c] ?? null;
                            $hasTv = (is_numeric($tv) && (float)$tv > 0);
                        ?>
                            <td class="py-2.5 px-2 border border-slate-400 bg-[#FFFF00] text-sm <?= $hasTv ? 'text-slate-950 font-bold' : 'text-slate-600' ?>">
                                <?= is_numeric($tv) ? ((float)$tv == 0 ? '-' : number_format((float)$tv, 2, ',', '.')) : '-' ?>
                            </td>
                        <?php endfor; ?>

                        <!-- Total Hours (Col AG) -->
                        <td class="py-2.5 px-3 border border-slate-400 bg-[#FFFF00] font-black text-base text-slate-950 text-right">
                            <?= is_numeric($monthData['totals'][32] ?? null) ? number_format((float)$monthData['totals'][32], 2, ',', '.') : '-' ?>
                        </td>

                        <!-- Remark Footer -->
                        <td class="py-2.5 px-3 border border-slate-400 bg-[#FFFF00] text-center text-slate-500">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section for Grand Total -->
    <div id="sectionGrandTotal" class="p-5 rounded-2xl bg-slate-800/95 border border-slate-700 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <i class="fa-solid fa-trophy text-lg"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-white uppercase">GRAND TOTAL DOWNTIME TAHUN <?= $tahun ?> (SEMUA ARMADA)</h4>
                    <p class="text-sm text-slate-400">Akumulasi 8 Periode Pelaporan (Januari s/d Agustus <?= $tahun ?>)</p>
                </div>
            </div>
            <span class="px-3 py-1.5 rounded-full text-sm font-mono font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">
                Akumulasi Lengkap
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-900/90 border border-rose-500/30">
                <span class="text-xs font-bold uppercase text-rose-400 block mb-1">Grand Total Unpaid (Repaire Rig)</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black font-mono text-rose-400"><?= number_format((float)($nptData['grand_total'][2] ?? 0), 2, ',', '.') ?></span>
                    <span class="text-sm text-slate-400">Jam</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/90 border border-amber-500/30">
                <span class="text-xs font-bold uppercase text-amber-400 block mb-1">Grand Total Unpaid (Personel)</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black font-mono text-amber-400"><?= number_format((float)($nptData['grand_total'][3] ?? 0), 2, ',', '.') ?></span>
                    <span class="text-sm text-slate-400">Jam</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/90 border border-blue-500/30">
                <span class="text-xs font-bold uppercase text-blue-400 block mb-1">Grand Total SBWC (Standby)</span>
                <?php 
                    $sbwcTot = 0;
                    for ($c = 4; $c <= 31; $c++) {
                        $sbwcTot += (float)($nptData['grand_total'][$c] ?? 0);
                    }
                ?>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black font-mono text-blue-400"><?= number_format($sbwcTot, 2, ',', '.') ?></span>
                    <span class="text-sm text-slate-400">Jam</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/90 border border-emerald-500/30">
                <span class="text-xs font-bold uppercase text-emerald-400 block mb-1">Grand Total NPT All Rig</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black font-mono text-emerald-400"><?= number_format((float)($nptData['grand_total'][32] ?? 0), 2, ',', '.') ?></span>
                    <span class="text-sm text-slate-400">Jam</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateNptYear() {
        const y = document.getElementById('selectTahunNpt').value;
        window.location.href = `<?= base_url('rekap-tahunan/npt') ?>/${y}`;
    }

    function scrollToGrandTotal() {
        const sec = document.getElementById('sectionGrandTotal');
        if (sec) {
            sec.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
<?= $this->endSection() ?>

