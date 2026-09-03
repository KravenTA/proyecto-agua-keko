<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturaModel extends Model
{
    protected $table         = 'lecturas';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'servicio_id', 'contador_id', 'periodo_id', 'tarifa_id',
        'lector_usuario_id', 'lectura_anterior', 'lectura_actual', 'fecha_lectura',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';
}