<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\SubjectEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) {
            abort(401);
        }

        // Active academic year
        $activeYear = AcademicYear::where('is_active', true)->first();

        $subjects = collect();
        if ($activeYear) {
            $assignments = AcademicYearStrandSubject::with([
                'subject',
                'strand',
            ])->where('academic_year_id', $activeYear->id)
              ->where('teacher_id', $user->user_pk_id)
              ->get();

            // Map to a view model with section and strand via adviser or strand-section
            $subjects = $assignments->map(function ($a) use ($activeYear) {
                $strand = $a->strand;

                // Try to find section via AcademicYearStrandSection where adviser matches this teacher
                $sectionName = null;
                $strandName = $strand?->name;

                // Prefer section where this teacher is adviser; else pick any active for the strand/year
                $preferred = AcademicYearStrandSection::with('section','adviserTeacher')
                    ->where('academic_year_id', $activeYear->id)
                    ->where('strand_id', $a->strand_id)
                    ->where('adviser_teacher_id', $a->teacher_id)
                    ->first();
                $sectionAssignment = $preferred ?: AcademicYearStrandSection::with('section','adviserTeacher')
                    ->where('academic_year_id', $activeYear->id)
                    ->where('strand_id', $a->strand_id)
                    ->where('is_active', true)
                    ->first();

                $adviserName = null;
                if ($sectionAssignment) {
                    $sectionName = optional($sectionAssignment->section)->name;
                    $adviser = $sectionAssignment->adviserTeacher;
                    if ($adviser && $adviser->last_name) {
                        $adviserName = $adviser->last_name . ', ' . $adviser->first_name;
                    }
                }

                // Per-subject student counts via SubjectEnrollment
                $enrollments = SubjectEnrollment::with(['studentEnrollment.student'])
                    ->where('academic_year_strand_subject_id', $a->id)
                    ->get();
                $students = $enrollments->map(function ($se) {
                    $st = optional($se->studentEnrollment)->student;
                    if (!$st) return null;
                    return [
                        'gender' => $st->gender,
                        'status' => $st->status,
                    ];
                })->filter();
                $counts = [
                    'total' => $students->count(),
                    'male' => $students->where('gender', 'male')->count(),
                    'female' => $students->where('gender', 'female')->count(),
                    'active' => $students->where('status', 'active')->count(),
                    'graduated' => $students->where('status', 'graduated')->count(),
                    'dropped' => $students->where('status', 'dropped')->count(),
                ];

                return [
                    'id' => $a->id,
                    'subject_code' => $a->subject?->code,
                    'subject_name' => $a->subject?->name,
                    'strand_name' => $strandName,
                    'section_name' => $sectionName,
                    'adviser_name' => $adviserName,
                    'counts' => $counts,
                ];
            });
        }

        return view('teacher.subjects.index', [
            'activeYear' => $activeYear,
            'subjects' => $subjects,
        ]);
    }
}
