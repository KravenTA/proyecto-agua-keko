<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContadoresTable extends Migration
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
            'numero_serie' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'lectura_inicial' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'fecha_instalacion' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'fecha_retiro' => [
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
        $this->forge->addUniqueKey('numero_serie');
        $this->forge->addForeignKey('servicio_id', 'servicios', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('contadores');
    }

    public function down()
    {
        $this->forge->dropTable('contadores');
    }
}
