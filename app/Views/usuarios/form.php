<?= view('layouts/header', [
        'title'      => esc($title) . ' - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<?php
    $esEdicion = ! empty($usuario);
    $accion    = $esEdicion
        ? base_url('usuarios/actualizar/' . $usuario['id'])
        : base_url('usuarios/crear');
?>

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

                <?php if ($esEdicion && ! empty($esUltimoAdmin)) : ?>
                    <div class="alert alert-warning text-white" role="alert">
                        Este es el unico administrador activo. No puedes desactivarlo ni cambiarle
                        el rol hasta que exista otro administrador.
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0"><?= esc($title) ?></h6>
                    </div>

                    <div class="card-body">
                        <form method="post" action="<?= $accion ?>">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required
                                       value="<?= esc(old('nombre', $usuario['nombre'] ?? '')) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" name="email" required
                                       value="<?= esc(old('email', $usuario['email'] ?? '')) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select class="form-control" name="rol_id" required>
                                    <option value="">Elige un rol</option>
                                    <?php foreach ($roles as $rol) : ?>
                                        <?php $sel = (string) old('rol_id', $usuario['rol_id'] ?? '') === (string) $rol['id']; ?>
                                        <option value="<?= $rol['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                            <?= esc($rol['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr class="horizontal dark">

                            <div class="mb-3">
                                <label class="form-label">Contrasena</label>
                                <input type="password" class="form-control" name="password"
                                       autocomplete="new-password" <?= $esEdicion ? '' : 'required' ?>>
                                <small class="text-xs text-secondary">
                                    <?= $esEdicion
                                        ? 'Dejalo en blanco para conservar la contrasena actual.'
                                        : 'Minimo 6 caracteres.' ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Repetir contrasena</label>
                                <input type="password" class="form-control" name="password_confirm"
                                       autocomplete="new-password" <?= $esEdicion ? '' : 'required' ?>>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <?php $activo = (int) old('activo', $usuario['activo'] ?? 1) === 1; ?>
                                <input class="form-check-input" type="checkbox" value="1"
                                       id="activo" name="activo" <?= $activo ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">Usuario activo</label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary mb-0">
                                    <?= $esEdicion ? 'Guardar cambios' : 'Crear usuario' ?>
                                </button>
                                <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary mb-0">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>