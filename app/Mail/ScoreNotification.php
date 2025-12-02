<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScoreNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $assessmentName;
    public $assessmentType;
    public $rawScore;
    public $maxScore;
    public $percentage;
    public $subject;
    public $academicYear;
    public $term;
    public $dateGiven;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $assessmentName, $assessmentType, $rawScore, $maxScore, $subject, $academicYear, $term, $dateGiven)
    {
        $this->studentName = $studentName;
        $this->assessmentName = $assessmentName;
        $this->assessmentType = $assessmentType;
        $this->rawScore = $rawScore;
        $this->maxScore = $maxScore;
        $this->percentage = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;
        $this->subject = $subject;
        $this->academicYear = $academicYear;
        $this->term = $term;
        $this->dateGiven = $dateGiven;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Assessment Score - ' . $this->studentName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.score_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
