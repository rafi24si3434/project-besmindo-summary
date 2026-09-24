<?php

namespace App\Models;

use CodeIgniter\Model;

class NptModel extends Model
{
    protected $table         = 'npt_harian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['rig_id', 'lokasi_id', 'tanggal', 'kategori_id', 'third_party_id', 'jam', 'remark', 'created_at'];

    /**
     * Ambil data NPT per rig per bulan - return array [tanggal][kategori_id][third_party_id|0] = jam
     */
    public function getByRigBulanTahun(int $rig_id, int $bulan, int $tahun): array
    {
        $rows = $this->db->table('npt_harian nh')
            ->select('nh.tanggal, nh.kategori_id, nh.third_party_id, nh.jam, nh.remark')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $tgl  = $row['tanggal'];
            $kid  = $row['kategori_id'];
            $tpid = $row['third_party_id'] ?? 0;
            $result[$tgl][$kid][$tpid] = [
                'jam'    => (float) $row['jam'],
                'remark' => $row['remark'],
            ];
        }
        return $result;
    }

    /**
     * Total jam per kategori untuk satu rig satu bulan
     */
    public function getTotalByKategori(int $rig_id, int $bulan, int $tahun): array
    {
        $rows = $this->db->table('npt_harian')
            ->select('kategori_id, SUM(jam) as total_jam')
            ->where('rig_id', $rig_id)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->groupBy('kategori_id')
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['kategori_id']] = (float) $row['total_jam'];
        }
        return $result;
    }

    /**
     * Total jam UNPAID untuk satu rig satu bulan
     */
    public function getTotalUnpaid(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->db->table('npt_harian nh')
            ->select('SUM(nh.jam) as total')
            ->join('kategori_downtime kd', 'kd.id = nh.kategori_id')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->where('kd.tipe', 'UNPAID')
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total jam SBWC untuk satu rig satu bulan
     */
    public function getTotalSBWC(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->db->table('npt_harian nh')
            ->select('SUM(nh.jam) as total')
            ->join('kategori_downtime kd', 'kd.id = nh.kategori_id')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->where('kd.tipe', 'SBWC')
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total downtime (UNPAID + SBWC) untuk satu rig satu bulan
     */
    public function getTotalDowntime(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->db->table('npt_harian')
            ->select('SUM(jam) as total')
            ->where('rig_id', $rig_id)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Upsert - insert jika belum ada, update jika sudah ada
     */
    public function upsertNpt(int $rig_id, string $tanggal, int $kategori_id, ?int $third_party_id, float $jam, ?string $remark): bool
    {
        $tpVal = $third_party_id ?? 'NULL';

        // Cek existing
        $builder = $this->db->table('npt_harian')
            ->where('rig_id', $rig_id)
            ->where('tanggal', $tanggal)
            ->where('kategori_id', $kategori_id);

        if ($third_party_id === null) {
            $builder->where('third_party_id IS NULL', null, false);
        } else {
            $builder->where('third_party_id', $third_party_id);
        }

        $existing = $builder->get()->getRowArray();

        if ($jam <= 0 && empty($remark)) {
            // Hapus jika jam = 0
            if ($existing) {
                $this->db->table('npt_harian')->where('id', $existing['id'])->delete();
            }
            return true;
        }

        $data = [
            'rig_id'         => $rig_id,
            'tanggal'        => $tanggal,
            'kategori_id'    => $kategori_id,
            'third_party_id' => $third_party_id,
            'jam'            => $jam,
            'remark'         => $remark,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return $this->db->table('npt_harian')->where('id', $existing['id'])->update(['jam' => $jam, 'remark' => $remark]);
        } else {
            return $this->db->table('npt_harian')->insert($data);
        }
    }

    /**
     * Hapus satu baris NPT
     */
    public function hapusRow(int $id): bool
    {
        return $this->db->table('npt_harian')->where('id', $id)->delete();
    }

    /**
     * Summary semua rig untuk NPT ALL RIG report - return [rig_id][kategori_id] = total_jam
     */
    public function getSummaryAllRig(int $bulan, int $tahun): array
    {
        $rows = $this->db->table('npt_harian')
            ->select('rig_id, kategori_id, SUM(jam) as total_jam')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->groupBy(['rig_id', 'kategori_id'])
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['rig_id']][$row['kategori_id']] = (float) $row['total_jam'];
        }
        return $result;
    }

    /**
     * Total downtime per hari untuk satu rig satu bulan (untuk validasi maks 24 jam/hari)
     */
    public function getTotalPerHari(int $rig_id, int $bulan, int $tahun): array
    {
        $rows = $this->db->table('npt_harian')
            ->select('tanggal, SUM(jam) as total_jam')
            ->where('rig_id', $rig_id)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->groupBy('tanggal')
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['tanggal']] = (float) $row['total_jam'];
        }
        return $result;
    }

    /**
     * Data lengkap NPT untuk export (join kategori & third_party)
     */
    public function getForExport(int $rig_id, int $bulan, int $tahun): array
    {
        return $this->db->table('npt_harian nh')
            ->select('nh.tanggal, nh.jam, nh.remark, kd.nama as kategori_nama, kd.tipe, tp.nama as third_party_nama')
            ->join('kategori_downtime kd', 'kd.id = nh.kategori_id')
            ->join('third_parties tp', 'tp.id = nh.third_party_id', 'left')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->orderBy('nh.tanggal', 'ASC')
            ->orderBy('kd.urutan', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * SINKRONISASI DARI DAILY REPORT LOG KE NPT HARIAN
     * Setiap baris daily_report_log di-sinkronkan ke pos NPT Harian.
     * Remark UNPAID (atau remark_npt jika berisi unpaid) otomatis disematkan ke pos UNPAID di NPT.
     */
    public function syncFromDailyLog(int $rig_id, array $logRow): void
    {
        $tanggal = $logRow['tanggal'];
        $remarkUnpaid = !empty($logRow['remark_unpaid']) ? trim($logRow['remark_unpaid']) : '';
        if (empty($remarkUnpaid) && !empty($logRow['remark_npt'])) {
            // Fallback jika remark_npt mengandung info unpaid/pump/rig dsb
            $remarkUnpaid = trim($logRow['remark_npt']);
        }
        $remarkGeneral = !empty($logRow['remark_npt']) ? trim($logRow['remark_npt']) : null;

        // Peta Kolom Log ke ID Kategori Downtime
        $mapping = [
            1  => [(float)($logRow['dt_rig'] ?? 0), $remarkUnpaid],        // Repaire Rig & Equipment (UNPAID)
            2  => [(float)($logRow['dt_tool'] ?? 0), $remarkUnpaid],       // Personnel / Tool (UNPAID)
            3  => [(float)($logRow['dt_rain'] ?? 0), $remarkGeneral],      // SWA Rain
            4  => [(float)($logRow['dt_dry_road'] ?? 0), $remarkGeneral],  // Dry Road & Public Road
            5  => [(float)($logRow['dt_dry_pad'] ?? 0), $remarkGeneral],   // Dry Well Pad
            6  => [(float)($logRow['dt_daylight'] ?? 0), $remarkGeneral],  // WO Daylight
            8  => [(float)($logRow['dt_phr_well'] ?? 0), $remarkGeneral],  // PHR Well & Accessories
            11 => [(float)($logRow['dt_trans'] ?? 0), $remarkGeneral],     // SHARING Units Trans
            12 => [(float)($logRow['dt_foam'] ?? 0), $remarkGeneral],      // Foam Unit
            13 => [(float)($logRow['dt_phr_op'] ?? 0), $remarkGeneral],    // PHR Operator
            14 => [(float)($logRow['dt_ce_pe'] ?? 0), $remarkGeneral],     // CE/PE
            15 => [(float)($logRow['dt_shutdown'] ?? 0), $remarkGeneral],  // Shutdown / Idul Fitri
        ];

        foreach ($mapping as $kategoriId => [$jam, $remark]) {
            if ($jam > 0 || !empty($remark)) {
                $this->upsertNpt($rig_id, $tanggal, $kategoriId, null, $jam, $jam > 0 ? $remark : null);
            }
        }

        // Pos 3rd Party (ID 10):
        $tpJamArray = $logRow['tp_jam'] ?? [];
        $jam3rdPartyTotal = (float)($logRow['dt_3rd_party'] ?? 0);
        
        if (!empty($tpJamArray)) {
            // Hapus yang null (kalau ada)
            $this->db->table('npt_harian')
                ->where('rig_id', $rig_id)
                ->where('tanggal', $tanggal)
                ->where('kategori_id', 10)
                ->where('third_party_id IS NULL')
                ->delete();

            // Insert per third party breakdown
            foreach ($tpJamArray as $tpId => $jam) {
                $jam = (float)$jam;
                if ($jam > 0) {
                    $this->upsertNpt($rig_id, $tanggal, 10, (int)$tpId, $jam, $remarkGeneral);
                } else {
                    // Hapus jika di-nol-kan
                    $this->hapusRow($rig_id, $tanggal, 10, (int)$tpId);
                }
            }
        } else if ($jam3rdPartyTotal > 0) {
            // Fallback (misal diisi dari sumber lain tapi tp_jam kosong)
            $existing3rd = $this->db->table('npt_harian')
                ->where('rig_id', $rig_id)
                ->where('tanggal', $tanggal)
                ->where('kategori_id', 10)
                ->where('third_party_id IS NOT NULL')
                ->get()->getResultArray();

            if (empty($existing3rd)) {
                $this->upsertNpt($rig_id, $tanggal, 10, null, $jam3rdPartyTotal, $remarkGeneral);
            }
        } else {
            // Jika dt_3rd_party = 0 dan tp_jam kosong, hapus semua
            $this->db->table('npt_harian')
                ->where('rig_id', $rig_id)
                ->where('tanggal', $tanggal)
                ->where('kategori_id', 10)
                ->delete();
        }
    }

    /**
     * SINKRONISASI DARI DAILY REPORT RINGKAS KE NPT HARIAN
     * Untuk sumur yang tidak memiliki rincian daily_report_log
     */
    public function syncFromDailyReportSummary(int $rig_id, array $report, array $dtInputs): void
    {
        $tanggal = !empty($report['tanggal_mulai']) 
            ? $report['tanggal_mulai'] 
            : sprintf('%04d-%02d-01', $report['tahun'], $report['bulan']);

        $remarkUnpaid = !empty($report['remark_unpaid']) ? trim($report['remark_unpaid']) : '';
        $remarkUmum = !empty($report['remark']) ? trim($report['remark']) : null;

        foreach ($dtInputs as $kId => $jam) {
            $jamVal = (float)$jam;
            $kategoriId = (int)$kId;
            $isUnpaid = in_array($kategoriId, [1, 2]);
            $remarkToUse = $isUnpaid ? ($remarkUnpaid ?: $remarkUmum) : $remarkUmum;

            if ($jamVal > 0) {
                $this->upsertNpt($rig_id, $tanggal, $kategoriId, null, $jamVal, $remarkToUse);
            }
        }
    }

    /**
     * SINKRONISASI BALIK DARI NPT KE DAILY REPORT DT & LOG
     * Jika user mengedit di NPT Harian, otomatis update daily_report_dt & daily_report_log yang cocok
     */
    public function syncNptBackToDaily(int $rig_id, string $tanggal, int $kategori_id, float $jam, ?string $remark = null): void
    {
        // 1. Cek apakah ada daily_report_log untuk rig dan tanggal ini
        $logRow = $this->db->table('daily_report_log drl')
            ->select('drl.*, dr.rig_id')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rig_id)
            ->where('drl.tanggal', $tanggal)
            ->get()->getRowArray();

        $kategoriColMap = [
            1  => 'dt_rig',
            2  => 'dt_tool',
            3  => 'dt_rain',
            4  => 'dt_dry_road',
            5  => 'dt_dry_pad',
            6  => 'dt_daylight',
            8  => 'dt_phr_well',
            10 => 'dt_3rd_party',
            11 => 'dt_trans',
            12 => 'dt_foam',
            13 => 'dt_phr_op',
            14 => 'dt_ce_pe',
            15 => 'dt_shutdown',
        ];

        if ($logRow && isset($kategoriColMap[$kategori_id])) {
            $colName = $kategoriColMap[$kategori_id];
            $updateLog = [$colName => $jam];

            // Jika UNPAID dan ada remark, update remark_unpaid
            if (in_array($kategori_id, [1, 2]) && !empty($remark)) {
                $updateLog['remark_unpaid'] = $remark;
            }

            // Recalculate total_dt dan total_hrs pada log
            $totalDt = 0;
            foreach ($kategoriColMap as $c) {
                $val = ($c === $colName) ? $jam : (float)($logRow[$c] ?? 0);
                $totalDt += $val;
            }
            $miru = (float)($logRow['miru_jam'] ?? 0);
            $ops  = (float)($logRow['ops_jam'] ?? 0);
            $updateLog['total_dt']  = $totalDt;
            $updateLog['total_hrs'] = $miru + $ops + $totalDt;

            $this->db->table('daily_report_log')->where('id', $logRow['id'])->update($updateLog);

            // Perbarui subtotal pada daily_report parent
            $parentReportId = $logRow['daily_report_id'];
            $allLogs = $this->db->table('daily_report_log')->where('daily_report_id', $parentReportId)->get()->getResultArray();
            $sumDt = 0; $sumMiru = 0; $sumOps = 0;
            foreach ($allLogs as $al) {
                $sumDt   += (float)$al['total_dt'];
                $sumMiru += (float)$al['miru_jam'];
                $sumOps  += (float)$al['ops_jam'];
            }
            $this->db->table('daily_report')->where('id', $parentReportId)->update([
                'total_dt'  => $sumDt,
                'total_jam' => $sumMiru + $sumOps + $sumDt,
            ]);
        }
    }
}
