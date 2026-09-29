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
        input[type="number"] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
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
            
            <!-- 1. OVERVIEW -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">01 // Komando</p>
                <nav aria-label="Navigasi utama" class="space-y-0.5">
                    <a href="<?= base_url('dashboard') ?>" title="Dashboard Eksekutif" class="nav-item <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-pie text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Dashboard Eksekutif</span>
                    </a>
                </nav>
            </div>

            <!-- 2. OPERASI & LOG HARIAN -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">02 // Operasi Lapangan</p>
                <nav aria-label="Pencatatan operasi" class="space-y-0.5">
                    <a href="<?= base_url('daily-report') ?>" title="Data Pekerjaan Sumur" class="nav-item <?= (str_starts_with(uri_string(), 'daily-report') && !str_starts_with(uri_string(), 'daily-report/log-harian')) ? 'active' : '' ?>">
                        <i class="fa-solid fa-bore-hole text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Data Sumur (Well Job)</span>
                    </a>
                    <a href="<?= base_url('daily-report/log-harian') ?>" title="Input Daily Report & NPT (24H)" class="nav-item <?= str_starts_with(uri_string(), 'daily-report/log-harian') ? 'active' : '' ?>">
                        <i class="fa-solid fa-Sliders text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Log Harian &amp; NPT</span>
                        <span class="sidebar-text flex-shrink-0 text-[9px] font-mono font-semibold px-1.5 py-0.5 rounded bg-white/[0.05] text-slate-400 border border-white/[0.06]">24H</span>
                    </a>
                    <a href="<?= base_url('npt') ?>" title="Matriks Kalender NPT" class="nav-item <?= str_starts_with(uri_string(), 'npt') ? 'active' : '' ?>">
                        <i class="fa-solid fa-table-cells text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Matriks Analisa NPT</span>
                    </a>
                </nav>
            </div>

            <!-- 3. LAPORAN & REKAPITULASI -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">03 // Analitik &amp; RAU</p>
                <nav aria-label="Laporan dan rekapitulasi" class="space-y-0.5">
                    <a href="<?= base_url('monthly-report') ?>" title="Monthly Report (Tabel RAU)" class="nav-item <?= str_starts_with(uri_string(), 'monthly-report') ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-check text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Monthly Report (RAU)</span>
                    </a>
                    <a href="<?= base_url('rekap-tahunan') ?>" title="Pusat Rekap Tahunan" class="nav-item <?= str_starts_with(uri_string(), 'rekap-tahunan') ? 'active' : '' ?>">
                        <i class="fa-solid fa-layer-group text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Rekapitulasi Tahunan</span>
                    </a>
                </nav>
            </div>

            <!-- 4. KONFIGURASI & AUDIT -->
            <div>
                <p class="sidebar-text px-2.5 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-[0.12em] mb-1.5">04 // Infrastruktur</p>
                <nav aria-label="Master data dan audit" class="space-y-0.5">
                    <a href="<?= base_url('master') ?>" title="Master Data Hub" class="nav-item <?= str_starts_with(uri_string(), 'master') ? 'active' : '' ?>">
                        <i class="fa-solid fa-database text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text flex-1 truncate">Master Data Hub</span>
                    </a>
                    <a href="<?= base_url('audit-log') ?>" title="Jejak Audit Sistem" class="nav-item <?= str_starts_with(uri_string(), 'audit-log') ? 'active' : '' ?>">
                        <i class="fa-solid fa-terminal text-xs w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text truncate">Jejak Audit Sistem</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Operator Session Footer -->
        <div class="p-3 border-t border-white/[0.07] bg-[#05070b] flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-400 flex items-center justify-center font-mono font-bold text-xs flex-shrink-0">
                    <?= strtoupper(substr(session()->get('nama') ?? 'A', 0, 1)) ?>
                </div>
                <div class="leading-tight truncate sidebar-text">
                    <p class="text-xs font-semibold text-slate-200 truncate"><?= esc(session()->get('nama') ?? 'Administrator') ?></p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-mono text-slate-500">AUTHENTICATED</span>
                    </div>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" title="Keluar dari Sistem" onclick="return confirm('Yakin ingin keluar dari sesi SIMOR?');"
                class="w-8 h-8 rounded-lg bg-white/[0.03] hover:bg-rose-500/15 border border-white/[0.06] hover:border-rose-500/30 text-slate-400 hover:text-rose-400 flex items-center justify-center transition flex-shrink-0">
                <i class="fa-solid fa-power-off text-xs"></i>
            </a>
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
                <div role="status" class="ui-alert ui-alert--success mb-5 font-medium">
                    <i class="fa-solid fa-circle-check text-sm text-emerald-400"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div role="alert" class="ui-alert ui-alert--danger mb-5 font-medium">
                    <i class="fa-solid fa-triangle-exclamation text-sm text-rose-400"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
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

    <?= $this->renderSection('scripts') ?>
</body>
</html>
