<?php

namespace App\Models\Helper;

use Illuminate\Database\Eloquent\Model;

class Pest extends Model
{
    protected $table = 'helper_pests';

    public function getMethodsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setMethodsAttribute($value)
    {
        $this->attributes['methods'] = implode(',', $value);
    }









}
