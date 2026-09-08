<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMonthlySummaryTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'rig_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'bulan'          => ['type' => 'INT', 'null' => false],
            'tahun'          => ['type' => 'INT', 'null' => false],
            'reliability'    => ['type' => 'DECIMAL', 'constraint' => '8,6', 'default' => 0],
            'availability'   => ['type' => 'DECIMAL', 'constraint' => '8,6', 'default' => 0],
            'utilization'    => ['type' => 'DECIMAL', 'constraint' => '8,6', 'default' => 0],
            'total_miru'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'total_ops'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'avg_miru'       => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'avg_cycle_time' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'total_well_job' => ['type' => 'INT', 'default' => 0],
            'sbwc_jam'       => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'unpaid_jam'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'revenue_target' => ['type' => 'BIGINT', 'default' => 0],
            'revenue_actual' => ['type' => 'BIGINT', 'default' => 0],
            'total_jam'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'remark'         => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['rig_id', 'bulan', 'tahun'], false, true, 'unik_summary');
        $this->forge->createTable('monthly_summary');
    }

    public function down(): void
    {
        $this->forge->dropTable('monthly_summary');
    }
}
