<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTipoPajaToTipoServicioContadoresTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('tipo_paja', 'contadores') && ! $this->db->fieldExists('tipo_servicio', 'contadores')) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_paja` `tipo_servicio` VARCHAR(20) NULL');
        }

        if ($this->db->fieldExists('capacidad', 'contadores')) {
            $this->forge->dropColumn('contadores', 'capacidad');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tipo_servicio', 'contadores') && ! $this->db->fieldExists('tipo_paja', 'contadores')) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_servicio` `tipo_paja` VARCHAR(50) NULL');
        }

        if (! $this->db->fieldExists('capacidad', 'contadores')) {
            $this->forge->addColumn('contadores', [
                'capacidad' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            ]);
        }
    }
}