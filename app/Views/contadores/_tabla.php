<div class="table-responsive p-0">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Codigo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sector</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referencia</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                <th class="text-secondary opacity-7"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contadores)) : ?>
                <tr>
                    <td colspan="7" class="text-center text-sm text-secondary py-4">
                        <?= ($filtros['busqueda'] !== '' || $filtros['sector_id'] !== '' || $filtros['activo'] !== '')
                            ? 'No hay contadores que coincidan con la busqueda.'
                            : 'Aun no hay contadores registrados.' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($contadores as $c) : ?>
                <tr>
                    <td class="ps-4">
                        <span class="text-sm font-weight-bold"><?= esc($c['numero_serie']) ?></span>
                    </td>
                    <td><span class="text-sm text-secondary"><?= esc($c['cliente_nombre']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['sector_nombre']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['referencia'] ?? '-') ?></span></td>
                    <td>
                        <span class="text-sm text-secondary">
                            <?= esc(ucwords(str_replace('_', ' ', (string) ($c['tipo_servicio'] ?? '-')))) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ((int) $c['activo'] === 1) : ?>
                            <span class="badge badge-sm bg-gradient-success">Activo</span>
                        <?php else : ?>
                            <span class="badge badge-sm bg-gradient-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('contadores/editar/' . $c['id']) ?>"
                           class="btn btn-link text-dark px-2 mb-0">Editar</a>

                        <?php if ((int) $c['activo'] === 1) : ?>
                            <form action="<?= base_url('contadores/desactivar/' . $c['id']) ?>"
                                  method="post" class="d-inline"
                                  onsubmit="return confirm('Se desactivara este contador. Podras reactivarlo despues.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-link text-danger px-2 mb-0">Desactivar</button>
                            </form>
                        <?php else : ?>
                            <form action="<?= base_url('contadores/activar/' . $c['id']) ?>"
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

<?php if ($pager->getPageCount() > 1) : ?>
    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('default', 'bootstrap5') ?>
    </div>
<?php endif; ?>