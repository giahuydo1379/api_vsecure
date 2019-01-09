<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DoorAlarmCustomer extends Model
{
	protected $table = 'dooralarm_customer';
    protected $fillable = [
        'mac_device', 'id_customer', 'is_owner', 'is_deleted', 'created_at', 'updated_at'
    ];
}
