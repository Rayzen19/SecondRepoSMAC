<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectRecordResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_record_id',
        'student_id',
        'remarks',
        'description',
        'date_submitted',
        'raw_score',
        'base_score',
        'final_score',
    ];

    protected $casts = [
        'date_submitted' => 'date',
        'raw_score' => 'decimal:2',
        'base_score' => 'decimal:2',
        'final_score' => 'decimal:2',
    ];

    public function subjectRecord(): BelongsTo
    {
        return $this->belongsTo(SubjectRecord::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
