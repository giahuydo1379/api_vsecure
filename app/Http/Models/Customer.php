<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function deviceToken()
    {
        return $this->hasMany(DeviceToken::class, 'customer_id');
    }


    public static function saveDoorAlarm(Customer $customer, DoorAlarm $doorAlarm, $deviceTokenId)
    {
        try {
            DB::beginTransaction();
            $doorAlarm->save();
            $doorAlarmId = $doorAlarm->id;
            $deviceToken = new DeviceToken(['customer_id' => $customer->id, 'dooralarm_id' => $doorAlarmId,
                'parent_id' => $deviceTokenId]);
            $deviceToken->save();
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public static function attachDoorAlarm(Customer $customer, $doorAlarmId, $deviceTokenId)
    {
        try {
            DB::beginTransaction();
            $deviceToken = new DeviceToken(['customer_id' => $customer->id, 'dooralarm_id' => $doorAlarmId,
                'parent_id' => $deviceTokenId]);
            $deviceToken->save();
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
