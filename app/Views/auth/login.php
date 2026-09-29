<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1120">
    <title>Masuk ke Sistem — SIMOR BMS | PT. Besmindo Materi Sewatama</title>
    <script>
        window.tailwind = { config: { darkMode: 'class' } };
        (function() {
            try {
                var savedTheme = localStorage.getItem('simor_theme_mode') || 'dark';
                document.documentElement.setAttribute('data-theme', savedTheme);
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">

    <style>
        :root {
            --lg-bg: #090d16;
            --lg-grid-line: rgba(148, 163, 184, 0.05);
            --lg-card-bg: #111827;
            --lg-card-sub: #0d1322;
            --lg-border: #1e293b;
            --lg-border-strong: #334155;
            --lg-text-title: #f8fafc;
            --lg-text-body: #cbd5e1;
            --lg-text-muted: #64748b;
            --lg-input-bg: #090d16;
            --lg-logo-bg: #0b1120;
            --lg-shadow: 0 24px 48px -12px rgba(2, 6, 23, 0.65);
        }

        :root[data-theme="light"] {
            --lg-bg: #f1f5f9;
            --lg-grid-line: rgba(15, 23, 42, 0.04);
            --lg-card-bg: #ffffff;
            --lg-card-sub: #f8fafc;
            --lg-border: #e2e8f0;
            --lg-border-strong: #cbd5e1;
            --lg-text-title: #0f172a;
            --lg-text-body: #334155;
            --lg-text-muted: #64748b;
            --lg-input-bg: #f8fafc;
            --lg-logo-bg: #0f172a;
            --lg-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.10);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--lg-bg);
            color: var(--lg-text-body);
            background-image:
                linear-gradient(to right, var(--lg-grid-line) 1px, transparent 1px),
                linear-gradient(to bottom, var(--lg-grid-line) 1px, transparent 1px);
            background-size: 36px 36px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }

        .lg-surface {
            background: var(--lg-card-bg);
            border: 1px solid var(--lg-border);
            border-radius: 20px;
            box-shadow: var(--lg-shadow);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .lg-sub-card {
            background: var(--lg-card-sub);
            border: 1px solid var(--lg-border);
            border-radius: 14px;
            transition: border-color 0.18s ease, transform 0.18s ease;
        }
        .lg-sub-card:hover {
            border-color: var(--lg-border-strong);
        }

        .lg-input {
            background: var(--lg-input-bg);
            border: 1px solid var(--lg-border-strong);
            color: var(--lg-text-title);
            border-radius: 12px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .lg-input::placeholder {
            color: var(--lg-text-muted);
        }
        .lg-input:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18);
            background: var(--lg-card-bg);
        }

        .lg-title { color: var(--lg-text-title); }
        .lg-body  { color: var(--lg-text-body); }
        .lg-muted { color: var(--lg-text-muted); }

        .lg-theme-btn {
            background: var(--lg-card-bg);
            border: 1px solid var(--lg-border-strong);
            color: var(--lg-text-body);
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.15s ease;
        }
        .lg-theme-btn:hover {
            border-color: #0284c7;
            color: var(--lg-text-title);
        }
    </style>
</head>
<body class="min-h-[100dvh] flex flex-col justify-between p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">

    <!-- Ambient Subtle Glow -->
    <div class="pointer-events-none fixed inset-0 z-0 flex items-center justify-center overflow-hidden">
        <div class="w-[680px] h-[420px] rounded-full bg-sky-500/[0.07] blur-[130px] -translate-x-24 -translate-y-12"></div>
        <div class="w-[520px] h-[360px] rounded-full bg-emerald-500/[0.05] blur-[130px] translate-x-32 translate-y-16"></div>
    </div>

    <!-- Top Utility Bar (Brand Pill + Theme Switcher) -->
    <header class="w-full max-w-[1080px] mx-auto relative z-10 flex items-center justify-between gap-4">
        <div class="inline-flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold tracking-wide uppercase lg-muted">
                PT. Besmindo Materi Sewatama
            </span>
        </div>

        <button type="button" onclick="toggleLoginTheme()" class="lg-theme-btn" id="btnThemeToggle" title="Ganti Tema Tampilan">
            <i class="fa-solid fa-sun text-amber-500" id="themeIcon"></i>
            <span id="themeText">Mode Terang</span>
        </button>
    </header>

    <!-- Main Content Grid -->
    <main class="w-full max-w-[1080px] mx-auto my-auto py-4 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- ═══ KOLOM KIRI (7 COL): PROFIL SISTEM & PILAR OPERASIONAL ═══ -->
        <section class="lg:col-span-7 lg-surface p-7 sm:p-9 hidden lg:flex flex-col justify-between relative overflow-hidden">
            <div>
                <!-- Header Badge & Logo -->
                <div class="flex items-center justify-between gap-4 mb-7">
                    <div class="px-4 py-2.5 rounded-xl border border-slate-700/60 inline-flex items-center shadow-sm"
                         style="background: var(--lg-logo-bg);">
                        <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                             alt="PT. Besmindo Materi Sewatama"
                             class="h-10 w-auto object-contain">
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-[11px] font-mono font-bold"
                         style="background: rgba(14, 165, 233, 0.1); border-color: rgba(14, 165, 233, 0.28); color: #0284c7;">
                        <i class="fa-solid fa-Layer-group text-[10px]"></i>
                        <span>SIMOR BMS · SISTEM TERPADU</span>
                    </div>
                </div>

                <!-- Headline & Executive Summary -->
                <div class="space-y-3">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-sky-500">
                        Sistem Informasi Manajemen Operasi Rig (SIMOR)
                    </div>
                    <h1 class="text-2xl xl:text-[28px] font-extrabold tracking-tight lg-title leading-[1.25]">
                        Pusat Kendali Pelaporan Operasi Sumur, Jam Kerja Rig &amp; Evaluasi Kinerja Bulanan
                    </h1>
                    <p class="text-sm lg-body leading-relaxed">
                        Platform digital resmi <strong>PT. Besmindo Materi Sewatama</strong> untuk mencatat aktivitas harian sumur secara terukur, memantau <em>Non-Productive Time</em>, dan menyajikan laporan kinerja armada secara akurat.
                    </p>
                </div>

                <!-- 3 Pilar Utama Sistem -->
                <div class="space-y-2.5 mt-6">
                    <div class="lg-sub-card p-3.5 flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                             style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold lg-title">
                                Log Harian Operasional 24 Jam (MIRU &amp; Operasi Wellwork)
                            </h3>
                            <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                Pencatatan jam kerja produktif per sumur beserta jarak tempuh lokasi yang tervalidasi penuh selama 24.00 jam setiap hari.
                            </p>
                        </div>
                    </div>

                    <div class="lg-sub-card p-3.5 flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                             style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25);">
                            <i class="fa-solid fa-clipboard-check text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold lg-title">
                                Pemantauan Terstruktur 13 Pos Downtime (SBWC &amp; Unpaid)
                            </h3>
                            <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                Rekapitulasi otomatis kendala cuaca, kesiapan jalan/lokasi, mitra <em>3rd Party</em>, hingga perbaikan peralatan rig dan <em>handling tools</em>.
                            </p>
                        </div>
                    </div>

                    <div class="lg-sub-card p-3.5 flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                             style="background: rgba(14, 165, 233, 0.12); color: #0ea5e9; border: 1px solid rgba(14, 165, 233, 0.25);">
                            <i class="fa-solid fa-chart-pie text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold lg-title">
                                Kalkulasi Otomatis RAU, Cycle Time &amp; Realisasi Pendapatan (ODR)
                            </h3>
                            <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                Perhitungan <em>Reliability, Availability, Utilization</em> serta rekapitulasi bulanan dan tahunan terintegrasi untuk seluruh unit rig.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Metrics Strip -->
            <div class="grid grid-cols-3 gap-3 pt-5 mt-6 border-t" style="border-color: var(--lg-border);">
                <div class="lg-sub-card px-3.5 py-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider lg-muted block">Cakupan Armada</span>
                    <span class="text-base font-extrabold font-mono lg-title mt-0.5 block">18 Unit Rig</span>
                </div>
                <div class="lg-sub-card px-3.5 py-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider lg-muted block">Standar Neraca Log</span>
                    <span class="text-base font-extrabold font-mono text-sky-500 mt-0.5 block">24.00 Jam/Hari</span>
                </div>
                <div class="lg-sub-card px-3.5 py-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider lg-muted block">Integrasi Laporan</span>
                    <span class="text-base font-extrabold font-mono text-emerald-500 mt-0.5 block">Otomatis &amp; Akurat</span>
                </div>
            </div>
        </section>

        <!-- ═══ KOLOM KANAN (5 COL): FORMULIR MASUK SISTEM ═══ -->
        <section class="lg:col-span-5 lg-surface p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <!-- Mobile Brand Header -->
                <div class="lg:hidden flex flex-col items-center text-center mb-6">
                    <div class="px-4 py-2.5 rounded-xl border border-slate-700/60 mb-3 shadow-sm"
                         style="background: var(--lg-logo-bg);">
                        <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                             alt="PT. Besmindo Materi Sewatama"
                             class="h-9 w-auto object-contain">
                    </div>
                    <h1 class="text-base font-extrabold lg-title tracking-tight">
                        SIMOR <span class="text-sky-500 font-mono">BMS</span>
                    </h1>
                    <p class="text-xs lg-muted mt-0.5">Sistem Informasi Manajemen Operasi Rig</p>
                </div>

                <!-- Heading Form Login -->
                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider mb-2.5"
                         style="background: rgba(14, 165, 233, 0.12); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.25);">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                        <span>Portal Akses Resmi</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold lg-title tracking-tight">
                        Selamat Datang di SIMOR
                    </h2>
                    <p class="text-xs sm:text-[13px] lg-muted mt-1 leading-relaxed">
                        Silakan masuk menggunakan akun pengguna Anda untuk mulai mengelola laporan operasional harian rig.
                    </p>
                </div>

                <!-- Flash Alerts -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div role="alert" class="p-3.5 rounded-xl mb-4 flex items-start gap-2.5 border"
                         style="background: rgba(244, 63, 94, 0.1); border-color: rgba(244, 63, 94, 0.3); color: #e11d48;">
                        <i class="fa-solid fa-circle-exclamation text-sm mt-0.5 shrink-0"></i>
                        <div class="text-xs font-semibold leading-snug">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div role="status" class="p-3.5 rounded-xl mb-4 flex items-start gap-2.5 border"
                         style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #059669;">
                        <i class="fa-solid fa-circle-check text-sm mt-0.5 shrink-0"></i>
                        <div class="text-xs font-semibold leading-snug">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Login -->
                <form action="<?= base_url('login') ?>" method="POST" class="space-y-4" id="loginForm" onsubmit="handleLoginSubmit()">
                    <?= csrf_field() ?>

                    <div>
                        <label for="username" class="block text-xs font-bold lg-title mb-1.5">
                            Username / ID Pengguna
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-user-tie absolute left-3.5 top-1/2 -translate-y-1/2 lg-muted text-xs pointer-events-none"></i>
                            <input type="text" id="username" name="username"
                                value="<?= old('username') ?>"
                                required autofocus autocomplete="username"
                                class="lg-input w-full pl-9 pr-3.5 py-2.5 text-sm font-medium"
                                placeholder="Masukkan username Anda">
                        </div>
                    </div>

                    <div>
                        <label for="loginPassword" class="block text-xs font-bold lg-title mb-1.5">
                            Kata Sandi (Password)
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 lg-muted text-xs pointer-events-none"></i>
                            <input type="password" id="loginPassword" name="password"
                                required autocomplete="current-password"
                                class="lg-input w-full pl-9 pr-10 py-2.5 text-sm font-medium"
                                placeholder="Masukkan kata sandi Anda">
                            <button type="button" id="togglePwd" onclick="togglePasswordVisibility()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg lg-muted hover:text-sky-500 flex items-center justify-center transition cursor-pointer"
                                title="Tampilkan atau sembunyikan kata sandi">
                                <i class="fa-solid fa-eye text-xs" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="btnSubmitLogin"
                            class="group w-full py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm shadow-lg shadow-sky-600/20 flex items-center justify-between transition cursor-pointer">
                            <span class="pl-1 flex items-center gap-2">
                                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                                <span id="btnSubmitText">Masuk ke Sistem SIMOR</span>
                            </span>
                            <span class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Petunjuk Ringkas -->
                <div class="lg-sub-card p-3 mt-5 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-sky-500 text-xs mt-0.5 shrink-0"></i>
                    <p class="text-[11px] lg-muted leading-relaxed">
                        Pastikan Anda memilih <strong>Unit Rig</strong> dan <strong>Periode Bulan/Tahun</strong> yang sesuai setelah berhasil masuk ke halaman utama.
                    </p>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="pt-5 mt-6 border-t flex items-center justify-between text-[11px] lg-muted"
                 style="border-color: var(--lg-border);">
                <span class="font-semibold">PT. Besmindo Materi Sewatama</span>
                <span class="font-mono font-bold text-sky-500">SIMOR BMS v3.0</span>
            </div>
        </section>

    </main>

    <!-- Bottom Copyright -->
    <footer class="w-full max-w-[1080px] mx-auto relative z-10 text-center sm:flex sm:items-center sm:justify-between text-[11px] lg-muted">
        <span>&copy; <?= date('Y') ?> PT. Besmindo Materi Sewatama. Hak cipta dilindungi.</span>
        <span class="hidden sm:inline">Divisi Operasional &amp; Pemeliharaan Armada Rig</span>
    </footer>

    <script>
        function updateThemeToggleUI(theme) {
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            if (!icon || !text) return;
            if (theme === 'light') {
                icon.className = 'fa-solid fa-moon text-slate-700';
                text.textContent = 'Mode Gelap';
            } else {
                icon.className = 'fa-solid fa-sun text-amber-400';
                text.textContent = 'Mode Terang';
            }
        }

        function toggleLoginTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            if (next === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            try {
                localStorage.setItem('simor_theme_mode', next);
            } catch (e) {}
            updateThemeToggleUI(next);
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('loginPassword');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function handleLoginSubmit() {
            const btn = document.getElementById('btnSubmitLogin');
            const txt = document.getElementById('btnSubmitText');
            if (btn && txt) {
                btn.disabled = true;
                btn.classList.add('opacity-80');
                txt.textContent = 'Memverifikasi Akun...';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            updateThemeToggleUI(current);
        });
    </script>
</body>
</html>
