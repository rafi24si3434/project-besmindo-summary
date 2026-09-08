<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimorSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // USERS
        // ============================================================
        $this->db->table('users')->ignore(true)->insert([
            'username'   => 'admin',
            'password'   => password_hash('admin123', PASSWORD_BCRYPT),
            'nama'       => 'Administrator',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // ============================================================
        // RIGS (18 unit)
        // ============================================================
        $rigs = [
            ['kode' => 'BMS#01',  'nama_rig' => 'BMS 01',  'odr' => 108000000],
            ['kode' => 'BMS#02',  'nama_rig' => 'BMS 02',  'odr' => 108000000],
            ['kode' => 'BMS#03',  'nama_rig' => 'BMS 03',  'odr' => 108000000],
            ['kode' => 'BMS#03A', 'nama_rig' => 'BMS 03A', 'odr' => 98000000],
            ['kode' => 'BMS#05',  'nama_rig' => 'BMS 05',  'odr' => 128000000],
            ['kode' => 'BMS#06',  'nama_rig' => 'BMS 06',  'odr' => 123000000],
            ['kode' => 'BMS#07',  'nama_rig' => 'BMS 07',  'odr' => 86197000],
            ['kode' => 'BMS#08',  'nama_rig' => 'BMS 08',  'odr' => 105000000],
            ['kode' => 'BMS#09',  'nama_rig' => 'BMS 09',  'odr' => 87904000],
            ['kode' => 'BMS#10',  'nama_rig' => 'BMS 10',  'odr' => 141000000],
            ['kode' => 'BMS#11',  'nama_rig' => 'BMS 11',  'odr' => 141000000],
            ['kode' => 'BMS#15',  'nama_rig' => 'BMS 15',  'odr' => 86197000],
            ['kode' => 'BMS#16',  'nama_rig' => 'BMS 16',  'odr' => 141000000],
            ['kode' => 'BMS#17',  'nama_rig' => 'BMS 17',  'odr' => 103000000],
            ['kode' => 'BMS#18',  'nama_rig' => 'BMS 18',  'odr' => 78197000],
            ['kode' => 'BMS#19',  'nama_rig' => 'BMS 19',  'odr' => 81500000],
            ['kode' => 'BMS#20',  'nama_rig' => 'BMS 20',  'odr' => 135000000],
            ['kode' => 'BMS#21',  'nama_rig' => 'BMS 21',  'odr' => 86200000],
        ];
        foreach ($rigs as $rig) {
            $rig['aktif']      = 1;
            $rig['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('rigs')->ignore(true)->insert($rig);
        }

        // ============================================================
        // KATEGORI DOWNTIME
        // ============================================================
        $kategori = [
            // UNPAID
            ['nama' => 'Repaire Rig & Equipment',   'tipe' => 'UNPAID', 'urutan' => 1],
            ['nama' => 'Personnel',                  'tipe' => 'UNPAID', 'urutan' => 2],
            // SBWC
            ['nama' => 'SWA Rain',                   'tipe' => 'SBWC',   'urutan' => 3],
            ['nama' => 'Dry Road & Public Road',      'tipe' => 'SBWC',   'urutan' => 4],
            ['nama' => 'Dry Well Pad',               'tipe' => 'SBWC',   'urutan' => 5],
            ['nama' => 'WO Daylight',                'tipe' => 'SBWC',   'urutan' => 6],
            ['nama' => 'Perfo Job',                  'tipe' => 'SBWC',   'urutan' => 7],
            ['nama' => 'PHR Well & Accessories',     'tipe' => 'SBWC',   'urutan' => 8],
            ['nama' => 'CPI Rig & Equipment',        'tipe' => 'SBWC',   'urutan' => 9],
            ['nama' => '3rd Party',                  'tipe' => 'SBWC',   'urutan' => 10],
            ['nama' => 'SHARING Units Trans',        'tipe' => 'SBWC',   'urutan' => 11],
            ['nama' => 'Foam Unit',                  'tipe' => 'SBWC',   'urutan' => 12],
            ['nama' => 'PHR Operator',               'tipe' => 'SBWC',   'urutan' => 13],
            ['nama' => 'CE/PE',                      'tipe' => 'SBWC',   'urutan' => 14],
            ['nama' => 'Idul Fitri & Pilkada',       'tipe' => 'SBWC',   'urutan' => 15],
            ['nama' => 'WO PLN',                     'tipe' => 'SBWC',   'urutan' => 16],
            ['nama' => 'WO OMS',                     'tipe' => 'SBWC',   'urutan' => 17],
            ['nama' => 'WO Decision from LSC',       'tipe' => 'SBWC',   'urutan' => 18],
        ];
        foreach ($kategori as $k) {
            $k['aktif'] = 1;
            $this->db->table('kategori_downtime')->ignore(true)->insert($k);
        }

        // ============================================================
        // THIRD PARTIES
        // ============================================================
        $thirdParties = [
            'BHI', 'HLS', 'WI/BUKAKA', 'EJP', 'HALCO', 'SCHL', 'MGA', 'SGN', 'PESI',
            'PT UNISAT NUSANTARA', 'PT. PCM', 'COSL',
        ];
        foreach ($thirdParties as $tp) {
            $this->db->table('third_parties')->ignore(true)->insert(['nama' => $tp, 'aktif' => 1]);
        }
    }
}
