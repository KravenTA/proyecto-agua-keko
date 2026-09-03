<?php

namespace App\Models;

use CodeIgniter\Model;

class ReciboModel extends Model
{
    protected $table         = 'recibos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'lectura_id', 'numero', 'fecha_emision', 'monto_consumo',
        'monto_adicional', 'concepto_adicional', 'total', 'estado',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Genera el siguiente numero de recibo. Formato REC-000001.
     * El campo numero es UNIQUE, asi que dos emisiones simultaneas harian
     * fallar el insert en vez de duplicar el numero.
     */
    public function siguienteNumero(): string
    {
        $ultimo = $this->select('numero')
            ->orderBy('id', 'DESC')
            ->first();

        $correlativo = 1;

        if ($ultimo && preg_match('/(\d+)$/', $ultimo['numero'], $m)) {
            $correlativo = ((int) $m[1]) + 1;
        }

        return 'REC-' . str_pad((string) $correlativo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Emite el recibo de una lectura. Si ya existe, devuelve el que hay:
     * una lectura tiene un solo recibo.
     */
    public function emitirPorLectura(int $lecturaId, float $montoConsumo): array
    {
        $existente = $this->where('lectura_id', $lecturaId)->first();

        if ($existente) {
            return $existente;
        }

        $id = $this->insert([
            'lectura_id'      => $lecturaId,
            'numero'          => $this->siguienteNumero(),
            'fecha_emision'   => date('Y-m-d H:i:s'),
            'monto_consumo'   => $montoConsumo,
            'monto_adicional' => 0,
            'total'           => $montoConsumo,
            'estado'          => 'pendiente',
        ], true);

        return $this->find($id);
    }

    /**
     * Un recibo con todos los datos para imprimirlo. (SDGODA-39 / SDGODA-47)
     */
    public function obtenerParaImprimir(int $reciboId): ?array
    {
        return $this->select('
                recibos.*,
                lecturas.lectura_anterior,
                lecturas.lectura_actual,
                lecturas.consumo,
                lecturas.monto,
                lecturas.fecha_lectura,
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
            ->join('lecturas', 'lecturas.id = recibos.lectura_id')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('tarifas', 'tarifas.id = lecturas.tarifa_id')
            ->where('recibos.id', $reciboId)
            ->first();
    }
}