<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'image_url',
        'image_path',
        'is_active',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        // Guard against missing columns/tables in some environments
        $table = $this->getTable();

        if (Schema::hasTable($table)) {
            if (Schema::hasColumn($table, 'is_active')) {
                $query->where('is_active', true);
            }

            if (Schema::hasColumn($table, 'published_at')) {
                $query->where(function ($q) {
                    $q->whereNull('published_at')
                        ->orWhere('published_at', '<=', Carbon::now());
                });
            }

            if (Schema::hasColumn($table, 'expires_at')) {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', Carbon::now());
                });
            }
        }

        return $query;
    }

    public function scopeLatest($query)
    {
        $table = $this->getTable();

        // Prefer published_at if it exists, otherwise fall back to created_at
        if (Schema::hasTable($table) && Schema::hasColumn($table, 'published_at')) {
            return $query->orderBy('published_at', 'desc')->orderBy('created_at', 'desc');
        }
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPublished()
    {
        return !$this->published_at || $this->published_at->isPast();
    }

    public function getImageAttribute()
    {
        // Prioritize uploaded image over URL
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return $this->image_url;
    }

    public function hasImage()
    {
        return $this->image_path || $this->image_url;
    }
}
