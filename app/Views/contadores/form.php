<?= view('layouts/header', [
        'title'      => esc($title) . ' - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <?php if (session()->getFlashdata('errores')) : ?>
                    <div class="alert alert-danger text-white" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ((array) session()->getFlashdata('errores') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($sectores)) : ?>
                    <div class="alert alert-warning text-white" role="alert">
                        Todavia no hay sectores registrados en el sistema. Pide que agreguen al
                        menos uno en la tabla <code>sectores</code> antes de registrar un contador.
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0"><?= esc($title) ?></h6>
                        <p class="text-sm text-secondary mb-0">
                            Registra el contador y el predio que lo conecta con un cliente.
                        </p>
                    </div>

                    <div class="card-body">
                        <form method="post" action="<?= base_url('contadores') ?>">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Cliente asociado</label>
                                <select class="form-control" name="cliente_id" required>
                                    <option value="">Elige un cliente</option>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <?php $sel = (string) old('cliente_id') === (string) $cliente['id']; ?>
                                        <option value="<?= $cliente['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                            <?= esc($cliente['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($clientes)) : ?>
                                    <small class="text-danger">
                                        No hay clientes activos. Registra uno primero en el modulo de Clientes.
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sector</label>
                                <select class="form-control" name="sector_id" required>
                                    <option value="">Elige un sector</option>
                                    <?php foreach ($sectores as $sector) : ?>
                                        <?php $sel = (string) old('sector_id') === (string) $sector['id']; ?>
                                        <option value="<?= $sector['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                            <?= esc($sector['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Referencia del predio</label>
                                <input type="text" class="form-control" name="referencia" maxlength="150"
                                       value="<?= esc(old('referencia')) ?>"
                                       placeholder="Ej. Casa color celeste, 2da entrada del caserio">
                                <small class="text-xs text-secondary">Opcional, ayuda a ubicar el predio en campo.</small>
                            </div>

                            <hr class="horizontal dark">

                            <div class="mb-3">
                                <label class="form-label">Codigo del contador</label>
                                <input type="text" class="form-control" name="numero_serie" required maxlength="30"
                                       value="<?= esc(old('numero_serie')) ?>"
                                       placeholder="Ej. CTR-0456">
                                <small class="text-xs text-secondary">Debe ser unico: identifica fisicamente al contador.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lectura inicial</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="lectura_inicial"
                                           value="<?= esc(old('lectura_inicial', '0')) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de instalacion</label>
                                    <input type="date" class="form-control" name="fecha_instalacion"
                                           value="<?= esc(old('fecha_instalacion', date('Y-m-d'))) ?>">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary mb-0">Guardar contador</button>
                                <a href="<?= base_url('contadores') ?>" class="btn btn-outline-secondary mb-0">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>