<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'email',
        'phone',
        'address',
        'department',
        'specialization',
        'term',
        'status',
        'profile_picture',
    ];

    public function getRouteKeyName(): string
    {
        return 'employee_number';
    }

    public function getNameAttribute(): string
    {
        $title = $this->gender === 'female' ? "Ma'am" : "Sir";
        return $title . ' ' . $this->last_name . ', ' . $this->first_name;
    }
}
