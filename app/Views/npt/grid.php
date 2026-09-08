<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Filter Navigation Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <i class="fa-solid fa-calendar-day text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-white"><?= esc($rig['kode']) ?> - Lembar NPT Harian</h3>
                <p class="text-xs text-slate-400">Pilih armada rig dan periode bulan untuk mengisi atau mengedit data downtime</p>
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

            <!-- Tombol Export Excel -->
            <a href="<?= base_url("export/npt/{$rigId}/{$bulan}/{$tahun}") ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 active:scale-95" title="Download data dalam format Excel">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- Quick Stat KPI Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
    </div>

    <!-- Interactive Spreadsheet Matrix Form -->
    <form action="<?= base_url('npt/simpan') ?>" method="POST" id="formNpt">
        <?= csrf_field() ?>
        <input type="hidden" name="rig_id" value="<?= $rigId ?>">
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">

        <div class="rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-700/80 flex items-center justify-between bg-slate-850">
                <div class="flex items-center gap-2 text-xs text-slate-300 font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Tabel Input Downtime (Maksimal 24 Jam per Tanggal)</span>
                </div>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Semua Perubahan</span>
                </button>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-950 text-slate-300 font-semibold uppercase text-[10px] border-b border-slate-700 sticky top-0 z-10">
                        <tr>
                            <th class="py-3 px-3 w-12 text-center border-r border-slate-800 sticky left-0 bg-slate-950 z-20">Tgl</th>
                            <?php foreach ($kategoriList as $k): ?>
                                <th class="py-2.5 px-2.5 text-center min-w-[90px] max-w-[120px] border-r border-slate-800/70" title="<?= esc($k['nama']) ?>">
                                    <span class="block truncate <?= $k['tipe'] == 'UNPAID' ? 'text-rose-400' : 'text-amber-400' ?>"><?= esc($k['nama']) ?></span>
                                    <span class="text-[8px] font-mono opacity-60"><?= $k['tipe'] ?></span>
                                </th>
                            <?php endforeach; ?>
                            <th class="py-3 px-3 text-center min-w-[70px] border-r border-slate-800 bg-slate-900 font-bold text-white">Total (Jam)</th>
                            <th class="py-3 px-3 min-w-[200px]">Remark / Keterangan Masalah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40 text-slate-300">
                        <?php for ($d = 1; $d <= $daysInMonth; $d++): 
                            $tglStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                            $dayTotal = 0;
                            $dayRemark = '';
                        ?>
                        <tr class="hover:bg-slate-750/50 transition" data-day="<?= $d ?>">
                            <td class="py-2 px-3 text-center font-mono font-bold text-white border-r border-slate-800 sticky left-0 bg-slate-850 z-10"><?= $d ?></td>
                            <?php foreach ($kategoriList as $k): 
                                $existingVal = $existingData[$tglStr][$k['id']][0]['jam'] ?? 0;
                                if ($existingVal > 0) {
                                    $dayTotal += $existingVal;
                                    if (empty($dayRemark)) {
                                        $dayRemark = $existingData[$tglStr][$k['id']][0]['remark'] ?? '';
                                    }
                                }
                            ?>
                                <td class="p-1 border-r border-slate-800/70 text-center">
                                    <input type="number" step="0.25" min="0" max="24"
                                        name="npt[<?= $d ?>][<?= $k['id'] ?>]"
                                        value="<?= $existingVal > 0 ? $existingVal : '' ?>"
                                        placeholder="-"
                                        oninput="recalcRow(<?= $d ?>)"
                                        class="npt-input-<?= $d ?> w-full px-2 py-1.5 text-center bg-slate-900/90 border border-transparent hover:border-slate-600 focus:border-blue-500 rounded-lg text-white font-mono text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                                </td>
                            <?php endforeach; ?>
                            <td class="py-2 px-3 text-center font-mono font-bold border-r border-slate-800 bg-slate-900/60" id="rowTotal_<?= $d ?>">
                                <span class="<?= $dayTotal > 0 ? ($dayTotal > 24 ? 'text-rose-400 font-extrabold' : 'text-blue-400') : 'text-slate-500' ?>">
                                    <?= number_format($dayTotal, 2) ?>
                                </span>
                            </td>
                            <td class="p-1">
                                <input type="text" name="remark[<?= $d ?>]" value="<?= esc($dayRemark) ?>" placeholder="Catatan masalah (opsional)"
                                    class="w-full px-3 py-1.5 bg-slate-900/90 border border-transparent hover:border-slate-600 focus:border-blue-500 rounded-lg text-white text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
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
                            <td class="py-3 px-3 text-xs text-slate-400">Total Akumulasi Periode Ini</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-4 border-t border-slate-700/80 flex items-center justify-end bg-slate-850">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan NPT Harian</span>
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
</script>
<?= $this->endSection() ?>
