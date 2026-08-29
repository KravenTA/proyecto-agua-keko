<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Tarifa de ejemplo vigente. Necesaria porque lecturas.tarifa_id es NOT NULL.
 */
class TarifasSeeder extends Seeder
{
    public function run()
    {
        $existe = $this->db->table('tarifas')->countAllResults();

        if ($existe > 0) {
            return;
        }

        $ahora = date('Y-m-d H:i:s');

        $this->db->table('tarifas')->insert([
            'precio_unitario' => 2.50,
            'cuota_minima'    => 15.00,
            'vigente_desde'   => '2026-01-01',
            'vigente_hasta'   => null,
            'activo'          => 1,
            'created_at'      => $ahora,
            'updated_at'      => $ahora,
        ]);
    }
}