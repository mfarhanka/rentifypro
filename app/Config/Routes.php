<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('', ['filter' => 'session'], static function ($routes) {
	$routes->get('dashboard', 'Dashboard::index');

	$routes->get('gadgets', 'GadgetController::index');
	$routes->get('gadgets/create', 'GadgetController::create');
	$routes->post('gadgets', 'GadgetController::store');
	$routes->get('gadgets/(:num)/edit', 'GadgetController::edit/$1');
	$routes->post('gadgets/(:num)', 'GadgetController::update/$1');

	$routes->get('rentals', 'RentalController::index');
	$routes->get('rentals/create', 'RentalController::create');
	$routes->post('rentals', 'RentalController::store');
	$routes->post('rentals/(:num)/status', 'RentalController::updateStatus/$1');
	$routes->get('rentals/(:num)/invoice', 'RentalController::invoice/$1');
});
