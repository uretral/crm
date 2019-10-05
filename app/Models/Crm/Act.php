<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Act extends Model
{
    protected $guarded = [];
    protected $fillable = ['act_nr','parent','address','booking_act_file','booking_act_signed','booking_act_transferred','destination','finished','floating','floating_date_from','floating_date_to','prefer_time_from','prefer_time_to','implement_act_file','implement_act_signed','lat','lon','region'];

    public function acts(){
        return $this->belongsTo(Lid::class,'parent');
    }
    public function lid(){
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
