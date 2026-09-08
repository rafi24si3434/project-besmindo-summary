<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['username', 'password', 'nama', 'created_at'];

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
