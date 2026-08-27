<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['rol_id', 'nombre', 'email', 'password_hash', 'activo'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

   protected $validationRules = [
        'id'     => 'permit_empty|is_natural_no_zero',
        'nombre' => 'required|min_length[3]|max_length[100]',
        'email'  => 'required|valid_email|max_length[150]|is_unique[usuarios.email,id,{id}]',
        'rol_id' => 'required|is_natural_no_zero|is_not_unique[roles.id]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'Escribe el nombre del usuario.',
            'min_length' => 'El nombre necesita al menos 3 caracteres.',
        ],
        'email' => [
            'required'    => 'Escribe el correo del usuario.',
            'valid_email' => 'Ese correo no tiene un formato valido.',
            'is_unique'   => 'Ya existe un usuario con ese correo.',
        ],
        'rol_id' => [
            'required'           => 'Elige un rol.',
            'is_natural_no_zero' => 'Elige un rol.',
            'is_not_unique'      => 'El rol seleccionado no existe.',
        ],
    ];

    public function listarConRol(array $filtros = []): array
    {
        $builder = $this->select('usuarios.*, roles.nombre AS rol_nombre')
            ->join('roles', 'roles.id = usuarios.rol_id', 'left');

        if (! empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('usuarios.nombre', $filtros['busqueda'])
                ->orLike('usuarios.email', $filtros['busqueda'])
                ->groupEnd();
        }

        if (isset($filtros['rol_id']) && $filtros['rol_id'] !== '') {
            $builder->where('usuarios.rol_id', $filtros['rol_id']);
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $builder->where('usuarios.activo', $filtros['activo']);
        }

        return $builder->orderBy('usuarios.nombre', 'ASC')->findAll();
    }

    public function contarAdminsActivos(): int
    {
        return $this->select('usuarios.id')
            ->join('roles', 'roles.id = usuarios.rol_id')
            ->where('roles.nombre', 'Administrador')
            ->where('usuarios.activo', 1)
            ->countAllResults();
    }

    public function esUltimoAdminActivo(int $id): bool
    {
        $usuario = $this->select('usuarios.activo, roles.nombre AS rol_nombre')
            ->join('roles', 'roles.id = usuarios.rol_id')
            ->where('usuarios.id', $id)
            ->first();

        if (! $usuario) {
            return false;
        }

        if ($usuario['rol_nombre'] !== 'Administrador' || (int) $usuario['activo'] !== 1) {
            return false;
        }

        return $this->contarAdminsActivos() <= 1;
    }
}