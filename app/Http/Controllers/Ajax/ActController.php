<?php


namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;
use App\Models\Crm\Act;
use App\Models\Crm\Volume;

class ActController extends Controller
{
    public function copyAct(){
        $act = request()->get('act');
        $from = request()->get('from');
        $to = request()->get('to');

        $originalAct = Act::find($act);
        $originalVolumes = Volume::where('parent',$originalAct->id)->get();
        $ActCount = Act::where('parent',$originalAct->parent)->get()->count();

        $newAct = new Act;
        $newAct->act_nr = $ActCount + 1;
        $newAct->parent = $originalAct->parent;
        $newAct->floating = 1;
        $newAct->floating_date_from = $from;
        $newAct->floating_date_to = $to;
        $newAct->address = $originalAct->address;
        $newAct->lat = $originalAct->lat;
        $newAct->lon = $originalAct->lon;
        $newAct->region = $originalAct->region;
        $newAct->save();

        $newActID = $newAct->id;

        foreach ($originalVolumes as $vol){
            $newVol = new Volume;

            $newVol->parent = $newActID;
            $newVol->lid_id = $vol->lid_id;
            $newVol->pest = $vol->pest;
            $newVol->square = $vol->square;
            $newVol->entity = $vol->entity;
            $newVol->method = $vol->method;
            $newVol->price_standard = $vol->price_standard;
            $newVol->price_fact = $vol->price_fact;
            $newVol->kpi = $vol->kpi;
            $newVol->note = $vol->note;
            $newVol->save();
        }
    }

}
