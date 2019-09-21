<?php

use App\Models\Helper\PaymentCondition;
use App\Models\Helper\PaymentRule;
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
    $router->resource('crm/lid_vue', \Crm\LidVueController::class);
    // helper
    $router->resource('helper/actions', \Helper\ActionController::class); // Действия
    $router->resource('helper/methods', \Helper\MethodController::class); // Методы
    $router->resource('helper/pests', \Helper\PestController::class); // Вредители
    $router->resource('helper/servicing', \Helper\ServicingController::class); // Обслуживание
    $router->resource('helper/statuses', \Helper\StatusController::class); // Статусы
    $router->resource('helper/squares', \Helper\SquareController::class); // Единицы площади
    $router->resource('helper/regions', \Helper\RegionController::class); // Регионы
    $router->resource('helper/phones', \Helper\PhoneController::class); // Телефоны
    $router->resource('helper/companies', \Helper\CompanyController::class); // Компании
    $router->resource('helper/equipment', \Helper\EquipmentController::class); // Оборудование
    $router->resource('helper/payment_rules', \Helper\PaymentRuleController::class); // Порядок расчетов
    $router->resource('helper/payment_conditions', \Helper\PaymentConditionController::class); // Условия постоплаты
    // Logistic
    $router->resource('logistic/routes', \Logistic\RouteController::class); // Логистика маршруты
//    $router->resource('logistic/routes/map', \Logistic\RouteControllerMap::class); // Логистика маршруты
    // Store
    $router->resource('store/equipment', \Store\EquipmentController::class); // Логистика маршруты
    // vue lid
    $router->resource('vue/lid', \Vue\LidController::class); // vue lid
});
