<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen max-w-2xl mx-auto">
    <div class="ui-card">
        <div class="ui-card-header">
            <div>
                <h3 class="ui-card-header-title">
                    <?= isset($lokasi) ? 'Edit Lokasi: ' . esc($lokasi['nama_lokasi']) : 'Tambah Lokasi Baru' ?>
                </h3>
                <p class="ui-card-header-sub">Pemberian nama / nomor identifikasi sumur operasi</p>
            </div>
            <a href="<?= base_url('master/lokasi') ?>"
                class="inline-flex items-center gap-1.5 text-xs font-medium transition"
                style="color:var(--muted-foreground)"
                onmouseover="this.style.color='var(--foreground)'"
                onmouseout="this.style.color='var(--muted-foreground)'">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <div class="ui-card-content">
            <form action="<?= isset($lokasi) ? base_url('master/lokasi/update/' . $lokasi['id']) : base_url('master/lokasi/simpan') ?>"
                method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="ui-form-group">
                    <label for="nama_lokasi">Nama / Tag Lokasi</label>
                    <input type="text" id="nama_lokasi" name="nama_lokasi"
                        value="<?= old('nama_lokasi', $lokasi['nama_lokasi'] ?? '') ?>" required
                        placeholder="Contoh: 5Q-69A, YM, 6N-29A">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="aktif" name="aktif" value="1"
                        <?= (old('aktif', $lokasi['aktif'] ?? 1) == 1) ? 'checked' : '' ?>>
                    <label for="aktif" class="cursor-pointer" style="color:var(--card-foreground);text-transform:none;letter-spacing:0">
                        Lokasi Aktif Digunakan
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4" style="border-top:1px solid var(--border)">
                    <a href="<?= base_url('master/lokasi') ?>"
                        class="px-4 py-2 rounded-md text-xs font-medium transition"
                        style="background:var(--secondary);color:var(--secondary-foreground)"
                        onmouseover="this.style.background='var(--accent)'"
                        onmouseout="this.style.background='var(--secondary)'">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white transition">
                        <i class="fa-solid fa-save text-xs"></i>
                        Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
