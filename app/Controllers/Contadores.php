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

    public function index()
    {
        $filtros = [
            'busqueda'  => trim((string) $this->request->getGet('q')),
            'sector_id' => $this->request->getGet('sector_id') ?? '',
            'activo'    => $this->request->getGet('activo') ?? '',
        ];

        $contadores = $this->contadores->listarFiltrado($filtros)->paginate(15);

        return view('contadores/index', [
            'title'      => 'Contadores',
            'contadores' => $contadores,
            'pager'      => $this->contadores->pager,
            'sectores'   => $this->sectores->where('activo', 1)->orderBy('nombre', 'ASC')->findAll(),
            'filtros'    => $filtros,
        ]);
    }

    /**
     * Devuelve solo la tabla, para actualizarla por AJAX sin recargar. (SDGODA-49)
     */
    public function tabla()
    {
        $filtros = [
            'busqueda'  => trim((string) $this->request->getGet('q')),
            'sector_id' => $this->request->getGet('sector_id') ?? '',
            'activo'    => $this->request->getGet('activo') ?? '',
        ];

        $contadores = $this->contadores->listarFiltrado($filtros)->paginate(15);

        return view('contadores/_tabla', [
            'contadores' => $contadores,
            'pager'      => $this->contadores->pager,
            'filtros'    => $filtros,
        ]);
    }

    /**
     * Formulario para registrar un nuevo contador/predio.
     */
    public function nuevo()
    {
        return view('contadores/form', [
            'title'    => 'Nuevo contador',
            'contador' => null,
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
        $tipoServicio     = trim((string) $this->request->getPost('tipo_servicio')) ?: null;
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
            'tipo_servicio'     => $tipoServicio,
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

    /**
     * Formulario para editar un contador existente. (HU-11)
     */
    public function editar($id = null)
    {
        $contador = $this->contadores->obtenerConDetalle((int) $id);

        if (! $contador) {
            return redirect()->to('/contadores')->with('errores', ['Ese contador no existe.']);
        }

        return view('contadores/form', [
            'title'    => 'Editar contador',
            'contador' => $contador,
        ]);
    }

    /**
     * Procesa la edicion de un contador. (HU-11)
     * Solo permite ajustar datos propios del contador (codigo, lectura,
     * fecha de instalacion); el predio/cliente se define al registrarlo.
     */
    public function actualizar($id = null)
    {
        $id       = (int) $id;
        $contador = $this->contadores->find($id);

        if (! $contador) {
            return redirect()->to('/contadores')->with('errores', ['Ese contador no existe.']);
        }

        $reglas = [
            'id'           => 'permit_empty|is_natural_no_zero',
            'numero_serie' => [
                'rules'  => 'required|max_length[30]|is_unique[contadores.numero_serie,id,{id}]',
                'errors' => [
                    'required'   => 'El codigo del contador es obligatorio.',
                    'max_length' => 'El codigo del contador es demasiado largo (max. 30 caracteres).',
                    'is_unique'  => 'Ya existe un contador registrado con ese codigo.',
                ],
            ],
            'tipo_servicio' => [
                'rules'  => 'permit_empty|in_list[cuarto_paja,media_paja,paja_completa]',
                'errors' => [
                    'in_list' => 'Selecciona un tipo de servicio valido.',
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

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $numeroSerie = trim((string) $this->request->getPost('numero_serie'));

        $datos = [
            'id'                => $id,
            'numero_serie'      => $numeroSerie,
            'tipo_servicio'     => trim((string) $this->request->getPost('tipo_servicio')) ?: null,
            'lectura_inicial'   => $this->request->getPost('lectura_inicial') ?: 0,
            'fecha_instalacion' => trim((string) $this->request->getPost('fecha_instalacion')) ?: null,
        ];

        if (! $this->contadores->update($id, $datos)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->contadores->errors());
        }

        return redirect()->to('/contadores')
            ->with('exito', 'Contador "' . esc($numeroSerie) . '" actualizado correctamente.');
    }

    /**
     * Desactiva un contador (baja de servicio o cambio de contador). (HU-11)
     * Criterio: un contador inactivo no aparece en la lista de pendientes
     * de lectura del lector (ver ContadorModel::listarActivosParaLectura()).
     */
    public function desactivar($id = null)
    {
        $id       = (int) $id;
        $contador = $this->contadores->find($id);

        if (! $contador) {
            return redirect()->to('/contadores')->with('errores', ['Ese contador no existe.']);
        }

        $this->contadores->update($id, [
            'activo'       => 0,
            'fecha_retiro' => date('Y-m-d'),
        ]);

        return redirect()->to('/contadores')
            ->with('exito', 'Contador "' . esc($contador['numero_serie']) . '" desactivado correctamente.');
    }

    /**
     * Reactiva un contador previamente desactivado. (HU-11)
     */
    public function activar($id = null)
    {
        $id       = (int) $id;
        $contador = $this->contadores->find($id);

        if (! $contador) {
            return redirect()->to('/contadores')->with('errores', ['Ese contador no existe.']);
        }

        $this->contadores->update($id, [
            'activo'       => 1,
            'fecha_retiro' => null,
        ]);

        return redirect()->to('/contadores')
            ->with('exito', 'Contador "' . esc($contador['numero_serie']) . '" activado correctamente.');
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
            'tipo_servicio' => [
                'rules'  => 'permit_empty|in_list[cuarto_paja,media_paja,paja_completa]',
                'errors' => [
                    'in_list' => 'Selecciona un tipo de servicio valido.',
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