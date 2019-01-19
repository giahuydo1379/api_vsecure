<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    public $table = 'notifications';
    protected $fillable = ['dooralarm_id', 'action', 'model_device'];

    public function deviceToken()
    {
        return $this->belongsTo(DeviceToken::class, 'device_token_id');
    }
}
