<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Lid extends Model
{
    protected $table = 'lids';
    public function statuses()
    {
        return $this->hasMany(LidStatus::class,'lid_id')->orderBy('created_at');
    }
    public function actions(){
        return $this->hasMany(LidAction::class,'lid_id')->orderBy('action_date');
    }
    public function status()
    {
        return $this->hasOne(LidStatus::class,'lid_id');
    }


    public function customer()
    {
        return $this->hasOne(Customer::class,'lid_id');
    }

    public function act(){
        return $this->hasMany(Act::class,'parent');
    }
    public function acts(){
        return $this->hasMany(Act::class,'parent')->orderBy('id');
    }

    public function master()
    {
        return $this->hasMany(MasterVolume::class);
    }

    public function sanitary()
    {
        return $this->hasMany(SanitaryVolume::class);
    }

    public function volumes(){
        return $this->hasMany(Volume::class,'lid_id');
    }
    public function implements(){
        return $this->hasMany(Implement::class,'lid_id');
    }

    public function volume(){
        $acts = $this->act();
        $res = [];
        foreach ($acts->getParent()->getRelationValue('act') as $act){
            $res[$act->id] = Volume::where('parent',$act->id);
        }

        return $res;
    }
    public function implement(){

        $acts = $this->act();
        $res = [];
        foreach ($acts->getParent()->getRelationValue('act') as $act){
            $res[$act->id] = Implement::where('parent',$act->id);
        }

        return $res;
    }
/*    public function volume(){

        return call_user_func([ new Act(),'volumes']);
    }*/


//    Public function getNameAttribute($name)
//    {
//        Return array_values(json_decode($name, true) ?: []);
//    }
//
//    Public function setNameAttribute($name)
//    {
//        $this->attributes['name'] = json_encode(array_values($name));
//    }

}

