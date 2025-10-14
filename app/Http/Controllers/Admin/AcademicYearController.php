<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSubject;
use App\Models\SubjectEnrollment;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderByDesc('is_active')->orderByDesc('name')->paginate(15);
        return view('admin.academic_years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic_years.create');
    }

    public function store(Request $request)
    {
        // Normalize inputs
        $request->merge(['name' => trim((string) $request->input('name'))]);
        $semester = $request->input('semester');

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years')->where(function ($q) use ($semester) {
                    return $semester
                        ? $q->where('semester', $semester)
                        : $q->whereNull('semester');
                }),
            ],
            'semester' => 'nullable|in:1st,2nd',
            'academic_status' => 'required|in:pending,completed,ongoing enrollment,ongoing school year',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        // Ensure only one record has a given unique academic_status at a time
        $uniqueStatuses = ['ongoing enrollment', 'ongoing school year'];
        if (isset($data['academic_status']) && in_array($data['academic_status'], $uniqueStatuses, true)) {
            AcademicYear::where('academic_status', $data['academic_status'])
                ->update(['academic_status' => 'pending']);
        }

        if ($data['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $year = AcademicYear::create($data);

        return redirect()->route('admin.academic-years.show', $year)->with('success', 'Academic year created.');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load([
            'strandSubjects.strand',
            'strandSubjects.subject',
            'strandSubjects.teacher',
            'strandAdvisers.strand',
            'strandAdvisers.teacher',
            'strandSections.strand',
            'strandSections.section',
            'strandSections.adviserTeacher',
        ]);
        // Enrollments for this AY
        $enrollments = StudentEnrollment::with(['student', 'strand', 'academicYearStrandSection.section'])
            ->where('academic_year_id', $academicYear->id)
            ->orderByDesc('id')
            ->get();

        return view('admin.academic_years.show', [
            'year' => $academicYear,
            'enrollments' => $enrollments,
        ]);
    }

    public function syncSubjectEnrollments(AcademicYear $academicYear)
    {
        // For each student enrollment in this AY, ensure SubjectEnrollment rows exist for each AYS Subject of the same strand
        // Query directly to avoid issues if the relation isn't preloaded
        $subjectsByStrand = AcademicYearStrandSubject::where('academic_year_id', $academicYear->id)
            ->select('id', 'strand_id')
            ->get()
            ->groupBy('strand_id');

        $studentEnrollments = StudentEnrollment::where('academic_year_id', $academicYear->id)
            ->select('id', 'strand_id')
            ->get();
        $created = 0; $existing = 0;

        foreach ($studentEnrollments as $enr) {
            $subjects = $subjectsByStrand->get($enr->strand_id) ?? collect();
            foreach ($subjects as $sub) {
                $se = SubjectEnrollment::firstOrCreate([
                    'student_enrollment_id' => $enr->id,
                    'academic_year_strand_subject_id' => $sub->id,
                ]);
                if ($se->wasRecentlyCreated) {
                    $created++;
                } else {
                    $existing++;
                }
            }
        }

        if ($subjectsByStrand->isEmpty()) {
            return back()->with('success', "No strand subjects configured for this academic year. Nothing to sync.");
        }

        return back()->with('success', "Subject enrollments synced. Created: {$created}, existing kept: {$existing}.");
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.edit', ['year' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        // Normalize inputs
        $request->merge(['name' => trim((string) $request->input('name'))]);
        $semester = $request->input('semester');

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years')
                    ->ignore($academicYear->id)
                    ->where(function ($q) use ($semester) {
                        return $semester
                            ? $q->where('semester', $semester)
                            : $q->whereNull('semester');
                    }),
            ],
            'semester' => 'nullable|in:1st,2nd',
            'academic_status' => 'required|in:pending,completed,ongoing enrollment,ongoing school year',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        // Ensure only one record has a given unique academic_status at a time
        $uniqueStatuses = ['ongoing enrollment', 'ongoing school year'];
        if (isset($data['academic_status']) && in_array($data['academic_status'], $uniqueStatuses, true)) {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->where('academic_status', $data['academic_status'])
                ->update(['academic_status' => 'pending']);
        }

        if ($data['is_active']) {
            AcademicYear::where('id', '!=', $academicYear->id)->where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('admin.academic-years.show', $academicYear)->with('success', 'Academic year updated.');
    }
}
