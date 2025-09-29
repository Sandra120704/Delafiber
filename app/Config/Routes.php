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
// $routes->get('personas', 'Persons::index', ['filter' => 'auth']); // Comentada para evitar conflicto
$routes->get('campanias', 'Campaigns::index', ['filter' => 'auth']);
// CRUD para campañas
$routes->get('campanias/create', 'Campaigns::create', ['filter' => 'auth']);
$routes->post('campanias/store', 'Campaigns::store', ['filter' => 'auth']);
$routes->get('campanias/edit/(:num)', 'Campaigns::edit/$1', ['filter' => 'auth']);
$routes->post('campanias/update/(:num)', 'Campaigns::update/$1', ['filter' => 'auth']);
$routes->get('campanias/delete/(:num)', 'Campaigns::delete/$1', ['filter' => 'auth']);

// Campañas: ver campaña específica
$routes->get('campanias/view/(:num)', 'Campaigns::view/$1', ['filter' => 'auth']);

// Configuración: guardar/actualizar preferencias
$routes->post('configuracion/guardar-preferencias', 'Configuracion::guardarPreferencias');
$routes->post('configuracion/actualizar-preferencias', 'Configuracion::actualizarPreferencias');

// === LEADS ===
$routes->group('leads', ['filter' => 'auth'], function($routes) {
    // Listar y crear
    $routes->get('/', 'Leads::index');
    $routes->get('create', 'Leads::create');
    $routes->post('store', 'Leads::store');
    // Ver, editar, eliminar
    $routes->get('view/(:num)', 'Leads::view/$1');
    $routes->get('edit/(:num)', 'Leads::edit/$1');
    $routes->post('update/(:num)', 'Leads::update/$1'); // corregido typo
    // Pipeline (vista kanban)
    $routes->get('pipeline', 'Leads::pipeline');
    // Acciones sobre leads
    $routes->post('moverEtapa', 'Leads::moverEtapa');
    $routes->post('convertir/(:num)', 'Leads::convertir/$1');
    $routes->post('descartar/(:num)', 'Leads::descartar/$1');
    // Búsqueda y AJAX
    $routes->get('buscarPorDni', 'Leads::buscarPorDni');
    $routes->post('agregarSeguimiento', 'Leads::agregarSeguimiento');
    $routes->post('crearTarea', 'Leads::crearTarea');
    $routes->post('completarTarea', 'Leads::completarTarea');
    // Exportar
    $routes->get('exportar', 'Leads::exportar');
});

// Perfil: editar y actualizar perfil
$routes->get('dashboard/perfil/edit', 'Dashboard::editPerfil', ['filter' => 'auth']);
$routes->post('dashboard/perfil/update', 'Dashboard::updatePerfil', ['filter' => 'auth']);

// Reportes: ver y exportar
$routes->get('reportes', 'Reportes::index', ['filter' => 'auth']);
$routes->get('reportes/exportar', 'Reportes::exportar', ['filter' => 'auth']);

// Tareas: CRUD
$routes->get('tareas', 'Tareas::index', ['filter' => 'auth']);
$routes->get('tareas/create', 'Tareas::create', ['filter' => 'auth']);
$routes->post('tareas/store', 'Tareas::store', ['filter' => 'auth']);
$routes->get('tareas/edit/(:num)', 'Tareas::edit/$1', ['filter' => 'auth']);
$routes->post('tareas/update/(:num)', 'Tareas::update/$1', ['filter' => 'auth']);
$routes->get('tareas/delete/(:num)', 'Tareas::delete/$1', ['filter' => 'auth']);

$routes->get('personas/buscardni', 'PersonaController::buscardni');

// === PERFIL Y CONFIGURACIÓN ===
$routes->group('perfil', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Perfil::index');
    $routes->get('edit', 'Perfil::edit');
    $routes->post('update', 'Perfil::update');
});
$routes->group('configuracion', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Configuracion::index');
    $routes->post('guardar-preferencias', 'Configuracion::guardarPreferencias');
    $routes->post('actualizar-preferencias', 'Configuracion::actualizarPreferencias');
});

// === NOTIFICACIONES ===
$routes->get('notificaciones', 'Dashboard::notificaciones', ['filter' => 'auth']);

// === EXPORTACIONES GENERALES ===
$routes->get('leads/exportar', 'Leads::exportar', ['filter' => 'auth']);
$routes->get('reportes/exportar', 'Reports::exportar', ['filter' => 'auth']);
