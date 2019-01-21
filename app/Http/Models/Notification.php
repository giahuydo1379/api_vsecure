<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/14/19
 * Time: 10:55 AM
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $table = 'notifications';

    protected $fillable = ['dooralarm_id', 'customer_id', 'device_token'];

    public static function findOrCreate($data = array())
    {
        $object = self::where($data)->first();
        return $object ? $object : new self();
    }
}