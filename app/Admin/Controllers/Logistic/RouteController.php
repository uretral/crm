<?php


namespace App\Admin\Controllers\Logistic;


use App\Http\Controllers\Controller;
use App\Models\Crm\Implement;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;

class RouteController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body('<div id="logisticRoutes"><logistic-routes></logistic-routes></div>');
    }

}
