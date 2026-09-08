<?= view('layouts/header', [
        'title'      => 'Periodos - Oficina del Agua',
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

        <?php if (! $abierto) : ?>
            <div class="alert alert-warning text-white" role="alert">
                No hay ningun periodo abierto. Mientras no lo haya, el lector no
                puede registrar lecturas.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Nuevo periodo</h6>
                        <p class="text-xs text-secondary mb-0">
                            Al abrir un periodo se cierra el anterior: solo puede
                            haber uno abierto a la vez.
                        </p>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('periodos/crear') ?>">
                            <?= csrf_field() ?>

                            <?php
                                $mesesLargos = [1 => 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                            ?>

                            <div class="mb-3">
                                <label class="form-label">Mes</label>
                                <select class="form-control" name="mes" required>
                                    <?php foreach ($mesesLargos as $num => $nombre) : ?>
                                        <option value="<?= $num ?>"
                                            <?= (int) old('mes', date('n')) === $num ? 'selected' : '' ?>>
                                            <?= $nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Año</label>
                                <input type="number" class="form-control" name="anio"
                                       min="2020" max="2100" required
                                       value="<?= esc(old('anio', date('Y'))) ?>">
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" value="1"
                                       id="abrir" name="abrir" checked>
                                <label class="form-check-label" for="abrir">
                                    Abrirlo de inmediato
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-0">
                                Crear periodo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Periodos registrados</h6>
                    </div>

                    <div class="card-body px-0 pt-3 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Periodo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lecturas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sin leer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($periodos)) : ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-sm text-secondary py-4">
                                                Aun no hay periodos registrados.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($periodos as $p) : ?>
                                        <tr>
                                            <td class="ps-4">
                                                <span class="text-sm font-weight-bold">
                                                    <?= esc(($mesesLargos[(int) $p['mes']] ?? '') . ' ' . $p['anio']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-secondary"><?= $p['total_lecturas'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($p['estado'] === 'abierto' && $p['sin_lectura'] > 0) : ?>
                                                    <span class="text-sm text-secondary"><?= $p['sin_lectura'] ?></span>
                                                <?php else : ?>
                                                    <span class="text-sm text-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($p['estado'] === 'abierto') : ?>
                                                    <span class="badge badge-sm bg-gradient-success">Abierto</span>
                                                <?php else : ?>
                                                    <span class="badge badge-sm bg-gradient-secondary">Cerrado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if ($p['estado'] === 'abierto') : ?>
                                                    <form action="<?= base_url('periodos/cerrar/' . $p['id']) ?>"
                                                          method="post" class="d-inline"
                                                          onsubmit="return confirm('Se cerrara este periodo. Los contadores sin lectura quedaran sin recibo.');">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-link text-danger px-2 mb-0">Cerrar</button>
                                                    </form>
                                                <?php else : ?>
                                                    <form action="<?= base_url('periodos/abrir/' . $p['id']) ?>"
                                                          method="post" class="d-inline"
                                                          onsubmit="return confirm('Se abrira este periodo y se cerrara el que este abierto.');">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-link text-success px-2 mb-0">Abrir</button>
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
        </div>

    </div>
</main>

<?= view('layouts/footer') ?>