<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\ReciboModel;

class Pagos extends BaseController
{
    protected $pagoModel;
    protected $reciboModel;

    public function __construct()
    {
        $this->pagoModel   = new PagoModel();
        $this->reciboModel = new ReciboModel();
    }

    /**
     * Pantalla "Pagos pendientes": recibos con estado = 'pendiente'.
     */
    public function index()
    {
        $data['pendientes'] = $this->reciboModel->listar(['estado' => 'pendiente'])->findAll();

        return view('pagos/pendientes', $data);
    }

    /**
     * Formulario para registrar el pago de un recibo pendiente.
     */
    public function nuevo($reciboId = null)
    {
        if (! is_numeric($reciboId) || (int) $reciboId <= 0) {
            return redirect()->to('/pagos')
                ->with('errores', ['Recibo no válido.']);
        }

        $recibo = $this->reciboModel->obtenerParaImprimir((int) $reciboId);

        if (! $recibo || $recibo['estado'] !== 'pendiente') {
            return redirect()->to('/pagos')
                ->with('errores', ['Ese recibo no existe o ya fue pagado.']);
        }

        $data['recibo'] = $recibo;

        return view('pagos/form', $data);
    }

    /**
     * Guarda el pago.
     */
    public function crear()
    {
        $reglas = [
            'lectura_id' => 'required|integer|is_unique[pagos.lectura_id]',
            'recibo_id'  => 'required|integer',
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
            (int) $this->request->getPost('recibo_id'),
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