<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVolumenTarifasTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('tipo', 'tarifas')) {
            $this->forge->addColumn('tarifas', [
                'tipo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if (! $this->db->fieldExists('volumen_incluido_litros', 'tarifas')) {
            $this->forge->addColumn('tarifas', [
                'volumen_incluido_litros' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'tipo',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tipo', 'tarifas')) {
            $this->forge->dropColumn('tarifas', 'tipo');
        }
        if ($this->db->fieldExists('volumen_incluido_litros', 'tarifas')) {
            $this->forge->dropColumn('tarifas', 'volumen_incluido_litros');
        }
    }
}