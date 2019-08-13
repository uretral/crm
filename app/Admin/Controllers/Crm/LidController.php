<?php

namespace App\Admin\Controllers\Crm;


use App\Admin\Extensions\Form\NestedFormFlat;
use App\Models\Crm\Act;
use App\Models\Crm\Customer;
use App\Models\Crm\Implement;
use App\Models\Crm\Lid;
use App\Http\Controllers\Controller;
use App\Models\Crm\LidStatus;
use App\Models\Helper\Action;
use App\Models\Helper\Method;
use App\Models\Helper\Pest;
use App\Models\Helper\Region;
use App\Models\Helper\Servicing;
use App\Models\Helper\Square;
use App\Models\Helper\Status;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Carbon\Carbon;
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
        if ($type)
            return str_replace('20', '', $st->year) . '-' . $st->month . '-' . $oLid . '-' . $st->day . '-U';
        else
            return str_replace('20', '', $st->year) . '-' . $st->month . '-' . $oLid . '-' . $st->day;
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
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
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
            ->body($this->form());
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
        $grid->expandFilter();

        $grid->id('ID');
        $grid->date_start('Дата обращения')->sortable();
        $grid->contract('Номер договора')->sortable();
        $grid->servicing('Тип обслуживания')->display(function ($servicing){
            return Servicing::find($servicing)->name;
        })->sortable();
        $grid->status('Статус')->display(function ($status){
            if($status['status']){
                return $status['date'].' (' .Status::find($status['status'])->name. ')';
            } else {
                return '';
            }

        });
//        $grid->status('Дата статус')->display(function ($status){
//            return ;
//        });
        $grid->action('Действие')->display(function ($action){
            return Action::find($action)->name;
        })->sortable();
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
    // Дата обращения:
/*            if($this->edit){
                $form->datetime('date_start', 'Дата обращения:')->readOnly();
            } else {
                $form->datetime('date_start', 'Дата обращения:')->value(Carbon::now())->readOnly();
            }*/
            $form->datetime('date_start', 'Дата обращения:');

    // Статус лида:
    // с (дата):


/*            if($this->edit){
                echo LidStatus::statusShow(request()->segment(3));
                $form->select('status.status', ' Статус лида:')->options(Status::all()->pluck('name', 'id'));
                $form->datetime('status.date', 'с (дата):');
            } else {
                $form->select('status.status', ' Статус лида:')->options(Status::all()->pluck('name', 'id'))->default(4);
                $form->datetime('status.date', 'с (дата):')->value(Carbon::now())->readOnly();
            }*/

            $form->select('status.status', ' Статус лида:')->options(Status::all()->pluck('name', 'id'));
            $form->datetime('status.date', 'с (дата):');

    //Менеджер:
            if($this->edit) {
                $d = Lid::find(request()->segment(3))->first();

                $form->display('fake', 'Менеджер:')->value(config('const.admin')->user()->name)->readOnly();
                $form->hidden('manager')->value(config('const.admin')->user()->id);
            } else {
                $form->display('fake', 'Менеджер:')->value(config('const.admin')->user()->name)->readOnly();
                $form->hidden('manager')->value(config('const.admin')->user()->id);
            }


    //Тип обслуживания: Действие:
/*            if($this->edit){
                $form->text('contract', 'Номер договора:')->readOnly();
            } else {
                $form->hidden('contract', 'Номер договора:')->value($this->contract());
            }*/
            $form->text('contract', 'Номер договора:');

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
            $form->text('customer.geo_lat','Широта')->required();
            $form->text('customer.geo_lon','Долгота')->required();
/*Московская обл, г Мытищи, ул Лётная, д 34/1, кв 5*/
            if($this->edit){
                $latLon = Region::where('city',$this->customer->city)->first();
            } else {
                $latLon = Region::where('city','Москва')->first();
                $form->hidden('center_lat')->default($latLon->center_lat);
                $form->hidden('center_lon')->default($latLon->center_lon);
            }

        });


        // Обьем работ:
        $form->tab('Обьем работ',function (Form $form){

            $form->html('<div id="implementMap"><implement-map  v-bind:date="date"></implement-map></div>');

            $form->hasManyFlat('act', 'Акт', function (Form\NestedForm $form) {


                $form->text('act_nr','Номер акта');

                $form->tableFlat('volume','Обьем(ы)', function ($table) {
                    $table->select('pest','Предмет работ:')->options(Pest::all()->pluck('name','id'))->load('method', '/api/getter/methods');
                    $table->select('method','Метод:')->options(Method::all()->pluck('name','id'));
                    $table->number('square','Площадь:')->default(0);
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
//            $form->dateButton('choose_master','Дата');
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
//            dump('$this->edit',$this->edit);
//            dump('$newAct',$newAct);
//            dump('$relations',$relations);
//            dump($form);
//            exit();
                /*
                $form->volumes  = [
      24 => [
        "id" => "24",
        "parent" => "16",
        "lid_id" => $form->model()->id,
        "pest" => "7",
        "method" => "1",
        "square" => "0",
        "entity" => "2",
        "price_standard" => "99999900",
        "price_fact" => "999900",
        "_remove_" => "0",
      ],
      25 => [
          "id" => "25",
          "parent" => "16",
          "lid_id" => $form->model()->id,
          "pest" => "7",
          "method" => "1",
          "square" => "0",
          "entity" => "2",
          "price_standard" => "888800",
          "price_fact" => "888800",
          "_remove_" => "0",
      ]
                ];*/



//            ($form->customer_status == 'on') ? $contract = $this->contract(true) : $contract = $this->contract();
//            $form->contract = value($contract);

        });

/*        $form->saving(function (Form $form){
            $t = false;
            exit();
            $t = call_user_func_array([new Test,'tableTest'],[$form->act,$form->model()->id]);
            if($t) {
                $newAct = [];
                foreach ($form->act as $actKey => $actVal) {

                    foreach ($actVal as $k =>$v) if(!is_array($v)){
                        $newAct[$actKey][$k] = $v;

                    }
                }
                $form->act = $newAct;
            } else {
                dump($form);
                exit();
            }

        });*/






        return $form;
    }

/*    public function saveParent($data){

    }
    public function updateParent($data){

    }

    public function tableSave($data){

        foreach ($data as $parentID => $parentData){

            if(is_numeric($parentID)){
                // update fn


            } else {
                // save fn
            }
        }

    }*/

}
