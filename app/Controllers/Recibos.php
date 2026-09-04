<?php

namespace App\Controllers;

use App\Models\ReciboModel;
use App\Models\PeriodoModel;

/**
 * SDGODA-47: Recibos emitidos. Consulta e impresion desde la oficina.
 */
class Recibos extends BaseController
{
    protected ReciboModel $recibos;
    protected PeriodoModel $periodos;

    public function __construct()
    {
        $this->recibos  = new ReciboModel();
        $this->periodos = new PeriodoModel();
    }

    public function index()
    {
        $filtros = [
            'busqueda'   => trim((string) $this->request->getGet('q')),
            'periodo_id' => $this->request->getGet('periodo_id') ?? '',
            'estado'     => $this->request->getGet('estado') ?? '',
        ];

        $recibos = $this->recibos->listar($filtros)->paginate(15);

        return view('recibos/index', [
            'title'    => 'Recibos',
            'recibos'  => $recibos,
            'pager'    => $this->recibos->pager,
            'periodos' => $this->periodos->listarTodos(),
            'filtros'  => $filtros,
        ]);
    }

    /**
     * Devuelve solo la tabla, para actualizarla por AJAX sin recargar.
     */
    public function tabla()
    {
        $filtros = [
            'busqueda'   => trim((string) $this->request->getGet('q')),
            'periodo_id' => $this->request->getGet('periodo_id') ?? '',
            'estado'     => $this->request->getGet('estado') ?? '',
        ];

        $recibos = $this->recibos->listar($filtros)->paginate(15);

        return view('recibos/_tabla', [
            'recibos' => $recibos,
            'pager'   => $this->recibos->pager,
            'filtros' => $filtros,
        ]);
    }

    /**
     * Recibo imprimible. Se identifica por el id del recibo, no por el de
     * la lectura, para que el numero de recibo sea el dato de referencia.
     */
    public function ver($reciboId = null)
    {
        $recibo = $this->recibos->obtenerParaImprimir((int) $reciboId);

        if (! $recibo) {
            return redirect()->to('/recibos')
                ->with('errores', ['No se encontro ese recibo.']);
        }

        return view('recibos/imprimible', [
            'title'           => 'Recibo ' . $recibo['numero'],
            'recibo'          => $recibo,
            'etiqueta'        => $this->periodos->etiqueta([
                'anio' => $recibo['periodo_anio'],
                'mes'  => $recibo['periodo_mes'],
            ]),
            // SDGODA-48: mismo dato que muestra el dashboard (HU-18) para
            // este cliente, para que los dos queden alineados.
            'meses_pendientes' => $this->recibos->mesesPendientesDelCliente((int) $recibo['cliente_id']),
        ]);
    }
}