<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// === RUTAS PÚBLICAS ===
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index'); 
$routes->get('auth', 'Auth::index');
$routes->get('auth/login', 'Auth::login'); 
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
    $routes->get('edit/(:num)', 'Campanias::edit/$1'); // <-- esta es la ruta para editar
    $routes->post('update/(:num)', 'Campanias::update/$1');
    $routes->get('delete/(:num)', 'Campanias::delete/$1');
    $routes->get('view/(:num)', 'Campanias::view/$1');
    $routes->get('toggleEstado/(:num)', 'Campanias::toggleEstado/$1');
    $routes->get('show/(:num)', 'Campanias::show/$1');
});

// === PERSONAS/CONTACTOS ===
$routes->get('api/personas/buscar', 'PersonaController::buscarAjax');
$routes->get('personas/buscardni', 'PersonaController::buscardni');

// === PERSONAS/CONTACTOS (CON filtro de autenticación) ===
$routes->group('personas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'PersonaController::index');
    $routes->get('crear', 'PersonaController::create');
    $routes->get('editar/(:num)', 'PersonaController::create/$1');
    $routes->post('guardar', 'PersonaController::guardar');
    $routes->get('eliminar/(:num)', 'PersonaController::delete/$1');
});

// API PÚBLICA (fuera del grupo con filtro)
$routes->get('api/personas/buscar', 'PersonaController::buscarAjax');

// === TAREAS (MEJORADAS) ===
$routes->group('tareas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Tareas::index');
    $routes->get('calendario', 'Tareas::calendario');
    $routes->get('getTareasCalendario', 'Tareas::getTareasCalendario');
    $routes->post('crear', 'Tareas::crear');
    $routes->post('crearTareaCalendario', 'Tareas::crearTareaCalendario');
    $routes->post('actualizarFechaTarea', 'Tareas::actualizarFechaTarea');
    $routes->post('actualizarTarea', 'Tareas::actualizarTarea');
    $routes->delete('eliminarTarea/(:num)', 'Tareas::eliminarTarea/$1');
    $routes->get('editar/(:num)', 'Tareas::editar/$1');
    $routes->post('editar/(:num)', 'Tareas::actualizar/$1');
    $routes->post('completar/(:num)', 'Tareas::completar/$1');
    $routes->post('reprogramar', 'Tareas::reprogramar');
    $routes->post('completarMultiples', 'Tareas::completarMultiples');
    $routes->post('eliminarMultiples', 'Tareas::eliminarMultiples');
    $routes->get('detalle/(:num)', 'Tareas::detalle/$1');
    $routes->get('verificarProximasVencer', 'Tareas::verificarProximasVencer');
    $routes->get('pendientes', 'Tareas::pendientes');
    $routes->get('vencidas', 'Tareas::vencidas');
});

// === COTIZACIONES ===
$routes->group('cotizaciones', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Cotizaciones::index');
    $routes->get('create', 'Cotizaciones::create');
    $routes->post('store', 'Cotizaciones::store');
    $routes->get('show/(:num)', 'Cotizaciones::show/$1');
    $routes->get('edit/(:num)', 'Cotizaciones::edit/$1');
    $routes->post('update/(:num)', 'Cotizaciones::update/$1');
    $routes->post('cambiarEstado/(:num)', 'Cotizaciones::cambiarEstado/$1');
    $routes->get('pdf/(:num)', 'Cotizaciones::generarPDF/$1');
    $routes->get('porLead/(:num)', 'Cotizaciones::porLead/$1');
});

// === SERVICIOS ===
$routes->group('servicios', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Servicios::index');
    $routes->get('create', 'Servicios::create');
    $routes->post('store', 'Servicios::store');
    $routes->get('edit/(:num)', 'Servicios::edit/$1');
    $routes->post('update/(:num)', 'Servicios::update/$1');
    $routes->post('toggleEstado/(:num)', 'Servicios::toggleEstado/$1');
    $routes->get('estadisticas', 'Servicios::estadisticas');
});

// === REPORTES ===
$routes->group('reportes', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Reportes::index');
    $routes->get('exportar-excel', 'Reportes::exportarExcel');
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
// -------------------- USUARIOS --------------------
$routes->group('usuarios', function($routes) {
    $routes->get('/', 'UsuarioController::index');
    $routes->get('crear', 'UsuarioController::crear');
    $routes->post('crear', 'UsuarioController::crear');
    $routes->post('editar/(:num)', 'UsuarioController::editar/$1');
    $routes->delete('eliminar/(:num)', 'UsuarioController::eliminar/$1');
    $routes->post('cambiarEstado/(:num)', 'UsuarioController::cambiarEstado/$1');
});

// === NOTIFICACIONES ===
$routes->get('notificaciones', 'Dashboard::notificaciones', ['filter' => 'auth']);

// Rutas principales
$routes->get('dashboard', 'Dashboard::index');
$routes->get('campanias', 'Campanias::index');
$routes->get('tareas', 'Tareas::index');
// Agrega las rutas que falten...