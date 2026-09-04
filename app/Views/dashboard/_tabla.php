<div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Cliente</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Telefono</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Direccion</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Meses pendientes</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)) : ?>
                <tr>
                    <td colspan="5" class="text-center text-sm text-secondary py-4">
                        <?= ($filtros['busqueda'] !== '' || $filtros['estado'] !== '')
                            ? 'No hay clientes que coincidan con la busqueda.'
                            : 'Aun no hay clientes registrados.' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($clientes as $c) : ?>
                <tr>
                    <td class="ps-4">
                        <span class="text-sm font-weight-bold"><?= esc($c['nombre']) ?></span>
                    </td>
                    <td><span class="text-sm text-secondary"><?= esc($c['telefono']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['direccion']) ?></span></td>
                    <td>
                        <span class="text-sm text-secondary">
                            <?= (int) $c['meses_pendientes'] > 0 ? (int) $c['meses_pendientes'] : '-' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($c['estado_cuenta'] === 'al_dia') : ?>
                            <span class="badge badge-sm bg-gradient-success">Al dia</span>
                        <?php else : ?>
                            <span class="badge badge-sm bg-gradient-warning">Pendiente</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>