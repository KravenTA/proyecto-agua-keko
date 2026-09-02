<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentosClienteTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('clientes', [
            'dpi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'email',
            ],
            'foto_vivienda' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'dpi',
            ],
            'recibo_luz' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'foto_vivienda',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('clientes', ['dpi', 'foto_vivienda', 'recibo_luz']);
    }
}