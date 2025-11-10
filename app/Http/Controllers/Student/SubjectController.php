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
            $enrollments = SubjectEnrollment::with([
                    'academicYearStrandSubject.subject',
                    'academicYearStrandSubject.teacher',
                ])
                ->whereHas('studentEnrollment', function ($q) use ($studentId, $activeYear) {
                    $q->where('student_id', $studentId)
                      ->where('academic_year_id', $activeYear->id);
                })
                ->get();

            // Map each enrolled subject with associated grades
            $subjects = $enrollments->map(function ($se) {
                $ays = $se->academicYearStrandSubject;
                return [
                    'id' => $ays->id,
                    'subject_name' => $ays->subject?->name,
                    'subject_code' => $ays->subject?->code,
                    'teacher' => $ays->teacher?->last_name
                        ? ($ays->teacher->last_name . ', ' . $ays->teacher->first_name)
                        : null,
                    // Grades (nullable decimals)
                    'fq_grade' => $se->fq_grade,
                    'sq_grade' => $se->sq_grade,
                    'a_grade' => $se->a_grade,
                    'f_grade' => $se->f_grade,
                    'remarks' => $se->remarks,
                ];
            });
        }

        return view('student.subjects.index', [
            'activeYear' => $activeYear,
            'subjects' => $subjects,
        ]);
    }
}
