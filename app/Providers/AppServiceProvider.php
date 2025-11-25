<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSubject;
use App\Models\Student;
use App\Observers\StudentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure the storage symlink exists so uploaded files are publicly accessible.
        try {
            $publicStorage = public_path('storage');
            if (!file_exists($publicStorage)) {
                // attempt to create the storage symlink (same as `php artisan storage:link`)
                Artisan::call('storage:link');
            }
        } catch (\Throwable $e) {
            // If the symlink creation fails (permission issues on Windows), do not break the app.
            // The README explains how to run `php artisan storage:link` manually.
        }
        // Share school year ended status and pre-enrollment status with student views
        View::composer('student.components.template', function ($view) {
            $schoolYearEnded = false;
            $preEnrollmentEnabled = false;
            
            // Check if student is authenticated
            if (Auth::guard('student')->check()) {
                $student = Auth::guard('student')->user();
                
                // Get the student's current enrollment (check both 'active' and 'enrolled' status)
                $enrollment = StudentEnrollment::where('student_id', $student->id)
                    ->whereIn('status', ['active', 'enrolled'])
                    ->latest()
                    ->first();
                
                if ($enrollment) {
                    // Check if any of the subjects in this enrollment have school_year_ended = true
                    $schoolYearEnded = AcademicYearStrandSubject::where('academic_year_id', $enrollment->academic_year_id)
                        ->where('academic_year_strand_section_id', $enrollment->academic_year_strand_section_id)
                        ->where('school_year_ended', true)
                        ->exists();
                    
                    // Check if pre-enrollment is enabled for the enrollment's academic year
                    $academicYear = \App\Models\AcademicYear::find($enrollment->academic_year_id);
                    if ($academicYear) {
                        $preEnrollmentEnabled = $academicYear->pre_enrollment_enabled;
                    }
                } else {
                    // If no enrollment, check the currently active academic year for pre-enrollment status
                    // Allow students without enrollment to still see the dashboard
                    $activeAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();
                    if ($activeAcademicYear) {
                        $preEnrollmentEnabled = $activeAcademicYear->pre_enrollment_enabled;
                    }
                }
            }
            
            $view->with('schoolYearEnded', $schoolYearEnded);
            $view->with('preEnrollmentEnabled', $preEnrollmentEnabled);
        });

        // Register student observer to auto-enroll new students
        try {
            Student::observe(StudentObserver::class);
        } catch (\Throwable $e) {
            // If observer registration fails, do not break the app boot.
        }
    }
}
