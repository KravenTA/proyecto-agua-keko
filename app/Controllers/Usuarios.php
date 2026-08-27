<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolModel;

class Usuarios extends BaseController
{
    protected UsuarioModel $usuarios;
    protected RolModel $roles;

    public function __construct()
    {
        $this->usuarios = new UsuarioModel();
        $this->roles    = new RolModel();
    }

    public function index()
    {
        $filtros = [
            'busqueda' => trim((string) $this->request->getGet('busqueda')),
            'rol_id'   => $this->request->getGet('rol_id') ?? '',
            'activo'   => $this->request->getGet('activo') ?? '',
        ];

        return view('usuarios/index', [
            'title'    => 'Usuarios',
            'usuarios' => $this->usuarios->listarConRol($filtros),
            'roles'    => $this->roles->orderBy('nombre', 'ASC')->findAll(),
            'filtros'  => $filtros,
        ]);
    }

    public function nuevo()
    {
        return view('usuarios/form', [
            'title'   => 'Nuevo usuario',
            'usuario' => null,
            'roles'   => $this->roles->where('activo', 1)->orderBy('nombre', 'ASC')->findAll(),
        ]);
    }

    public function crear()
    {
        $reglas = [
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Escribe una contrasena.',
                    'min_length' => 'La contrasena necesita al menos 6 caracteres.',
                ],
            ],
            'password_confirm' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Repite la contrasena.',
                    'matches'  => 'Las contrasenas no coinciden.',
                ],
            ],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $datos = [
            'rol_id'        => (int) $this->request->getPost('rol_id'),
            'nombre'        => trim((string) $this->request->getPost('nombre')),
            'email'         => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'activo'        => $this->request->getPost('activo') ? 1 : 0,
        ];

        if (! $this->usuarios->insert($datos)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->usuarios->errors());
        }

        return redirect()->to('/usuarios')->with('exito', 'Usuario creado.');
    }

    public function editar($id = null)
    {
        $usuario = $this->usuarios->find((int) $id);

        if (! $usuario) {
            return redirect()->to('/usuarios')->with('errores', ['Ese usuario no existe.']);
        }

        return view('usuarios/form', [
            'title'         => 'Editar usuario',
            'usuario'       => $usuario,
            'roles'         => $this->roles->where('activo', 1)->orderBy('nombre', 'ASC')->findAll(),
            'esUltimoAdmin' => $this->usuarios->esUltimoAdminActivo((int) $id),
        ]);
    }

    public function actualizar($id = null)
    {
        $id      = (int) $id;
        $usuario = $this->usuarios->find($id);

        if (! $usuario) {
            return redirect()->to('/usuarios')->with('errores', ['Ese usuario no existe.']);
        }

        $password = (string) $this->request->getPost('password');

        if ($password !== '') {
            $reglas = [
                'password' => [
                    'rules'  => 'min_length[6]',
                    'errors' => ['min_length' => 'La contrasena necesita al menos 6 caracteres.'],
                ],
                'password_confirm' => [
                    'rules'  => 'matches[password]',
                    'errors' => ['matches' => 'Las contrasenas no coinciden.'],
                ],
            ];

            if (! $this->validate($reglas)) {
                return redirect()->back()->withInput()
                    ->with('errores', $this->validator->getErrors());
            }
        }

        $rolIdNuevo  = (int) $this->request->getPost('rol_id');
        $activoNuevo = $this->request->getPost('activo') ? 1 : 0;

        if ($this->usuarios->esUltimoAdminActivo($id)) {
            $rolAdmin = $this->roles->where('nombre', 'Administrador')->first();

            if ($activoNuevo === 0) {
                return redirect()->back()->withInput()->with('errores', [
                    'No puedes desactivar al unico administrador activo. Crea o activa otro administrador primero.',
                ]);
            }

            if ($rolAdmin && $rolIdNuevo !== (int) $rolAdmin['id']) {
                return redirect()->back()->withInput()->with('errores', [
                    'No puedes cambiarle el rol al unico administrador activo. Crea o activa otro administrador primero.',
                ]);
            }
        }

        $datos = [
            'id'     => $id,
            'rol_id' => $rolIdNuevo,
            'nombre' => trim((string) $this->request->getPost('nombre')),
            'email'  => trim((string) $this->request->getPost('email')),
            'activo' => $activoNuevo,
        ];

        if ($password !== '') {
            $datos['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (! $this->usuarios->update($id, $datos)) {
            return redirect()->back()->withInput()
                ->with('errores', $this->usuarios->errors());
        }

        return redirect()->to('/usuarios')->with('exito', 'Cambios guardados.');
    }

    public function eliminar($id = null)
    {
        $id = (int) $id;

        if (! $this->usuarios->find($id)) {
            return redirect()->to('/usuarios')->with('errores', ['Ese usuario no existe.']);
        }

        if ($id === (int) session()->get('usuario_id')) {
            return redirect()->to('/usuarios')->with('errores', ['No puedes desactivar tu propia cuenta.']);
        }

        if ($this->usuarios->esUltimoAdminActivo($id)) {
            return redirect()->to('/usuarios')->with('errores', [
                'No puedes desactivar al unico administrador activo. Crea o activa otro administrador primero.',
            ]);
        }

        $this->usuarios->update($id, ['activo' => 0]);

        return redirect()->to('/usuarios')->with('exito', 'Usuario desactivado.');
    }

    public function activar($id = null)
    {
        $id = (int) $id;

        if (! $this->usuarios->find($id)) {
            return redirect()->to('/usuarios')->with('errores', ['Ese usuario no existe.']);
        }

        $this->usuarios->update($id, ['activo' => 1]);

        return redirect()->to('/usuarios')->with('exito', 'Usuario activado.');
    }
}