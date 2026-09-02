<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTipoTuberiaToTipoPajaContadoresTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('tipo_tuberia', 'contadores') && ! $this->db->fieldExists('tipo_paja', 'contadores')) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_tuberia` `tipo_paja` VARCHAR(50) NULL');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tipo_paja', 'contadores') && ! $this->db->fieldExists('tipo_tuberia', 'contadores')) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_paja` `tipo_tuberia` VARCHAR(50) NULL');
        }
    }
}