<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d1422">
    <title>Masuk Sistem — SIMOR BMS</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/shadcn-bms.css') ?>">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--background);
        }
        /* Subtle grid pattern background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 48px 48px;
            opacity: .25;
            pointer-events: none;
            z-index: 0;
        }
        /* Glow accent top */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(212,168,32,.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }
        .login-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            position: relative;
            z-index: 1;
        }
        .input-field {
            background: #080e18 !important;
            border: 1px solid var(--input) !important;
            border-radius: var(--radius-sm) !important;
            color: var(--foreground) !important;
            font-size: .875rem;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            width: 100%;
            padding: .625rem 1rem .625rem 2.5rem;
            outline: none;
        }
        .input-field:focus {
            border-color: var(--ring) !important;
            box-shadow: 0 0 0 3px rgba(212,168,32,.15) !important;
        }
        .input-field::placeholder { color: #384e65; }
        .btn-login {
            width: 100%;
            padding: .75rem 1.5rem;
            background: var(--primary);
            color: var(--primary-foreground);
            font-weight: 700;
            font-size: .875rem;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: background 150ms ease, transform 100ms ease, box-shadow 150ms ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,.2), 0 0 0 1px rgba(212,168,32,.2);
            letter-spacing: .02em;
        }
        .btn-login:hover { background: #e0b526; box-shadow: 0 4px 12px rgba(212,168,32,.25); }
        .btn-login:active { transform: scale(.98); }
    </style>
</head>
<body class="shadcn-ui min-h-[100dvh] flex items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-sm mx-auto" style="position:relative;z-index:1">

        <!-- Brand Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-card border border-border mb-3 shadow-lg max-w-full">
                <img src="<?= base_url('assets/img/logo_besmindo.png') ?>" alt="PT. Besmindo Materi Sewatama" class="h-11 sm:h-12 w-auto max-w-[260px] sm:max-w-[280px] object-contain">
            </div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color:var(--foreground);letter-spacing:-.03em">
                SIMOR <span style="color:var(--primary)">BMS</span>
            </h1>
            <p class="text-xs mt-1" style="color:var(--muted-foreground)">Sistem Informasi Manajemen Operasi Rig</p>
        </div>

        <!-- Card -->
        <div class="login-card p-6">

            <div class="mb-5">
                <h2 class="text-sm font-semibold" style="color:var(--foreground)">Masuk ke Sistem</h2>
                <p class="text-xs mt-0.5" style="color:var(--muted-foreground)">Gunakan kredensial akun Anda untuk melanjutkan</p>
            </div>

            <!-- Alerts -->
            <?php if (session()->getFlashdata('error')): ?>
                <div role="alert" class="ui-alert ui-alert--danger mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-sm flex-shrink-0"></i>
                    <span class="text-xs font-medium"><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div role="status" class="ui-alert ui-alert--success mb-4">
                    <i class="fa-solid fa-circle-check text-sm flex-shrink-0"></i>
                    <span class="text-xs font-medium"><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Username -->
                <div class="ui-form-group">
                    <label for="username">Nama Pengguna (Username)</label>
                    <div class="ui-input-wrapper">
                        <i class="ui-input-icon fa-solid fa-user"></i>
                        <input type="text" id="username" name="username"
                            value="<?= old('username', 'admin') ?>"
                            required autofocus autocomplete="username"
                            class="input-field"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                <!-- Password -->
                <div class="ui-form-group">
                    <label for="loginPassword">Kata Sandi (Password)</label>
                    <div class="ui-input-wrapper" style="position:relative">
                        <i class="ui-input-icon fa-solid fa-lock"></i>
                        <input type="password" id="loginPassword" name="password"
                            required autocomplete="current-password"
                            class="input-field" style="padding-right:2.5rem"
                            placeholder="Masukkan password Anda">
                        <button type="button" id="togglePwd"
                            onclick="togglePasswordVisibility()"
                            style="position:absolute;right:.625rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted-foreground);cursor:pointer;min-height:auto;padding:.25rem"
                            title="Tampilkan/Sembunyikan password">
                            <i class="fa-solid fa-eye text-xs" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-1">
                    <button type="submit" class="btn-login">
                        <i class="fa-solid fa-right-to-bracket text-sm"></i>
                        <span>Masuk ke Sistem</span>
                    </button>
                </div>
            </form>

            <!-- Hint -->
            <div class="mt-5 pt-4" style="border-top:1px solid var(--border)">
                <div class="flex items-center gap-2 p-3 rounded-md" style="background:rgba(212,168,32,.06);border:1px solid rgba(212,168,32,.15)">
                    <i class="fa-solid fa-circle-info text-xs flex-shrink-0" style="color:var(--primary)"></i>
                    <p class="text-xs" style="color:var(--muted-foreground)">
                        Username: <code class="font-mono font-bold" style="color:var(--primary)">admin</code>
                        &nbsp;/&nbsp;
                        Password: <code class="font-mono font-bold" style="color:var(--primary)">admin123</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] mt-4" style="color:var(--muted-foreground);position:relative;z-index:1">
            &copy; <?= date('Y') ?> PT. Besmindo Materi Sewatama &mdash; SIMOR v2.0
        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('loginPassword');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
