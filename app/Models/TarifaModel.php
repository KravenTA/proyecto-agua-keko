<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifaModel extends Model
{
    protected $table            = 'tarifas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tipo', 'volumen_incluido_litros', 'precio_unitario', 'cuota_minima', 'vigente_desde', 'vigente_hasta', 'activo'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Busca una tarifa activa DEL MISMO TIPO cuya vigencia se traslape con
     * la fecha de inicio dada. Cada tipo (cuarto_paja, media_paja,
     * paja_completa, exceso) tiene su propia linea de vigencias
     * independiente -- pueden convivir 4 tarifas activas a la vez, una
     * por tipo, pero no dos del mismo tipo traslapadas.
     */
    public function existeTraslape(string $tipo, string $vigenteDesde): bool
    {
        return $this->where('tipo', $tipo)
            ->where('activo', 1)
            ->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $vigenteDesde)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /**
     * Devuelve el historial de tarifas, ordenado por tipo y luego por fecha
     * de vigencia (mas reciente primero). Permite filtrar opcionalmente por
     * un rango de fechas y/o por tipo.
     * (Usado por HU-05: Consultar historial de tarifas)
     */
    public function historial(?string $desde = null, ?string $hasta = null, ?string $tipo = null): array
    {
        $builder = $this->orderBy('tipo', 'ASC')->orderBy('vigente_desde', 'DESC');

        if ($desde) {
            $builder->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $desde)
            ->groupEnd();
        }

        if ($hasta) {
            $builder->where('vigente_desde <=', $hasta);
        }

        if ($tipo) {
            $builder->where('tipo', $tipo);
        }

        return $builder->findAll();
    }

    /**
     * Devuelve la tarifa activa vigente del tipo indicado, en la fecha dada
     * (por defecto, hoy). Cada tipo de servicio (y el tipo "exceso") tiene
     * su propia tarifa vigente independiente.
     */
    public function tarifaVigente(string $tipo, ?string $fecha = null): ?array
    {
        $fecha = $fecha ?? date('Y-m-d');

        return $this->where('tipo', $tipo)
            ->where('activo', 1)
            ->where('vigente_desde <=', $fecha)
            ->groupStart()
                ->where('vigente_hasta IS NULL')
                ->orWhere('vigente_hasta >=', $fecha)
            ->groupEnd()
            ->orderBy('vigente_desde', 'DESC')
            ->first();
    }

    /**
     * Devuelve las tarifas vigentes de TODOS los tipos a la vez, en la
     * fecha dada, indexadas por tipo. Util para el historial (mostrar la
     * tarifa vigente de cada categoria) y para el calculo de lecturas.
     */
    public function tarifasVigentesPorTipo(?string $fecha = null): array
    {
        $tipos     = ['cuarto_paja', 'media_paja', 'paja_completa', 'exceso'];
        $resultado = [];

        foreach ($tipos as $tipo) {
            $resultado[$tipo] = $this->tarifaVigente($tipo, $fecha);
        }

        return $resultado;
    }

    /**
     * Calcula el monto a cobrar por un consumo, segun la tarifa del tipo de
     * paja del contador. (HU-15)
     *
     * Regla acordada:
     *  - Si el consumo entra en el volumen incluido, se cobra la cuota minima.
     *  - Si lo excede, se cobra la cuota minima mas el excedente al precio
     *    unitario de la tarifa de tipo 'exceso'.
     *
     * El consumo llega en metros cubicos; volumen_incluido_litros esta en
     * litros, por eso la conversion.
     *
     * Devuelve null si falta la tarifa del tipo o la de exceso.
     */
    public function calcularMonto(array $tarifa, float $consumoM3, ?string $fecha = null): ?float
    {
        $incluidoM3 = ((float) $tarifa['volumen_incluido_litros']) / 1000;
        $cuota      = (float) $tarifa['cuota_minima'];

        if ($consumoM3 <= $incluidoM3) {
            return round($cuota, 2);
        }

        $tarifaExceso = $this->tarifaVigente('exceso', $fecha);

        if (! $tarifaExceso) {
            return null;
        }

        $excedenteM3 = $consumoM3 - $incluidoM3;

        return round($cuota + ($excedenteM3 * (float) $tarifaExceso['precio_unitario']), 2);
    }
}