<?= view('layouts/header', ['title' => 'Registrar tarifa - Oficina del Agua', 'body_class' => 'g-sidenav-show bg-gray-100']) ?>

<main class="main-content mt-0">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-12 mx-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1"
                             style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                            <h5 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                Registrar nueva tarifa
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('mensaje')) : ?>
                            <div class="alert alert-success text-white" role="alert">
                                <?= esc(session()->getFlashdata('mensaje')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger text-white" role="alert">
                                <?= esc(session()->getFlashdata('error')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger text-white" role="alert">
                                <ul class="mb-0 ps-3">
                                    <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                                        <li><?= esc($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('tarifas') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label ms-1">Precio por unidad (Q)</label>
                                <input type="number" step="0.01" min="0.01" name="precio_unitario"
                                       class="form-control" value="<?= old('precio_unitario') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Cuota mínima (Q)</label>
                                <input type="number" step="0.01" min="0" name="cuota_minima"
                                       class="form-control" value="<?= old('cuota_minima') ?>">
                                <small class="text-muted ms-1">Opcional, por defecto 0.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Vigente desde</label>
                                <input type="date" name="vigente_desde" class="form-control"
                                       value="<?= old('vigente_desde') ?>" required>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="btn btn-lg w-100 mt-4 mb-0 text-white"
                                    style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                    Registrar tarifa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>