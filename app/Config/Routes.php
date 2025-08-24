<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/leads', 'LeadController::index');
$routes->get('/leads/create', 'LeadController::create');
$routes->post('/leads/store', 'LeadController::store');

/* Rutas para la gestion de Personas */
$routes->get('/personas', 'PersonaController::index');
$routes->get('/personas/create', 'PersonaController::crear');
$routes->post('/personas/store', 'PersonaController::store');
$routes->get('/personas/edit/(:num)', 'PersonaController::edit/$1');
$routes->post('/personas/update/(:num)', 'PersonaController::update/$1');
$routes->get('/personas/delete/(:num)', 'PersonaController::delete/$1');
