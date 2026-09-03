<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            ['rol' => 'Administrador', 'nombre' => 'Administrador del sistema', 'email' => 'admin@oficinaagua.test'],
            ['rol' => 'Secretaria',    'nombre' => 'Secretaria de oficina',     'email' => 'secretaria@oficinaagua.test'],
            ['rol' => 'Lector',        'nombre' => 'Lector de campo',           'email' => 'lector@oficinaagua.test'],
        ];

        $ahora = date('Y-m-d H:i:s');

        foreach ($usuarios as $usuario) {
            $rol = $this->db->table('roles')
                ->where('nombre', $usuario['rol'])
                ->get()->getRowArray();

            if (! $rol) {
                echo "Falta el rol {$usuario['rol']}. Ejecuta primero RolesSeeder.\n";
                continue;
            }

            $existe = $this->db->table('usuarios')
                ->where('email', $usuario['email'])
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            $this->db->table('usuarios')->insert([
                'rol_id'        => $rol['id'],
                'nombre'        => $usuario['nombre'],
                'email'         => $usuario['email'],
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'activo'        => 1,
                'created_at'    => $ahora,
                'updated_at'    => $ahora,
            ]);
        }
    }
}