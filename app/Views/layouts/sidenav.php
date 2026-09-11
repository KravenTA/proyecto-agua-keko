<?= view('layouts/navbar-movil') ?>
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
       id="sidenav-main">

    <div class="sidenav-header">
        <a class="navbar-brand m-0 d-flex align-items-center" href="<?= base_url('usuarios') ?>">
<img src="<?= base_url('assets/img/logo_keko.png') ?>" class="navbar-brand-img h-100" alt="Keko">        </a>
    </div>

    <hr class="horizontal dark mt-0">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('dashboard*') ? 'active' : '' ?>"
                       href="<?= base_url('dashboard') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('usuarios*') ? 'active' : '' ?>"
                       href="<?= base_url('usuarios') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('periodos*') ? 'active' : '' ?>"
                       href="<?= base_url('periodos') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="16" y1="3" x2="16" y2="7"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Periodos</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('clientes*') ? 'active' : '' ?>"
                        href="<?= base_url('clientes') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 21c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5"/><circle cx="17.5" cy="8.5" r="2.6"/><path d="M15.5 14.3c3 .3 5.3 2.8 5.5 5.9"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Clientes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('contadores*') ? 'active' : '' ?>"
                       href="<?= base_url('contadores') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15a8 8 0 1 1 16 0"/><line x1="12" y1="15" x2="15.5" y2="10.5"/><circle cx="12" cy="15" r="1"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Contadores</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('lecturas') ? 'active' : '' ?>"
                        href="<?= base_url('lecturas') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c4 5 7 8.4 7 12a7 7 0 0 1-14 0c0-3.6 3-7 7-12z"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Lecturas</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Lector'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('lecturas/pendientes*') || url_is('lecturas/registrar*')) ? 'active' : '' ?>"
                        href="<?= base_url('lecturas/pendientes') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M7 9l1.5 1.5L11 8"/><line x1="13" y1="9" x2="18" y2="9"/><path d="M7 15l1.5 1.5L11 14"/><line x1="13" y1="15" x2="18" y2="15"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Pendientes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('recibos*') ? 'active' : '' ?>"
                        href="<?= base_url('recibos') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-2.5-1.5L13 21l-2.5-1.5L8 21l-2-1.5z"/><line x1="8.5" y1="8" x2="15.5" y2="8"/><line x1="8.5" y1="12" x2="15.5" y2="12"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Recibos</span>
                    </a>
                </li>
            <?php endif; ?>


            <?php if (in_array(session()->get('rol_nombre'), ['Administrador', 'Secretaria'], true)) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('pagos*') ? 'active' : '' ?>"
                       href="<?= base_url('pagos') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="12"/><line x1="10" y1="21" x2="10" y2="7"/><line x1="16" y1="21" x2="16" y2="14"/><line x1="20" y1="21" x2="20" y2="4"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Pagos</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('rol_nombre') === 'Administrador') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('tarifas*') ? 'active' : '' ?>"
                       href="<?= base_url('tarifas/historial') ?>">
                        <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="9" cy="7" rx="6" ry="3"/><path d="M3 7v5c0 1.7 2.7 3 6 3s6-1.3 6-3V7"/><path d="M3 12v5c0 1.7 2.7 3 6 3 1.1 0 2.1-.15 3-.4"/><path d="M15 9.3c2.9.4 5 1.6 5 3.2v5c0 1.7-2.7 3-6 3-.7 0-1.4-.06-2-.17"/></svg>
                        </span>
                        <span class="nav-link-text ms-1">Tarifas</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= url_is('equipo*') ? 'active' : '' ?>"
                   href="<?= base_url('equipo') ?>">
                    <span class="nav-icon me-2 d-flex align-items-center justify-content-center">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="3"/><path d="M2.5 20.5c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5"/><path d="M16.5 8a3 3 0 1 1 0-6"/><path d="M14.5 14.3c3 .3 5.3 2.8 5.5 5.9"/></svg>
                    </span>
                    <span class="nav-link-text ms-1">Equipo</span>
                </a>
            </li>

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