<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Act extends Model
{
    protected $guarded = [];

    public function acts(){
        return $this->belongsTo(Lid::class,'parent');
    }

    public function volume()
    {
        return $this->hasMany(Volume::class,'parent');
    }
    public function implement()
    {
        return $this->hasMany(Implement::class,'parent');
    }
}
