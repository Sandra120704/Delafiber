<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// === RUTAS PÚBLICAS ===
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// === DASHBOARD ===
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard\Index::index');
    $routes->get('getLeadQuickInfo/(:num)', 'Dashboard\Index::getLeadQuickInfo/$1');
    $routes->post('quickAction', 'Dashboard\Index::quickAction');
    $routes->post('completarTarea', 'Dashboard\Index::completarTarea');
});

// === LEADS ===
$routes->group('leads', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Leads::index');
    $routes->get('create', 'Leads::create');
    $routes->post('store', 'Leads::store');
    $routes->get('view/(:num)', 'Leads::view/$1');
    $routes->get('edit/(:num)', 'Leads::edit/$1');
    $routes->post('update/(:num)', 'Leads::update/$1');
    $routes->get('pipeline', 'Leads::pipeline');
    $routes->post('moverEtapa', 'Leads::moverEtapa');
    $routes->post('convertir/(:num)', 'Leads::convertir/$1');
    $routes->post('descartar/(:num)', 'Leads::descartar/$1');
    $routes->get('buscarPorDni', 'Leads::buscarPorDni');
    $routes->post('agregarSeguimiento', 'Leads::agregarSeguimiento');
    $routes->post('crearTarea', 'Leads::crearTarea');
    $routes->post('completarTarea', 'Leads::completarTarea');
    $routes->get('exportar', 'Leads::exportar');
});

// === CAMPAÑAS ===
$routes->group('campanias', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Campanias::index');
    $routes->get('create', 'Campanias::create');
    $routes->post('store', 'Campanias::store');
    $routes->get('edit/(:num)', 'Campanias::edit/$1');
    $routes->post('update/(:num)', 'Campanias::update/$1');
    $routes->get('delete/(:num)', 'Campanias::delete/$1');
    $routes->get('view/(:num)', 'Campanias::view/$1');
});

// === PERSONAS/CONTACTOS ===
$routes->group('personas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'PersonaController::index');
    $routes->get('create', 'PersonaController::create');
    $routes->post('create', 'PersonaController::create');
    $routes->get('edit/(:num)', 'PersonaController::edit/$1');
    $routes->post('edit/(:num)', 'PersonaController::edit/$1');
    $routes->get('delete/(:num)', 'PersonaController::delete/$1');
    $routes->get('buscardni', 'PersonaController::buscardni');
});

// === TAREAS ===
$routes->group('tareas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Tareas::index');
    $routes->get('pendientes', 'Tareas::pendientes');
    $routes->get('vencidas', 'Tareas::vencidas');
    $routes->post('completar/(:num)', 'Tareas::completar/$1');
    $routes->post('create', 'Tareas::create');
});

// === REPORTES ===
$routes->group('reportes', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Reportes::index');
    $routes->get('exportar', 'Reportes::exportar');
});

// === PERFIL ===
$routes->group('perfil', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Perfil::index');
    $routes->get('edit', 'Perfil::edit');
    $routes->post('update', 'Perfil::update');
});

// === CONFIGURACIÓN ===
$routes->group('configuracion', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Configuracion::index');
    $routes->get('obtener-preferencias', 'Configuracion::obtenerPreferencias');
    $routes->post('guardar-preferencias', 'Configuracion::guardarPreferencias');
    $routes->post('actualizar-preferencias', 'Configuracion::actualizarPreferencias');
});

// === NOTIFICACIONES ===
$routes->get('notificaciones', 'Dashboard::notificaciones', ['filter' => 'auth']);