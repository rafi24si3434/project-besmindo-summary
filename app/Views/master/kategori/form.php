<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-xl space-y-6">
        <div class="border-b border-slate-700 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white"><?= isset($kategori) ? 'Edit Kategori ' . esc($kategori['nama']) : 'Form Kategori Downtime Baru' ?></h3>
                <p class="text-xs text-slate-400">Atur parameter pos downtime dan dampaknya terhadap biaya</p>
            </div>
            <a href="<?= base_url('master/kategori') ?>" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="<?= isset($kategori) ? base_url('master/kategori/update/' . $kategori['id']) : base_url('master/kategori/simpan') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Pos Downtime</label>
                <input type="text" name="nama" value="<?= old('nama', $kategori['nama'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Contoh: SWA Rain, Repaire Rig & Equipment">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tipe Klasifikasi</label>
                <select name="tipe" required class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="UNPAID" <?= (old('tipe', $kategori['tipe'] ?? '') == 'UNPAID') ? 'selected' : '' ?>>UNPAID (Kerusakan/Insiden Beban Kontraktor)</option>
                    <option value="SBWC" <?= (old('tipe', $kategori['tipe'] ?? '') == 'SBWC') ? 'selected' : '' ?>>SBWC (Standby With Crew - Dibayar Klien)</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">UNPAID secara langsung mengurangi Availability & Reliability rig.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor Urut Kolom</label>
                <input type="number" name="urutan" value="<?= old('urutan', $kategori['urutan'] ?? '1') ?>" required min="1"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <p class="text-[11px] text-slate-400 mt-1">Menentukan urutan kolom dalam tabel input NPT dan ekspor lembar Excel.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="aktif" id="aktif" value="1" <?= (old('aktif', $kategori['aktif'] ?? 1) == 1) ? 'checked' : '' ?>
                    class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                <label for="aktif" class="text-xs text-slate-300 cursor-pointer">Kategori Aktif Digunakan</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-700">
                <a href="<?= base_url('master/kategori') ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save text-xs"></i>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
