<?php

namespace App\Admin\Controllers\Helper;

use App\Models\Calc\MethodPrice;
use App\Models\Helper\Constant;
use App\Models\Helper\Method;
use App\Models\Helper\Pest;
use App\Http\Controllers\Controller;
use App\Models\Store\Chemical;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PestController extends Controller
{
    use HasResourceActions;

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
            ->body($this->grid());
    }

    public function checkTable($id){
        $pest =  Pest::where('id',$id)->first();
        foreach ($pest->methods as $method){
            MethodPrice::where('method',$method)->where('pest',$id)->firstOrCreate([
                'method' => $method,
                'pest' => $id,
            ]);
        }
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
        $this->checkTable($id);
        $table = $this->convertTable($id);
        $pest = json_encode(Pest::where('id',$id)->first()->toArray(),JSON_UNESCAPED_UNICODE);
        $chemicals = json_encode(Chemical::all()->keyBy('id')->toArray(),JSON_UNESCAPED_UNICODE);
        $methods = json_encode(Method::all()->keyBy('id')->toArray(),JSON_UNESCAPED_UNICODE);
        $constant = json_encode(Constant::all()->keyBy('id')->toArray(),JSON_UNESCAPED_UNICODE);
        return $content
            ->header('Detail')
            ->description('description')
            ->body("<div id='calcPest'><calc-pest 
                                v-bind:table='" .$table. "' 
                                v-bind:pest='" .$pest. "' 
                                v-bind:chemicals='" .$chemicals. "' 
                                v-bind:methods='" .$methods. "' 
                                v-bind:constant='" .$constant. "' 
                                
                                ></calc-pest></div>");
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Pest);

        $grid->id('ID')->sortable();
        $grid->name('Вредитель')->sortable()->editable();
        $grid->sort('Сортировка')->sortable()->editable();
        $grid->active('Активность')->switch();

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
        $show = new Show(Pest::findOrFail($id));

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
        $form = new Form(new Pest);

        $form->display('id');
        $form->text('name','Вредитель');
        $form->multipleSelect('methods','Методы')->options(Method::all()->pluck('name','id'));
        $form->number('sort')->default(10);
        $form->switch('active');
//
        return $form;
    }


    public function convertTable($pest){
       $tbl = MethodPrice::where('pest',$pest)->orderBy('method','ASC')->get()->toArray();
       $arr = [];
       foreach ($tbl as $t){
           $arr[$t['method']][$t['remedy']] = $t;
       }
       return json_encode($arr);

    }

    public function getTable($pest){
        return json_encode(MethodPrice::where('pest',$pest)->orderBy('method','ASC')->get()->toArray());
    }

    public function saveField(){
        $b = MethodPrice::where('id',request()->get('id'))->update([request()->get('field') => request()->get('value')]);
        if($b > 0){
            return $this->convertTable(request()->get('pest'));
        } else {
            return 0;
        }
    }

    public function addRemedy(){
        $create = MethodPrice::create([
            'method' => request()->get('method'),
            'pest' => request()->get('pest'),
        ])->id;
        if($create){
            return $this->convertTable(request()->get('pest'));
        } else {
            return 0;
        }
    }
    public function removeRemedy(){
      return  MethodPrice::where('id',request()->get('id'))->delete();
    }
}
