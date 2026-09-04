<div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">No. Recibo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contador</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Periodo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                <th class="text-secondary opacity-7"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recibos)) : ?>
                <tr>
                    <td colspan="7" class="text-center text-sm text-secondary py-4">
                        <?= ($filtros['busqueda'] !== '' || $filtros['periodo_id'] !== '' || $filtros['estado'] !== '')
                            ? 'No hay recibos que coincidan con la busqueda.'
                            : 'Aun no se ha emitido ningun recibo.' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php
                $meses = [1 => 'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            ?>

            <?php foreach ($recibos as $r) : ?>
                <tr>
                    <td class="ps-4">
                        <span class="text-sm font-weight-bold"><?= esc($r['numero']) ?></span>
                    </td>
                    <td><span class="text-sm text-secondary"><?= esc($r['cliente_nombre']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($r['numero_contador']) ?></span></td>
                    <td>
                        <span class="text-sm text-secondary">
                            <?= esc(($meses[(int) $r['periodo_mes']] ?? '') . ' ' . $r['periodo_anio']) ?>
                        </span>
                    </td>
                    <td><span class="text-sm font-weight-bold">Q<?= number_format((float) $r['total'], 2) ?></span></td>
                    <td>
                        <?php
                            $colores = [
                                'pendiente' => 'bg-gradient-warning',
                                'pagado'    => 'bg-gradient-success',
                                'vencido'   => 'bg-gradient-danger',
                                'anulado'   => 'bg-gradient-secondary',
                            ];
                            $color = $colores[$r['estado']] ?? 'bg-gradient-secondary';
                        ?>
                        <span class="badge badge-sm <?= $color ?>"><?= esc(ucfirst($r['estado'])) ?></span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('recibos/ver/' . $r['id']) ?>"
                           class="btn btn-link text-dark px-2 mb-0">Ver</a>
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