<?php

namespace App\Models;

use CodeIgniter\Model;

class LogErrorModel extends Model
{
    protected $table         = 'log_errores';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nivel', 'mensaje', 'archivo', 'linea', 'usuario_id'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}