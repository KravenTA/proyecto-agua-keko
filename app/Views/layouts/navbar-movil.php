<?php
    // Barra superior visible solo en pantallas chicas. El sidenav de Soft UI
    // se oculta en movil y necesita este boton para abrirse; sin el, no hay
    // navegacion desde el celular.
?>
<nav class="navbar navbar-expand-lg bg-white shadow-sm position-sticky top-0 z-index-3 d-xl-none">
    <div class="container-fluid px-3 py-2">
        <a class="navbar-brand m-0 d-flex align-items-center" href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/img/logo-ct-dark.png') ?>" style="height: 28px; width: auto;" alt="logo">
            <span class="ms-2 font-weight-bold text-sm">Oficina de Agua</span>
        </a>

        <button class="navbar-toggler shadow-none border-0 p-2" type="button" id="iconNavbarSidenav">
            <span class="navbar-toggler-icon">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
    </div>
</nav>
<script>
(function () {
    // Soft UI abre el sidenav agregando g-sidenav-pinned al body, pero no
    // lo cierra al tocar fuera. En movil eso deja el menu abierto tapando
    // el contenido.
    document.addEventListener('click', function (evento) {
        const body = document.body;

        if (! body.classList.contains('g-sidenav-pinned')) return;

        const sidenav = document.getElementById('sidenav-main');
        const boton   = document.getElementById('iconNavbarSidenav');

        if (! sidenav) return;

        const tocoDentro = sidenav.contains(evento.target)
                        || (boton && boton.contains(evento.target));

        if (! tocoDentro) {
            body.classList.remove('g-sidenav-pinned');
        }
    });
})();
</script>