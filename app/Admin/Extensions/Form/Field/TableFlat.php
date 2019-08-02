<?php


namespace App\Admin\Extensions\Form\Field;




use App\Admin\Controllers\Crm\LidController;
use Encore\Admin\Form\NestedForm;
use Illuminate\Database\Eloquent\Relations\HasMany as Relation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Encore\Admin\Form\Field\Hidden;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\String_;

class TableFlat extends HasManyFlat
{
    /**
     * @var string
     */
    protected $viewMode = 'table';

    protected $className = '';

    /**
     * Table constructor.
     *
     * @param string $column
     * @param array  $arguments
     */
    public function __construct($column, $arguments = [])
    {

        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            list($this->label, $this->builder) = $arguments;
        }
    }


    public static function str_replace_once($search, $replace, $text)
    {
        $pos = strpos($text, $search);
        return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    public function buildRelatedFlatForms($id = '')
    {
        $this->relationName = $this->column;
        $model = $this->form->model();
        $relation = call_user_func([$model, $this->relationName]);

        $forms = [];
        foreach ($relation as $k => $arModel) if($k == $id){

            foreach ($arModel->get() as  $m_model) {

                $m_data = $m_model->getAttributes();
                $m_key = $m_data['id'];
//                $m_model = $arModel->getModel()->forceFill($m_data);
                $forms[$m_key] = $this->buildNestedFormFlat($this->column(), $this->builder , $m_key)->fill($m_data);
            }

        }


        ///////////

        $headers = [];
        $fields = [];
        $fields_tpl = [];
        $hidden = [];
        $scripts = [];

        /* @var Field $field */
        foreach ($this->buildNestedForm($this->column, $this->builder)->fields() as $field) {

            $field_tpl = $field;

            $field->setElementNameFlat($this->relationName,$this->elementName);

            $replace =preg_replace('#[0-9]+#','new___PARENT_KEY__',$field->getElementName());

            $field_tpl->setElementName($replace);

            if (is_a($field, Hidden::class)) {
                $hidden[] = $field->render();
            } else {
                /* Hide label and set field width 100% */
                $field->setLabelClass(['hidden']);
                $field_tpl->setLabelClass(['hidden']);
                $field->setWidth(12, 0);
                $field_tpl->setWidth(12, 0);
                $fields[] = $field->render();
                $fields_tpl[] = $field_tpl->render();
                $headers[] = $field->label();
            }

            /*
             * Get and remove the last script of Admin::$script stack.
             */
            if ($field->getScript()) {
                $scripts[] = array_pop(Admin::$script);
            }


        }



        /* Build row elements */
        $template_child = array_reduce($fields_tpl, function ($all, $field) {
            $all .= "<td>{$field}</td>";

            return $all;
        }, '');

        /* Build cell with hidden elements */
        $template_child .= '<td class="hidden">'.implode('', $hidden).'</td>';



        $this->setupScript(implode("\r\n", $scripts));


        // specify a view to render.
        $this->view = $this->views[$this->viewMode];

        return [
            'headers'        => $headers,
            'forms'          => $forms,
            'template_child' => $template_child,
            'relationName'   => $this->relationName,
            'options'        => $this->options,
            'child_column'   => $this->column,
            'child_label'    => $this->label,
            'parent_name'    => $this->elementName,
        ];
    }

    /**
     * @return array
     */
    protected function buildRelatedForms()
    {
        $this->relationName = $this->column;

        if (is_null($this->form)) {
            return [];
        }

        $model = $this->form->model();
        $relation = call_user_func([$model, $this->relationName]);

        $forms = [];


        if ($values = old($this->column)) {
            foreach ($values as $key => $data) {
                if ($data[NestedForm::REMOVE_FLAG_NAME] == 1) {
                    continue;
                }
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)->fill($data);
            }
        } else {
            foreach ($this->value as $key => $data) {
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)->fill($data);
            }
        }

        return $forms;
    }

    public function prepare($input)
    {
        $form = $this->buildNestedForm($this->column, $this->builder);

        $prepare = $form->prepare($input);

        return collect($prepare)->reject(function ($item) {
            return $item[NestedForm::REMOVE_FLAG_NAME] == 1;
        })->map(function ($item) {
            unset($item[NestedForm::REMOVE_FLAG_NAME]);

            return $item;
        })->toArray();
    }

    protected function getKeyName()
    {
        if (is_null($this->form)) {
            return;
        }

        return 'id';
    }

    protected function buildNestedFormFlat($column, \Closure $builder, $key = null)
    {

        $form = new NestedForm($column);

        $form->setForm($this->form)
            ->setKey($key);

        call_user_func($builder, $form);

        $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }

    protected function buildNestedForm($column, \Closure $builder, $key = null)
    {
        $form = new NestedForm($column);

        $form->setForm($this->form)
            ->setKey($key);

        call_user_func($builder, $form);

        $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }



    /**
     * Render the `HasMany` field for table style.
     *
     * @throws \Exception
     *
     * @return mixed
     */


    protected function renderTableFlat()
    {
        $headers = [];
        $fields = [];
        $hidden = [];
        $scripts = [];





        /* @var Field $field */
        foreach ($this->buildNestedForm($this->column, $this->builder)->fields() as $field) {


            if (is_a($field, Hidden::class)) {

                $replace = str_replace('new___LA_KEY__','new___PARENT_KEY__',$this->elementName);
                $field->setElementName($replace.'[new___LA_KEY__][_remove_]');
                $hidden[] = $field->render();
            } else {
                $replace = str_replace('new___LA_KEY__','new___PARENT_KEY__',$this->elementName);
                $field->setElementName($replace.'[new___LA_KEY__]['.$field->column().']');

                /* Hide label and set field width 100% */
                $field->setLabelClass(['hidden']);
                $field->setWidth(12, 0);
                $fields[] = $field->render();
                $headers[] = $field->label();
            }

            /*
             * Get and remove the last script of Admin::$script stack.
             */
            if ($field->getScript()) {
                $scripts[] = array_pop(Admin::$script);
            }
        }

        /* Build row elements */
        $template = array_reduce($fields, function ($all, $field) {
            $all .= "<td>{$field}</td>";

            return $all;
        }, '');

        /* Build cell with hidden elements */
        $template .= '<td class="hidden">'.implode('', $hidden).'</td>';

        $this->setupScript(implode("\r\n", $scripts));

        // specify a view to render.
        $this->view = $this->views[$this->viewMode];

        return parent::render()->with([
            'headers'      => $headers,
            'forms'        => $this->buildRelatedForms(),
            'template'     => $template,
            'relationName' => $this->relationName,
            'options'      => $this->options,
        ]);
    }


    public function render()
    {
//        return $this->renderTableParent();
        if ($this->viewMode == 'table') {
            return $this->renderTableFlat();
        }

        // specify a view to render.
        $this->view = $this->views[$this->viewMode];



        list($template, $script) = $this->buildNestedForm($this->column, $this->builder)
            ->getTemplateHtmlAndScript();

        $this->setupScript($script);


        return parent::render()->with([
            'forms'        => $this->buildRelatedForms(),
            'template'     => $template,
            'relationName' => $this->relationName,
            'options'      => $this->options,
        ]);
    }
}

