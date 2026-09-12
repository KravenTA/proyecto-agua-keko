<?= view('layouts/header', [
        'title'      => 'Auditoría - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">

        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-1">Auditoría</h6>
                <p class="text-sm text-secondary mb-0">
                    Registro de altas, ediciones y bajas de usuarios y tarifas (últimos 200 movimientos).
                </p>
            </div>

            <div class="card-body pb-2">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-12 col-md-4">
                        <label for="filtro-tabla" class="form-label text-xs">Módulo</label>
                        <select class="form-control form-control-sm" id="filtro-tabla" onchange="location.href='<?= base_url('auditoria') ?>?tabla=' + this.value">
                            <option value="">Todos</option>
                            <option value="usuarios" <?= $filtros['tabla'] === 'usuarios' ? 'selected' : '' ?>>Usuarios</option>
                            <option value="tarifas" <?= $filtros['tabla'] === 'tarifas' ? 'selected' : '' ?>>Tarifas</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Fecha</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Módulo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acción</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registros)) : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-sm text-secondary py-4">
                                        No hay movimientos de auditoría registrados todavía.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($registros as $r) : ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-sm text-secondary">
                                            <?= esc(date('d/m/Y H:i', strtotime($r['created_at']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-secondary">
                                            <?= esc($r['usuario_nombre'] ?? 'Sistema') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-bold text-capitalize">
                                            <?= esc($r['tabla']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $colores = ['alta' => 'success', 'edicion' => 'info', 'baja' => 'danger'];
                                            $color   = $colores[$r['accion']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-sm bg-gradient-<?= $color ?> text-capitalize">
                                            <?= esc($r['accion']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-secondary">#<?= (int) $r['registro_id'] ?></span>
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