<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;
use App\Models\AcademicYearStrandSubject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['strandSubjects.strand'])->orderBy('code');
        
        // Filter by strand if specified
        if ($request->filled('strand_id')) {
            $query->whereHas('strandSubjects', function($q) use ($request) {
                $q->where('strand_id', $request->strand_id);
            });
        }
        
        $allSubjects = $query->get();
        
        // Group subjects by type
        $coreSubjects = $allSubjects->where('type', 'core');
        $appliedSubjects = $allSubjects->where('type', 'applied');
        $specializedSubjects = $allSubjects->where('type', 'specialized');
        
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        return view('admin.subjects.index', compact('coreSubjects', 'appliedSubjects', 'specializedSubjects', 'strands'));
    }

    public function create()
    {
        $allStrands = Strand::where('is_active', true)->orderBy('code')->get();
        return view('admin.subjects.create', compact('allStrands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:0',
            'type' => 'required|in:core,applied,specialized',
            'semester' => 'required|in:1st,2nd',
            'strand_ids' => 'required|array|min:1',
            'strand_ids.*' => 'exists:strands,id',
            'grade_level' => 'nullable|in:11,12',
        ]);

        // Create the subject
        $subject = Subject::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'units' => $data['units'],
            'type' => $data['type'],
            'semester' => $data['semester'],
        ]);

        // Link to all selected strands with default percentages
        foreach ($data['strand_ids'] as $strandId) {
            StrandSubject::create([
                'strand_id' => $strandId,
                'subject_id' => $subject->id,
                'grade_level' => $data['grade_level'] ?? null,
                'semestral_period' => $data['semester'],
                'written_works_percentage' => 20,
                'performance_tasks_percentage' => 60,
                'quarterly_assessment_percentage' => 20,
                'is_active' => true,
            ]);
        }

        return redirect()
            ->route('admin.subjects.show', $subject)
            ->with('success', 'Subject created and linked to ' . count($data['strand_ids']) . ' strand(s) successfully.');
    }

    public function edit(Subject $subject)
    {
        // Load the subject with its strand relationships
        $subject->load(['strandSubjects.strand']);
        
        // Get all active strands
        $allStrands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get strands that currently have this subject
        $linkedStrandIds = $subject->strandSubjects->pluck('strand_id')->toArray();
        
        return view('admin.subjects.edit', compact('subject', 'allStrands', 'linkedStrandIds'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:0',
            'type' => 'required|in:core,applied,specialized',
            'semester' => 'required|in:1st,2nd',
            'strand_ids' => 'nullable|array',
            'strand_ids.*' => 'exists:strands,id',
            'grade_level' => 'nullable|in:11,12',
        ]);

        $subject->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'units' => $data['units'],
            'type' => $data['type'],
            'semester' => $data['semester'],
        ]);

        // Update strand links if strand_ids are provided
        if ($request->has('strand_ids')) {
            $strandIds = $request->input('strand_ids', []);
            $gradeLevel = $request->input('grade_level');
            
            // Get all existing strand subjects for this subject
            $existingLinks = $subject->strandSubjects()
                ->get()
                ->keyBy('strand_id');
            
            // Remove unchecked strands
            foreach ($existingLinks as $strandId => $link) {
                if (!in_array($strandId, $strandIds)) {
                    $link->delete();
                }
            }
            
            // Add or update strands
            foreach ($strandIds as $strandId) {
                if (isset($existingLinks[$strandId])) {
                    // Update existing link with new grade level
                    $existingLinks[$strandId]->update([
                        'grade_level' => $gradeLevel,
                        'semestral_period' => $subject->semester,
                    ]);
                } else {
                    // Create new link
                    StrandSubject::create([
                        'strand_id' => $strandId,
                        'subject_id' => $subject->id,
                        'grade_level' => $gradeLevel,
                        'semestral_period' => $subject->semester,
                        'written_works_percentage' => 20,
                        'performance_tasks_percentage' => 60,
                        'quarterly_assessment_percentage' => 20,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.subjects.show', $subject)->with('success', 'Subject updated successfully.');
    }

    public function show(Subject $subject)
    {
        return view('admin.subjects.show', compact('subject'));
    }

    public function teachers(Subject $subject)
    {
        $teachers = collect([]);

        // Get teachers with actual assignments through AcademicYearStrandSubject
        $assignedTeachers = AcademicYearStrandSubject::with([
            'teacher',
            'academicYear',
            'strand'
        ])
        ->where('subject_id', $subject->id)
        ->whereHas('teacher') // Only include records with valid teachers
        ->get()
        ->groupBy('teacher_id');

        $assignedTeacherIds = [];

        foreach ($assignedTeachers as $teacherId => $assignments) {
            $teacher = $assignments->first()->teacher;
            $assignmentDetails = $assignments->map(function ($assignment) {
                return [
                    'academic_year' => $assignment->academicYear->name ?? 'N/A',
                    'strand' => $assignment->strand->code ?? 'N/A',
                ];
            });
            
            $teachers->push([
                'teacher' => $teacher,
                'assignments' => $assignmentDetails,
                'has_assignment' => true,
            ]);

            $assignedTeacherIds[] = $teacherId;
        }

        // Get teachers who are qualified to teach this subject (from teacher_subject pivot)
        $qualifiedTeachers = $subject->teachers()
            ->whereNotIn('teachers.id', $assignedTeacherIds)
            ->get();

        foreach ($qualifiedTeachers as $teacher) {
            $teachers->push([
                'teacher' => $teacher,
                'assignments' => collect([]),
                'has_assignment' => false,
            ]);
        }

        return view('admin.subjects.teachers', compact('subject', 'teachers'));
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.subjects.index')
                ->with('error', 'Unable to delete subject. It may be linked to other records.');
        }
    }
}
