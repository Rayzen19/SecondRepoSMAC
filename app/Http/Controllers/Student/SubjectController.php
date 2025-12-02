<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SubjectEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { abort(401); }
        
        // Get the actual student ID from the user's user_pk_id
        $studentId = $user->user_pk_id;
        if (!$studentId) {
            abort(403, 'Student profile not linked to this account.');
        }

        $activeYear = AcademicYear::where('is_active', true)->first();

        $subjects = collect();
        if ($activeYear) {
            // Get student enrollment with strand and section information
            $studentEnrollment = \App\Models\StudentEnrollment::with(['strand', 'academicYearStrandSection.section'])
                ->where('student_id', $studentId)
                ->where('academic_year_id', $activeYear->id)
                ->first();

            if ($studentEnrollment) {
                $strandId = $studentEnrollment->strand_id;
                $sectionAssignmentId = $studentEnrollment->academic_year_strand_section_id;
                $gradeLevel = $studentEnrollment->academicYearStrandSection?->section?->grade ?? null;

                // Extract numeric grade level (e.g., "G-11" -> "11")
                $numericGradeLevel = null;
                if ($gradeLevel) {
                    $numericGradeLevel = str_replace(['G-', 'Grade ', 'Grade-'], '', $gradeLevel);
                }

                // Get all subjects that should be available for this strand based on StrandSubject
                $strandSubjects = \App\Models\StrandSubject::with(['subject'])
                    ->where('strand_id', $strandId)
                    ->when($numericGradeLevel, function ($q) use ($numericGradeLevel) {
                        $q->where(function ($qq) use ($numericGradeLevel) {
                            $qq->where('grade_level', $numericGradeLevel)
                               ->orWhereNull('grade_level');
                        });
                    })
                    ->where('is_active', true)
                    ->get();

                // Map each subject and check if teacher is assigned in AcademicYearStrandSubject
                $subjects = $strandSubjects->map(function ($strandSubject) use ($activeYear, $strandId, $sectionAssignmentId, $studentEnrollment) {
                    $subject = $strandSubject->subject;
                    
                    // Check if this subject has been assigned to a teacher for this academic year
                    $academicYearStrandSubject = \App\Models\AcademicYearStrandSubject::with(['teacher'])
                        ->where('academic_year_id', $activeYear->id)
                        ->where('strand_id', $strandId)
                        ->where('subject_id', $subject->id)
                        ->where(function ($q) use ($sectionAssignmentId) {
                            $q->where('academic_year_strand_section_id', $sectionAssignmentId)
                              ->orWhereNull('academic_year_strand_section_id');
                        })
                        ->first();
                    
                    // Find subject enrollment if it exists (for grades)
                    $subjectEnrollment = null;
                    if ($academicYearStrandSubject) {
                        $subjectEnrollment = \App\Models\SubjectEnrollment::where('student_enrollment_id', $studentEnrollment->id)
                            ->where('academic_year_strand_subject_id', $academicYearStrandSubject->id)
                            ->first();
                    }
                    
                    $teacher = $academicYearStrandSubject?->teacher;
                    
                    return [
                        'id' => $academicYearStrandSubject?->id ?? $subject->id,
                        'subject_name' => $subject?->name,
                        'subject_code' => $subject?->code,
                        'subject_type' => $subject?->type,
                        'semester' => $subject?->semester,
                        'units' => $subject?->units,
                        'strand' => $studentEnrollment->strand?->code,
                        'teacher' => $teacher && $teacher->last_name
                            ? ($teacher->last_name . ', ' . $teacher->first_name)
                            : 'Not assigned yet',
                        // Grades (nullable decimals from SubjectEnrollment if exists)
                        'fq_grade' => $subjectEnrollment?->fq_grade,
                        'sq_grade' => $subjectEnrollment?->sq_grade,
                        'a_grade' => $subjectEnrollment?->a_grade,
                        'f_grade' => $subjectEnrollment?->f_grade,
                        'remarks' => $subjectEnrollment?->remarks,
                    ];
                });
            }
        }

        // Group subjects by type
        $coreSubjects = $subjects->where('subject_type', 'core');
        $appliedSubjects = $subjects->where('subject_type', 'applied');
        $specializedSubjects = $subjects->where('subject_type', 'specialized');

        // Get grade level from student enrollment
        $gradeLevel = null;
        if (isset($studentEnrollment) && $studentEnrollment) {
            $gradeLevel = $studentEnrollment->academicYearStrandSection?->section?->grade;
        }

        return view('student.subjects.index', [
            'activeYear' => $activeYear,
            'gradeLevel' => $gradeLevel,
            'coreSubjects' => $coreSubjects,
            'appliedSubjects' => $appliedSubjects,
            'specializedSubjects' => $specializedSubjects,
        ]);
    }
}
