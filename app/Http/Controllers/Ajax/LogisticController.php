<?php


namespace App\Http\Controllers\Ajax;

use App\Admin\Controllers\Crm\SanitaryVolumeController;
use App\Http\Controllers\Controller;
use App\Models\Crm\Customer;
use App\Models\Crm\Implement;
use App\Models\Crm\SanitaryVolume;
use App\Models\Helper\Equipment;
use App\Models\Helper\Region;
use App\Models\Store\ImplementEquipment;
use Encore\Admin\Auth\Database\Administrator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;


class LogisticController extends Controller
{
    public $masters;
    public $equipment;
    public $equipmentByImplement;
    public $date;
    public $hours;
    public $test;
    public $schedule;
    public $multiSchedule = [];
    public $cityCode;
    public $regions;
    public $region;
    public $lid = [];
    public $request;

    public function masters()
    {
        $m = Administrator::whereHas('roles', function ($q) {
            $q->where('role_id', 3); // role_start_dateid
        })->get();//->pluck('name', 'id');

        $arr = [];
        foreach ($m as $master) if ($master['city'] == $this->getCityCode()) {
            $arr[$master['id']] = $master['name'];
        }
        return $arr;

    }
    public function equipment(){
        return $this->equipment = Equipment::all()->keyBy('id');
    }

    public function equipmentByImplement($date,$master){
        $res = [];
        $implementArr =  ImplementEquipment::where('date',$date)->where('master',$master)->get()->toArray();//->pluck('equipment','master');
        foreach ($implementArr as $implementVal){
            $res[$implementVal['equipment_name_id']][$implementVal['equipment']] = $implementVal; // equipment
        }
        return $res;
    }

    public function getEquipment(){
        $date = \request('date_active');
        $master = \request('master');
        $equipmentNameID = \request('equipment_id');
        $activeRecords = ImplementEquipment::where('date',\request('date_active'))->where('equipment_name_id', \request('equipment_id'))->pluck('master','equipment')->toArray();
        $passiveRecord = \App\Models\Store\Equipment::where('name',$equipmentNameID)->get()->toArray();

        foreach ($passiveRecord as $k => $record){
            if(array_key_exists($record['id'],$activeRecords) ){
                $passiveRecord[$k]['master'] = $activeRecords[$record['id']];
            } else {
                $passiveRecord[$k]['master'] = false;
            }
        }
        return json_encode($passiveRecord,JSON_UNESCAPED_UNICODE);
    }

    public function updateEquipment(){
        $req = \request()->all();//;
        ImplementEquipment::where('date',$req['date'])->where('equipment_name_id',$req['equipment'])->where('master',$req['master'])->delete();
        foreach ($req['equipmentItem'] as $item){
            $eq = new ImplementEquipment;
            $eq->master = $req['master'];
            $eq->equipment = $item;
            $eq->equipment_name_id = $req['equipment'];
            $eq->date = $req['date'];
            $eq->save();
        }

//        return json_encode($req['equipmentItem']);

    }


    public function busyHours($master)
    {
        $volume = Implement::whereBetween('start_date', [Carbon::make($this->date), Carbon::make($this->date)->add(1, 'day')])
            ->where('master', $master)
            ->orderBy('start_date')
            ->get()->toArray();
        $vol = [];
        $geo = [];
        foreach ($volume as $k => $v) {
            $vol[$k] = $v;
            $vol[$k]['start'] = Carbon::createFromFormat('Y-m-d H:i:s', $v['start_date'])->format('H');
            $vol[$k]['end'] = Carbon::createFromFormat('Y-m-d H:i:s', $v['end_date'])->format('H');
            $vol[$k]['length'] = (int)$vol[$k]['end'] - (int)$vol[$k]['start'];
            if ($v['lid_id']) {
                $this->lid[$v['lid_id']]['master'][] = $v['master'];
                $this->lid[$v['lid_id']]['customer'] = $this->customer($v['lid_id']);
            }
        }
        return $vol;
    }

    public function customer($lid)
    {
        return Customer::where('lid_id', $lid)->first();

    }


    public function schedule()
    {
        foreach ($this->masters as $k => $master) {
            $this->schedule[$k]['name'] = $master;
            $this->schedule[$k]['volumes'] = $this->busyHours($k);
            $this->schedule[$k]['equipment'] = $this->equipmentByImplement($this->date,$k);
        }
        return $this->schedule;
    }
    public function region(){
        if (!is_null(\request()->get('lat')))
            $region = Region::where('center_lat', \request()->get('lat'))->first();
        else
            $region = Region::where('id', 1)->first();
        return $this->region =  $region;
    }

    public function getCityCode()
    {
        if (!is_null(\request()->get('lat')))
            $region = Region::where('center_lat', \request()->get('lat'))->first();
        else
            $region = Region::where('id', 1)->first();
        return $region->code;
    }


    public function new()
    {
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->schedule();


        return view('map.logistic_multi_day', [
            'data' => $this
        ]);
    }

    public function get()
    {
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->schedule();

        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    public function getMany()
    {
        $this->masters = $this->masters();
        $this->equipment = $this->equipment();
        $this->regions = Region::all();
        $this->region = $this->region();
//        $this->equipmentByImplement = $this->equipmentByImplement(\request()->get('date'));
//        $this->ee = ImplementEquipment::where('date','2019-05-08')->where('master',4)->get();

        for ($i = 0; $i < \request()->get('days'); $i++){
            $this->date = Carbon::make(\request()->get('date'))->add($i, 'day');
            $this->multiSchedule[$this->date->toDateString()] =  $this->schedule();
        }

        return json_encode($this, JSON_UNESCAPED_UNICODE);

    }

    public function update()
    {
        $request = Request::all();
        $startDate = Carbon::make($request['date'] . ' ' . $request['start'] . '.00.00');
        $endDate = Carbon::make($request['date'] . ' ' . ($request['start'] + $request['duration']) . '.00.00');
        $update = [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'master'     => (int)$request['master']
        ];
        return Implement::where('id', (int)$request['order'])->update($update);
    }

    public function setEquipment(){

    }

}

// {"action":"master","date":"08.08.2018","order":"4","start":"11","duration":"4","master":"9"}
