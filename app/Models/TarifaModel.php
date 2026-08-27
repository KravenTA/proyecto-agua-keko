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

     /**
     * Devuelve el historial de tarifas, ordenado por fecha de vigencia (mas reciente primero).
     * Permite filtrar opcionalmente por un rango de fechas que se cruce con vigente_desde/vigente_hasta.
     * (Usado por HU-05: Consultar historial de tarifas)
     */
    public function historial(?string $desde = null, ?string $hasta = null): array
    {
        $builder = $this->orderBy('vigente_desde', 'DESC');

        if ($desde) {
            $builder->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $desde)
            ->groupEnd();
        }

        if ($hasta) {
            $builder->where('vigente_desde <=', $hasta);
        }

        return $builder->findAll();
    }

    /**
     * Devuelve la tarifa activa vigente en la fecha dada (por defecto, hoy).
     */
    public function tarifaVigente(?string $fecha = null): ?array
    {
        $fecha = $fecha ?? date('Y-m-d');

        return $this->where('activo', 1)
            ->where('vigente_desde <=', $fecha)
            ->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $fecha)
            ->groupEnd()
            ->orderBy('vigente_desde', 'DESC')
            ->first();
    }
}