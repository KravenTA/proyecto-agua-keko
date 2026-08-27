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
            return redirect()->to('/dashboard');
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

        switch (strtolower($rol['nombre'] ?? '')) {
            case 'administrador':
                return redirect()->to('/dashboard/admin');
            case 'secretaria':
                return redirect()->to('/dashboard/secretaria');
            case 'lector':
                return redirect()->to('/dashboard/lector');
            default:
                return redirect()->to('/dashboard');
        }
    }

    /**
     * Cierra la sesión del usuario y destruye todos los datos guardados
     * por Auth::login() (usuario_id, usuario_nombre, rol_id, rol_nombre, isLoggedIn).
     */
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
}