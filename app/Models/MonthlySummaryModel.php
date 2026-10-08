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
        $nominal_jam = $days * 24.0;

        $unpaid_jam = $nptModel->getTotalUnpaid($rig_id, $bulan, $tahun);
        $sbwc_jam = $nptModel->getTotalSBWC($rig_id, $bulan, $tahun);
        $total_miru = $dailyReportModel->getTotalMIRU($rig_id, $bulan, $tahun);
        $total_ops = $dailyReportModel->getTotalOPS($rig_id, $bulan, $tahun);
        $total_well_job = $dailyReportModel->getTotalWellJob($rig_id, $bulan, $tahun);
        $reported_jam = $dailyReportModel->getTotalJam($rig_id, $bulan, $tahun);

        // Jika rig beroperasi parsial bulan (misal BMS#15 = 192 jam, BMS#18 = 120 jam)
        $total_jam = $reported_jam > 0 ? $reported_jam : $nominal_jam;

        $existing = $this->where('rig_id', $rig_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // 1. Ekstrak Schedule MTC dari remark JSON atau catatan perawatan
        $schedule_mtc = 0.0;
        if ($existing && !empty($existing['remark'])) {
            $decoded = json_decode((string)$existing['remark'], true);
            if (is_array($decoded)) {
                if (isset($decoded['schedule_mtc'])) {
                    $schedule_mtc = (float)$decoded['schedule_mtc'];
                }
                // Jika schedule_mtc bernilai 0 di form, periksa otomatis apakah ada di catatan notes
                if ($schedule_mtc <= 0 && !empty($decoded['notes']) && is_array($decoded['notes'])) {
                    foreach ($decoded['notes'] as $note) {
                        $txt = (string)($note['isi'] ?? '');
                        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:hr|hrs|jam)\b.*?preventive\s+maintenance/i', $txt, $m)) {
                            $schedule_mtc += (float)str_replace(',', '.', $m[1]);
                        }
                    }
                }
            }
        }

        // 2. Jika data monthly_summary untuk periode ini sudah ada dan memiliki angka operasi
        // (misalnya diimpor dari dokumen resmi SUMMARY OPERATION), jangan timpa jika belum ada log sama sekali
        if ($existing && (float)($existing['revenue_actual'] ?? 0) > 0 && $total_well_job == 0 && $total_miru == 0 && $total_ops == 0) {
            return $existing;
        }

        // 3. FORMULA DEFINITIF OPERASIONAL EXCEL:
        // Reliability = (Total Jam - Unpaid Jam) / Total Jam
        $reliability = $total_jam > 0 ? max(0, ($total_jam - $unpaid_jam) / $total_jam) : 0;

        // Availability = (Total Jam - (Unpaid Jam + Schedule MTC)) / Total Jam
        $availability = $total_jam > 0 ? max(0, ($total_jam - ($unpaid_jam + $schedule_mtc)) / $total_jam) : 0;

        // Utilization = (Total Jam - (SBWC Jam + Unpaid Jam + Schedule MTC)) / Total Jam
        // atau ((Total MIRU + Total OPS) - Schedule MTC) / Total Jam
        $utilization = $total_jam > 0 ? max(0, ($total_jam - ($sbwc_jam + $unpaid_jam + $schedule_mtc)) / $total_jam) : 0;

        // Average MIRU = Total MIRU / Total Well Job
        $avg_miru = $total_well_job > 0 ? ($total_miru / $total_well_job) : 0;

        // Average Cycle Time = (Total Jam - (Total Well Job - 1)) / Total Well Job
        $avg_cycle_time = $total_well_job > 0 ? max(0, ($total_jam - ($total_well_job - 1)) / $total_well_job) : 0;

        // 4. INCENTIVE TARGET (Target Revenue):
        // Target Ratio: 92% untuk BMS#01..09, BMS#16; 94% untuk BMS#10, 11, 15, 17..21
        $cleanRigCode = strtoupper(str_replace([' ', '-', '#'], '', (string)($rig['kode'] ?? '')));
        $target94List = ['BMS10', 'BMS11', 'BMS15', 'BMS17', 'BMS18', 'BMS19', 'BMS20', 'BMS21'];
        $targetRatio = in_array($cleanRigCode, $target94List, true) ? 0.94 : 0.92;
        $revenue_target = (int)round($odr * ($total_jam / 24.0) * $targetRatio);

        // 5. REVENUE ACTUAL:
        // Tarif Operasional:
        // OPS = ODR / 24 (100%)
        // MIRU = Rate OPS * 0.75 (75%)
        // SBWC 75% = Rate OPS * 0.75 (SWA Rain & Dry Road/Public Issue: kategori ID 3 & 4)
        // SBWC 65% = Rate OPS * 0.65 (Kategori SBWC lainnya: Pad, Daylight, Perfo, 3rd Party, dsb)
        // UNPAID = 0 (0%)
        $rate_ops = $odr / 24.0;
        $rate_miru = $rate_ops * 0.75;
        $rate_sbwc75 = $rate_ops * 0.75;
        $rate_sbwc65 = $rate_ops * 0.65;

        $sbwc75_jam = $nptModel->getTotalSBWC75($rig_id, $bulan, $tahun);
        $sbwc65_jam = $nptModel->getTotalSBWC65($rig_id, $bulan, $tahun);

        // Fallback jika belum masuk ke npt_harian namun tercatat di daily_report_dt
        if ($sbwc_jam > 0 && ($sbwc75_jam + $sbwc65_jam) == 0) {
            $rowDt = $this->db->table('daily_report_dt dt')
                ->select('
                    SUM(CASE WHEN kd.tipe = "SBWC" AND (kd.id IN (3,4) OR LOWER(kd.nama) LIKE "%rain%" OR LOWER(kd.nama) LIKE "%dry road%") THEN dt.jam ELSE 0 END) as sbwc75,
                    SUM(CASE WHEN kd.tipe = "SBWC" AND kd.id NOT IN (3,4) AND LOWER(kd.nama) NOT LIKE "%rain%" AND LOWER(kd.nama) NOT LIKE "%dry road%" THEN dt.jam ELSE 0 END) as sbwc65
                ')
                ->join('daily_report dr', 'dr.id = dt.daily_report_id')
                ->join('kategori_downtime kd', 'kd.id = dt.kategori_id')
                ->where('dr.rig_id', $rig_id)
                ->where('dr.bulan', $bulan)
                ->where('dr.tahun', $tahun)
                ->get()->getRowArray();
            $sbwc75_jam = (float)($rowDt['sbwc75'] ?? 0);
            $sbwc65_jam = (float)($rowDt['sbwc65'] ?? 0);
        }

        // Fallback jika tidak ada breakdown sama sekali
        if ($sbwc_jam > 0 && ($sbwc75_jam + $sbwc65_jam) == 0) {
            $sbwc65_jam = $sbwc_jam;
        }

        $revenue_actual = (int)round(
            ($total_ops * $rate_ops) +
            ($total_miru * $rate_miru) +
            ($sbwc75_jam * $rate_sbwc75) +
            ($sbwc65_jam * $rate_sbwc65)
        );

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

        // Pertahankan remark JSON (termasuk notes dan schedule_mtc)
        if ($existing && !empty($existing['remark'])) {
            $data['remark'] = $existing['remark'];
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
