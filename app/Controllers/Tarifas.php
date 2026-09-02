<?php

namespace App\Controllers;

use App\Models\TarifaModel;

class Tarifas extends BaseController
{
    private const TIPOS_VALIDOS = ['cuarto_paja', 'media_paja', 'paja_completa', 'exceso'];

    /**
     * Muestra el formulario para registrar una nueva tarifa.
     */
    public function create()
    {
        return view('tarifas/create', [
            'tipos' => self::TIPOS_VALIDOS,
        ]);
    }

    /**
     * Procesa el registro de una nueva tarifa (HU-04).
     * Criterios: monto numerico positivo, fecha de vigencia obligatoria,
     * no permite traslape de vigencias activas DEL MISMO TIPO.
     */
    public function store()
    {
        $rules = [
            'tipo'                     => 'required|in_list[' . implode(',', self::TIPOS_VALIDOS) . ']',
            'volumen_incluido_litros'  => 'permit_empty|is_natural',
            'precio_unitario'          => 'required|decimal|greater_than[0]',
            'cuota_minima'             => 'permit_empty|decimal|greater_than_equal_to[0]',
            'vigente_desde'            => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tipo         = $this->request->getPost('tipo');
        $vigenteDesde = $this->request->getPost('vigente_desde');

        $tarifaModel = new TarifaModel();

        // Regla: no permitir traslape de vigencias activas del mismo tipo.
        if ($tarifaModel->existeTraslape($tipo, $vigenteDesde)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una tarifa activa de este tipo vigente en esa fecha o posterior. Cierra su vigencia antes de registrar una nueva.');
        }

        // El tipo "exceso" no tiene volumen incluido (se cobra desde el litro 1 de exceso).
        $volumenIncluido = $tipo === 'exceso' ? null : ($this->request->getPost('volumen_incluido_litros') ?: null);

        $tarifaModel->insert([
            'tipo'                    => $tipo,
            'volumen_incluido_litros' => $volumenIncluido,
            'precio_unitario'         => $this->request->getPost('precio_unitario'),
            'cuota_minima'            => $this->request->getPost('cuota_minima') ?: 0,
            'vigente_desde'           => $vigenteDesde,
            'vigente_hasta'           => null,
            'activo'                  => 1,
        ]);

        return redirect()->to('/tarifas/nueva')
            ->with('mensaje', 'Tarifa registrada correctamente.');
    }

    /**
     * Muestra el historial de tarifas, con filtro opcional por rango de
     * fechas y por tipo. (HU-05)
     */
    public function historial()
    {
        $desde = $this->request->getGet('desde');
        $hasta = $this->request->getGet('hasta');
        $tipo  = $this->request->getGet('tipo');

        $tarifaModel = new TarifaModel();

        $tarifas         = $tarifaModel->historial($desde ?: null, $hasta ?: null, $tipo ?: null);
        $tarifasVigentes = $tarifaModel->tarifasVigentesPorTipo();

        return view('tarifas/historial', [
            'title'           => 'Historial de tarifas',
            'tarifas'         => $tarifas,
            'tarifasVigentes' => $tarifasVigentes,
            'tipos'           => self::TIPOS_VALIDOS,
            'filtros'         => [
                'desde' => $desde,
                'hasta' => $hasta,
                'tipo'  => $tipo,
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
     * Solo permite corregir el monto/cuota/volumen incluido, no el tipo ni
     * la fecha de inicio de vigencia (eso podria romper la logica de
     * traslape con otras tarifas del mismo tipo).
     */
    public function actualizar($id)
    {
        $tarifaModel = new TarifaModel();
        $tarifa = $tarifaModel->find($id);

        if (! $tarifa) {
            return redirect()->to('/tarifas/historial')->with('error', 'Tarifa no encontrada.');
        }

        $rules = [
            'precio_unitario'         => 'required|decimal|greater_than[0]',
            'cuota_minima'            => 'permit_empty|decimal|greater_than_equal_to[0]',
            'volumen_incluido_litros' => 'permit_empty|is_natural',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $volumenIncluido = $tarifa['tipo'] === 'exceso' ? null : ($this->request->getPost('volumen_incluido_litros') ?: null);

        $tarifaModel->update($id, [
            'precio_unitario'         => $this->request->getPost('precio_unitario'),
            'cuota_minima'            => $this->request->getPost('cuota_minima') ?: 0,
            'volumen_incluido_litros' => $volumenIncluido,
        ]);

        return redirect()->to('/tarifas/historial')
            ->with('mensaje', 'Tarifa actualizada correctamente.');
    }

    /**
     * Cierra la vigencia de una tarifa. (HU-06)
     * Criterios: registra la fecha de fin de vigencia; no afecta lecturas ya
     * calculadas (las lecturas guardan su propio tarifa_base_id/tarifa_exceso_id,
     * asi que siguen apuntando a esta tarifa aunque quede cerrada/inactiva).
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