<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDireccionToClientesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('clientes', [
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'telefono',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('clientes', 'direccion');
    }
}

