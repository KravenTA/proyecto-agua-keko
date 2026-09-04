<?php

namespace App\Models\Traits;

use App\Models\AuditoriaModel;

/**
 * Agrega auditoria automatica (alta/edicion/baja) a un modelo, usando los
 * callbacks nativos de CodeIgniter. El modelo que lo use debe:
 *
 *  1. use Auditable;
 *  2. protected $auditoriaTabla = 'usuarios'; // nombre logico, igual al $table
 *  3. registrar los callbacks:
 *       protected $beforeUpdate = ['auditarAntesEditar'];
 *       protected $afterInsert  = ['auditarAlta'];
 *       protected $afterUpdate  = ['auditarEdicion'];
 *       protected $beforeDelete = ['auditarBaja'];
 *
 * Ver UsuarioModel/TarifaModel para el ejemplo completo de integracion.
 */
trait Auditable
{
    /**
     * Guarda el estado del registro justo antes de un update, para poder
     * comparar "antes" vs "despues" en el afterUpdate (en afterUpdate el
     * registro en la base de datos ya quedo con los datos nuevos).
     */
    private ?array $auditoriaAnterior = null;

    protected function auditarAntesEditar(array $datos): array
    {
        $id = is_array($datos['id']) ? ($datos['id'][0] ?? null) : $datos['id'];
        $this->auditoriaAnterior = $id !== null ? $this->find($id) : null;

        return $datos;
    }

    protected function auditarAlta(array $datos): array
    {
        $id = $datos['id'] ?? null;

        if ($id !== null) {
            $this->registrarAuditoria('alta', (int) $id, null, $datos['data'] ?? null);
        }

        return $datos;
    }

    protected function auditarEdicion(array $datos): array
    {
        $id = is_array($datos['id']) ? ($datos['id'][0] ?? null) : $datos['id'];

        if ($id !== null) {
            $this->registrarAuditoria('edicion', (int) $id, $this->auditoriaAnterior, $datos['data'] ?? null);
        }

        return $datos;
    }

    protected function auditarBaja(array $datos): array
    {
        foreach ((array) $datos['id'] as $id) {
            $anterior = $this->find($id);
            $this->registrarAuditoria('baja', (int) $id, $anterior, null);
        }

        return $datos;
    }

    private function registrarAuditoria(string $accion, int $registroId, ?array $anterior, ?array $nuevo): void
    {
        (new AuditoriaModel())->insert([
            'tabla'            => $this->auditoriaTabla ?? $this->table,
            'registro_id'      => $registroId,
            'accion'           => $accion,
            'usuario_id'       => session('usuario_id') ?? null,
            'datos_anteriores' => $anterior ? json_encode($anterior) : null,
            'datos_nuevos'     => $nuevo ? json_encode($nuevo) : null,
        ]);
    }
}