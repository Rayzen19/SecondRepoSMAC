<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'subject', 'body', 'attachment_path', 'attachment_name', 'attachment_type', 'attachment_size'];

    protected static function boot()
    {
        parent::boot();
        
        // Delete all recipients and reports when message is deleted
        static::deleting(function ($message) {
            $message->recipients()->delete();
            $message->reports()->delete();
        });
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    public function reports()
    {
        return $this->hasMany(MessageReport::class);
    }

    /**
     * Check if message has been reported by a specific user
     */
    public function isReportedBy($userId): bool
    {
        return $this->reports()->where('reported_by', $userId)->exists();
    }

    /**
     * Get count of reports for this message
     */
    public function getReportsCountAttribute(): int
    {
        return $this->reports()->count();
    }
}