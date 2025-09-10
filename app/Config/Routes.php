<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->resource('dashboard', ['controller' => 'DashboardController']);
$routes->resource('sections', ['controller' => 'SectionsController']);
$routes->resource('indicator', ['controller' => 'SectionsController']);

$routes->post('sectionsList', 'SectionsController::sectionsList');

$routes->get('indicators/(:segment)', 'SectionsController::indicators/$1');

$routes->resource('famplanning', ['controller' => 'FamPlanningController']);


service('auth')->routes($routes);
