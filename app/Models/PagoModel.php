<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table            = 'pagos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'lectura_id',
        'recibo_id',
        'usuario_id',
        'deposito_id',
        'monto',
        'fecha_pago',
        'metodo',
        'referencia',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'lectura_id' => 'required|integer|is_unique[pagos.lectura_id]',
        'monto'      => 'required|decimal|greater_than[0]',
        'fecha_pago' => 'required|valid_date',
        'metodo'     => 'required|max_length[30]',
    ];

    protected $validationMessages = [
        'lectura_id' => [
            'is_unique' => 'Esta lectura ya tiene un pago registrado.',
        ],
        'monto' => [
            'greater_than' => 'El monto debe ser mayor a cero.',
        ],
    ];

    /**
     * Registra el pago dentro de una transacción: obtiene o crea el recibo
     * de la lectura (vía ReciboModel), y guarda el pago con lectura_id +
     * recibo_id + el usuario que lo registra.
     */
    public function registrarPago(int $lecturaId, float $monto, string $fechaPago, string $metodo, int $usuarioId, ?string $referencia = null): int|false
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $reciboModel = new ReciboModel();
        $reciboId    = $reciboModel->obtenerOCrearParaLectura($lecturaId);

        $pagoId = $this->insert([
            'lectura_id' => $lecturaId,
            'recibo_id'  => $reciboId,
            'usuario_id' => $usuarioId,
            'monto'      => $monto,
            'fecha_pago' => $fechaPago,
            'metodo'     => $metodo,
            'referencia' => $referencia,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return false;
        }

        return $pagoId;
    }
}