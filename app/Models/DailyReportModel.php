<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyReportModel extends Model
{
    protected $table         = 'daily_report';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'rig_id', 'lokasi_id', 'no_well', 'tanggal_mulai', 'tanggal_selesai',
        'jarak', 'miru_jam', 'ops_jam', 'total_dt', 'total_jam',
        'status_job', 'remark', 'bulan', 'tahun', 'created_at'
    ];

    public function getByRigBulanTahun(int $rig_id, int $bulan, int $tahun): array
    {
        return $this->db->table('daily_report dr')
            ->select('dr.*, l.nama_lokasi')
            ->join('lokasi l', 'l.id = dr.lokasi_id', 'left')
            ->where('dr.rig_id', $rig_id)
            ->where('dr.bulan', $bulan)
            ->where('dr.tahun', $tahun)
            ->orderBy('dr.no_well', 'ASC')
            ->get()->getResultArray();
    }

    public function getTotalMIRU(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('miru_jam')
            ->first();
        return (float) ($row['miru_jam'] ?? 0);
    }

    public function getTotalOPS(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('ops_jam')
            ->first();
        return (float) ($row['ops_jam'] ?? 0);
    }

    public function getTotalWellJob(int $rig_id, int $bulan, int $tahun): int
    {
        return $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->countAllResults();
    }

    public function getTotalJam(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('total_jam')
            ->first();
        return (float) ($row['total_jam'] ?? 0);
    }

    public function getNextNoWell(int $rig_id, int $bulan, int $tahun): int
    {
        $row = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectMax('no_well')
            ->first();
        return ((int) ($row['no_well'] ?? 0)) + 1;
    }
}
