<?= view('layouts/header', [
    'title'      => 'Recibo - Oficina de Agua',
    'body_class' => 'bg-gray-100',
]) ?>

<style>
    .recibo-container {
        max-width: 800px;
        margin: 30px auto;
    }

    .recibo {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .recibo-header {
        text-align: center;
        border-bottom: 2px solid #344767;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }

    .recibo-header h2 {
        margin-bottom: 5px;
        font-weight: 700;
    }

    .recibo-header p {
        margin-bottom: 0;
    }

    .dato {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .dato strong {
        color: #344767;
    }

    .dato-sub {
        padding-left: 16px;
        font-size: 13px;
        color: #67748e;
    }

    .aviso-pendientes {
        margin-bottom: 20px;
        padding: 12px 16px;
        background: #fff4e5;
        border: 1px solid #f0ad4e;
        border-radius: 8px;
        color: #7a4a00;
        font-size: 14px;
        text-align: center;
    }

    .tabla-lecturas {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .tabla-lecturas th,
    .tabla-lecturas td {
        padding: 8px 6px;
        border-bottom: 1px solid #eee;
        text-align: right;
    }

    .tabla-lecturas th:first-child,
    .tabla-lecturas td:first-child {
        text-align: left;
    }

    .tabla-lecturas thead th {
        color: #67748e;
        text-transform: uppercase;
        font-size: 11px;
        border-bottom: 2px solid #344767;
    }

    .total {
        margin-top: 25px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }

    .total-label {
        font-size: 14px;
        color: #67748e;
        text-transform: uppercase;
        font-weight: 600;
    }

    .total-monto {
        font-size: 32px;
        font-weight: 700;
        color: #344767;
    }

    .firma {
        margin-top: 40px;
        text-align: center;
    }

    .firma-linea {
        display: inline-block;
        font-family: 'Segoe Script', cursive;
        font-size: 22px;
        color: #344767;
        border-bottom: 1px solid #344767;
        padding: 0 30px 4px;
        margin-bottom: 6px;
    }

    .firma-nota {
        font-size: 11px;
        color: #67748e;
    }

    .acciones {
        margin-top: 20px;
        text-align: center;
    }

    @media print {
        body {
            background: #fff !important;
        }

        .recibo-container {
            max-width: 100%;
            margin: 0;
        }

        .recibo {
            box-shadow: none;
            border-radius: 0;
            padding: 20px;
        }

        .acciones {
            display: none !important;
        }

        .row {
            display: flex !important;
            flex-wrap: nowrap !important;
        }

        .row > [class*="col-"] {
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        .text-md-end {
            text-align: right !important;
        }
    }
</style>

<?php
    // SDGODA-53: nombres de mes locales, sin depender de PeriodoModel dentro de la vista.
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    $totalGeneral = 0;
?>

<main class="main-content position-relative">

    <div class="container-fluid py-4">

        <div class="recibo-container">

            <div class="recibo">

                <div class="recibo-header">
                    <h2>OFICINA DE AGUA</h2>

                    <p class="text-secondary">
                        RECIBO DE CONSUMO DE AGUA
                    </p>

                    <p class="text-center" style="margin-top:4px;">
                        Recibo No. <strong><?= esc($recibo['numero']) ?></strong>
                    </p>
                </div>

                <div class="row mb-4">

                    <div class="col-md-6">

                        <p class="text-xs text-secondary mb-1">
                            CONTRIBUYENTE
                        </p>

                        <h5 class="mb-1">
                            <?= esc($recibo['cliente_nombre']) ?>
                        </h5>

                        <p class="text-sm mb-1">
                            NIT: <strong><?= esc($recibo['cliente_nit'] ?: 'C/F') ?></strong>
                        </p>

                        <p class="text-sm mb-1">
                            DPI: <strong><?= esc($recibo['cliente_dpi'] ?: 'N/D') ?></strong>
                        </p>

                        <?php if (! empty($recibo['direccion'])) : ?>
                            <p class="text-sm mb-1">
                                Dirección: <strong><?= esc($recibo['direccion']) ?></strong>
                                <?php if (! empty($recibo['sector_nombre'])) : ?>
                                    (Sector/Zona: <?= esc($recibo['sector_nombre']) ?>)
                                <?php endif; ?>
                            </p>
                        <?php elseif (! empty($recibo['sector_nombre'])) : ?>
                            <p class="text-sm mb-1">
                                Sector/Zona: <?= esc($recibo['sector_nombre']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (! empty($recibo['cliente_telefono'])) : ?>
                            <p class="text-sm mb-0">
                                Tel:
                                <?= esc($recibo['cliente_telefono']) ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-6 text-md-end">

                        <p class="text-xs text-secondary mb-1">
                            PERÍODO
                        </p>

                        <h5 class="mb-1">
                            <?= esc($etiqueta) ?>
                        </h5>

                        <p class="text-sm mb-0">
                            Contador:
                            <strong>
                                <?= esc($recibo['numero_contador']) ?>
                            </strong>
                        </p>

                        <p class="text-sm mb-0">
                            Fecha lectura:
                            <?= esc($recibo['fecha_lectura']) ?>
                        </p>

                        <p class="text-sm mb-0">
                            Fecha generado:
                            <?= esc(
                                ! empty($recibo['fecha_emision'])
                                    ? date('d/m/Y', strtotime($recibo['fecha_emision']))
                                    : '—'
                            ) ?>
                        </p>

                        <p class="text-sm mb-0">
                            Fecha vencimiento:
                            <strong>
                                <?= esc(
                                    ! empty($recibo['fecha_vencimiento'])
                                        ? date('d/m/Y', strtotime($recibo['fecha_vencimiento']))
                                        : '—'
                                ) ?>
                            </strong>
                        </p>

                    </div>

                </div>

                <?php if ($meses_pendientes > 1) : ?>
                    <div class="aviso-pendientes">
                        Este cliente tiene <strong><?= $meses_pendientes ?> meses pendientes de pago</strong>,
                        incluyendo este recibo.
                    </div>
                <?php endif; ?>

                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3">
                    Detalle de consumo
                </h6>

                <?php if (! empty($pendientes)) : ?>

                    <?php foreach ($pendientes as $p) : ?>

                        <?php
                            $incluidoM3P     = ((float) $p['volumen_incluido_litros']) / 1000;
                            $consumoP        = (float) $p['consumo'];
                            $excedenteP      = max(0, $consumoP - $incluidoM3P);
                            $cuotaMinimaP    = (float) $p['cuota_minima'];
                            $totalP          = (float) $p['total'];
                            $cargoExcedenteP = $excedenteP > 0 ? max(0, $totalP - $cuotaMinimaP) : 0;
                            $totalGeneral   += $totalP;

                            $etiquetaP = ($meses[(int) $p['periodo_mes']] ?? 'Mes ' . $p['periodo_mes'])
                                . ' - ' . $p['periodo_anio'];
                        ?>

                        <div class="dato">
                            <span>Canon de Agua <?= esc($etiquetaP) ?></span>
                            <strong>Q<?= number_format($cuotaMinimaP, 2) ?></strong>
                        </div>

                        <?php if ($excedenteP > 0) : ?>
                            <div class="dato dato-sub">
                                <span>
                                    Exceso de Agua / Servicio de Agua por Consumo
                                    (<?= number_format($excedenteP, 2) ?> m³)
                                </span>
                                <strong>Q<?= number_format($cargoExcedenteP, 2) ?></strong>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php else : ?>

                    <p class="text-sm text-secondary">
                        No hay periodos pendientes de pago para este cliente.
                    </p>

                <?php endif; ?>

                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mt-4 mb-2">
                    Detalle de lecturas por período
                </h6>

                <table class="tabla-lecturas">
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th>Lect. anterior</th>
                            <th>Lect. actual</th>
                            <th>Consumo (m³)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendientes as $p) : ?>
                            <?php
                                $etiquetaFila = ($meses[(int) $p['periodo_mes']] ?? 'Mes ' . $p['periodo_mes'])
                                    . ' - ' . $p['periodo_anio'];
                            ?>
                            <tr>
                                <td><?= esc($etiquetaFila) ?></td>
                                <td><?= number_format((float) $p['lectura_anterior'], 2) ?></td>
                                <td><?= number_format((float) $p['lectura_actual'], 2) ?></td>
                                <td><?= number_format((float) $p['consumo'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total">

                    <div class="total-label">
                        Total general a pagar
                    </div>

                    <div class="total-monto">
                        Q<?= number_format($totalGeneral, 2) ?>
                    </div>

                </div>

                <div class="firma">
                    <div class="firma-linea">Oficina de Agua</div>
                    <p class="firma-nota mb-0">Firma electrónica</p>
                    <p class="firma-nota mb-0">Generado por: Sistema</p>
                </div>

                <div class="acciones">

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-primary">
                        Imprimir recibo
                    </button>

                    <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                        <a href="<?= base_url('recibos') ?>" class="btn btn-outline-secondary">
                            Ver todos los recibos
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('lecturas/pendientes') ?>" class="btn btn-outline-secondary">
                        Pendientes de lectura
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?= view('layouts/footer') ?>