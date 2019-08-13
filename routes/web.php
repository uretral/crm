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

//Artisan::call('view:clear');


use Carbon\Carbon;

//dump(Carbon::make('2019-08-31')->add(1, 'day')->toDateString());

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
Route::get('/logistic/map/many','\App\Http\Controllers\Ajax\LogisticController@getMany');
Route::get('/logistic/map/get_equipment','\App\Http\Controllers\Ajax\LogisticController@getEquipment');
Route::post('/logistic/map/update_equipment','\App\Http\Controllers\Ajax\LogisticController@updateEquipment');


//Route::get('/test', '\App\Models\My\Test@tableTest');
Route::get('/test', '\App\Models\My\Test@vue');

