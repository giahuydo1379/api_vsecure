<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DoorAlarm extends Model
{
    public $table = 'dooralarm';
    public $timestamps = false;

    protected $fillable = ['name', 'password', 'location', 'mac', 'version', 'volume', 'arm_delay',
        'alarm_duration', 'self_test_mode', 'timing_arm_disarm', 'is_arm', 'is_home', 'is_alarm', 'door_status',
        'battery_capacity', 'reamaining', 'is_deleted'
    ];

    protected $hidden = ['password',];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'device_token',
            'dooralarm_id', 'customer_id');
    }

    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class,'dooralarm_id');
    }
}
