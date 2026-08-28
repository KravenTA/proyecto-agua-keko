<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'telefono', 'direccion', 'email', 'activo', 'observaciones'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Verifica si el cliente tiene al menos un contador activo asociado
     * (a traves de sus servicios). Se usa para bloquear la desactivacion. (HU-08)
     */
    public function tieneContadoresActivos(int $clienteId): bool
    {
        return $this->db->table('contadores')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('servicios.cliente_id', $clienteId)
            ->where('contadores.activo', 1)
            ->countAllResults() > 0;
    }
}