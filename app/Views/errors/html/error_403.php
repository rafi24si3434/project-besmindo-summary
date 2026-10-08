<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>403 // Akses Ditolak — SIMOR BMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('simor_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <style>
        :root {
            --bg-canvas: #07090e;
            --bg-card: #0d121c;
            --bg-sub: #080b11;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.16);
            --text-title: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #64748b;
            --grid-line: rgba(245, 158, 11, 0.06);
            --accent-amber: #f59e0b;
            --accent-amber-bg: rgba(245, 158, 11, 0.12);
            --accent-amber-border: rgba(245, 158, 11, 0.35);
            --btn-primary-bg: #d97706;
            --btn-primary-hover: #b45309;
            --btn-primary-text: #ffffff;
            --btn-secondary-bg: #141c2b;
            --btn-secondary-border: #25334a;
            --btn-secondary-text: #cbd5e1;
        }

        html[data-theme="light"] {
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --bg-sub: #f1f5f9;
            --border-subtle: #e2e8f0;
            --border-strong: #cbd5e1;
            --text-title: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --grid-line: rgba(217, 119, 6, 0.05);
            --accent-amber: #b45309;
            --accent-amber-bg: #fffbeb;
            --accent-amber-border: #fde68a;
            --btn-primary-bg: #d97706;
            --btn-primary-hover: #b45309;
            --btn-primary-text: #ffffff;
            --btn-secondary-bg: #ffffff;
            --btn-secondary-border: #cbd5e1;
            --btn-secondary-text: #1e293b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-canvas);
            color: var(--text-body);
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background-image: 
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 32px 32px;
            background-position: center center;
        }

        .err-topbar {
            position: relative;
            z-index: 10;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--bg-card);
            backdrop-filter: blur(12px);
        }

        .err-brand {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
        }

        .err-brand-logo {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: #d97706;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 900;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
        }

        .err-brand-text h1 {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-title);
            line-height: 1.2;
        }
        .err-brand-text span {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: var(--btn-secondary-bg);
            border: 1px solid var(--btn-secondary-border);
            color: var(--text-body);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .err-stage {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .err-shell {
            max-width: 760px;
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
            border-radius: 20px;
            box-shadow: 0 24px 50px -12px rgba(0, 0, 0, 0.45);
            overflow: hidden;
        }

        .err-shell-ribbon {
            background: repeating-linear-gradient(
                -45deg,
                rgba(245, 158, 11, 0.25),
                rgba(245, 158, 11, 0.25) 10px,
                rgba(245, 158, 11, 0.08) 10px,
                rgba(245, 158, 11, 0.08) 20px
            );
            height: 6px;
            width: 100%;
            border-bottom: 1px solid rgba(245, 158, 11, 0.35);
        }

        .err-content {
            padding: 2.5rem 2.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .err-header-visual {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1.5rem;
        }

        .err-num-cluster {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
        }

        .err-num-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(4.5rem, 12vw, 7.5rem);
            font-weight: 900;
            line-height: 0.9;
            letter-spacing: -0.06em;
            color: var(--accent-amber);
            text-shadow: 0 0 32px rgba(245, 158, 11, 0.25);
        }

        .err-badge-stack {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .err-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            width: fit-content;
        }

        .err-pill--amber {
            background: var(--accent-amber-bg);
            border: 1px solid var(--accent-amber-border);
            color: var(--accent-amber);
        }

        .lock-box {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            background: var(--accent-amber-bg);
            border: 1px solid var(--accent-amber-border);
            color: var(--accent-amber);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .err-prose h2 {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            font-weight: 800;
            color: var(--text-title);
            letter-spacing: -0.02em;
            line-height: 1.25;
            margin-bottom: 0.5rem;
        }

        .err-prose p {
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--text-body);
        }

        .err-telemetry-panel {
            background: var(--bg-sub);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        .err-telemetry-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 0.35rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .err-telemetry-row:last-child { border-bottom: none; padding-bottom: 0; }

        .err-telemetry-key {
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
        }
        .err-telemetry-val {
            color: var(--text-title);
            font-weight: 700;
            text-align: right;
        }

        .err-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            padding-top: 0.5rem;
        }

        .err-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 11px;
            font-size: 12.5px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
        }
        .err-btn:active { transform: translateY(1px); }

        .err-btn--primary {
            background: var(--btn-primary-bg);
            color: var(--btn-primary-text) !important;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
        }
        .err-btn--primary:hover { background: var(--btn-primary-hover); }

        .err-btn--secondary {
            background: var(--btn-secondary-bg);
            border: 1px solid var(--btn-secondary-border);
            color: var(--btn-secondary-text) !important;
        }
        .err-btn--secondary:hover {
            border-color: var(--accent-amber);
            color: var(--text-title) !important;
        }

        .err-footer {
            position: relative;
            z-index: 10;
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <header class="err-topbar">
        <a href="<?= base_url() ?>" class="err-brand" title="Ke Beranda SIMOR">
            <div class="err-brand-logo">BMS</div>
            <div class="err-brand-text">
                <h1>SIMOR BMS</h1>
                <span>PT. BESMINDO MATERI SEWATAMA</span>
            </div>
        </a>

        <button type="button" onclick="toggleTheme()" class="theme-toggle-btn" id="btnThemeToggle">
            <i class="fa-solid fa-circle-half-stroke text-amber-500"></i>
            <span id="themeToggleLabel">Mode</span>
        </button>
    </header>

    <main class="err-stage">
        <div class="err-shell">
            <div class="err-shell-ribbon"></div>

            <div class="err-content">
                <div class="err-header-visual">
                    <div class="err-num-cluster">
                        <div class="err-num-display">403</div>
                        <div class="err-badge-stack">
                            <span class="err-pill err-pill--amber">
                                <i class="fa-solid fa-lock"></i>
                                <span>ACCESS // RESTRICTED</span>
                            </span>
                            <span class="err-pill err-pill--amber">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>PRIVILEGE DENIED</span>
                            </span>
                        </div>
                    </div>

                    <div class="lock-box" title="Security perimeter active">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>

                <div class="err-prose">
                    <h2>Protokol Keamanan: Akses Modul Ditolak</h2>
                    <p>
                        Akun Anda saat ini tidak memiliki otorisasi yang mencukupi untuk membuka modul atau data ini.
                        Bila sesi login Anda telah kedaluwarsa, silakan lakukan login ulang untuk memperbarui token otentikasi.
                    </p>
                </div>

                <div class="err-telemetry-panel">
                    <div class="err-telemetry-row">
                        <span class="err-telemetry-key">Status Otorisasi</span>
                        <span class="err-telemetry-val" style="color: var(--accent-amber);">HTTP 403 FORBIDDEN</span>
                    </div>
                    <div class="err-telemetry-row">
                        <span class="err-telemetry-key">Target Rute</span>
                        <span class="err-telemetry-val"><?= esc(current_url()) ?></span>
                    </div>
                    <div class="err-telemetry-row">
                        <span class="err-telemetry-key">Waktu Verifikasi</span>
                        <span class="err-telemetry-val"><?= date('Y-m-d H:i:s') ?> WIB</span>
                    </div>
                    <div class="err-telemetry-row">
                        <span class="err-telemetry-key">Status Firewall</span>
                        <span class="err-telemetry-val" style="color: #10b981;">ACTIVE // STRICT ENFORCEMENT</span>
                    </div>
                </div>

                <div class="err-actions">
                    <a href="<?= base_url('dashboard') ?>" class="err-btn err-btn--primary">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Kembali ke Dashboard Utama</span>
                    </a>

                    <a href="<?= base_url('login') ?>" class="err-btn err-btn--secondary">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login Ulang</span>
                    </a>

                    <button type="button" onclick="history.back()" class="err-btn err-btn--secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="err-footer">
        <span>&copy; <?= date('Y') ?> PT. BESMINDO MATERI SEWATAMA // SECURITY SYSTEM</span>
        <span>ERROR_CODE: 0x403_FORBIDDEN_ACCESS</span>
    </footer>

    <script>
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('simor_theme', next);
            updateThemeLabel();
        }

        function updateThemeLabel() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const label = document.getElementById('themeToggleLabel');
            if (label) {
                label.textContent = current === 'dark' ? 'Mode Hitam' : 'Mode Putih';
            }
        }
        updateThemeLabel();
    </script>
</body>
</html>
