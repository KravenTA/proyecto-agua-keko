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
        $tarifa = $this->db->table('tarifas')->select('id')->get()->getRowArray();

        $lector = $this->db->table('usuarios')
            ->select('usuarios.id')
            ->join('roles', 'roles.id = usuarios.rol_id')
            ->where('roles.nombre', 'Lector')
            ->get()->getRowArray();

        // Si nadie creo un usuario Lector todavia, usamos cualquier usuario.
        $lector = $lector ?: $this->db->table('usuarios')->select('id')->get()->getRowArray();

        $cerrado = $this->db->table('periodos')->where('estado', 'cerrado')->get()->getRowArray();
        $abierto = $this->db->table('periodos')->where('estado', 'abierto')->get()->getRowArray();

        if (! $tarifa || ! $lector || ! $cerrado || ! $abierto) {
            echo "Faltan tarifas, usuarios o periodos. Ejecuta DatabaseSeeder completo.\n";
            return;
        }

        $contadores = $this->db->table('contadores')
            ->select('contadores.id, contadores.servicio_id')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('contadores.activo', 1)
            ->where('servicios.estado', 'activo')
            ->orderBy('contadores.id', 'ASC')
            ->get()->getResultArray();

        $ahora = date('Y-m-d H:i:s');

        foreach ($contadores as $i => $contador) {
            $anterior = 100 + $i * 3;
            $actual   = $anterior + 8 + ($i % 5);

            $this->insertarSiFalta($contador, $cerrado['id'], $tarifa['id'], $lector['id'],
                $anterior, $actual, '2026-07-28', $ahora);

            // Solo la mitad tiene lectura del periodo abierto: el resto son
            // los que deben aparecer como pendientes.
            if ($i % 2 === 0) {
                $this->insertarSiFalta($contador, $abierto['id'], $tarifa['id'], $lector['id'],
                    $actual, $actual + 9 + ($i % 4), '2026-08-26', $ahora);
            }
        }
    }

    private function insertarSiFalta(array $contador, int $periodoId, int $tarifaId,
                                     int $lectorId, float $anterior, float $actual,
                                     string $fecha, string $ahora): void
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
            'fecha_lectura'     => $fecha,
            'created_at'        => $ahora,
            'updated_at'        => $ahora,
        ]);
    }
}