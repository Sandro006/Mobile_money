<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Auth par numéro
$routes->get('/login', 'LoginController::index');
$routes->post('/login', 'LoginController::submit');
$routes->get('/logout', 'LoginController::logout');

// Zone client (protégée)
$routes->get('/client', 'ClientController::index');

// Opérations

// Depot
$routes->get('operation/page-depot', 'OperationController::pageDepot');
$routes->post('operation/depot', 'OperationController::depot');

//  Retrait
$routes->get('operation/page-retrait', 'OperationController::pageRetrait');
$routes->post('operation/retrait', 'OperationController::retrait');

//Transfer
$routes->get('operation/page-transfert', 'OperationController::pageTransfert');
$routes->post('operation/transfert', 'OperationController::transfert');

// Historique
$routes->get('client/historique', 'ClientController::historique');
