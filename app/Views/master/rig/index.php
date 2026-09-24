<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen space-y-4">

    <!-- Section Header -->
    <div class="ui-section-header">
        <div>
            <h3 class="ui-section-title">Data Armada Rig Besmindo</h3>
            <p class="ui-section-sub">Total terdaftar 18 unit armada rig beserta nilai Operator Daily Rate (ODR)</p>
        </div>
        <a href="<?= base_url('master/rig/tambah') ?>"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Rig Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="ui-card">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="text-center w-12">No</th>
                        <th>Kode Rig</th>
                        <th>Nama Rig</th>
                        <th class="text-right">Tarif Harian (ODR)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($rigs as $r): ?>
                    <tr>
                        <td class="text-center font-num" style="color:var(--muted-foreground)"><?= $no++ ?></td>
                        <td class="font-bold" style="color:var(--foreground)"><?= esc($r['kode']) ?></td>
                        <td style="color:var(--card-foreground)"><?= esc($r['nama_rig']) ?></td>
                        <td class="text-right font-num font-semibold" style="color:var(--success-fg)">
                            Rp <?= number_format($r['odr'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <?php if ($r['aktif']): ?>
                                <span class="ui-badge ui-badge--success">Aktif</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge--muted">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="<?= base_url('master/rig/edit/' . $r['id']) ?>" title="Edit"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-md transition"
                                    style="background:rgba(212,168,32,.1);color:var(--primary)"
                                    onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'"
                                    onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="<?= base_url('master/rig/hapus/' . $r['id']) ?>" method="POST"
                                    onsubmit="return confirm('Hapus rig ini?');" class="inline">
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
