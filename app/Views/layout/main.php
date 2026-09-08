<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIMOR BMS' ?> - PT. Besmindo Materi Sewatama</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 0.875rem; /* 14px standar seimbang */
            line-height: 1.4;
        }
        .font-num {
            font-family: 'JetBrains Mono', monospace;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 flex h-screen overflow-hidden antialiased">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col flex-shrink-0 select-none z-30 shadow-xl">
        <!-- Brand Header dengan Logo Resmi Besmindo -->
        <div class="p-3.5 border-b border-slate-800 bg-slate-900/50 flex flex-col items-center justify-center text-center">
            <a href="<?= base_url('dashboard') ?>" class="block w-full">
                <div class="bg-slate-950 p-2 rounded-xl border border-slate-800 flex items-center justify-center mb-1.5">
                    <img src="<?= base_url('assets/img/logo_besmindo.png') ?>" alt="PT. Besmindo Materi Sewatama" class="max-h-10 w-auto object-contain">
                </div>
            </a>
            <h1 class="font-bold text-sm tracking-wide text-white">SIMOR <span class="text-blue-400 font-extrabold">BMS</span></h1>
            <p class="text-[10px] text-slate-400 font-medium tracking-wide">Rig Operations Management</p>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-5">
            <div>
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Utama</p>
                <nav class="space-y-1">
                    <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-gauge-high text-sm w-5 text-center text-blue-400 <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'text-white' : '' ?>"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pencatatan Harian</p>
                <nav class="space-y-1">
                    <a href="<?= base_url('npt') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'npt') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-clock-rotate-left text-sm w-5 text-center text-amber-400 <?= str_starts_with(uri_string(), 'npt') ? 'text-white' : '' ?>"></i>
                        <span>Input NPT Harian</span>
                    </a>
                    <a href="<?= base_url('daily-report') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'daily-report') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-file-waveform text-sm w-5 text-center text-emerald-400 <?= str_starts_with(uri_string(), 'daily-report') ? 'text-white' : '' ?>"></i>
                        <span>Daily Report (Sumur)</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Laporan & Rekap</p>
                <nav class="space-y-1">
                    <a href="<?= base_url('monthly-report') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'monthly-report') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-calendar-days text-sm w-5 text-center text-purple-400 <?= str_starts_with(uri_string(), 'monthly-report') ? 'text-white' : '' ?>"></i>
                        <span>Monthly Report (RAU)</span>
                    </a>
                    <a href="<?= base_url('monthly-report#tabContent_charts') ?>" onclick="if(window.location.pathname.includes('monthly-report')){ switchTab('charts'); return false; }" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all text-slate-300 hover:bg-slate-850 hover:text-white">
                        <i class="fa-solid fa-chart-column text-sm w-5 text-center text-sky-400"></i>
                        <span>Grafik Analisis (5 KPI)</span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= (str_starts_with(uri_string(), 'rekap-tahunan') && !str_starts_with(uri_string(), 'rekap-tahunan/npt')) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-chart-line text-sm w-5 text-center text-cyan-400 <?= (str_starts_with(uri_string(), 'rekap-tahunan') && !str_starts_with(uri_string(), 'rekap-tahunan/npt')) ? 'text-white' : '' ?>"></i>
                        <span>Rekap Tahunan Operasi</span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan/npt/2026') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'rekap-tahunan/npt') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-clock-rotate-left text-sm w-5 text-center text-rose-400 <?= str_starts_with(uri_string(), 'rekap-tahunan/npt') ? 'text-white' : '' ?>"></i>
                        <span>Rekap NPT Tahunan</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Master Data</p>
                <nav class="space-y-1">
                    <a href="<?= base_url('master/rig') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'master/rig') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-tower-broadcast text-sm w-5 text-center"></i>
                        <span>Armada Rig & ODR</span>
                    </a>
                    <a href="<?= base_url('master/kategori') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'master/kategori') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-tags text-sm w-5 text-center"></i>
                        <span>Kategori Downtime</span>
                    </a>
                    <a href="<?= base_url('master/third-party') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'master/third-party') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-handshake text-sm w-5 text-center"></i>
                        <span>Vendor 3rd Party</span>
                    </a>
                    <a href="<?= base_url('master/lokasi') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'master/lokasi') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-location-dot text-sm w-5 text-center"></i>
                        <span>Lokasi / Sumur</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Profil Akun & Tombol Keluar -->
        <div class="p-3 border-t border-slate-800 bg-slate-950/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                    <?= strtoupper(substr(session()->get('nama') ?? 'A', 0, 1)) ?>
                </div>
                <div class="leading-tight truncate">
                    <p class="text-xs font-bold text-white truncate"><?= session()->get('nama') ?? 'Admin' ?></p>
                    <span class="text-[10px] text-emerald-400 font-medium">Online</span>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" title="Keluar" onclick="return confirm('Yakin ingin keluar?');"
                class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white font-semibold text-xs transition">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- Halaman Konten -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-900">
        <header class="h-16 bg-slate-950/70 border-b border-slate-800 flex items-center justify-between px-6 z-20">
            <div>
                <h2 class="text-base font-bold text-white tracking-wide"><?= $page_title ?? 'Dashboard' ?></h2>
                <p class="text-[11px] text-slate-400"><?= $page_subtitle ?? 'Manajemen Operasi Rig & Pengawasan Non-Productive Time' ?></p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-xs font-semibold text-white block font-mono"><?= date('l, d F Y') ?></span>
                <span class="text-[10px] text-blue-400 font-medium">PT. Besmindo Materi Sewatama</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-base text-emerald-400"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-base text-rose-400"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
