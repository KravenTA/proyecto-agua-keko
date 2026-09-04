<?= view('layouts/header', [
        'title'      => 'Dashboard - Oficina del Agua',
        'body_class' => 'g-sidenav-show bg-gray-100',
    ]) ?>

<?= view('layouts/sidenav') ?>

<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">

        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-0">Estado de cuenta de clientes</h6>
            </div>

            <div class="card-body pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="buscador" class="form-label text-xs">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="buscador"
                               placeholder="Nombre o telefono del cliente"
                               value="<?= esc($filtros['busqueda']) ?>" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="filtro-estado" class="form-label text-xs">Estado</label>
                        <select class="form-control form-control-sm" id="filtro-estado">
                            <option value="">Todos</option>
                            <option value="al_dia" <?= $filtros['estado'] === 'al_dia' ? 'selected' : '' ?>>Al dia</option>
                            <option value="pendiente" <?= $filtros['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-1 text-end">
                        <span class="text-xs text-secondary d-none" id="cargando">Buscando...</span>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-3 pb-2" id="contenedor-tabla">
                <?= view('dashboard/_tabla', [
                        'clientes' => $clientes,
                        'filtros'  => $filtros,
                    ]) ?>
            </div>
        </div>

    </div>
</main>

<script>
(function () {
    const buscador   = document.getElementById('buscador');
    const estado     = document.getElementById('filtro-estado');
    const contenedor = document.getElementById('contenedor-tabla');
    const cargando   = document.getElementById('cargando');

    let temporizador = null;
    let controlador  = null;

    function construirUrl() {
        const params = new URLSearchParams();
        if (buscador.value.trim() !== '') params.set('q', buscador.value.trim());
        if (estado.value !== '') params.set('estado', estado.value);
        return '<?= base_url('dashboard/tabla') ?>?' + params.toString();
    }

    async function actualizar() {
        if (controlador) controlador.abort();
        controlador = new AbortController();

        cargando.classList.remove('d-none');

        try {
            const respuesta = await fetch(construirUrl(), {
                signal: controlador.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (! respuesta.ok) throw new Error('Respuesta ' + respuesta.status);

            contenedor.innerHTML = await respuesta.text();

            const paramsUrl = new URLSearchParams();
            if (buscador.value.trim() !== '') paramsUrl.set('q', buscador.value.trim());
            if (estado.value !== '') paramsUrl.set('estado', estado.value);
            const query = paramsUrl.toString();
            history.replaceState(null, '', '<?= base_url('dashboard') ?>' + (query ? '?' + query : ''));

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
        temporizador = setTimeout(actualizar, 350);
    });

    estado.addEventListener('change', actualizar);
})();
</script>

<?= view('layouts/footer') ?>