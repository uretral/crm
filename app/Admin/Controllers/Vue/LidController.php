<?php

namespace App\Admin\Controllers\Vue;


use App\Admin\Extensions\Form\NestedFormFlat;
use App\Models\Admin\AdminUser;
use App\Models\Crm\Act;
use App\Models\Crm\Customer;
use App\Models\Crm\Implement;
use App\Models\Crm\Lid;
use App\Http\Controllers\Controller;
use App\Models\Crm\LidAction;
use App\Models\Crm\LidStatus;
use App\Models\Crm\Volume;
use App\Models\Helper\Action;
use App\Models\Helper\Company;
use App\Models\Helper\Drug;
use App\Models\Helper\Equipment;
use App\Models\Helper\Method;
use App\Models\Helper\PaymentRule;
use App\Models\Helper\Pest;
use App\Models\Helper\Phone;
use App\Models\Helper\Region;
use App\Models\Helper\Servicing;
use App\Models\Helper\Square;
use App\Models\Helper\Status;
use App\Models\Helper\Tools;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/*
use App\Models\Crm\Volume;
use App\Models\My\Test;
use Encore\Admin\Form\NestedForm;
use App\Models\Ajax\Modal;
use App\Models\Crm\MasterVolume;
use App\User;
use function Composer\Autoload\includeFile;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Layout\Row;
use Encore\Admin\Facades\Admin as nb;
use Encore\Admin\Widgets\Table;
*/

class LidController extends Controller
{
    use HasResourceActions;

    public $modelData;

    private $edit = false;

    private $actHolder = [];

    public $current;
    public $customer;

    /*
     *   +"year": 2019
  +"month": 5
  +"day": 30
  +"dayOfWeek": 4
  +"dayOfYear": 150
  +"hour": 0
  +"minute": 0
  +"second": 0
  +"micro": 0
  +"timestamp": 1559174400
  +"formatted": "2019-05-30 00:00:00"
     * */

    public function contract($type = false)
    {
        $st = Carbon::today()->toObject();
        $oLid = Lid::whereBetween('date_start', [Carbon::make($st->formatted), Carbon::make($st->formatted)->add(1, 'day')])
                ->get()->count() + 1;

        return str_replace('20', '', $st->year) . '-' . $st->month . '-' . $oLid . '-' . $st->day;


        /*
        if ($type)
            return str_replace('20', '', $st->year) . '-' . $st->month . '-' . $oLid . '-' . $st->day . '-U';
        else
            return str_replace('20', '', $st->year) . '-' . $st->month . '-' . $oLid . '-' . $st->day;*/
    }

    public function modelData(){
        return $this->modelData = Lid::where('id',request()->segment(3))->first();
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
        $helpers = [];

        $helpers['user'] = $this->usersList();
        $helpers['masters'] = Administrator::whereHas('roles', function ($q) {
            $q->where('role_id', 3); // ->where('active', 1)
        })->get()->keyBy('id');
        $helpers['actions'] = Action::all()->keyBy('id')->toArray();
        $helpers['companies'] =  Company::all()->keyBy('id')->toArray();
        $helpers['drugs'] = Drug::all()->keyBy('id')->toArray();
        $helpers['equipments'] = Equipment::all()->keyBy('id')->toArray();
        $helpers['methods'] = Method::all()->keyBy('id')->toArray();
        $helpers['pests'] = Pest::all()->keyBy('id')->toArray();
        $helpers['phones'] = Phone::all()->keyBy('id')->toArray();
        $helpers['regions'] = Region::all()->keyBy('id')->toArray();
        $helpers['servicing'] = Servicing::all()->keyBy('id')->toArray();
        $helpers['square'] = Square::all()->keyBy('id')->toArray();
        $helpers['statuses'] = Status::all()->keyBy('id')->toArray();
        $helpers['tools'] = Tools::all()->keyBy('id')->toArray();
        $helpers['payment_rules'] = PaymentRule::all()->keyBy('id')->toArray();
        return $helpers;
    }




    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid())
            ;
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $lid =  json_encode(Lid::with(['statuses','customer','actions','acts'])->where('id',$id)->first()->toArray());
        $acts =  json_encode(Act::with(['volume','implement'])->where('parent',$id)->get()->keyBy('id')->toArray());
        $user = json_encode(config('const.admin')->user()->toArray());
        $helpers = json_encode($this->helpers(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $statuses = json_encode(LidStatus::where('lid_id',$id)->orderBy('id')->get()->keyBy('id')->toArray());
        $regions = json_encode(Region::all()->keyBy('region')->toArray());

//        dump($acts);
        return $content
            ->header('Edit')
            ->description('description')
            ->body("<div id='lid'><lid  
                                        v-bind:lid='" .$lid. "' 
                                        v-bind:acts='" .$acts. "' 
                                        v-bind:user='" .$user. "'                              
                                        v-bind:helpers='" .$helpers. "'
                                        v-bind:statuses='" .$statuses. "'
                                        v-bind:regions='" .$regions. "'
                                    ></lid></div>");
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Новый заказ')
//            ->description('description')
            ->body($this->formCreate());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Lid);
//        $grid->disableCreateButton();
//        $grid->expandFilter();
        $grid->filter(function ($filter){
            $filter->expand();
            $filter->equal('contract', '№ Договора');
            $filter->where(function ($query){
                $query->whereHas('customer', function ($query) {
                    $query->where('phone', 'like', "%{$this->input}%");
                });
            },'Телефон')->mobile(['mask' => '+7 (999) 999 99 99']);

            $filter->where(function ($query){
                $query->whereHas('customer', function ($query) {
                    $query->where('organization', 'like', "%{$this->input}%");
                });
            },'Организация');

            $filter->where(function ($query){
                $query->whereHas('status', function ($query) {
                    $query->where('status', $this->input);
                });
            },'Статус')->select(Status::all()->pluck('name','id'));

            $filter->equal('action','Действие')->select(Action::all()->pluck('name','id'));


        });


        $grid->id('ID')->sortable();
        $grid->date_start('Дата обращения')->sortable();
        $grid->contract('Номер договора')->sortable();
//        $grid->servicing('Тип обслуживания')->display(function ($servicing){
//            return Servicing::find($servicing)->name;
//        })->sortable();
        $grid->status('Статус')->display(function ($status){
            if($status['status']){
                return $status['date'].' (' .Status::find($status['status'])->name. ')';
            } else {
                return '';
            }

        });
        $grid->action('Действие')->using(Action::all()->pluck('name','id')->toArray())->sortable();
        $grid->action_date('Дата и время действия')->sortable();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Lid::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Lid);

        $this->modelData();

        if(request()->segment(4)){
            $this->edit = true;

            $this->customer = Customer::where('lid_id',request()->segment(3))->first();

            $this->current = Lid::find(request()->segment(3))->first();

        }
        // presets
//        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
        });


// Основные данные:

        $form->tab('Основные данные',function (Form $form){



            if($this->modelData->manager_starter){
                $form->htmlField('manager_starter','Мэнеджер начавший лид')
                    ->display(AdminUser::where('id',$this->modelData->manager_starter)->first()->name);
            }

    // Дата обращения:
            $form->datetime('date_start', 'Дата обращения:');

    // Статус лида:
    // с (дата):

            $form->select('site','Сайт')->options(Company::all()->pluck('name','id'));

            $form->select('status.status', ' Статус лида:')->options(Status::all()->pluck('name', 'id'))->default(4);
            $form->datetime('status.date', 'с (дата):')->default(Carbon::now()->toDateTimeString());

    //Менеджер:

            $form->htmlField('manager','Мэнеджер')
                ->display(AdminUser::where('id',$this->modelData->manager)->first()->name);
            $form->hidden('manager')->value(config('const.admin')->user()->id);


    //Тип обслуживания: Действие:


            $form->select('servicing', 'Тип обслуживания:')->options(Servicing::all()->pluck('name', 'id'))->default(1);
            $form->select('action', 'Действие:')->options(Action::all()->pluck('name', 'id'))->required();
            $form->datetime('action_date', 'Дата и время действия:');
            $form->text('action_note', 'Примечание:');            //Комментарий:
            $form->textarea('comment','Комментарий:');
        });

        $form->tab('Данные клиента',function (Form $form){
            $states = [
                'on' => ['value' => 1, 'text' => 'Юридический', 'color' => 'danger'],
                'off' => ['value' => 0, 'text' => 'Физизический', 'color' => 'success'],
            ];
            $form->switch('customer.status', 'Статус клиента:')->states($states);

            $form->text('customer.name', 'Имя');
            $form->text('customer.organization', 'Организация');
            $form->mobile('customer.phone', 'Телефон')->options(['mask' => '+7 (999) 999 99 99']);
            $form->mobile('customer.phone_ext', 'Телефон добавочный')->options(['mask' => '+7 (999) 999 99 99']);
            $form->email('customer.email', 'email');
            $form->dadataAddress('customer.address', 'Адрес');
            $form->text('customer.destination', 'Расстояние');
            $form->hidden('customer.city');
            $form->hidden('customer.region');
            $form->text('customer.geo_lat','Широта');
            $form->text('customer.geo_lon','Долгота');
//            $form->hidden('center_lat');
//            $form->hidden('center_lon');
        });


        // Обьем работ:
        $form->tab('Обьем работ',function (Form $form){

            $form->html();

            $form->hasManyFlat('act', 'Акт', function (Form\NestedForm $form) {

                $finished = [
                    'on' => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                    'off' => ['value' => 0, 'text' => 'Нет', 'color' => 'danger'],
                ];

                $form->text('act_nr','Номер акта');
                $form->switch('floating','Плавающий?');
                $form->date('floating_date_from','Срок с...');
                $form->date('floating_date_to','Срок до...');
                $form->dadataLatLon('address', 'Адрес');
                $form->text('lat', 'Широта');
                $form->text('lon', 'Долгота');
                $form->select('region', 'Регион')->options(Region::all()->pluck('region','region'));
                $form->switch('finished','Работы завершены?')->states($finished);
                $form->switch('booking_act_transferred','Бухгалтерский акт передан?')->states($finished);
                $form->switch('booking_act_signed','Бухгалтерский акт подписан?')->states($finished);
                $form->file('booking_act_file','Бухгалтерский акт скан');
                $form->switch('implement_act_signed','Исполнительский акт подписан?')->states($finished);
                $form->file('implement_act_file','Исполнительский акт скан');

                $form->tableFlat('volume','Обьем(ы)', function ($table) {
                    $table->select('pest','Предмет работ:')->options(Pest::all()->pluck('name','id'))->load('method', '/api/getter/methods');
                    $table->select('method','Метод:')->options(Method::all()->pluck('name','id'));
                    $table->text('square','Площадь:')->pattern('^[0-9]*[.]?[0-9]+$');
                    $table->select('entity','Единица площади:')->options(Square::all()->pluck('name','id'));
                    $table->text('price_standard','Цена гост');
                    $table->text('price_fact','Цена факт.');
                })->parent('act');

                $form->tableFlat('implement','Исполнители', function ($table) {
                    $table->dateButton('choose_master','Дата');
                    $table->select('master','Мастер')->options(Implement::masters());
                    $table->datetime('start_date','Начало работ');
                    $table->datetime('end_date','Окончание Работ');
                })->parent('act');

            });
        });

        $form->tab('Документы',function (Form $form){
            $form->text('contract', 'Номер договора:');
            $form->switch('contract_transferred', 'Договор передан:');
            $form->switch('contract_signed', 'Договор подписан:');
            $form->file('contract_file', 'Файл договора:');
            $form->switch('customer_payment','Безнал?');
            $form->select('payment_rule','Порядок рачсетов?')->options(PaymentRule::all()->pluck('name','id'));
            $form->text('payment_condition','Условия постоплаты (в днях)');
        });

        $form->hasMany('volumes','Обьем(ы)', function (Form\NestedForm $form) {
            $form->select('pest','Предмет работ:')->options(Pest::all()->pluck('name','id'))->load('method', '/api/getter/methods');
            $form->select('method','Метод:')->options(Method::all()->pluck('name','id'));
            $form->number('square','Площадь:')->default(0);
            $form->select('entity','Единица площади:')->options(Square::all()->pluck('name','id'));
            $form->text('price_standard','Цена гост');
            $form->text('price_fact','Цена факт.');
            $form->text('lid_id');
            $form->text('parent');
            $form->text('id');
        });
        $form->hasMany('implements','Исполнители', function (Form\NestedForm $form) {
            $form->text('master','Мастер');
            $form->datetime('start_date','Начало работ');
            $form->datetime('end_date','Окончание Работ');
            $form->text('lid_id');
            $form->text('parent');
            $form->text('id');
        });


        $form->submitted(function (Form $form){
            $form->ignore('center_lat');
            $form->ignore('center_lon');
        });
        $form->saving(function (Form $form) {

            if($form->act){





            $form->volumes = [];
            $form->implements = [];
                $newAct = [];
                $relations = [];
                /* acts array*/
                foreach ($form->act as $actKey => $actArr) if(($actArr['act_nr'])) { // 1

                    /*act with relations*/

                    $currentActKey = is_numeric($actKey) ? $actKey : Act::max('id') + 1;

                    foreach ($actArr as $actSubKeys => $actVal) { // 2

                        /*relations*/
                        if (is_array($actVal)) {
                            $relationName = $actSubKeys.'s';
                            foreach ($actVal as $relKey => $relVal) { // 3
                                if(is_numeric($relKey)) {
                                    $relVal['id'] = $relKey;
                                    $relationID = $relKey;

                                } else {
                                    $relVal['id'] = false;
                                    $relationID = 'new_'.rand(1,1000);
                                }
                                $relVal['parent'] = $currentActKey;
                                $relVal['lid_id'] = $form->model()->id;
                                $relations[$relationName][$relationID] = $relVal;
                            }

                        } else {
                            /*act*/
                                $newAct[$actKey][$actSubKeys] = $actVal;
                        }
                    }
                }

                /*adding to form data*/
                $form->act = $newAct;
                foreach ($relations as $relationName =>$relationValue) {
                    $form->$relationName = $relationValue;
                }
            }
        });








        return $form;
    }

    public function formCreate(){
        $form = new Lid;
        $form->contract = $this->contract();
        $form->date_start = Carbon::now()->toDateTimeString();
        $form->manager = config('const.admin')->user()->id;
        $form->manager_starter = config('const.admin')->user()->id;

        $form->save();
        $lastID = DB::getPdo()->lastInsertId();
        return redirect('/crm/lids/'.$lastID.'/edit');

/*

        $form->text('contract', 'Номер договора:')->value($this->contract());
        $form->html("
        <script>
//        $(document).ready(function(){
//            $('.btn-primary').click();
//        })

</script>
        ");
        $form->saved(function (Form $form){

//            dump($form->model());
//            exit();
//            return redirect('/crm/lids/'.$form->model()->id.'/edit');
        });
        return $form;*/

//                    ($form->customer_status == 'on') ? $contract = $this->contract(true) : $contract = $this->contract();
//            $form->contract = value($contract);

    }



    public function updateField(){
        $request = request()->all();
        switch ($request['model']):
            case 'lid':
                return  Lid::where('id',(int)$request['id'])->update([$request['field'] => $request['value']]);
                break;
            case 'action':
                $update = Lid::where('id',(int)$request['id'])->update([$request['field'] => $request['value']]);
                $find = LidAction::where('salt',$request['salt'])->first();
                if(is_null($find)) {
                    $arr = [
                        'lid_id' => $request['id'],
                        'manager' => $request['manager'],
                        'salt' => $request['salt'],
                        $request['field'] => $request['value'],
                    ];
                    $res = LidAction::create($arr)->id;
                } else {
                    $res = LidAction::where('id',$find->id)->update([$request['field'] => $request['value']]);
                }
                return $res;
                break;
            case 'customer':
                $find = Customer::where('salt',$request['salt'])->first();
                if(is_null($find)) {
                    $arr = [
                        'lid_id' => $request['id'],
                        'manager' => $request['manager'],
                        'salt' => $request['salt'],
                        $request['field'] => $request['value'],
                    ];
                    $res = LidAction::create($arr)->id;
                } else {
                    $res = LidAction::where('id',$find->id)->update([$request['field'] => $request['value']]);
                }
                return 'act';
                break;
            case 'act':
                return Act::where('id',(int)$request['child_id'])->update([$request['field'] => $request['value']]);
                break;
            case 'imp':
                return 'implement';
                break;
        endswitch;
    }

    public function log(){
        $request = request()->all();
        $arr = [
            'lid_id' => $request['id'],
            'manager' => $request['manager'],
            'salt' => $request['salt'],
        ];
        switch ($request['model']):
            case 'status':

                $find = LidStatus::where('salt',$request['salt'])->first();
                $arr['status'] = $request['value'];
                is_null($find) ? $res =  LidStatus::create($arr)->id : $res = LidStatus::where('id',$find->id)->update($arr);
                return $res;

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

    public function getActRelations(){
        $volumes = Volume::where('parent',request()->get('act'))->get()->toArray();
        $implements = Implement::where('parent',request()->get('act'))->get()->toArray();
        $ArrImplements = [];
        foreach ($implements as $k => $implement){
            $ArrImplements[$k] = $implement;
            $start_date=date_create($implement['start_date']);
            $end_date=date_create($implement['end_date']);
            $ArrImplements[$k]['start_date'] = date_format($start_date,'Y-m-d\TH:i');
            $ArrImplements[$k]['end_date'] = date_format($end_date,'Y-m-d\TH:i');
        }
        return json_encode(array('volumes' => $volumes,'implements' => $ArrImplements));

    }
    public function addVolume(){
        $volume = new Volume;
        $volume->lid_id = request()->get('lid');
        $volume->parent = request()->get('act');
        $volume->save();
        return json_encode(Volume::find($volume->id)->toArray());
    }
    public function addImplement(){
        $implement = new Implement;
        $implement->lid_id = request()->get('lid');
        $implement->parent = request()->get('act');
        $implement->save();
        return json_encode(Volume::find($implement->id)->toArray());
    }

    public function deleteVolume(){
        $volume = Volume::where('id',request()->get('id'))->delete();
        return $volume;

    }
    public function deleteImplement(){
        $implement = Implement::where('id',request()->get('id'))->delete();
        return $implement;
    }

    public function addActRelation(){
        $model = request()->get('model');
        switch ($model){
            case 'volume':
                return Volume::where('id',request()->get('id'))->update([request()->get('field') => request()->get('value')]);
                break;
            case 'implement':
                return Implement::where('id',request()->get('id'))->update([request()->get('field') => request()->get('value')]);
                break;
        }

    }


}
