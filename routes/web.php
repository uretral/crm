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


use App\Models\Crm\Customer;
use App\Models\Crm\Lid;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;



//dump(Carbon::make('2019-08-31')->add(1, 'day')->toDateString());

Route::any('/attach', 'ImageController@attach');

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
Route::get('/ajax/lid/act_per_date','\App\Admin\Controllers\Vue\LidController@actPerDate');
Route::get('/ajax/lid/save_float_acts','\App\Admin\Controllers\Vue\LidController@saveFloatActs');
Route::get('/ajax/lid/get_acts','\App\Admin\Controllers\Vue\LidController@getActs');
Route::get('/ajax/lid/delete_act','\App\Admin\Controllers\Vue\LidController@deleteAct');
Route::get('/ajax/lid/customer_search','\App\Admin\Controllers\Vue\LidController@customerSearch');
Route::get('/ajax/lid/add_empty_act','\App\Admin\Controllers\Vue\LidController@addEmptyAct');

// Methods prices
Route::get('/ajax/price/init_table','\App\Admin\Controllers\Helper\PestController@initTable');
Route::get('/ajax/price/save_field','\App\Admin\Controllers\Helper\PestController@saveField');
Route::get('/ajax/price/add_remedy','\App\Admin\Controllers\Helper\PestController@addRemedy');
Route::get('/ajax/price/remove_remedy','\App\Admin\Controllers\Helper\PestController@removeRemedy');


//Route::get('/test', '\App\Models\My\Test@tableTest');
//Route::get('/test', '\App\Models\My\Test@vue');
Route::get('/test', function () {

    $pest = \App\Models\Helper\Pest::all();
    $delArr = [4,6,14,15,16];
/*    foreach ($pest as $p){
        $dif = array_diff($p->methods,$delArr);
//        dump($p->methods);

//        dump(array_diff($p->methods,$delArr),'-----');

        $f = \App\Models\Helper\Pest::where('id',$p->id)->update(['methods'=>implode(',',$dif)]);
        dump($f);





    }*/



/*
    $vols = \App\Models\Crm\Volume::all();
//    dump($acts);

    $cnt = $vols->count();
    foreach ($vols as $k => $vol) if($vol->id === 1070){

        $items = [];

        $id = $vol->method;
        switch ($id){
            case 4:
                // Комплекс  = 1,3
                $items = [1,3];
                break;
            case 6:
                // Обработка от плесени 9,10
                $items = [9,10];
                break;
            case 14:
                // Горячий + барьер  3,27
                $items = [3,27];
                break;
            case 15:
                // 	Комплекс + барьер 1,3,27
                $items = [1,3,27];
                break;
            case 16:
                // Дезинсекция, дератизация  18,19
                $items = [18,19];
                break;
            case 28:
                //покос триммером+уборка  28,36
                $items = [28,36];
                break;
            case 29:
                // покос трактором +уборка 29,36
                $items = [29,36];
                break;
            case null:
                continue;
                break;
            default :
                $items = [$id];
//                if($id == 11){
//                    dump($act);
//                }
                break;

        }

        // parent -> act_id
        // lid_id -> lid_id


/*        foreach ($items as $item){
            $method = new \App\Models\Crm\ActMethod;
            $method->act_id = $vol->parent;
            $method->lid_id = $vol->lid_id;
            $method->vol_id = $vol->id;
            $method->method = $item;
            $method->save();



        }*/



//



//    }



//    dump($cnt);*/

}

);

