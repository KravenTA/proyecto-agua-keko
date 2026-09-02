<?= view('layouts/header', ['title' => 'Historial de tarifas - Oficina del Agua', 'body_class' => 'g-sidenav-show bg-gray-100']) ?>

<main class="main-content mt-0">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1"
                             style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                            <h5 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                Historial de tarifas
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4">
                            <?php foreach ($tipos as $t) : ?>
                                <?php $vigente = $tarifasVigentes[$t] ?? null; ?>
                                <div class="col-md-3 mb-2">
                                    <div class="p-3 rounded"
                                         style="background: <?= $vigente ? 'linear-gradient(135deg, #1565c0 0%, #29b6f6 100%)' : '#e9ecef' ?>; color: <?= $vigente ? '#fff' : '#333' ?>;">
                                        <strong><?= esc(ucwords(str_replace('_', ' ', $t))) ?></strong><br>
                                        <?php if ($vigente) : ?>
                                            Q<?= esc(number_format((float) $vigente['precio_unitario'], 2)) ?>
                                            <?= $t !== 'exceso' ? '/ ' . esc(number_format((float) $vigente['volumen_incluido_litros'])) . ' L' : '/ m³' ?>
                                        <?php else : ?>
                                            Sin tarifa vigente
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <form action="<?= base_url('tarifas/historial') ?>" method="get" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label ms-1">Tipo</label>
                                <select name="tipo" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($tipos as $t) : ?>
                                        <option value="<?= esc($t) ?>" <?= ($filtros['tipo'] ?? '') === $t ? 'selected' : '' ?>>
                                            <?= esc(ucwords(str_replace('_', ' ', $t))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label ms-1">Desde</label>
                                <input type="date" name="desde" class="form-control"
                                    value="<?= esc($filtros['desde'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label ms-1">Hasta</label>
                                <input type="date" name="hasta" class="form-control"
                                    value="<?= esc($filtros['hasta'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label ms-1 d-block invisible">Filtrar</label>
                                <button type="submit"
                                    class="btn w-100 text-white m-0"
                                    style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%); height: 45px;">
                                    Filtrar
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Volumen incluido</th>
                                        <th>Precio</th>
                                        <th>Cuota mínima</th>
                                        <th>Vigente desde</th>
                                        <th>Vigente hasta</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tarifas)) : ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                No hay tarifas registradas en ese rango.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($tarifas as $tarifa) : ?>
                                        <?php
                                            $vigenteDeSuTipo = $tarifasVigentes[$tarifa['tipo']] ?? null;
                                            $esLaVigente     = $vigenteDeSuTipo && $tarifa['id'] === $vigenteDeSuTipo['id'];
                                        ?>
                                        <tr<?= $esLaVigente ? ' class="table-info"' : '' ?>>
                                            <td><?= esc(ucwords(str_replace('_', ' ', $tarifa['tipo'] ?? '—'))) ?></td>
                                            <td><?= $tarifa['volumen_incluido_litros'] ? esc(number_format((float) $tarifa['volumen_incluido_litros'])) . ' L' : '—' ?></td>
                                            <td>Q<?= esc(number_format((float) $tarifa['precio_unitario'], 2)) ?></td>
                                            <td>Q<?= esc(number_format((float) $tarifa['cuota_minima'], 2)) ?></td>
                                            <td><?= esc($tarifa['vigente_desde']) ?></td>
                                            <td><?= esc($tarifa['vigente_hasta'] ?? 'Sin cerrar') ?></td>
                                            <td>
                                                <?php if (! $tarifa['activo']) : ?>
                                                    <span class="badge bg-secondary">Inactiva</span>
                                                <?php elseif ($esLaVigente) : ?>
                                                    <span class="badge bg-success">Vigente hoy</span>
                                                <?php else : ?>
                                                    <span class="badge bg-light text-dark">Activa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('tarifas/editar/' . $tarifa['id']) ?>">Editar</a>
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