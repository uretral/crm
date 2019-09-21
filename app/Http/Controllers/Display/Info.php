<?php


namespace App\Http\Controllers\Display;


use App\Http\Controllers\Controller;
use Carbon\Carbon;

class Info extends Controller
{
    public function logisticMap(){
        $nowDate = Carbon::now()->format('Y-m-d');

        $html = <<<HTML
<link rel="stylesheet" href="/less/helper.css">

        <div id="mainLogisticMap">
        <main-logistic-map date="{$nowDate}" region="Москва"></main-logistic-map>
        </div>
        <script src="/js/leafLet.js"></script>
        <script src="/js/main.logistic.map.js"></script>

HTML;
        return $html;


    }

}
