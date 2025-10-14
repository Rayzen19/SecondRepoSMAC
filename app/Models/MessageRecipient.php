<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    use HasFactory;

    protected $table = 'message_recipients';
    protected $fillable = ['message_id', 'recipient_id', 'read_at'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function recipient()
    {
        return $this->belongsTo(\App\Models\User::class, 'recipient_id');
    }
}
