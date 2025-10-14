<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicYearStrandSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'strand_id',
        'section_id',
        'adviser_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function adviserTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'adviser_teacher_id');
    }
}
