<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 pb-12">

    <!-- ═══ 1. HEADER TELEMETRI UTAMA ═══ -->
    <div class="bms-bezel-shell">
        <div class="bms-bezel-core p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/30 text-sky-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-bell-concierge text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-500/15 text-sky-400 border border-sky-500/30">
                            TELEMETRY UX // DESIGN SYSTEM
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-white tracking-tight mt-1">
                        Pusat Desain: Tampilan Error &amp; Sistem Notifikasi
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Standar antarmuka telemetri untuk penanganan galat HTTP, notifikasi mengambang (toast), dan dialog konfirmasi taktil.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>ENGINE ACTIVE</span>
                </span>
            </div>
        </div>
    </div>

    <!-- ═══ 2. KATALOG TAMPILAN ERROR HTTP STANDAR SIMOR (4 KARTU) ═══ -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-md bg-rose-500/15 border border-rose-500/30 text-rose-400 font-mono text-xs font-black flex items-center justify-center">01</span>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-200">
                    Katalog Tampilan Error Layar Penuh (HTTP Error Screens)
                </h3>
            </div>
            <span class="text-xs text-slate-400 hidden sm:inline">Klik kartu atau tombol untuk melihat halaman penuh</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- Card Error 404 -->
            <div class="lh-surface-card p-5 flex flex-col justify-between gap-4 border-t-4 border-t-sky-500 group hover:border-sky-400 transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-3xl font-black text-sky-400 tracking-tight">404</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-sky-500/10 border border-sky-500/30 text-sky-400">
                            NOT FOUND
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-100 group-hover:text-sky-400 transition">
                            Halaman / Rute Tidak Ditemukan
                        </h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Dilengkapi radar telemetri berputar, pembacaan URL yang dituju, status aman, dan navigasi cepat ke modul utama.
                        </p>
                    </div>
                </div>

                <a href="<?= base_url('errors/preview/404') ?>" target="_blank"
                    class="w-full py-2.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Tampilan 404</span>
                </a>
            </div>

            <!-- Card Error 500 -->
            <div class="lh-surface-card p-5 flex flex-col justify-between gap-4 border-t-4 border-t-rose-500 group hover:border-rose-400 transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-3xl font-black text-rose-500 tracking-tight">500</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 border border-rose-500/30 text-rose-400">
                            SERVER ERROR
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-100 group-hover:text-rose-400 transition">
                            Kendala Sistem &amp; Interlock
                        </h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Menampilkan generator kode tiket insiden unik (1-klik salin), jaminan rollback data, dan tombol muat ulang darurat.
                        </p>
                    </div>
                </div>

                <a href="<?= base_url('errors/preview/500') ?>" target="_blank"
                    class="w-full py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Tampilan 500</span>
                </a>
            </div>

            <!-- Card Error 403 -->
            <div class="lh-surface-card p-5 flex flex-col justify-between gap-4 border-t-4 border-t-amber-500 group hover:border-amber-400 transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-3xl font-black text-amber-500 tracking-tight">403</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/10 border border-amber-500/30 text-amber-400">
                            FORBIDDEN
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-100 group-hover:text-amber-400 transition">
                            Akses Otoritas Ditolak
                        </h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Peringatan perimeter keamanan, verifikasi sesi kedaluwarsa, tombol login ulang, dan perlindungan firewall.
                        </p>
                    </div>
                </div>

                <a href="<?= base_url('errors/preview/403') ?>" target="_blank"
                    class="w-full py-2.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Tampilan 403</span>
                </a>
            </div>

            <!-- Card Error 400 -->
            <div class="lh-surface-card p-5 flex flex-col justify-between gap-4 border-t-4 border-t-cyan-500 group hover:border-cyan-400 transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-3xl font-black text-cyan-400 tracking-tight">400</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                            BAD REQUEST
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-100 group-hover:text-cyan-400 transition">
                            Parameter Tidak Valid
                        </h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Mendeteksi ketidaksesuaian payload data telemetri, token CSRF tidak cocok, atau URL query corrupt.
                        </p>
                    </div>
                </div>

                <a href="<?= base_url('errors/preview/400') ?>" target="_blank"
                    class="w-full py-2.5 px-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>Buka Tampilan 400</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ═══ 3. INTERACTIVE TOAST NOTIFICATION PLAYGROUND ═══ -->
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-mono text-xs font-black flex items-center justify-center">02</span>
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-200">
                Pusat Pengujian Notifikasi Mengambang (Floating Toasts)
            </h3>
        </div>

        <div class="lh-surface-card p-5 space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 lh-divider-b pb-4">
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-100">
                        Klik Tombol di Bawah untuk Menguji Toast Telemetri Langsung di Layar Anda
                    </h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Dilengkapi bar penghitung waktu mundur otomatis (progress line), jeda saat di-hover, tombol dismiss 'X', dan nada audio telemetri.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <label class="flex items-center gap-2 text-xs text-slate-300 font-bold cursor-pointer">
                        <input type="checkbox" id="chkAudioFeedback" checked class="rounded border-slate-600 text-sky-500 focus:ring-0">
                        <span>Audio Feedback Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Trigger Toast -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                <!-- Success Toast Trigger -->
                <button type="button" onclick="triggerToastDemo('success')"
                    class="p-3.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-bold text-xs transition flex items-center justify-between gap-2 text-left cursor-pointer active:scale-98">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-base text-emerald-400"></i>
                        <div>
                            <div class="font-extrabold text-white text-[13px]">Toast Sukses</div>
                            <div class="text-[10px] opacity-80 font-mono">TELEMETRY // OK_200</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-play text-xs opacity-60"></i>
                </button>

                <!-- Error Toast Trigger -->
                <button type="button" onclick="triggerToastDemo('error')"
                    class="p-3.5 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs transition flex items-center justify-between gap-2 text-left cursor-pointer active:scale-98">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-base text-rose-400"></i>
                        <div>
                            <div class="font-extrabold text-white text-[13px]">Toast Gagal / Error</div>
                            <div class="text-[10px] opacity-80 font-mono">ANOMALY // ERR_500</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-play text-xs opacity-60"></i>
                </button>

                <!-- Warning Toast Trigger -->
                <button type="button" onclick="triggerToastDemo('warning')"
                    class="p-3.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 font-bold text-xs transition flex items-center justify-between gap-2 text-left cursor-pointer active:scale-98">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-shield-halved text-base text-amber-400"></i>
                        <div>
                            <div class="font-extrabold text-white text-[13px]">Toast Peringatan</div>
                            <div class="text-[10px] opacity-80 font-mono">ALERT // WARN_300</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-play text-xs opacity-60"></i>
                </button>

                <!-- Info Toast Trigger -->
                <button type="button" onclick="triggerToastDemo('info')"
                    class="p-3.5 rounded-xl border border-sky-500/30 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-bold text-xs transition flex items-center justify-between gap-2 text-left cursor-pointer active:scale-98">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-info text-base text-sky-400"></i>
                        <div>
                            <div class="font-extrabold text-white text-[13px]">Toast Info Sistem</div>
                            <div class="text-[10px] opacity-80 font-mono">SYSTEM // INFO</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-play text-xs opacity-60"></i>
                </button>

            </div>

            <!-- Trigger Stack Button -->
            <div class="pt-2 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
                <span>Ingin melihat antrian tumpuk (*stacked toasts*)? Klik tombol di kanan:</span>
                <button type="button" onclick="triggerStackDemo()"
                    class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>Tampilkan Rangkaian 3 Toast Berturut-turut</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ═══ 4. INTERACTIVE TACTILE CONFIRMATION DIALOG (MODAL) ═══ -->
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-amber-500/15 border border-amber-500/30 text-amber-400 font-mono text-xs font-black flex items-center justify-center">03</span>
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-200">
                Dialog Konfirmasi Taktil (Executive Modal Confirmation)
            </h3>
        </div>

        <div class="lh-surface-card p-5 space-y-4">
            <div>
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-100">
                    Pengganti Elegan untuk `window.confirm()` Browser Bawaan
                </h4>
                <p class="text-xs text-slate-400 mt-0.5">
                    Menghadirkan dialog konfirmasi berbasis promise (`await SimorModal.confirm(...)`) dengan efek kaca buram latar, fokus otomatis, dan dukungan keyboard (`Esc` &amp; `Enter`).
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">

                <!-- Danger Modal -->
                <button type="button" onclick="triggerModalDemo('danger')"
                    class="p-4 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-slate-200 font-bold text-xs transition flex flex-col gap-2 text-left cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/20 text-rose-400 border border-rose-500/40">
                            DANGER // DELETE
                        </span>
                        <i class="fa-solid fa-trash-can text-rose-400 group-hover:scale-110 transition"></i>
                    </div>
                    <div class="font-extrabold text-white text-sm mt-1">Konfirmasi Hapus Data</div>
                    <p class="text-[11px] text-slate-400">Uji dialog saat menghapus laporan sumur atau baris NPT.</p>
                </button>

                <!-- Warning Modal -->
                <button type="button" onclick="triggerModalDemo('warning')"
                    class="p-4 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-slate-200 font-bold text-xs transition flex flex-col gap-2 text-left cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/20 text-amber-400 border border-amber-500/40">
                            WARNING // UNSAVED
                        </span>
                        <i class="fa-solid fa-triangle-exclamation text-amber-400 group-hover:scale-110 transition"></i>
                    </div>
                    <div class="font-extrabold text-white text-sm mt-1">Perubahan Belum Disimpan</div>
                    <p class="text-[11px] text-slate-400">Peringatan saat operator hendak beralih halaman sebelum menekan simpan.</p>
                </button>

                <!-- Success / Info Modal -->
                <button type="button" onclick="triggerModalDemo('success')"
                    class="p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-slate-200 font-bold text-xs transition flex flex-col gap-2 text-left cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                            ACTION // PUBLISH
                        </span>
                        <i class="fa-solid fa-file-export text-emerald-400 group-hover:scale-110 transition"></i>
                    </div>
                    <div class="font-extrabold text-white text-sm mt-1">Publikasi Monthly Report</div>
                    <p class="text-[11px] text-slate-400">Konfirmasi finalisasi laporan RAU bulanan armada rig ke manajemen.</p>
                </button>

            </div>
        </div>
    </div>

    <!-- ═══ 5. INLINE TELEMETRY ALERTS MATRIX (UNTUK FORM & TABEL) ═══ -->
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-blue-500/15 border border-blue-500/30 text-blue-400 font-mono text-xs font-black flex items-center justify-center">04</span>
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-200">
                Pola Peringatan Sejajar Dalam Formulir (Inline Telemetry Alerts)
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <!-- Inline Success -->
            <div class="simor-inline-alert simor-inline-alert--success">
                <i class="fa-solid fa-circle-check text-lg shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <h5 class="text-xs font-extrabold text-emerald-400 tracking-wide uppercase font-mono">STATUS // DATA TERSINKRON</h5>
                    <p class="text-xs text-slate-300">Catatan harian 24 Jam Rig 01 tanggal 15 September 2026 telah sesuai dengan formula neraca jam.</p>
                </div>
            </div>

            <!-- Inline Error -->
            <div class="simor-inline-alert simor-inline-alert--error">
                <i class="fa-solid fa-triangle-exclamation text-lg shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <h5 class="text-xs font-extrabold text-rose-400 tracking-wide uppercase font-mono">ANOMALI // VALIDASI GAGAL</h5>
                    <p class="text-xs text-slate-300">Total akumulasi jam (25.50 Jam) melebihi batas siklus 24.00 Jam per hari kalender.</p>
                </div>
            </div>

            <!-- Inline Warning -->
            <div class="simor-inline-alert simor-inline-alert--warning">
                <i class="fa-solid fa-shield-halved text-lg shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <h5 class="text-xs font-extrabold text-amber-400 tracking-wide uppercase font-mono">PERINGATAN // JADWAL TERAKHIR</h5>
                    <p class="text-xs text-slate-300">Ini adalah hari terakhir pekerjaan Well #5. Segera daftarkan sumur baru sebelum menyimpan.</p>
                </div>
            </div>

            <!-- Inline Info -->
            <div class="simor-inline-alert simor-inline-alert--info">
                <i class="fa-solid fa-circle-info text-lg shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <h5 class="text-xs font-extrabold text-sky-400 tracking-wide uppercase font-mono">INFORMASI // INTEGRITAS DATA</h5>
                    <p class="text-xs text-slate-300">Seluruh perubahan master data rig dan kategori otomatis tercatat pada jejak audit sistem.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Interactive Showcase Script -->
<script>
    function triggerToastDemo(type) {
        const withSound = document.getElementById('chkAudioFeedback')?.checked ?? true;

        if (type === 'success') {
            SimorToast.show({
                type: 'success',
                title: 'Data Operasi Berhasil Disimpan',
                message: 'Log harian Rig 01 Sumur #5 tanggal 15/09/2026 (24.00 Jam) sukses diperbarui ke database.',
                sound: withSound
            });
        } else if (type === 'error') {
            SimorToast.show({
                type: 'error',
                title: 'Gagal Menyimpan Log Harian',
                message: 'Total akumulasi (25.50 Jam) melebihi batas 24 Jam. Silakan kurangi jam MIRU atau Operasi.',
                sound: withSound
            });
        } else if (type === 'warning') {
            SimorToast.show({
                type: 'warning',
                title: 'Peringatan Jadwal Sumur',
                message: 'Jadwal Well #5 telah selesai. Siap melanjutkan ke pendaftaran Well #6?',
                sound: withSound
            });
        } else if (type === 'info') {
            SimorToast.show({
                type: 'info',
                title: 'Sinkronisasi NPT Selesai',
                message: 'Matriks downtime bulanan telah disinkronkan otomatis dengan tabel RAU dan Rekap Tahunan.',
                sound: withSound
            });
        }
    }

    function triggerStackDemo() {
        const withSound = document.getElementById('chkAudioFeedback')?.checked ?? true;
        SimorToast.info('Memulai pengecekan telemetri armada rig...', 'Sinkronisasi Dimulai');
        setTimeout(() => {
            SimorToast.warning('Rig 02 terdeteksi mengalami NPT cuaca hujan (3.50 Jam).', 'Peringatan Downtime');
        }, 350);
        setTimeout(() => {
            SimorToast.success('Seluruh 18 rig berhasil terhubung dan siap beroperasi.', 'Pengecekan Selesai');
        }, 700);
    }

    async function triggerModalDemo(type) {
        if (type === 'danger') {
            const confirmed = await SimorModal.confirm({
                type: 'danger',
                title: 'Hapus Pekerjaan Sumur #4?',
                message: 'Tindakan ini akan menghapus seluruh catatan log harian (24 Jam) dan entri downtime pada sumur ini. Tindakan ini tidak dapat dibatalkan.',
                confirmText: 'Ya, Hapus Sumur Permanen',
                cancelText: 'Batalkan'
            });
            if (confirmed) {
                SimorToast.success('Pekerjaan Sumur #4 berhasil dihapus dari sistem.', 'Penghapusan Sukses');
            } else {
                SimorToast.info('Tindakan penghapusan dibatalkan oleh operator.', 'Dibatalkan');
            }
        } else if (type === 'warning') {
            const confirmed = await SimorModal.confirm({
                type: 'warning',
                title: 'Beralih Halaman Tanpa Menyimpan?',
                message: 'Terdapat perubahan angka jam kerja yang belum disimpan ke database. Jika Anda berpindah, data yang belum disimpan akan hilang.',
                confirmText: 'Tetap Beralih',
                cancelText: 'Lanjutkan Pengisian'
            });
            if (confirmed) {
                SimorToast.warning('Beralih halaman tanpa menyimpan perubahan.', 'Perubahan Diabaikan');
            }
        } else if (type === 'success') {
            const confirmed = await SimorModal.confirm({
                type: 'success',
                title: 'Publikasikan Laporan Bulanan (RAU)?',
                message: 'Laporan RAU periode September 2026 untuk Rig 01 akan dikunci dan diserahkan ke sistem rekapitulasi eksekutif.',
                confirmText: 'Ya, Publikasikan Sekarang',
                cancelText: 'Cek Kembali'
            });
            if (confirmed) {
                SimorToast.success('Laporan bulanan sukses dipublikasikan ke Dewan Manajemen.', 'Publikasi Selesai');
            }
        }
    }
</script>
<?= $this->endSection() ?>
