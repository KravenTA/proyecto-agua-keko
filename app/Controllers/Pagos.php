<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\LecturaModel;

class Pagos extends BaseController
{
    protected $pagoModel;
    protected $lecturaModel;

    public function __construct()
    {
        $this->pagoModel    = new PagoModel();
        $this->lecturaModel = new LecturaModel();
    }

    /**
     * Pantalla "Pagos pendientes": lista las lecturas que todavía no
     * tienen un pago registrado.
     */
    public function index()
    {
        $data['pendientes'] = $this->lecturaModel->pendientesDePago();

        return view('pagos/pendientes', $data);
    }

    /**
     * Formulario para registrar el pago de una lectura específica.
     */
    public function nuevo($lecturaId = null)
    {
        if (! is_numeric($lecturaId) || (int) $lecturaId <= 0) {
            return redirect()->to('/pagos')
                ->with('errores', ['Lectura no válida.']);
        }

        $lectura = $this->lecturaModel->obtenerLecturaPendiente((int) $lecturaId);

        if (! $lectura) {
            return redirect()->to('/pagos')
                ->with('errores', ['Esa lectura no existe o ya tiene un pago registrado.']);
        }

        $data['lectura'] = $lectura;

        return view('pagos/form', $data);
    }

    /**
     * Guarda el pago.
     */
    public function crear()
    {
        $reglas = [
            'lectura_id' => 'required|integer|is_unique[pagos.lectura_id]',
            'monto'      => 'required|decimal|greater_than[0]',
            'fecha_pago' => 'required|valid_date',
            'metodo'     => 'required|max_length[30]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $usuarioId = session()->get('usuario_id');

        $pagoId = $this->pagoModel->registrarPago(
            (int) $this->request->getPost('lectura_id'),
            (float) $this->request->getPost('monto'),
            $this->request->getPost('fecha_pago'),
            $this->request->getPost('metodo'),
            (int) $usuarioId,
            $this->request->getPost('referencia')
        );

        if ($pagoId === false) {
            return redirect()->back()
                ->withInput()
                ->with('errores', ['No se pudo registrar el pago. Intenta de nuevo.']);
        }

        return redirect()->to('/pagos')
            ->with('exito', 'Pago registrado correctamente.');
    }
}