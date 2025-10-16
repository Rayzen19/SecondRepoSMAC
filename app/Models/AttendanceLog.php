<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'student_number',
        'student_name',
        'strand',
        'year_level',
        'section',
        'semester',
        'subject',
        'assessment_type',
        'date',
        'time',
        'status',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    /**
     * Get the student that owns the attendance log.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year for the attendance log.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope a query to only include logs for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include logs for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include logs with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted time attribute.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('H:i') : '-';
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'excused' => 'info',
            default => 'secondary',
        };
    }
}