<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Administrador', 'Secretaria', 'Lector'];
        $ahora = date('Y-m-d H:i:s');

        foreach ($roles as $nombre) {
            $existe = $this->db->table('roles')
                ->where('nombre', $nombre)
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $this->db->table('roles')->insert([
                'nombre'     => $nombre,
                'activo'     => 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
}