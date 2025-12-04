<?php

namespace App\Mail;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $recipientName;
    public $recipientType; // 'student', 'teacher', 'guardian'

    /**
     * Create a new message instance.
     */
    public function __construct(Announcement $announcement, string $recipientName, string $recipientType)
    {
        $this->announcement = $announcement;
        $this->recipientName = $recipientName;
        $this->recipientType = $recipientType;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('New Announcement: ' . $this->announcement->title)
            ->view('emails.announcement_notification')
            ->with([
                'announcement' => $this->announcement,
                'recipientName' => $this->recipientName,
                'recipientType' => $this->recipientType,
                'appName' => config('app.name'),
            ]);
    }
}
