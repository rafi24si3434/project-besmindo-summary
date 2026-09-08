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
}
