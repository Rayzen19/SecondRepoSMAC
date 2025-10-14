<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYearStrandSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'strand_id',
        'subject_id',
        'teacher_id',
        'written_works_percentage',
        'performance_tasks_percentage',
        'quarterly_assessment_percentage',
        'written_works_based_grade_percentage',
        'performance_tasks_based_grade_percentage',
        'quarterly_assessment_based_grade_percentage',
        'over_all_based_grade_percentage',
        'academic_year_strand_adviser_id',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class);
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(AcademicYearStrandAdviser::class, 'academic_year_strand_adviser_id');
    }
}
