<?php

namespace App\Models;

use CodeIgniter\Model;

class ReciboModel extends Model
{
    protected $table         = 'recibos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'lectura_id', 'numero', 'fecha_emision', 'fecha_vencimiento',
        'monto_consumo', 'monto_adicional', 'concepto_adicional',
        'total', 'estado',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Genera el siguiente numero de recibo. Formato REC-000001.
     * El campo numero es UNIQUE, asi que dos emisiones simultaneas harian
     * fallar el insert en vez de duplicar el numero.
     *
     * Calcula el siguiente numero de recibo a partir del correlativo mas
     * alto que exista realmente (no de la ultima fila insertada).
     *
     * FIX: la version anterior usaba ORDER BY id DESC, asumiendo que la
     * fila con el id mas alto siempre tenia el numero mas alto. Si esa fila
     * quedaba "atrasada" (por ejemplo, porque una emision anterior fallo a
     * mitad de camino, o se cargaron datos de prueba fuera de orden), el
     * calculo devolvia un numero que ya existia y el insert reventaba por
     * la restriccion UNIQUE de la columna numero.
     */
    public function siguienteNumero(): string
    {
        $ultimo = $this->select("numero, CAST(SUBSTRING(numero, 5) AS UNSIGNED) AS correlativo")
            ->orderBy('correlativo', 'DESC')
            ->first();

        $correlativo = ((int) ($ultimo['correlativo'] ?? 0)) + 1;

        return 'REC-' . str_pad((string) $correlativo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Emite el recibo de una lectura. Si ya existe, devuelve el que hay:
     * una lectura tiene un solo recibo.
     *
     * FIX: se agrega un reintento acotado por si, aun con el correlativo
     * bien calculado, dos emisiones casi simultaneas leen el mismo
     * "ultimo numero" antes de que la primera termine de insertar. En ese
     * caso el UNIQUE de la BD rechaza el segundo insert; en vez de dejar
     * pasar esa excepcion hacia arriba (lo que dejaba la lectura guardada
     * pero sin su recibo), se vuelve a calcular el numero y se reintenta.
     */
    public function emitirPorLectura(int $lecturaId, float $montoConsumo): array
    {
        $existente = $this->where('lectura_id', $lecturaId)->first();

        if ($existente) {
            return $existente;
        }

        $fechaEmision = date('Y-m-d H:i:s');
        $intentosRestantes = 3;

        while (true) {
            try {
                $id = $this->insert([
                    'lectura_id'        => $lecturaId,
                    'numero'            => $this->siguienteNumero(),
                    'fecha_emision'     => $fechaEmision,
                    // SDGODA-53: 15 dias de plazo despues de la emision.
                    'fecha_vencimiento' => date('Y-m-d', strtotime($fechaEmision . ' +15 days')),
                    'monto_consumo'     => $montoConsumo,
                    'monto_adicional'   => 0,
                    'total'             => $montoConsumo,
                    'estado'            => 'pendiente',
                ], true);

                return $this->find($id);
            } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
                $esDuplicadoDeNumero = stripos($e->getMessage(), 'numero') !== false
                    && stripos($e->getMessage(), 'duplicate') !== false;

                $intentosRestantes--;

                if (! $esDuplicadoDeNumero || $intentosRestantes <= 0) {
                    throw $e;
                }
                // Vuelve al inicio del while y recalcula siguienteNumero().
            }
        }
    }

    /**
     * Un recibo con todos los datos para imprimirlo. (SDGODA-39 / SDGODA-47)
     *
     * SDGODA-53: se agrega el sector/zona del servicio (via sectores) y el
     * nit/dpi del cliente, para que el recibo se parezca a la factura de
     * referencia del ingeniero.
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
                clientes.id AS cliente_id,
                clientes.nombre AS cliente_nombre,
                clientes.telefono AS cliente_telefono,
                clientes.nit AS cliente_nit,
                clientes.dpi AS cliente_dpi,
                servicios.codigo AS servicio_codigo,
                servicios.direccion AS direccion,
                sectores.nombre AS sector_nombre,
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
            ->join('sectores', 'sectores.id = servicios.sector_id')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('tarifas', 'tarifas.id = lecturas.tarifa_id')
            ->where('recibos.id', $reciboId)
            ->first();
    }

    /**
     * Cuantos recibos pendientes de pago tiene un cliente (SDGODA-48).
     *
     * Misma definicion de "pendiente" que ClienteModel::estadoDeCuenta()
     * (recibos.estado = 'pendiente') para que el recibo (HU-16) y el
     * dashboard (HU-18) muestren siempre el mismo numero para un cliente.
     * Incluye el recibo que se esta consultando/imprimiendo, si todavia
     * no esta pagado.
     */
    public function mesesPendientesDelCliente(int $clienteId): int
    {
        return $this->join('lecturas', 'lecturas.id = recibos.lectura_id')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->where('servicios.cliente_id', $clienteId)
            ->where('recibos.estado', 'pendiente')
            ->countAllResults();
    }

    /**
     * Recibos pendientes de un cliente, con el detalle de periodo, lecturas
     * y tarifa de cada uno, para el desglose mes a mes del recibo imprimible
     * (SDGODA-53), igual al formato de la factura de referencia.
     *
     * Misma definicion de "pendiente" que mesesPendientesDelCliente().
     */
    public function listarPendientesDelCliente(int $clienteId)
    {
        return $this->select('
                recibos.id,
                recibos.numero,
                recibos.total,
                recibos.estado,
                lecturas.lectura_anterior,
                lecturas.lectura_actual,
                lecturas.consumo,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes,
                tarifas.volumen_incluido_litros,
                tarifas.cuota_minima
            ')
            ->join('lecturas', 'lecturas.id = recibos.lectura_id')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id')
            ->join('tarifas', 'tarifas.id = lecturas.tarifa_id')
            ->where('servicios.cliente_id', $clienteId)
            ->where('recibos.estado', 'pendiente')
            ->orderBy('periodos.anio', 'ASC')
            ->orderBy('periodos.mes', 'ASC')
            ->findAll();
    }

    /**
     * Recibos emitidos, con datos del cliente y periodo, para consulta desde
     * la oficina. $filtros acepta: busqueda, periodo_id, estado.
     */
    public function listar(array $filtros = [])
    {
        $builder = $this->select('
                recibos.id,
                recibos.numero,
                recibos.fecha_emision,
                recibos.total,
                recibos.estado,
                clientes.nombre AS cliente_nombre,
                contadores.numero_serie AS numero_contador,
                periodos.anio AS periodo_anio,
                periodos.mes AS periodo_mes
            ')
            ->join('lecturas', 'lecturas.id = recibos.lectura_id')
            ->join('servicios', 'servicios.id = lecturas.servicio_id')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('periodos', 'periodos.id = lecturas.periodo_id');

        if (! empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('recibos.numero', $filtros['busqueda'])
                ->orLike('clientes.nombre', $filtros['busqueda'])
                ->orLike('contadores.numero_serie', $filtros['busqueda'])
                ->groupEnd();
        }

        if (! empty($filtros['periodo_id'])) {
            $builder->where('lecturas.periodo_id', $filtros['periodo_id']);
        }

        if (! empty($filtros['estado'])) {
            $builder->where('recibos.estado', $filtros['estado']);
        }

        return $builder->orderBy('recibos.id', 'DESC');
    }
}