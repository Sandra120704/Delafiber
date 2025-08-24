<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/leads', 'LeadController::index');
$routes->get('/leads/create', 'LeadController::create');
$routes->post('/leads/store', 'LeadController::store');

