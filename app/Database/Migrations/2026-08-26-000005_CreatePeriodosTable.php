<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodosTable extends Migration
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
            'anio' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'mes' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['abierto', 'cerrado'],
                'default'    => 'abierto',
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
        $this->forge->addUniqueKey(['anio', 'mes']);
        $this->forge->createTable('periodos');
    }

    public function down()
    {
        $this->forge->dropTable('periodos');
    }
}
