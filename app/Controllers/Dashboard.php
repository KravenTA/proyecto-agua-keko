<?php

namespace App\Controllers;

use App\Models\ClienteModel;

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
        // Whitelist tambien aca, aunque el modelo ya valida: asi el valor
        // que viaja en $filtros['orden'] hacia la vista es siempre uno de
        // los dos permitidos (para pintar la flecha correcta).
        $ordenesPermitidos = ['nombre', 'meses_pendientes'];
        $orden = $this->request->getGet('orden');
        if (! in_array($orden, $ordenesPermitidos, true)) {
            $orden = 'nombre';
        }

        $direccion = strtoupper((string) $this->request->getGet('direccion')) === 'DESC'
            ? 'DESC'
            : 'ASC';

        return [
            'busqueda'  => trim((string) $this->request->getGet('q')),
            'estado'    => $this->request->getGet('estado') ?? '',
            'orden'     => $orden,
            'direccion' => $direccion,
        ];
    }
}