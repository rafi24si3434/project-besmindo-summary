<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThirdPartiesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('third_parties');
    }

    public function down(): void
    {
        $this->forge->dropTable('third_parties');
    }
}
