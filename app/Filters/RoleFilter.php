<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * $arguments trae los roles permitidos, definidos en la ruta.
     * Ej: 'filter' => 'role:Administrador,Secretaria'
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $rolesPermitidos = $arguments ?? [];
        $rolActual       = session()->get('rol_nombre');

        if (! in_array($rolActual, $rolesPermitidos, true)) {
            return redirect()->to('/login')
                ->with('errores', ['No tienes permiso para acceder a esa sección.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hace falta nada despues de la respuesta.
    }
}