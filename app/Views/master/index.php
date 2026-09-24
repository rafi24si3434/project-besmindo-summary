<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen space-y-5">
    
    <!-- Header -->
    <div class="ui-section-header">
        <div>
            <h3 class="ui-section-title"><?= esc($title) ?></h3>
            <p class="ui-section-sub"><?= esc($page_subtitle) ?></p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-700/80 mb-5 overflow-x-auto custom-scrollbar pb-2">
        <a href="?tab=rig" class="px-4 py-2 text-xs font-bold whitespace-nowrap border-b-2 transition-all <?= $activeTab === 'rig' ? 'border-blue-500 text-blue-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-500' ?>">
            <i class="fa-solid fa-tower-broadcast mr-1.5"></i> Armada Rig
        </a>
        <a href="?tab=kategori" class="px-4 py-2 text-xs font-bold whitespace-nowrap border-b-2 transition-all <?= $activeTab === 'kategori' ? 'border-amber-500 text-amber-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-500' ?>">
            <i class="fa-solid fa-tags mr-1.5"></i> Kategori Downtime
        </a>
        <a href="?tab=third_party" class="px-4 py-2 text-xs font-bold whitespace-nowrap border-b-2 transition-all <?= $activeTab === 'third_party' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-500' ?>">
            <i class="fa-solid fa-handshake mr-1.5"></i> Vendor 3rd Party
        </a>
        <a href="?tab=lokasi" class="px-4 py-2 text-xs font-bold whitespace-nowrap border-b-2 transition-all <?= $activeTab === 'lokasi' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-500' ?>">
            <i class="fa-solid fa-location-dot mr-1.5"></i> Lokasi / Sumur
        </a>
    </div>

    <!-- TAB 1: ARMADA RIG -->
    <div class="<?= $activeTab === 'rig' ? 'block' : 'hidden' ?> space-y-4">
        <div class="flex justify-end">
            <a href="<?= base_url('master/rig/tambah') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Rig Baru
            </a>
        </div>
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
                            <td class="text-right font-num font-semibold" style="color:var(--success-fg)">Rp <?= number_format($r['odr'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $r['aktif'] ? '<span class="ui-badge ui-badge--success">Aktif</span>' : '<span class="ui-badge ui-badge--muted">Nonaktif</span>' ?></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('master/rig/edit/' . $r['id']) ?>" title="Edit" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:rgba(212,168,32,.1);color:var(--primary)" onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'" onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form action="<?= base_url('master/rig/hapus/' . $r['id']) ?>" method="POST" onsubmit="return confirm('Hapus rig ini?');" class="inline">
                                        <?= csrf_field() ?><button type="submit" title="Hapus" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:var(--danger-bg);color:var(--danger-fg)" onmouseover="this.style.background='var(--destructive)';this.style.color='#fff'" onmouseout="this.style.background='var(--danger-bg)';this.style.color='var(--danger-fg)'"><i class="fa-solid fa-trash text-xs"></i></button>
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

    <!-- TAB 2: KATEGORI DOWNTIME -->
    <div class="<?= $activeTab === 'kategori' ? 'block' : 'hidden' ?> space-y-4">
        <div class="flex justify-end">
            <a href="<?= base_url('master/kategori/tambah') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Kategori
            </a>
        </div>
        <div class="ui-card">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="text-center w-16">Urutan</th>
                            <th>Nama Pos Downtime</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kategori as $k): ?>
                        <tr>
                            <td class="text-center font-num" style="color:var(--muted-foreground)"><?= $k['urutan'] ?></td>
                            <td class="font-semibold" style="color:var(--foreground)"><?= esc($k['nama']) ?></td>
                            <td class="text-center"><?= $k['tipe'] == 'UNPAID' ? '<span class="ui-badge ui-badge--danger">UNPAID</span>' : '<span class="ui-badge ui-badge--warning">SBWC</span>' ?></td>
                            <td class="text-center"><?= $k['aktif'] ? '<span class="ui-badge ui-badge--success">Aktif</span>' : '<span class="ui-badge ui-badge--muted">Nonaktif</span>' ?></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('master/kategori/edit/' . $k['id']) ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:rgba(212,168,32,.1);color:var(--primary)" onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'" onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form action="<?= base_url('master/kategori/hapus/' . $k['id']) ?>" method="POST" onsubmit="return confirm('Hapus kategori ini?');" class="inline">
                                        <?= csrf_field() ?><button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:var(--danger-bg);color:var(--danger-fg)" onmouseover="this.style.background='var(--destructive)';this.style.color='#fff'" onmouseout="this.style.background='var(--danger-bg)';this.style.color='var(--danger-fg)'"><i class="fa-solid fa-trash text-xs"></i></button>
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

    <!-- TAB 3: 3RD PARTY -->
    <div class="<?= $activeTab === 'third_party' ? 'block' : 'hidden' ?> space-y-4">
        <div class="flex justify-end">
            <a href="<?= base_url('master/third-party/tambah') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Vendor 3rd Party
            </a>
        </div>
        <div class="ui-card">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="text-center w-12">No</th>
                            <th>Nama Vendor 3rd Party</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($thirdParties as $tp): ?>
                        <tr>
                            <td class="text-center font-num" style="color:var(--muted-foreground)"><?= $no++ ?></td>
                            <td class="font-semibold" style="color:var(--foreground)"><?= esc($tp['nama']) ?></td>
                            <td class="text-center"><?= $tp['aktif'] ? '<span class="ui-badge ui-badge--success">Aktif</span>' : '<span class="ui-badge ui-badge--muted">Nonaktif</span>' ?></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('master/third-party/edit/' . $tp['id']) ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:rgba(212,168,32,.1);color:var(--primary)" onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'" onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form action="<?= base_url('master/third-party/hapus/' . $tp['id']) ?>" method="POST" onsubmit="return confirm('Hapus vendor ini?');" class="inline">
                                        <?= csrf_field() ?><button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:var(--danger-bg);color:var(--danger-fg)" onmouseover="this.style.background='var(--destructive)';this.style.color='#fff'" onmouseout="this.style.background='var(--danger-bg)';this.style.color='var(--danger-fg)'"><i class="fa-solid fa-trash text-xs"></i></button>
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

    <!-- TAB 4: LOKASI SUMUR -->
    <div class="<?= $activeTab === 'lokasi' ? 'block' : 'hidden' ?> space-y-4">
        <div class="flex justify-end">
            <a href="<?= base_url('master/lokasi/tambah') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-md shadow transition">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Lokasi Baru
            </a>
        </div>
        <div class="ui-card">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="text-center w-12">No</th>
                            <th>Nama / Tag Lokasi Sumur</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($lokasi as $l): ?>
                        <tr>
                            <td class="text-center font-num" style="color:var(--muted-foreground)"><?= $no++ ?></td>
                            <td class="font-semibold" style="color:var(--foreground)"><?= esc($l['nama_lokasi']) ?></td>
                            <td class="text-center"><?= $l['aktif'] ? '<span class="ui-badge ui-badge--success">Aktif</span>' : '<span class="ui-badge ui-badge--muted">Nonaktif</span>' ?></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('master/lokasi/edit/' . $l['id']) ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:rgba(212,168,32,.1);color:var(--primary)" onmouseover="this.style.background='var(--primary)';this.style.color='var(--primary-foreground)'" onmouseout="this.style.background='rgba(212,168,32,.1)';this.style.color='var(--primary)'"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form action="<?= base_url('master/lokasi/hapus/' . $l['id']) ?>" method="POST" onsubmit="return confirm('Hapus lokasi ini?');" class="inline">
                                        <?= csrf_field() ?><button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-md transition" style="background:var(--danger-bg);color:var(--danger-fg)" onmouseover="this.style.background='var(--destructive)';this.style.color='#fff'" onmouseout="this.style.background='var(--danger-bg)';this.style.color='var(--danger-fg)'"><i class="fa-solid fa-trash text-xs"></i></button>
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

</div>
<?= $this->endSection() ?>

