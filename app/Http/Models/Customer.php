<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = 'customers';
    protected $fillable = [
        'nick_name', 'email', 'password', 'is_deleted'
    ];
}
