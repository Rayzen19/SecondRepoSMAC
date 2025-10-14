<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandAdviser;
use App\Models\AcademicYearStrandSubject;
use App\Models\Strand;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AcademicYearStrandSubjectController extends Controller
{
    public function create(Request $request)
    {
        $academicYear = AcademicYear::findOrFail($request->query('academic_year'));
        $strand = Strand::findOrFail($request->query('strand'));
        $academicYearStrandAdviser = AcademicYearStrandAdviser::findOrFail($request->query('academic_year_strand_adviser_id'));

        if (!$academicYearStrandAdviser || $academicYearStrandAdviser->academic_year_id !== $academicYear->id || $academicYearStrandAdviser->strand_id !== $strand->id) {
            abort(404, 'Invalid academic year strand adviser reference.');
        }

        $strands = Strand::orderBy('code')->get();
        $subjects = $strand->strandSubjects()
            ->with('subject')
            ->where('semestral_period', $academicYear->semester)
            ->get();

        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.academic_year_strand_subjects.create', compact('academicYear', 'strands', 'subjects', 'teachers', 'strand', 'academicYearStrandAdviser'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'strand_id' => ['required', 'exists:strands,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'written_works_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'written_works_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'over_all_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'academic_year_strand_adviser_id' => ['required', 'exists:academic_year_strand_advisers,id'],
        ]);

        // Ensure 100% total for main grading percentages
        $total = $data['written_works_percentage'] + $data['performance_tasks_percentage'] + $data['quarterly_assessment_percentage'];
        if (abs($total - 100) > 0.0001) {
            return back()->withInput()->withErrors(['written_works_percentage' => 'Written Works + Performance Tasks + Quarterly Assessment must total 100%.']);
        }

        // Ensure uniqueness within AY+Strand+Subject+Teacher
        $exists = AcademicYearStrandSubject::where('academic_year_id', $data['academic_year_id'])
            ->where('strand_id', $data['strand_id'])
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['subject_id' => 'This subject assignment already exists for the selected academic year, and strand.']);
        }

        AcademicYearStrandSubject::create($data);

        return redirect()->route('admin.academic-year-strand-advisers.show', $data['academic_year_strand_adviser_id'])->with('success', 'Subject assigned to strand for this academic year.');
    }

    public function edit(AcademicYearStrandSubject $assignment)
    {
        $assignment->load(['academicYear', 'strand', 'subject', 'teacher']);
        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.academic_year_strand_subjects.edit', compact('assignment', 'teachers'));
    }

    public function update(Request $request, AcademicYearStrandSubject $assignment)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'written_works_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'written_works_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'performance_tasks_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quarterly_assessment_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'over_all_based_grade_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $total = $data['written_works_percentage'] + $data['performance_tasks_percentage'] + $data['quarterly_assessment_percentage'];
        if (abs($total - 100) > 0.0001) {
            return back()->withInput()->withErrors(['written_works_percentage' => 'Written Works + Performance Tasks + Quarterly Assessment must total 100%.']);
        }

        $assignment->update($data);

        return redirect()->route('admin.academic-year-strand-advisers.show', $assignment->academic_year_strand_adviser_id)->with('success', 'Subject assigned to strand for this academic year.');
    }
}
