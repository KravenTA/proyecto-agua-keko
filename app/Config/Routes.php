<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->get('tarifas/nueva', 'Tarifas::create');
$routes->post('tarifas', 'Tarifas::store');
$routes->get('tarifas/historial', 'Tarifas::historial');
$routes->get('tarifas/editar/(:num)', 'Tarifas::editar/$1');
$routes->post('tarifas/actualizar/(:num)', 'Tarifas::actualizar/$1');
$routes->post('tarifas/cerrar-vigencia/(:num)', 'Tarifas::cerrarVigencia/$1');

// HU-02: Gestion de usuarios y roles (SDGODA-17)
$routes->group('usuarios', static function ($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('nuevo', 'Usuarios::nuevo');
    $routes->post('crear', 'Usuarios::crear');
    $routes->get('editar/(:num)', 'Usuarios::editar/$1');
    $routes->post('actualizar/(:num)', 'Usuarios::actualizar/$1');
    $routes->post('eliminar/(:num)', 'Usuarios::eliminar/$1');
    $routes->post('activar/(:num)', 'Usuarios::activar/$1');
});

// HU-07/HU-08: Registrar, editar y eliminar cliente (SDGODA-XX)
$routes->group('clientes', static function ($routes) {
    $routes->get('/', 'Clientes::index');
    $routes->get('tabla', 'Clientes::tabla');
    $routes->get('nuevo', 'Clientes::nuevo');
    $routes->post('/', 'Clientes::crear');
    $routes->get('editar/(:num)', 'Clientes::editar/$1');
    $routes->post('actualizar/(:num)', 'Clientes::actualizar/$1');
    $routes->post('eliminar/(:num)', 'Clientes::eliminar/$1');
    $routes->post('activar/(:num)', 'Clientes::activar/$1');
});

// HU-10: Registrar contador/predio y asociarlo a un cliente (SDGODA-25)
$routes->group('contadores', ['filter' => ['auth', 'role:Administrador,Secretaria']], static function ($routes) {
    $routes->get('/', 'Contadores::index');
    $routes->get('tabla', 'Contadores::tabla');
    $routes->get('nuevo', 'Contadores::nuevo');
    $routes->post('/', 'Contadores::crear');

    // HU-11: Editar, activar y desactivar un contador (SDGODA-26)
    $routes->get('editar/(:num)', 'Contadores::editar/$1');
    $routes->post('actualizar/(:num)', 'Contadores::actualizar/$1');
    $routes->post('desactivar/(:num)', 'Contadores::desactivar/$1');
    $routes->post('activar/(:num)', 'Contadores::activar/$1');


});

// HU-12/HU-14: registro de lecturas en campo (SDGODA-27, 37, 38)
$routes->group('lecturas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('pendientes', 'Lecturas::pendientes');
    $routes->get('registrar/(:num)', 'Lecturas::registrar/$1');
    $routes->post('guardar/(:num)', 'Lecturas::guardar/$1');
});

// SDGODA-49: historial de lecturas, para oficina
$routes->group('lecturas', ['filter' => ['auth', 'role:Administrador,Secretaria']], static function ($routes) {
    $routes->get('/', 'Lecturas::index');
    $routes->get('tabla', 'Lecturas::tabla');
});

// SDGODA-39 / SDGODA-47: Recibos emitidos
$routes->group('recibos', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Recibos::index');
    $routes->get('ver/(:num)', 'Recibos::ver/$1');
    $routes->get('tabla', 'Recibos::tabla');
});

// HU-17: Registrar pago de un recibo pendiente (SDGODA-40)
$routes->group('pagos', ['filter' => ['auth', 'role:Administrador,Secretaria']], static function ($routes) {
    $routes->get('/', 'Pagos::index');
    $routes->get('nuevo/(:num)', 'Pagos::nuevo/$1');
    $routes->post('/', 'Pagos::crear');
    $routes->get('tabla', 'Pagos::tabla');
});

// HU-18: Dashboard de estado de cuenta (SDGODA-41)
$routes->group('dashboard', ['filter' => ['auth', 'role:Administrador,Secretaria']], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('tabla', 'Dashboard::tabla');
});