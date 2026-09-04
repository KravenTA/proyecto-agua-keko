<?= view('layouts/header', [
        'title'      => 'Contadores - Oficina de Agua',
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
                <h6 class="mb-0">Contadores registrados</h6>
                <a href="<?= base_url('contadores/nuevo') ?>" class="btn btn-primary btn-sm mb-0">
                    Nuevo contador
                </a>
            </div>

            <div class="card-body pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="buscador" class="form-label text-xs">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="buscador"
                               placeholder="Codigo, cliente o direccion"
                               value="<?= esc($filtros['busqueda']) ?>" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="filtro-sector" class="form-label text-xs">Sector</label>
                        <select class="form-control form-control-sm" id="filtro-sector">
                            <option value="">Todos</option>
                            <?php foreach ($sectores as $s) : ?>
                                <option value="<?= $s['id'] ?>"
                                    <?= (string) $filtros['sector_id'] === (string) $s['id'] ? 'selected' : '' ?>>
                                    <?= esc($s['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="filtro-activo" class="form-label text-xs">Estado</label>
                        <select class="form-control form-control-sm" id="filtro-activo">
                            <option value="">Todos</option>
                            <option value="1" <?= $filtros['activo'] === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= $filtros['activo'] === '0' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-1 text-end">
                        <span class="text-xs text-secondary d-none" id="cargando">Buscando...</span>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-3 pb-2" id="contenedor-tabla">
                <?= view('contadores/_tabla', [
                        'contadores' => $contadores,
                        'pager'      => $pager,
                        'filtros'    => $filtros,
                    ]) ?>
            </div>
        </div>

    </div>
</main>

<script>
(function () {
    const buscador   = document.getElementById('buscador');
    const sector     = document.getElementById('filtro-sector');
    const activo     = document.getElementById('filtro-activo');
    const contenedor = document.getElementById('contenedor-tabla');
    const cargando   = document.getElementById('cargando');
    const urlBase    = '<?= base_url('contadores/tabla') ?>';

    let temporizador = null;
    let peticion     = null;

    function construirUrl(pagina) {
        const params = new URLSearchParams();
        if (buscador.value.trim() !== '') params.set('q', buscador.value.trim());
        if (sector.value !== '')          params.set('sector_id', sector.value);
        if (activo.value !== '')          params.set('activo', activo.value);
        if (pagina)                       params.set('page', pagina);
        return urlBase + '?' + params.toString();
    }

    async function actualizar(pagina) {
        if (peticion) peticion.abort();
        peticion = new AbortController();

        cargando.classList.remove('d-none');

        try {
            const respuesta = await fetch(construirUrl(pagina), {
                signal: peticion.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (! respuesta.ok) throw new Error('Respuesta ' + respuesta.status);

            contenedor.innerHTML = await respuesta.text();

            history.replaceState(null, '', '<?= base_url('contadores') ?>?' +
                construirUrl(pagina).split('?')[1]);

        } catch (error) {
            if (error.name !== 'AbortError') {
                contenedor.innerHTML =
                    '<p class="text-center text-sm text-danger py-4">' +
                    'No se pudo cargar la lista. Revisa tu conexion e intenta de nuevo.</p>';
            }
        } finally {
            cargando.classList.add('d-none');
        }
    }

    buscador.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(() => actualizar(1), 350);
    });

    sector.addEventListener('change', () => actualizar(1));
    activo.addEventListener('change', () => actualizar(1));

    contenedor.addEventListener('click', function (evento) {
        const enlace = evento.target.closest('.pagination a');
        if (! enlace) return;
        evento.preventDefault();
        const pagina = new URL(enlace.href, window.location.origin).searchParams.get('page');
        actualizar(pagina || 1);
    });
})();
</script>

<?= view('layouts/footer') ?>