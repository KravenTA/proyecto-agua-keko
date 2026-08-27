<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');

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