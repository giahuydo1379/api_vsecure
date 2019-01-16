<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nick_name', 'email', 'password', 'is_deleted'
    ];

    protected $hidden = [
        'password',
    ];

    public function doorAlarms()
    {
        return $this->belongsToMany(DoorAlarm::class, 'device_token',
            'customer_id', 'dooralarm_id');
    }

    public function deviceToken(){
        return $this->hasMany(DeviceToken::class,'customer_id');
    }
}
