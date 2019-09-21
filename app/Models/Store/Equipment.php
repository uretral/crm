<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'store_equipments';
    public function name(){
        return $this->hasOne(\App\Models\Helper\Equipment::class,'name');
    }
}
