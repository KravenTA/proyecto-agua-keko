<?= view('layouts/header', [
        'title'      => $title,
        'body_class' => $body_class,
    ]) ?>

<?= view('layouts/sidenav') ?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">

        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Equipo — Grupo 4</h6>
                <p class="text-sm text-secondary mb-0">
                    Sistema de Gestión — Oficina del Agua · Universidad Mariano Gálvez de Guatemala, Campus Jutiapa
                </p>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <?php foreach ($integrantes as $persona) : ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card h-100 text-center border">
                                <div class="card-body">
                                    <?php if (! empty($persona['foto'])) : ?>
                                        <img src="<?= base_url('assets/img/equipo/' . $persona['foto']) ?>"
                                             alt="<?= esc($persona['nombre']) ?>"
                                             class="mx-auto mb-3 d-block"
                                             style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover;">
                                    <?php else : ?>
                                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                                             style="width: 72px; height: 72px; border-radius: 50%;
                                                    background-color: var(--oa-navy, #04263D); color: #fff;
                                                    font-weight: 700; font-size: 1.1rem;">
                                            <?= esc($persona['iniciales']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <h6 class="mb-1"><?= esc($persona['nombre']) ?></h6>
                                    <p class="text-sm text-secondary mb-0"><?= esc($persona['rol']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="horizontal dark my-4">

                <p class="text-sm text-secondary mb-0">
                    &copy; <?= date('Y') ?> Oficina del Agua. Todos los derechos reservados.
                </p>
            </div>
        </div>

    </div>
</main>

<?= view('layouts/footer') ?>