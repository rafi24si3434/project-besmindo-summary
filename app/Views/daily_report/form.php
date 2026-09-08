<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-slate-800 border border-slate-700 shadow-xl space-y-6">
        <div class="border-b border-slate-700 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white"><?= isset($report) ? "Edit Pekerjaan Sumur #{$report['no_well']}" : "Form Input Pekerjaan Sumur #{$nextNoWell}" ?></h3>
                <p class="text-xs text-slate-400">Armada Rig: <strong class="text-blue-400"><?= esc($rig['kode']) ?></strong> (Periode Bulan <?= $report['bulan'] ?? $bulan ?>/<?= $report['tahun'] ?? $tahun ?>)</p>
            </div>
            <a href="<?= base_url('daily-report/' . ($report['rig_id'] ?? $rigId) . '/' . ($report['bulan'] ?? $bulan) . '/' . ($report['tahun'] ?? $tahun)) ?>" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Matriks
            </a>
        </div>

        <form action="<?= isset($report) ? base_url('daily-report/update/' . $report['id']) : base_url('daily-report/simpan') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $report['rig_id'] ?? $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $report['bulan'] ?? $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $report['tahun'] ?? $tahun ?>">

            <!-- Bagian 1: Identitas Sumur -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-750">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">No Well</label>
                    <input type="number" name="no_well" value="<?= old('no_well', $report['no_well'] ?? $nextNoWell) ?>" required min="1"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-num font-bold text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Location (Nama Sumur)</label>
                    <select name="lokasi_id" required class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs font-semibold focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasiList as $lok): ?>
                            <option value="<?= $lok['id'] ?>" <?= (old('lokasi_id', $report['lokasi_id'] ?? '') == $lok['id']) ? 'selected' : '' ?>>
                                <?= esc($lok['nama_lokasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Distance (Rp)</label>
                    <input type="number" name="jarak" value="<?= old('jarak', $report['jarak'] ?? '0') ?>" min="0"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-num text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= old('tanggal_mulai', $report['tanggal_mulai'] ?? '') ?>"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= old('tanggal_selesai', $report['tanggal_selesai'] ?? '') ?>"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Bagian 2: Jam Operasi Utama (MIRU & OPS) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-xl bg-lime-950/20 border border-lime-500/30">
                    <label class="block text-xs font-bold text-lime-400 uppercase mb-1.5">MIRU (Jam)</label>
                    <input type="number" step="0.25" min="0" name="miru_jam" id="miru_jam" value="<?= old('miru_jam', $report['miru_jam'] ?? '0') ?>" oninput="calcTotalJam()" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-lime-400 font-num font-bold text-sm focus:ring-2 focus:ring-lime-500">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi Move In / Rig Up</p>
                </div>

                <div class="p-3.5 rounded-xl bg-lime-950/20 border border-lime-500/30">
                    <label class="block text-xs font-bold text-lime-400 uppercase mb-1.5">OPS (Jam)</label>
                    <input type="number" step="0.25" min="0" name="ops_jam" id="ops_jam" value="<?= old('ops_jam', $report['ops_jam'] ?? '0') ?>" oninput="calcTotalJam()" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-lime-400 font-num font-bold text-sm focus:ring-2 focus:ring-lime-500">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi Operasi Pengeboran / Workover</p>
                </div>
            </div>

            <!-- Bagian 3: Pos Rincian Downtime (SBWC & UNPAID persis seperti Excel) -->
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-750 space-y-4">
                <div class="border-b border-slate-750 pb-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-yellow-400 uppercase flex items-center gap-1.5">
                        <i class="fa-solid fa-clock"></i>
                        <span>Rincian Jam Downtime Per Pos (SBWC & UNPAID)</span>
                    </span>
                    <span class="text-[11px] text-slate-400">Total DT: <strong id="previewTotalDt" class="text-orange-300 font-num">0.00</strong> Jam</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <?php foreach ($kategoriList as $k): 
                        $kId = $k['id'];
                        $val = $dtMap[$kId] ?? 0;
                    ?>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 truncate mb-1" title="<?= esc($k['nama']) ?>">
                            <?= esc($k['nama']) ?>
                            <span class="text-[9px] <?= $k['tipe'] == 'UNPAID' ? 'text-rose-400' : 'text-yellow-400' ?> font-mono">(<?= $k['tipe'] ?>)</span>
                        </label>
                        <input type="number" step="0.25" min="0" name="dt[<?= $kId ?>]" value="<?= $val > 0 ? $val : '' ?>" placeholder="0"
                            oninput="calcTotalJam()"
                            class="dt-input w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-white font-num text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Ringkasan Total Jam Sumur -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-300 uppercase">Formula:</span>
                    <span class="text-xs text-slate-400">Total Jam = MIRU + OPS + Total DT</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-[11px] text-slate-400 block">TOTAL HOURS:</span>
                        <span id="previewTotalJam" class="font-num font-black text-xl text-emerald-400">0.00 Jam</span>
                    </div>
                </div>
            </div>

            <!-- Status Job & Remark -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Status Job</label>
                    <select name="status_job" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-semibold text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="JOB COMPLETED" <?= (old('status_job', $report['status_job'] ?? '') == 'JOB COMPLETED') ? 'selected' : '' ?>>JOB COMPLETED</option>
                        <option value="JOB PROGRESS" <?= (old('status_job', $report['status_job'] ?? '') == 'JOB PROGRESS') ? 'selected' : '' ?>>JOB PROGRESS</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Remark NPT / Catatan</label>
                    <input type="text" name="remark" value="<?= old('remark', $report['remark'] ?? '') ?>" placeholder="Catatan khusus kendala/operasi sumur"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-700">
                <a href="<?= base_url('daily-report/' . ($report['rig_id'] ?? $rigId) . '/' . ($report['bulan'] ?? $bulan) . '/' . ($report['tahun'] ?? $tahun)) ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-lg shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>SIMPAN PEKERJAAN SUMUR</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function calcTotalJam() {
        const miru = parseFloat(document.getElementById('miru_jam').value) || 0;
        const ops = parseFloat(document.getElementById('ops_jam').value) || 0;
        
        let sumDt = 0;
        document.querySelectorAll('.dt-input').forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) sumDt += val;
        });

        document.getElementById('previewTotalDt').innerText = sumDt.toFixed(2);
        
        const total = miru + ops + sumDt;
        document.getElementById('previewTotalJam').innerText = total.toFixed(2) + ' Jam';
    }
    calcTotalJam();
</script>
<?= $this->endSection() ?>
