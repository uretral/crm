<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class LidAction extends Model
{
    protected $table = 'lid_actions';
    protected $fillable = ['lid_id','action','date','comment','manager','salt'];
}





