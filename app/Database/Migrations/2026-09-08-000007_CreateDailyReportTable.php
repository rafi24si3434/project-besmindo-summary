<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyReportTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'rig_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'lokasi_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'no_well'         => ['type' => 'INT', 'default' => 0],
            'tanggal_mulai'   => ['type' => 'DATE', 'null' => true],
            'tanggal_selesai' => ['type' => 'DATE', 'null' => true],
            'jarak'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'miru_jam'        => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0],
            'ops_jam'         => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0],
            'total_dt'        => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0],
            'total_jam'       => ['type' => 'DECIMAL', 'constraint' => '8,2', 'default' => 0],
            'status_job'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => ''],
            'remark'          => ['type' => 'TEXT', 'null' => true],
            'bulan'           => ['type' => 'INT', 'null' => false],
            'tahun'           => ['type' => 'INT', 'null' => false],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['rig_id', 'bulan', 'tahun']);
        $this->forge->createTable('daily_report');
    }

    public function down(): void
    {
        $this->forge->dropTable('daily_report');
    }
}
