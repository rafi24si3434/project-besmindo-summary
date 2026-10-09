<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up(): void
    {
        // 1. Index komposit pada daily_report_dt untuk mempercepat aggregasi breakdown downtime
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_dr_dt_composite ON daily_report_dt (daily_report_id, kategori_id)");

        // 2. Index pada npt_harian untuk pencarian per tanggal dan rentang tanggal
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_npt_tanggal ON npt_harian (tanggal)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_npt_rig_tanggal ON npt_harian (rig_id, tanggal)");

        // 3. Index pada monthly_summary untuk query tahunan & bulanan instan
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_ms_tahun_bulan ON monthly_summary (tahun, bulan)");

        // 4. Index rigs aktif
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_rigs_aktif ON rigs (aktif)");
    }

    public function down(): void
    {
        $this->db->query("DROP INDEX IF EXISTS idx_dr_dt_composite ON daily_report_dt");
        $this->db->query("DROP INDEX IF EXISTS idx_npt_tanggal ON npt_harian");
        $this->db->query("DROP INDEX IF EXISTS idx_npt_rig_tanggal ON npt_harian");
        $this->db->query("DROP INDEX IF EXISTS idx_ms_tahun_bulan ON monthly_summary");
        $this->db->query("DROP INDEX IF EXISTS idx_rigs_aktif ON rigs");
    }
}
