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

     /**
     * Muestra el historial de tarifas, con filtro opcional por rango de fechas. (HU-05)
     */
    public function historial()
    {
        $desde = $this->request->getGet('desde');
        $hasta = $this->request->getGet('hasta');

        $tarifaModel = new TarifaModel();

        $tarifas       = $tarifaModel->historial($desde ?: null, $hasta ?: null);
        $tarifaVigente = $tarifaModel->tarifaVigente();

        return view('tarifas/historial', [
            'title'         => 'Historial de tarifas',
            'tarifas'       => $tarifas,
            'tarifaVigente' => $tarifaVigente,
            'filtros'       => [
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    /**
     * Formulario para editar una tarifa existente. (HU-06)
     */
    public function editar($id)
    {
        $tarifaModel = new TarifaModel();
        $tarifa = $tarifaModel->find($id);

        if (! $tarifa) {
            return redirect()->to('/tarifas/historial')->with('error', 'Tarifa no encontrada.');
        }

        return view('tarifas/editar', [
            'title'  => 'Editar tarifa',
            'tarifa' => $tarifa,
        ]);
    }

    /**
     * Procesa la edicion de una tarifa. (HU-06)
     * Solo permite corregir el monto/cuota, no la fecha de inicio de vigencia
     * (eso podria romper la logica de traslape con otras tarifas).
     */
    public function actualizar($id)
    {
        $tarifaModel = new TarifaModel();
        $tarifa = $tarifaModel->find($id);

        if (! $tarifa) {
            return redirect()->to('/tarifas/historial')->with('error', 'Tarifa no encontrada.');
        }

        $rules = [
            'precio_unitario' => 'required|decimal|greater_than[0]',
            'cuota_minima'    => 'permit_empty|decimal|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tarifaModel->update($id, [
            'precio_unitario' => $this->request->getPost('precio_unitario'),
            'cuota_minima'    => $this->request->getPost('cuota_minima') ?: 0,
        ]);

        return redirect()->to('/tarifas/historial')
            ->with('mensaje', 'Tarifa actualizada correctamente.');
    }

    /**
     * Cierra la vigencia de una tarifa. (HU-06)
     * Criterios: registra la fecha de fin de vigencia; no afecta lecturas ya
     * calculadas (las lecturas guardan su propio tarifa_id, asi que siguen
     * apuntando a esta tarifa aunque quede cerrada/inactiva).
     */
    public function cerrarVigencia($id)
    {
        $tarifaModel = new TarifaModel();
        $tarifa = $tarifaModel->find($id);

        if (! $tarifa) {
            return redirect()->to('/tarifas/historial')->with('error', 'Tarifa no encontrada.');
        }

        if (! $tarifa['activo'] || $tarifa['vigente_hasta']) {
            return redirect()->to('/tarifas/historial')->with('error', 'Esta tarifa ya tiene su vigencia cerrada.');
        }

        $fechaCierre = $this->request->getPost('fecha_cierre') ?: date('Y-m-d');

        if ($fechaCierre < $tarifa['vigente_desde']) {
            return redirect()->back()
                ->with('error', 'La fecha de cierre no puede ser anterior a la fecha en que la tarifa comenzo a regir.');
        }

        $tarifaModel->update($id, [
            'vigente_hasta' => $fechaCierre,
            'activo'        => 0,
        ]);

        return redirect()->to('/tarifas/historial')
            ->with('mensaje', 'Vigencia de la tarifa cerrada correctamente.');
    }
}