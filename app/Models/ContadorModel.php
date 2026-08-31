<?php

namespace App\Models;

use CodeIgniter\Model;

class ContadorModel extends Model
{
    protected $table         = 'contadores';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['servicio_id', 'numero_serie', 'lectura_inicial', 'fecha_instalacion', 'fecha_retiro', 'activo'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'servicio_id'  => 'required|is_natural_no_zero',
        'numero_serie' => 'required|is_unique[contadores.numero_serie,id,{id}]|max_length[30]',
    ];

    protected $validationMessages = [
        'servicio_id' => [
            'required' => 'El contador debe estar asociado a un predio/servicio.',
        ],
        'numero_serie' => [
            'required'  => 'El código del contador es obligatorio.',
            'is_unique' => 'Ya existe un contador registrado con ese código.',
        ],
    ];

    public function listarConDetalle(): array
    {
        return $this->select('
                contadores.id,
                contadores.numero_serie,
                contadores.lectura_inicial,
                contadores.fecha_instalacion,
                contadores.activo,
                servicios.codigo AS servicio_codigo,
                servicios.direccion AS referencia,
                sectores.nombre AS sector_nombre,
                clientes.id AS cliente_id,
                clientes.nombre AS cliente_nombre
            ')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->join('sectores', 'sectores.id = servicios.sector_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->orderBy('contadores.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Un contador con el nombre de su cliente y referencia del predio,
     * para mostrarlos (solo lectura) en el formulario de edicion. (HU-11)
     */
    public function obtenerConDetalle(int $id): ?array
    {
        return $this->select('
                contadores.*,
                servicios.direccion AS referencia,
                clientes.nombre AS cliente_nombre
            ')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->where('contadores.id', $id)
            ->first();
    }

    /**
     * Contadores activos, pendientes de lectura. (HU-11)
     * Criterio: un contador inactivo no debe aparecer en esta lista.
     */
    public function listarActivosParaLectura(): array
    {
        return $this->select('
                contadores.id,
                contadores.numero_serie,
                servicios.direccion AS referencia,
                clientes.nombre AS cliente_nombre
            ')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->where('contadores.activo', 1)
            ->orderBy('contadores.numero_serie', 'ASC')
            ->findAll();
    }
}