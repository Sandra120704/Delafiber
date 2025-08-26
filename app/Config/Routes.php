<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Leads
$routes->get('/leads', 'LeadController::index');
$routes->get('/leads/create', 'LeadController::create');
$routes->get('/leads/create/(:num)', 'LeadController::create/$1');
$routes->post('/leads/store', 'LeadController::store');
$routes->get('/leads/edit/(:num)', 'LeadController::edit/$1');
$routes->post('/leads/update/(:num)', 'LeadController::update/$1');

// Personas
$routes->get('/personas', 'PersonaController::index');
$routes->get('/personas/create', 'PersonaController::crear');
$routes->post('/personas/store', 'PersonaController::store');
$routes->post('/personas/update/(:num)', 'PersonaController::update/$1');
$routes->get('/personas/delete/(:num)', 'PersonaController::delete/$1');
$routes->get('/personas/edit/(:num)', 'PersonaController::edit/$1');


// Seguimientos
$routes->get('/seguimientos', 'SeguimientoController::index');

// Autenticación
$routes->get('/login', 'UsuarioController::login'); // vista de login
$routes->post('/login', 'UsuarioController::login_action'); // acción de login
$routes->get('/logout', 'UsuarioController::logout'); // logout
$routes->get('/crear-admin', 'UsuarioController::crearAdmin'); // crear admin

// Dashboard
$routes->get('/', 'Dashboard::index'); // Página principal
$routes->get('dashboard', 'Dashboard::index'); // Dashboard opcional



