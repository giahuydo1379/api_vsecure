<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DoorAlarm extends Model
{
    public $table = 'dooralarm';

    protected $fillable = ['name', 'password', 'location', 'mac', 'version', 'volume', 'arm_delay',
        'alarm_duration', 'self_test_mode', 'timing_arm_disarm', 'is_arm', 'is_home', 'is_alarm', 'door_status',
        'battery_capacity', 'reamaining', 'is_deleted'
    ];

    protected $hidden = ['password',];
}
