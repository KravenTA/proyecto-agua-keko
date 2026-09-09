<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * SDGODA-53: agrega el NIT del cliente para mostrarlo en el recibo
 * imprimible, siguiendo la factura de referencia del ingeniero.
 */
class AddNitToClientesTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('nit', 'clientes')) {
            $this->forge->addColumn('clientes', [
                'nit' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'dpi',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nit', 'clientes')) {
            $this->forge->dropColumn('clientes', 'nit');
        }
    }
}