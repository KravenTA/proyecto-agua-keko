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

    public function index()
    {
        $filtros = $this->filtrosDesdeRequest();

        $pendientes = $this->reciboModel->listar($filtros)->paginate(15);

        return view('pagos/pendientes', [
            'title'      => 'Pagos pendientes',
            'pendientes' => $pendientes,
            'pager'      => $this->reciboModel->pager,
            'periodos'   => (new \App\Models\PeriodoModel())->listarTodos(),
            'filtros'    => $filtros,
        ]);
    }

    /**
     * Devuelve solo la tabla, para actualizarla por AJAX sin recargar. (SDGODA-49)
     */
    public function tabla()
    {
        $filtros = $this->filtrosDesdeRequest();

        $pendientes = $this->reciboModel->listar($filtros)->paginate(15);

        return view('pagos/_tabla', [
            'pendientes' => $pendientes,
            'pager'      => $this->reciboModel->pager,
            'filtros'    => $filtros,
        ]);
    }

    /**
     * El estado siempre queda en 'pendiente': esta pantalla es para cobrar,
     * no para consultar el historial. Ese es el listado de recibos.
     */
    private function filtrosDesdeRequest(): array
    {
        return [
            'busqueda'   => trim((string) $this->request->getGet('q')),
            'periodo_id' => $this->request->getGet('periodo_id') ?? '',
            'estado'     => 'pendiente',
        ];
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