<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRigsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'nama_rig'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'odr'        => ['type' => 'BIGINT', 'default' => 0, 'null' => false],
            'aktif'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('rigs');
    }

    public function down(): void
    {
        $this->forge->dropTable('rigs');
    }
}
