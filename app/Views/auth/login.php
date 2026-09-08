<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SIMOR BMS</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: radial-gradient(circle at top, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-slate-900/95 border-2 border-slate-700/80 rounded-3xl shadow-2xl backdrop-blur-2xl p-8 sm:p-10">
        
        <!-- Logo Resmi PT. Besmindo Materi Sewatama -->
        <div class="text-center mb-8">
            <div class="bg-slate-950 p-4 rounded-2xl border-2 border-slate-800 shadow-inner inline-block w-full max-w-sm mx-auto mb-4">
                <img src="<?= base_url('assets/img/logo_besmindo.png') ?>" alt="Logo PT. Besmindo Materi Sewatama" class="max-h-16 w-auto mx-auto object-contain">
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-wide">SIMOR <span class="text-blue-400">BMS</span></h1>
            <p class="text-sm font-bold text-slate-300 mt-1">Sistem Informasi Manajemen Operasi Rig</p>
            <p class="text-xs text-slate-400">PT. Besmindo Materi Sewatama</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/15 border-2 border-rose-500/40 text-rose-200 text-sm font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-xl text-rose-400"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/15 border-2 border-emerald-500/40 text-emerald-200 text-sm font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-bold text-slate-200 uppercase tracking-wider mb-2">Nama Pengguna (Username)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 pointer-events-none text-base">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" value="<?= old('username', 'admin') ?>" required autofocus
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-950 border-2 border-slate-700 rounded-2xl text-white text-base font-semibold focus:outline-none focus:ring-4 focus:ring-blue-500/40 focus:border-blue-500 transition-all placeholder:text-slate-500"
                        placeholder="Ketik username Anda">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-200 uppercase tracking-wider mb-2">Kata Sandi (Password)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 pointer-events-none text-base">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="loginPassword" required
                        class="w-full pl-12 pr-12 py-3.5 bg-slate-950 border-2 border-slate-700 rounded-2xl text-white text-base font-semibold focus:outline-none focus:ring-4 focus:ring-blue-500/40 focus:border-blue-500 transition-all placeholder:text-slate-500"
                        placeholder="Ketik password Anda">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-200 text-base" title="Lihat password">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Masuk Besar & Jelas -->
            <button type="submit"
                class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-base font-extrabold rounded-2xl shadow-xl shadow-blue-500/30 transition duration-200 flex items-center justify-center gap-3 active:scale-[0.98]">
                <span>MASUK KE SISTEM</span>
                <i class="fa-solid fa-arrow-right text-base"></i>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t-2 border-slate-800 text-center">
            <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800 inline-block text-xs font-medium text-slate-300">
                <span class="text-slate-400">Akun Petugas:</span> Username: <strong class="text-blue-400 font-mono">admin</strong> | Password: <strong class="text-blue-400 font-mono">admin123</strong>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('loginPassword');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
