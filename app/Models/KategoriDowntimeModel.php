<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriDowntimeModel extends Model
{
    protected $table         = 'kategori_downtime';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama', 'tipe', 'urutan', 'aktif'];

    public function getKategoriAktif(): array
    {
        return $this->where('aktif', 1)->orderBy('urutan', 'ASC')->findAll();
    }

    public function getByTipe(string $tipe): array
    {
        return $this->where('aktif', 1)->where('tipe', $tipe)->orderBy('urutan', 'ASC')->findAll();
    }

    public function getAllOrdered(): array
    {
        return $this->orderBy('urutan', 'ASC')->findAll();
    }
}
