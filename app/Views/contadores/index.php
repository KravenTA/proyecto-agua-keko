<?= view('layouts/header', [
        'title'      => 'Contadores - Oficina del Agua',
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
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Contadores registrados</h6>
                <a href="<?= base_url('contadores/nuevo') ?>" class="btn btn-primary btn-sm mb-0">
                    Nuevo contador
                </a>
            </div>

            <div class="card-body px-0 pt-3 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Codigo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sector</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referencia</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lectura inicial</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Instalacion</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contadores)) : ?>
                                <tr>
                                    <td colspan="8" class="text-center text-sm text-secondary py-4">
                                        Aun no hay contadores registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($contadores as $c) : ?>
                                <tr>
                                    <td class="ps-4"><span class="text-sm font-weight-bold"><?= esc($c['numero_serie']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['cliente_nombre']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['sector_nombre']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['referencia'] ?? '-') ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['lectura_inicial']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['fecha_instalacion'] ?? '-') ?></span></td>
                                    <td>
                                        <?php if ((int) $c['activo'] === 1) : ?>
                                            <span class="badge badge-sm bg-gradient-success">Activo</span>
                                        <?php else : ?>
                                            <span class="badge badge-sm bg-gradient-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('contadores/editar/' . $c['id']) ?>"
                                           class="btn btn-link text-dark px-2 mb-0">Editar</a>

                                        <?php if ((int) $c['activo'] === 1) : ?>
                                            <form action="<?= base_url('contadores/desactivar/' . $c['id']) ?>"
                                                  method="post" class="d-inline"
                                                  onsubmit="return confirm('¿Desactivar este contador? Ya no aparecera en las lecturas pendientes.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-link text-danger px-2 mb-0">Desactivar</button>
                                            </form>
                                        <?php else : ?>
                                            <form action="<?= base_url('contadores/activar/' . $c['id']) ?>"
                                                  method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-link text-success px-2 mb-0">Activar</button>
                                            </form>
                                        <?php endif; ?>
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