<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table         = 'lokasi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_lokasi', 'aktif'];

    public function getAktif(): array
    {
        return $this->where('aktif', 1)->orderBy('nama_lokasi', 'ASC')->findAll();
    }

    /**
     * Cari lokasi berdasarkan ID atau Nama; jika belum ada di Master Data, otomatis buat baru & kembalikan ID-nya.
     */
    public function findOrCreate(string $namaInput, int $lokasiId = 0): int
    {
        $namaClean = trim($namaInput);

        // Jika ID sudah dipilih dan namanya tidak diubah ke nama baru yang berbeda
        if ($lokasiId > 0) {
            $existingById = $this->find($lokasiId);
            if ($existingById) {
                if ($namaClean === '' || strcasecmp(trim($existingById['nama_lokasi']), $namaClean) === 0) {
                    return (int)$existingById['id'];
                }
            }
        }

        if ($namaClean === '') {
            return $lokasiId > 0 ? $lokasiId : 0;
        }

        // Cek apakah nama lokasi sudah ada di database (case-insensitive)
        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->where('LOWER(TRIM(nama_lokasi))', strtolower($namaClean))
            ->get()
            ->getRowArray();

        if ($row) {
            if ((int)($row['aktif'] ?? 1) === 0) {
                $this->update($row['id'], ['aktif' => 1]);
            }
            return (int)$row['id'];
        }

        // Otomatis simpan ke Master Data Lokasi
        $newId = $this->insert([
            'nama_lokasi' => strtoupper($namaClean),
            'aktif'       => 1,
        ]);

        return (int)$newId;
    }
}
