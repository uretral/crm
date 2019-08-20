<?php

namespace App\Admin\Extensions\Form\Field;

use Encore\Admin\Form\Field;

class HtmlField extends Field
{

    public $display = '';

    protected $view = 'admin.form.htmlField';


    public function display($display)
    {
        $this->display = $display;

        return $this;
    }

    public function render()
    {
        return parent::render()->with('display',$this->display);
    }
}
