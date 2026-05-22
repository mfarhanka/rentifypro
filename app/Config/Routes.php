<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'AuthController::loginView', ['as' => 'login']);
$routes->post('login', 'AuthController::loginAction');
$routes->get('customers/setup/(:segment)', 'CustomerController::setup/$1');
$routes->post('customers/setup/(:segment)', 'CustomerController::completeSetup/$1');

service('auth')->routes($routes);

$routes->group('', ['filter' => 'session'], static function ($routes) {
	$routes->get('dashboard', 'Dashboard::index');
	$routes->get('customers', 'CustomerController::index');
	$routes->get('customers/create', 'CustomerController::create');
	$routes->post('customers', 'CustomerController::store');
	$routes->get('staff', 'StaffController::index');
	$routes->get('staff/create', 'StaffController::create');
	$routes->post('staff', 'StaffController::store');
	$routes->get('staff/(:num)/edit', 'StaffController::edit/$1');
	$routes->post('staff/(:num)', 'StaffController::update/$1');
	$routes->post('staff/(:num)/suspend', 'StaffController::toggleSuspend/$1');
	$routes->post('staff/(:num)/delete', 'StaffController::delete/$1');

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
