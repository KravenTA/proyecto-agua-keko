<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table         = 'servicios';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['cliente_id', 'sector_id', 'codigo', 'direccion', 'fecha_alta', 'estado'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'sector_id'  => 'required|is_natural_no_zero',
        'codigo'     => 'required|is_unique[servicios.codigo]|max_length[30]',
        'direccion'  => 'permit_empty|max_length[150]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Debe asociar el predio a un cliente.',
        ],
        'sector_id' => [
            'required' => 'Debe seleccionar un sector.',
        ],
        'codigo' => [
            'required'  => 'El código del predio/servicio es obligatorio.',
            'is_unique' => 'Ese código de predio ya está registrado.',
        ],
    ];
}