<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Sectores de ejemplo. Sin al menos uno, el formulario de contadores
 * (HU-10) no se puede usar.
 */
class SectoresSeeder extends Seeder
{
    public function run()
    {
        $sectores = [
            'Aldea La Majada',
            'Aldea El Zapote',
            'Barrio El Centro',
            'Caserio Los Encuentros',
            'Aldea San Antonio',
        ];

        $ahora = date('Y-m-d H:i:s');

        foreach ($sectores as $nombre) {
            $existe = $this->db->table('sectores')
                ->where('nombre', $nombre)
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $this->db->table('sectores')->insert([
                'nombre'     => $nombre,
                'activo'     => 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
}