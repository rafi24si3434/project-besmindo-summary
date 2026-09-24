<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen space-y-4">

    <div class="ui-section-header">
        <div>
            <h3 class="ui-section-title">Daftar Titik Lokasi Sumur Minyak</h3>
            <p class="ui-section-sub">Master penamaan sumur untuk pencatatan operasi harian rig (5Q-69A, 6N-29A, dll)</p>
        </div>
        <a href="<?= base_url('master/lokasi/tambah') ?>"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Lokasi
        </a>
    </div>

    <div class="ui-card">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="text-center w-14">No</th>
                        <th>Nama / Tag Lokasi Sumur</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lokasi)): ?>
                    <tr>
                        <td colspan="4" class="py-10 text-center" style="color:var(--muted-foreground)">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-location-dot text-2xl opacity-40"></i>
                                <span>Belum ada lokasi yang ditambahkan</span>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($lokasi as $l): ?>
                        <tr>
                            <td class="text-center font-num" style="color:var(--muted-foreground)"><?= $no++ ?></td>
                            <td class="font-semibold" style="color:var(--foreground)"><?= esc($l['nama_lokasi']) ?></td>
                            <td class="text-center">
                                <?php if ($l['aktif']): ?>
                                    <span class="ui-badge ui-badge--success">Aktif</span>
                                <?php else: ?>
                                    <span class="ui-badge ui-badge--muted">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('master/lokasi/edit/' . $l['id']) ?>" title="Edit"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-md transition"
                                        style="background:rgba(212,168,32,.1);color:var(--primary)"
                                        onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'"
                                        onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="<?= base_url('master/lokasi/hapus/' . $l['id']) ?>" method="POST"
                                        onsubmit="return confirm('Hapus lokasi ini?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" title="Hapus"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md transition"
                                            style="background:var(--danger-bg);color:var(--danger-fg)"
                                            onmouseover="this.style.background='var(--destructive)';this.style.color='#fff'"
                                            onmouseout="this.style.background='var(--danger-bg)';this.style.color='var(--danger-fg)'">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
