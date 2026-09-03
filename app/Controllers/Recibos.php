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
            'title'    => 'Recibo ' . $recibo['numero'],
            'recibo'   => $recibo,
            'etiqueta' => $this->periodos->etiqueta([
                'anio' => $recibo['periodo_anio'],
                'mes'  => $recibo['periodo_mes'],
            ]),
        ]);
    }
}