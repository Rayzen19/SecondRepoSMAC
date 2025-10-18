<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * The subjects that the teacher can teach.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')
            ->withTimestamps();
    }

    /**
     * Get the sections that this teacher advises.
     */
    public function advisedSections()
    {
        return $this->hasMany(AcademicYearStrandSection::class, 'adviser_teacher_id');
    }

    /**
     * Get the subjects that this teacher teaches.
     */
    public function teachingAssignments()
    {
        return $this->hasMany(AcademicYearStrandSubject::class, 'teacher_id');
    }
}
