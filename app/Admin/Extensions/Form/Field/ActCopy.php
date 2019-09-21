<?php

namespace App\Admin\Extensions\Form\Field;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ActCopy extends Field
{
    protected static $css = [
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    protected static $js = [
        '/vendor/laravel-admin/moment/min/moment-with-locales.min.js',
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];
    protected $format = 'YYYY-MM-DD';

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }
    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $config = [];

    protected $view = 'admin.form.copy_act';

    public $act;






    public function act($act = '')
    {
        return  $this->act = $act;
    }


    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->options['format'] = $this->format;
        $this->options['locale'] = config('app.locale');
        $this->options['allowInputToggle'] = true;


        $this->script = <<< EOT



    $(document).on('click','.copy-act',function(){
    let valID = $(this).text();
    let from = $("#datetimepicker_from_"+valID).val();
    let to = $("#datetimepicker_to_"+valID).val();
    
                    $.ajax({
                    type: "get",
                    url: "/copy_act?act="+valID+"&from="+from+"&to="+to,
                }).success(function( data ) {
                     location.reload();
                });


    });
EOT;

//        $this->prepend('<i class="fa fa-calendar fa-fw"></i>')
//            ->defaultAttribute('style', 'width: 110px');


        $this->addVariables([
            'act' => $this->value,
        ]);



        return parent::render();
    }
}
