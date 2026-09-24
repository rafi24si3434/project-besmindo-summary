<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'username',
        'modul',
        'action',
        'rig_id',
        'deskripsi',
        'ip_address',
        'created_at'
    ];

    /**
     * Helper statis untuk mencatat log aktivitas dari mana saja
     */
    public static function record(string $modul, string $action, string $deskripsi, ?int $rigId = null): void
    {
        try {
            $session = session();
            $request = service('request');

            $userId = $session ? $session->get('user_id') : null;
            $namaUser = $session ? ($session->get('nama') ?: $session->get('username')) : 'Sistem';
            $ip = $request ? $request->getIPAddress() : '127.0.0.1';

            $model = new self();
            $model->insert([
                'user_id'    => $userId,
                'username'   => $namaUser ?: 'System',
                'modul'      => strtoupper($modul),
                'action'     => strtoupper($action),
                'rig_id'     => $rigId,
                'deskripsi'  => $deskripsi,
                'ip_address' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Log tidak boleh memutus flow utama jika terjadi error kecil
            log_message('error', 'Gagal mencatat audit log: ' . $e->getMessage());
        }
    }

    /**
     * Ambil riwayat log beserta data rig
     */
    public function getLogs(int $limit = 150, ?string $modul = null, ?int $rigId = null): array
    {
        $builder = $this->db->table($this->table . ' a')
            ->select('a.*, r.kode as rig_kode, r.nama_rig')
            ->join('rigs r', 'r.id = a.rig_id', 'left')
            ->orderBy('a.id', 'DESC');

        if (!empty($modul) && $modul !== 'ALL') {
            $builder->where('a.modul', $modul);
        }

        if (!empty($rigId)) {
            $builder->where('a.rig_id', $rigId);
        }

        return $builder->limit($limit)->get()->getResultArray();
    }
}
