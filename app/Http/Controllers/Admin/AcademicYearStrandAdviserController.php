<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandAdviser;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\Strand;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearStrandAdviserController extends Controller
{
    public function create(Request $request)
    {
        $academicYear = AcademicYear::findOrFail($request->query('academic_year'));
        $strands = Strand::orderBy('code')->get();
        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.academic_year_strand_advisers.create', compact('academicYear', 'strands', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'strand_id' => ['required', 'exists:strands,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
        ]);

        // Ensure unique adviser per AY+Strand
        $exists = AcademicYearStrandAdviser::where('academic_year_id', $data['academic_year_id'])
            ->where('strand_id', $data['strand_id'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['strand_id' => 'This strand already has an adviser for the selected academic year.']);
        }

        AcademicYearStrandAdviser::create($data);

        return redirect()->route('admin.academic-years.show', $data['academic_year_id'])->with('success', 'Adviser assigned successfully.');
    }

    public function edit(AcademicYearStrandAdviser $adviser)
    {
        $adviser->load(['academicYear', 'strand', 'teacher']);
        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.academic_year_strand_advisers.edit', compact('adviser', 'teachers'));
    }

    public function update(Request $request, AcademicYearStrandAdviser $adviser)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
        ]);

        $adviser->update($data);

        return redirect()->route('admin.academic-years.show', $adviser->academic_year_id)->with('success', 'Adviser updated successfully.');
    }

    public function show(AcademicYearStrandAdviser $adviser)
    {
        $adviser->load(['academicYear', 'strand', 'teacher']);

        $assignments = AcademicYearStrandSubject::with(['subject', 'teacher'])
            ->where('academic_year_id', $adviser->academic_year_id)
            ->where('strand_id', $adviser->strand_id)
            ->orderBy('id', 'desc')
            ->get();

        $sections = AcademicYearStrandSection::where('academic_year_id', $adviser->academic_year_id)
            ->where('strand_id', $adviser->strand_id)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.academic_year_strand_advisers.show', compact('adviser', 'assignments', 'sections'));
    }
}
