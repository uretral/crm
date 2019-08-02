<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class LidStatus extends Model
{
    protected $table = 'lid_statuses';
    protected $fillable = ['lid_id','status','date'];

    public function lid()
    {
        return $this->belongsTo(Lid::class,'lid_id');
    }

    public static function statusShow($lidID)
    {
        return view('form.status_show',['data' => LidStatus::where('lid_id',$lidID)->get()]);

    }

    public function statusAdd($model)
    {
        $lidStatus = new LidStatus();

        $lidStatus->lid_id = $model->lid_id;
        $lidStatus->status = $model->status;
        $lidStatus->date = $model->date;
        $lidStatus->save();

    }
}
