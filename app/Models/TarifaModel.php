<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifaModel extends Model
{
    protected $table            = 'tarifas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['precio_unitario', 'cuota_minima', 'vigente_desde', 'vigente_hasta', 'activo'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Busca una tarifa activa cuya vigencia se traslape con la fecha de inicio dada.
     * Una tarifa activa "abierta" (vigente_hasta = NULL) se considera vigente
     * indefinidamente hacia adelante, así que cualquier nueva tarifa que empiece
     * después también se traslaparía con ella.
     */
    public function existeTraslape(string $vigenteDesde): bool
    {
        return $this->where('activo', 1)
            ->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $vigenteDesde)
            ->groupEnd()
            ->countAllResults() > 0;
    }
}