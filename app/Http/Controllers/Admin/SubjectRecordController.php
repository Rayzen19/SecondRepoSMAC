<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSubject;
use App\Models\SubjectRecord;
use App\Models\SubjectEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectRecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = SubjectRecord::query()
            ->with([
                'assignment.subject',
                'assignment.teacher',
                'assignment.academicYear',
            ])
            ->orderByDesc('id');

        // Optional: filter by academic year via assignment relation
        if ($request->filled('academic_year_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('academic_year_id', $request->integer('academic_year_id'));
            });
        }

        $records = $query->paginate(20);
        $academicYears = AcademicYear::orderByDesc('id')->get();

        return view('admin.subject_records.index', compact('records', 'academicYears'));
    }

    public function create(): View
    {
        $assignments = AcademicYearStrandSubject::with(['subject','teacher','academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderByDesc('id')->limit(50)->get();

        return view('admin.subject_records.create', compact('assignments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'academic_year_strand_subject_id' => ['required', 'exists:academic_year_strand_subjects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:written work,performance task,quarterly assessment'],
            'quarter' => ['nullable', 'in:1st,2nd'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'date_given' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $record = SubjectRecord::create($data);
        return redirect()->route('admin.subject-records.show', $record)->with('success', 'Class record created.');
    }

    public function show(SubjectRecord $subjectRecord): View
    {
        $subjectRecord->load([
            'assignment.subject',
            'assignment.teacher',
            'assignment.academicYear',
            'results.student',
        ]);
        return view('admin.subject_records.show', ['record' => $subjectRecord]);
    }

    public function edit(SubjectRecord $subjectRecord): View
    {
        $assignments = AcademicYearStrandSubject::with(['subject'])
            ->orderByDesc('id')->limit(50)->get();

        return view('admin.subject_records.edit', [
            'record' => $subjectRecord,
            'assignments' => $assignments,
        ]);
    }

    public function update(Request $request, SubjectRecord $subjectRecord): RedirectResponse
    {
        $data = $request->validate([
            'academic_year_strand_subject_id' => ['required', 'exists:academic_year_strand_subjects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:written work,performance task,quarterly assessment'],
            'quarter' => ['nullable', 'in:1st,2nd'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'date_given' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $subjectRecord->update($data);
        return redirect()->route('admin.subject-records.show', $subjectRecord)->with('success', 'Class record updated.');
    }

    public function destroy(SubjectRecord $subjectRecord): RedirectResponse
    {
        $subjectRecord->delete();
        return redirect()->route('admin.subject-records.index')->with('success', 'Class record deleted.');
    }

    public function export(SubjectRecord $subjectRecord)
    {
        $subjectRecord->load(['results.student']);
        $filename = 'class_record_' . $subjectRecord->id . '_entries.csv';

        return response()->streamDownload(function () use ($subjectRecord) {
            $out = fopen('php://output', 'w');
            // Header row
            fputcsv($out, ['#', 'Student Number', 'Student Name', 'Gender', 'Raw Score', 'Base Score', 'Final Score', 'Date Submitted', 'Remarks', 'Description']);

            $i = 0;
            foreach ($subjectRecord->results as $res) {
                $i++;
                $student = $res->student;
                fputcsv($out, [
                    $i,
                    $student?->student_number,
                    $student?->name,
                    $student?->gender,
                    $res->raw_score,
                    $res->base_score,
                    $res->final_score,
                    optional($res->date_submitted)->format('Y-m-d'),
                    $res->remarks,
                    $res->description,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
