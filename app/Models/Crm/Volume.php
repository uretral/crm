<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    protected $guarded = [];
//    public $fillable = [
//        'id',
//        'parent',
//        'lid_id',
//        'pest',
//        'square',
//        'entity',
//        'method',
//        'price_standard',
//        'price_fact',
//        'kpi',
//        'note',
//        'created_at',
//        'updated_at'
//    ];

    public function act(){
        return $this->belongsTo(Act::class,'parent');
    }

}












