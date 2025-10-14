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

        $activeYear = AcademicYear::where('is_active', true)->first();

        $subjects = collect();
        if ($activeYear) {
            $enrollments = SubjectEnrollment::with([
                    'academicYearStrandSubject.subject',
                    'academicYearStrandSubject.teacher',
                ])
                ->whereHas('studentEnrollment', function ($q) use ($user, $activeYear) {
                    $q->where('student_id', $user->id)
                      ->where('academic_year_id', $activeYear->id);
                })
                ->get();

            $subjects = $enrollments->map(function ($se) {
                $ays = $se->academicYearStrandSubject;
                return [
                    'id' => $ays->id,
                    'subject_name' => $ays->subject?->name,
                    'subject_code' => $ays->subject?->code,
                    'teacher' => $ays->teacher?->last_name
                        ? ($ays->teacher->last_name . ', ' . $ays->teacher->first_name)
                        : null,
                ];
            });
        }

        return view('student.subjects.index', [
            'activeYear' => $activeYear,
            'subjects' => $subjects,
        ]);
    }
}
