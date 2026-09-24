<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--import space-y-6">

    <!-- Header Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 border border-slate-700 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center flex-shrink-0 text-2xl shadow-inner">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-extrabold uppercase tracking-wider">Fitur Pintar</span>
                    <span class="text-xs text-slate-400">Multi-Sheet &amp; Auto-Detect</span>
                </div>
                <h2 class="text-xl font-black text-white tracking-tight mt-1">Pusat Unggah &amp; Import Berkas Excel</h2>
                <p class="text-xs text-slate-300 mt-0.5">Unggah berkas Daily Report atau NPT lapangan. Sistem otomatis mengenali armada rig, memetakan data, dan memperbarui kalkulasi laporan.</p>
            </div>
        </div>

        <!-- Download Template Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="<?= base_url('import/template/daily-report') ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-600 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-400 text-sm"></i>
                <span>Template Daily Report</span>
            </a>
            <a href="<?= base_url('import/template/npt') ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-600 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-excel text-teal-400 text-sm"></i>
                <span>Template NPT</span>
            </a>
        </div>
    </div>

    <!-- Alert / Feedback Hasil Impor -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0 text-rose-400"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('import_results')): 
        $res = session()->getFlashdata('import_results');
    ?>
        <div class="p-5 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 shadow-lg space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-white">Import Berkas Berhasil Diproses!</h3>
                    <p class="text-xs text-emerald-300">Data telah disimpan ke database dan KPI bulanan armada langsung direkalkulasi.</p>
                </div>
            </div>

            <!-- Quick Metrics Hasil Impor -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-700">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sheet Diproses</span>
                    <h4 class="text-xl font-black text-white font-num mt-1"><?= $res['processed_sheets'] ?> <span class="text-xs text-slate-400 font-normal">/ <?= $res['total_sheets'] ?></span></h4>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-700">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pekerjaan Sumur</span>
                    <h4 class="text-xl font-black text-emerald-400 font-num mt-1">+<?= number_format($res['imported_wells'], 0, ',', '.') ?></h4>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-700">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catatan NPT</span>
                    <h4 class="text-xl font-black text-teal-400 font-num mt-1">+<?= number_format($res['imported_npt'], 0, ',', '.') ?></h4>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-700">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lokasi Baru Dibuat</span>
                    <h4 class="text-xl font-black text-indigo-400 font-num mt-1">+<?= number_format($res['new_locations'], 0, ',', '.') ?></h4>
                </div>
            </div>

            <?php if (!empty($res['affected_rigs'])): ?>
                <div class="text-xs text-slate-300">
                    <strong class="text-white">Rig yang terpengaruh &amp; direkalkulasi:</strong>
                    <span class="text-teal-300"><?= implode(', ', $res['affected_rigs']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Log Pesan Detail -->
            <?php if (!empty($res['messages'])): ?>
                <details class="text-xs bg-slate-900/90 rounded-xl p-3 border border-slate-700 text-slate-300 cursor-pointer">
                    <summary class="font-bold text-slate-200">Lihat Catatan Detail Pemrosesan (<?= count($res['messages']) ?>)</summary>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-[11px] text-slate-400">
                        <?php foreach ($res['messages'] as $msg): ?>
                            <li><?= esc($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Form Area Unggah File -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Form Utama -->
        <div class="lg:col-span-2 rounded-2xl bg-slate-800/90 border border-slate-700 shadow-md p-6">
            <form action="<?= base_url('import/proses') ?>" method="POST" enctype="multipart/form-data" class="space-y-6" id="formImport">
                <?= csrf_field() ?>

                <!-- Drag & Drop Zone -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Pilih Berkas Excel Laporan (.xlsx, .xls, .csv)
                    </label>
                    <div id="dropZone" class="border-2 border-dashed border-slate-600 hover:border-blue-500 rounded-2xl p-8 text-center bg-slate-900/50 hover:bg-slate-900/80 transition cursor-pointer flex flex-col items-center justify-center gap-3">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600/15 text-blue-400 border border-blue-500/30 flex items-center justify-center text-3xl shadow-inner">
                            <i class="fa-solid fa-file-excel text-emerald-400"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-white" id="fileNamePreview">Tarik &amp; Letakkan Berkas di Sini</h4>
                            <p class="text-xs text-slate-400 mt-1">atau <span class="text-blue-400 font-bold underline">Klik untuk menjelajahi komputer</span></p>
                        </div>
                        <span class="text-[11px] px-3 py-1 rounded-full bg-slate-800 text-slate-400 border border-slate-700">Mendukung berkas multi-sheet (18 rig dalam 1 file)</span>
                        <input type="file" name="file_excel" id="fileInput" accept=".xlsx,.xls,.csv" class="hidden" required>
                    </div>
                </div>

                <!-- Opsi Mode Deteksi Cerdas -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-3.5 rounded-xl border border-slate-700 bg-slate-900/70 hover:border-blue-500 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="mode" value="auto" checked class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="block text-xs font-bold text-white">Deteksi Otomatis</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">Sistem mengenali jenis Daily Report atau NPT secara mandiri</span>
                        </div>
                    </label>
                    <label class="p-3.5 rounded-xl border border-slate-700 bg-slate-900/70 hover:border-blue-500 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="mode" value="daily_report" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="block text-xs font-bold text-white">Daily Report</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">Fokus pekerjaan sumur, jam MIRU, OPS, dan status job</span>
                        </div>
                    </label>
                    <label class="p-3.5 rounded-xl border border-slate-700 bg-slate-900/70 hover:border-blue-500 cursor-pointer transition flex items-start gap-3">
                        <input type="radio" name="mode" value="npt" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="block text-xs font-bold text-white">NPT Harian</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">Fokus rincian jam downtime kategori SBWC &amp; UNPAID</span>
                        </div>
                    </label>
                </div>

                <!-- Parameter Pelengkap Periode & Rig -->
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-blue-400 text-xs"></i>
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Pengaturan Cadangan (Jika Tidak Tertera di Excel)</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Pilihan Rig Cadangan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1">Armada Rig (Opsional):</label>
                            <select name="rig_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs font-bold text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Otomatis Baca Nama Sheet --</option>
                                <?php foreach ($allRigs as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= esc($r['kode']) ?> - <?= esc($r['nama_rig']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Bulan Cadangan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1">Bulan Operasi:</label>
                            <?php 
                            $bulanList = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            ?>
                            <select name="bulan" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs font-bold text-white focus:ring-2 focus:ring-blue-500">
                                <?php foreach ($bulanList as $num => $nama): ?>
                                    <option value="<?= $num ?>" <?= $num == 9 ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tahun Cadangan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1">Tahun Operasi:</label>
                            <select name="tahun" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs font-bold text-white focus:ring-2 focus:ring-blue-500">
                                <?php for ($y = 2024; $y <= 2028; $y++): ?>
                                    <option value="<?= $y ?>" <?= $y == 2026 ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" id="btnSubmit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                        <span>Mulai Proses Import Data</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Info & Petunjuk -->
        <div class="space-y-4">
            <!-- Kartu Status Database -->
            <div class="rounded-2xl bg-slate-800/90 border border-slate-700 shadow-md p-5 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <i class="fa-solid fa-database text-blue-400"></i>
                    <span>Kapasitas Data Sistem Saat Ini</span>
                </h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900 border border-slate-700/80">
                        <span class="text-xs text-slate-300 font-medium">Rekaman Daily Report</span>
                        <span class="text-sm font-extrabold text-white font-num"><?= number_format($totalDaily, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900 border border-slate-700/80">
                        <span class="text-xs text-slate-300 font-medium">Titik Catatan NPT</span>
                        <span class="text-sm font-extrabold text-teal-400 font-num"><?= number_format($totalNpt, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900 border border-slate-700/80">
                        <span class="text-xs text-slate-300 font-medium">Master Lokasi Terdaftar</span>
                        <span class="text-sm font-extrabold text-indigo-400 font-num"><?= number_format($totalWells, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Pintar -->
            <div class="rounded-2xl bg-slate-800/90 border border-slate-700 shadow-md p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-400"></i>
                    <span>Keunggulan Fitur Cerdas</span>
                </h3>

                <ul class="text-xs text-slate-300 space-y-2.5">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5 text-[11px]"></i>
                        <span><strong>Mendukung Multi-Sheet:</strong> Jika file memiliki 18 sheet rig sekaligus, semuanya akan diproses otomatis.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5 text-[11px]"></i>
                        <span><strong>Pencocokan Rig Fleksibel:</strong> Nama sheet seperti <em>BMS 01</em>, <em>BMS#01</em>, atau <em>BMS 02 ODR BARU</em> otomatis dikenali.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5 text-[11px]"></i>
                        <span><strong>Lokasi Otomatis Dibuat:</strong> Nama sumur baru yang belum ada di master data akan didaftarkan otomatis tanpa error.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-400 mt-0.5 text-[11px]"></i>
                        <span><strong>Auto-Sync KPI:</strong> Begitu impor selesai, tabel Monthly Report dan grafik Dashboard langsung terbarui.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('fileNamePreview');
    const form = document.getElementById('formImport');
    const btnSubmit = document.getElementById('btnSubmit');

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            const f = fileInput.files[0];
            const sizeKB = (f.size / 1024).toFixed(1);
            preview.innerHTML = `<span class="text-emerald-400 font-extrabold">${f.name}</span> <span class="text-slate-400 font-normal">(${sizeKB} KB)</span>`;
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-slate-900');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-slate-900');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            const f = files[0];
            const sizeKB = (f.size / 1024).toFixed(1);
            preview.innerHTML = `<span class="text-emerald-400 font-extrabold">${f.name}</span> <span class="text-slate-400 font-normal">(${sizeKB} KB)</span>`;
        }
    });

    form.addEventListener('submit', () => {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>Sedang Memproses Berkas...</span>`;
    });
});
</script>
<?= $this->endSection() ?>
