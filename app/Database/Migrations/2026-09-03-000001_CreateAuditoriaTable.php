<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Bitacora de auditoria generica para usuarios, tarifas y pagos (SDGODA-45).
 * Una fila por cada alta/edicion/baja, con quien lo hizo y cuando.
 */
class CreateAuditoriaTable extends Migration
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
            'tabla' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'registro_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'accion' => [
                'type'       => 'ENUM',
                'constraint' => ['alta', 'edicion', 'baja'],
                'null'       => false,
            ],
            'usuario_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true, // null si la accion la hizo el sistema (seeders, procesos automaticos)
            ],
            'datos_anteriores' => [
                'type' => 'TEXT',
                'null' => true, // JSON. Vacio en altas.
            ],
            'datos_nuevos' => [
                'type' => 'TEXT',
                'null' => true, // JSON. Vacio en bajas.
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['tabla', 'registro_id']);
        $this->forge->addKey('usuario_id');
        $this->forge->createTable('auditoria');
    }

    public function down()
    {
        $this->forge->dropTable('auditoria');
    }
}