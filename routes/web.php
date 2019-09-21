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


use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;

//dump(Carbon::make('2019-08-31')->add(1, 'day')->toDateString());
//MAIN LOGISTIC MAP
Route::get('/display/logistic_map','\App\Http\Controllers\Display\Info@logisticMap');


Route::post('/logistic/map/new','\App\Http\Controllers\Ajax\LogisticController@new');

Route::post('/logistic/map/update','\App\Http\Controllers\Ajax\LogisticController@update');
Route::post('/logistic/map/update_implement','\App\Http\Controllers\Ajax\LogisticController@updateImplement');
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
Route::get('/logistic/map/get_by_geo','\App\Http\Controllers\Ajax\LogisticController@getByGeo');
Route::post('/logistic/map/update','\App\Http\Controllers\Ajax\LogisticController@update');
Route::get('/logistic/map/many','\App\Http\Controllers\Ajax\LogisticController@getMany');
Route::get('/logistic/map/get_equipment','\App\Http\Controllers\Ajax\LogisticController@getEquipment');
Route::post('/logistic/map/update_equipment','\App\Http\Controllers\Ajax\LogisticController@updateEquipment');
Route::post('/logistic/map/acts_fulfilled','\App\Http\Controllers\Ajax\LogisticController@actsFulfilled');
Route::get('/logistic/map/floating','\App\Http\Controllers\Ajax\LogisticController@floating');
Route::get('/logistic/map/get_acts','\App\Http\Controllers\Ajax\LogisticController@getActs');
Route::get('/logistic/map/update_implements','\App\Http\Controllers\Ajax\LogisticController@updateImplements');

// copy act
Route::get('/copy_act','\App\Http\Controllers\Ajax\ActController@copyAct');

// vue lid
Route::get('/ajax/lid/update_field','\App\Admin\Controllers\Vue\LidController@updateField');
Route::get('/ajax/lid/log','\App\Admin\Controllers\Vue\LidController@log');
Route::get('/ajax/lid/get_act_relations','\App\Admin\Controllers\Vue\LidController@getActRelations');
Route::get('/ajax/lid/add_volume','\App\Admin\Controllers\Vue\LidController@addVolume');
Route::get('/ajax/lid/delete_volume','\App\Admin\Controllers\Vue\LidController@deleteVolume');
Route::get('/ajax/lid/add_implement','\App\Admin\Controllers\Vue\LidController@addImplement');
Route::get('/ajax/lid/delete_implement','\App\Admin\Controllers\Vue\LidController@deleteImplement');
Route::get('/ajax/lid/add_act_relation','\App\Admin\Controllers\Vue\LidController@addActRelation');

//Route::get('/test', '\App\Models\My\Test@tableTest');
//Route::get('/test', '\App\Models\My\Test@vue');
Route::get('/test', function (){
//    dump(Carbon::now()->toDateTimeString());
//    return view('test.yupnav');
    $m = Administrator::whereHas('roles', function ($q) {
        $q->where('role_id', 3)->where('active',1); // role_start_dateid
    })->get();//->pluck('name', 'id');
    dump($m);
});

