<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolModel;

class Auth extends BaseController
{
    /**
     * Muestra el formulario de login.
     */
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->destinoSegunRol());
        }

        return view('auth/login');
    }

    /**
     * Procesa el envío del formulario de login.
     */
    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel
            ->where('email', $email)
            ->where('activo', 1)
            ->first();

        if (! $usuario || ! password_verify($password, $usuario['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Correo o contraseña incorrectos.');
        }

        $rolModel = new RolModel();
        $rol = $rolModel->find($usuario['rol_id']);

        session()->set([
            'usuario_id'     => $usuario['id'],
            'usuario_nombre' => $usuario['nombre'],
            'rol_id'         => $usuario['rol_id'],
            'rol_nombre'     => $rol['nombre'] ?? '',
            'isLoggedIn'     => true,
        ]);

        return redirect()->to($this->destinoSegunRol());
    }

    /**
     * Cierra la sesión del usuario, eliminando las claves guardadas
     * por Auth::login() (usuario_id, usuario_nombre, rol_id, rol_nombre, isLoggedIn).
     */
    public function logout()
    {
        session()->remove(['usuario_id', 'usuario_nombre', 'rol_id', 'rol_nombre', 'isLoggedIn']);

        return redirect()->to('/login')
            ->with('mensaje', 'Sesión cerrada correctamente.');
    }

    /**
     * A donde mandar a un usuario ya logueado, segun su rol. (SDGODA-41)
     *
     * Administrador y Secretaria van al dashboard de estado de cuenta.
     * Lector no tiene acceso a ese dashboard (ver RoleFilter en Routes.php),
     * asi que va directo a su pantalla de trabajo: pendientes de lectura.
     */
    private function destinoSegunRol(): string
    {
        return match (strtolower(session()->get('rol_nombre') ?? '')) {
            'administrador', 'secretaria' => '/dashboard',
            'lector'                      => '/lecturas/pendientes',
            default                       => '/login',
        };
    }
}