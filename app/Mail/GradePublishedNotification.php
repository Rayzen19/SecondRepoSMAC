<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GradePublishedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $studentName;
    public string $subjectName;
    public string $strandName;
    public string $schoolYearName;
    public string $semester;

    public function __construct(
        string $studentName,
        string $subjectName,
        string $strandName,
        string $schoolYearName,
        string $semester
    ) {
        $this->studentName = $studentName;
        $this->subjectName = $subjectName;
        $this->strandName = $strandName;
        $this->schoolYearName = $schoolYearName;
        $this->semester = $semester;
    }

    public function build()
    {
        return $this->subject('SMAC: Grades Published for ' . $this->subjectName)
            ->view('emails.grade_published_notification')
            ->with([
                'studentName' => $this->studentName,
                'subjectName' => $this->subjectName,
                'strandName' => $this->strandName,
                'schoolYearName' => $this->schoolYearName,
                'semester' => $this->semester,
            ]);
    }
}
