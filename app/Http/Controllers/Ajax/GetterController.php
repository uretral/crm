<?php


namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;
use App\Models\Helper\Method;
use App\Models\Helper\Pest;
use DB;
use Illuminate\Http\Request;

class GetterController extends Controller
{

    public function methods(Request $request)
    {
        $meth = Pest::where('id',$request->get('q'))->first();
        return Method::whereIn('id', $meth->methods)->get(['id', DB::raw('name as text')]);
    }


}
