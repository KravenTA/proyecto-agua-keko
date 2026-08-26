<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecibosTable extends Migration
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
            'lectura_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'fecha_emision' => [
                'type' => 'DATETIME',
            ],
            'monto_consumo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'monto_adicional' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'concepto_adicional' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'pagado', 'vencido', 'anulado'],
                'default'    => 'pendiente',
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
        $this->forge->addUniqueKey('numero');
        $this->forge->addForeignKey('lectura_id', 'lecturas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('recibos');
    }

    public function down()
    {
        $this->forge->dropTable('recibos');
    }
}
