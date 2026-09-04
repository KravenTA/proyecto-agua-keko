<div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Contador</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sector</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Periodo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Anterior</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actual</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Consumo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Monto</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Recibo</th>
                <th class="text-secondary opacity-7"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lecturas)) : ?>
                <tr>
                    <td colspan="10" class="text-center text-sm text-secondary py-4">
                        <?= ($filtros['busqueda'] !== '' || $filtros['periodo_id'] !== '' || $filtros['sector_id'] !== '')
                            ? 'No hay lecturas que coincidan con la busqueda.'
                            : 'Aun no se ha registrado ninguna lectura.' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php $meses = [1 => 'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic']; ?>

            <?php foreach ($lecturas as $l) : ?>
                <tr>
                    <td class="ps-4">
                        <span class="text-sm font-weight-bold"><?= esc($l['numero_contador']) ?></span>
                    </td>
                    <td><span class="text-sm text-secondary"><?= esc($l['cliente_nombre']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($l['sector_nombre']) ?></span></td>
                    <td>
                        <span class="text-sm text-secondary">
                            <?= esc(($meses[(int) $l['periodo_mes']] ?? '') . ' ' . $l['periodo_anio']) ?>
                        </span>
                    </td>
                    <td><span class="text-sm text-secondary"><?= number_format((float) $l['lectura_anterior'], 2) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= number_format((float) $l['lectura_actual'], 2) ?></span></td>
                    <td>
                        <span class="text-sm text-secondary">
                            <?= number_format((float) $l['consumo'], 2) ?> m³
                        </span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold">
                            <?= $l['monto'] !== null ? 'Q' . number_format((float) $l['monto'], 2) : '-' ?>
                        </span>
                    </td>
                    <td>
                        <?php if (! empty($l['recibo_numero'])) : ?>
                            <span class="text-sm text-secondary"><?= esc($l['recibo_numero']) ?></span>
                        <?php else : ?>
                            <span class="badge badge-sm bg-gradient-secondary">Sin recibo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <?php if (! empty($l['recibo_id'])) : ?>
                            <a href="<?= base_url('recibos/ver/' . $l['recibo_id']) ?>"
                               class="btn btn-link text-dark px-2 mb-0">Ver recibo</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($pager->getPageCount() > 1) : ?>
    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('default', 'bootstrap5') ?>
    </div>
<?php endif; ?>