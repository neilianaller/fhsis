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

$routes->post('saveFP', 'FamPlanningController::save');
$routes->get('getFP', 'FamPlanningController::get');

$routes->post('saveMaternal', 'MaternalController::save');
$routes->get('getMaternal', 'MaternalController::get');

$routes->post('saveChild', 'ChildController::save');
$routes->get('getChild', 'ChildController::get');

$routes->post('saveOral', 'OralController::save');
$routes->get('getOral', 'OralController::get');

$routes->post('saveNCDisease', 'NCDiseaseController::save');
$routes->get('getNCDisease', 'NCDiseaseController::get');

$routes->post('saveEnvi', 'EnviController::save');
$routes->get('getEnvi', 'EnviController::get');

$routes->post('saveIDisease', 'IDiseaseController::save');
$routes->get('getIDisease', 'IDiseaseController::get');

$routes->get('subsection/(:segment)', 'SubSectionsController::subsection/$1');

$routes->post('reportslist', 'ReportsController::list');
$routes->get('download/(:num)', 'ReportsController::download/$1');

$routes->post('generateFPReport', 'ReportsController::generateFPReport');
$routes->post('generateMaternalReport', 'ReportsController::generateMaternalReport');
$routes->post('generateChildReport', 'ReportsController::generateChildReport');
$routes->post('generateOralReport', 'ReportsController::generateOralReport');

$routes->resource('famplanning', ['controller' => 'FamPlanningController']);
$routes->resource('maternal', ['controller' => 'MaternalController']);
$routes->resource('child', ['controller' => 'ChildController']);
$routes->resource('oral', ['controller' => 'OralController']);
$routes->resource('ncdisease', ['controller' => 'NCDiseaseController']);
$routes->resource('envi', ['controller' => 'EnviController']);
$routes->resource('idisease', ['controller' => 'IDiseaseController']);
$routes->resource('reports', ['controller' => 'ReportsController']);

service('auth')->routes($routes);
