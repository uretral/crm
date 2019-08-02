<?php

namespace App\Admin\Extensions\Form\Field;

use Encore\Admin\Form\Field;

class DateButton extends Field
{
    protected static $css = [
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    protected static $js = [
        '/vendor/laravel-admin/moment/min/moment-with-locales.min.js',
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];

    protected $format = 'YYYY-MM-DD';

    protected $view = 'admin.datebutton';

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

    public function render()
    {

        $this->script = <<<EOT
        
        function formatDate(date) {
            var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
    
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
    
            return [year, month, day].join('-');
        }

        $('.$this->id').datepicker()
            .on('changeDate', function(ev){
                $('.$this->id').datepicker('hide'); 
                window.buttonDate = formatDate(ev.date.valueOf());  
                window.buttonAction = $(this).attr('rel');  
               
            });
EOT;

        return parent::render();
    }
}
