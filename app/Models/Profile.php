<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',  
        'last_name',
        'gender',
        'dob',
        'profile_for',
        'age',
        'height',
        'weight',
        'marital_status',
        'mother_tongue',
        'religion_id',
        'caste_id',
        'sub_caste_id',
        'gotra',
        'manglik',
        'blood_group',
        'physical_status',
        'body_type',
        'complexion',
        'about_me',
        'hobbies',
        'eating_habits',
        'drinking_habits',
        'smoking_habits'
    ];

    protected $casts = [
        'dob' => 'date',
    ];
}
