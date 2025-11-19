<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuardianNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $guardianName;
    public string $studentName;
    public string $studentEmail;
    public string $studentPassword;
    public string $studentNumber;
    public string $guardianEmail;
    public string $guardianPassword;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $guardianName,
        string $studentName,
        string $studentEmail,
        string $studentPassword,
        string $studentNumber,
        string $guardianEmail,
        string $guardianPassword,
        ?string $loginUrl = null
    ) {
        $this->guardianName = $guardianName;
        $this->studentName = $studentName;
        $this->studentEmail = $studentEmail;
        $this->studentPassword = $studentPassword;
        $this->studentNumber = $studentNumber;
        $this->guardianEmail = $guardianEmail;
        $this->guardianPassword = $guardianPassword;
        $this->loginUrl = $loginUrl ?: 'http://127.0.0.1:8000/admin/login';
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Student Account Created - ' . $this->studentName)
            ->view('emails.guardian_notification')
            ->with([
                'guardianName' => $this->guardianName,
                'studentName' => $this->studentName,
                'studentEmail' => $this->studentEmail,
                'studentPassword' => $this->studentPassword,
                'studentNumber' => $this->studentNumber,
                'guardianEmail' => $this->guardianEmail,
                'guardianPassword' => $this->guardianPassword,
                'loginUrl' => $this->loginUrl,
                'appName' => config('app.name'),
            ]);
    }
}
