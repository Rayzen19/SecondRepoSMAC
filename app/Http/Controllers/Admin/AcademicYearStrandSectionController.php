<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Strand;
use Illuminate\Http\Request;

class AcademicYearStrandSectionController extends Controller
{
    public function show(AcademicYearStrandSection $assignment)
    {
        $assignment->load(['academicYear', 'strand', 'section', 'adviserTeacher']);
        return view('admin.academic_year_strand_sections.show', compact('assignment'));
    }

    public function create(Request $request)
    {
        $academicYear = AcademicYear::findOrFail($request->query('academic_year'));
        
        $strands = Strand::with(['academicYearStrandAdvisers' => function ($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        }])->whereHas('academicYearStrandAdvisers', function ($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->orderBy('code')->get();

        $sections = Section::orderBy('grade')->orderBy('name')->get();
    $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
    return view('admin.academic_year_strand_sections.create', compact('academicYear', 'strands', 'sections', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'strand_id' => ['required', 'exists:strands,id'],
            'section_id' => ['required', 'array', 'min:1'],
            'section_id.*' => ['required', 'exists:sections,id'],
            'adviser_teacher_id' => ['nullable', 'exists:teachers,id'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $errors = [];
        foreach ($data['section_id'] as $sectionId) {
            $exists = AcademicYearStrandSection::where('academic_year_id', $data['academic_year_id'])
                ->where('strand_id', $data['strand_id'])
                ->where('section_id', $sectionId)
                ->exists();
            if ($exists) {
                $errors[] = $sectionId;
            }
        }
        if (!empty($errors)) {
            $duplicateSections = Section::whereIn('id', $errors)->orderBy('grade')->orderBy('name')->get();
            $names = $duplicateSections->map(function ($s) {
                return "Grade {$s->grade} - {$s->name}";
            })->toArray();
            $message = 'The following selected sections are already assigned for the selected academic year and strand: ' . implode(', ', $names);
            return back()->withInput()->withErrors(['section_id' => $message]);
        }

    foreach ($data['section_id'] as $sectionId) {
            AcademicYearStrandSection::create([
                'academic_year_id' => $data['academic_year_id'],
                'strand_id' => $data['strand_id'],
                'section_id' => $sectionId,
        'adviser_teacher_id' => $data['adviser_teacher_id'] ?? null,
                'is_active' => $data['is_active'],
            ]);
        }

        return redirect()->route('admin.academic-years.show', $data['academic_year_id'])->with('success', 'Sections assigned successfully.');
    }

    public function edit(AcademicYearStrandSection $assignment)
    {
    $assignment->load(['academicYear', 'strand', 'section', 'adviserTeacher']);
        $strands = Strand::orderBy('code')->get();
        $sections = Section::orderBy('grade')->orderBy('name')->get();
    $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
    return view('admin.academic_year_strand_sections.edit', compact('assignment', 'strands', 'sections', 'teachers'));
    }

    public function update(Request $request, AcademicYearStrandSection $assignment)
    {
        $data = $request->validate([
            'strand_id' => ['required', 'exists:strands,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'adviser_teacher_id' => ['nullable', 'exists:teachers,id'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', $assignment->is_active);

        // Enforce uniqueness on update
        $exists = AcademicYearStrandSection::where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $data['strand_id'])
            ->where('section_id', $data['section_id'])
            ->where('id', '!=', $assignment->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['section_id' => 'This section is already assigned for the selected academic year.']);
        }

    $assignment->update($data);

        return redirect()->route('admin.academic-years.show', $assignment->academic_year_id)->with('success', 'Section assignment updated successfully.');
    }
}
