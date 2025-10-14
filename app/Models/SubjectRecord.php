<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_strand_subject_id',
        'name',
        'description',
        'max_score',
        'type',
        'quarter',
        'date_given',
        'remarks',
    ];

    protected $casts = [
        'date_given' => 'date',
        'max_score' => 'decimal:2',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AcademicYearStrandSubject::class, 'academic_year_strand_subject_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(SubjectRecordResult::class);
    }
}
