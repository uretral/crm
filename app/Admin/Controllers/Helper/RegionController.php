<?php

namespace App\Admin\Controllers\Helper;

use App\Models\Helper\Company;
use App\Models\Helper\Region;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RegionController extends Controller
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
        $grid = new Grid(new Region);

        $grid->id('ID');
        $grid->region('Регион');
        $grid->city('Город');
//        $grid->phone('Телефон')->select();
        $grid->center_lat('Широта');
        $grid->center_lon('Долгота');
        $grid->zoom('zoom');
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
        $show = new Show(Region::findOrFail($id));

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
        $form = new Form(new Region);

        $form->display('id');
        $form->text('region','Регион');
        $form->text('city','Город');
        $form->text('center_lat','Широта');
        $form->text('center_lon','Долгота');
        $form->text('zoom','zoom');
        $form->hasMany('phones','Телефоны',function ($form){
            $form->select('owner','Владелец')->options(Company::all()->pluck('name','id'));
            $form->text('hint','Назначение');
            $form->text('phone','Телефон');
        });

/*        $form->saving(function (Form $form) {

            dump($form);
            exit();

        });*/
        return $form;
    }
}
