<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixContadoresTipoServicioColumn extends Migration
{
    public function up()
    {
        $columnas = $this->columnasActuales();

        if (in_array('tipo_servicio', $columnas, true)) {
            $this->eliminarCapacidadSiExiste($columnas);
            return;
        }

        if (in_array('tipo_paja', $columnas, true)) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_paja` `tipo_servicio` VARCHAR(20) NULL');
        } elseif (in_array('tipo_tuberia', $columnas, true)) {
            $this->db->query('ALTER TABLE `contadores` CHANGE `tipo_tuberia` `tipo_servicio` VARCHAR(20) NULL');
        } else {
            $this->forge->addColumn('contadores', [
                'tipo_servicio' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'numero_serie',
                ],
            ]);
        }

        $this->eliminarCapacidadSiExiste($this->columnasActuales());
    }

    public function down()
    {
        // No revertimos: esta migracion solo corrige un estado inconsistente,
        // no agrega una funcionalidad nueva que tenga sentido deshacer.
    }

    private function eliminarCapacidadSiExiste(array $columnas): void
    {
        if (in_array('capacidad', $columnas, true)) {
            $this->forge->dropColumn('contadores', 'capacidad');
        }
    }

    /**
     * Consulta directa a information_schema, sin pasar por el cache de
     * columnas de CodeIgniter. Ese cache queda desactualizado cuando varias
     * migraciones alteran la misma tabla dentro de una sola corrida de
     * "php spark migrate" -- que es exactamente lo que le paso a Oliver.
     */
    private function columnasActuales(): array
    {
        $resultado = $this->db->query(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contadores'"
        )->getResultArray();

        return array_column($resultado, 'COLUMN_NAME');
    }
}