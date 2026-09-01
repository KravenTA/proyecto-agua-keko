<?php

namespace App\Controllers;

use App\Models\ContadorModel;
use App\Models\PeriodoModel;

/**
 * HU-12: Ver contadores pendientes de lectura del periodo (SDGODA-27).
 */
class Lecturas extends BaseController
{
    protected ContadorModel $contadores;
    protected PeriodoModel $periodos;

    public function __construct()
    {
        $this->contadores = new ContadorModel();
        $this->periodos   = new PeriodoModel();
    }

    public function pendientes()
    {
        $periodo = $this->periodos->periodoActual();

        if (! $periodo) {
            return view('lecturas/pendientes', [
                'title'      => 'Pendientes de lectura',
                'periodo'    => null,
                'etiqueta'   => '',
                'pendientes' => [],
                'busqueda'   => '',
            ]);
        }

        $busqueda = trim((string) $this->request->getGet('q'));

        return view('lecturas/pendientes', [
            'title'      => 'Pendientes de lectura',
            'periodo'    => $periodo,
            'etiqueta'   => $this->periodos->etiqueta($periodo),
            'pendientes' => $this->contadores->pendientesDeLectura((int) $periodo['id'], $busqueda),
            'busqueda'   => $busqueda,
        ]);
    }
}