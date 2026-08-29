<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Clientes de ejemplo para probar busqueda y paginacion (HU-09)
 * y como base para los modulos de servicios y contadores.
 */
class ClientesSeeder extends Seeder
{
    public function run()
    {
        $nombres = [
            'Ana Lucia Moran', 'Carlos Perez Ruiz', 'Maria Jose Aguilar',
            'Jorge Ramirez Lopez', 'Sofia Hernandez', 'Luis Alberto Cruz',
            'Elena Vasquez', 'Pedro Antonio Diaz', 'Claudia Morales',
            'Roberto Gonzalez', 'Andrea Castillo', 'Miguel Angel Reyes',
            'Patricia Solis', 'Fernando Estrada', 'Gabriela Mendez',
            'Hector Villatoro', 'Silvia Corado', 'Mario Alvarado',
            'Rosa Maria Chacon', 'Julio Cesar Barrios', 'Marta Alicia Pineda',
            'Oscar Recinos', 'Karla Sandoval', 'Byron Estuardo Lima',
            'Vilma Guzman',
        ];

        $aldeas = [
            'Aldea La Majada', 'Aldea El Zapote', 'Barrio El Centro',
            'Caserio Los Encuentros', 'Aldea San Antonio',
        ];

        $ahora = date('Y-m-d H:i:s');
        $filas = [];

        foreach ($nombres as $i => $nombre) {
            $filas[] = [
                'nombre'    => $nombre,
                'telefono'  => '2' . str_pad((string) (3000000 + $i * 7919), 7, '0', STR_PAD_LEFT),
                'direccion' => $aldeas[$i % count($aldeas)] . ', Jutiapa',
                'email'     => $i % 3 === 0 ? null : strtolower(explode(' ', $nombre)[0]) . ($i + 1) . '@correo.test',
                'activo'    => $i % 8 === 0 ? 0 : 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        foreach ($filas as $fila) {
            $existe = $this->db->table('clientes')
                ->where('nombre', $fila['nombre'])
                ->countAllResults();

            if ($existe === 0) {
                $this->db->table('clientes')->insert($fila);
            }
        }
    }
}