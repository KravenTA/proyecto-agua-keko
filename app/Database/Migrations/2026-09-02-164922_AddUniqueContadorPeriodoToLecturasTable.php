<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Impide dos lecturas del mismo contador en el mismo periodo. La validacion
 * del controlador cubre el caso comun, pero dos envios simultaneos podrian
 * pasar. Mismo riesgo que SDGODA-44 plantea para pagos.
 */
class AddUniqueContadorPeriodoToLecturasTable extends Migration
{
    public function up()
    {
        $this->db->query('
            ALTER TABLE lecturas
            ADD UNIQUE KEY uk_lecturas_contador_periodo (contador_id, periodo_id)
        ');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE lecturas DROP INDEX uk_lecturas_contador_periodo');
    }
}