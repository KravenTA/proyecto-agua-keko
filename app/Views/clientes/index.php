<?= view('layouts/header', [
        'title'      => 'Clientes - Oficina del Agua',
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
                <h6 class="mb-0">Clientes registrados</h6>
                <a href="<?= base_url('clientes/nuevo') ?>" class="btn btn-primary btn-sm mb-0">
                    Nuevo cliente
                </a>
            </div>

            <div class="card-body px-0 pt-3 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Nombre</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Telefono</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Direccion</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clientes)) : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-sm text-secondary py-4">
                                        Aun no hay clientes registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($clientes as $c) : ?>
                                <tr>
                                    <td class="ps-4"><span class="text-sm font-weight-bold"><?= esc($c['nombre']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['telefono']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['direccion'] ?? '-') ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($c['email'] ?? '-') ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc(date('Y-m-d', strtotime($c['created_at']))) ?></span></td>
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