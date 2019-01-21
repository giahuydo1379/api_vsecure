<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/14/19
 * Time: 10:55 AM
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

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

}