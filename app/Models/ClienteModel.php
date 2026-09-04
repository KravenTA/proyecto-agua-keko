<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nombre', 'telefono', 'direccion', 'email', 'activo', 'observaciones',
        'dpi', 'foto_vivienda', 'recibo_luz',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Prepara la consulta de busqueda para el listado paginado. (HU-09)
     * Devuelve el modelo listo para llamar a paginate().
     */
    public function buscar(string $termino = '', string $activo = '')
    {
        if ($termino !== '') {
            $this->groupStart()
                ->like('nombre', $termino)
                ->orLike('telefono', $termino)
                ->orLike('email', $termino)
                ->groupEnd();
        }

        if ($activo !== '') {
            $this->where('activo', $activo);
        }

        return $this->orderBy('nombre', 'ASC');
    }

    /**
     * Verifica si el cliente tiene al menos un contador activo asociado
     * (a traves de sus servicios). Se usa para bloquear la desactivacion. (HU-08)
     */
    public function tieneContadoresActivos(int $clienteId): bool
    {
        return $this->db->table('contadores')
            ->join('servicios', 'servicios.id = contadores.servicio_id')
            ->where('servicios.cliente_id', $clienteId)
            ->where('contadores.activo', 1)
            ->countAllResults() > 0;
    }

    /**
     * Estado de cuenta por cliente, para el dashboard (HU-18 / SDGODA-41).
     *
     * "Pendiente" = el cliente tiene al menos un recibo con estado
     * 'pendiente' (una lectura sin pago asociado; recibos.estado pasa a
     * 'pagado' en el momento de registrar el pago, ver PagoModel::registrarPago()).
     * "Al dia" = no tiene ningun recibo pendiente (incluye clientes sin
     * lecturas registradas todavia).
     *
     * $filtros acepta: busqueda (nombre/telefono), estado ('pendiente'|'al_dia').
     *
     * meses_pendientes viaja de una vez en el resultado (cuenta de recibos
     * pendientes por cliente) para que SDGODA-48 lo reutilice sin tener que
     * rehacer esta consulta.
     */
    public function estadoDeCuenta(array $filtros = []): array
    {
        $builder = $this->db->table('clientes')
            ->select("
                clientes.id,
                clientes.nombre,
                clientes.telefono,
                clientes.direccion,
                clientes.activo,
                COUNT(recibos_pendientes.id) AS meses_pendientes,
                CASE WHEN COUNT(recibos_pendientes.id) > 0 THEN 'pendiente' ELSE 'al_dia' END AS estado_cuenta
            ", false)
            ->join('servicios', 'servicios.cliente_id = clientes.id', 'left')
            ->join('lecturas', 'lecturas.servicio_id = servicios.id', 'left')
            ->join(
                'recibos AS recibos_pendientes',
                "recibos_pendientes.lectura_id = lecturas.id AND recibos_pendientes.estado = 'pendiente'",
                'left'
            )
            ->groupBy('clientes.id');

        if (! empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('clientes.nombre', $filtros['busqueda'])
                ->orLike('clientes.telefono', $filtros['busqueda'])
                ->groupEnd();
        }

        if (! empty($filtros['estado'])) {
            $builder->having('estado_cuenta', $filtros['estado']);
        }

        return $builder->orderBy('clientes.nombre', 'ASC')->get()->getResultArray();
    }
}