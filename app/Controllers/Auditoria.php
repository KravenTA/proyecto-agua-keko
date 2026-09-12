<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;

class Auditoria extends BaseController
{
    protected AuditoriaModel $auditoria;

    public function __construct()
    {
        $this->auditoria = new AuditoriaModel();
    }

    public function index()
    {
        $filtros = [
            'tabla' => $this->request->getGet('tabla') ?? '',
        ];

        return view('auditoria/index', [
            'title'      => 'Auditoría',
            'registros'  => $this->auditoria->listarConUsuario($filtros),
            'filtros'    => $filtros,
        ]);
    }
}