<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectRecord;
use Illuminate\Http\Request;

class ClassRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = SubjectRecord::query()
            ->with([
                'subjectEnrollment.studentEnrollment.student',
                'subjectEnrollment.studentEnrollment.academicYear',
                'subjectEnrollment.academicYearStrandSubject.subject',
                'subjectEnrollment.academicYearStrandSubject.teacher',
            ])
            ->orderByDesc('id');

        if ($request->filled('academic_year_id')) {
            $query->whereHas('subjectEnrollment.studentEnrollment', function ($q) use ($request) {
                $q->where('academic_year_id', $request->integer('academic_year_id'));
            });
        }
        if ($request->filled('teacher_id')) {
            $query->whereHas('subjectEnrollment.academicYearStrandSubject', function ($q) use ($request) {
                $q->where('teacher_id', $request->integer('teacher_id'));
            });
        }

        $records = $query->paginate(20);

        return view('admin.class_records.index', compact('records'));
    }
}
