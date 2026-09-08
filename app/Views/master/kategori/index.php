<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-800/80 border border-slate-700/70 shadow-lg">
        <div>
            <h3 class="text-base font-semibold text-white">Klasifikasi Downtime (NPT)</h3>
            <p class="text-xs text-slate-400">Pengaturan pos downtime untuk UNPAID (beban kontraktor) dan SBWC (Standby With Crew)</p>
        </div>
        <a href="<?= base_url('master/kategori/tambah') ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Kategori Baru</span>
        </a>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl bg-slate-800/80 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/90 text-slate-300 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-700">
                    <tr>
                        <th class="py-3 px-4 w-16 text-center">Urutan</th>
                        <th class="py-3 px-4">Nama Pos Downtime</th>
                        <th class="py-3 px-4 text-center">Tipe Klasifikasi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-slate-300">
                    <?php foreach ($kategori as $k): ?>
                    <tr class="hover:bg-slate-750/50 transition">
                        <td class="py-3 px-4 text-center font-mono text-slate-400"><?= $k['urutan'] ?></td>
                        <td class="py-3 px-4 font-semibold text-white"><?= esc($k['nama']) ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($k['tipe'] == 'UNPAID'): ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    UNPAID
                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    SBWC
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($k['aktif']): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-500/10 text-emerald-400">Aktif</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-700/50 text-slate-400">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="<?= base_url('master/kategori/edit/' . $k['id']) ?>" title="Edit" class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-600 hover:text-white transition">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="<?= base_url('master/kategori/hapus/' . $k['id']) ?>" method="POST" onsubmit="return confirm('Hapus kategori ini?');" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="Hapus" class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
