<?php

namespace App\Admin\Controllers\Crm;

use App\Models\Crm\Act;
use App\Http\Controllers\Controller;
use App\Models\Helper\Method;
use App\Models\Helper\Pest;
use App\Models\Helper\Square;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ActController extends Controller
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
        $grid = new Grid(new Act);

        $grid->id('ID');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    public function detail($id)
    {
        $show = new Show(Act::findOrFail($id));

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
    public function form()
    {
        $form = new Form(new Act);

        $form->hasMany('volume','Обемы', function ($form){
            $form->select('pest','Предмет работ:')->options(Pest::all()->pluck('name','id'))->load('method', '/api/getter/methods');
            $form->select('method','Метод:')->options(Method::all()->pluck('name','id'));
            $form->number('square','Площадь:')->default(0);
            $form->select('entity','Единица площади:')->options(Square::all()->pluck('name','id'));
            $form->text('price_standard','Цена гост');
            $form->text('price_fact','Цена факт.');
        });

        return $form;
    }
}
