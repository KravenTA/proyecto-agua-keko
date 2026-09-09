<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * SDGODA-53: los recibos emitidos antes de que existiera la columna
 * fecha_vencimiento quedaron con el valor en NULL. Se rellenan con
 * fecha_emision + 15 dias, misma regla que usa
 * ReciboModel::emitirPorLectura() para los recibos nuevos.
 */
class BackfillFechaVencimientoRecibos extends Migration
{
    public function up()
    {
        $this->db->query("
            UPDATE recibos
            SET fecha_vencimiento = DATE_ADD(fecha_emision, INTERVAL 15 DAY)
            WHERE fecha_vencimiento IS NULL
        ");
    }

    public function down()
    {
        // No se revierte: no hay forma de distinguir los valores
        // originales de los que se rellenaron aqui.
    }
}