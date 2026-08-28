<?= view('layouts/header', ['title' => 'Editar tarifa - Oficina del Agua', 'body_class' => 'g-sidenav-show bg-gray-100']) ?>

<main class="main-content mt-0">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-12 mx-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1"
                             style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                            <h5 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                Editar tarifa
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger text-white"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger text-white">
                                <ul class="mb-0 ps-3">
                                    <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                                        <li><?= esc($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted ms-1">
                            Vigente desde: <?= esc($tarifa['vigente_desde']) ?>
                            (la fecha de inicio no se puede modificar aquí)
                        </p>

                        <form action="<?= base_url('tarifas/actualizar/' . $tarifa['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label ms-1">Precio por unidad (Q)</label>
                                <input type="number" step="0.01" min="0.01" name="precio_unitario"
                                       class="form-control" value="<?= old('precio_unitario', $tarifa['precio_unitario']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Cuota mínima (Q)</label>
                                <input type="number" step="0.01" min="0" name="cuota_minima"
                                       class="form-control" value="<?= old('cuota_minima', $tarifa['cuota_minima']) ?>">
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="btn btn-lg w-100 mt-4 mb-0 text-white"
                                    style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>

                        <?php if (! $tarifa['vigente_hasta'] && $tarifa['activo']) : ?>
                            <hr class="my-4">
                            <form action="<?= base_url('tarifas/cerrar-vigencia/' . $tarifa['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label ms-1">Fecha de fin de vigencia</label>
                                    <input type="date" name="fecha_cierre" class="form-control"
                                           value="<?= date('Y-m-d') ?>">
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    Cerrar vigencia de esta tarifa
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>