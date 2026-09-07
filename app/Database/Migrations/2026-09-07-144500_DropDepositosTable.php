<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;


class DropDepositosTable extends Migration
{
    public function up()
    {
        $this->forge->dropForeignKey('pagos', 'pagos_deposito_id_foreign');
        $this->forge->dropColumn('pagos', 'deposito_id');
        $this->forge->dropTable('depositos');
    }

    public function down()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'no_boleta' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'banco' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'fecha_deposito' => [
                'type' => 'DATETIME',
            ],
            'monto_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addUniqueKey('no_boleta');
        $this->forge->createTable('depositos');

        $this->forge->addColumn('pagos', [
            'deposito_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'usuario_id',
            ],
        ]);
        $this->forge->addForeignKey('deposito_id', 'depositos', 'id', 'CASCADE', 'SET NULL', 'pagos');
        $this->forge->processIndexes('pagos');
    }
}