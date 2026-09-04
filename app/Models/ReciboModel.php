<?php

namespace App\Models;

use CodeIgniter\Model;

class ReciboModel extends Model
{
    protected $table            = 'recibos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'lectura_id',
        'numero',
        'fecha_emision',
        'monto_consumo',
        'monto_adicional',
        'concepto_adicional',
        'total',
        'estado',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Devuelve el recibo de una lectura si ya existe; si no, lo crea
     * "perezosamente" (nada en el sistema los crea antes de un intento
     * de pago, ni siquiera HU-16, que solo imprime al vuelo).
     */
    public function obtenerOCrearParaLectura(int $lecturaId): int
    {
        $existente = $this->where('lectura_id', $lecturaId)->first();

        if ($existente) {
            return (int) $existente['id'];
        }

        $lecturaModel = new LecturaModel();
        $lectura      = $lecturaModel->obtenerParaRecibo($lecturaId);

        if (! $lectura) {
            throw new \RuntimeException("No existe la lectura #{$lecturaId} para generar su recibo.");
        }

        $montoConsumo = $lectura['monto'];

        return $this->insert([
            'lectura_id'      => $lecturaId,
            'numero'          => 'REC-' . str_pad((string) $lecturaId, 6, '0', STR_PAD_LEFT),
            'fecha_emision'   => date('Y-m-d H:i:s'),
            'monto_consumo'   => $montoConsumo,
            'monto_adicional' => 0,
            'total'           => $montoConsumo,
            'estado'          => 'pendiente',
        ]);
    }
}