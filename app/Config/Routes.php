<?php

use CodeIgniter\Router\RouteCollection;

/**
 * SIMOR - Sistem Informasi Manajemen Operasi Rig BMS
 * Routes Configuration
 */

/** @var RouteCollection $routes */

// ============================================================
// AUTH ROUTES
// ============================================================
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// ============================================================
// PROTECTED ROUTES (require login)
// ============================================================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // ---- MASTER DATA ----
    $routes->group('master', function ($routes) {
        $routes->get('/', 'Master::index');

        // Rig
        $routes->get('rig', 'Master\Rig::index');
        $routes->get('rig/tambah', 'Master\Rig::tambah');
        $routes->post('rig/simpan', 'Master\Rig::simpan');
        $routes->get('rig/edit/(:num)', 'Master\Rig::edit/$1');
        $routes->post('rig/update/(:num)', 'Master\Rig::update/$1');
        $routes->post('rig/hapus/(:num)', 'Master\Rig::hapus/$1');

        // Kategori Downtime
        $routes->get('kategori', 'Master\KategoriDowntime::index');
        $routes->get('kategori/tambah', 'Master\KategoriDowntime::tambah');
        $routes->post('kategori/simpan', 'Master\KategoriDowntime::simpan');
        $routes->get('kategori/edit/(:num)', 'Master\KategoriDowntime::edit/$1');
        $routes->post('kategori/update/(:num)', 'Master\KategoriDowntime::update/$1');
        $routes->post('kategori/hapus/(:num)', 'Master\KategoriDowntime::hapus/$1');

        // Third Party
        $routes->get('third-party', 'Master\ThirdParty::index');
        $routes->get('third-party/tambah', 'Master\ThirdParty::tambah');
        $routes->post('third-party/simpan', 'Master\ThirdParty::simpan');
        $routes->get('third-party/edit/(:num)', 'Master\ThirdParty::edit/$1');
        $routes->post('third-party/update/(:num)', 'Master\ThirdParty::update/$1');
        $routes->post('third-party/hapus/(:num)', 'Master\ThirdParty::hapus/$1');

        // Lokasi / Sumur
        $routes->get('lokasi', 'Master\Lokasi::index');
        $routes->get('lokasi/tambah', 'Master\Lokasi::tambah');
        $routes->post('lokasi/simpan', 'Master\Lokasi::simpan');
        $routes->get('lokasi/edit/(:num)', 'Master\Lokasi::edit/$1');
        $routes->post('lokasi/update/(:num)', 'Master\Lokasi::update/$1');
        $routes->post('lokasi/hapus/(:num)', 'Master\Lokasi::hapus/$1');
    });

    // ---- NPT HARIAN ----
    $routes->group('npt', function ($routes) {
        $routes->get('/', 'Npt::index');
        $routes->get('(:num)/(:num)/(:num)', 'Npt::grid/$1/$2/$3');           // rig_id/bulan/tahun
        $routes->get('(:num)/(:num)', 'Npt::grid/$1/$2');                     // rig_id/bulan (auto default tahun)
        $routes->post('simpan', 'Npt::simpan');
        $routes->get('data/(:num)/(:num)/(:num)', 'Npt::getData/$1/$2/$3');   // AJAX
        $routes->get('get-rig-wells/(:num)/(:num)/(:num)', 'Npt::getRigWells/$1/$2/$3'); // AJAX detail sumur
        $routes->get('get-rig-wells/(:num)/(:num)', 'Npt::getRigWells/$1/$2');           // AJAX detail sumur (default tahun)
    });

    // ---- DAILY REPORT ----
    $routes->group('daily-report', function ($routes) {
        $routes->get('/', 'DailyReport::index');
        $routes->get('(:num)/(:num)/(:num)', 'DailyReport::grid/$1/$2/$3');   // rig_id/bulan/tahun
        $routes->get('tambah/(:num)/(:num)/(:num)', 'DailyReport::tambah/$1/$2/$3');
        $routes->post('simpan', 'DailyReport::simpan');
        $routes->post('simpan-sumur-cepat', 'DailyReport::simpanSumurCepat');
        $routes->get('log-harian', 'DailyReport::logHarianDefault');
        $routes->get('log-harian/(:num)/(:num)/(:num)', 'DailyReport::logHarian/$1/$2/$3');
        $routes->post('simpan-log', 'DailyReport::simpanLog');
        $routes->post('hapus-log/(:num)', 'DailyReport::hapusLog/$1');
        $routes->get('detail/(:num)', 'DailyReport::detail/$1');
        $routes->get('edit/(:num)', 'DailyReport::edit/$1');
        $routes->post('update/(:num)', 'DailyReport::update/$1');
        $routes->post('hapus/(:num)', 'DailyReport::hapus/$1');
    });

    // ---- MONTHLY REPORT ----
    $routes->group('monthly-report', function ($routes) {
        $routes->get('/', 'MonthlyReport::index');
        $routes->get('(:num)/(:num)', 'MonthlyReport::view/$1/$2');            // bulan/tahun
        $routes->post('hitung', 'MonthlyReport::hitung');                      // recalculate
    });

    // ---- REKAP TAHUNAN ----
    $routes->group('rekap-tahunan', function ($routes) {
        $routes->get('/', 'RekapTahunan::index');
        $routes->get('npt', 'RekapTahunan::npt');
        $routes->get('npt/(:num)', 'RekapTahunan::npt/$1');                   // tahun
        $routes->get('(:num)', 'RekapTahunan::view/$1');                       // tahun
        $routes->get('rig/(:num)/(:num)', 'RekapTahunan::viewRig/$1/$2');      // rig_id/tahun
    });

    // ---- EXPORT EXCEL ----
    $routes->group('export', function ($routes) {
        $routes->get('npt/(:num)/(:num)/(:num)', 'Export::npt/$1/$2/$3');              // rig_id/bulan/tahun
        $routes->get('npt-all/(:num)/(:num)', 'Export::nptAll/$1/$2');                 // bulan/tahun
        $routes->get('daily-report/(:num)/(:num)/(:num)', 'Export::dailyReport/$1/$2/$3');
        $routes->get('daily-report-all/(:num)/(:num)', 'Export::dailyReportAll/$1/$2');               // All Rigs in Bulan/Tahun
        $routes->get('monthly-report/(:num)/(:num)', 'Export::monthlyReport/$1/$2');
        $routes->get('rekap-tahunan/(:num)', 'Export::rekapTahunan/$1');
        $routes->get('rekap-npt-tahunan/(:num)', 'Export::rekapNptTahunan/$1');
        $routes->get('bundle-all/(:num)/(:num)', 'Export::bundleAll/$1/$2');                          // Complete All-in-One Package
    });

    // ---- AUDIT TRAIL / RIWAYAT AKTIVITAS ----
    $routes->get('audit-log', 'AuditLog::index');
});
