<?php


namespace App\Http\Controllers\Ajax;

use App\Admin\Controllers\Crm\SanitaryVolumeController;
use App\Http\Controllers\Controller;
use App\Models\Crm\Act;
use App\Models\Crm\Customer;
use App\Models\Crm\Implement;
use App\Models\Crm\Lid;
use App\Models\Crm\SanitaryVolume;
use App\Models\Crm\Volume;
use App\Models\Helper\Action;
use App\Models\Helper\Company;
use App\Models\Helper\Drug;
use App\Models\Helper\Equipment;
use App\Models\Helper\Method;
use App\Models\Helper\Pest;
use App\Models\Helper\Phone;
use App\Models\Helper\Region;
use App\Models\Helper\Servicing;
use App\Models\Helper\Square;
use App\Models\Helper\Status;
use App\Models\Helper\Tools;
use App\Models\Store\ImplementEquipment;
use Carbon\CarbonTimeZone;
use Encore\Admin\Auth\Database\Administrator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;


class LogisticController extends Controller
{
    public $masters;
    public $equipment;
    public $equipmentList;
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
    public $floating = [];
    public $helpers = [];


    public function helpers(){

        $this->helpers['actions'] = Action::all()->keyBy('id')->toArray();
        $this->helpers['companies'] = Company::all()->keyBy('id')->toArray();
        $this->helpers['drugs'] = Drug::all()->keyBy('id')->toArray();
        $this->helpers['equipments'] = Equipment::all()->keyBy('id')->toArray();
        $this->helpers['methods'] = Method::all()->keyBy('id')->toArray();
        $this->helpers['pests'] = Pest::all()->keyBy('id')->toArray();
        $this->helpers['phones'] = Phone::all()->keyBy('id')->toArray();
        $this->helpers['regions'] = Region::all()->keyBy('id')->toArray();
        $this->helpers['servicing'] = Servicing::all()->keyBy('id')->toArray();
        $this->helpers['square'] = Square::all()->keyBy('id')->toArray();
        $this->helpers['statuses'] = Status::all()->keyBy('id')->toArray();
        $this->helpers['tools'] = Tools::all()->keyBy('id')->toArray();
    }


    public function equipmentList(){
        return  Equipment::all()->pluck('code','id');

    }

    public function masters()
    {
        $m = Administrator::whereHas('roles', function ($q) {
            $q->where('role_id', 3)->where('active',1); // role_start_dateid
        })->get();//->pluck('name', 'id');

        $arr = [];
        foreach ($m as $master) if ($master['city'] == $this->getCityCode()) {
            $arr[$master['id']] = array(
                'name' => $master['name'],
                'color' => $master['color'],
            );
//            $arr[$master['id']]['name'] = $master['name'];
//            $arr[$master['id']]['color'] = $master['color'];
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



    public function busyHoursByGeo($master)
    {
        $volume = Implement::whereBetween('start_date', [Carbon::make($this->date), Carbon::make($this->date)->add(1, 'day')])
            ->where('master', $master)
            ->orderBy('start_date')
            ->get()->toArray();
        $vol = [];
        $geo = [];
        foreach ($volume as $k => $v) {
            $lid = Lid::find($v['lid_id'])->toArray();
            $act = Act::find($v['parent'])->toArray();
            $vols = Volume::where('parent',$v['parent'])->get()->keyBy('id')->toArray();

            $start = (int)Carbon::createFromFormat('Y-m-d H:i:s', $v['start_date'])->format('H');
            $end = (int)Carbon::createFromFormat('Y-m-d H:i:s', $v['end_date'])->format('H');
            if(!$act['lat'] || !$act['lon'] ) {
                $customer = Customer::where('lid_id',$act['parent'])->first()->toArray();
                $key = $customer['geo_lat'] . '-' . $customer['geo_lon'] . '-' .$start;
            } else {
                $key = $act['lat'] . '-' . $act['lon'] . '-' .$start;
            }
            $customer = Customer::where('lid_id',$act['parent'])->first()->toArray();
            if($act['address']){
                $address = $act['address'];
            } else {
                $address = $customer['address'];
            }
            $nowDateTime = Carbon::now()->format('Y-m-d H:i:s');
            $now = strtotime($nowDateTime);
            if($act['finished']){
                $vol[$key]['state'] = 'done';
            } elseif(!$act['finished'] && strtotime($v['start_date']) < $now && strtotime($v['end_date']) > $now) {
                $vol[$key]['state'] = 'doing';
            } elseif (!$act['finished'] &&  strtotime($v['start_date']) > $now) {
                $vol[$key]['state'] = 'todo';
            } elseif (!$act['finished'] &&  strtotime($v['end_date']) < $now) {
                $vol[$key]['state'] = 'late';
            } else {
                $vol[$key]['state'] = 'error';
            }

            $vol[$key]['id'] = $key . '-' .$start;
            $vol[$key]['lid'][$lid['id']] = $lid;
            $vol[$key]['start'] = $start;
            $vol[$key]['end'] = $end;
            $vol[$key]['start_date'] = strtotime($v['start_date']);
            $vol[$key]['datestr'] = $now;
            $vol[$key]['end_date'] = strtotime($v['end_date']);
            $vol[$key]['length'] = $end - $start;
            $vol[$key]['address'] = $address;
            $vol[$key]['master'] = $v['master'];
            $vol[$key]['marker'] = $key . '-' .$start . '-' .$v['master'];
            $vol[$key]['ids'][] = $v['id'];
            $vol[$key]['actIDs'][] = $v['parent'];
            $vol[$key]['customer'] = $customer;

            $vol[$key]['actsArray'][$act['id']] = $act;

            $key2 = $act['id'];
            $vol[$key]['acts'][$key2] = $v;
//            $vol[$key][$key2]['rrrrrrrrr'] = $act;

            $vol[$key]['acts'][$key2]['start'] = $start;
            $vol[$key]['acts'][$key2]['end'] = $end;
            $vol[$key]['acts'][$key2]['length'] = $end - $start;

            foreach ($vols as $volKey => $volVal){
                $vol[$key]['volume'][$volKey] = $volVal;
            }


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
    public function scheduleByGeo()
    {
        foreach ($this->masters as $k => $master) {
            $this->schedule[$k]['name'] = $master;
            $this->schedule[$k]['volumes'] = $this->busyHoursByGeo($k);
            $this->schedule[$k]['equipment'] = $this->equipmentByImplement($this->date,$k);
        }
        return $this->schedule;
    }
    public function region(){

        if (!is_null(\request()->get('lat'))){
            $region = Region::where('center_lat', \request()->get('lat'))->first();
        }  else if(!is_null(\request()->get('region'))) {
            $region = Region::where('region', \request()->get('region'))->first();
        } else {
            $region = Region::where('id', 1)->first();
        }
        return $this->region =  $region;
    }

    public function getCityCode()
    {
        $this->region();
//        if (!is_null(\request()->get('lat')))
//            $region = Region::where('center_lat', \request()->get('lat'))->first();
//        else if(!is_null(\request()->get('region')))
//            $region = Region::where('region', \request()->get('region'))->first();
//        else
//            $region = Region::where('id', 1)->first();
        return $this->region->code;
    }


    public function new()
    {
        $this->region();
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->schedule();
        $this->equipmentList = $this->equipmentList();
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

    public function getByGeo()
    {
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->scheduleByGeo();
        $this->equipmentList = $this->equipmentList();
        $this->helpers();
        if (Request::get('view') == 1) {
            dump($this);
        }
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

    public function updateImplement()
    {
        $request = Request::all();
        $startDate = Carbon::make($request['date'] . ' ' . $request['start'] . '.00.00');
        $endDate = Carbon::make($request['date'] . ' ' . ($request['start'] + $request['duration']) . '.00.00');
        $arOrders = explode(':',$request['order']);
        foreach ($arOrders as $implementID){
            $update = [
                'master' => (int)$request['master'],
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
            Implement::find((int)$implementID)->update($update);

        }

    }

    public function getActs(){
        $acts = Act::with('lid')->whereIn('id',explode(',',\request()->get('acts')))->get();
        return json_encode($acts, JSON_UNESCAPED_UNICODE);
    }

    public function setEquipment(){

    }

    public function forgotten(){
        /*
         *  акты у которых
         * date_to больше чем сейчас
         * Мастера не назначены
         * floating = 1
         * finished != 1
         * */


    }

    public function floating(){
        $this->region();
        $floating = Act::whereDate('floating_date_from','<=',\request()->get('date'))
            ->whereDate('floating_date_to','>=',\request()->get('date'))
            ->where('floating',1)
            ->where('finished','!=', 1)
            ->whereIn('region',[$this->region->region,$this->region->area])
            ->get()->toArray();

        $arr = [];
        foreach ($floating as $f)
            if( $f['id'] && !Implement::where('parent',$f['id'])->first())
            {
            $arr[$f['lat'].'-'.$f['lon']][] = $f;
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    public function updateImplements(){
        $masters = json_decode(\request()->get('masters'));
        $acts = json_decode(\request()->get('acts'));
        foreach ($acts as $act){
            $lidID = Act::where('id',$act)->first()->parent;

            foreach ($masters as $master){
                $implement = new Implement;
                $implement->parent = $act;
                $implement->lid_id = $lidID;
                $implement->master = (int)$master->master;
                $implement->start_date = $master->startTime;
                $implement->end_date = $master->endTime;
                $implement->save();
            }
        }


        return 'saved';

    }

    public static function actsFulfilled(){
        $data = json_decode(\request()->get('data'));
        $response = [];
        foreach ($data as $act){
            Act::find($act->parent)->update(['finished' => 1]);
            $response[] =  Act::where('id',$act->parent)->first()->finished;
        }
        if(count($response) == array_sum($response)) {
            return 1;
        } else {
            return 0;
        }

    }

}

// {"action":"master","date":"08.08.2018","order":"4","start":"11","duration":"4","master":"9"}
