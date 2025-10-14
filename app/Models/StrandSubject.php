<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrandSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'strand_subjects';

    protected $fillable = [
        'strand_id',
        'subject_id',
        'semestral_period',
        'written_works_percentage',
        'performance_tasks_percentage',
        'quarterly_assessment_percentage',
        'is_active',
    ];

    protected $casts = [
        'written_works_percentage' => 'float',
        'performance_tasks_percentage' => 'float',
        'quarterly_assessment_percentage' => 'float',
        'is_active' => 'boolean',
    ];

    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }

    public function subject(): BelongsTo
    {
        // Load even if the Subject was soft-deleted
        return $this->belongsTo(Subject::class)->withTrashed();
    }
}
