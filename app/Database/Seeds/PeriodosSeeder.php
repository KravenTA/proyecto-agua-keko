<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Periodos de ejemplo: uno cerrado y uno abierto.
 * El periodo "actual" es el que tiene estado = 'abierto'; se define asi
 * para que la oficina pueda cerrar un periodo tarde sin que el sistema
 * quede sin periodo vigente. (HU-12)
 */
class PeriodosSeeder extends Seeder
{
    public function run()
    {
        $ahora = date('Y-m-d H:i:s');

        $periodos = [
            ['anio' => 2026, 'mes' => 7, 'estado' => 'cerrado'],
            ['anio' => 2026, 'mes' => 8, 'estado' => 'abierto'],
        ];

        foreach ($periodos as $periodo) {
            $existe = $this->db->table('periodos')
                ->where('anio', $periodo['anio'])
                ->where('mes', $periodo['mes'])
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $this->db->table('periodos')->insert($periodo + [
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
}