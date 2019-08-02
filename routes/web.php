<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Artisan::call('view:clear');

use App\Models\Helper\Method;
use App\Models\Helper\Pest;

Route::post('/logistic/map/new','\App\Http\Controllers\Ajax\LogisticController@new');

Route::post('/logistic/map/update','\App\Http\Controllers\Ajax\LogisticController@update');
Route::get('/api/getter/methods','\App\Http\Controllers\Ajax\GetterController@methods');
/*Route::get('/api/getter',function (){

    $meth = Pest::where('id',request()->get('q'))->first();
    $return = Method::whereIn('id', $meth->methods)->get(['id', DB::raw('name as text')]);
//    return  $meth;
    dump($return);
});*/

// DADATA

Route::get('/dadata/curl','\App\Http\Controllers\Ajax\Dadata@address');
Route::get('/dadata/osm','\App\Http\Controllers\Ajax\Dadata@osm');


// test section

Route::get('/logistic/map/new','\App\Http\Controllers\Ajax\LogisticController@get');
Route::post('/logistic/map/update','\App\Http\Controllers\Ajax\LogisticController@update');

//Route::get('/test', '\App\Models\My\Test@tableTest');
Route::get('/test', '\App\Models\My\Test@vue');

