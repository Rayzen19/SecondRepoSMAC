<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use Illuminate\Console\Command;

class EmailStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display statistics about users with email addresses for notification purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Email Notification Statistics');
        $this->newLine();

        // Students
        $totalStudents = Student::where('status', 'active')->count();
        $studentsWithEmail = Student::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();
        
        $this->line('📚 <fg=cyan>STUDENTS</>');
        $this->line("   Total Active: {$totalStudents}");
        $this->line("   With Email: {$studentsWithEmail}");
        $this->line("   Without Email: " . ($totalStudents - $studentsWithEmail));
        if ($totalStudents > 0) {
            $percentage = round(($studentsWithEmail / $totalStudents) * 100, 2);
            $this->line("   Coverage: {$percentage}%");
        }
        $this->newLine();

        // Teachers
        $totalTeachers = Teacher::where('status', 'active')->count();
        $teachersWithEmail = Teacher::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();
        
        $this->line('👨‍🏫 <fg=yellow>TEACHERS</>');
        $this->line("   Total Active: {$totalTeachers}");
        $this->line("   With Email: {$teachersWithEmail}");
        $this->line("   Without Email: " . ($totalTeachers - $teachersWithEmail));
        if ($totalTeachers > 0) {
            $percentage = round(($teachersWithEmail / $totalTeachers) * 100, 2);
            $this->line("   Coverage: {$percentage}%");
        }
        $this->newLine();

        // Guardians
        $totalGuardians = Guardian::where('status', 'active')->count();
        $guardiansWithEmail = Guardian::where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();
        
        $this->line('👪 <fg=green>GUARDIANS</>');
        $this->line("   Total Active: {$totalGuardians}");
        $this->line("   With Email: {$guardiansWithEmail}");
        $this->line("   Without Email: " . ($totalGuardians - $guardiansWithEmail));
        if ($totalGuardians > 0) {
            $percentage = round(($guardiansWithEmail / $totalGuardians) * 100, 2);
            $this->line("   Coverage: {$percentage}%");
        }
        $this->newLine();

        // Total
        $totalUsers = $totalStudents + $totalTeachers + $totalGuardians;
        $totalWithEmail = $studentsWithEmail + $teachersWithEmail + $guardiansWithEmail;
        
        $this->line('📊 <fg=magenta>TOTAL</>');
        $this->line("   Total Active Users: {$totalUsers}");
        $this->line("   Users With Email: {$totalWithEmail}");
        $this->line("   Users Without Email: " . ($totalUsers - $totalWithEmail));
        if ($totalUsers > 0) {
            $percentage = round(($totalWithEmail / $totalUsers) * 100, 2);
            $this->line("   Overall Coverage: {$percentage}%");
        }
        $this->newLine();

        $this->info('When an announcement is created, emails will be sent to all ' . $totalWithEmail . ' users with valid email addresses.');

        return 0;
    }
}
