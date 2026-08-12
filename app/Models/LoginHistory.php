<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip',
        'device',
        'browser',
        'os',
        'login_time',
        'logout_time',
    ];
}
