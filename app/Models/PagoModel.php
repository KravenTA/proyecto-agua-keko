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
        'recibo_id'  => 'required|integer',
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
     * Registra el pago y marca el recibo como pagado, en una transaccion.
     * El recibo ya existe (se crea al registrar la lectura, en
     * Lecturas::guardar() -> ReciboModel::emitirPorLectura()).
     */
    public function registrarPago(int $lecturaId, int $reciboId, float $monto, string $fechaPago, string $metodo, int $usuarioId, ?string $referencia = null): int|false
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $pagoId = $this->insert([
            'lectura_id' => $lecturaId,
            'recibo_id'  => $reciboId,
            'usuario_id' => $usuarioId,
            'monto'      => $monto,
            'fecha_pago' => $fechaPago,
            'metodo'     => $metodo,
            'referencia' => $referencia,
        ]);

        (new ReciboModel())->update($reciboId, ['estado' => 'pagado']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return false;
        }

        return $pagoId;
    }
}