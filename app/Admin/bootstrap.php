<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Column;
use App\Admin\Extensions\Nav\Links;
use App\Admin\Extensions\Nav\Heading;
use App\Admin\Extensions\Form\LightBox;
use App\Admin\Extensions\Form\Field\DateButton;
use App\Admin\Extensions\Form\History;
use \App\Admin\Extensions\Form\Field\HasManyFlat;
use App\Admin\Extensions\Form\Field\TableFlat;
use App\Admin\Extensions\Form\Field\HiddenFlat;
use App\Admin\Extensions\Form\Field\DadataAddress;
use App\Admin\Extensions\Form\Field\DadataLatLon;
use App\Admin\Extensions\Form\Field\SelectFlat;
use App\Admin\Extensions\Form\Field\HtmlField;

Encore\Admin\Form::forget(['map', 'editor']);

Form::extend('dateButton',DateButton::class);
Form::extend('lightBox', LightBox::class);
Form::extend('history', History::class);
Form::extend('hasManyFlat', HasManyFlat::class);
Form::extend('tableFlat', TableFlat::class);
Form::extend('hiddenFlat', HiddenFlat::class);
Form::extend('dadataAddress', DadataAddress::class);
Form::extend('dadataLatLon', DadataLatLon::class);
Form::extend('selectFlat',SelectFlat::class);
Form::extend('htmlField',HtmlField::class);


$vueRoute = [];
$route = request()->getPathInfo();
switch (true):
    case  stristr($route,"/crm/lids/"):
        $vueRoute[] = '/js/implement.map.js';
//        $vueRoute[] = '/js/get.address.data.js';
        break;
    case $route == "/logistic/routes":
        $vueRoute[] = '/js/logistic.routes.js';
        break;
    case stristr($route,"/logistic/routes/"):
        $vueRoute[] = '/js/logistic.routes.map.js';
        break;
endswitch;

Admin::css([
    '/less/helper.css',

]);



Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(new Links());
    $navbar->left(new Heading());
});

Admin::js($vueRoute);

