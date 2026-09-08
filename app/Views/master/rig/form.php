<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-xl space-y-6">
        <div class="border-b border-slate-700 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white"><?= isset($rig) ? 'Edit Rig ' . esc($rig['kode']) : 'Form Registrasi Rig Baru' ?></h3>
                <p class="text-xs text-slate-400">Silakan lengkapi parameter armada operasional</p>
            </div>
            <a href="<?= base_url('master/rig') ?>" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="<?= isset($rig) ? base_url('master/rig/update/' . $rig['id']) : base_url('master/rig/simpan') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kode Rig</label>
                <input type="text" name="kode" value="<?= old('kode', $rig['kode'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Contoh: BMS#01">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Rig</label>
                <input type="text" name="nama_rig" value="<?= old('nama_rig', $rig['nama_rig'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Contoh: BMS 01">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Operator Daily Rate (ODR / Hari)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-mono">Rp</span>
                    <input type="number" name="odr" value="<?= old('odr', $rig['odr'] ?? '0') ?>" required min="0"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Contoh: 108000000">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Nilai tarif kontrak harian rig yang digunakan untuk menghitung Incentive Target dan Pendapatan Aktual.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="aktif" id="aktif" value="1" <?= (old('aktif', $rig['aktif'] ?? 1) == 1) ? 'checked' : '' ?>
                    class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                <label for="aktif" class="text-xs text-slate-300 cursor-pointer">Armada Aktif Beroperasi</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-700">
                <a href="<?= base_url('master/rig') ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save text-xs"></i>
                    <span>Simpan Rig</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
