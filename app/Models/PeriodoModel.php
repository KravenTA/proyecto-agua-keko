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

    /**
     * Todos los periodos, del mas reciente al mas viejo.
     */
    public function listarTodos(): array
    {
        return $this->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->findAll();
    }

    /**
     * True si ya existe un periodo para ese año y mes.
     */
    public function existe(int $anio, int $mes, ?int $exceptoId = null): bool
    {
        $builder = $this->where('anio', $anio)->where('mes', $mes);

        if ($exceptoId !== null) {
            $builder->where('id !=', $exceptoId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Cuenta contadores activos sin lectura en el periodo. Se usa para
     * advertir antes de cerrar: esos clientes se quedarian sin recibo.
     */
    public function contadoresSinLectura(int $periodoId): int
    {
        return $this->db->table('contadores')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('contadores.activo', 1)
            ->where('servicios.estado', 'activo')
            ->where("NOT EXISTS (
                SELECT 1 FROM lecturas l
                WHERE l.contador_id = contadores.id
                  AND l.periodo_id = " . $periodoId . "
            )", null, false)
            ->countAllResults();
    }
    
    /**
     * Cuantas lecturas tiene registradas un periodo.
     */
    public function totalLecturas(int $periodoId): int
    {
        return $this->db->table('lecturas')
            ->where('periodo_id', $periodoId)
            ->countAllResults();
    }
}