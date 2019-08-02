<?php


namespace App\Http\Controllers\Ajax;

use App\Admin\Controllers\Crm\SanitaryVolumeController;
use App\Http\Controllers\Controller;
use App\Models\Crm\Customer;
use App\Models\Crm\Implement;
use App\Models\Crm\SanitaryVolume;
use App\Models\Helper\Region;
use Encore\Admin\Auth\Database\Administrator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;



class LogisticController extends Controller
{
    public $masters;
    public $date;
    public $hours;
    public $test;
    public $schedule;
    public $cityCode;
    public $lid = [];

    public function masters()
    {
       $m  = Administrator::whereHas('roles', function ($q) {
            $q->where('role_id', 3); // role_start_dateid
        })->get();//->pluck('name', 'id');

        $arr = [];
        foreach ($m as $master)if($master['city'] == $this->getCityCode()){
            $arr[$master['id']] = $master['name'];
        }
        return $arr;

    }

    public function busyHours($master)
    {
        $volume = Implement::whereBetween('start_date',[Carbon::make($this->date),Carbon::make($this->date)->add(1,'day')])
            ->where('master',$master)
            ->get()->toArray();
        $vol = [];
        $geo = [];
        foreach ($volume as $k => $v){
            $vol[$k] = $v;
            $vol[$k]['start'] = Carbon::createFromFormat('Y-m-d H:i:s',$v['start_date'])->format('H');
            $vol[$k]['end'] = Carbon::createFromFormat('Y-m-d H:i:s',$v['end_date'])->format('H');
            $vol[$k]['length'] = (int)$vol[$k]['end'] - (int)$vol[$k]['start'];
            if($v['lid_id']){
                $this->lid[$v['lid_id']]['master'][] = $v['master'];
                $this->lid[$v['lid_id']]['customer'] = $this->customer($v['lid_id']);
            }
        }

        return $vol;
    }
    public function customer($lid)
    {
        return Customer::where('lid_id',$lid)->first();

    }

    public function schedule()
    {
        foreach ($this->masters as $k => $master){
            $this->schedule[$k]['name'] = $master;
            $this->schedule[$k]['volumes'] = $this->busyHours($k);
        }
        return $this->schedule;
    }

    public function getCityCode()
    {
        $region = Region::where('center_lat',\request()->get('lat'))->first();
        return $region->code;
    }



    public function new()
    {
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date  = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->schedule();


        return view('map.logistic_multi_day',[
            'data' => $this
        ]);
    }

    public function get()
    {
        $this->masters = $this->masters();
        $this->hours = range(0, 23);
        $this->date  = Request::get('date');
        $this->test = Carbon::createFromFormat('Y-m-d H:i:s.u', '2019-02-01 03:45:27.612584')->format('H');
        $this->schedule = $this->schedule();

        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    public function update()
    {
//        $request = Request::get('data');
        $request = Request::all();
        $startDate = Carbon::make($request['date'].' '.$request['start'].'.00.00');
        $endDate = Carbon::make($request['date'].' '.($request['start'] + $request['duration']).'.00.00');
        $update = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'master' => (int)$request['master']
        ];
        return Implement::where('id',(int)$request['order'])->update($update);
    }

}

// {"action":"master","date":"08.08.2018","order":"4","start":"11","duration":"4","master":"9"}
