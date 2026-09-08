<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyReportDtTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'daily_report_id' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'tanggal'         => ['type' => 'DATE', 'null' => false],
            'kategori_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'third_party_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'jam'             => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('daily_report_id');
        $this->forge->createTable('daily_report_dt');
    }

    public function down(): void
    {
        $this->forge->dropTable('daily_report_dt');
    }
}
