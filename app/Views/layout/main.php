<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#090b10">
    <title><?= $title ?? 'SIMOR BMS' ?> — PT. Besmindo Materi Sewatama</title>
    <script>
        window.tailwind = { config: { darkMode: 'class' } };
    </script>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <script>
        if (typeof tailwind !== 'undefined') {
            tailwind.config = { darkMode: 'class' };
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/simor-notifications.css') ?>">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('simor_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.classList.toggle('dark', savedTheme === 'dark');
        })();
    </script>
    <style>
        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.875rem;
            line-height: 1.45;
        }
        .font-num {
            font-family: 'JetBrains Mono', monospace;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums slashed-zero;
        }
        /* Hilangkan panah atas/bawah pada semua input angka agar tidak menabrak label & tidak terklik tidak sengaja */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        /* SweetAlert2 Executive Styling */
        .swal2-popup {
            border-radius: 18px !important;
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif !important;
            padding: 1.5rem !important;
        }
        html.dark .swal2-popup, html[data-theme="dark"] .swal2-popup {
            background: #0d121d !important;
            color: #f1f5f9 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65) !important;
        }
        html.dark .swal2-title, html[data-theme="dark"] .swal2-title {
            color: #ffffff !important;
            font-size: 1.15rem !important;
            font-weight: 800 !important;
        }
        html.dark .swal2-html-container, html[data-theme="dark"] .swal2-html-container {
            color: #94a3b8 !important;
        }
        .swal2-actions button {
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            padding: 9px 20px !important;
        }
        /* SweetAlert2 Animated Checkmark / Success Icon Styling */
        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
        }
        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: #10b981 !important;
        }
        .swal2-icon.swal2-success .swal2-success-ring {
            border: .25em solid rgba(16, 185, 129, 0.25) !important;
        }
        html.dark .swal2-icon.swal2-success [class^=swal2-success-circular-line],
        html.dark .swal2-icon.swal2-success .swal2-success-fix,
        html[data-theme="dark"] .swal2-icon.swal2-success [class^=swal2-success-circular-line],
        html[data-theme="dark"] .swal2-icon.swal2-success .swal2-success-fix {
            background-color: #0d121d !important;
        }
        html:not(.dark) .swal2-icon.swal2-success [class^=swal2-success-circular-line],
        html:not(.dark) .swal2-icon.swal2-success .swal2-success-fix {
            background-color: #ffffff !important;
        }
        /* SweetAlert2 Warning / Danger Icon Styling (Logout Confirmation) */
        .swal2-icon.swal2-warning {
            border-color: #f43f5e !important;
            color: #f43f5e !important;
        }
        .swal2-icon.swal2-warning .swal2-icon-content {
            color: #f43f5e !important;
            font-family: 'Geist', sans-serif !important;
            font-weight: 800 !important;
        }
        .swal2-timer-progress-bar {
            background: #e11d48 !important;
        }
    </style>
</head>
<body class="shadcn-ui bg-[#090b10] text-slate-100 flex h-[100dvh] overflow-hidden antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-blue-600 focus:px-4 focus:py-2 focus:text-white focus:shadow-lg">Langsung ke konten</a>

    <!-- Sidebar Command Panel -->
    <aside id="mainSidebar" class="w-64 bg-[#07090e] border-r border-white/[0.07] flex flex-col flex-shrink-0 select-none z-30 transition-all duration-300 relative">
        <!-- Brand Header -->
        <div class="p-4 border-b border-white/[0.07] bg-white/[0.01] flex flex-col gap-2.5">
            <a href="<?= base_url('dashboard') ?>" class="block group">
                <div id="sidebarBrandLogoBox" class="bg-[#0c1018] px-3 py-2.5 rounded-xl border border-white/[0.07] group-hover:border-blue-500/40 transition-colors flex items-center justify-center shadow-inner">
                    <img src="<?= base_url('assets/img/logo_besmindo.png') ?>" alt="PT. Besmindo Materi Sewatama" class="max-h-8 w-auto object-contain">
                </div>
            </a>
            <div class="sidebar-text flex items-center justify-between px-0.5">
                <div>
                    <h1 class="font-bold text-xs tracking-tight text-white flex items-center gap-1.5">
                        <span>SIMOR</span>
                        <span class="text-[10px] font-mono font-semibold text-blue-400 bg-blue-500/10 border border-blue-500/25 px-1.5 py-0.2 rounded">BMS</span>
                    </h1>
                    <p class="text-[10px] text-slate-500 font-medium tracking-tight mt-0.5">Rig Operations Telemetry</p>
                </div>
                <span class="text-[9px] font-mono text-slate-600 uppercase tracking-widest">v3.0</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-5">
            
            <!-- 1. RINGKASAN UTAMA -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">01 // Ringkasan Utama</p>
                <nav aria-label="Navigasi utama" class="space-y-0.5">
                    <a href="<?= base_url('dashboard') ?>" title="Dashboard Utama & Monitoring Armada Rig" class="nav-item <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
                        <i class="fa-solid fa-gauge-high text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Dashboard Utama</span>
                    </a>
                </nav>
            </div>

            <!-- 2. OPERASIONAL HARIAN -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">02 // Operasional Harian</p>
                <nav aria-label="Pencatatan operasi harian" class="space-y-0.5">
                    <a href="<?= base_url('daily-report') ?>" title="Daily Report (Daftar Pekerjaan Sumur / Well Job)" class="nav-item <?= (str_starts_with(uri_string(), 'daily-report') && !str_starts_with(uri_string(), 'daily-report/log-harian')) ? 'active' : '' ?>">
                        <i class="fa-solid fa-bore-hole text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Daily Report (Pekerjaan Sumur)</span>
                    </a>
                    <a href="<?= base_url('daily-report/log-harian') ?>" title="Input Log Harian Operasi & Downtime (24 Jam)" class="nav-item <?= str_starts_with(uri_string(), 'daily-report/log-harian') ? 'active' : '' ?>">
                        <i class="fa-solid fa-clipboard-list text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Log Harian Operasi</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-mono font-semibold px-1.5 py-0.5 rounded bg-white/[0.05] text-slate-400 border border-white/[0.06]">24H</span>
                    </a>
                    <a href="<?= base_url('npt') ?>" title="Laporan & Rekapitulasi Downtime Rig (Buku NPT)" class="nav-item <?= str_starts_with(uri_string(), 'npt') ? 'active' : '' ?>">
                        <i class="fa-solid fa-table-cells text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Laporan NPT (Downtime Rig)</span>
                    </a>
                </nav>
            </div>

            <!-- 3. REKAPITULASI & EVALUASI -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">03 // Rekapitulasi &amp; Evaluasi</p>
                <nav aria-label="Laporan dan evaluasi berkala" class="space-y-0.5">
                    <a href="<?= base_url('monthly-report') ?>" title="Monthly Report (Evaluasi Performa RAU & Revenue ODR)" class="nav-item <?= str_starts_with(uri_string(), 'monthly-report') ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-check text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Monthly Report (Performa RAU)</span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan') ?>" title="Rekapitulasi Tahunan (Akumulasi 12 Bulan Daily Report & NPT)" class="nav-item <?= str_starts_with(uri_string(), 'rekap-tahunan') ? 'active' : '' ?>">
                        <i class="fa-solid fa-layer-group text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Rekapitulasi Tahunan</span>
                    </a>
                </nav>
            </div>

            <!-- 4. MASTER DATA & SISTEM -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">04 // Master Data &amp; Sistem</p>
                <nav aria-label="Master data dan sistem" class="space-y-0.5">
                    <a href="<?= base_url('master') ?>" title="Pusat Kelola Master Rig, Kategori NPT, 3rd Party & Lokasi" class="nav-item <?= str_starts_with(uri_string(), 'master') ? 'active' : '' ?>">
                        <i class="fa-solid fa-database text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Master Data (Rig &amp; Vendor)</span>
                    </a>
                    <a href="<?= base_url('audit-log') ?>" title="Log Audit Aktivitas & Keamanan Sistem" class="nav-item <?= str_starts_with(uri_string(), 'audit-log') ? 'active' : '' ?>">
                        <i class="fa-solid fa-shield-halved text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Log Audit Sistem</span>
                    </a>
                    <a href="<?= base_url('errors/notifikasi') ?>" title="Pratinjau Komponen Notifikasi & Error UI (Developer)" class="nav-item opacity-60 hover:opacity-100 transition-opacity <?= str_starts_with(uri_string(), 'errors') ? 'active !opacity-100' : '' ?>">
                        <i class="fa-solid fa-palette text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate text-[11px]">Pratinjau Desain UI</span>
                        <span class="sidebar-text flex-shrink-0 text-[8px] font-mono font-semibold px-1 py-0.2 rounded bg-white/[0.04] text-slate-500 border border-white/[0.05]">DEV</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Lead Architect & Developer Credit Badge -->
        <div class="px-3 py-2 border-t border-white/[0.07] bg-white/[0.01]">
            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-white/[0.02] border border-white/[0.05] hover:border-emerald-500/30 transition-all group" title="Project Architected &amp; Engineered by Muhammad Rafi">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-5 h-5 rounded-md bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400 shrink-0 group-hover:bg-emerald-500/20 transition">
                        <i class="fa-solid fa-code text-[10px]"></i>
                    </div>
                    <div class="truncate sidebar-text leading-tight">
                        <span class="text-[9px] font-mono text-slate-500 uppercase tracking-wider block">Lead Architect</span>
                        <span class="text-[11px] font-bold text-slate-300 group-hover:text-emerald-400 transition truncate block">Muhammad Rafi</span>
                    </div>
                </div>
                <span class="sidebar-text text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">ENG</span>
            </div>
        </div>

        <!-- Operator Session Footer -->
        <div class="p-3 border-t border-white/[0.07] bg-[#05070b] flex items-center justify-between gap-2">
            <a href="<?= base_url('akun/password') ?>" class="flex items-center gap-2.5 overflow-hidden group" title="Klik untuk Ubah Password & Keamanan Akun">
                <div class="w-8 h-8 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-400 flex items-center justify-center font-mono font-bold text-xs flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition">
                    <?= strtoupper(substr(session()->get('nama') ?? 'A', 0, 1)) ?>
                </div>
                <div class="leading-tight truncate sidebar-text">
                    <p class="text-xs font-semibold text-slate-200 group-hover:text-amber-400 transition truncate"><?= esc(session()->get('nama') ?? 'Administrator') ?></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-mono text-slate-400">Ubah Password</span>
                    </div>
                </div>
            </a>
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <a href="<?= base_url('akun/password') ?>" title="Keamanan Akun & Ubah Password"
                    class="w-8 h-8 rounded-lg bg-white/[0.03] hover:bg-amber-500/15 border border-white/[0.06] hover:border-amber-500/30 text-slate-400 hover:text-amber-400 flex items-center justify-center transition">
                    <i class="fa-solid fa-key text-xs"></i>
                </a>
                <a href="<?= base_url('logout') ?>" id="btnSidebarLogout" title="Keluar dari Sistem (Logout)" onclick="return confirmLogout(event);"
                    class="w-8 h-8 rounded-lg bg-white/[0.03] hover:bg-rose-500/15 border border-white/[0.06] hover:border-rose-500/30 text-slate-400 hover:text-rose-400 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-power-off text-xs"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Viewport -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#090b10] relative z-10">
        <!-- Sticky Telemetry Topbar -->
        <header class="h-15 py-3 bg-[#090b10]/85 backdrop-blur-md border-b border-white/[0.07] flex items-center justify-between px-4 sm:px-6 z-20 gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <button type="button" id="btnToggleSidebar" onclick="toggleSidebar()" class="w-8 h-8 rounded-lg bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] text-slate-300 hover:text-white flex items-center justify-center transition flex-shrink-0" title="Toggle Navigasi">
                    <i id="toggleIcon" class="fa-solid fa-bars-staggered text-xs"></i>
                </button>
                <div class="h-4 w-[1px] bg-white/[0.08] hidden sm:block"></div>
                <div class="truncate">
                    <h2 class="text-sm sm:text-base font-bold text-white tracking-tight truncate"><?= $page_title ?? 'Executive Operations Center' ?></h2>
                    <p class="text-[11px] text-slate-400 truncate hidden sm:block"><?= $page_subtitle ?? 'Manajemen Operasi Armada Rig & Pengawasan Non-Productive Time' ?></p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
                <!-- Segmented Theme Switcher (Putih / Hitam) -->
                <div class="theme-switcher-pill" role="group" aria-label="Pilih Tema Tampilan">
                    <button type="button" id="btnThemeLight" onclick="setSimorTheme('light')" class="theme-switcher-btn" title="Mode Putih (Light Mode)">
                        <i class="fa-solid fa-sun text-[11px] text-amber-500"></i>
                        <span>Putih</span>
                    </button>
                    <button type="button" id="btnThemeDark" onclick="setSimorTheme('dark')" class="theme-switcher-btn" title="Mode Hitam (Dark Mode)">
                        <i class="fa-solid fa-moon text-[11px] text-blue-400"></i>
                        <span>Hitam</span>
                    </button>
                </div>

                <a href="<?= base_url('akun/password') ?>" class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/[0.03] hover:bg-amber-500/15 border border-white/[0.08] hover:border-amber-500/30 text-slate-300 hover:text-amber-400 text-xs font-semibold transition" title="Keamanan Akun & Ubah Password">
                    <i class="fa-solid fa-key text-[11px] text-amber-500"></i>
                    <span>Password</span>
                </a>

                <div class="hidden md:flex items-center gap-2 px-2.5 py-1 rounded-md bg-white/[0.02] border border-white/[0.06]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] font-mono text-slate-300"><?= date('d M Y') ?></span>
                </div>
                <a href="<?= base_url('daily-report/log-harian') ?>" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-sm transition">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Input Log 24H</span>
                </a>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6">
            <?php if (session()->getFlashdata('success')): ?>
                <div role="status" class="simor-inline-alert simor-inline-alert--success mb-5">
                    <i class="fa-solid fa-circle-check text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider block mb-0.5">TELEMETRI // BERHASIL</span>
                        <span class="text-xs"><?= session()->getFlashdata('success') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div role="alert" class="simor-inline-alert simor-inline-alert--error mb-5">
                    <i class="fa-solid fa-triangle-exclamation text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider block mb-0.5">ANOMALI // PERIKSA DATA</span>
                        <span class="text-xs"><?= session()->getFlashdata('error') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div role="alert" class="simor-inline-alert simor-inline-alert--warning mb-5">
                    <i class="fa-solid fa-shield-halved text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider block mb-0.5">PERINGATAN OPERASI</span>
                        <span class="text-xs"><?= session()->getFlashdata('warning') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="ui-page max-w-[1600px] mx-auto">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Script Global Sidebar & Theme Engine -->
    <script>
        function setSimorTheme(mode) {
            const theme = (mode === 'light') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.toggle('dark', theme === 'dark');
            localStorage.setItem('simor_theme', theme);

            const btnLight = document.getElementById('btnThemeLight');
            const btnDark  = document.getElementById('btnThemeDark');
            if (btnLight && btnDark) {
                btnLight.classList.toggle('is-active', theme === 'light');
                btnDark.classList.toggle('is-active', theme === 'dark');
            }

            // Sinkronkan warna grid & label Chart.js jika ada grafik di halaman
            if (typeof Chart !== 'undefined' && Chart.instances) {
                const isLight = (theme === 'light');
                const tickColor = isLight ? '#475569' : '#94a3b8';
                const gridColor = isLight ? 'rgba(15, 23, 42, 0.07)' : 'rgba(255, 255, 255, 0.06)';
                Object.values(Chart.instances).forEach(chart => {
                    if (!chart || !chart.options) return;
                    if (chart.options.scales) {
                        Object.values(chart.options.scales).forEach(scale => {
                            if (scale.ticks) scale.ticks.color = tickColor;
                            if (scale.grid) scale.grid.color = gridColor;
                        });
                    }
                    if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                        chart.options.plugins.legend.labels.color = isLight ? '#1e293b' : '#cbd5e1';
                    }
                    chart.update('none');
                });
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const icon = document.getElementById('toggleIcon');
            const isMinimized = sidebar.classList.contains('w-16');

            if (isMinimized) {
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-bars-staggered');
                localStorage.setItem('sidebar_minimized', 'false');
            } else {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                icon.classList.remove('fa-bars-staggered');
                icon.classList.add('fa-bars');
                localStorage.setItem('sidebar_minimized', 'true');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const currentTheme = localStorage.getItem('simor_theme') || 'dark';
            setSimorTheme(currentTheme);

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

        // Matikan perubahan angka saat scroll mouse (wheel) atau tekan panah atas/bawah pada semua <input type="number">
        document.addEventListener('wheel', function(e) {
            if (document.activeElement && document.activeElement.tagName === 'INPUT' && document.activeElement.type === 'number') {
                document.activeElement.blur();
            }
            if (e.target && e.target.tagName === 'INPUT' && e.target.type === 'number') {
                e.target.blur();
            }
        }, { capture: true, passive: true });

        document.addEventListener('keydown', function(e) {
            if (e.target && e.target.tagName === 'INPUT' && e.target.type === 'number' && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                e.preventDefault();
            }
        }, { capture: true });
    </script>

    <!-- SweetAlert2 Enterprise Library -->
    <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>

    <!-- Simor Industrial Notification & Modal Engine -->
    <script src="<?= base_url('assets/js/simor-notifications.js') ?>"></script>
    <script>
        // ═══ SWEETALERT2 EXECUTIVE LOGOUT CONFIRMATION ═══
        function confirmLogout(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            const logoutUrl = '<?= base_url('logout') ?>';
            const isDark = document.documentElement.classList.contains('dark') || 
                           document.documentElement.getAttribute('data-theme') === 'dark';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluar dari Sistem?',
                    html: `
                        <div style="font-size: 0.875rem; color: ${isDark ? '#94a3b8' : '#64748b'}; margin-top: 6px; line-height: 1.55;">
                            Apakah Anda yakin ingin mengakhiri sesi operasional <strong style="color: ${isDark ? '#f1f5f9' : '#0f172a'};">SIMOR BMS</strong>?<br>
                            Sesi Anda akan ditutup dan kredensial harus dimasukkan kembali.
                        </div>
                    `,
                    icon: 'warning',
                    iconColor: '#f43f5e',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-power-off mr-1.5"></i> Ya, Logout Sekarang',
                    cancelButtonText: '<i class="fa-solid fa-xmark mr-1.5"></i> Batal',
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: isDark ? '#334155' : '#94a3b8',
                    reverseButtons: true,
                    focusCancel: true,
                    background: isDark ? '#0d121d' : '#ffffff',
                    color: isDark ? '#f1f5f9' : '#0f172a'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengakhiri Sesi...',
                            html: '<div style="font-size: 0.85rem; color: #94a3b8; font-family: monospace;">Membersihkan kredensial &amp; mengunci telemetri...</div>',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            timer: 600,
                            timerProgressBar: true,
                            background: isDark ? '#0d121d' : '#ffffff',
                            color: isDark ? '#f1f5f9' : '#0f172a',
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        }).then(() => {
                            window.location.href = logoutUrl;
                        });
                    }
                });
            } else if (window.SimorModal && typeof window.SimorModal.confirm === 'function') {
                window.SimorModal.confirm({
                    type: 'danger',
                    title: 'Keluar dari Sistem?',
                    message: 'Apakah Anda yakin ingin mengakhiri sesi operasional SIMOR BMS?',
                    confirmText: 'Ya, Logout Sekarang',
                    cancelText: 'Batal',
                    metaTag: 'AUTH // TERMINATE_SESSION'
                }).then((confirmed) => {
                    if (confirmed) {
                        window.location.href = logoutUrl;
                    }
                });
            } else {
                if (confirm('Yakin ingin keluar dari sesi SIMOR?')) {
                    window.location.href = logoutUrl;
                }
            }
            return false;
        }

        // Global click interceptor for any logout link in the DOM
        document.addEventListener('click', (e) => {
            const logoutLink = e.target.closest('a[href*="/logout"], a[href$="logout"], [data-action="logout"]');
            if (logoutLink) {
                e.preventDefault();
                confirmLogout(e);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            <?php if (session()->getFlashdata('success')): ?>
                if (window.SimorToast) {
                    SimorToast.success(<?= json_encode(session()->getFlashdata('success')) ?>, 'OPERASI BERHASIL');
                }
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                if (window.SimorToast) {
                    SimorToast.error(<?= json_encode(session()->getFlashdata('error')) ?>, 'PERINGATAN SISTEM');
                }
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                if (window.SimorToast) {
                    SimorToast.warning(<?= json_encode(session()->getFlashdata('warning')) ?>, 'PERHATIAN');
                }
            <?php endif; ?>
            <?php if (session()->getFlashdata('info')): ?>
                if (window.SimorToast) {
                    SimorToast.info(<?= json_encode(session()->getFlashdata('info')) ?>, 'INFORMASI TELEMETRI');
                }
            <?php endif; ?>
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
