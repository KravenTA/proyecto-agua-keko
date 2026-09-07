<?php

namespace App\Controllers;

use App\Models\PeriodoModel;

/**
 * SDGODA-62: Mantenimiento de periodos. Solo Administrador.
 *
 * Un periodo agrupa las lecturas de un mes. El sistema toma como "actual"
 * el que este abierto, asi que sin un periodo abierto no se pueden
 * registrar lecturas.
 */
class Periodos extends BaseController
{
    protected PeriodoModel $periodos;

    public function __construct()
    {
        $this->periodos = new PeriodoModel();
    }

    public function index()
    {
        $periodos = $this->periodos->listarTodos();

        // Se agrega el conteo de lecturas para que el admin vea de un vistazo
        // que tan avanzado esta cada periodo antes de cerrarlo.
        foreach ($periodos as &$p) {
            $p['total_lecturas'] = $this->periodos->totalLecturas((int) $p['id']);
            $p['sin_lectura']    = $this->periodos->contadoresSinLectura((int) $p['id']);
        }

        return view('periodos/index', [
            'title'    => 'Periodos',
            'periodos' => $periodos,
            'abierto'  => $this->periodos->periodoActual(),
        ]);
    }

    /**
     * Crea un periodo nuevo. Si se pide abrirlo, cierra el que estuviera
     * abierto: solo puede haber uno a la vez, o periodoActual() tomaria
     * el mas reciente y el lector registraria en el mes equivocado.
     */
    public function crear()
    {
        $anio = (int) $this->request->getPost('anio');
        $mes  = (int) $this->request->getPost('mes');

        if ($anio < 2020 || $anio > 2100) {
            return redirect()->back()->withInput()
                ->with('errores', ['El anio no es valido.']);
        }

        if ($mes < 1 || $mes > 12) {
            return redirect()->back()->withInput()
                ->with('errores', ['El mes no es valido.']);
        }

        if ($this->periodos->existe($anio, $mes)) {
            return redirect()->back()->withInput()
                ->with('errores', ['Ya existe un periodo para ese mes y año.']);
        }

        $abrir = (bool) $this->request->getPost('abrir');

        if ($abrir) {
            $this->cerrarElAbierto();
        }

        $this->periodos->insert([
            'anio'   => $anio,
            'mes'    => $mes,
            'estado' => $abrir ? 'abierto' : 'cerrado',
        ]);

        return redirect()->to('/periodos')
            ->with('exito', 'Periodo creado' . ($abrir ? ' y abierto.' : '.'));
    }

    /**
     * Abre un periodo cerrado, cerrando el que estuviera abierto.
     */
    public function abrir($id = null)
    {
        $id      = (int) $id;
        $periodo = $this->periodos->find($id);

        if (! $periodo) {
            return redirect()->to('/periodos')->with('errores', ['Ese periodo no existe.']);
        }

        if ($periodo['estado'] === 'abierto') {
            return redirect()->to('/periodos')->with('errores', ['Ese periodo ya esta abierto.']);
        }

        $this->cerrarElAbierto();
        $this->periodos->update($id, ['estado' => 'abierto']);

        return redirect()->to('/periodos')->with('exito',
            'Periodo ' . $this->periodos->etiqueta($periodo) . ' abierto.');
    }

    /**
     * Cierra un periodo. Advierte si quedan contadores sin leer, pero no lo
     * impide: puede haber predios inaccesibles y la oficina necesita cerrar.
     */
    public function cerrar($id = null)
    {
        $id      = (int) $id;
        $periodo = $this->periodos->find($id);

        if (! $periodo) {
            return redirect()->to('/periodos')->with('errores', ['Ese periodo no existe.']);
        }

        if ($periodo['estado'] === 'cerrado') {
            return redirect()->to('/periodos')->with('errores', ['Ese periodo ya esta cerrado.']);
        }

        $sinLectura = $this->periodos->contadoresSinLectura($id);

        $this->periodos->update($id, ['estado' => 'cerrado']);

        $mensaje = 'Periodo ' . $this->periodos->etiqueta($periodo) . ' cerrado.';

        if ($sinLectura > 0) {
            $mensaje .= ' Quedaron ' . $sinLectura . ' contador(es) sin lectura: '
                . 'esos clientes no tendran recibo de este periodo.';
        }

        return redirect()->to('/periodos')->with('exito', $mensaje);
    }

    private function cerrarElAbierto(): void
    {
        $abierto = $this->periodos->periodoActual();

        if ($abierto) {
            $this->periodos->update($abierto['id'], ['estado' => 'cerrado']);
        }
    }
}