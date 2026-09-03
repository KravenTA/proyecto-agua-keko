<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogErroresTable extends Migration
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
            'nivel' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'mensaje' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'linea' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'usuario_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->createTable('log_errores');
    }

    public function down()
    {
        $this->forge->dropTable('log_errores');
    }
}