<?php
use App\Controllers\PrefixeController;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/prefixe', [PrefixeController::class, 'index']);
$routes->get('/prefixe/create', [PrefixeController::class, 'create']);
$routes->post('/prefixe/store', [PrefixeController::class, 'store']);
$routes->get('/prefixe/show/(:num)', [PrefixeController::class, 'show']);
$routes->get('/prefixe/edit/(:num)', [PrefixeController::class, 'edit']);
$routes->post('/prefixe/update/(:num)', [PrefixeController::class, 'update']);
$routes->post('/prefixe/delete/(:num)', [PrefixeController::class, 'delete']);