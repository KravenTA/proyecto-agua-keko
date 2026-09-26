<?php
/**
 * Encabezados ordenables (Parcial 2 - ordenamiento de columnas).
 * $orden y $dir vienen del controller (Clientes::leerOrden).
 */
$orden = $orden ?? 'nombre';
$dir   = $dir ?? 'asc';

$encabezado = function (string $columna, string $texto, string $clasesExtra = '') use ($orden, $dir, $termino, $activo): string {
    $esActiva = ($orden === $columna);

    // Si ya esta ordenada ascendente, el siguiente clic la pone descendente.
    $siguiente = ($esActiva && $dir === 'asc') ? 'desc' : 'asc';

    if ($esActiva) {
        $flecha   = $dir === 'asc' ? '▲' : '▼';
        $ariaSort = $dir === 'asc' ? 'ascending' : 'descending';
    } else {
        $flecha   = '⇅';
        $ariaSort = 'none';
    }

    // Enlace normal como respaldo (funciona aunque no cargue el JavaScript).
    $parametros = array_filter([
        'q'      => $termino,
        'activo' => $activo,
        'orden'  => $columna,
        'dir'    => $siguiente,
    ], fn ($valor) => $valor !== '');

    $url = base_url('clientes') . '?' . http_build_query($parametros);

    $colorTexto = $esActiva ? 'text-dark' : 'text-secondary opacity-7';
    $colorFlecha = $esActiva ? '' : ' opacity-5';
    $titulo = 'Ordenar ' . ($siguiente === 'asc' ? 'ascendente' : 'descendente');

    return '<th class="text-uppercase text-xxs font-weight-bolder ' . $colorTexto . ' ' . $clasesExtra . '" aria-sort="' . $ariaSort . '">'
        . '<a href="' . esc($url, 'attr') . '" class="ordenar" data-orden="' . esc($columna, 'attr') . '" data-dir="' . $siguiente . '" title="' . $titulo . '">'
        . esc($texto) . ' <span class="flecha-orden' . $colorFlecha . '">' . $flecha . '</span>'
        . '</a></th>';
};
?>
<div class="table-responsive p-0 tabla-ancha">
    <table class="table align-items-center mb-0">
        <thead>
            <tr>
                <?= $encabezado('nombre', 'Nombre', 'ps-4') ?>
                <?= $encabezado('telefono', 'Telefono') ?>
                <?= $encabezado('direccion', 'Direccion') ?>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Docs</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                <th class="text-secondary opacity-7"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)) : ?>
                <tr>
                    <td colspan="7" class="text-center text-sm text-secondary py-4">
                        <?= ($termino !== '' || $activo !== '')
                            ? 'No hay clientes que coincidan con la busqueda.'
                            : 'Aun no hay clientes registrados.' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($clientes as $c) : ?>
                <tr>
                    <td class="ps-4"><span class="text-sm font-weight-bold"><?= esc($c['nombre']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['telefono']) ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['direccion'] ?? '-') ?></span></td>
                    <td><span class="text-sm text-secondary"><?= esc($c['email'] ?? '-') ?></span></td>
                    <td>
                        <?php if (! empty($c['foto_vivienda'])) : ?>
                            <a href="<?= base_url($c['foto_vivienda']) ?>" target="_blank" class="text-sm">Foto</a>
                        <?php endif; ?>
                        <?php if (! empty($c['foto_vivienda']) && ! empty($c['recibo_luz'])) : ?>
                            <span class="text-sm text-secondary"> · </span>
                        <?php endif; ?>
                        <?php if (! empty($c['recibo_luz'])) : ?>
                            <a href="<?= base_url($c['recibo_luz']) ?>" target="_blank" class="text-sm">Recibo</a>
                        <?php endif; ?>
                        <?php if (empty($c['foto_vivienda']) && empty($c['recibo_luz'])) : ?>
                            <span class="text-sm text-secondary">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int) $c['activo'] === 1) : ?>
                            <span class="badge badge-sm bg-gradient-success">Activo</span>
                        <?php else : ?>
                            <span class="badge badge-sm bg-gradient-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('clientes/editar/' . $c['id']) ?>"
                           class="btn btn-link text-dark px-2 mb-0">Editar</a>

                        <?php if ((int) $c['activo'] === 1) : ?>
                            <form action="<?= base_url('clientes/eliminar/' . $c['id']) ?>"
                                  method="post" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar (desactivar) a este cliente?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-link text-danger px-2 mb-0">Eliminar</button>
                            </form>
                        <?php else : ?>
                            <form action="<?= base_url('clientes/activar/' . $c['id']) ?>"
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