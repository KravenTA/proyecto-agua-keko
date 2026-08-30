<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodoModel extends Model
{
    protected $table         = 'periodos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['anio', 'mes', 'estado'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Devuelve el periodo vigente: el que esta abierto. Si hubiera mas de
     * uno, toma el mas reciente. Null si no hay ninguno abierto.
     */
    public function periodoActual(): ?array
    {
        return $this->where('estado', 'abierto')
            ->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->first();
    }

    /**
     * Nombre legible del periodo, por ejemplo "Agosto 2026".
     */
    public function etiqueta(array $periodo): string
    {
        $meses = [
            1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
        ];

        $mes = (int) $periodo['mes'];

        return ($meses[$mes] ?? 'Mes ' . $mes) . ' ' . $periodo['anio'];
    }
}