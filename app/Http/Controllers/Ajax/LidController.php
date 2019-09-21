<?php


namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;
use App\Models\Admin\AdminUser;
use App\Models\Crm\Lid;
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

class LidController extends Controller
{
    public $helpers = [];
    public $lid;
    public $acts;
    public $manager;
    public $user;

    public function manager(){
        return config('const.admin')->user();
    }

    public function usersList(){
        $arrUsers = AdminUser::all()->keyBy('id')->toArray();
        $arrUsers[0] = [
            'active' => 1,
            'avatar' => null,
            'city' => 495,
            'color' => '#ff0000',
            'name' => 'Пустой'
        ];
        return $arrUsers;
    }


    public function helpers(){



        $this->helpers['user'] = $this->usersList();
        $this->helpers['actions'] = Action::all()->keyBy('id')->toArray();
/*        $companies =  Company::all()->keyBy('id')->toArray();
        foreach ($companies as $companyKey => $company) {
            $this->helpers['companies'][$companyKey]['value'] = $company['id'];
            $this->helpers['companies'][$companyKey]['label'] = $company['name'];
        }*/
        $this->helpers['companies'] =  Company::all()->keyBy('id')->toArray();
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
    public function lid(){
        return $this->lid = Lid::find(request()->get('id'))->toArray();
    }

    public function updateLid($id,$field,$value){
        return Lid::find($id)->update([$field => $value]);
    }






    public function updateField(){
        $id = request()->get('id');
        $model = request()->get('model');
        $field = request()->get('field');
        $value = request()->get('value');
        switch ($model):
            case 'lid':
                $lid = Lid::where('id',(int)$id)->update([$field => $value]);
                return  $lid;
                break;
            case 'act':
                return 'act';
                break;
            case 'vol':
                return 'volume';
                break;
            case 'imp':
                return 'implement';
                break;
        endswitch;
    }

    public function getLid(){
        $this->helpers();
        $this->lid();
        $this->manager();


//        dump('pp',$this->user);
//

        return json_encode($this,JSON_UNESCAPED_UNICODE);

    }
}
