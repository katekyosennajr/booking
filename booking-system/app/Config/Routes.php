<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Booking routes
$routes->group('bookings', function($routes) {
    $routes->get('/', 'BookingController::index');
    $routes->get('list', 'BookingController::getBookings');
    $routes->post('create', 'BookingController::create');
    $routes->put('update/(:num)', 'BookingController::update/$1');
    $routes->delete('delete/(:num)', 'BookingController::delete/$1');
});
