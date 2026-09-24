<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- ═══ Callout Header ═══════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl flex-shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white tracking-tight">Riwayat Aktivitas &amp; Audit Trail</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan transparansi perubahan data: siapa, kapan, pada rig apa, dan rincian perubahan.</p>
            </div>
        </div>

        <!-- Filter Form Toolbar -->
        <form method="GET" action="<?= base_url('audit-log') ?>" class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Modul -->
            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-indigo-400 text-xs"></i>
                    <span>Modul:</span>
                </span>
                <select name="modul" onchange="this.form.submit()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <option value="ALL" class="bg-slate-900 text-white" <?= $selectedModul === 'ALL' ? 'selected' : '' ?>>Semua Modul</option>
                    <option value="NPT" class="bg-slate-900 text-white" <?= $selectedModul === 'NPT' ? 'selected' : '' ?>>NPT (Downtime)</option>
                    <option value="DAILY_REPORT" class="bg-slate-900 text-white" <?= $selectedModul === 'DAILY_REPORT' ? 'selected' : '' ?>>Daily Report (Operasi)</option>
                    <option value="MASTER" class="bg-slate-900 text-white" <?= $selectedModul === 'MASTER' ? 'selected' : '' ?>>Master Data</option>
                </select>
            </div>

            <!-- Filter Rig -->
            <div class="flex items-center bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-oil-well text-blue-400 text-xs"></i>
                    <span>Rig:</span>
                </span>
                <select name="rig_id" onchange="this.form.submit()" class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <option value="" class="bg-slate-900 text-white">Semua Rig</option>
                    <?php foreach ($allRigs as $r): ?>
                        <option value="<?= $r['id'] ?>" class="bg-slate-900 text-white" <?= $selectedRigId == $r['id'] ? 'selected' : '' ?>><?= esc($r['kode']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($selectedModul !== 'ALL' || !empty($selectedRigId)): ?>
                <a href="<?= base_url('audit-log') ?>" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset</span>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ═══ Ringkasan Statistik Log ═══════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Riwayat Ditampilkan</span>
                <h4 class="text-xl font-black text-white mt-1"><?= number_format($totalLogs) ?> <span class="text-xs font-normal text-slate-400">Aktivitas</span></h4>
            </div>
            <i class="fa-solid fa-list-check text-2xl text-slate-600"></i>
        </div>

        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-400">Pencatatan NPT</span>
                <h4 class="text-xs font-bold text-slate-300 mt-1">Audit aktif untuk setiap jam downtime</h4>
            </div>
            <i class="fa-solid fa-clock-rotate-left text-2xl text-amber-400/40"></i>
        </div>

        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-400">Daily Report Operasi</span>
                <h4 class="text-xs font-bold text-slate-300 mt-1">Terekam per sumur dan per tanggal log</h4>
            </div>
            <i class="fa-solid fa-file-waveform text-2xl text-emerald-400/40"></i>
        </div>
    </div>

    <!-- ═══ Timeline Daftar Riwayat Aktivitas ═════════════════════ -->
    <div class="rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-700/80 flex items-center justify-between bg-slate-850">
            <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                <i class="fa-solid fa-timeline text-indigo-400"></i>
                Linimasa Perubahan Data Terkini
            </h3>
            <span class="text-[11px] text-slate-400 font-medium">Diperbarui secara otomatis</span>
        </div>

        <?php if (empty($logs)): ?>
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 text-2xl mb-3">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-300">Belum Ada Riwayat Aktivitas</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Riwayat akan otomatis tercatat saat operator melakukan perubahan data pada form NPT atau Daily Report.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-700/40">
                <?php foreach ($logs as $log): 
                    $modulClass = match($log['modul']) {
                        'NPT'          => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                        'DAILY_REPORT' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                        'MASTER'       => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                        default        => 'bg-slate-700/40 text-slate-300 border-slate-600',
                    };

                    $actionIcon = match($log['action']) {
                        'SIMPAN_NPT'        => 'fa-floppy-disk text-amber-400',
                        'TAMBAH_SUMUR'      => 'fa-plus-circle text-emerald-400',
                        'UPDATE_SUMUR'      => 'fa-pen-to-square text-cyan-400',
                        'HAPUS_SUMUR'       => 'fa-trash text-rose-400',
                        'SIMPAN_LOG_HARIAN' => 'fa-calendar-check text-indigo-400',
                        default             => 'fa-circle-dot text-slate-400',
                    };

                    $timeStr = !empty($log['created_at']) ? date('d M Y · H:i:s', strtotime($log['created_at'])) : '-';
                ?>
                <div class="p-4 sm:p-5 hover:bg-slate-750/50 transition flex items-start gap-4">
                    <!-- Icon Bulat -->
                    <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center flex-shrink-0 text-sm mt-0.5 shadow-sm">
                        <i class="fa-solid <?= $actionIcon ?>"></i>
                    </div>

                    <!-- Detail Isi Log -->
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Badge Modul -->
                            <span class="px-2 py-0.5 rounded-md border text-[10px] font-extrabold uppercase tracking-wider <?= $modulClass ?>">
                                <?= esc($log['modul']) ?>
                            </span>

                            <!-- Badge Rig (jika ada) -->
                            <?php if (!empty($log['rig_kode'])): ?>
                                <span class="px-2 py-0.5 rounded-md bg-blue-500/15 text-blue-300 border border-blue-500/30 text-[10px] font-bold">
                                    <i class="fa-solid fa-oil-well mr-1"></i><?= esc($log['rig_kode']) ?>
                                </span>
                            <?php endif; ?>

                            <!-- Waktu Kejadian -->
                            <span class="text-xs text-slate-400 font-mono flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[11px]"></i>
                                <?= $timeStr ?>
                            </span>
                        </div>

                        <!-- Deskripsi Lengkap -->
                        <p class="text-sm font-semibold text-white leading-relaxed">
                            <?= esc($log['deskripsi']) ?>
                        </p>

                        <!-- Info User Pelaksana -->
                        <div class="flex items-center gap-3 text-xs text-slate-400 pt-0.5">
                            <span class="flex items-center gap-1 text-slate-300 font-medium">
                                <i class="fa-solid fa-user-circle text-indigo-400 text-xs"></i>
                                Oleh: <strong class="text-white"><?= esc($log['username']) ?></strong>
                            </span>
                            <span class="text-slate-600">·</span>
                            <span class="font-mono text-[11px] text-slate-500">
                                IP: <?= esc($log['ip_address'] ?? '127.0.0.1') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
