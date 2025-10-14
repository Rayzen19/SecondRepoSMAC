<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'birthdate',
        'email',
        'mobile_number',
        'address',
        'guardian_name',
        'guardian_contact',
        'guardian_email',
        'program',
        'academic_year',
        'academic_year_id',
        'status',
        'profile_picture',
        'generated_password_encrypted',
    ];

    public function getRouteKeyName(): string
    {
        return 'student_number';
    }

    public function getNameAttribute(): string
    {
        return $this->last_name . ', ' . $this->first_name;
    }

    public function getContactAttribute(): string
    {
        return $this->mobile_number . ' | ' . $this->email;
    }

    public function getGuardianAttribute(): string
    {
        return $this->guardian_name . ' | ' . $this->guardian_contact;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at >= now()->startOfYear();
    }

}
