<?php

namespace App\Models;

use CodeIgniter\Model;

class MonthlySummaryModel extends Model
{
    protected $table         = 'monthly_summary';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'rig_id', 'bulan', 'tahun', 'reliability', 'availability', 'utilization',
        'total_miru', 'total_ops', 'avg_miru', 'avg_cycle_time', 'total_well_job',
        'sbwc_jam', 'unpaid_jam', 'revenue_target', 'revenue_actual', 'total_jam', 'remark'
    ];

    public function getByBulanTahun(int $bulan, int $tahun): array
    {
        return $this->db->table('rigs r')
            ->select('r.id as rig_id, r.kode, r.nama_rig, r.odr, ms.*')
            ->join('monthly_summary ms', 'ms.rig_id = r.id AND ms.bulan = ' . (int)$bulan . ' AND ms.tahun = ' . (int)$tahun, 'left')
            ->where('r.aktif', 1)
            ->orderBy('r.id', 'ASC')
            ->get()->getResultArray();
    }

    public function getByRigTahun(int $rig_id, int $tahun): array
    {
        return $this->where('rig_id', $rig_id)
            ->where('tahun', $tahun)
            ->orderBy('bulan', 'ASC')
            ->findAll();
    }

    public function hitungDanSimpan(int $rig_id, int $bulan, int $tahun): array
    {
        $rigModel = new RigModel();
        $nptModel = new NptModel();
        $dailyReportModel = new DailyReportModel();

        $rig = $rigModel->find($rig_id);
        $odr = (float)($rig['odr'] ?? 0);

        $days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $total_jam = $days * 24.0;

        $unpaid_jam = $nptModel->getTotalUnpaid($rig_id, $bulan, $tahun);
        $sbwc_jam = $nptModel->getTotalSBWC($rig_id, $bulan, $tahun);
        $total_miru = $dailyReportModel->getTotalMIRU($rig_id, $bulan, $tahun);
        $total_ops = $dailyReportModel->getTotalOPS($rig_id, $bulan, $tahun);
        $total_well_job = $dailyReportModel->getTotalWellJob($rig_id, $bulan, $tahun);

        $reliability = $total_jam > 0 ? max(0, 1.0 - ($unpaid_jam / $total_jam)) : 0;
        $availability = $total_jam > 0 ? max(0, 1.0 - ($unpaid_jam / $total_jam)) : 0;
        $utilization = $total_jam > 0 ? ($total_ops / $total_jam) : 0;
        $avg_miru = $total_well_job > 0 ? ($total_miru / $total_well_job) : 0;
        $avg_cycle_time = $total_well_job > 0 ? ($total_ops / $total_well_job) : 0;
        $revenue_target = (int)($odr * $days);
        
        $productive_jam = max(0, $total_jam - $sbwc_jam - $unpaid_jam);
        $revenue_actual = (int)($odr * ($productive_jam / 24.0));

        $data = [
            'rig_id'         => $rig_id,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'reliability'    => round($reliability, 6),
            'availability'   => round($availability, 6),
            'utilization'    => round($utilization, 6),
            'total_miru'     => $total_miru,
            'total_ops'      => $total_ops,
            'avg_miru'       => round($avg_miru, 2),
            'avg_cycle_time' => round($avg_cycle_time, 2),
            'total_well_job' => $total_well_job,
            'sbwc_jam'       => $sbwc_jam,
            'unpaid_jam'     => $unpaid_jam,
            'revenue_target' => $revenue_target,
            'revenue_actual' => $revenue_actual,
            'total_jam'      => $total_jam,
        ];

        $existing = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // Jika data monthly_summary untuk periode ini sudah ada dan memiliki angka operasi
        // (misalnya diimpor dari dokumen resmi SUMMARY OPERATION), jangan timpa dengan default 0 jam.
        if ($existing && (float)($existing['revenue_actual'] ?? 0) > 0 && $total_well_job == 0 && $total_miru == 0 && $total_ops == 0) {
            return $existing;
        }

        if ($existing) {
            $this->update($existing['id'], $data);
            $data['id'] = $existing['id'];
        } else {
            $data['id'] = $this->insert($data);
        }

        return $data;
    }

    public function upsertSummary(array $data)
    {
        $existing = $this->where('rig_id', (int)$data['rig_id'])
                         ->where('bulan', (int)$data['bulan'])
                         ->where('tahun', (int)$data['tahun'])
                         ->first();

        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        } else {
            return $this->insert($data);
        }
    }
}
