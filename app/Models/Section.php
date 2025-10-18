<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'grade',
        'strand_id',
    ];

    /**
     * Section belongs to a Strand
     */
    public function strand(): BelongsTo
    {
        return $this->belongsTo(Strand::class);
    }
}
