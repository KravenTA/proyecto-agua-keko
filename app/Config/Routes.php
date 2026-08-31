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
$routes->group('contadores', static function ($routes) {
    $routes->get('/', 'Contadores::index');
    $routes->get('nuevo', 'Contadores::nuevo');
    $routes->post('/', 'Contadores::crear');

    // HU-11: Editar, activar y desactivar un contador (SDGODA-26)
    $routes->get('editar/(:num)', 'Contadores::editar/$1');
    $routes->post('actualizar/(:num)', 'Contadores::actualizar/$1');
    $routes->post('desactivar/(:num)', 'Contadores::desactivar/$1');
    $routes->post('activar/(:num)', 'Contadores::activar/$1');


});