<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Predios (servicios) de ejemplo. Cada servicio liga un cliente con un
 * sector y guarda la direccion del predio. Depende de ClientesSeeder
 * y SectoresSeeder.
 */
class ServiciosSeeder extends Seeder
{
    public function run()
    {
        $clientes = $this->db->table('clientes')
            ->select('id, nombre, direccion')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $sectores = $this->db->table('sectores')
            ->select('id, nombre')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (empty($clientes) || empty($sectores)) {
            echo "Faltan clientes o sectores. Ejecuta ClientesSeeder y SectoresSeeder primero.\n";
            return;
        }

        $ahora = date('Y-m-d H:i:s');

        foreach ($clientes as $i => $cliente) {
            $codigo = 'SRV-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);

            $existe = $this->db->table('servicios')
                ->where('codigo', $codigo)
                ->countAllResults();

            if ($existe > 0) {
                continue;
            }

            // Un par de servicios suspendidos, para verificar que la lista
            // de pendientes de lectura los excluye. (HU-12)
            $estado = ($i % 11 === 0 && $i > 0) ? 'suspendido' : 'activo';

            $this->db->table('servicios')->insert([
                'cliente_id' => $cliente['id'],
                'sector_id'  => $sectores[$i % count($sectores)]['id'],
                'codigo'     => $codigo,
                'direccion'  => $cliente['direccion'],
                'fecha_alta' => '2026-01-15',
                'estado'     => $estado,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
}