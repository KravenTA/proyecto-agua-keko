<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturasTable extends Migration
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
            'servicio_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'contador_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'periodo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tarifa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'lector_usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'lectura_anterior' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'lectura_actual' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'fecha_lectura' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('servicio_id', 'servicios', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('contador_id', 'contadores', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('periodo_id', 'periodos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('tarifa_id', 'tarifas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('lector_usuario_id', 'usuarios', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('lecturas');
    }

    public function down()
    {
        $this->forge->dropTable('lecturas');
    }
}
