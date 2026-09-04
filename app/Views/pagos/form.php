<?= view('layouts/header', [
        'title'      => 'Registrar pago - Oficina del Agua',
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

                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Registrar pago &mdash; Recibo <?= esc($recibo['numero']) ?></h6>
                        <p class="text-sm text-secondary mb-0">
                            <?= esc($recibo['cliente_nombre']) ?> &mdash;
                            Servicio <?= esc($recibo['servicio_codigo']) ?>,
                            periodo <?= esc($recibo['periodo_mes']) ?>/<?= esc($recibo['periodo_anio']) ?>
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Total del recibo</label>
                            <input type="text" class="form-control" value="Q<?= number_format((float) $recibo['total'], 2) ?>" disabled>
                        </div>

                        <form method="post" action="<?= base_url('pagos') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="lectura_id" value="<?= esc($recibo['lectura_id']) ?>">
                            <input type="hidden" name="recibo_id" value="<?= esc($recibo['id']) ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Monto a pagar</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="monto"
                                           required
                                           value="<?= esc(old('monto', $recibo['total'])) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de pago</label>
                                    <input type="date" class="form-control" name="fecha_pago" required
                                           value="<?= esc(old('fecha_pago', date('Y-m-d'))) ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Metodo de pago</label>
                                    <select class="form-control" name="metodo" required>
                                        <?php $metodoActual = old('metodo'); ?>
                                        <option value="">Elige un metodo</option>
                                        <option value="efectivo" <?= $metodoActual === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                        <option value="transferencia" <?= $metodoActual === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                        <option value="deposito" <?= $metodoActual === 'deposito' ? 'selected' : '' ?>>Deposito</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Referencia (opcional)</label>
                                    <input type="text" class="form-control" name="referencia" maxlength="100"
                                           value="<?= esc(old('referencia')) ?>"
                                           placeholder="No. de boleta o transferencia">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary mb-0">Guardar pago</button>
                                <a href="<?= base_url('pagos') ?>" class="btn btn-outline-secondary mb-0">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>