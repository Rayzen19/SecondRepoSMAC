<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { abort(401); }

        $enrollments = StudentEnrollment::with(['academicYear', 'strand', 'academicYearStrandSection.section'])
            ->where('student_id', $user->id)
            ->orderByDesc('academic_year_id')
            ->get();

        $rows = $enrollments->map(function ($e) {
            return [
                'year' => $e->academicYear?->name,
                'display_name' => $e->academicYear?->display_name,
                'semester' => $e->academicYear?->semester,
                'status' => $e->status,
                'strand' => $e->strand?->name,
                'section' => $e->academicYearStrandSection?->section?->name,
            ];
        });

        return view('student.academic_years.index', [
            'rows' => $rows,
        ]);
    }
}
