<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNptHarianTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'rig_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'lokasi_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'tanggal'         => ['type' => 'DATE', 'null' => false],
            'kategori_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'third_party_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'jam'             => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'remark'          => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['rig_id', 'tanggal', 'kategori_id', 'third_party_id'], false, true, 'unik_npt');
        $this->forge->createTable('npt_harian');
    }

    public function down(): void
    {
        $this->forge->dropTable('npt_harian');
    }
}
