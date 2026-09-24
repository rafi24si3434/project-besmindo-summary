<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen max-w-2xl mx-auto">
    <div class="ui-card">
        <div class="ui-card-header">
            <div>
                <h3 class="ui-card-header-title">
                    <?= isset($kategori) ? 'Edit Kategori: ' . esc($kategori['nama']) : 'Tambah Kategori Downtime' ?>
                </h3>
                <p class="ui-card-header-sub">Atur parameter pos downtime dan dampak biaya operasional</p>
            </div>
            <a href="<?= base_url('master/kategori') ?>"
                class="inline-flex items-center gap-1.5 text-xs font-medium transition"
                style="color:var(--muted-foreground)"
                onmouseover="this.style.color='var(--foreground)'"
                onmouseout="this.style.color='var(--muted-foreground)'">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <div class="ui-card-content">
            <form action="<?= isset($kategori) ? base_url('master/kategori/update/' . $kategori['id']) : base_url('master/kategori/simpan') ?>"
                method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Nama Kategori -->
                <div class="ui-form-group">
                    <label for="nama">Nama Pos Downtime</label>
                    <input type="text" id="nama" name="nama"
                        value="<?= old('nama', $kategori['nama'] ?? '') ?>" required
                        placeholder="Contoh: Rain, Repaire Rig & Equipment">
                </div>

                <!-- Tipe -->
                <div class="ui-form-group">
                    <label for="tipe">Tipe Klasifikasi</label>
                    <select id="tipe" name="tipe" required>
                        <option value="UNPAID" <?= (old('tipe', $kategori['tipe'] ?? '') == 'UNPAID') ? 'selected' : '' ?>>
                            UNPAID — Kerusakan/Insiden Beban Kontraktor
                        </option>
                        <option value="SBWC" <?= (old('tipe', $kategori['tipe'] ?? '') == 'SBWC') ? 'selected' : '' ?>>
                            SBWC — Standby With Crew (Dibayar Klien)
                        </option>
                    </select>
                    <p class="text-xs" style="color:var(--muted-foreground)">
                        UNPAID secara langsung mengurangi Availability &amp; Reliability rig.
                    </p>
                </div>

                <!-- Urutan -->
                <div class="ui-form-group">
                    <label for="urutan">Nomor Urut Kolom</label>
                    <input type="number" id="urutan" name="urutan"
                        value="<?= old('urutan', $kategori['urutan'] ?? '1') ?>" required min="1">
                    <p class="text-xs" style="color:var(--muted-foreground)">
                        Menentukan urutan kolom dalam tabel NPT dan ekspor Excel.
                    </p>
                </div>

                <!-- Aktif -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="aktif" name="aktif" value="1"
                        <?= (old('aktif', $kategori['aktif'] ?? 1) == 1) ? 'checked' : '' ?>>
                    <label for="aktif" class="cursor-pointer" style="color:var(--card-foreground);text-transform:none;letter-spacing:0">
                        Kategori Aktif Digunakan
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4" style="border-top:1px solid var(--border)">
                    <a href="<?= base_url('master/kategori') ?>"
                        class="px-4 py-2 rounded-md text-xs font-medium transition"
                        style="background:var(--secondary);color:var(--secondary-foreground)"
                        onmouseover="this.style.background='var(--accent)'"
                        onmouseout="this.style.background='var(--secondary)'">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white transition">
                        <i class="fa-solid fa-save text-xs"></i>
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
