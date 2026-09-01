<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoTuberiaCapacidadContadoresTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('tipo_tuberia', 'contadores')) {
            $this->forge->addColumn('contadores', [
                'tipo_tuberia' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'numero_serie',
                ],
            ]);
        }

        if (! $this->db->fieldExists('capacidad', 'contadores')) {
            $this->forge->addColumn('contadores', [
                'capacidad' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'tipo_tuberia',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tipo_tuberia', 'contadores')) {
            $this->forge->dropColumn('contadores', 'tipo_tuberia');
        }
        if ($this->db->fieldExists('capacidad', 'contadores')) {
            $this->forge->dropColumn('contadores', 'capacidad');
        }
    }
}