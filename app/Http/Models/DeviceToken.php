<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/14/19
 * Time: 10:55 AM
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeviceToken extends Model
{
    public $table = 'device_token';

    protected $fillable = ['dooralarm_id', 'customer_id', 'device_token', 'parent_id'];

    public function notifications()
    {
        return $this->hasMany(Notify::class, 'device_token_id');
    }

    public static function findOrCreate($data = array())
    {
        $object = self::where($data)->first();
        return $object ? $object : new self();
    }

    public static function findByDeviceToken($deviceTokenStr)
    {
        if (!$deviceTokenStr)
            return null;
        $deviceTokens = DeviceToken::where('device_token', $deviceTokenStr)->get();
        if (!$deviceTokens || $deviceTokens->isEmpty())
            return null;
        return $deviceTokens;
    }

    /*
     * params int $customerId
     * return null | id device contain device token parent
     * */
    public static function findDeviceByCustomerId($customerId)
    {
        $deviceTokenIds = DeviceToken::where(['dooralarm_id' => null, 'customer_id' => $customerId, 'parent_id' => null])
            ->pluck('id');
        return $deviceTokenIds;
    }

    public static function deviceTokenShare(Customer $customer, DoorAlarm $doorAlarm, $parentDeviceTokenIds)
    {
        try {
            DB::beginTransaction();
            if ($parentDeviceTokenIds->isEmpty())
                return false;
            foreach ($parentDeviceTokenIds as $parentDeviceTokenId) {
                $deviceToken = DeviceToken::where(['customer_id' => $customer->id, 'dooralarm_id' => $doorAlarm->id, 'parent_id' => $parentDeviceTokenId])
                    ->first();
                if ($deviceToken)
                    return false;
                $update = Customer::attachDoorAlarm($customer, $doorAlarm->id, $parentDeviceTokenId);
                if (!$update)
                    return false;
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}