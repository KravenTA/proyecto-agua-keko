<?= view('layouts/header', [
        'title'      => 'Usuarios - Oficina del Agua',
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
                <h6 class="mb-0">Usuarios del sistema</h6>
                <a href="<?= base_url('usuarios/nuevo') ?>" class="btn btn-primary btn-sm mb-0">
                    Nuevo usuario
                </a>
            </div>

            <div class="card-body pb-2">
                <form method="get" action="<?= base_url('usuarios') ?>" class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label text-xs">Buscar</label>
                        <input type="text" class="form-control form-control-sm" name="busqueda"
                               placeholder="Nombre o correo"
                               value="<?= esc($filtros['busqueda']) ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label text-xs">Rol</label>
                        <select class="form-control form-control-sm" name="rol_id">
                            <option value="">Todos</option>
                            <?php foreach ($roles as $rol) : ?>
                                <option value="<?= $rol['id'] ?>"
                                    <?= (string) $filtros['rol_id'] === (string) $rol['id'] ? 'selected' : '' ?>>
                                    <?= esc($rol['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-xs">Estado</label>
                        <select class="form-control form-control-sm" name="activo">
                            <option value="">Todos</option>
                            <option value="1" <?= $filtros['activo'] === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= $filtros['activo'] === '0' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-secondary btn-sm mb-0">Filtrar</button>
                    </div>
                </form>
            </div>

            <div class="card-body px-0 pt-3 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Nombre</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rol</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)) : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-sm text-secondary py-4">
                                        No hay usuarios que coincidan con el filtro.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($usuarios as $u) : ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-sm font-weight-bold"><?= esc($u['nombre']) ?></span>
                                    </td>
                                    <td><span class="text-sm text-secondary"><?= esc($u['email']) ?></span></td>
                                    <td><span class="text-sm text-secondary"><?= esc($u['rol_nombre'] ?? '-') ?></span></td>
                                    <td>
                                        <?php if ((int) $u['activo'] === 1) : ?>
                                            <span class="badge badge-sm bg-gradient-success">Activo</span>
                                        <?php else : ?>
                                            <span class="badge badge-sm bg-gradient-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('usuarios/editar/' . $u['id']) ?>"
                                           class="btn btn-link text-dark px-2 mb-0">Editar</a>

                                        <?php if ((int) $u['activo'] === 1) : ?>
                                            <form action="<?= base_url('usuarios/eliminar/' . $u['id']) ?>"
                                                  method="post" class="d-inline"
                                                  onsubmit="return confirm('Se desactivara este usuario. Podras reactivarlo despues.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-link text-danger px-2 mb-0">Desactivar</button>
                                            </form>
                                        <?php else : ?>
                                            <form action="<?= base_url('usuarios/activar/' . $u['id']) ?>"
                                                  method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-link text-success px-2 mb-0">Activar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
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