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

    /**
     * Listado de clientes registrados.
     */
    public function index()
    {
        return view('clientes/index', [
            'title'    => 'Clientes',
            'clientes' => $this->clientes->orderBy('nombre', 'ASC')->findAll(),
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
            'activo'    => 1,
        ];

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
        ];

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
        ];
    }
}