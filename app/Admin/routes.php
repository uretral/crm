<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();



Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('crm/lids', \Crm\LidController::class);
    $router->resource('crm_entity/acts', \Crm\ActController::class);
    $router->resource('helper/actions', \Helper\ActionController::class); // Действия
    $router->resource('helper/methods', \Helper\MethodController::class); // Методы
    $router->resource('helper/pests', \Helper\PestController::class); // Вредители
    $router->resource('helper/servicing', \Helper\ServicingController::class); // Обслуживание
    $router->resource('helper/statuses', \Helper\StatusController::class); // Статусы
    $router->resource('helper/squares', \Helper\SquareController::class); // Единицы площади
    $router->resource('helper/regions', \Helper\RegionController::class); // Регионы
    $router->resource('helper/phones', \Helper\PhoneController::class); // Телефоны
    $router->resource('helper/companies', \Helper\CompanyController::class); // Компании

});
