<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * SDGODA-53: agrega la fecha de vencimiento del recibo, para mostrarla
 * en el recibo imprimible junto a la fecha de emision, siguiendo la
 * factura de referencia.
 */
class AddFechaVencimientoToRecibosTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('fecha_vencimiento', 'recibos')) {
            $this->forge->addColumn('recibos', [
                'fecha_vencimiento' => [
                    'type'       => 'DATE',
                    'null'       => true,
                    'after'      => 'fecha_emision',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('fecha_vencimiento', 'recibos')) {
            $this->forge->dropColumn('recibos', 'fecha_vencimiento');
        }
    }
}