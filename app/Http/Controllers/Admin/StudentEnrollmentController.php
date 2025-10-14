<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\Strand;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentEnrollment::with([
            'student',
            'strand',
            'academicYear',
            'academicYearStrandSection.section',
            'academicYearStrandSection.strand'
        ])->latest();

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->integer('academic_year_id'));
        }
        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->integer('strand_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $enrollments = $query->paginate(15)->withQueryString();

        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('id')->get();
        $strands = Strand::orderBy('name')->get();

        return view('admin.student_enrollments.index', compact('enrollments', 'academicYears', 'strands'));
    }

    public function create(Request $request)
    {
        $students = Student::orderBy('last_name')->get();
        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('id')->get();
        $strands = Strand::orderBy('name')->get();
        $strandSectionsQuery = AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
            ->orderByDesc('id');
        if ($request->filled('academic_year_id')) {
            $strandSectionsQuery->where('academic_year_id', $request->integer('academic_year_id'));
        }
        $strandSections = $strandSectionsQuery->get();

        return view('admin.student_enrollments.create', compact('students', 'academicYears', 'strands', 'strandSections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'strand_id' => ['nullable', 'exists:strands,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'academic_year_strand_section_id' => ['required', 'exists:academic_year_strand_sections,id'],
            'status' => ['required', Rule::in(['enrolled', 'dropped', 'completed'])],
        ]);

        // Generate registration number: YEAR-XXXX scoped by academic_year_id
        $year = AcademicYear::findOrFail($data['academic_year_id']);
        $prefix = $year->name; // assumes name like 2025-2026; use first part as YEAR if needed

        $attempts = 0;
        do {
            $count = StudentEnrollment::where('academic_year_id', $year->id)->count() + 1;
            $reg = sprintf('%s-%05d', substr($prefix, 0, 5), $count);
            $data['registration_number'] = $reg;
            try {
                $enrollment = StudentEnrollment::create($data);
                $conflict = false;
            } catch (\Illuminate\Database\QueryException $ex) {
                // retry on unique constraint violation
                if (str_contains(strtolower($ex->getMessage()), 'unique') && $attempts < 3) {
                    $conflict = true;
                    $attempts++;
                    usleep(100000); // 100ms
                    continue;
                }
                throw $ex;
            }
        } while ($conflict);

        return redirect()
            ->route('admin.student-enrollments.show', $enrollment)
            ->with('success', 'Student enrollment created.');
    }

    public function show(StudentEnrollment $studentEnrollment)
    {
        $studentEnrollment->load([
            'student',
            'strand',
            'academicYear',
            'academicYearStrandSection.section',
            'academicYearStrandSection.strand',
            'subjectEnrollments.academicYearStrandSubject.subject',
            'subjectEnrollments.academicYearStrandSubject.teacher',
        ]);
        return view('admin.student_enrollments.show', compact('studentEnrollment'));
    }

    public function edit(StudentEnrollment $studentEnrollment)
    {
        $students = Student::orderBy('last_name')->get();
        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('id')->get();
        $strands = Strand::orderBy('name')->get();
        $strandSections = AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
            ->orderByDesc('id')->get();

        return view('admin.student_enrollments.edit', compact('studentEnrollment', 'students', 'academicYears', 'strands', 'strandSections'));
    }

    public function update(Request $request, StudentEnrollment $studentEnrollment): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'strand_id' => ['nullable', 'exists:strands,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'academic_year_strand_section_id' => ['required', 'exists:academic_year_strand_sections,id'],
            'status' => ['required', Rule::in(['enrolled', 'dropped', 'completed'])],
        ]);
    // Do not allow editing registration_number; keep existing
    $studentEnrollment->update($data);

        return redirect()
            ->route('admin.student-enrollments.show', $studentEnrollment)
            ->with('success', 'Student enrollment updated.');
    }

    public function destroy(StudentEnrollment $studentEnrollment): RedirectResponse
    {
        $studentEnrollment->delete();

        return redirect()
            ->route('admin.student-enrollments.index')
            ->with('success', 'Student enrollment removed.');
    }

    // Dependent dropdown endpoint to fetch AY-Strand-Section options
    public function sectionsOptions(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'strand_id' => ['nullable', 'exists:strands,id'],
        ]);

        $query = AcademicYearStrandSection::with(['section', 'strand'])
            ->where('academic_year_id', (int) $validated['academic_year_id']);

        if (!empty($validated['strand_id'])) {
            $query->where('strand_id', (int) $validated['strand_id']);
        }

        $options = $query->orderBy('strand_id')->orderBy('section_id')->get()
            ->map(function ($assn) {
                $label = trim(($assn->strand?->code ? $assn->strand?->code . ' — ' : '') . ($assn->section?->grade . ' ' . $assn->section?->name));
                return [
                    'id' => $assn->id,
                    'text' => $label,
                ];
            });

        return response()->json(['data' => $options]);
    }

    // Dependent dropdown endpoint to fetch Strand options for a given AY
    public function strandsOptions(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $strandIds = AcademicYearStrandSection::where('academic_year_id', (int) $validated['academic_year_id'])
            ->whereNotNull('strand_id')
            ->distinct()
            ->pluck('strand_id');

        $strands = Strand::whereIn('id', $strandIds)->orderBy('name')->get()
            ->map(fn($s) => ['id' => $s->id, 'text' => ($s->code ? $s->code . ' — ' : '') . $s->name]);

        return response()->json(['data' => $strands]);
    }
}
