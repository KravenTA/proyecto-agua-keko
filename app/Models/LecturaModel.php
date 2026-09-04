<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturaModel extends Model
{
    protected $table         = 'lecturas';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'servicio_id',
        'contador_id',
        'periodo_id',
        'tarifa_id',
        'lector_usuario_id',
        'lectura_anterior',
        'lectura_actual',
        'consumo',
        'monto',
        'fecha_lectura',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Listado de lecturas registradas, con filtros y paginacion. (SDGODA-49)
     * $filtros acepta: busqueda, periodo_id, sector_id.
     */
    public function listarFiltrado(array $filtros = [])
    {
        $builder = $this->select('
                lecturas.id,
                lecturas.lectura_anterior,
                lecturas.lectura_actual,
                lecturas.consumo,
                lecturas.monto,
                lecturas.fecha_lectura,
                contadores.numero_serie AS numero_contador,
                clientes.nombre AS cliente_nombre,
                sectores.nombre AS sector_nombre,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes,
                usuarios.nombre AS lector_nombre,
                recibos.id AS recibo_id,
                recibos.numero AS recibo_numero
            ')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('sectores', 'sectores.id = servicios.sector_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('usuarios', 'usuarios.id = lecturas.lector_usuario_id', 'left')
            ->join('recibos', 'recibos.lectura_id = lecturas.id', 'left');

        if (! empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('contadores.numero_serie', $filtros['busqueda'])
                ->orLike('clientes.nombre', $filtros['busqueda'])
                ->orLike('recibos.numero', $filtros['busqueda'])
                ->groupEnd();
        }

        if (! empty($filtros['periodo_id'])) {
            $builder->where('lecturas.periodo_id', $filtros['periodo_id']);
        }

        if (! empty($filtros['sector_id'])) {
            $builder->where('servicios.sector_id', $filtros['sector_id']);
        }

        return $builder->orderBy('lecturas.fecha_lectura', 'DESC')
            ->orderBy('lecturas.id', 'DESC');
    }
}