<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_enrollment_id',
        'academic_year_strand_subject_id',
        'fq_grade',
        'sq_grade',
        'a_grade',
        'f_grade',
        'remarks',
        'description',
    ];

    protected $casts = [
        'fq_grade' => 'decimal:2',
        'sq_grade' => 'decimal:2',
        'a_grade' => 'decimal:2',
        'f_grade' => 'decimal:2',
    ];

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function academicYearStrandSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicYearStrandSubject::class);
    }

    public function subjectRecords(): HasMany
    {
        return $this->hasMany(SubjectRecord::class);
    }
}
