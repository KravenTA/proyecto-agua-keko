<?php

namespace App\Controllers;

use App\Models\ClienteModel;

/**
 * HU-18: Dashboard de estado de cuenta (SDGODA-41).
 */
class Dashboard extends BaseController
{
    protected ClienteModel $clientes;

    public function __construct()
    {
        $this->clientes = new ClienteModel();
    }

    public function index()
    {
        $filtros = $this->filtrosDesdeGet();

        return view('dashboard/index', [
            'title'    => 'Dashboard',
            'clientes' => $this->clientes->estadoDeCuenta($filtros),
            'filtros'  => $filtros,
        ]);
    }

    /**
     * Devuelve solo la tabla, para actualizarla por AJAX sin recargar
     * (mismo patron de recibos/tabla y clientes/tabla).
     */
    public function tabla()
    {
        $filtros = $this->filtrosDesdeGet();

        return view('dashboard/_tabla', [
            'clientes' => $this->clientes->estadoDeCuenta($filtros),
            'filtros'  => $filtros,
        ]);
    }

    private function filtrosDesdeGet(): array
    {
        return [
            'busqueda' => trim((string) $this->request->getGet('q')),
            'estado'   => $this->request->getGet('estado') ?? '',
        ];
    }
}