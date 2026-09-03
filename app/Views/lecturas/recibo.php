<?= view('layouts/header', [
    'title'      => 'Recibo - Oficina del Agua',
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
    }
</style>

<main class="main-content position-relative">

    <div class="container-fluid py-4">

        <div class="recibo-container">

            <div class="recibo">

                <div class="recibo-header">
                    <h2>OFICINA DEL AGUA</h2>

                    <p class="text-secondary">
                        RECIBO DE CONSUMO DE AGUA
                    </p>
                </div>

                <div class="row mb-4">

                    <div class="col-md-6">

                        <p class="text-xs text-secondary mb-1">
                            CLIENTE
                        </p>

                        <h5 class="mb-1">
                            <?= esc($lectura['cliente_nombre']) ?>
                        </h5>

                        <?php if (! empty($lectura['direccion'])) : ?>
                            <p class="text-sm mb-1">
                                <?= esc($lectura['direccion']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (! empty($lectura['cliente_telefono'])) : ?>
                            <p class="text-sm mb-0">
                                Tel:
                                <?= esc($lectura['cliente_telefono']) ?>
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
                                <?= esc($lectura['numero_contador']) ?>
                            </strong>
                        </p>

                        <p class="text-sm mb-0">
                            Fecha:
                            <?= esc($lectura['fecha_lectura']) ?>
                        </p>

                    </div>

                </div>

                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3">
                    Detalle de consumo
                </h6>

                <div class="dato">
                    <span>Lectura anterior</span>

                    <strong>
                        <?= number_format(
                            (float) $lectura['lectura_anterior'],
                            2
                        ) ?>
                    </strong>
                </div>

                <div class="dato">
                    <span>Lectura actual</span>

                    <strong>
                        <?= number_format(
                            (float) $lectura['lectura_actual'],
                            2
                        ) ?>
                    </strong>
                </div>

                <div class="dato">
                    <span>Consumo</span>

                    <strong>
                        <?= number_format(
                            (float) $lectura['consumo'],
                            2
                        ) ?>
                        m³
                    </strong>
                </div>

                <?php
                    $incluidoM3     = ((float) $lectura['volumen_incluido_litros']) / 1000;
                    $consumo        = (float) $lectura['consumo'];
                    $excedenteM3    = max(0, $consumo - $incluidoM3);
                    $cargoExcedente = $excedenteM3 > 0 ? ((float) $lectura['monto'] - (float) $lectura['cuota_minima']) : 0;
                ?>

                <div class="dato">
                    <span>Volumen incluido</span>
                    <strong><?= number_format($incluidoM3, 2) ?> m³</strong>
                </div>

                <?php if ($excedenteM3 > 0) : ?>
                    <div class="dato">
                        <span>Excedente</span>
                        <strong><?= number_format($excedenteM3, 2) ?> m³</strong>
                    </div>

                    <div class="dato">
                        <span>Cargo por excedente</span>
                        <strong>Q<?= number_format($cargoExcedente, 2) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="dato">
                    <span>Tarifa aplicada</span>

                    <strong>
                        <?= esc(
                            ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $lectura['tarifa_tipo']
                                )
                            )
                        ) ?>
                    </strong>
                </div>

                <div class="dato">
                    <span>Cuota mínima</span>

                    <strong>
                        Q<?= number_format(
                            (float) $lectura['cuota_minima'],
                            2
                        ) ?>
                    </strong>
                </div>

                <div class="total">

                    <div class="total-label">
                        Monto a pagar
                    </div>

                    <div class="total-monto">
                        Q<?= number_format(
                            (float) $lectura['monto'],
                            2
                        ) ?>
                    </div>

                </div>

                <div class="acciones">

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-primary">
                        Imprimir recibo
                    </button>

                    <a
                        href="<?= base_url('lecturas/pendientes') ?>"
                        class="btn btn-outline-secondary">
                        Volver
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?= view('layouts/footer') ?>