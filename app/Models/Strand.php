<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Strand extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * One strand has many StrandSubject rows (pivot model).
     */
    public function strandSubjects(): HasMany
    {
        return $this->hasMany(StrandSubject::class);
    }

    /**
     * Many-to-many: Strand <-> Subject via strand_subjects.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'strand_subjects')
            ->withPivot([
                'semestral_period',
                'written_works_percentage',
                'performance_tasks_percentage',
                'quarterly_assessment_percentage',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->withTimestamps();
    }

    /**
     * Helper to get only active subjects on non-deleted pivot rows.
     */
    public function activeSubjects(): BelongsToMany
    {
        return $this->subjects()
            ->wherePivot('is_active', true)
            ->wherePivotNull('deleted_at');
    }

    /**
     * One strand has many academic year strand advisers.
     */
    public function academicYearStrandAdvisers(): HasMany
    {
        return $this->hasMany(AcademicYearStrandAdviser::class);
    }

    
}
