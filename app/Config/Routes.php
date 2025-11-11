<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ReportsController::index');
$routes->resource('dashboard', ['controller' => 'DashboardController']);
$routes->resource('sections', ['controller' => 'SectionsController']);
$routes->resource('subsections', ['controller' => 'SubSectionsController']);
$routes->resource('categories', ['controller' => 'CategoriesController']);
$routes->resource('indicators', ['controller' => 'IndicatorsController']);


$routes->post('sectionsList', 'SectionsController::sectionsList');

$routes->post('save/(:any)', to: 'EntriesController::save/$1');
$routes->get('get/(:any)', 'EntriesController::get/$1');

$routes->get('subsection/(:segment)', 'SubSectionsController::subsection/$1');

$routes->post('reportslist', 'ReportsController::list');
$routes->get('download/(:num)', 'ReportsController::download/$1');

$routes->post('generateFPReport', 'ReportsController::generateFPReport');
$routes->post('generateMaternalReport', 'ReportsController::generateMaternalReport');
$routes->post('generateChildReport', 'ReportsController::generateChildReport');
$routes->post('generateOralReport', 'ReportsController::generateOralReport');
$routes->post('generateNCDiseaseReport', 'ReportsController::generateNCDiseaseReport');
$routes->post('generateEnviReport', 'ReportsController::generateEnviReport');
$routes->post('generateIDiseaseReport', 'ReportsController::generateIDiseaseReport');
$routes->post('generateAllReport', 'ReportsController::generateAllReport');

$routes->resource('famplanning', ['controller' => 'FamPlanningController']);
$routes->resource('maternal', ['controller' => 'MaternalController']);
$routes->resource('child', ['controller' => 'ChildController']);
$routes->resource('oral', ['controller' => 'OralController']);
$routes->resource('ncdisease', ['controller' => 'NCDiseaseController']);
$routes->resource('envi', ['controller' => 'EnviController']);
$routes->resource('idisease', ['controller' => 'IDiseaseController']);
$routes->resource('reports', ['controller' => 'ReportsController']);

service('auth')->routes($routes);
