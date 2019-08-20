<?php

namespace App\Models\Helper;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'helper_regions';

    public function phones(){
        return $this->hasMany(Phone::class,'region');
    }
    public function cities(){
        $cities = $this::all()->toArray();
        return array_column($cities, NULL, 'city');
    }
    public function regions(){
        $cities = $this::all()->toArray();
        return array_column($cities, NULL, 'region');
    }
}
