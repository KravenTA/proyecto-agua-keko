<?= view('layouts/header', [
        'title'      => esc($title) . ' - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<?php
    $esEdicion = ! empty($cliente);
    $accion    = $esEdicion
        ? base_url('clientes/actualizar/' . $cliente['id'])
        : base_url('clientes');
?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-12 mx-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1"
                             style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                            <h5 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                <?= esc($title) ?>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <?php if (session()->getFlashdata('errores')) : ?>
                            <div class="alert alert-danger text-white" role="alert">
                                <ul class="mb-0 ps-3">
                                    <?php foreach ((array) session()->getFlashdata('errores') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= $accion ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label ms-1">Nombre completo</label>
                                <input type="text" name="nombre" class="form-control"
                                       value="<?= esc(old('nombre', $cliente['nombre'] ?? '')) ?>"
                                       required minlength="3" maxlength="100"
                                       placeholder="Ej. Maria Lopez Garcia">
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Telefono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="<?= esc(old('telefono', $cliente['telefono'] ?? '')) ?>"
                                       required minlength="8" maxlength="20"
                                       placeholder="Ej. 5555-1234">
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Direccion</label>
                                <textarea name="direccion" class="form-control" rows="2" required
                                          minlength="5" maxlength="255"
                                          placeholder="Ej. Caserio Los Cerritos, Canton Valencia, Jutiapa"><?= esc(old('direccion', $cliente['direccion'] ?? '')) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Correo electronico (opcional)</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= esc(old('email', $cliente['email'] ?? '')) ?>" maxlength="150"
                                       placeholder="Para notificaciones y recibos">
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">DPI (opcional)</label>
                                <input type="text" name="dpi" class="form-control"
                                       value="<?= esc(old('dpi', $cliente['dpi'] ?? '')) ?>" maxlength="20"
                                       placeholder="Ej. 1234 56789 0101">
                            </div>

                            <div class="mb-3">
                                <label class="form-label ms-1">Foto de la vivienda (opcional)</label>
                                <input type="file" name="foto_vivienda" class="form-control" accept="image/*">
                                <?php if ($esEdicion && ! empty($cliente['foto_vivienda'])) : ?>
                                    <small class="text-secondary">
                                        Ya hay una foto guardada.
                                        <a href="<?= base_url($cliente['foto_vivienda']) ?>" target="_blank">Ver actual</a>
                                        — subir una nueva la reemplaza.
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label ms-1">Recibo de luz (opcional, imagen o PDF)</label>
                                <input type="file" name="recibo_luz" class="form-control" accept="image/*,.pdf">
                                <?php if ($esEdicion && ! empty($cliente['recibo_luz'])) : ?>
                                    <small class="text-secondary">
                                        Ya hay un recibo guardado.
                                        <a href="<?= base_url($cliente['recibo_luz']) ?>" target="_blank">Ver actual</a>
                                        — subir uno nuevo lo reemplaza.
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit"
                                    class="btn btn-lg w-100 mt-0 mb-0 text-white"
                                    style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                    <?= $esEdicion ? 'Guardar cambios' : 'Guardar cliente' ?>
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('clientes') ?>" class="text-secondary text-sm">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>