<?= view('layouts/header', [
        'title'      => 'Pagos pendientes - Oficina del Agua',
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
            <div class="card-header pb-0">
                <h6 class="mb-0">Recibos pendientes de pago</h6>
            </div>

            <div class="card-body pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="buscador" class="form-label text-xs">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="buscador"
                               placeholder="No. de recibo, cliente o contador"
                               value="<?= esc($filtros['busqueda']) ?>" autocomplete="off">
                    </div>
                    <div class="col-8 col-md-4">
                        <label for="filtro-periodo" class="form-label text-xs">Periodo</label>
                        <select class="form-control form-control-sm" id="filtro-periodo">
                            <option value="">Todos</option>
                            <?php $mesesLargos = [1 => 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; ?>
                            <?php foreach ($periodos as $p) : ?>
                                <option value="<?= $p['id'] ?>"
                                    <?= (string) $filtros['periodo_id'] === (string) $p['id'] ? 'selected' : '' ?>>
                                    <?= esc(($mesesLargos[(int) $p['mes']] ?? '') . ' ' . $p['anio']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4 col-md-1 text-end">
                        <span class="text-xs text-secondary d-none" id="cargando">Buscando...</span>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-3 pb-2" id="contenedor-tabla">
                <?= view('pagos/_tabla', [
                        'pendientes' => $pendientes,
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
    const periodo    = document.getElementById('filtro-periodo');
    const contenedor = document.getElementById('contenedor-tabla');
    const cargando   = document.getElementById('cargando');
    const urlBase    = '<?= base_url('pagos/tabla') ?>';

    let temporizador = null;
    let peticion     = null;

    function construirUrl(pagina) {
        const params = new URLSearchParams();
        if (buscador.value.trim() !== '') params.set('q', buscador.value.trim());
        if (periodo.value !== '')         params.set('periodo_id', periodo.value);
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

            history.replaceState(null, '', '<?= base_url('pagos') ?>?' +
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

    periodo.addEventListener('change', () => actualizar(1));

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