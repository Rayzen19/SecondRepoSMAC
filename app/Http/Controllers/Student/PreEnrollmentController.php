<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PreEnrollment;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Section;
use App\Models\AcademicYearStrandSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreEnrollmentController extends Controller
{
    /**
     * Show the pre-enrollment form
     */
    public function index()
    {
        $authenticatedUser = Auth::guard('student')->user();
        
        \Log::info('Pre-enrollment accessed', [
            'user_id' => $authenticatedUser?->id,
            'user_email' => $authenticatedUser?->email
        ]);
        
        if (!$authenticatedUser) {
            \Log::warning('Pre-enrollment: No user authenticated');
            return redirect()->route('student.auth.loginForm')
                ->with('error', 'Please login to access pre-enrollment.');
        }
        
        // Find the actual Student record by email
        $student = \App\Models\Student::where('email', $authenticatedUser->email)->first();
        
        if (!$student) {
            \Log::error('Pre-enrollment: Student record not found', [
                'user_id' => $authenticatedUser->id,
                'email' => $authenticatedUser->email
            ]);
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found. Please contact the administrator.');
        }
        
        \Log::info('Pre-enrollment: Student record found', [
            'student_id' => $student->id,
            'student_number' => $student->student_number
        ]);
        
        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            \Log::warning('Pre-enrollment: No active academic year');
            return redirect()->route('student.dashboard')
                ->with('error', 'No active academic year found.');
        }

        \Log::info('Active academic year found', [
            'year' => $currentAcademicYear->name,
            'pre_enrollment_enabled' => $currentAcademicYear->pre_enrollment_enabled
        ]);

        // Check if pre-enrollment is enabled
        if (!$currentAcademicYear->pre_enrollment_enabled) {
            \Log::warning('Pre-enrollment: Feature not enabled');
            return redirect()->route('student.dashboard')
                ->with('error', 'Pre-enrollment is not currently available.');
        }

        // Get student's current enrollment
        $currentEnrollment = StudentEnrollment::with(['strand', 'academicYearStrandSection.section'])
            ->where('student_id', $student->id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();

        if (!$currentEnrollment) {
            \Log::warning('Pre-enrollment: Student has no current enrollment', [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'academic_year_id' => $currentAcademicYear->id,
                'academic_year' => $currentAcademicYear->name
            ]);
            
            // Check if student has any enrollments at all
            $anyEnrollment = StudentEnrollment::where('student_id', $student->id)->count();
            
            // Create a minimal enrollment object for the view
            $currentEnrollment = (object)[
                'student_id' => $student->id,
                'academic_year_id' => $currentAcademicYear->id,
                'strand' => null,
                'academicYearStrandSection' => null,
                'status' => 'not_enrolled'
            ];
            
            $errorMessage = $anyEnrollment === 0 
                ? 'You are not currently enrolled. Please contact the administrator to complete your enrollment before submitting a pre-enrollment request.'
                : 'You must be enrolled in the current academic year (' . $currentAcademicYear->name . ') to pre-enroll. Please contact the administrator if you believe this is an error.';
            
            // Get strands for the form
            $strands = \App\Models\Strand::where('is_active', true)->orderBy('name')->get();
            
            // Set existingPreEnrollment to null since student is not properly enrolled
            $existingPreEnrollment = null;
            
            // Show the pre-enrollment form with an informational message instead of redirecting
            return view('student.pre_enrollment.index', compact(
                'currentEnrollment',
                'currentAcademicYear',
                'strands',
                'existingPreEnrollment'
            ))->with('info', $errorMessage);
        }

        \Log::info('Pre-enrollment: All checks passed, showing form');

        // Check if student has already submitted pre-enrollment
        $existingPreEnrollment = PreEnrollment::where('student_id', $student->id)
            ->where('current_academic_year_id', $currentAcademicYear->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        // Get all active strands
        $strands = Strand::where('is_active', true)->orderBy('name')->get();

        // For testing - use simple view first
        // return view('student.pre_enrollment.index_simple', compact(
        //     'currentEnrollment',
        //     'currentAcademicYear'
        // ));

        return view('student.pre_enrollment.index', compact(
            'currentEnrollment',
            'currentAcademicYear',
            'strands',
            'existingPreEnrollment'
        ));
    }

    /**
     * Get available sections for a grade level and strand
     */
    public function getSections(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => ['required', 'string'],
            'strand_id' => ['required', 'exists:strands,id'],
        ]);

        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            return response()->json(['sections' => []]);
        }

        // Maximum students per section
        $maxStudentsPerSection = 30;

        // Get sections that match the grade level and strand
        // For Grade 12, we look at existing sections
        $sections = Section::where('grade', $validated['grade_level'])
            ->where('strand_id', $validated['strand_id'])
            ->orderBy('name')
            ->get()
            ->map(function ($section) use ($currentAcademicYear, $maxStudentsPerSection) {
                // Count current enrollments in this section for the current academic year
                $currentEnrollments = StudentEnrollment::whereHas('academicYearStrandSection', function($query) use ($section, $currentAcademicYear) {
                    $query->where('section_id', $section->id)
                          ->where('academic_year_id', $currentAcademicYear->id);
                })->count();

                // Count pending pre-enrollments for this section
                $preEnrollments = PreEnrollment::where('section_id', $section->id)
                    ->where('current_academic_year_id', $currentAcademicYear->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->count();

                $totalCount = $currentEnrollments + $preEnrollments;
                $availableSpots = max(0, $maxStudentsPerSection - $totalCount);

                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'display' => $section->grade . ' - ' . $section->name . ' (' . $availableSpots . '/' . $maxStudentsPerSection . ' spots available)',
                    'total_count' => $totalCount,
                    'available_spots' => $availableSpots,
                    'is_full' => $totalCount >= $maxStudentsPerSection
                ];
            })
            ->filter(function ($section) {
                // Only show sections that are not full
                return !$section['is_full'];
            })
            ->values();

        return response()->json(['sections' => $sections]);
    }

    /**
     * Store the pre-enrollment submission
     */
    public function store(Request $request)
    {
        $authenticatedUser = Auth::guard('student')->user();
        
        if (!$authenticatedUser) {
            return redirect()->route('student.auth.loginForm')
                ->with('error', 'Please login to access pre-enrollment.');
        }
        
        // Find the actual Student record by email
        $student = \App\Models\Student::where('email', $authenticatedUser->email)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found. Please contact the administrator.');
        }
        
        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear || !$currentAcademicYear->pre_enrollment_enabled) {
            return redirect()->route('student.pre-enrollment.index')
                ->with('error', 'Pre-enrollment is not currently available.');
        }

        // Check if student has already submitted
        $existingPreEnrollment = PreEnrollment::where('student_id', $student->id)
            ->where('current_academic_year_id', $currentAcademicYear->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingPreEnrollment) {
            return redirect()->route('student.pre-enrollment.index')
                ->with('error', 'You have already submitted your pre-enrollment.');
        }

        $validated = $request->validate([
            'grade_level' => ['required', 'string'],
            'strand_id' => ['required', 'exists:strands,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
        ]);

        // If a section is selected, check if it's not full (maximum 30 students)
        if (!empty($validated['section_id'])) {
            $section = Section::find($validated['section_id']);
            
            if ($section) {
                $maxStudentsPerSection = 30;
                
                // Count current enrollments in this section
                $currentEnrollments = StudentEnrollment::whereHas('academicYearStrandSection', function($query) use ($section, $currentAcademicYear) {
                    $query->where('section_id', $section->id)
                          ->where('academic_year_id', $currentAcademicYear->id);
                })->count();

                // Count pending pre-enrollments for this section
                $preEnrollments = PreEnrollment::where('section_id', $section->id)
                    ->where('current_academic_year_id', $currentAcademicYear->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->count();

                $totalCount = $currentEnrollments + $preEnrollments;

                if ($totalCount >= $maxStudentsPerSection) {
                    return redirect()->route('student.pre-enrollment.index')
                        ->with('error', 'The selected section is already full (30/30 students). Please select another section or choose no preference.');
                }
            }
        }

        try {
            DB::beginTransaction();

            $preEnrollment = PreEnrollment::create([
                'student_id' => $student->id,
                'current_academic_year_id' => $currentAcademicYear->id,
                'target_academic_year_id' => null, // Will be set when next academic year is created
                'strand_id' => $validated['strand_id'],
                'section_id' => $validated['section_id'] ?? null,
                'grade_level' => $validated['grade_level'],
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('student.pre-enrollment.index')
                ->with('success', 'Your pre-enrollment has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('student.pre-enrollment.index')
                ->with('error', 'Failed to submit pre-enrollment. Please try again.');
        }
    }

    /**
     * Cancel/withdraw pre-enrollment
     */
    public function cancel($id)
    {
        $authenticatedUser = Auth::guard('student')->user();
        
        if (!$authenticatedUser) {
            return redirect()->route('student.auth.loginForm')
                ->with('error', 'Please login to access pre-enrollment.');
        }
        
        // Find the actual Student record by email
        $student = \App\Models\Student::where('email', $authenticatedUser->email)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found. Please contact the administrator.');
        }
        
        $preEnrollment = PreEnrollment::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $preEnrollment->delete();

        return redirect()->route('student.pre-enrollment.index')
            ->with('success', 'Your pre-enrollment has been cancelled.');
    }
}
