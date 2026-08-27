<?php

namespace App\Controllers;

use App\Models\TarifaModel;

class Tarifas extends BaseController
{
    /**
     * Muestra el formulario para registrar una nueva tarifa.
     */
    public function create()
    {
        return view('tarifas/create');
    }

    /**
     * Procesa el registro de una nueva tarifa (HU-04).
     * Criterios: monto numerico positivo, fecha de vigencia obligatoria,
     * no permite traslape de vigencias activas.
     */
    public function store()
    {
        $rules = [
            'precio_unitario' => 'required|decimal|greater_than[0]',
            'cuota_minima'    => 'permit_empty|decimal|greater_than_equal_to[0]',
            'vigente_desde'   => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $vigenteDesde = $this->request->getPost('vigente_desde');

        $tarifaModel = new TarifaModel();

        // Regla: no permitir traslape de vigencias activas.
        if ($tarifaModel->existeTraslape($vigenteDesde)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una tarifa activa vigente en esa fecha o posterior. Cierra su vigencia antes de registrar una nueva.');
        }

        $tarifaModel->insert([
            'precio_unitario' => $this->request->getPost('precio_unitario'),
            'cuota_minima'    => $this->request->getPost('cuota_minima') ?: 0,
            'vigente_desde'   => $vigenteDesde,
            'vigente_hasta'   => null,
            'activo'          => 1,
        ]);

        return redirect()->to('/tarifas/nueva')
            ->with('mensaje', 'Tarifa registrada correctamente.');
    }
}