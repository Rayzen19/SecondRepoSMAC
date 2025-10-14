<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'strand_id',
        'academic_year_id',
        'academic_year_strand_section_id',
    'registration_number',
        'status',
    ];

    protected $casts = [
        // future: add date casts if needed
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicYearStrandSection(): BelongsTo
    {
        return $this->belongsTo(AcademicYearStrandSection::class);
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class);
    }
}
