<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTarifasTable extends Migration
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
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'cuota_minima' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'vigente_desde' => [
                'type' => 'DATE',
            ],
            'vigente_hasta' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->createTable('tarifas');
    }

    public function down()
    {
        $this->forge->dropTable('tarifas');
    }
}
