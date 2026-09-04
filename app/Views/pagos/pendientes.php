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
                <h6 class="mb-0">Recibos pendientes de pago</h6>
            </div>

            <div class="card-body px-0 pt-3 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Recibo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contador</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Periodo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha emision</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendientes)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center text-sm text-secondary py-4">
                                        No hay recibos pendientes de pago.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($pendientes as $r) : ?>
                                <tr>
                                    <td class="ps-4"><span class="text-sm font-weight-bold"><?= esc($r['numero']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($r['cliente_nombre']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($r['numero_contador']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($r['periodo_mes']) ?>/<?= esc($r['periodo_anio']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($r['fecha_emision']) ?></span></td>
                                    <td><span class="text-sm font-weight-bold">Q<?= number_format((float) $r['total'], 2) ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('pagos/nuevo/' . $r['id']) ?>"
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