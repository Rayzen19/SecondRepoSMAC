<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guardian_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'email',
        'mobile_number',
        'address',
        'status',
        'profile_picture',
        'generated_password_encrypted',
    ];

    /**
     * Get the students associated with this guardian
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_students')
            ->withTimestamps()
            ->withPivot('deleted_at');
    }

    /**
     * Get the full name of the guardian
     */
    public function getNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return $name;
    }
}
