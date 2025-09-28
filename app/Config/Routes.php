<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('dashboard', 'Dashboard\Index::index', ['filter' => 'auth']);

// Rutas de autenticación (solo estas, fuera del grupo)
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/check', 'Auth::checkAuth');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('auth', function() {
    return redirect()->to('auth/login');
});

// Dashboard
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard\Index::index');
    $routes->get('getLeadQuickInfo/(:num)', 'Dashboard\Index::getLeadQuickInfo/$1');
    $routes->post('quickAction', 'Dashboard\Index::quickAction');
    $routes->post('completarTarea', 'Dashboard\Index::completarTarea');
});

$routes->get('personas', 'PersonaController::index');
$routes->get('personas/create', 'PersonaController::create');
$routes->post('personas/create', 'PersonaController::create');
$routes->get('personas/edit/(:num)', 'PersonaController::edit/$1');
$routes->post('personas/edit/(:num)', 'PersonaController::edit/$1');
$routes->get('personas/delete/(:num)', 'PersonaController::delete/$1');
$routes->get('configuracion/obtener-preferencias', 'Configuracion::obtenerPreferencias');
$routes->get('dashboard/perfil', 'Dashboard::perfil');
$routes->get('dashboard/notificaciones', 'Dashboard::notificaciones');

// Rutas legacy
$routes->get('personas', 'Persons::index', ['filter' => 'auth']);
$routes->get('campanias', 'Campaigns::index', ['filter' => 'auth']);

$routes->group('leads', function($routes) {
    $routes->get('/', 'Leads::index');
    $routes->get('create', 'Leads::create');
    $routes->post('store', 'Leads::store');
    $routes->get('edit/(:num)', 'Leads::edit/$1');
    $routes->post('update/(:num)', 'Leads::update/$1');
    $routes->get('view/(:num)', 'Leads::view/$1');
    $routes->post('buscarPorTelefono', 'Leads::buscarPorTelefono');
    $routes->get('pipeline', 'Leads::pipeline');
    $routes->post('updateEtapa', 'Leads::updateEtapa');
});
$routes->get('personas/buscardni', 'PersonaController::buscardni');