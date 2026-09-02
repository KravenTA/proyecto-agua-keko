<?= view('layouts/header', [
        'title'      => 'Registrar lectura - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<?php
    $anterior = $contador['lectura_anterior'] ?? $contador['lectura_inicial'] ?? 0;
?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">

                <?php if (session()->getFlashdata('errores')) : ?>
                    <div class="alert alert-danger text-white" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ((array) session()->getFlashdata('errores') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-0"><?= esc($contador['numero_serie']) ?></h5>
                                <p class="text-sm mb-0"><?= esc($contador['cliente_nombre']) ?></p>
                                <p class="text-xs text-secondary mb-0">
                                    <?= esc($contador['direccion'] ?? 'Sin direccion registrada') ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="text-xs text-secondary mb-0">Periodo</p>
                                <p class="text-sm font-weight-bold mb-0"><?= esc($etiqueta) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <p class="text-xs text-secondary mb-1">Lectura anterior</p>
                            <h2 class="mb-0"><?= esc($anterior) ?></h2>
                        </div>

                        <form method="post" action="<?= base_url('lecturas/guardar/' . $contador['id']) ?>">
                            <?= csrf_field() ?>

                            <div class="mb-4">
                                <label for="lectura_actual" class="form-label">Lectura actual</label>
                                <input type="number" step="0.01" min="<?= esc($anterior) ?>"
                                       class="form-control form-control-lg text-center"
                                       id="lectura_actual" name="lectura_actual"
                                       inputmode="decimal" required autofocus
                                       value="<?= esc(old('lectura_actual')) ?>"
                                       placeholder="0.00">
                                <small class="text-xs text-secondary">
                                    Debe ser mayor o igual a <?= esc($anterior) ?>.
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary mb-0">
                                    Guardar lectura
                                </button>
                                <a href="<?= base_url('lecturas/pendientes') ?>"
                                   class="btn btn-outline-secondary mb-0">Cancelar</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>