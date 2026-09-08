<?php

namespace App\Models;

use CodeIgniter\Model;

class ThirdPartyModel extends Model
{
    protected $table         = 'third_parties';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama', 'aktif'];

    public function getAktif(): array
    {
        return $this->where('aktif', 1)->orderBy('nama', 'ASC')->findAll();
    }
}
