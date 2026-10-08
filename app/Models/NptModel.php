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
     * Total jam SBWC 75% (SWA Rain & Dry Road/Public Issue)
     */
    public function getTotalSBWC75(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->db->table('npt_harian nh')
            ->select('SUM(nh.jam) as total')
            ->join('kategori_downtime kd', 'kd.id = nh.kategori_id')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->where('kd.tipe', 'SBWC')
            ->groupStart()
                ->whereIn('kd.id', [3, 4])
                ->orLike('LOWER(kd.nama)', 'rain')
                ->orLike('LOWER(kd.nama)', 'dry road')
            ->groupEnd()
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total jam SBWC 65% (Kategori SBWC selain Rain & Dry Road)
     */
    public function getTotalSBWC65(int $rig_id, int $bulan, int $tahun): float
    {
        $row = $this->db->table('npt_harian nh')
            ->select('SUM(nh.jam) as total')
            ->join('kategori_downtime kd', 'kd.id = nh.kategori_id')
            ->where('nh.rig_id', $rig_id)
            ->where('MONTH(nh.tanggal)', $bulan)
            ->where('YEAR(nh.tanggal)', $tahun)
            ->where('kd.tipe', 'SBWC')
            ->whereNotIn('kd.id', [3, 4])
            ->notLike('LOWER(kd.nama)', 'rain')
            ->notLike('LOWER(kd.nama)', 'dry road')
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
     * Hapus satu baris NPT (bisa berdasarkan ID baris atau kombinasi rig_id, tanggal, kategori_id, third_party_id)
     */
    public function hapusRow($idOrRigId, ?string $tanggal = null, ?int $kategori_id = null, ?int $third_party_id = null): bool
    {
        if ($tanggal === null && $kategori_id === null) {
            return $this->db->table('npt_harian')->where('id', (int)$idOrRigId)->delete();
        }

        $builder = $this->db->table('npt_harian')
            ->where('rig_id', (int)$idOrRigId)
            ->where('tanggal', $tanggal)
            ->where('kategori_id', (int)$kategori_id);

        if ($third_party_id === null) {
            $builder->where('third_party_id IS NULL', null, false);
        } else {
            $builder->where('third_party_id', (int)$third_party_id);
        }

        return $builder->delete();
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
     * Jika jam bernilai 0 dan remark kosong, upsertNpt akan otomatis menghapus row terkait di npt_harian.
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
            // Panggil upsertNpt tanpa guard jam > 0 agar jam = 0 otomatis menghapus baris npt_harian
            $this->upsertNpt($rig_id, $tanggal, $kategoriId, null, $jam, $jam > 0 ? $remark : null);
        }

        // Pos 3rd Party (ID 10):
        $tpJamArray = $logRow['tp_jam'] ?? [];
        $jam3rdPartyTotal = (float)($logRow['dt_3rd_party'] ?? 0);

        // Bersihkan entri 3rd party tanggal ini terlebih dahulu agar sinkron dengan pilihan terbaru
        $this->db->table('npt_harian')
            ->where('rig_id', $rig_id)
            ->where('tanggal', $tanggal)
            ->where('kategori_id', 10)
            ->delete();

        if (!empty($tpJamArray)) {
            foreach ($tpJamArray as $tpId => $jam) {
                $jam = (float)$jam;
                if ($jam > 0) {
                    // tpId = 0 berarti Tanpa Perusahaan (third_party_id = NULL)
                    $thirdPartyId = ((int)$tpId === 0) ? null : (int)$tpId;
                    $this->upsertNpt($rig_id, $tanggal, 10, $thirdPartyId, $jam, $remarkGeneral);
                }
            }
        } elseif ($jam3rdPartyTotal > 0) {
            // Disimpan sebagai Tanpa Perusahaan (NULL)
            $this->upsertNpt($rig_id, $tanggal, 10, null, $jam3rdPartyTotal, $remarkGeneral);
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

            $this->upsertNpt($rig_id, $tanggal, $kategoriId, null, $jamVal, $jamVal > 0 ? $remarkToUse : null);
        }
    }

    /**
     * SINKRONISASI BALIK DARI NPT KE DAILY REPORT DT & LOG
     * Jika user mengedit di NPT Harian, otomatis update daily_report_dt, daily_report_log, dan daily_report
     */
    public function syncNptBackToDaily(int $rig_id, string $tanggal, int $kategori_id, float $jam, ?string $remark = null): void
    {
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

        if (!isset($kategoriColMap[$kategori_id])) {
            return;
        }

        $colName = $kategoriColMap[$kategori_id];
        $bulan = (int)date('n', strtotime($tanggal));
        $tahun = (int)date('Y', strtotime($tanggal));

        // 1. Cek apakah ada daily_report_log untuk rig dan tanggal ini
        $logRow = $this->db->table('daily_report_log drl')
            ->select('drl.*, dr.rig_id')
            ->join('daily_report dr', 'dr.id = drl.daily_report_id')
            ->where('dr.rig_id', $rig_id)
            ->where('drl.tanggal', $tanggal)
            ->get()->getRowArray();

        $parentReportId = null;

        if ($logRow) {
            $parentReportId = (int)$logRow['daily_report_id'];
            $updateLog = [$colName => $jam];

            if (in_array($kategori_id, [1, 2]) && !empty($remark)) {
                $updateLog['remark_unpaid'] = $remark;
            } elseif (in_array($kategori_id, [1, 2]) && $jam <= 0) {
                $updateLog['remark_unpaid'] = null;
            }

            // Hitung total_dt baru
            $totalDt = 0.0;
            foreach ($kategoriColMap as $c) {
                $val = ($c === $colName) ? $jam : (float)($logRow[$c] ?? 0);
                $totalDt += $val;
            }

            $miru = (float)($logRow['miru_jam'] ?? 0);
            $ops  = (float)($logRow['ops_jam'] ?? 0);

            // Jika total jam sebelumnya bernilai 24, sesuaikan ops_jam agar total tetap 24 jam kalender
            if (($miru + $ops + (float)($logRow['total_dt'] ?? 0)) >= 23.99 || ($ops == 0 && $miru == 0 && $totalDt > 0)) {
                $ops = max(0.0, 24.0 - $miru - $totalDt);
                $updateLog['ops_jam'] = $ops;
            }

            $totalHrs = $miru + $ops + $totalDt;
            $updateLog['total_dt']  = $totalDt;
            $updateLog['total_hrs'] = $totalHrs;

            if ($totalHrs <= 0.001) {
                // Jika semua jam di baris log bernilai 0 dan jam NPT ini diubah jadi 0/dihapus, bersihkan log
                $this->db->table('daily_report_log')->where('id', $logRow['id'])->delete();
            } else {
                $this->db->table('daily_report_log')->where('id', $logRow['id'])->update($updateLog);
            }
        } elseif ($jam > 0) {
            // Log belum ada, tapi NPT bernilai > 0. Cari sumur aktif untuk rig ini di periode bulan/tanggal ini
            $well = $this->db->table('daily_report')
                ->where('rig_id', $rig_id)
                ->where('tanggal_mulai <=', $tanggal)
                ->where('tanggal_selesai >=', $tanggal)
                ->get()->getRowArray();

            if (!$well) {
                $well = $this->db->table('daily_report')
                    ->where('rig_id', $rig_id)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->orderBy('no_well', 'DESC')
                    ->get()->getRowArray();
            }

            if (!$well) {
                // Buat sumur otomatis jika belum ada sama sekali untuk rig ini di bulan tersebut
                $lokasiRow = $this->db->table('lokasi')->where('aktif', 1)->get()->getRowArray();
                $lokasiId = $lokasiRow ? (int)$lokasiRow['id'] : 1;
                $newWellId = $this->db->table('daily_report')->insert([
                    'rig_id'          => $rig_id,
                    'lokasi_id'       => $lokasiId,
                    'no_well'         => 1,
                    'tanggal_mulai'   => $tanggal,
                    'tanggal_selesai' => $tanggal,
                    'jarak'           => 0,
                    'miru_jam'        => 0,
                    'ops_jam'         => max(0.0, 24.0 - $jam),
                    'total_dt'        => $jam,
                    'total_jam'       => 24.0,
                    'status_job'      => 'JOB PROGRESS',
                    'remark'          => $remark,
                    'remark_unpaid'   => in_array($kategori_id, [1, 2]) ? $remark : null,
                    'bulan'           => $bulan,
                    'tahun'           => $tahun,
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
                $parentReportId = (int)$this->db->insertID();
            } else {
                $parentReportId = (int)$well['id'];
                // Perpanjang tanggal sumur jika log ini melewati tanggal sumur sebelumnya
                $wEnd = $well['tanggal_selesai'] ?? $tanggal;
                if ($tanggal > $wEnd) {
                    $this->db->table('daily_report')->where('id', $parentReportId)->update(['tanggal_selesai' => $tanggal]);
                }
            }

            $opsJam = max(0.0, 24.0 - $jam);
            $newLog = [
                'daily_report_id' => $parentReportId,
                'tanggal'         => $tanggal,
                'jarak'           => 0,
                'miru_jam'        => 0,
                'ops_jam'         => $opsJam,
                'total_dt'        => $jam,
                'total_hrs'       => 24.0,
                'remark_npt'      => $remark,
                'remark_unpaid'   => in_array($kategori_id, [1, 2]) ? $remark : null,
                $colName          => $jam,
            ];
            $this->db->table('daily_report_log')->insert($newLog);
        }

        // Rekalkulasi subtotal pada daily_report parent dan daily_report_dt
        if ($parentReportId) {
            $allLogs = $this->db->table('daily_report_log')->where('daily_report_id', $parentReportId)->get()->getResultArray();
            $sumDt = 0.0; $sumMiru = 0.0; $sumOps = 0.0;
            $katTotals = [];

            foreach ($allLogs as $al) {
                $sumDt   += (float)$al['total_dt'];
                $sumMiru += (float)$al['miru_jam'];
                $sumOps  += (float)$al['ops_jam'];

                foreach ($kategoriColMap as $kId => $cField) {
                    $katTotals[$kId] = ($katTotals[$kId] ?? 0.0) + (float)($al[$cField] ?? 0);
                }
            }

            $this->db->table('daily_report')->where('id', $parentReportId)->update([
                'miru_jam'  => $sumMiru,
                'ops_jam'   => $sumOps,
                'total_dt'  => $sumDt,
                'total_jam' => $sumMiru + $sumOps + $sumDt,
            ]);

            // Sinkronkan daily_report_dt
            $this->db->table('daily_report_dt')->where('daily_report_id', $parentReportId)->delete();
            foreach ($katTotals as $kId => $hrs) {
                if ($hrs > 0) {
                    $this->db->table('daily_report_dt')->insert([
                        'daily_report_id' => $parentReportId,
                        'tanggal'         => $tanggal,
                        'kategori_id'     => $kId,
                        'jam'             => $hrs,
                    ]);
                }
            }
        }

        // Auto-recalculate Monthly Summary
        $monthlySummaryModel = new MonthlySummaryModel();
        $monthlySummaryModel->hitungDanSimpan($rig_id, $bulan, $tahun);
    }
}

