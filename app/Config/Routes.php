<?php
use App\Controllers\PrefixeController;
use CodeIgniter\Router\RouteCollection;
use App\Controllers\BaremeController;
use App\Controllers\GainController;
use App\Controllers\SituationController;
use App\Controllers\CommissionController;
use App\Controllers\CompensationController;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/prefixe', [PrefixeController::class, 'index']);
$routes->get('/prefixe/create', [PrefixeController::class, 'create']);
$routes->post('/prefixe/store', [PrefixeController::class, 'store']);
$routes->get('/prefixe/show/(:num)', [PrefixeController::class, 'show']);
$routes->get('/prefixe/edit/(:num)', [PrefixeController::class, 'edit']);
$routes->post('/prefixe/update/(:num)', [PrefixeController::class, 'update']);
$routes->post('/prefixe/delete/(:num)', [PrefixeController::class, 'delete']);

$routes->get('/bareme', [BaremeController::class, 'index']);
$routes->get('/bareme/edit/(:num)', [BaremeController::class, 'edit']);
$routes->post('/bareme/update/(:num)', [BaremeController::class, 'update']);

$routes->get('/gain', [GainController::class, 'index']);


$routes->get('/situation', [SituationController::class, 'index']);
$routes->get('/situation/detail/(:num)', [SituationController::class, 'detail']);
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

// Authentification Opérateur
$routes->get('operateur/auth', 'OperateurAuthController::index');
$routes->post('operateur/auth', 'OperateurAuthController::login');
$routes->get('operateur/logout', 'OperateurAuthController::logout');

$routes->get('/commission', [CommissionController::class, 'index']);
$routes->post('/commission/update', [CommissionController::class, 'update']);

$routes->get('/compensation', [CompensationController::class, 'index']);
