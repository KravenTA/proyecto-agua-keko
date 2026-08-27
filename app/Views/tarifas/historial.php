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

                        <?php if ($tarifaVigente) : ?>
                            <div class="alert alert-info text-white mb-4"
                                 style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%); border: none;">
                                <strong>Tarifa vigente hoy:</strong>
                                Q<?= esc(number_format((float) $tarifaVigente['precio_unitario'], 2)) ?> por unidad
                                (desde <?= esc($tarifaVigente['vigente_desde']) ?>)
                            </div>
                        <?php else : ?>
                            <div class="alert alert-warning">
                                No hay ninguna tarifa activa vigente hoy.
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('tarifas/historial') ?>" method="get" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label ms-1">Desde</label>
                                <input type="date" name="desde" class="form-control"
                                       value="<?= esc($filtros['desde'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label ms-1">Hasta</label>
                                <input type="date" name="hasta" class="form-control"
                                       value="<?= esc($filtros['hasta'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit"
                                    class="btn w-100 text-white"
                                    style="background: linear-gradient(135deg, #1565c0 0%, #29b6f6 100%);">
                                    Filtrar
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Precio por unidad</th>
                                        <th>Cuota mínima</th>
                                        <th>Vigente desde</th>
                                        <th>Vigente hasta</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tarifas)) : ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No hay tarifas registradas en ese rango.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($tarifas as $tarifa) : ?>
                                        <?php
                                            $esLaVigente = $tarifaVigente && $tarifa['id'] === $tarifaVigente['id'];
                                        ?>
                                        <tr<?= $esLaVigente ? ' class="table-info"' : '' ?>>
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