<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .sec-panel {
        background: var(--card);
        border: 1.5px solid var(--border-strong);
        border-radius: 1rem;
        box-shadow: var(--shadow-card);
    }
    .sec-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted-foreground);
        margin-bottom: 0.375rem;
        display: block;
    }
    .sec-input {
        width: 100%;
        height: 44px;
        padding: 0 0.95rem;
        border-radius: 0.65rem;
        background: var(--background);
        border: 1.5px solid var(--border-strong);
        color: var(--foreground);
        font-size: 0.92rem;
        font-weight: 700;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .sec-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--ring);
    }
</style>

<div class="max-w-3xl mx-auto space-y-5 pb-10">

    <!-- Banner Status Keamanan -->
    <div class="sec-panel p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[11px] font-extrabold tracking-wider uppercase border border-emerald-500/25">
                            ACCOUNT SECURITY &bull; BCRYPT HASH PROTECTION
                        </span>
                        <?php if (!empty($isDefaultPass)): ?>
                            <span class="px-2.5 py-0.5 rounded-md bg-rose-500/15 text-rose-600 dark:text-rose-400 text-[11px] font-extrabold uppercase border border-rose-500/30">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i>Masih Menggunakan Password Standar (admin123)
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 text-[11px] font-bold border border-sky-500/25">
                                <i class="fa-solid fa-lock mr-1"></i>Password Kustom Aktif
                            </span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1" style="color: var(--foreground);">
                        PENGATURAN KEAMANAN AKUN &amp; UBAH PASSWORD
                    </h2>
                    <p class="text-xs sm:text-sm mt-0.5" style="color: var(--muted-foreground);">
                        Amankan akun administrator Anda sebelum sistem di-hosting ke jaringan publik.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Ubah Password & Profil -->
    <div class="sec-panel p-5 sm:p-6">
        <form action="<?= base_url('akun/password') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b" style="border-color: var(--border);">
                <div>
                    <label for="inpNamaUser" class="sec-label">Nama Lengkap Operator / Admin</label>
                    <input type="text" id="inpNamaUser" name="nama" value="<?= esc($user['nama'] ?? '') ?>" required class="sec-input">
                </div>
                <div>
                    <label for="inpUsername" class="sec-label">Username Login</label>
                    <input type="text" id="inpUsername" name="username" value="<?= esc($user['username'] ?? '') ?>" required class="sec-input font-mono">
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="inpPassLama" class="sec-label">
                        <i class="fa-solid fa-key text-amber-500 mr-1"></i>Password Saat Ini (Password Lama)
                    </label>
                    <input type="password" id="inpPassLama" name="password_lama" required
                        placeholder="Masukkan password Anda saat ini..."
                        class="sec-input">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="inpPassBaru" class="sec-label">
                            <i class="fa-solid fa-lock text-emerald-500 mr-1"></i>Password Baru (Min. 6 Karakter)
                        </label>
                        <input type="password" id="inpPassBaru" name="password_baru" required minlength="6"
                            placeholder="Kombinasi huruf, angka & simbol..."
                            class="sec-input">
                    </div>
                    <div>
                        <label for="inpPassKonf" class="sec-label">
                            <i class="fa-solid fa-circle-check text-sky-500 mr-1"></i>Ulangi Password Baru
                        </label>
                        <input type="password" id="inpPassKonf" name="konfirmasi_password" required minlength="6"
                            placeholder="Ketik ulang password baru..."
                            class="sec-input">
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/25 text-xs space-y-1" style="color: var(--foreground);">
                <div class="font-extrabold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-virus"></i>
                    <span>Perlindungan Keamanan Aktif pada Sistem Ini:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11.5px]" style="color: var(--muted-foreground);">
                    <li>Enkripsi satu arah <strong>BCRYPT</strong> (password tidak dapat dibaca meskipun database dilihat).</li>
                    <li><strong>Anti-Brute-Force Throttler</strong>: Kunci otomatis 15 menit jika terdapat &gt;5 percobaan gagal dari IP yang sama.</li>
                    <li><strong>CSRF Token &amp; Secure HTTP Headers</strong> aktif di seluruh formulir transaksi.</li>
                </ul>
            </div>

            <div class="pt-3 border-t flex items-center justify-end gap-3" style="border-color: var(--border);">
                <a href="<?= base_url('dashboard') ?>"
                   class="px-4 py-2.5 rounded-xl text-xs font-extrabold border transition"
                   style="border-color: var(--border-strong); color: var(--foreground);">
                    Kembali ke Dashboard
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-extrabold flex items-center gap-2 shadow-md transition cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Password Baru</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
