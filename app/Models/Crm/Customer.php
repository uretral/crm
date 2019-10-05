<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
//    protected $guarded = [];
    protected $fillable = [
        'lid_id',
        'name',
        'icon',
        'status',
        'organization',
        'phone',
        'phone_ext',
        'address',
        'addresses',
        'email',
        'emails',
        'destination',
        'created_at',
        'updated_at',
        'city',
        'region',
        'geo_lat',
        'geo_lon'
    ];



















}
