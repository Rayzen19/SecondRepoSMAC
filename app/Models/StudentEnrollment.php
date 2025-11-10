<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    public function section()
    {
        return $this->hasOneThrough(
            Section::class,
            AcademicYearStrandSection::class,
            'id', // Foreign key on academic_year_strand_sections table
            'id', // Foreign key on sections table
            'academic_year_strand_section_id', // Local key on student_enrollments table
            'section_id' // Local key on academic_year_strand_sections table
        );
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class);
    }

    /**
     * Ensure SubjectEnrollment rows exist for this student enrollment
     * by creating missing SubjectEnrollment records for all
     * AcademicYearStrandSubject assignments that match this
     * enrollment's academic_year_id and academic_year_strand_section_id.
     * Returns the number of created SubjectEnrollment records.
     */
    public function syncSubjectEnrollments(): int
    {
        $created = 0;

        $query = AcademicYearStrandSubject::where('academic_year_id', $this->academic_year_id);

        if ($this->academic_year_strand_section_id) {
            $query->where('academic_year_strand_section_id', $this->academic_year_strand_section_id);
        }

        $aysList = $query->get();

        foreach ($aysList as $ays) {
            $exists = $this->subjectEnrollments()
                ->where('academic_year_strand_subject_id', $ays->id)
                ->exists();

            if (!$exists) {
                $this->subjectEnrollments()->create([
                    'academic_year_strand_subject_id' => $ays->id,
                ]);
                $created++;
            }
        }

        return $created;
    }
}
