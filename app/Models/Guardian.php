<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guardian_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'email',
        'mobile_number',
        'address',
        'status',
        'profile_picture',
    ];
}
