<?php

namespace App\Models\Helper;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'helper_phones';

    protected $fillable = ['region','owner','hint','phone'];

    public function region(){
        return $this->belongsTo(Region::class,'region');
    }
}
