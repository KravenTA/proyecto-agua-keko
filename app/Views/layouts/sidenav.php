<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
       id="sidenav-main">

    <div class="sidenav-header">
        <a class="navbar-brand m-0 d-flex align-items-center" href="<?= base_url('usuarios') ?>">
            <img src="<?= base_url('assets/img/logo-ct-dark.png') ?>" class="navbar-brand-img h-100" alt="logo">
            <span class="ms-2 font-weight-bold">Oficina del Agua</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('usuarios*') ? 'active' : '' ?>"
                       href="<?= base_url('usuarios') ?>">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('clientes*') ? 'active' : '' ?>"
                        href="<?= base_url('clientes') ?>">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-circle-08 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Clientes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('contadores*') ? 'active' : '' ?>"
                       href="<?= base_url('contadores') ?>">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-settings-gear-65 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Contadores</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Lector'], true)) : ?>
    <li class="nav-item">
        <a class="nav-link <?= url_is('lecturas*') ? 'active' : '' ?>"
           href="<?= base_url('lecturas/pendientes') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="ni ni-bullet-list-67 text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Pendientes</span>
        </a>
    </li>
<?php endif; ?>

            <?php /*
                Modulos pendientes. Cada equipo descomenta el suyo cuando lo implemente:
                Tarifas (ni-money-coins), Lecturas (ni-bullet-list-67), Pagos (ni-chart-bar-32)
            */ ?>

        </ul>
    </div>

    <div class="sidenav-footer mx-3 mt-3 pt-3 border-top">
        <p class="text-xs text-secondary mb-1">
            <?= esc(session()->get('usuario_nombre')) ?>
        </p>
        <p class="text-xs text-secondary mb-2">
            <?= esc(session()->get('rol_nombre')) ?>
        </p>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm w-100 mb-0">
            Cerrar sesion
        </a>
    </div>

</aside>