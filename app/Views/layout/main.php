<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d1524">
    <title><?= $title ?? 'SIMOR BMS' ?> - PT. Besmindo Materi Sewatama</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">
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
<body class="shadcn-ui bg-slate-900 text-slate-100 flex h-[100dvh] overflow-hidden antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-yellow-400 focus:px-4 focus:py-2 focus:text-slate-950 focus:shadow-lg">Langsung ke konten</a>
    <!-- Sidebar Collapsible -->
    <aside id="mainSidebar" class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col flex-shrink-0 select-none z-30 shadow-2xl transition-all duration-300 relative group/sidebar">
        <!-- Brand Header dengan Logo Resmi Besmindo -->
        <div class="p-3.5 border-b border-slate-800 bg-slate-900/60 flex flex-col items-center justify-center text-center relative overflow-hidden">
            <a href="<?= base_url('dashboard') ?>" class="block w-full">
                <div class="bg-slate-950 p-2 rounded-xl border border-slate-800 flex items-center justify-center mb-1.5 shadow-inner">
                    <img src="<?= base_url('assets/img/logo_besmindo.png') ?>" alt="PT. Besmindo Materi Sewatama" class="max-h-9 w-auto object-contain">
                </div>
            </a>
            <div class="sidebar-text transition-opacity duration-200">
                <h1 class="font-bold text-sm tracking-wide text-white">SIMOR <span class="text-blue-400 font-extrabold">BMS</span></h1>
                <p class="text-[10px] text-slate-400 font-medium tracking-wide">Rig Operations Management</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-4">
            
            <!-- 1. UTAMA -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Utama</p>
                <nav aria-label="Navigasi utama" class="space-y-1">
                    <a href="<?= base_url('dashboard') ?>" title="Dashboard Utama" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-gauge-high text-sm w-5 text-center text-blue-400 flex-shrink-0 <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text truncate">Dashboard Utama</span>
                    </a>
                </nav>
            </div>

            <!-- 2. PENCATATAN OPERASI -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pencatatan Operasi</p>
                <nav aria-label="Pencatatan operasi" class="space-y-1">
                    <!-- STEP 1 -->
                    <a href="<?= base_url('daily-report') ?>" title="Step 1 — Rekap Sumur (Well)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= (str_starts_with(uri_string(), 'daily-report') && !str_starts_with(uri_string(), 'daily-report/log-harian')) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-file-waveform text-sm w-5 text-center text-emerald-400 flex-shrink-0 <?= (str_starts_with(uri_string(), 'daily-report') && !str_starts_with(uri_string(), 'daily-report/log-harian')) ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text flex-1 truncate">Rekap Sumur (Well)</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-md <?= (str_starts_with(uri_string(), 'daily-report') && !str_starts_with(uri_string(), 'daily-report/log-harian')) ? 'bg-white/20 text-white' : 'bg-emerald-500/15 text-emerald-400' ?>">STEP 1</span>
                    </a>
                    <!-- STEP 2 -->
                    <a href="<?= base_url('daily-report/log-harian') ?>" title="Step 2 — Input Daily Report" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'daily-report/log-harian') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-pen-nib text-sm w-5 text-center text-cyan-400 flex-shrink-0 <?= str_starts_with(uri_string(), 'daily-report/log-harian') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text flex-1 truncate">Input Daily Report</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-md <?= str_starts_with(uri_string(), 'daily-report/log-harian') ? 'bg-white/20 text-white' : 'bg-cyan-500/15 text-cyan-400' ?>">STEP 2</span>
                    </a>
                </nav>
            </div>

            <!-- 3. PENCATATAN DOWNTIME -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pencatatan Downtime</p>
                <nav aria-label="Pencatatan downtime" class="space-y-1">
                    <!-- STEP 3 -->
                    <a href="<?= base_url('npt') ?>" title="NPT (Downtime Hub) — Harian, Bulanan, & Tahunan" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'npt') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                        <i class="fa-solid fa-clock-rotate-left text-sm w-5 text-center text-amber-400 flex-shrink-0 <?= str_starts_with(uri_string(), 'npt') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text flex-1 truncate">NPT (Downtime Hub)</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-md <?= str_starts_with(uri_string(), 'npt') ? 'bg-white/20 text-white' : 'bg-amber-500/15 text-amber-400' ?>">STEP 3</span>
                    </a>
                </nav>
            </div>

            <!-- 3. LAPORAN BULANAN -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Laporan Bulanan</p>
                <nav aria-label="Laporan bulanan" class="space-y-1">
                    <a href="<?= base_url('monthly-report') ?>" title="Monthly Report (Tabel RAU)" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= (str_starts_with(uri_string(), 'monthly-report')) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-calendar-days text-sm w-5 text-center text-purple-400 flex-shrink-0 <?= str_starts_with(uri_string(), 'monthly-report') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text truncate">Monthly Report (Tabel RAU)</span>
                    </a>
                </nav>
            </div>

            <!-- 4. REKAPITULASI TAHUNAN -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Rekapitulasi Tahunan</p>
                <nav aria-label="Rekapitulasi tahunan" class="space-y-1">
                    <a href="<?= base_url('rekap-tahunan') ?>" title="Pusat Rekap Tahunan" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'rekap-tahunan') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-chart-line text-sm w-5 text-center text-cyan-400 flex-shrink-0 <?= str_starts_with(uri_string(), 'rekap-tahunan') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text truncate">Pusat Rekap Tahunan</span>
                    </a>
                </nav>
            </div>

            <!-- 5. MASTER DATA -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Master Data</p>
                <nav aria-label="Master data" class="space-y-1">
                    <a href="<?= base_url('master') ?>" title="Master Data Hub" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'master') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-database text-sm w-5 text-center flex-shrink-0 <?= str_starts_with(uri_string(), 'master') ? 'text-white' : 'text-blue-400' ?>"></i>
                        <span class="sidebar-text flex-1 truncate">Master Data Hub</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-md <?= str_starts_with(uri_string(), 'master') ? 'bg-white/20 text-white' : 'bg-blue-500/15 text-blue-400' ?>">4 Modul</span>
                    </a>
                </nav>
            </div>

            <!-- 6. AUDIT & LOG SISTEM -->
            <div>
                <p class="sidebar-text px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Audit &amp; Log Sistem</p>
                <nav aria-label="Audit dan log sistem" class="space-y-1">
                    <a href="<?= base_url('audit-log') ?>" title="Riwayat Aktivitas & Jejak Audit" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all <?= str_starts_with(uri_string(), 'audit-log') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-850 hover:text-white' ?>">
                        <i class="fa-solid fa-clock-rotate-left text-sm w-5 text-center text-indigo-400 flex-shrink-0 <?= str_starts_with(uri_string(), 'audit-log') ? 'text-white' : '' ?>"></i>
                        <span class="sidebar-text truncate">Riwayat Aktivitas (Log)</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Profil Akun & Tombol Keluar -->
        <div class="p-3 border-t border-slate-800 bg-slate-950/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow">
                    <?= strtoupper(substr(session()->get('nama') ?? 'A', 0, 1)) ?>
                </div>
                <div class="leading-tight truncate sidebar-text">
                    <p class="text-xs font-bold text-white truncate"><?= session()->get('nama') ?? 'Admin' ?></p>
                    <span class="text-[10px] text-emerald-400 font-medium">Online</span>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" title="Keluar dari Sistem" onclick="return confirm('Yakin ingin keluar?');"
                class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white font-semibold text-xs transition flex-shrink-0">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- Halaman Konten Utama -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-900">
        <header class="h-16 bg-slate-950/70 border-b border-slate-800 flex items-center justify-between px-4 sm:px-6 z-20 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Tombol Minimize / Toggle Sidebar -->
                <button type="button" id="btnToggleSidebar" onclick="toggleSidebar()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-blue-600 border border-slate-700 hover:border-blue-500 text-slate-300 hover:text-white flex items-center justify-center transition shadow active:scale-95 flex-shrink-0" title="Kecilkan / Besarkan Sidebar">
                    <i id="toggleIcon" class="fa-solid fa-bars-staggered text-sm"></i>
                </button>
                <div class="truncate">
                    <h2 class="text-base font-bold text-white tracking-wide truncate"><?= $page_title ?? 'Dashboard' ?></h2>
                    <p class="text-[11px] text-slate-400 truncate"><?= $page_subtitle ?? 'Manajemen Operasi Rig & Pengawasan Non-Productive Time' ?></p>
                </div>
            </div>
            <div class="text-right hidden sm:block flex-shrink-0">
                <span class="text-xs font-semibold text-white block font-mono"><?= date('l, d F Y') ?></span>
                <span class="text-[10px] text-blue-400 font-medium tracking-wide">PT. Besmindo Materi Sewatama</span>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6 transition-all duration-300">
            <?php if (session()->getFlashdata('success')): ?>
                <div role="status" class="ui-alert ui-alert--success mb-4 text-xs font-semibold">
                    <i class="fa-solid fa-circle-check text-base text-emerald-400"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div role="alert" class="ui-alert ui-alert--danger mb-4 text-xs font-semibold">
                    <i class="fa-solid fa-triangle-exclamation text-base text-rose-400"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <div class="ui-page">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Script Global Sidebar Toggle -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const icon = document.getElementById('toggleIcon');
            const isMinimized = sidebar.classList.contains('w-16');

            if (isMinimized) {
                // Expand
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-bars-staggered');
                localStorage.setItem('sidebar_minimized', 'false');
            } else {
                // Minimize
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                icon.classList.remove('fa-bars-staggered');
                icon.classList.add('fa-bars');
                localStorage.setItem('sidebar_minimized', 'true');
            }
        }

        // Restore status minimize dari localStorage
        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('sidebar_minimized') === 'true') {
                const sidebar = document.getElementById('mainSidebar');
                const icon = document.getElementById('toggleIcon');
                if (sidebar) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-16');
                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                    if (icon) {
                        icon.classList.remove('fa-bars-staggered');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
