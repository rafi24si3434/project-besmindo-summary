<?php

namespace App\Models;

use CodeIgniter\Model;

class RigModel extends Model
{
    protected $table         = 'rigs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama_rig', 'odr', 'aktif', 'created_at'];

    public function getRigAktif(): array
    {
        return $this->where('aktif', 1)->orderBy('id', 'ASC')->findAll();
    }

    public function getRigById(int $id): ?array
    {
        return $this->find($id);
    }
}
