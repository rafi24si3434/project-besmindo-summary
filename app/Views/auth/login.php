<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#070a11">
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">

    <style>
        /*
         * =========================================================================
         * EXECUTIVE HIGH-END DESIGN SYSTEM — SIMOR BMS / PT. BESMINDO MATERI SEWATAMA
         * Concept: $150k Agency-Tier Industrial Operations Portal
         * Archetype: Ethereal Obsidian Glass & High-Precision Telemetry
         * =========================================================================
         */
        :root {
            --bms-bg: #070a11;
            --bms-surface: #0c111c;
            --bms-surface-card: #0f1726;
            --bms-surface-elevated: #131e31;
            --bms-border: rgba(255, 255, 255, 0.08);
            --bms-border-strong: rgba(255, 255, 255, 0.15);
            --bms-text-title: #f8fafc;
            --bms-text-body: #cbd5e1;
            --bms-text-muted: #64748b;
            --bms-input-bg: rgba(6, 10, 18, 0.7);
            --bms-emerald: #10b981;
            --bms-emerald-glow: rgba(16, 185, 129, 0.22);
            --bms-sky: #0284c7;
            --bms-sky-light: #38bdf8;
            --bms-card-shadow: 0 32px 80px -20px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(255, 255, 255, 0.07);
            --bms-logo-bg: #070c16;
            --bms-grid-dot: rgba(255, 255, 255, 0.05);
        }

        :root[data-theme="light"] {
            --bms-bg: #f1f5f9;
            --bms-surface: #ffffff;
            --bms-surface-card: #f8fafc;
            --bms-surface-elevated: #f1f5f9;
            --bms-border: #e2e8f0;
            --bms-border-strong: #cbd5e1;
            --bms-text-title: #0f172a;
            --bms-text-body: #334155;
            --bms-text-muted: #64748b;
            --bms-input-bg: #ffffff;
            --bms-emerald: #059669;
            --bms-emerald-glow: rgba(5, 150, 105, 0.15);
            --bms-sky: #0284c7;
            --bms-sky-light: #0284c7;
            --bms-card-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.09), 0 0 0 1px rgba(0, 0, 0, 0.05);
            --bms-logo-bg: #0f172a;
            --bms-grid-dot: rgba(15, 23, 42, 0.04);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bms-bg);
            color: var(--bms-text-body);
            transition: background-color 0.25s ease, color 0.25s ease;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }

        /* ═══ AMBIENT ATMOSPHERIC BACKGROUND ═══ */
        .ambient-mesh {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .ambient-mesh::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--bms-grid-dot) 1.25px, transparent 1.25px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, #000 60%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, #000 60%, transparent 100%);
        }

        .ambient-orb-1 {
            position: absolute;
            top: -12%;
            left: -8%;
            width: 580px;
            height: 580px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.14) 0%, rgba(14, 165, 233, 0) 70%);
            filter: blur(80px);
            animation: orbFloat 14s ease-in-out infinite alternate;
        }

        .ambient-orb-2 {
            position: absolute;
            bottom: -15%;
            right: -6%;
            width: 620px;
            height: 620px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.13) 0%, rgba(16, 185, 129, 0) 70%);
            filter: blur(90px);
            animation: orbFloat 18s ease-in-out infinite alternate-reverse;
        }

        @keyframes orbFloat {
            0%   { transform: translate(0px, 0px) scale(1); }
            50%  { transform: translate(35px, -25px) scale(1.08); }
            100% { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* ═══ DOUBLE-BEZEL (DOPPELRAND) HARDWARE ARCHITECTURE ═══ */
        .bms-outer-chassis {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--bms-border);
            border-radius: 26px;
            box-shadow: var(--bms-card-shadow);
            padding: 7px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .bms-inner-core {
            border-radius: 20px;
            overflow: hidden;
            background: var(--bms-surface);
            border: 1px solid var(--bms-border);
        }

        /* ═══ MASTER BESMINDO LOGO SHOWCASE & SPECIAL ANIMATIONS ═══ */
        .besmindo-logo-pod {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            padding: 12px 22px;
            background: var(--bms-logo-bg);
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 
                0 14px 35px -10px rgba(0, 0, 0, 0.65),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                inset 0 1px 1px rgba(255, 255, 255, 0.22);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease, border-color 0.4s ease;
            overflow: hidden;
            transform-style: preserve-3d;
            perspective: 800px;
        }

        .besmindo-logo-pod:hover {
            border-color: rgba(16, 185, 129, 0.55);
            box-shadow: 
                0 22px 50px -12px rgba(16, 185, 129, 0.35),
                0 0 35px rgba(2, 132, 199, 0.22),
                inset 0 1px 2px rgba(255, 255, 255, 0.35);
        }

        /* ── Specialized Animation 1: Multi-phase Electromagnetic Reactor Aura ── */
        .besmindo-logo-glow {
            position: absolute;
            inset: -55%;
            background: radial-gradient(circle at 40% 50%, rgba(16, 185, 129, 0.35) 0%, rgba(2, 132, 199, 0.22) 40%, transparent 72%);
            filter: blur(22px);
            opacity: 0.85;
            animation: specialReactorAura 6s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes specialReactorAura {
            0%, 100% {
                opacity: 0.6;
                transform: scale(0.95) rotate(0deg);
                filter: blur(20px);
            }
            50% {
                opacity: 1;
                transform: scale(1.15) rotate(180deg);
                filter: blur(28px);
            }
        }

        /* ── Specialized Animation 2: Laser Scan Sheen Glint ── */
        .besmindo-logo-pod::after {
            content: '';
            position: absolute;
            top: -60%;
            left: -120%;
            width: 260%;
            height: 220%;
            background: linear-gradient(
                110deg,
                transparent 35%,
                rgba(56, 189, 248, 0.15) 45%,
                rgba(255, 255, 255, 0.9) 50%,
                rgba(16, 185, 129, 0.28) 55%,
                transparent 65%
            );
            transform: skewX(-25deg);
            animation: specialLaserSweep 4.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            pointer-events: none;
            z-index: 4;
        }

        @keyframes specialLaserSweep {
            0% {
                transform: translateX(-100%) skewX(-25deg);
                opacity: 0;
            }
            8% {
                opacity: 0.95;
            }
            36% {
                transform: translateX(115%) skewX(-25deg);
                opacity: 0.95;
            }
            37%, 100% {
                transform: translateX(115%) skewX(-25deg);
                opacity: 0;
            }
        }

        /* ── Specialized Animation 3: Micro Cybernetic HUD Corner Brackets ── */
        .hud-bracket {
            position: absolute;
            width: 10px;
            height: 10px;
            pointer-events: none;
            z-index: 5;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            opacity: 0.7;
            animation: hudBracketGlow 3.5s ease-in-out infinite alternate;
        }
        .hud-bracket.hud-tl {
            top: 5px;
            left: 5px;
            border-top: 2px solid #10b981;
            border-left: 2px solid #10b981;
        }
        .hud-bracket.hud-tr {
            top: 5px;
            right: 5px;
            border-top: 2px solid #38bdf8;
            border-right: 2px solid #38bdf8;
        }
        .hud-bracket.hud-bl {
            bottom: 5px;
            left: 5px;
            border-bottom: 2px solid #38bdf8;
            border-left: 2px solid #38bdf8;
        }
        .hud-bracket.hud-br {
            bottom: 5px;
            right: 5px;
            border-bottom: 2px solid #10b981;
            border-right: 2px solid #10b981;
        }

        .besmindo-logo-pod:hover .hud-bracket {
            opacity: 1;
        }
        .besmindo-logo-pod:hover .hud-bracket.hud-tl {
            transform: translate(-2px, -2px) scale(1.18);
            box-shadow: -2px -2px 8px rgba(16, 185, 129, 0.7);
        }
        .besmindo-logo-pod:hover .hud-bracket.hud-tr {
            transform: translate(2px, -2px) scale(1.18);
            box-shadow: 2px -2px 8px rgba(56, 189, 248, 0.7);
        }
        .besmindo-logo-pod:hover .hud-bracket.hud-bl {
            transform: translate(-2px, 2px) scale(1.18);
            box-shadow: -2px 2px 8px rgba(56, 189, 248, 0.7);
        }
        .besmindo-logo-pod:hover .hud-bracket.hud-br {
            transform: translate(2px, 2px) scale(1.18);
            box-shadow: 2px 2px 8px rgba(16, 185, 129, 0.7);
        }

        @keyframes hudBracketGlow {
            0% {
                opacity: 0.5;
                filter: brightness(0.9);
            }
            100% {
                opacity: 1;
                filter: brightness(1.3) drop-shadow(0 0 5px rgba(16, 185, 129, 0.55));
            }
        }

        /* ── Specialized Animation 4: Telemetry Scanline Texture ── */
        .besmindo-logo-scanline {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                transparent 50%,
                rgba(0, 0, 0, 0.2) 51%,
                transparent 52%
            );
            background-size: 100% 4px;
            pointer-events: none;
            opacity: 0.45;
            z-index: 2;
        }

        /* ── Specialized Animation 5: Levitation with Phosphor Neon Halo Shadows ── */
        .besmindo-logo-img {
            position: relative;
            z-index: 3;
            height: 66px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.45));
            animation: besmindoSpecialFloat 4.6s cubic-bezier(0.45, 0, 0.55, 1) infinite;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), filter 0.35s ease;
            will-change: transform, filter;
        }

        @media (min-width: 1280px) {
            .besmindo-logo-img {
                height: 74px;
            }
        }

        @keyframes besmindoSpecialFloat {
            0%, 100% {
                transform: translateY(0px) scale(1);
                filter: 
                    drop-shadow(0 4px 10px rgba(0, 0, 0, 0.5))
                    drop-shadow(0 0 14px rgba(16, 185, 129, 0.28));
            }
            50% {
                transform: translateY(-4px) scale(1.015);
                filter: 
                    drop-shadow(0 12px 24px rgba(0, 0, 0, 0.6))
                    drop-shadow(0 0 24px rgba(56, 189, 248, 0.45));
            }
        }

        /* ═══ TELEMETRY RADAR STATUS PILL ═══ */
        .radar-beacon {
            position: relative;
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background-color: #10b981;
        }
        .radar-beacon::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background-color: #10b981;
            opacity: 0.6;
            animation: beaconPing 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes beaconPing {
            0%   { transform: scale(0.8); opacity: 0.8; }
            75%, 100% { transform: scale(2.4); opacity: 0; }
        }

        /* ═══ TACTILE FORM INPUT CONTROLS ═══ */
        .bms-input-box {
            position: relative;
            background: var(--bms-input-bg);
            border: 1px solid var(--bms-border-strong);
            border-radius: 12px;
            color: var(--bms-text-title);
            transition: border-color 0.18s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.18s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.18s ease;
        }
        .bms-input-box:focus-within {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2), 0 8px 20px -6px rgba(2, 132, 199, 0.15);
            background: var(--bms-surface);
        }
        .bms-input-box input {
            background: transparent;
            color: var(--bms-text-title);
            outline: none;
        }
        .bms-input-box input::placeholder {
            color: var(--bms-text-muted);
        }

        /* ═══ BUTTON-IN-BUTTON PRIMARY CTA ═══ */
        .bms-btn-cta {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 7px 8px 7px 20px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 16px -2px rgba(2, 132, 199, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.25);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            overflow: hidden;
        }
        .bms-btn-cta:hover:not(:disabled) {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            box-shadow: 0 8px 24px -3px rgba(2, 132, 199, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }
        .bms-btn-cta:active:not(:disabled) {
            transform: translateY(1px) scale(0.99);
            box-shadow: 0 2px 8px -1px rgba(2, 132, 199, 0.3);
        }
        .bms-btn-cta .btn-icon-island {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8125rem;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        }
        .bms-btn-cta:hover:not(:disabled) .btn-icon-island {
            transform: translateX(2px);
            background: rgba(255, 255, 255, 0.25);
        }

        /* ═══ THEME TOGGLE PILL ═══ */
        .bms-theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 13px;
            border-radius: 9999px;
            background: var(--bms-surface);
            border: 1px solid var(--bms-border-strong);
            color: var(--bms-text-body);
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .bms-theme-toggle:hover {
            border-color: #0284c7;
            color: var(--bms-text-title);
            transform: translateY(-1px);
        }

        /* ═══ TOP PRECISION PROGRESS LINE ═══ */
        #topProgressBar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #10b981, #0284c7, #38bdf8);
            z-index: 10000;
            transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
            opacity: 0;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.8);
        }
        #topProgressBar.is-running {
            opacity: 1;
        }

        /* ═══ IN-PLACE AUTHENTICATION HANDSHAKE STAGES ═══ */
        .auth-panel-wrapper {
            position: relative;
        }
        .auth-form-stage {
            transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), filter 0.28s ease;
        }
        .auth-panel-wrapper.is-authenticating .auth-form-stage {
            opacity: 0;
            transform: translateY(-8px);
            filter: blur(3px);
            pointer-events: none;
        }
        .auth-loading-stage {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1) 0.05s, transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
        }
        .auth-panel-wrapper.is-authenticating .auth-loading-stage {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .status-phrase {
            display: inline-block;
            transition: opacity 0.16s ease, transform 0.16s ease;
        }
        .status-phrase.is-switching {
            opacity: 0;
            transform: translateY(4px);
        }

        @keyframes pulseSpinner {
            to { transform: rotate(360deg); }
        }
        .bms-spinner {
            width: 18px;
            height: 18px;
            border-radius: 9999px;
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-top-color: #10b981;
            border-right-color: #0284c7;
            animation: pulseSpinner 0.7s linear infinite;
        }

        /* Entrance Animations */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .anim-fade-up {
            animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @media (prefers-reduced-motion: reduce) {
            .besmindo-logo-pod, .besmindo-logo-img, .besmindo-logo-glow, .hud-bracket, .ambient-orb-1, .ambient-orb-2 { 
                animation: none !important; 
                transform: none !important;
            }
            .besmindo-logo-pod::after {
                display: none !important;
            }
            .auth-form-stage, .auth-loading-stage, .status-phrase { transition: none !important; }
            .anim-fade-up { animation: none !important; }
        }
    </style>
</head>
<body class="min-h-[100dvh] flex flex-col justify-between p-3.5 sm:p-6 lg:p-8 relative">

    <!-- Top Progress Line -->
    <div id="topProgressBar" role="progressbar" aria-hidden="true"></div>

    <!-- Ambient Mesh & Luminous Orbs -->
    <div class="ambient-mesh" aria-hidden="true">
        <div class="ambient-orb-1"></div>
        <div class="ambient-orb-2"></div>
    </div>

    <!-- ═══ TOP NAVBAR HEADER ═══ -->
    <header class="w-full max-w-[1140px] mx-auto flex items-center justify-between gap-4 z-20 anim-fade-up" style="animation-delay: 0.05s;">
        <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-white/[0.03] border border-white/[0.08] backdrop-blur-md">
            <span class="radar-beacon"></span>
            <span class="text-xs font-bold tracking-tight text-slate-200">
                PT. Besmindo Materi Sewatama
            </span>
            <span class="text-xs text-slate-500 hidden sm:inline">·</span>
            <span class="text-[11px] font-mono text-emerald-400 font-semibold hidden sm:inline">OPERASIONAL RIG AKTIF</span>
        </div>

        <div class="flex items-center gap-3">
            <!-- Jakarta Realtime Monospace Clock -->
            <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/[0.02] border border-white/[0.06] text-[11px] font-mono text-slate-400">
                <i class="fa-regular fa-clock text-[10px] text-sky-400"></i>
                <span id="liveJakartaClock">--:--:-- WIB</span>
            </div>

            <!-- Theme Switcher -->
            <button type="button" onclick="toggleLoginTheme()" class="bms-theme-toggle" id="btnThemeToggle" title="Ganti Tema Tampilan">
                <i class="fa-solid fa-sun text-amber-400 text-xs" id="themeIcon"></i>
                <span id="themeText">Mode Terang</span>
            </button>
        </div>
    </header>

    <!-- ═══ MAIN DOUBLE-BEZEL CONTAINER ═══ -->
    <main class="w-full max-w-[1140px] mx-auto my-auto py-4 sm:py-6 z-10 anim-fade-up" style="animation-delay: 0.12s;">
        <div class="bms-outer-chassis">
            <div class="bms-inner-core grid grid-cols-1 lg:grid-cols-12">

                <!-- ════════════════════════════════════════════════════════════════
                     KOLOM KIRI (7 COL): EXECUTIVE RIG BRIEFING & ANIMATED LOGO
                     ════════════════════════════════════════════════════════════════ -->
                <section class="lg:col-span-7 p-6 sm:p-9 lg:p-10 hidden lg:flex flex-col justify-between border-r border-[var(--bms-border)] relative overflow-hidden">
                    
                    <!-- Top Section: Animated Logo Showcase & Fleet Meta -->
                    <div>
                        <div class="flex items-center justify-between gap-4 pb-6 border-b border-[var(--bms-border)]">
                            
                            <!-- Master Animated Logo Island -->
                            <div class="besmindo-logo-pod group cursor-pointer" id="besmindoLogoPodDesktop" title="PT. Besmindo Materi Sewatama — Oilfield Drilling Operations">
                                <span class="hud-bracket hud-tl" aria-hidden="true"></span>
                                <span class="hud-bracket hud-tr" aria-hidden="true"></span>
                                <span class="hud-bracket hud-bl" aria-hidden="true"></span>
                                <span class="hud-bracket hud-br" aria-hidden="true"></span>
                                <div class="besmindo-logo-glow" aria-hidden="true"></div>
                                <div class="besmindo-logo-scanline" aria-hidden="true"></div>
                                <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                                     alt="PT. Besmindo Materi Sewatama"
                                     class="besmindo-logo-img">
                            </div>

                            <!-- Fleet Registry Monospace Badge -->
                            <div class="text-right">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold uppercase tracking-wider mb-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    18 RIG FLEET READY
                                </div>
                                <span class="text-[11px] font-mono text-[var(--bms-text-muted)] block">PORTAL KONTROL OPERASI</span>
                            </div>
                        </div>

                        <!-- Hero Headline Block -->
                        <div class="py-6 border-b border-[var(--bms-border)]">
                            <div class="inline-flex items-center gap-2 mb-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30 uppercase tracking-widest">
                                    SIMOR // BMS-CORE
                                </span>
                                <span class="text-[11px] font-mono text-[var(--bms-text-muted)]">Sistem Informasi Manajemen Operasi Rig</span>
                            </div>

                            <h1 class="text-2xl xl:text-[27px] font-black tracking-tight text-[var(--bms-text-title)] leading-[1.28]">
                                Pelaporan Harian Sumur, Neraca 24 Jam &amp; Evaluasi Kinerja Bulanan
                            </h1>

                            <p class="text-xs sm:text-[13px] text-[var(--bms-text-muted)] mt-2.5 leading-relaxed max-w-[56ch]">
                                Portal kerja resmi divisi operasional untuk pemantauan aktivitas rig harian, pengawasan pos downtime (SBWC/Unpaid), dan rekapitulasi realisasi target pendapatan ODR.
                            </p>
                        </div>

                        <!-- 3-Pillar Architectural Specs -->
                        <div class="divide-y divide-[var(--bms-border)]">
                            
                            <div class="py-3.5 flex items-start gap-3.5 group">
                                <div class="w-7 h-7 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 font-mono text-xs font-bold mt-0.5 group-hover:scale-105 transition-transform">
                                    01
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-[var(--bms-text-title)]">
                                        Log Operasional 24.00 Jam (MIRU &amp; Operasi Wellwork)
                                    </h3>
                                    <p class="text-[11.5px] text-[var(--bms-text-muted)] mt-0.5 leading-relaxed">
                                        Pencatatan jam kerja produktif per sumur beserta jarak lokasi yang tervalidasi secara presisi dan terhindar dari benturan tanggal.
                                    </p>
                                </div>
                            </div>

                            <div class="py-3.5 flex items-start gap-3.5 group">
                                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-mono text-xs font-bold mt-0.5 group-hover:scale-105 transition-transform">
                                    02
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-[var(--bms-text-title)]">
                                        Matriks 13 Pos Downtime (SBWC &amp; Unpaid)
                                    </h3>
                                    <p class="text-[11.5px] text-[var(--bms-text-muted)] mt-0.5 leading-relaxed">
                                        Pemilahan kendala cuaca hujan/banjir, kesiapan lokasi, rekanan 3rd Party, serta perbaikan mekanikal armada &amp; handling tools.
                                    </p>
                                </div>
                            </div>

                            <div class="py-3.5 flex items-start gap-3.5 group">
                                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 font-mono text-xs font-bold mt-0.5 group-hover:scale-105 transition-transform">
                                    03
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-[var(--bms-text-title)]">
                                        Kalkulasi Otomatis Indeks RAU &amp; Realisasi ODR
                                    </h3>
                                    <p class="text-[11.5px] text-[var(--bms-text-muted)] mt-0.5 leading-relaxed">
                                        Rekapitulasi otomatis Reliability, Availability, Utilization, dan Cycle Time hingga kompilasi tahunan seluruh armada.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Bottom Live Status Bar -->
                    <div class="pt-5 border-t border-[var(--bms-border)] grid grid-cols-3 gap-3">
                        <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                            <span class="text-[10px] font-mono uppercase text-[var(--bms-text-muted)] block">Armada Rig</span>
                            <span class="text-xs sm:text-sm font-extrabold font-mono text-[var(--bms-text-title)] mt-0.5 block">18 Unit Aktif</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                            <span class="text-[10px] font-mono uppercase text-[var(--bms-text-muted)] block">Siklus Kerja</span>
                            <span class="text-xs sm:text-sm font-extrabold font-mono text-emerald-400 mt-0.5 block">24.00 Jam/Hari</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                            <span class="text-[10px] font-mono uppercase text-[var(--bms-text-muted)] block">Koneksi Sesi</span>
                            <span class="text-xs sm:text-sm font-extrabold font-mono text-sky-400 mt-0.5 block">Terenkripsi</span>
                        </div>
                    </div>

                    <!-- Lead Engineering & System Architect Signature -->
                    <div class="mt-4 pt-3.5 border-t border-[var(--bms-border)] flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400 shrink-0 shadow-sm relative group overflow-hidden">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping absolute top-1 right-1"></span>
                                <i class="fa-solid fa-code text-[11px] relative z-10"></i>
                            </div>
                            <div class="min-w-0 leading-tight">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-emerald-400">SYSTEM ARCHITECT</span>
                                    <span class="text-slate-500 text-[10px]">·</span>
                                    <span class="text-[9px] font-mono text-[var(--bms-text-muted)]">CORE PLATFORM</span>
                                </div>
                                <p class="text-xs font-bold text-[var(--bms-text-title)] mt-0.5 truncate tracking-tight">
                                    <span class="text-[var(--bms-text-muted)] font-normal">Architected &amp; Engineered by</span>
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-sky-300 to-sky-400 font-extrabold ml-1">Muhammad Rafi</span>
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 text-right hidden sm:block">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[9px] font-mono font-bold bg-white/[0.02] border border-white/[0.08] text-slate-300">
                                <i class="fa-solid fa-bolt text-[8px] text-amber-400"></i>
                                HIGH-PRECISION BUILD
                            </span>
                        </div>
                    </div>

                </section>


                <!-- ════════════════════════════════════════════════════════════════
                     KOLOM KANAN (5 COL): FORMULIR OTENTIKASI & LOADER HANDSHAKE
                     ════════════════════════════════════════════════════════════════ -->
                <section class="lg:col-span-5 p-6 sm:p-9 lg:p-10 flex flex-col justify-between bg-[var(--bms-surface-card)] relative">

                    <!-- Mobile-Only Brand Header with Animated Logo -->
                    <div class="lg:hidden flex items-center justify-between gap-3 pb-5 mb-5 border-b border-[var(--bms-border)]">
                        <div class="besmindo-logo-pod !p-2 sm:!p-2.5 !rounded-xl" id="besmindoLogoPodMobile" title="PT. Besmindo Materi Sewatama">
                            <span class="hud-bracket hud-tl" aria-hidden="true"></span>
                            <span class="hud-bracket hud-tr" aria-hidden="true"></span>
                            <span class="hud-bracket hud-bl" aria-hidden="true"></span>
                            <span class="hud-bracket hud-br" aria-hidden="true"></span>
                            <div class="besmindo-logo-glow" aria-hidden="true"></div>
                            <div class="besmindo-logo-scanline" aria-hidden="true"></div>
                            <img src="<?= base_url('assets/img/logo_besmindo.png') ?>"
                                 alt="PT. Besmindo Materi Sewatama"
                                 class="besmindo-logo-img !h-11 sm:!h-12 w-auto object-contain relative z-10">
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold uppercase mb-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                18 RIG
                            </div>
                            <span class="text-xs font-mono font-bold text-sky-400 block">SIMOR BMS</span>
                        </div>
                    </div>

                    <!-- Interactive Wrapper: Smooth In-Place Handshake Transformation -->
                    <div class="auth-panel-wrapper my-auto py-2" id="authPanelWrapper">

                        <!-- STAGE A: LOGIN FORM -->
                        <div class="auth-form-stage" id="authFormStage">
                            <div class="mb-6">
                                <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-sky-400 block mb-1">
                                    Otentikasi Pengguna
                                </span>
                                <h2 class="text-xl sm:text-2xl font-black text-[var(--bms-text-title)] tracking-tight">
                                    Masuk ke SIMOR
                                </h2>
                                <p class="text-xs text-[var(--bms-text-muted)] mt-1.5 leading-relaxed">
                                    Silakan masukkan username dan password akun operasional Anda untuk membuka lembar kerja.
                                </p>
                            </div>

                            <!-- Flash Alerts -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div role="alert" class="p-3.5 rounded-xl mb-4 flex items-start gap-2.5 border bg-rose-500/10 border-rose-500/30 text-rose-400">
                                    <i class="fa-solid fa-circle-exclamation text-xs mt-0.5 shrink-0"></i>
                                    <div class="text-xs font-semibold leading-snug">
                                        <?= session()->getFlashdata('error') ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('success')): ?>
                                <div role="status" class="p-3.5 rounded-xl mb-4 flex items-start gap-2.5 border bg-emerald-500/10 border-emerald-500/30 text-emerald-400">
                                    <i class="fa-solid fa-circle-check text-xs mt-0.5 shrink-0"></i>
                                    <div class="text-xs font-semibold leading-snug">
                                        <?= session()->getFlashdata('success') ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('login') ?>" method="POST" class="space-y-4" id="loginForm" onsubmit="return handleLoginSubmit(event)">
                                <?= csrf_field() ?>

                                <!-- Field: Username -->
                                <div>
                                    <label for="username" class="block text-xs font-bold text-[var(--bms-text-title)] mb-1.5">
                                        Username ID
                                    </label>
                                    <div class="bms-input-box flex items-center px-3.5 py-2.5">
                                        <i class="fa-solid fa-user-gear text-xs text-[var(--bms-text-muted)] mr-2.5 shrink-0"></i>
                                        <input type="text" id="username" name="username"
                                            value="<?= old('username') ?>"
                                            required autofocus autocomplete="username"
                                            class="w-full text-xs sm:text-sm font-medium"
                                            placeholder="Masukkan username akun">
                                    </div>
                                </div>

                                <!-- Field: Password -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label for="loginPassword" class="block text-xs font-bold text-[var(--bms-text-title)]">
                                            Kata Sandi
                                        </label>
                                        <span class="text-[10px] font-mono text-[var(--bms-text-muted)]">Rahasia &amp; Terenkripsi</span>
                                    </div>
                                    <div class="bms-input-box flex items-center px-3.5 py-2.5">
                                        <i class="fa-solid fa-lock text-xs text-[var(--bms-text-muted)] mr-2.5 shrink-0"></i>
                                        <input type="password" id="loginPassword" name="password"
                                            required autocomplete="current-password"
                                            class="w-full text-xs sm:text-sm font-medium"
                                            placeholder="Masukkan kata sandi akun">
                                        <button type="button" id="togglePwd" onclick="togglePasswordVisibility()"
                                            class="w-6 h-6 rounded text-[var(--bms-text-muted)] hover:text-sky-400 flex items-center justify-center transition cursor-pointer shrink-0 ml-1.5"
                                            title="Tampilkan / sembunyikan kata sandi">
                                            <i class="fa-solid fa-eye text-xs" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Submit Button (Button-In-Button) -->
                                <div class="pt-3">
                                    <button type="submit" id="btnSubmitLogin" class="bms-btn-cta group">
                                        <span>Masuk ke Lembar Kerja</span>
                                        <div class="btn-icon-island">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </div>
                                    </button>
                                </div>

                                <!-- Mobile Developer Credit Pill -->
                                <div class="lg:hidden pt-4 mt-2 border-t border-[var(--bms-border)] text-center">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/[0.02] border border-white/[0.06] text-[10px] font-mono text-slate-400">
                                        <i class="fa-solid fa-code text-[9px] text-emerald-400"></i>
                                        <span>Architected by</span>
                                        <span class="font-bold text-slate-200">Muhammad Rafi</span>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- STAGE B: IN-PLACE ARCHITECTURAL LOADER -->
                        <div class="auth-loading-stage" id="authLoadingStage" aria-live="polite">
                            <div class="py-6">
                                <!-- Operator Identifier -->
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-[var(--bms-border)] bg-white/[0.02] mb-5">
                                    <span class="radar-beacon"></span>
                                    <span id="loadingUserLabel" class="text-xs font-mono font-bold text-[var(--bms-text-title)]">
                                        @operator
                                    </span>
                                </div>

                                <!-- Spinner + Changing Status Phrase -->
                                <div class="flex items-center gap-3.5 mb-2.5">
                                    <div class="bms-spinner" id="loadingSpinner"></div>
                                    <h3 id="loadingHeadline" class="status-phrase text-base sm:text-lg font-bold text-[var(--bms-text-title)] tracking-tight">
                                        Memverifikasi kredensial...
                                    </h3>
                                </div>

                                <!-- Sub-caption updating smoothly -->
                                <p id="loadingSubline" class="status-phrase text-xs text-[var(--bms-text-muted)] leading-relaxed pl-[31px]">
                                    Memeriksa kecocokan akun dan otorisasi sesi kerja.
                                </p>

                                <!-- Minimalist Progress Track -->
                                <div class="mt-6 pl-[31px]">
                                    <div class="w-full h-1 rounded-full overflow-hidden bg-white/[0.08]">
                                        <div id="inlineProgressBar"
                                             class="h-full bg-gradient-to-r from-emerald-500 to-sky-500 transition-all duration-200 ease-out"
                                             style="width: 15%;"></div>
                                    </div>
                                    <div class="flex items-center justify-between mt-2.5 text-[11px] font-mono text-[var(--bms-text-muted)]">
                                        <span id="loadingStepCounter">TAHAP 01 / 03</span>
                                        <span id="loadingPercent" class="text-sky-400 font-bold">15%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Security Footnote -->
                    <div class="pt-5 mt-6 border-t border-[var(--bms-border)] flex items-center justify-between text-[11px] text-[var(--bms-text-muted)]">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-emerald-400 text-[10px]"></i>
                            Sesi Terenkripsi &amp; CSRF Aktif
                        </span>
                        <span class="font-mono uppercase font-bold text-[10px] text-slate-500">BMS-SECURE</span>
                    </div>

                </section>

            </div>
        </div>
    </main>

    <!-- ═══ FOOTER ═══ -->
    <footer class="w-full max-w-[1140px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--bms-text-muted)] z-20 anim-fade-up pt-1" style="animation-delay: 0.18s;">
        <div class="flex items-center gap-2">
            <span>&copy; <?= date('Y') ?> <strong>PT. Besmindo Materi Sewatama</strong>.</span>
            <span class="hidden md:inline font-mono text-[11px] text-slate-500">· Rig Operations Telemetry Portal</span>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/[0.02] border border-white/[0.06] backdrop-blur-sm text-[11px] font-mono">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            <span class="text-slate-400">Architected &amp; Engineered by</span>
            <span class="font-bold text-slate-200">Muhammad Rafi</span>
        </div>
    </footer>

    <!-- ═══ JAVASCRIPT LOGIC & TELEMETRY CLOCK ═══ -->
    <script>
        // Jakarta Live Realtime Monospace Clock
        function updateJakartaClock() {
            const clockEl = document.getElementById('liveJakartaClock');
            if (!clockEl) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateJakartaClock, 1000);
        updateJakartaClock();

        // Theme Toggle UI Controller
        function updateThemeToggleUI(theme) {
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            if (!icon || !text) return;
            if (theme === 'light') {
                icon.className = 'fa-solid fa-moon text-slate-700 text-xs';
                text.textContent = 'Mode Gelap';
            } else {
                icon.className = 'fa-solid fa-sun text-amber-400 text-xs';
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
                localStorage.setItem('simor_theme', next);
            } catch (e) {}
            updateThemeToggleUI(next);
        }

        // Password Peek Toggle
        function togglePasswordVisibility() {
            const input = document.getElementById('loginPassword');
            const icon = document.getElementById('eyeIcon');
            if (!input || !icon) return;
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

        // Fluid In-Place Auth Handshake Experience
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
            }, 120);
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
                topBar.style.width = '25%';
            }

            const steps = [
                {
                    at: 0,
                    pct: 35,
                    stepText: 'TAHAP 01 / 03',
                    title: 'Memverifikasi akun...',
                    sub: 'Memeriksa kredensial pengguna dan validitas sesi keamanan.'
                },
                {
                    at: 380,
                    pct: 75,
                    stepText: 'TAHAP 02 / 03',
                    title: 'Menyiapkan telemetri 18 rig...',
                    sub: 'Memuat data armada, lembar kerja harian, dan matriks downtime.'
                },
                {
                    at: 820,
                    pct: 100,
                    stepText: 'TAHAP 03 / 03',
                    title: 'Membuka lembar kerja SIMOR...',
                    sub: 'Otentikasi berhasil. Mengarahkan ke Executive Command Center...'
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
            }, 1150);

            return false;
        }

        // ═══ INTERACTIVE 3D SPRING TILT PARALLAX (BESMINDO LOGO) ═══
        function initLogoParallax(podId) {
            const pod = document.getElementById(podId);
            if (!pod) return;

            let isHovered = false;

            pod.addEventListener('mouseenter', () => {
                isHovered = true;
                pod.style.transition = 'transform 0.12s ease-out, box-shadow 0.3s ease, border-color 0.3s ease';
            });

            pod.addEventListener('mousemove', (e) => {
                if (!isHovered) return;
                const rect = pod.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const cx = rect.width / 2;
                const cy = rect.height / 2;
                const rotateX = ((y - cy) / cy) * -8;
                const rotateY = ((x - cx) / cx) * 8;

                pod.style.transform = `perspective(800px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.025, 1.025, 1.025)`;
            });

            pod.addEventListener('mouseleave', () => {
                isHovered = false;
                pod.style.transition = 'transform 0.55s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease, border-color 0.4s ease';
                pod.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            updateThemeToggleUI(current);
            initLogoParallax('besmindoLogoPodDesktop');
            initLogoParallax('besmindoLogoPodMobile');
        });
    </script>
</body>
</html>
