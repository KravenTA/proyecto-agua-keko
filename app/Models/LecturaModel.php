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
     * Obtiene una lectura con todos los datos necesarios
     * para generar el recibo imprimible.
     */
    public function obtenerParaRecibo(int $lecturaId): ?array
    {
        return $this->select('
                lecturas.*,
                clientes.nombre AS cliente_nombre,
                clientes.telefono AS cliente_telefono,
                servicios.codigo AS servicio_codigo,
                servicios.direccion AS direccion,
                contadores.numero_serie AS numero_contador,
                contadores.tipo_servicio AS tipo_servicio,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes,
                tarifas.tipo AS tarifa_tipo,
                tarifas.volumen_incluido_litros,
                tarifas.precio_unitario,
                tarifas.cuota_minima
            ')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('tarifas', 'tarifas.id = lecturas.tarifa_id')
            ->where('lecturas.id', $lecturaId)
            ->first();
    }

    /**
     * Lecturas que todavía NO tienen un pago registrado —
     * para la pantalla de "pendientes de pago".
     */
    public function pendientesDePago(): array
    {
        return $this->select('
                lecturas.id AS lectura_id,
                clientes.nombre AS cliente_nombre,
                servicios.codigo AS servicio_codigo,
                contadores.numero_serie AS numero_contador,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes,
                lecturas.fecha_lectura,
                lecturas.monto
            ')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('pagos', 'pagos.lectura_id = lecturas.id', 'left')
            ->where('pagos.id', null)
            ->orderBy('lecturas.fecha_lectura', 'ASC')
            ->findAll();
    }

    /**
     * Trae una lectura específica solo si existe y todavía
     * NO tiene un pago registrado (para el formulario de "nuevo pago").
     */
    public function obtenerLecturaPendiente(int $lecturaId): ?array
    {
        return $this->select('
                lecturas.id AS lectura_id,
                clientes.nombre AS cliente_nombre,
                servicios.codigo AS servicio_codigo,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes,
                lecturas.monto
            ')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('pagos', 'pagos.lectura_id = lecturas.id', 'left')
            ->where('lecturas.id', $lecturaId)
            ->where('pagos.id', null)
            ->first();
    }
}