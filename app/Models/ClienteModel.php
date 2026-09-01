<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nombre', 'telefono', 'direccion', 'email', 'activo', 'observaciones',
        'dpi', 'foto_vivienda', 'recibo_luz',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function tieneContadoresActivos(int $clienteId): bool
    {
        return $this->db->table('contadores')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('servicios.cliente_id', $clienteId)
            ->where('contadores.activo', 1)
            ->countAllResults() > 0;
    }
}