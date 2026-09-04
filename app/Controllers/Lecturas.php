<?php

namespace App\Controllers;

use App\Models\ContadorModel;
use App\Models\PeriodoModel;
use App\Models\LecturaModel;
use App\Models\TarifaModel;
use App\Models\ReciboModel;

/**
 * HU-12: Ver contadores pendientes de lectura del periodo (SDGODA-27).
 * HU-14: Ingresar lectura de contador desde celular (SDGODA-37).
 * HU-15: Calcular consumo y aplicar tarifa vigente (SDGODA-38).
 */
class Lecturas extends BaseController
{
    protected ContadorModel $contadores;
    protected PeriodoModel $periodos;
    protected LecturaModel $lecturas;
    protected TarifaModel $tarifas;
    protected ReciboModel $recibos;

    public function __construct()
    {
        $this->contadores = new ContadorModel();
        $this->periodos   = new PeriodoModel();
        $this->lecturas   = new LecturaModel();
        $this->tarifas    = new TarifaModel();
        $this->recibos    = new ReciboModel();
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

    /**
     * Formulario para ingresar la lectura de un contador. (HU-14)
     */
    public function registrar($contadorId = null)
    {
        $contadorId = (int) $contadorId;
        $periodo    = $this->periodos->periodoActual();

        if (! $periodo) {
            return redirect()->to('/lecturas/pendientes')
                ->with('errores', ['No hay ningun periodo abierto.']);
        }

        $contador = $this->contadores->obtenerParaLectura($contadorId, (int) $periodo['id']);

        if (! $contador) {
            return redirect()->to('/lecturas/pendientes')->with('errores', [
                'Ese contador no esta pendiente de lectura en este periodo.',
            ]);
        }

        return view('lecturas/registrar', [
            'title'    => 'Registrar lectura',
            'contador' => $contador,
            'periodo'  => $periodo,
            'etiqueta' => $this->periodos->etiqueta($periodo),
        ]);
    }

    /**
     * Guarda la lectura ingresada por el lector, calculando el consumo y el
     * monto con la tarifa vigente. (HU-14 / HU-15)
     *
     * La tarifa se guarda en la lectura para que el recibo siga apuntando a
     * la que se aplico, aunque despues se cierre su vigencia.
     */
    public function guardar($contadorId = null)
    {
        $contadorId = (int) $contadorId;
        $periodo    = $this->periodos->periodoActual();

        if (! $periodo) {
            return redirect()->to('/lecturas/pendientes')
                ->with('errores', ['No hay ningun periodo abierto.']);
        }

        $contador = $this->contadores->obtenerParaLectura($contadorId, (int) $periodo['id']);

        if (! $contador) {
            return redirect()->to('/lecturas/pendientes')->with('errores', [
                'Ese contador ya tiene lectura registrada en este periodo, o no esta disponible.',
            ]);
        }

        $anterior = (float) ($contador['lectura_anterior'] ?? $contador['lectura_inicial'] ?? 0);
        $actual   = $this->request->getPost('lectura_actual');

        if ($actual === null || $actual === '' || ! is_numeric($actual)) {
            return redirect()->back()->withInput()
                ->with('errores', ['Escribe la lectura actual.']);
        }

        $actual = (float) $actual;

        if ($actual < $anterior) {
            return redirect()->back()->withInput()->with('errores', [
                'La lectura actual (' . $actual . ') no puede ser menor que la anterior ('
                . $anterior . '). Si el contador fue reemplazado, da de baja este contador '
                . 'y registra el nuevo desde el modulo de Contadores.',
            ]);
        }

        $fecha  = date('Y-m-d');
        $tarifa = $this->tarifas->tarifaVigente((string) $contador['tipo_servicio'], $fecha);

        if (! $tarifa) {
            return redirect()->back()->withInput()->with('errores', [
                'No hay una tarifa vigente para el tipo de servicio de este contador. '
                . 'Avisa en la oficina antes de continuar.',
            ]);
        }

        $consumo = $actual - $anterior;
        $monto   = $this->tarifas->calcularMonto($tarifa, $consumo, $fecha);

        if ($monto === null) {
            return redirect()->back()->withInput()->with('errores', [
                'El consumo excede el volumen incluido pero no hay una tarifa de exceso '
                . 'vigente. Avisa en la oficina antes de continuar.',
            ]);
        }

        $this->lecturas->insert([
            'servicio_id'       => $contador['servicio_id'],
            'contador_id'       => $contador['id'],
            'periodo_id'        => $periodo['id'],
            'tarifa_id'         => $tarifa['id'],
            'lector_usuario_id' => session()->get('usuario_id'),
            'lectura_anterior'  => $anterior,
            'lectura_actual'    => $actual,
            'consumo'           => $consumo,
            'monto'             => $monto,
            'fecha_lectura'     => $fecha,
        ]);

        $lecturaId = $this->lecturas->getInsertID();
        $recibo    = $this->recibos->emitirPorLectura($lecturaId, $monto);

        return redirect()->to('/recibos/ver/' . $recibo['id']);
    }
}
