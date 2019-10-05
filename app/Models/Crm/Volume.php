<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    protected $guarded = [];
    public $fillable = [
        'parent',
        'lid_id',
        'pest',
        'square',
        'entity',
        'method',
        'price_standard',
        'price_fact',
        'kpi',
        'note'
    ];

    public function act(){
        return $this->belongsTo(Act::class,'parent');
    }

    public function getMethodAttribute($value)
    {
        return explode(',', $value);
    }

//    public function setMethodAttribute($value)
//    {
//        $this->attributes['method'] = implode(',', $value);
//    }

}












