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
        'servicio_id'  => 'required|is_natural_no_zero',
        'numero_serie' => 'required|is_unique[contadores.numero_serie]|max_length[30]',
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
     * Contadores pendientes de lectura del periodo indicado. (HU-12)
     *
     * Un contador esta pendiente si:
     *  - esta activo,
     *  - su servicio (predio) esta activo,
     *  - y no tiene una lectura registrada en ese periodo.
     *
     * Trae ademas la ultima lectura conocida, para que el lector sepa que
     * valor esperar cuando llegue al predio.
     */
    public function pendientesDeLectura(int $periodoId, string $busqueda = ''): array
    {
        $builder = $this->select('
                contadores.id,
                contadores.numero_serie,
                contadores.lectura_inicial,
                servicios.id AS servicio_id,
                servicios.codigo AS servicio_codigo,
                servicios.direccion,
                sectores.nombre AS sector_nombre,
                clientes.nombre AS cliente_nombre,
                clientes.telefono AS cliente_telefono,
                (
                    SELECT l.lectura_actual
                    FROM lecturas l
                    WHERE l.contador_id = contadores.id
                    ORDER BY l.fecha_lectura DESC, l.id DESC
                    LIMIT 1
                ) AS lectura_anterior
            ')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->join('sectores', 'sectores.id = servicios.sector_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->where('contadores.activo', 1)
            ->where('servicios.estado', 'activo')
            ->where("NOT EXISTS (
                SELECT 1 FROM lecturas lp
                WHERE lp.contador_id = contadores.id
                  AND lp.periodo_id = " . $periodoId . "
            )", null, false);

        if ($busqueda !== '') {
            $builder->groupStart()
                ->like('contadores.numero_serie', $busqueda)
                ->orLike('clientes.nombre', $busqueda)
                ->orLike('servicios.direccion', $busqueda)
                ->orLike('sectores.nombre', $busqueda)
                ->groupEnd();
        }

        return $builder->orderBy('sectores.nombre', 'ASC')
            ->orderBy('contadores.numero_serie', 'ASC')
            ->findAll();
    }
}