<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use App\Models\StudentEnrollment;
use Throwable;

class AcademicYearStrandSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'strand_id',
        'subject_id',
        'teacher_id',
        'academic_year_strand_section_id',
        'written_works_percentage',
        'performance_tasks_percentage',
        'quarterly_assessment_percentage',
        'written_works_based_grade_percentage',
        'performance_tasks_based_grade_percentage',
        'quarterly_assessment_based_grade_percentage',
        'over_all_based_grade_percentage',
        'academic_year_strand_adviser_id',
        'grades_published',
        'school_year_ended',
        'school_year_ended_at',
    ];

    protected $casts = [
        'grades_published' => 'boolean',
        'school_year_ended' => 'boolean',
        'school_year_ended_at' => 'datetime',
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

    public function sectionAssignment(): BelongsTo
    {
        return $this->belongsTo(AcademicYearStrandSection::class, 'academic_year_strand_section_id');
    }

    /**
     * Booted model events.
     * When a new assignment is created, ensure SubjectEnrollment rows
     * exist for all matching StudentEnrollment rows so students see the
     * new subject immediately on their grades page.
     */
    protected static function booted()
    {
        static::created(function (AcademicYearStrandSubject $ays) {
            try {
                $query = StudentEnrollment::where('academic_year_id', $ays->academic_year_id);

                // If assignment targets a specific section, match that section
                if ($ays->academic_year_strand_section_id) {
                    $query->where('academic_year_strand_section_id', $ays->academic_year_strand_section_id);
                } else {
                    // otherwise match enrollments in the same strand for the year
                    $query->where('strand_id', $ays->strand_id);
                }

                $enrollments = $query->get();

                foreach ($enrollments as $en) {
                    // create if not exists
                    $en->subjectEnrollments()->firstOrCreate([
                        'academic_year_strand_subject_id' => $ays->id,
                    ]);
                }

                Log::info('Auto-synced subject enrollments for new assignment', ['ays_id' => $ays->id, 'created_for' => $enrollments->count()]);
            } catch (Throwable $e) {
                Log::warning('Failed to auto-sync subject enrollments on AYS created: ' . $e->getMessage(), ['ays_id' => $ays->id ?? null]);
            }
        });
    }
}
