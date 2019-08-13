<?php

namespace App\Admin\Extensions\Form\Field;

use App\Models\Helper\Region;
use Encore\Admin\Form\Field;

class DadataAddress extends Field
{
    protected static $js = [
        '/js/dadata.js',
        '/js/leafLet.js',
    ];

    protected $view = 'admin.form.dadata_address';

    public $inputs = [];

    public function inputs($inputs = [])
    {
        $this->inputs = $inputs;
        return $this;
    }

    public function geoPoints(){
        return Region::all()->toArray();
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
        return parent::render()->with([
            'points' => json_encode($this->geoPoints(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
        ]);
    }
}
