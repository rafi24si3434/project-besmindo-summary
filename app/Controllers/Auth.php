<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ActivityLogModel;
use Config\Services;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('auth/login');
    }

    public function login()
    {
        $throttler = Services::throttler();
        $ipAddress = (string)$this->request->getIPAddress();
        $throttleKey = 'login_attempt_' . md5($ipAddress);

        // Proteksi Anti-Brute-Force: Maksimal 5 percobaan login gagal per 15 menit (900 detik) per IP
        if ($throttler->check($throttleKey, 5, 900) === false) {
            $waitSec = $throttler->getTokenTime();
            $waitMin = max(1, (int)ceil($waitSec / 60));
            return redirect()->back()->withInput()->with(
                'error',
                "Terlalu banyak percobaan login yang gagal dari IP Anda ({$ipAddress}). Demi keamanan, akses dikunci sementara selama {$waitMin} menit."
            );
        }

        $userModel = new UserModel();
        $username  = trim((string)$this->request->getPost('username'));
        $password  = (string)$this->request->getPost('password');

        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            ActivityLogModel::record(
                'AUTH',
                'LOGIN_GAGAL',
                "Percobaan login gagal untuk username '{$username}' dari IP {$ipAddress}."
            );
            return redirect()->back()->withInput()->with('error', 'Username atau password yang Anda masukkan salah!');
        }

        // Regenerate Session ID untuk mencegah serangan Session Fixation
        session()->regenerate(true);

        $isDefaultPassword = password_verify('admin123', $user['password']);

        session()->set([
            'is_logged_in'        => true,
            'user_id'             => $user['id'],
            'username'            => $user['username'],
            'nama'                => $user['nama'],
            'is_default_password' => $isDefaultPassword,
        ]);

        ActivityLogModel::record(
            'AUTH',
            'LOGIN_BERHASIL',
            "User '{$user['username']}' ({$user['nama']}) berhasil login dari IP {$ipAddress}."
        );

        $welcomeMsg = 'Selamat datang kembali, ' . esc($user['nama']) . '.';
        if ($isDefaultPassword) {
            $welcomeMsg .= ' ⚠️ Anda masih menggunakan password standar (admin123). Segera ubah password Anda melalui menu Ubah Password.';
        }

        return redirect()->to(base_url('dashboard'))->with('success', $welcomeMsg);
    }

    /**
     * Halaman Pengaturan Keamanan & Ubah Password Akun
     */
    public function ubahPassword()
    {
        $userId = (int)session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title'         => 'Keamanan Akun & Ubah Password',
            'page_title'    => 'Pengaturan Keamanan Akun & Password',
            'page_subtitle' => 'Perbarui password akses dan identitas operator untuk mengamankan sistem di lingkungan produksi',
            'user'          => $user,
            'isDefaultPass' => password_verify('admin123', $user['password']),
        ];

        return view('auth/ubah_password', $data);
    }

    /**
     * Proses Simpan Password Baru & Identitas Akun
     */
    public function simpanPassword()
    {
        $userId = (int)session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $nama               = trim((string)$this->request->getPost('nama'));
        $username           = trim((string)$this->request->getPost('username'));
        $passwordLama       = (string)$this->request->getPost('password_lama');
        $passwordBaru       = (string)$this->request->getPost('password_baru');
        $konfirmasiPassword = (string)$this->request->getPost('konfirmasi_password');

        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Password lama yang Anda masukkan tidak sesuai!');
        }

        if (strlen($passwordBaru) < 6) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Password baru minimal harus terdiri dari 6 karakter!');
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Konfirmasi password baru tidak cocok!');
        }

        if ($passwordBaru === 'admin123') {
            return redirect()->back()->withInput()->with('error', 'Gagal: Demi keamanan server hosting, dilarang menggunakan password standar "admin123"!');
        }

        $updateData = [
            'password' => password_hash($passwordBaru, PASSWORD_BCRYPT),
        ];

        if ($nama !== '' && strlen($nama) >= 2) {
            $updateData['nama'] = $nama;
        }
        if ($username !== '' && strlen($username) >= 3 && $username !== $user['username']) {
            // Cek apakah username sudah dipakai user lain
            $existingUser = $userModel->findByUsername($username);
            if ($existingUser && (int)$existingUser['id'] !== $userId) {
                return redirect()->back()->withInput()->with('error', "Gagal: Username '{$username}' sudah digunakan oleh akun lain.");
            }
            $updateData['username'] = $username;
        }

        $userModel->update($userId, $updateData);

        // Perbarui session & regenerate session ID
        session()->regenerate(true);
        session()->set([
            'username'            => $updateData['username'] ?? $user['username'],
            'nama'                => $updateData['nama'] ?? $user['nama'],
            'is_default_password' => false,
        ]);

        ActivityLogModel::record(
            'AUTH',
            'UBAH_PASSWORD',
            "User '{$user['username']}' berhasil memperbarui password keamanan akun."
        );

        return redirect()->back()->with('success', 'Password dan keamanan akun Anda berhasil diperbarui! Password baru langsung aktif.');
    }

    public function logout()
    {
        $username = session()->get('username') ?? 'User';
        ActivityLogModel::record('AUTH', 'LOGOUT', "User '{$username}' keluar (logout) dari sistem.");
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout dengan aman.');
    }
}
