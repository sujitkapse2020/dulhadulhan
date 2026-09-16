<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    protected $table = 'user_photos';

    protected $fillable = [
        'user_id',
        'image',
        'path',
        'is_profile',
        'is_private',
        'sort_order',
        'status'
    ];
}
