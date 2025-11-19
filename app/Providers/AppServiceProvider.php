<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSubject;

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
    }
}
