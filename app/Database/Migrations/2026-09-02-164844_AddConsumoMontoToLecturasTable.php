<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * HU-15: lecturas necesita guardar el consumo calculado y el monto a cobrar.
 * Son nullable porque las lecturas registradas por HU-14 todavia no los tienen.
 */
class AddConsumoMontoToLecturasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lecturas', [
            'consumo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'lectura_actual',
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'consumo',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lecturas', ['consumo', 'monto']);
    }
}