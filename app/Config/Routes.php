<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->resource('dashboard', ['controller' => 'DashboardController']);
$routes->resource('sections', ['controller' => 'SectionsController']);
$routes->resource('subsections', ['controller' => 'SubSectionsController']);
$routes->resource('categories', ['controller' => 'CategoriesController']);
$routes->resource('indicators', ['controller' => 'IndicatorsController']);

$routes->post('sectionsList', 'SectionsController::sectionsList');

$routes->post('addEntry', 'FamPlanningController::addEntry');

$routes->get('subsection/(:segment)', 'SubSectionsController::subsection/$1');

$routes->resource('famplanning', ['controller' => 'FamPlanningController']);


service('auth')->routes($routes);
