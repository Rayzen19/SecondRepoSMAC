<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectRecordResultController extends Controller
{
    public function index(Request $request): View
    {
        $query = SubjectRecordResult::query()
            ->with(['student', 'subjectRecord.subjectEnrollment.academicYearStrandSubject.subject', 'subjectRecord.subjectEnrollment.studentEnrollment.student'])
            ->orderByDesc('id');

        if ($request->filled('subject_record_id')) {
            $query->where('subject_record_id', $request->integer('subject_record_id'));
        }

        $results = $query->paginate(20);

        return view('admin.subject_record_results.index', compact('results'));
    }

    public function create(Request $request): View
    {
        $subjectRecord = null;
        if ($request->filled('subject_record_id')) {
            $subjectRecord = SubjectRecord::with(['subjectEnrollment.studentEnrollment.student'])->find($request->integer('subject_record_id'));
        }
        $students = Student::orderBy('last_name')->get();
        return view('admin.subject_record_results.create', compact('students', 'subjectRecord'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_record_id' => ['required', 'exists:subject_records,id'],
            'student_id' => ['required', 'exists:students,id'],
            'remarks' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'date_submitted' => ['nullable', 'date'],
            'raw_score' => ['nullable', 'numeric', 'min:0'],
            'base_score' => ['nullable', 'numeric', 'min:0'],
            'final_score' => ['nullable', 'numeric', 'min:0'],
        ]);

        $result = SubjectRecordResult::create($data);

        return redirect()->route('admin.subject-record-results.show', $result)
            ->with('success', 'Class record entry created.');
    }

    public function show(SubjectRecordResult $subjectRecordResult): View
    {
        $subjectRecordResult->load(['student', 'subjectRecord.subjectEnrollment.academicYearStrandSubject.subject', 'subjectRecord.subjectEnrollment.studentEnrollment.student']);
        return view('admin.subject_record_results.show', [
            'result' => $subjectRecordResult,
        ]);
    }

    public function edit(SubjectRecordResult $subjectRecordResult): View
    {
        $subjectRecordResult->load(['student', 'subjectRecord']);
        $students = Student::orderBy('last_name')->get();
        return view('admin.subject_record_results.edit', [
            'result' => $subjectRecordResult,
            'students' => $students,
            'subjectRecord' => $subjectRecordResult->subjectRecord,
        ]);
    }

    public function update(Request $request, SubjectRecordResult $subjectRecordResult): RedirectResponse
    {
        $data = $request->validate([
            'subject_record_id' => ['required', 'exists:subject_records,id'],
            'student_id' => ['required', 'exists:students,id'],
            'remarks' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'date_submitted' => ['nullable', 'date'],
            'raw_score' => ['nullable', 'numeric', 'min:0'],
            'base_score' => ['nullable', 'numeric', 'min:0'],
            'final_score' => ['nullable', 'numeric', 'min:0'],
        ]);

        $subjectRecordResult->update($data);

        return redirect()->route('admin.subject-record-results.show', $subjectRecordResult)
            ->with('success', 'Class record entry updated.');
    }

    public function destroy(SubjectRecordResult $subjectRecordResult): RedirectResponse
    {
        $subjectRecordResult->delete();
        return redirect()->route('admin.subject-record-results.index')
            ->with('success', 'Class record entry deleted.');
    }
}
