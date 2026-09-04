<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table         = 'auditoria';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['tabla', 'registro_id', 'accion', 'usuario_id', 'datos_anteriores', 'datos_nuevos'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Historial de auditoria de un registro especifico, mas reciente primero.
     */
    public function historialDe(string $tabla, int $registroId): array
    {
        return $this->where('tabla', $tabla)
            ->where('registro_id', $registroId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}