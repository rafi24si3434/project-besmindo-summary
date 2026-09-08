<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-xl space-y-6">
        <div class="border-b border-slate-700 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white"><?= isset($vendor) ? 'Edit Vendor ' . esc($vendor['nama']) : 'Tambah Vendor Baru' ?></h3>
                <p class="text-xs text-slate-400">Penyedia layanan sumur pihak ketiga</p>
            </div>
            <a href="<?= base_url('master/third-party') ?>" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="<?= isset($vendor) ? base_url('master/third-party/update/' . $vendor['id']) : base_url('master/third-party/simpan') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Vendor</label>
                <input type="text" name="nama" value="<?= old('nama', $vendor['nama'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Contoh: BHI, HALCO, SCHL">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="aktif" id="aktif" value="1" <?= (old('aktif', $vendor['aktif'] ?? 1) == 1) ? 'checked' : '' ?>
                    class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                <label for="aktif" class="text-xs text-slate-300 cursor-pointer">Vendor Aktif</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-700">
                <a href="<?= base_url('master/third-party') ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save text-xs"></i>
                    <span>Simpan Vendor</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
