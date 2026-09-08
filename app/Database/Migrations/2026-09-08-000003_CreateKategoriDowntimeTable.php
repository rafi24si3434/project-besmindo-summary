<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriDowntimeTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'tipe'   => ['type' => 'ENUM', 'constraint' => ['UNPAID', 'SBWC'], 'null' => false],
            'urutan' => ['type' => 'INT', 'default' => 0],
            'aktif'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kategori_downtime');
    }

    public function down(): void
    {
        $this->forge->dropTable('kategori_downtime');
    }
}
