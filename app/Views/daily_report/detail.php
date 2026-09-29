<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen space-y-6 max-w-6xl mx-auto pb-16">

    <!-- ═══ 1. TOMBOL KEMBALI BESAR & HEADER SUMUR (RAMAH ORANG TUA / SENIOR) ═══ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-slate-850 border-2 border-slate-700 shadow-xl">
        <div class="flex items-center gap-4">
            <!-- Tombol Kembali Besar -->
            <a href="<?= esc($backUrl) ?>" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-sm shadow-lg shadow-blue-600/30 border border-blue-400/30 transition-all active:scale-95 flex-shrink-0"
               title="Kembali ke halaman ringkasan daily report">
                <i class="fa-solid fa-arrow-left text-base"></i>
                <span>KEMBALI</span>
            </a>
            
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-xl bg-slate-800 text-amber-300 border border-amber-500/40 text-xs font-mono font-black">
                        NO. WELL #<?= esc($report['no_well']) ?>
                    </span>
                    <span class="text-xs text-slate-300 font-semibold">
                        Rig <?= esc($rig['kode']) ?> (<?= esc($rig['nama_rig']) ?>)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <span><?= esc($namaLokasi) ?></span>
                </h1>
            </div>
        </div>

        <!-- Status Pekerjaan Badge Besar -->
        <div>
            <?php $stDetail = strtoupper(trim((string)($report['status_job'] ?? ''))); ?>
            <?php if ($stDetail === 'JOB COMPLETED'): ?>
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-emerald-500/20 text-emerald-300 border-2 border-emerald-500/50 text-xs sm:text-sm font-black">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SELESAI (JOB COMPLETED)</span>
                </div>
            <?php elseif ($stDetail === 'JOB SUSPEND'): ?>
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-rose-500/20 text-rose-300 border-2 border-rose-500/50 text-xs sm:text-sm font-black">
                    <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                    <span>DITUNDA (JOB SUSPEND)</span>
                </div>
            <?php else: ?>
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-amber-500/20 text-amber-300 border-2 border-amber-500/50 text-xs sm:text-sm font-black">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>SEDANG BERJALAN (PROGRESS)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══ 2. KARTU ANGKA UTAMA (BESAR, JELAS, MUDAH DIBACA) ═══════════════════ -->
    <?php
        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $totalHariLog = count($logs);
    ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <!-- Total Jam Kerja -->
        <div class="p-5 rounded-3xl bg-slate-900 border-2 border-emerald-500/40 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Total Waktu Kerja</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl sm:text-4xl font-black font-num text-emerald-400"><?= number_format($totalJam, 2) ?></span>
                <span class="text-sm font-bold text-slate-300">Jam</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Akumulasi seluruh waktu sumur</p>
        </div>

        <!-- Jam Operasi (OPS) -->
        <div class="p-5 rounded-3xl bg-slate-900 border-2 border-blue-500/40 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Jam Operasi (OPS)</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-gears"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl sm:text-4xl font-black font-num text-sky-400"><?= number_format($opsJam, 2) ?></span>
                <span class="text-sm font-bold text-slate-300">Jam</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Waktu rig aktif bekerja</p>
        </div>

        <!-- Jam Pindah & MIRU -->
        <div class="p-5 rounded-3xl bg-slate-900 border-2 border-lime-500/40 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Jam MIRU / Moving</span>
                <div class="w-9 h-9 rounded-xl bg-lime-500/20 text-lime-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl sm:text-4xl font-black font-num text-lime-400"><?= number_format($miruJam, 2) ?></span>
                <span class="text-sm font-bold text-slate-300">Jam</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Jarak tempuh: <strong class="text-white"><?= (float)$report['jarak'] > 0 ? rtrim(rtrim(number_format((float)$report['jarak'], 2, '.', ''), '0'), '.') : '0' ?> KM</strong></p>
        </div>

        <!-- Total Kendala (Downtime) -->
        <div class="p-5 rounded-3xl bg-slate-900 border-2 <?= $totalDt > 0 ? 'border-amber-500/60' : 'border-slate-700' ?> shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Jam Kendala (NPT)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl sm:text-4xl font-black font-num <?= $totalDt > 0 ? 'text-amber-400' : 'text-slate-400' ?>"><?= number_format($totalDt, 2) ?></span>
                <span class="text-sm font-bold text-slate-300">Jam</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">SBWC: <?= number_format($sbwcJam, 2) ?>h | UNPAID: <?= number_format($unpaidJam, 2) ?>h</p>
        </div>

    </div>

    <!-- ═══ 3. KARTU INFORMASI LENGKAP SUMUR ════════════════════════════════════ -->
    <div class="p-6 rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-400"></i>
                <span>Informasi Rinci Pekerjaan Sumur</span>
            </h2>
            <a href="<?= base_url('daily-report/edit/' . $report['id']) ?>" 
               class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 border border-amber-500/30 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Data Sumur</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <!-- Nama Lokasi -->
            <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800">
                <span class="text-xs font-bold text-slate-400 block mb-1">Nama / Tag Lokasi:</span>
                <span class="text-base font-black text-white"><?= esc($namaLokasi) ?></span>
            </div>

            <!-- Tanggal Mulai -->
            <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800">
                <span class="text-xs font-bold text-slate-400 block mb-1">Tanggal Mulai:</span>
                <span class="text-base font-black text-white">
                    <?= $report['tanggal_mulai'] ? date('d F Y', strtotime($report['tanggal_mulai'])) : '-' ?>
                </span>
            </div>

            <!-- Tanggal Selesai -->
            <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800">
                <span class="text-xs font-bold text-slate-400 block mb-1">Tanggal Selesai:</span>
                <span class="text-base font-black text-white">
                    <?= $report['tanggal_selesai'] ? date('d F Y', strtotime($report['tanggal_selesai'])) : '-' ?>
                </span>
            </div>

            <!-- Durasi Tercatat -->
            <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800">
                <span class="text-xs font-bold text-slate-400 block mb-1">Total Hari Tercatat:</span>
                <span class="text-base font-black text-amber-300 font-num"><?= $totalHariLog ?> Hari Log</span>
            </div>
        </div>

        <!-- Catatan / Remark Operasi -->
        <?php if (!empty($report['remark'])): ?>
        <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800">
            <span class="text-xs font-bold text-slate-400 block mb-1 uppercase tracking-wider">Catatan Operasi / Uraian Pekerjaan:</span>
            <p class="text-sm font-medium text-slate-200 leading-relaxed italic">"<?= esc($report['remark']) ?>"</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- ═══ 4. TABEL LOG HARIAN (DETAIL PER HARI) ═══════════════════════════════ -->
    <div class="rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-xl overflow-hidden space-y-0">
        <!-- Header Tabel -->
        <div class="bg-gradient-to-r from-slate-850 to-slate-800 p-5 border-b-2 border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-black text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-list-check text-emerald-400"></i>
                    <span>Rincian Log Harian Operasi (Per Hari)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan waktu MIRU, operasi, dan jam kendala setiap harinya pada sumur ini</p>
            </div>
            
            <a href="<?= base_url("daily-report/log-harian/{$rig['id']}/{$report['bulan']}/{$report['tahun']}") ?>" 
               class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Buka Form Input Log Harian</span>
            </a>
        </div>

        <!-- Tabel Isi -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#0B1E4A] text-white text-xs font-extrabold uppercase border-b-2 border-slate-700 tracking-wider text-center select-none">
                    <tr>
                        <th class="py-3.5 px-3 w-14 border border-slate-700">Hari</th>
                        <th class="py-3.5 px-4 w-36 border border-slate-700 text-left">Tanggal</th>
                        <th class="py-3.5 px-3 w-28 border border-slate-700 text-right text-lime-400">MIRU (Jam)</th>
                        <th class="py-3.5 px-3 w-28 border border-slate-700 text-right text-lime-300">OPS (Jam)</th>
                        <th class="py-3.5 px-3 w-28 border border-slate-700 text-right text-yellow-300">Downtime</th>
                        <th class="py-3.5 px-3 w-28 border border-slate-700 text-right text-emerald-400 font-black">Total Jam</th>
                        <th class="py-3.5 px-5 border border-slate-700 text-left">Uraian / Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-sm font-num text-slate-200">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-sans">
                            <div class="max-w-sm mx-auto space-y-2">
                                <i class="fa-solid fa-folder-open text-3xl text-slate-600"></i>
                                <p class="font-bold text-slate-300">Belum ada rincian log harian untuk sumur ini.</p>
                                <p class="text-xs text-slate-500">Gunakan tombol "Input Log Harian" di atas untuk menambahkan catatan per hari.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $i => $lg): 
                            $dtTotal = (float)$lg['total_dt'];
                            $jamTotal = (float)$lg['total_hrs'];
                        ?>
                        <tr class="hover:bg-slate-800/60 transition">
                            <td class="py-3.5 px-3 text-center font-bold text-slate-400 border border-slate-800 bg-slate-900/50">
                                <?= $i + 1 ?>
                            </td>
                            <td class="py-3.5 px-4 font-sans font-bold text-sky-300 border border-slate-800 whitespace-nowrap">
                                <?= date('d F Y', strtotime($lg['tanggal'])) ?>
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold text-lime-400 border border-slate-800">
                                <?= (float)$lg['miru_jam'] > 0 ? number_format((float)$lg['miru_jam'], 2) : '-' ?>
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold text-lime-300 border border-slate-800">
                                <?= (float)$lg['ops_jam'] > 0 ? number_format((float)$lg['ops_jam'], 2) : '-' ?>
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold <?= $dtTotal > 0 ? 'text-amber-400' : 'text-slate-500' ?> border border-slate-800">
                                <?= $dtTotal > 0 ? number_format($dtTotal, 2) : '-' ?>
                            </td>
                            <td class="py-3.5 px-3 text-right font-black text-emerald-400 border border-slate-800">
                                <?= $jamTotal > 0 ? number_format($jamTotal, 2) : '-' ?>
                            </td>
                            <td class="py-3.5 px-5 font-sans text-slate-300 border border-slate-800 text-xs">
                                <?= !empty($lg['remark_npt']) ? esc($lg['remark_npt']) : '<span class="text-slate-600 italic">Operasi normal lancar</span>' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <!-- Footer Total -->
                <tfoot class="bg-[#0B1E4A] text-white text-xs font-black border-t-2 border-slate-700">
                    <tr>
                        <td colspan="2" class="py-3.5 px-4 text-center font-sans text-amber-300 border border-slate-700 uppercase">
                            TOTAL KESELURUHAN
                        </td>
                        <td class="py-3.5 px-3 text-right text-lime-400 border border-slate-700"><?= number_format($miruJam, 2) ?></td>
                        <td class="py-3.5 px-3 text-right text-lime-300 border border-slate-700"><?= number_format($opsJam, 2) ?></td>
                        <td class="py-3.5 px-3 text-right text-amber-400 border border-slate-700"><?= number_format($totalDt, 2) ?></td>
                        <td class="py-3.5 px-3 text-right text-emerald-300 text-sm bg-[#0E2A66] border border-slate-700"><?= number_format($totalJam, 2) ?></td>
                        <td class="border border-slate-700"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ═══ 5. RINCIAN POS KENDALA & DOWNTIME ═══════════════════════════════════ -->
    <div class="p-6 rounded-3xl bg-slate-900 border-2 border-slate-700 shadow-xl space-y-4">
        <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
            <span>Rincian Pos Downtime Terinci</span>
        </h3>
        
        <?php if (empty($dtRows)): ?>
            <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3 text-emerald-300 text-sm font-bold">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>Tidak ada catatan downtime pada sumur ini. Seluruh operasi berjalan lancar tanpa kendala.</span>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <?php foreach ($dtRows as $d): ?>
                    <div class="p-4 rounded-2xl bg-slate-850 border border-slate-800 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-black uppercase text-slate-300 block">
                                <?= esc($d['nama_kategori'] ?? 'Kategori #' . $d['kategori_id']) ?>
                            </span>
                            <span class="text-[11px] font-bold <?= ($d['tipe'] ?? '') === 'UNPAID' ? 'text-rose-400' : 'text-amber-400' ?>">
                                <?= esc($d['tipe'] ?? 'SBWC') ?>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black font-num text-white"><?= number_format((float)$d['jam'], 2) ?></span>
                            <span class="text-xs text-slate-400 block">Jam</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ═══ 6. TOMBOL AKSI BAWAH BESAR ══════════════════════════════════════════ -->
    <div class="flex flex-wrap items-center justify-between gap-4 p-5 rounded-3xl bg-slate-850 border-2 border-slate-700">
        <a href="<?= esc($backUrl) ?>" 
           class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-sm border border-slate-600 transition active:scale-95">
            <i class="fa-solid fa-arrow-left text-base"></i>
            <span>KEMBALI KE RINGKASAN DAILY REPORT</span>
        </a>

        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= base_url('daily-report/edit/' . $report['id']) ?>" 
               class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-sm shadow transition active:scale-95">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Sumur Ini</span>
            </a>
            <a href="<?= base_url("daily-report/log-harian/{$rig['id']}/{$report['bulan']}/{$report['tahun']}") ?>" 
               class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/20 transition active:scale-95">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Input Log Harian</span>
            </a>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
