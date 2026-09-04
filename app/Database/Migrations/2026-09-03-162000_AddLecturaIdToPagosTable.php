<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 
 * El UNIQUE en lectura_id es lo que garantiza "no pagar dos veces la misma
 * lectura" a nivel de base de datos, no solo con una validacion en PHP.
 */
class AddLecturaIdToPagosTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pagos', [
            'lectura_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        $this->forge->addForeignKey('lectura_id', 'lecturas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('pagos');

        $this->db->query('
            ALTER TABLE pagos
            ADD UNIQUE KEY uk_pagos_lectura (lectura_id)
        ');

        $this->forge->modifyColumn('pagos', [
            'recibo_id' => [
                'name'       => 'recibo_id',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropForeignKey('pagos', 'pagos_lectura_id_foreign');
        $this->db->query('ALTER TABLE pagos DROP INDEX uk_pagos_lectura');
        $this->forge->dropColumn('pagos', 'lectura_id');

        $this->forge->modifyColumn('pagos', [
            'recibo_id' => [
                'name'       => 'recibo_id',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}