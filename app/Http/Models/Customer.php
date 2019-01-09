<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nick_name', 'email', 'password', 'is_deleted'
    ];
}
