<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#090d16">
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">

    <style>
        /*
         * Design Read: Enterprise Oilfield Operations Portal (/login) for PT. Besmindo Materi Sewatama.
         * Aesthetic: Restrained Linear / Industrial-Editorial spec sheet, single accent (#0284c7),
         * crisp hairlines, tabular monospace indices, and calm architectural motion (no sci-fi radar/neon).
         */
        :root {
            --lg-bg: #090c12;
            --lg-surface: #0f141f;
            --lg-surface-elevated: #141b29;
            --lg-border: #1e2738;
            --lg-border-strong: #2d3a52;
            --lg-text-title: #f8fafc;
            --lg-text-body: #cbd5e1;
            --lg-text-muted: #64748b;
            --lg-input-bg: #0a0e17;
            --lg-accent: #0284c7;
            --lg-accent-light: #38bdf8;
            --lg-logo-plate: #0b0f19;
            --lg-shadow: 0 24px 48px -16px rgba(0, 0, 0, 0.7);
        }

        :root[data-theme="light"] {
            --lg-bg: #f3f5f8;
            --lg-surface: #ffffff;
            --lg-surface-elevated: #f8fafc;
            --lg-border: #e2e8f0;
            --lg-border-strong: #cbd5e1;
            --lg-text-title: #0f172a;
            --lg-text-body: #334155;
            --lg-text-muted: #64748b;
            --lg-input-bg: #f8fafc;
            --lg-accent: #0284c7;
            --lg-accent-light: #0284c7;
            --lg-logo-plate: #0f172a;
            --lg-shadow: 0 20px 40px -18px rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--lg-bg);
            color: var(--lg-text-body);
            transition: background-color 0.2s ease, color 0.2s ease;
            -webkit-font-smoothing: antialiased;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }

        /* Architectural Frame Container */
        .lg-frame {
            background: var(--lg-surface);
            border: 1px solid var(--lg-border);
            border-radius: 16px;
            box-shadow: var(--lg-shadow);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .lg-hairline {
            border-color: var(--lg-border);
        }

        .lg-title { color: var(--lg-text-title); }
        .lg-body  { color: var(--lg-text-body); }
        .lg-muted { color: var(--lg-text-muted); }

        /* Form Inputs — Tactile & High Contrast */
        .lg-input {
            background: var(--lg-input-bg);
            border: 1px solid var(--lg-border-strong);
            color: var(--lg-text-title);
            border-radius: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .lg-input::placeholder {
            color: var(--lg-text-muted);
        }
        .lg-input:focus {
            outline: none;
            border-color: var(--lg-accent);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.16);
            background: var(--lg-surface);
        }

        /* Primary CTA Button — Tactile push, single-line lock */
        .lg-btn-primary {
            background-color: #0284c7;
            color: #ffffff;
            border-radius: 10px;
            font-weight: 700;
            transition: background-color 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        .lg-btn-primary:hover:not(:disabled) {
            background-color: #0369a1;
        }
        .lg-btn-primary:active:not(:disabled) {
            transform: translateY(1px);
        }

        /* Theme Toggle Pill */
        .lg-theme-btn {
            background: var(--lg-surface);
            border: 1px solid var(--lg-border-strong);
            color: var(--lg-text-body);
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            transition: border-color 0.15s ease, color 0.15s ease;
        }
        .lg-theme-btn:hover {
            border-color: var(--lg-accent);
            color: var(--lg-text-title);
        }

        /* ═══ Restrained Architectural Entrance (No bounce, no scale-pop) ═══ */
        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-1 { animation: revealUp 0.45s cubic-bezier(0.22, 1, 0.36, 1) 0.02s both; }
        .reveal-2 { animation: revealUp 0.50s cubic-bezier(0.22, 1, 0.36, 1) 0.08s both; }
        .reveal-3 { animation: revealUp 0.50s cubic-bezier(0.22, 1, 0.36, 1) 0.14s both; }

        /* ═══ Top Precision Progress Bar ═══ */
        #topProgressBar {
            position: fixed;
            top: 0;
            left: 0;
            height: 2.5px;
            width: 0%;
            background-color: #0284c7;
            z-index: 10000;
            transition: width 0.22s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.2s ease;
            opacity: 0;
        }
        #topProgressBar.is-running {
            opacity: 1;
        }

        /* ═══ In-Place Session Handshake State (Clean, Editorial, Non-AI) ═══ */
        .auth-panel-wrapper {
            position: relative;
        }

        .auth-form-stage {
            transition: opacity 0.28s cubic-bezier(0.22, 1, 0.36, 1), transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), filter 0.28s ease;
        }
        .auth-panel-wrapper.is-authenticating .auth-form-stage {
            opacity: 0;
            transform: translateY(-6px);
            filter: blur(2px);
            pointer-events: none;
        }

        .auth-loading-stage {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.32s cubic-bezier(0.22, 1, 0.36, 1) 0.06s, transform 0.32s cubic-bezier(0.22, 1, 0.36, 1) 0.06s;
        }
        .auth-panel-wrapper.is-authenticating .auth-loading-stage {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Word crossfade for status text */
        .status-phrase {
            display: inline-block;
            transition: opacity 0.18s ease, transform 0.18s ease;
        }
        .status-phrase.is-switching {
            opacity: 0;
            transform: translateY(4px);
        }

        /* Minimalist 14px precision spinner */
        @keyframes crispSpin {
            to { transform: rotate(360deg); }
        }
        .crisp-spinner {
            width: 15px;
            height: 15px;
            border-radius: 9999px;
            border: 2px solid var(--lg-border-strong);
            border-top-color: #0284c7;
            animation: crispSpin 0.65s linear infinite;
            flex-shrink: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal-1, .reveal-2, .reveal-3 { animation: none !important; }
            .auth-form-stage, .auth-loading-stage, .status-phrase { transition: none !important; }
        }
    </style>
</head>
<body class="min-h-[100dvh] flex flex-col justify-between p-4 sm:p-6 lg:p-10">

    <!-- Top Edge Precision Progress Line -->
    <div id="topProgressBar" role="progressbar" aria-hidden="true"></div>

    <!-- Top Bar -->
    <header class="w-full max-w-[1060px] mx-auto flex items-center justify-between gap-4 reveal-1">
        <div class="inline-flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-semibold tracking-tight lg-muted">
                PT. Besmindo Materi Sewatama
            </span>
            <span class="text-xs lg-muted hidden sm:inline">/</span>
            <span class="text-xs font-mono lg-muted hidden sm:inline">SIMOR BMS v3.0</span>
        </div>

        <button type="button" onclick="toggleLoginTheme()" class="lg-theme-btn" id="btnThemeToggle" title="Ganti Tema Tampilan">
            <i class="fa-solid fa-sun text-amber-500" id="themeIcon"></i>
            <span id="themeText">Mode Terang</span>
        </button>
    </header>

    <!-- Main Split Frame -->
    <main class="w-full max-w-[1060px] mx-auto my-auto py-6">
        <div class="lg-frame grid grid-cols-1 lg:grid-cols-12 overflow-hidden">

            <!-- ═══ KOLOM KIRI (7 COL): EDITORIAL OPERATIONAL SPEC SHEET ═══ -->
            <section class="lg:col-span-7 p-7 sm:p-10 hidden lg:flex flex-col justify-between border-r lg-hairline reveal-2">
                <div>
                    <!-- Brand & Document Index Header -->
                    <div class="flex items-center justify-between gap-4 pb-7 border-b lg-hairline">
                        <div class="px-3.5 py-2 rounded-lg border border-slate-700/70 inline-flex items-center"
                             style="background: var(--lg-logo-plate);">
                            <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                                 alt="PT. Besmindo Materi Sewatama"
                                 class="h-9 w-auto object-contain">
                        </div>

                        <div class="text-right font-mono">
                            <span class="text-[11px] font-semibold lg-title block">SISTEM OPERASI RIG</span>
                            <span class="text-[11px] lg-muted block">18 UNIT ARMADA AKTIF</span>
                        </div>
                    </div>

                    <!-- Primary Headline (Max 2 lines rhythm, clean sans) -->
                    <div class="py-7 border-b lg-hairline">
                        <p class="text-[11px] font-mono font-semibold uppercase tracking-widest text-sky-500 mb-2">
                            SIMOR — Sistem Informasi Manajemen Operasi Rig
                        </p>
                        <h1 class="text-2xl xl:text-[26px] font-extrabold tracking-tight lg-title leading-[1.28]">
                            Pelaporan Harian Sumur, Neraca Jam Kerja &amp; Evaluasi Kinerja Bulanan
                        </h1>
                        <p class="text-xs sm:text-[13px] lg-muted mt-2.5 leading-relaxed max-w-[58ch]">
                            Portal kerja resmi divisi operasional untuk pencatatan aktivitas sumur harian, pemantauan <em>Non-Productive Time</em>, dan rekapitulasi realisasi ODR.
                        </p>
                    </div>

                    <!-- Architectural Spec Index (01 / 02 / 03 with divide-y hairlines) -->
                    <div class="divide-y lg-hairline">
                        <div class="py-4 flex items-baseline gap-4">
                            <span class="font-mono text-xs font-bold text-sky-500 shrink-0 w-7">01</span>
                            <div>
                                <h3 class="text-xs font-bold lg-title">
                                    Log Operasional 24.00 Jam (MIRU &amp; Operasi Wellwork)
                                </h3>
                                <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                    Pencatatan jam kerja produktif per sumur beserta jarak lokasi yang tervalidasi penuh selama 24 jam/hari.
                                </p>
                            </div>
                        </div>

                        <div class="py-4 flex items-baseline gap-4">
                            <span class="font-mono text-xs font-bold text-sky-500 shrink-0 w-7">02</span>
                            <div>
                                <h3 class="text-xs font-bold lg-title">
                                    Matriks 13 Pos Downtime (SBWC &amp; Unpaid)
                                </h3>
                                <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                    Klasifikasi kendala cuaca, kesiapan lokasi, mitra <em>3rd Party</em>, serta perbaikan rig &amp; <em>handling tools</em>.
                                </p>
                            </div>
                        </div>

                        <div class="py-4 flex items-baseline gap-4">
                            <span class="font-mono text-xs font-bold text-sky-500 shrink-0 w-7">03</span>
                            <div>
                                <h3 class="text-xs font-bold lg-title">
                                    Kalkulasi Otomatis RAU, Cycle Time &amp; Pendapatan
                                </h3>
                                <p class="text-xs lg-muted mt-0.5 leading-relaxed">
                                    Perhitungan <em>Reliability, Availability, Utilization</em> serta rekapitulasi bulanan dan tahunan seluruh armada.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Spec Metrics -->
                <div class="pt-5 border-t lg-hairline grid grid-cols-3 gap-4">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider lg-muted block">Armada</span>
                        <span class="text-sm font-bold font-mono lg-title mt-0.5 block">18 Unit Rig</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider lg-muted block">Standar Log</span>
                        <span class="text-sm font-bold font-mono lg-title mt-0.5 block">24.00 Jam/Hari</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider lg-muted block">Sinkronisasi</span>
                        <span class="text-sm font-bold font-mono text-sky-500 mt-0.5 block">Real-Time</span>
                    </div>
                </div>
            </section>

            <!-- ═══ KOLOM KANAN (5 COL): FORMULIR OTENTIKASI + IN-PLACE LOADER ═══ -->
            <section class="lg:col-span-5 p-6 sm:p-9 flex flex-col justify-between reveal-3"
                     style="background: var(--lg-surface-elevated);">

                <!-- Mobile Header -->
                <div class="lg:hidden flex items-center justify-between gap-3 pb-5 mb-5 border-b lg-hairline">
                    <div class="px-3 py-1.5 rounded-lg border border-slate-700/70 inline-flex items-center"
                         style="background: var(--lg-logo-plate);">
                        <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                             alt="PT. Besmindo Materi Sewatama"
                             class="h-7 w-auto object-contain">
                    </div>
                    <span class="text-xs font-mono font-bold text-sky-500">SIMOR BMS</span>
                </div>

                <!-- Interactive Wrapper: Form morphs cleanly into Loading State in-place -->
                <div class="auth-panel-wrapper my-auto py-2" id="authPanelWrapper">

                    <!-- STAGE A: NORMAL LOGIN FORM -->
                    <div class="auth-form-stage" id="authFormStage">
                        <div class="mb-6">
                            <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-sky-500 block mb-1">
                                Otentikasi Pengguna
                            </span>
                            <h2 class="text-xl sm:text-2xl font-extrabold lg-title tracking-tight">
                                Masuk ke SIMOR
                            </h2>
                            <p class="text-xs sm:text-[13px] lg-muted mt-1 leading-relaxed">
                                Masukkan ID pengguna dan kata sandi Anda untuk membuka lembar kerja operasional.
                            </p>
                        </div>

                        <!-- Flash Alerts -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div role="alert" class="p-3 rounded-lg mb-4 flex items-start gap-2.5 border"
                                 style="background: rgba(244, 63, 94, 0.08); border-color: rgba(244, 63, 94, 0.28); color: #e11d48;">
                                <i class="fa-solid fa-circle-exclamation text-xs mt-0.5 shrink-0"></i>
                                <div class="text-xs font-semibold leading-snug">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div role="status" class="p-3 rounded-lg mb-4 flex items-start gap-2.5 border"
                                 style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.28); color: #059669;">
                                <i class="fa-solid fa-circle-check text-xs mt-0.5 shrink-0"></i>
                                <div class="text-xs font-semibold leading-snug">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login') ?>" method="POST" class="space-y-4" id="loginForm" onsubmit="return handleLoginSubmit(event)">
                            <?= csrf_field() ?>

                            <div>
                                <label for="username" class="block text-xs font-semibold lg-title mb-1.5">
                                    Username
                                </label>
                                <input type="text" id="username" name="username"
                                    value="<?= old('username') ?>"
                                    required autofocus autocomplete="username"
                                    class="lg-input w-full px-3.5 py-2.5 text-sm font-medium"
                                    placeholder="Masukkan username">
                            </div>

                            <div>
                                <label for="loginPassword" class="block text-xs font-semibold lg-title mb-1.5">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <input type="password" id="loginPassword" name="password"
                                        required autocomplete="current-password"
                                        class="lg-input w-full pl-3.5 pr-10 py-2.5 text-sm font-medium"
                                        placeholder="Masukkan kata sandi">
                                    <button type="button" id="togglePwd" onclick="togglePasswordVisibility()"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-md lg-muted hover:text-sky-500 flex items-center justify-center transition cursor-pointer"
                                        title="Tampilkan atau sembunyikan kata sandi">
                                        <i class="fa-solid fa-eye text-xs" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" id="btnSubmitLogin"
                                    class="lg-btn-primary w-full py-2.5 px-4 text-sm flex items-center justify-between cursor-pointer">
                                    <span>Masuk ke Sistem</span>
                                    <i class="fa-solid fa-arrow-right text-xs opacity-80"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- STAGE B: IN-PLACE ARCHITECTURAL LOADING SEQUENCE -->
                    <div class="auth-loading-stage" id="authLoadingStage" aria-live="polite">
                        <div class="py-6">
                            <!-- Operator Pill -->
                            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md border lg-hairline mb-5"
                                 style="background: var(--lg-surface);">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                <span id="loadingUserLabel" class="text-[11px] font-mono font-semibold lg-title">
                                    @admin
                                </span>
                            </div>

                            <!-- Spinner + Changing Status Phrase -->
                            <div class="flex items-center gap-3 mb-2">
                                <div class="crisp-spinner" id="loadingSpinner"></div>
                                <h3 id="loadingHeadline" class="status-phrase text-base sm:text-lg font-bold lg-title tracking-tight">
                                    Memuat sesi kerja...
                                </h3>
                            </div>

                            <!-- Sub-caption that updates calmly -->
                            <p id="loadingSubline" class="status-phrase text-xs lg-muted leading-relaxed pl-[27px]">
                                Memeriksa kredensial akun dan menyiapkan koneksi aman.
                            </p>

                            <!-- Minimalist Single-Color Progress Track -->
                            <div class="mt-6 pl-[27px]">
                                <div class="w-full h-[3px] rounded-full overflow-hidden" style="background: var(--lg-border);">
                                    <div id="inlineProgressBar"
                                         class="h-full bg-sky-500 transition-all duration-200 ease-out"
                                         style="width: 12%;"></div>
                                </div>
                                <div class="flex items-center justify-between mt-2 text-[11px] font-mono lg-muted">
                                    <span id="loadingStepCounter">TAHAP 01 / 03</span>
                                    <span id="loadingPercent">12%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Security Footnote -->
                <div class="pt-5 mt-6 border-t lg-hairline flex items-center justify-between text-[11px] lg-muted">
                    <span>Sesi terenkripsi · Proteksi CSRF aktif</span>
                    <span class="font-mono">BMS-OPS</span>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-[1060px] mx-auto text-center sm:flex sm:items-center sm:justify-between text-[11px] lg-muted">
        <span>&copy; <?= date('Y') ?> PT. Besmindo Materi Sewatama</span>
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

        let isLoginSubmitting = false;

        function setPhraseSmooth(headlineEl, sublineEl, newTitle, newSub) {
            if (!headlineEl || !sublineEl) return;
            if (headlineEl.dataset.current === newTitle) return;
            headlineEl.dataset.current = newTitle;

            headlineEl.classList.add('is-switching');
            sublineEl.classList.add('is-switching');

            setTimeout(() => {
                headlineEl.textContent = newTitle;
                sublineEl.textContent = newSub;
                headlineEl.classList.remove('is-switching');
                sublineEl.classList.remove('is-switching');
            }, 140);
        }

        function handleLoginSubmit(e) {
            if (isLoginSubmitting) return true;
            if (e) e.preventDefault();

            const form = document.getElementById('loginForm');
            const userInp = document.getElementById('username');
            const passInp = document.getElementById('loginPassword');
            if (!userInp.value.trim() || !passInp.value) {
                form.reportValidity();
                return false;
            }

            isLoginSubmitting = true;
            const uname = userInp.value.trim().toLowerCase();

            const wrapper     = document.getElementById('authPanelWrapper');
            const topBar      = document.getElementById('topProgressBar');
            const userLabel   = document.getElementById('loadingUserLabel');
            const headlineEl  = document.getElementById('loadingHeadline');
            const sublineEl   = document.getElementById('loadingSubline');
            const inlineBar   = document.getElementById('inlineProgressBar');
            const stepCounter = document.getElementById('loadingStepCounter');
            const pctEl       = document.getElementById('loadingPercent');

            if (userLabel) userLabel.textContent = '@' + uname;
            if (wrapper) wrapper.classList.add('is-authenticating');
            if (topBar) {
                topBar.classList.add('is-running');
                topBar.style.width = '20%';
            }

            const steps = [
                {
                    at: 0,
                    pct: 28,
                    stepText: 'TAHAP 01 / 03',
                    title: 'Memverifikasi akun...',
                    sub: 'Memeriksa kredensial pengguna dan validitas sesi keamanan.'
                },
                {
                    at: 420,
                    pct: 68,
                    stepText: 'TAHAP 02 / 03',
                    title: 'Memuat data operasional rig...',
                    sub: 'Menyiapkan parameter 18 unit rig, log harian, dan matriks NPT.'
                },
                {
                    at: 860,
                    pct: 100,
                    stepText: 'TAHAP 03 / 03',
                    title: 'Membuka lembar kerja SIMOR...',
                    sub: 'Otentikasi selesai. Mengarahkan ke halaman utama...'
                }
            ];

            steps.forEach((st) => {
                setTimeout(() => {
                    setPhraseSmooth(headlineEl, sublineEl, st.title, st.sub);
                    if (inlineBar) inlineBar.style.width = st.pct + '%';
                    if (topBar) topBar.style.width = st.pct + '%';
                    if (stepCounter) stepCounter.textContent = st.stepText;
                    if (pctEl) pctEl.textContent = st.pct + '%';
                }, st.at);
            });

            setTimeout(() => {
                HTMLFormElement.prototype.submit.call(form);
            }, 1220);

            return false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            updateThemeToggleUI(current);
        });
    </script>
</body>
</html>
