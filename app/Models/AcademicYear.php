<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'semester',
        'academic_status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function strandSubjects()
    {
        return $this->hasMany(AcademicYearStrandSubject::class);
    }

    public function strandAdvisers()
    {
        return $this->hasMany(AcademicYearStrandAdviser::class);
    }

    public function strandSections()
    {
        return $this->hasMany(AcademicYearStrandSection::class);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ($this->semester ? " ({$this->semester} Semester)" : '');
    }

    public function getStatusAttribute()
    {
        return $this->academic_status ? ucfirst($this->academic_status) : 'N/A';
    }
}
