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

    /**
     * Listado general de auditoria, con el nombre del usuario que hizo el
     * cambio, mas reciente primero. Para la pantalla de consulta.
     */
    public function listarConUsuario(array $filtros = []): array
    {
        $builder = $this->select('
                auditoria.id,
                auditoria.tabla,
                auditoria.registro_id,
                auditoria.accion,
                auditoria.datos_anteriores,
                auditoria.datos_nuevos,
                auditoria.created_at,
                usuarios.nombre AS usuario_nombre
            ')
            ->join('usuarios', 'usuarios.id = auditoria.usuario_id', 'left');

        if (! empty($filtros['tabla'])) {
            $builder->where('auditoria.tabla', $filtros['tabla']);
        }

        return $builder->orderBy('auditoria.created_at', 'DESC')->findAll(200);
    }
}