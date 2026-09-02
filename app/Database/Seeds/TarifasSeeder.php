<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Una tarifa vigente por cada tipo de servicio. Sin esto no se puede
 * registrar una lectura: lecturas.tarifa_id es NOT NULL y la tarifa se
 * resuelve por el tipo de paja del contador.
 */
class TarifasSeeder extends Seeder
{
    public function run()
    {
        $ahora = date('Y-m-d H:i:s');

        $tarifas = [
            ['tipo' => 'cuarto_paja',   'volumen_incluido_litros' => 10000, 'cuota_minima' => 15.00, 'precio_unitario' => 2.50],
            ['tipo' => 'media_paja',    'volumen_incluido_litros' => 20000, 'cuota_minima' => 25.00, 'precio_unitario' => 2.50],
            ['tipo' => 'paja_completa', 'volumen_incluido_litros' => 40000, 'cuota_minima' => 45.00, 'precio_unitario' => 2.50],
            ['tipo' => 'exceso',        'volumen_incluido_litros' => 0,     'cuota_minima' => 0.00,  'precio_unitario' => 3.00],
        ];

        foreach ($tarifas as $tarifa) {
            $existe = $this->db->table('tarifas')
                ->where('tipo', $tarifa['tipo'])
                ->where('activo', 1)
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $this->db->table('tarifas')->insert($tarifa + [
                'vigente_desde' => '2026-01-01',
                'vigente_hasta' => null,
                'activo'        => 1,
                'created_at'    => $ahora,
                'updated_at'    => $ahora,
            ]);
        }
    }
}