<?= view('layouts/header', [
        'title'      => 'Pendientes de lectura - Oficina del Agua',
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

        <?php if (! $periodo) : ?>
            <div class="alert alert-warning text-white" role="alert">
                No hay ningun periodo abierto. Pide en la oficina que abran el
                periodo del mes para poder registrar lecturas.
            </div>
        <?php else : ?>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <p class="text-xs text-secondary mb-0">Periodo</p>
                            <h6 class="mb-0"><?= esc($etiqueta) ?></h6>
                        </div>
                        <div class="text-end">
                            <p class="text-xs text-secondary mb-0">Pendientes</p>
                            <h6 class="mb-0"><?= count($pendientes) ?></h6>
                        </div>
                    </div>

                    <form method="get" action="<?= base_url('lecturas/pendientes') ?>" class="mt-3">
                        <input type="search" class="form-control" name="q"
                               placeholder="Buscar por contador, cliente, sector o direccion"
                               value="<?= esc($busqueda) ?>" autocomplete="off">
                    </form>
                </div>
            </div>

            <?php if (empty($pendientes)) : ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-sm text-secondary mb-0">
                            <?= $busqueda !== ''
                                ? 'Ningun contador pendiente coincide con la busqueda.'
                                : 'No quedan contadores pendientes de lectura en este periodo.' ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <?php $sectorPrevio = null; ?>

            <?php foreach ($pendientes as $p) : ?>

                <?php if ($p['sector_nombre'] !== $sectorPrevio) : ?>
                    <p class="text-uppercase text-secondary text-xxs font-weight-bolder mt-4 mb-2 ps-1">
                        <?= esc($p['sector_nombre']) ?>
                    </p>
                    <?php $sectorPrevio = $p['sector_nombre']; ?>
                <?php endif; ?>

                <div class="card mb-2">
                    <div class="card-body py-3">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-0"><?= esc($p['numero_serie']) ?></h6>
                                <p class="text-sm mb-0"><?= esc($p['cliente_nombre']) ?></p>
                            </div>
                            <span class="badge badge-sm bg-gradient-warning">Pendiente</span>
                        </div>

                        <p class="text-xs text-secondary mb-1 mt-2">
                            <?= esc($p['direccion'] ?? 'Sin direccion registrada') ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-xs text-secondary">
                                Lectura anterior:
                                <strong><?= esc($p['lectura_anterior'] ?? $p['lectura_inicial']) ?></strong>
                            </span>

                            <?php if (! empty($p['cliente_telefono'])) : ?>
                                <a href="tel:<?= esc($p['cliente_telefono']) ?>"
                                   class="text-xs text-primary">
                                    <?= esc($p['cliente_telefono']) ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <a href="<?= base_url('lecturas/registrar/' . $p['id']) ?>"
                           class="btn btn-primary btn-sm w-100 mt-3 mb-0">
                            Registrar lectura
                        </a>
                        
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</main>

<?= view('layouts/footer') ?>