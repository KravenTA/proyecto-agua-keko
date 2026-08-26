<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepositosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'no_boleta' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'banco' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'fecha_deposito' => [
                'type' => 'DATETIME',
            ],
            'monto_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('no_boleta');
        $this->forge->createTable('depositos');
    }

    public function down()
    {
        $this->forge->dropTable('depositos');
    }
}
