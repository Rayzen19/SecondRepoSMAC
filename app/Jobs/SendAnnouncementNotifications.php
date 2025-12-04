<?php

namespace App\Jobs;

use App\Mail\AnnouncementNotification;
use App\Models\Announcement;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $announcement;

    /**
     * Create a new job instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send to all active students
            $this->sendToStudents();
            
            // Send to all active teachers
            $this->sendToTeachers();
            
            // Send to all active guardians
            $this->sendToGuardians();
            
            Log::info('Announcement notifications sent successfully', [
                'announcement_id' => $this->announcement->id,
                'announcement_title' => $this->announcement->title
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send announcement notifications', [
                'announcement_id' => $this->announcement->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send notifications to all students
     */
    private function sendToStudents(): void
    {
        Student::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->chunk(100, function ($students) {
                foreach ($students as $student) {
                    try {
                        $recipientName = $student->first_name . ' ' . $student->last_name;
                        
                        Mail::to($student->email)
                            ->send(new AnnouncementNotification(
                                $this->announcement,
                                $recipientName,
                                'student'
                            ));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send announcement to student', [
                            'student_id' => $student->id,
                            'email' => $student->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });
    }

    /**
     * Send notifications to all teachers
     */
    private function sendToTeachers(): void
    {
        Teacher::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->chunk(100, function ($teachers) {
                foreach ($teachers as $teacher) {
                    try {
                        $title = $teacher->gender === 'female' ? "Ma'am" : "Sir";
                        $recipientName = $title . ' ' . $teacher->first_name . ' ' . $teacher->last_name;
                        
                        Mail::to($teacher->email)
                            ->send(new AnnouncementNotification(
                                $this->announcement,
                                $recipientName,
                                'teacher'
                            ));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send announcement to teacher', [
                            'teacher_id' => $teacher->id,
                            'email' => $teacher->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });
    }

    /**
     * Send notifications to all guardians
     */
    private function sendToGuardians(): void
    {
        Guardian::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->chunk(100, function ($guardians) {
                foreach ($guardians as $guardian) {
                    try {
                        $recipientName = $guardian->first_name . ' ' . $guardian->last_name;
                        
                        Mail::to($guardian->email)
                            ->send(new AnnouncementNotification(
                                $this->announcement,
                                $recipientName,
                                'guardian'
                            ));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send announcement to guardian', [
                            'guardian_id' => $guardian->id,
                            'email' => $guardian->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });
    }
}
