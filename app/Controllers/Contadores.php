<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\ContadorModel;
use App\Models\SectorModel;
use App\Models\ServicioModel;

class Contadores extends BaseController
{
    protected ContadorModel $contadores;
    protected ServicioModel $servicios;
    protected ClienteModel $clientes;
    protected SectorModel $sectores;

    public function __construct()
    {
        $this->contadores = new ContadorModel();
        $this->servicios  = new ServicioModel();
        $this->clientes   = new ClienteModel();
        $this->sectores   = new SectorModel();
    }

    /**
     * Listado de contadores registrados, con su cliente y sector.
     */
    public function index()
    {
        return view('contadores/index', [
            'title'      => 'Contadores',
            'contadores' => $this->contadores->listarConDetalle(),
        ]);
    }

    /**
     * Formulario para registrar un nuevo contador/predio.
     */
    public function nuevo()
    {
        return view('contadores/form', [
            'title'    => 'Nuevo contador',
            'clientes' => $this->clientes->where('activo', 1)->orderBy('nombre', 'ASC')->findAll(),
            'sectores' => $this->sectores->where('activo', 1)->orderBy('nombre', 'ASC')->findAll(),
        ]);
    }

        /**
     * Procesa el registro de un contador y del predio (servicio) que lo
     * conecta con un cliente. (HU-10)
     *
     * Criterios de aceptacion:
     *  - El formulario exige un cliente asociado.
     *  - El codigo del contador es unico.
     *  - Se guarda el sector y la referencia del predio.
     */
    public function crear()
    {
        if (! $this->validate($this->reglasValidacion())) {
            return redirect()->back()->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $clienteId        = (int) $this->request->getPost('cliente_id');
        $sectorId         = (int) $this->request->getPost('sector_id');
        $referencia       = trim((string) $this->request->getPost('referencia'));
        $numeroSerie      = trim((string) $this->request->getPost('numero_serie'));
        $lecturaInicial   = $this->request->getPost('lectura_inicial');
        $fechaInstalacion = trim((string) $this->request->getPost('fecha_instalacion')) ?: date('Y-m-d');

        $db = db_connect();
        $db->transStart();

        // El "servicio" es el predio: liga al cliente con el sector.
        // Su codigo interno se deriva del numero de contador para
        // garantizar que sea unico sin pedirle al usuario un dato extra.
        $servicioId = $this->servicios->insert([
            'cliente_id' => $clienteId,
            'sector_id'  => $sectorId,
            'codigo'     => 'SRV-' . $numeroSerie,
            'direccion'  => $referencia !== '' ? $referencia : null,
            'fecha_alta' => date('Y-m-d'),
            'estado'     => 'activo',
        ]);

        if (! $servicioId) {
            $db->transRollback();

            return redirect()->back()->withInput()
                ->with('errores', $this->servicios->errors() ?: ['No se pudo registrar el predio del cliente.']);
        }

        $contadorId = $this->contadores->insert([
            'servicio_id'       => $servicioId,
            'numero_serie'      => $numeroSerie,
            'lectura_inicial'   => $lecturaInicial !== null && $lecturaInicial !== '' ? $lecturaInicial : 0,
            'fecha_instalacion' => $fechaInstalacion,
            'activo'            => 1,
        ]);

        if (! $contadorId) {
            $db->transRollback();

            return redirect()->back()->withInput()
                ->with('errores', $this->contadores->errors() ?: ['No se pudo registrar el contador.']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()
                ->with('errores', ['Ocurrio un error al guardar. Intenta de nuevo.']);
        }

        return redirect()->to('/contadores')
            ->with('exito', 'Contador "' . esc($numeroSerie) . '" registrado y asociado correctamente.');
    }


        private function reglasValidacion(): array
    {
        return [
            'cliente_id' => [
                'rules'  => 'required|is_natural_no_zero|is_not_unique[clientes.id]',
                'errors' => [
                    'required'           => 'Selecciona el cliente asociado.',
                    'is_natural_no_zero' => 'Selecciona el cliente asociado.',
                    'is_not_unique'      => 'El cliente seleccionado no existe.',
                ],
            ],
            'sector_id' => [
                'rules'  => 'required|is_natural_no_zero|is_not_unique[sectores.id]',
                'errors' => [
                    'required'           => 'Selecciona el sector del predio.',
                    'is_natural_no_zero' => 'Selecciona el sector del predio.',
                    'is_not_unique'      => 'El sector seleccionado no existe.',
                ],
            ],
            'referencia' => [
                'rules'  => 'permit_empty|max_length[150]',
                'errors' => [
                    'max_length' => 'La referencia es demasiado larga (max. 150 caracteres).',
                ],
            ],
            'numero_serie' => [
                'rules'  => 'required|max_length[30]|is_unique[contadores.numero_serie]',
                'errors' => [
                    'required'   => 'El codigo del contador es obligatorio.',
                    'max_length' => 'El codigo del contador es demasiado largo (max. 30 caracteres).',
                    'is_unique'  => 'Ya existe un contador registrado con ese codigo.',
                ],
            ],
            'lectura_inicial' => [
                'rules'  => 'permit_empty|decimal',
                'errors' => [
                    'decimal' => 'La lectura inicial debe ser un numero valido.',
                ],
            ],
            'fecha_instalacion' => [
                'rules'  => 'permit_empty|valid_date',
                'errors' => [
                    'valid_date' => 'Ingresa una fecha valida.',
                ],
            ],
        ];
    }
}