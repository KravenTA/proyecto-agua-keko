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
            'title' => 'Nuevo cliente',
        ]);
    }

    /**
     * Procesa el registro de un nuevo cliente. (HU-07)
     * Criterios: valida campos obligatorios, guarda en BD, muestra confirmacion.
     */
    public function crear()
    {
        $reglas = [
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
}