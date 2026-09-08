<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyReportDtModel extends Model
{
    protected $table         = 'daily_report_dt';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'daily_report_id', 'tanggal', 'kategori_id', 'third_party_id', 'jam'
    ];

    public function getByDailyReportId(int $dailyReportId): array
    {
        return $this->db->table('daily_report_dt drd')
            ->select('drd.*, kd.nama as kategori_nama, kd.tipe, tp.nama as third_party_nama')
            ->join('kategori_downtime kd', 'kd.id = drd.kategori_id', 'left')
            ->join('third_parties tp', 'tp.id = drd.third_party_id', 'left')
            ->where('drd.daily_report_id', $dailyReportId)
            ->get()->getResultArray();
    }
}
