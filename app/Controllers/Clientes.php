<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class Clientes extends BaseController
{
    protected ClienteModel $clientes;

    public function __construct()
    {
        $this->clientes = new ClienteModel();
    }

    public function index()
    {
        $termino = trim((string) $this->request->getGet('q'));
        $activo  = (string) ($this->request->getGet('activo') ?? '');
        [$orden, $dir] = $this->leerOrden();

        $clientes = $this->clientes->buscar($termino, $activo)
            ->orderBy($orden, $dir)
            ->paginate(10);

        return view('clientes/index', [
            'title'    => 'Clientes',
            'clientes' => $clientes,
            'pager'    => $this->clientes->pager,
            'termino'  => $termino,
            'activo'   => $activo,
            'orden'    => $orden,
            'dir'      => $dir,
        ]);
    }

    /**
     * Devuelve solo la tabla de resultados, para actualizarla por AJAX
     * sin recargar la pagina completa. (HU-09)
     */
    public function tabla()
    {
        $termino = trim((string) $this->request->getGet('q'));
        $activo  = (string) ($this->request->getGet('activo') ?? '');
        [$orden, $dir] = $this->leerOrden();

        $clientes = $this->clientes->buscar($termino, $activo)
            ->orderBy($orden, $dir)
            ->paginate(10);

        return view('clientes/_tabla', [
            'clientes' => $clientes,
            'pager'    => $this->clientes->pager,
            'termino'  => $termino,
            'activo'   => $activo,
            'orden'    => $orden,
            'dir'      => $dir,
        ]);
    }

    /**
     * Formulario para registrar un nuevo cliente.
     */
    public function nuevo()
    {
        return view('clientes/form', [
            'title'   => 'Nuevo cliente',
            'cliente' => null,
        ]);
    }

    /**
     * Procesa el registro de un nuevo cliente. (HU-07)
     */
    public function crear()
    {
        $reglas = $this->reglasValidacion();

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $datos = [
            'nombre'    => trim((string) $this->request->getPost('nombre')),
            'telefono'  => trim((string) $this->request->getPost('telefono')),
            'direccion' => trim((string) $this->request->getPost('direccion')),
            'email'     => trim((string) $this->request->getPost('email')) ?: null,
            'dpi'       => trim((string) $this->request->getPost('dpi')) ?: null,
            'activo'    => 1,
        ];

        $rutaFoto   = $this->guardarArchivo('foto_vivienda');
        $rutaRecibo = $this->guardarArchivo('recibo_luz');

        if ($rutaFoto !== null) {
            $datos['foto_vivienda'] = $rutaFoto;
        }
        if ($rutaRecibo !== null) {
            $datos['recibo_luz'] = $rutaRecibo;
        }

        if (! $this->clientes->insert($datos)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->clientes->errors());
        }

        return redirect()->to('/clientes')
            ->with('exito', 'Cliente "' . esc($datos['nombre']) . '" registrado correctamente.');
    }

    /**
     * Formulario para editar un cliente existente. (HU-08)
     */
    public function editar($id = null)
    {
        $cliente = $this->clientes->find((int) $id);

        if (! $cliente) {
            return redirect()->to('/clientes')->with('errores', ['Ese cliente no existe.']);
        }

        return view('clientes/form', [
            'title'   => 'Editar cliente',
            'cliente' => $cliente,
        ]);
    }

    /**
     * Procesa la edicion de un cliente. (HU-08)
     * Criterio: la edicion refleja cambios inmediatos.
     */
    public function actualizar($id = null)
    {
        $id      = (int) $id;
        $cliente = $this->clientes->find($id);

        if (! $cliente) {
            return redirect()->to('/clientes')->with('errores', ['Ese cliente no existe.']);
        }

        $reglas = $this->reglasValidacion();

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $datos = [
            'nombre'    => trim((string) $this->request->getPost('nombre')),
            'telefono'  => trim((string) $this->request->getPost('telefono')),
            'direccion' => trim((string) $this->request->getPost('direccion')),
            'email'     => trim((string) $this->request->getPost('email')) ?: null,
            'dpi'       => trim((string) $this->request->getPost('dpi')) ?: null,
        ];

        $rutaFoto   = $this->guardarArchivo('foto_vivienda');
        $rutaRecibo = $this->guardarArchivo('recibo_luz');

        if ($rutaFoto !== null) {
            $datos['foto_vivienda'] = $rutaFoto;
        }
        if ($rutaRecibo !== null) {
            $datos['recibo_luz'] = $rutaRecibo;
        }

        if (! $this->clientes->update($id, $datos)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->clientes->errors());
        }

        return redirect()->to('/clientes')
            ->with('exito', 'Cliente "' . esc($datos['nombre']) . '" actualizado correctamente.');
    }

    /**
     * Desactiva ("elimina") un cliente. (HU-08)
     * Criterio: no permite eliminar cliente con contadores activos.
     */
    public function eliminar($id = null)
    {
        $id      = (int) $id;
        $cliente = $this->clientes->find($id);

        if (! $cliente) {
            return redirect()->to('/clientes')->with('errores', ['Ese cliente no existe.']);
        }

        if ($this->clientes->tieneContadoresActivos($id)) {
            return redirect()->to('/clientes')->with('errores', [
                'No puedes eliminar a "' . esc($cliente['nombre']) . '" porque tiene contadores activos asociados. Da de baja sus contadores primero.',
            ]);
        }

        $this->clientes->update($id, ['activo' => 0]);

        return redirect()->to('/clientes')
            ->with('exito', 'Cliente "' . esc($cliente['nombre']) . '" desactivado correctamente.');
    }

    /**
     * Reactiva un cliente previamente desactivado.
     */
    public function activar($id = null)
    {
        $id      = (int) $id;
        $cliente = $this->clientes->find($id);

        if (! $cliente) {
            return redirect()->to('/clientes')->with('errores', ['Ese cliente no existe.']);
        }

        $this->clientes->update($id, ['activo' => 1]);

        return redirect()->to('/clientes')
            ->with('exito', 'Cliente "' . esc($cliente['nombre']) . '" activado correctamente.');
    }

    /**
     * Guarda un archivo subido (imagen o PDF) en public/uploads/clientes/
     * y devuelve la ruta relativa a guardar en BD, o null si no se subio nada.
     */
    private function guardarArchivo(string $campo): ?string
    {
        $archivo = $this->request->getFile($campo);

        if ($archivo === null || ! $archivo->isValid() || $archivo->hasMoved()) {
            return null;
        }

        $carpeta = ROOTPATH . 'public/uploads/clientes/';

        if (! is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombreNuevo = $archivo->getRandomName();
        $archivo->move($carpeta, $nombreNuevo);

        return 'uploads/clientes/' . $nombreNuevo;
    }

    /**
     * Lee la columna y direccion de ordenamiento desde la URL.
     * Solo se aceptan columnas de la lista blanca, para evitar
     * que alguien mande cualquier cosa al ORDER BY.
     */
    private function leerOrden(): array
    {
        $permitidas = ['id', 'nombre', 'telefono', 'direccion'];

        $orden = (string) $this->request->getGet('orden');
        $dir   = strtolower((string) $this->request->getGet('dir'));

        if (! in_array($orden, $permitidas, true)) {
            $orden = 'nombre';
        }

        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'asc';
        }

        return [$orden, $dir];
    }

    private function reglasValidacion(): array
    {
        return [
            'nombre' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'El nombre es obligatorio.',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                ],
            ],
            'telefono' => [
                'rules'  => 'required|min_length[8]|max_length[20]',
                'errors' => [
                    'required'   => 'El telefono es obligatorio.',
                    'min_length' => 'El telefono debe tener al menos 8 digitos.',
                ],
            ],
            'direccion' => [
                'rules'  => 'required|min_length[5]|max_length[255]',
                'errors' => [
                    'required'   => 'La direccion es obligatoria.',
                    'min_length' => 'La direccion es demasiado corta.',
                ],
            ],
            'email' => [
                'rules'  => 'permit_empty|valid_email|max_length[150]',
                'errors' => [
                    'valid_email' => 'Ingresa un correo electronico valido.',
                ],
            ],
            'dpi' => [
                'rules' => 'permit_empty|max_length[20]',
            ],
            'foto_vivienda' => [
                'rules'  => 'permit_empty|is_image[foto_vivienda]|max_size[foto_vivienda,2048]',
                'errors' => [
                    'is_image' => 'La foto de la vivienda debe ser una imagen valida.',
                    'max_size' => 'La foto de la vivienda no puede pesar mas de 2MB.',
                ],
            ],
            'recibo_luz' => [
                'rules'  => 'permit_empty|max_size[recibo_luz,2048]|ext_in[recibo_luz,jpg,jpeg,png,pdf]',
                'errors' => [
                    'max_size' => 'El recibo de luz no puede pesar mas de 2MB.',
                    'ext_in'   => 'El recibo de luz debe ser imagen (jpg/png) o PDF.',
                ],
            ],
        ];
    }
}