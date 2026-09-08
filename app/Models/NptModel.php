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
        $existing = $this->db->table('npt_harian')
            ->where('rig_id', $rig_id)
            ->where('tanggal', $tanggal)
            ->where('kategori_id', $kategori_id)
            ->where('third_party_id IS ' . ($third_party_id ? '' : 'NULL') . ($third_party_id ? '='. $third_party_id : ''), null, false)
            ->get()->getRowArray();

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
}
