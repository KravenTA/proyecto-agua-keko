<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Un contador por servicio. Algunos inactivos a proposito, para verificar
 * que la lista de pendientes de lectura los excluye. (HU-12)
 */
class ContadoresSeeder extends Seeder
{
    public function run()
    {
        $servicios = $this->db->table('servicios')
            ->select('id')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (empty($servicios)) {
            echo "Faltan servicios. Ejecuta ServiciosSeeder primero.\n";
            return;
        }

        $ahora = date('Y-m-d H:i:s');

        foreach ($servicios as $i => $servicio) {
            $numeroSerie = 'CTR-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);

            $existe = $this->db->table('contadores')
                ->where('numero_serie', $numeroSerie)
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $inactivo = ($i % 9 === 0 && $i > 0);

            $this->db->table('contadores')->insert([
                'servicio_id'       => $servicio['id'],
                'numero_serie'      => $numeroSerie,
                'lectura_inicial'   => 0,
                'fecha_instalacion' => '2026-01-20',
                'fecha_retiro'      => $inactivo ? '2026-06-30' : null,
                'activo'            => $inactivo ? 0 : 1,
                'created_at'        => $ahora,
                'updated_at'        => $ahora,
            ]);
        }
    }
}