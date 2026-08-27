<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $rol = $this->db->table('roles')
            ->where('nombre', 'Administrador')
            ->get()
            ->getRowArray();

        if (! $rol) {
            echo "Falta el rol Administrador. Ejecuta primero RolesSeeder.\n";
            return;
        }

        $email = 'admin@oficinaagua.test';

        $existe = $this->db->table('usuarios')
            ->where('email', $email)
            ->countAllResults();

        if ($existe > 0) {
            return;
        }

        $ahora = date('Y-m-d H:i:s');

        $this->db->table('usuarios')->insert([
            'rol_id'        => $rol['id'],
            'nombre'        => 'Administrador del sistema',
            'email'         => $email,
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'activo'        => 1,
            'created_at'    => $ahora,
            'updated_at'    => $ahora,
        ]);
    }
}