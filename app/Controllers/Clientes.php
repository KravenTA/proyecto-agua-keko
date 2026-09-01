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
        return view('clientes/index', [
            'title'    => 'Clientes',
            'clientes' => $this->clientes->orderBy('nombre', 'ASC')->findAll(),
        ]);
    }

    public function nuevo()
    {
        return view('clientes/form', [
            'title'   => 'Nuevo cliente',
            'cliente' => null,
        ]);
    }

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

        $rutaFoto  = $this->guardarArchivo('foto_vivienda');
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
     * y devuelve la ruta relativa a guardar en BD, o null si no se subió nada.
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
                'rules'  => 'permit_empty|max_length[20]',
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