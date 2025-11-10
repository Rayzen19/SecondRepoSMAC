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
        
        // Delete all recipients when message is deleted
        static::deleting(function ($message) {
            $message->recipients()->delete();
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
}
