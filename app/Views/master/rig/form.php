<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen max-w-2xl mx-auto">
    <div class="ui-card">
        <!-- Card Header -->
        <div class="ui-card-header">
            <div>
                <h3 class="ui-card-header-title">
                    <?= isset($rig) ? 'Edit Rig ' . esc($rig['kode']) : 'Registrasi Rig Baru' ?>
                </h3>
                <p class="ui-card-header-sub">Lengkapi parameter armada operasional</p>
            </div>
            <a href="<?= base_url('master/rig') ?>"
                class="inline-flex items-center gap-1.5 text-xs font-medium transition"
                style="color:var(--muted-foreground)"
                onmouseover="this.style.color='var(--foreground)'"
                onmouseout="this.style.color='var(--muted-foreground)'">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <!-- Card Body -->
        <div class="ui-card-content">
            <form action="<?= isset($rig) ? base_url('master/rig/update/' . $rig['id']) : base_url('master/rig/simpan') ?>"
                method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Kode Rig -->
                <div class="ui-form-group">
                    <label for="kode">Kode Rig</label>
                    <input type="text" id="kode" name="kode"
                        value="<?= old('kode', $rig['kode'] ?? '') ?>" required
                        placeholder="Contoh: BMS 01">
                </div>

                <!-- Nama Rig -->
                <div class="ui-form-group">
                    <label for="nama_rig">Nama Rig</label>
                    <input type="text" id="nama_rig" name="nama_rig"
                        value="<?= old('nama_rig', $rig['nama_rig'] ?? '') ?>" required
                        placeholder="Contoh: RIG BMS 01">
                </div>

                <!-- ODR -->
                <div class="ui-form-group">
                    <label for="odr">Operator Daily Rate (ODR / Hari)</label>
                    <div class="ui-input-wrapper">
                        <span class="ui-input-icon font-mono text-xs">Rp</span>
                        <input type="number" id="odr" name="odr"
                            value="<?= old('odr', $rig['odr'] ?? '0') ?>" required min="0"
                            placeholder="Contoh: 26500000">
                    </div>
                    <p class="text-xs" style="color:var(--muted-foreground)">
                        Tarif kontrak harian rig — digunakan untuk menghitung Revenue Target &amp; Aktual.
                    </p>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="aktif" name="aktif" value="1"
                        <?= (old('aktif', $rig['aktif'] ?? 1) == 1) ? 'checked' : '' ?>>
                    <label for="aktif" class="cursor-pointer" style="color:var(--card-foreground);text-transform:none;letter-spacing:0">
                        Armada Aktif Beroperasi
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4" style="border-top:1px solid var(--border)">
                    <a href="<?= base_url('master/rig') ?>"
                        class="px-4 py-2 rounded-md text-xs font-medium transition"
                        style="background:var(--secondary);color:var(--secondary-foreground)"
                        onmouseover="this.style.background='var(--accent)'"
                        onmouseout="this.style.background='var(--secondary)'">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-xs font-semibold transition bg-blue-600 hover:bg-blue-500 text-white">
                        <i class="fa-solid fa-save text-xs"></i>
                        Simpan Rig
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
