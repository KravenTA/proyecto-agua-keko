<?= view('layouts/header', [
        'title'      => 'Clientes - Oficina del Agua',
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
                <h6 class="mb-0">Clientes registrados</h6>
                <a href="<?= base_url('clientes/nuevo') ?>" class="btn btn-primary btn-sm mb-0">
                    Nuevo cliente
                </a>
            </div>

            <div class="card-body pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-7">
                        <label for="buscador" class="form-label text-xs">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="buscador"
                               placeholder="Nombre, telefono o correo"
                               value="<?= esc($termino) ?>" autocomplete="off">
                    </div>
                    <div class="col-8 col-md-3">
                        <label for="filtro-activo" class="form-label text-xs">Estado</label>
                        <select class="form-control form-control-sm" id="filtro-activo">
                            <option value="">Todos</option>
                            <option value="1" <?= $activo === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= $activo === '0' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-2 text-end">
                        <span class="text-xs text-secondary d-none" id="cargando">Buscando...</span>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-3 pb-2" id="contenedor-tabla">
                <?= view('clientes/_tabla', [
                        'clientes' => $clientes,
                        'pager'    => $pager,
                        'termino'  => $termino,
                        'activo'   => $activo,
                    ]) ?>
            </div>
        </div>

    </div>
</main>

<script>
(function () {
    const buscador   = document.getElementById('buscador');
    const filtro     = document.getElementById('filtro-activo');
    const contenedor = document.getElementById('contenedor-tabla');
    const cargando   = document.getElementById('cargando');
    const urlBase    = '<?= base_url('clientes/tabla') ?>';

    let temporizador = null;
    let peticion     = null;

    function construirUrl(pagina) {
        const params = new URLSearchParams();
        if (buscador.value.trim() !== '') params.set('q', buscador.value.trim());
        if (filtro.value !== '')          params.set('activo', filtro.value);
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

            // Refleja la busqueda en la URL sin recargar, para poder compartirla.
            history.replaceState(null, '', '<?= base_url('clientes') ?>?' +
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

    // Espera a que el usuario deje de escribir antes de consultar.
    buscador.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(() => actualizar(1), 500);
    });

    filtro.addEventListener('change', () => actualizar(1));

    // Los enlaces del paginador se generan despues, por eso se escucha
    // el contenedor y no cada enlace por separado.
    contenedor.addEventListener('click', function (evento) {
        const enlace = evento.target.closest('.pagination a');
        if (! enlace) return;

        evento.preventDefault();

        const pagina = new URL(enlace.href, window.location.origin)
            .searchParams.get('page');

        actualizar(pagina || 1);
    });
})();
</script>

<?= view('layouts/footer') ?>