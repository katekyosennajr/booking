<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->group('auth', function($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->post('logout', 'AuthController::logout');
});

// Protected routes
$routes->group('api', ['filter' => 'auth'], function($routes) {
    // Booking routes
    $routes->group('bookings', function($routes) {
        $routes->get('/', 'BookingController::index');
        $routes->get('list', 'BookingController::getBookings');
        $routes->get('user', 'BookingController::getUserBookings');
        $routes->post('check-availability', 'BookingController::checkAvailability');
        $routes->post('create', 'BookingController::create');
        $routes->put('update/(:num)', 'BookingController::update/$1');
        $routes->delete('delete/(:num)', 'BookingController::delete/$1');
    });

    // Service routes
    $routes->group('services', function($routes) {
        $routes->get('/', 'ServiceController::index');
        $routes->get('(:num)', 'ServiceController::show/$1');
        $routes->get('active', 'ServiceController::active');
        $routes->post('create', 'ServiceController::create');
        $routes->put('update/(:num)', 'ServiceController::update/$1');
        $routes->delete('delete/(:num)', 'ServiceController::delete/$1');
    });

    // Time slot routes
    $routes->group('time-slots', function($routes) {
        $routes->get('/', 'TimeSlotController::index');
        $routes->get('(:num)', 'TimeSlotController::show/$1');
        $routes->get('available/(:num)', 'TimeSlotController::getAvailable/$1');
        $routes->post('check-availability', 'TimeSlotController::checkAvailability');
        $routes->post('create', 'TimeSlotController::create');
        $routes->put('update/(:num)', 'TimeSlotController::update/$1');
        $routes->delete('delete/(:num)', 'TimeSlotController::delete/$1');
    });
});
