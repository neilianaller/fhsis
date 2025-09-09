<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->resource('dashboard', ['controller' => 'DashboardController']);


service('auth')->routes($routes);
