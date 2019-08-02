<?php

namespace App\Models\Crm;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class Implement extends Model
{
    protected $guarded = [];

    public function act(){
        return $this->belongsTo(Act::class,'parent');
    }

    public static function masters()
    {
        return Administrator::whereHas('roles', function ($q) {
            $q->where('role_id', 3); // role_start_dateid
        })->get()->pluck('name', 'id');
    }
}
