<?php

namespace App\Libraries;

use App\Models\LogErrorModel;
use CodeIgniter\Log\Handlers\HandlerInterface;
use Psr\Log\LogLevel;

/**
 * Handler de logs que guarda los errores del sistema en la base de datos
 * (tabla log_errores), ademas de los archivos de texto normales.
 */
class DatabaseLogHandler implements HandlerInterface
{
    protected array $handles = [LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR];

    public function setDateFormat(string $format): self
    {
        return $this;
    }

    public function canHandle(string $level): bool
    {
        return in_array($level, $this->handles, true);
    }

    public function handle($level, $message): bool
    {
        try {
            $model = new LogErrorModel();

            $usuarioId = session('usuario_id') ?? null;

            $archivo = null;
            $linea   = null;

            if (preg_match('/in\s+(.+):(\d+)/', (string) $message, $coincidencias)) {
                $archivo = $coincidencias[1];
                $linea   = (int) $coincidencias[2];
            }

            $model->insert([
                'nivel'      => $level,
                'mensaje'    => (string) $message,
                'archivo'    => $archivo,
                'linea'      => $linea,
                'usuario_id' => $usuarioId,
            ]);
        } catch (\Throwable $e) {
            // Si falla el guardado del log, no debe romper el flujo normal
            // de la aplicacion.
        }

        return true;
    }
}