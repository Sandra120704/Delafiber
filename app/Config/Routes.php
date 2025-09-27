<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('personas', 'PersonaController::index');
$routes->get('personas/create', 'PersonaController::create');
$routes->post('personas/create', 'PersonaController::create');
$routes->get('personas/edit/(:num)', 'PersonaController::edit/$1');
$routes->post('personas/edit/(:num)', 'PersonaController::edit/$1');
$routes->get('personas/delete/(:num)', 'PersonaController::delete/$1');
$routes->get('configuracion/obtener-preferencias', 'Configuracion::obtenerPreferencias');
$routes->get('dashboard/perfil', 'Dashboard::perfil');
$routes->get('dashboard/notificaciones', 'Dashboard::notificaciones');
