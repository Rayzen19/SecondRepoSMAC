<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'hours',
        'units',
        'type',
        'semester',
    ];

    /**
     * One subject has many StrandSubject rows (pivot model).
     */
    public function strandSubjects(): HasMany
    {
        return $this->hasMany(StrandSubject::class);
    }

    /**
     * Many-to-many: Subject <-> Strand via strand_subjects.
     */
    public function strands(): BelongsToMany
    {
        return $this->belongsToMany(Strand::class, 'strand_subjects')
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
}
