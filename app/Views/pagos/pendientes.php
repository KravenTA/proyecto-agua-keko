<?= view('layouts/header', [
        'title'      => 'Pagos pendientes - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">

        <?php if (session()->getFlashdata('exito')) : ?>
            <div class="alert alert-success text-white" role="alert">
                <?= esc(session()->getFlashdata('exito')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errores')) : ?>
            <div class="alert alert-danger text-white" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) session()->getFlashdata('errores') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-0">Lecturas pendientes de pago</h6>
            </div>

            <div class="card-body px-0 pt-3 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Cliente</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Servicio</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contador</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Periodo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha lectura</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Monto</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendientes)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center text-sm text-secondary py-4">
                                        No hay lecturas pendientes de pago.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($pendientes as $p) : ?>
                                <tr>
                                    <td class="ps-4"><span class="text-sm font-weight-bold"><?= esc($p['cliente_nombre']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($p['servicio_codigo']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($p['numero_contador']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($p['periodo_mes']) ?>/<?= esc($p['periodo_anio']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($p['fecha_lectura']) ?></span></td>
                                    <td><span class="text-sm font-weight-bold">Q<?= number_format((float) $p['monto'], 2) ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('pagos/nuevo/' . $p['lectura_id']) ?>"
                                           class="btn btn-primary btn-sm mb-0">Registrar pago</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<?= view('layouts/footer') ?>