<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Lecturas de ejemplo. Deja a proposito una parte de los contadores sin
 * lectura del periodo abierto, para poder verificar la lista de pendientes
 * de lectura del lector. (HU-12)
 */
class LecturasSeeder extends Seeder
{
    public function run()
    {
        $tarifaModel = new \App\Models\TarifaModel();

        $lector = $this->db->table('usuarios')
            ->select('usuarios.id')
            ->join('roles', 'roles.id = usuarios.rol_id')
            ->where('roles.nombre', 'Lector')
            ->get()->getRowArray();

        // Si nadie creo un usuario Lector todavia, usamos cualquier usuario.
        $lector = $lector ?: $this->db->table('usuarios')->select('id')->get()->getRowArray();

        $cerrado = $this->db->table('periodos')->where('estado', 'cerrado')->get()->getRowArray();
        $abierto = $this->db->table('periodos')->where('estado', 'abierto')->get()->getRowArray();

        if (! $lector || ! $cerrado || ! $abierto) {
            echo "Faltan usuarios o periodos. Ejecuta DatabaseSeeder completo.\n";
            return;
        }

        $contadores = $this->db->table('contadores')
            ->select('contadores.id, contadores.servicio_id, contadores.tipo_servicio')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('contadores.activo', 1)
            ->where('servicios.estado', 'activo')
            ->orderBy('contadores.id', 'ASC')
            ->get()->getResultArray();

        $ahora = date('Y-m-d H:i:s');

        foreach ($contadores as $i => $contador) {
            $tarifa = $tarifaModel->tarifaVigente((string) $contador['tipo_servicio'], '2026-07-28');

            if (! $tarifa) {
                continue;
            }

            $anterior = 100 + $i * 3;
            $actual   = $anterior + 8 + ($i % 5);
            $monto    = $tarifaModel->calcularMonto($tarifa, $actual - $anterior, '2026-07-28');

            $this->insertarSiFalta($contador, $cerrado['id'], $tarifa['id'], $lector['id'],
                $anterior, $actual, (float) $monto, '2026-07-28', $ahora);

            if ($i % 2 === 0) {
                $actual2 = $actual + 9 + ($i % 4);
                $monto2  = $tarifaModel->calcularMonto($tarifa, $actual2 - $actual, '2026-08-26');

                $this->insertarSiFalta($contador, $abierto['id'], $tarifa['id'], $lector['id'],
                    $actual, $actual2, (float) $monto2, '2026-08-26', $ahora);
            }
        }
    }

    private function insertarSiFalta(array $contador, int $periodoId, int $tarifaId,
                                     int $lectorId, float $anterior, float $actual,
                                     float $monto, string $fecha, string $ahora): void
    {
        $existe = $this->db->table('lecturas')
            ->where('contador_id', $contador['id'])
            ->where('periodo_id', $periodoId)
            ->countAllResults();

        if ($existe > 0) {
            return;
        }

        $this->db->table('lecturas')->insert([
            'servicio_id'       => $contador['servicio_id'],
            'contador_id'       => $contador['id'],
            'periodo_id'        => $periodoId,
            'tarifa_id'         => $tarifaId,
            'lector_usuario_id' => $lectorId,
            'lectura_anterior'  => $anterior,
            'lectura_actual'    => $actual,
            'consumo'           => $actual - $anterior,
            'monto'             => $monto,
            'fecha_lectura'     => $fecha,
            'created_at'        => $ahora,
            'updated_at'        => $ahora,
        ]);
    }
}