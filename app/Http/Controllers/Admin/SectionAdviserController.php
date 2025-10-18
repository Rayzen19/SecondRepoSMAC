<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\StrandSubject;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class SectionAdviserController extends Controller
{
    /**
     * Display the section and adviser assignment page.
     */
    public function index()
    {
        // Get all active strands
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get all active teachers for adviser assignment
        $teachers = Teacher::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get saved adviser assignments from session
        $savedAdvisers = session('adviser_assignments', []);
        
        return view('admin.section_advisers.index', compact('strands', 'teachers', 'savedAdvisers'));
    }
    
    /**
     * Save adviser assignments
     */
    public function saveAdvisers(Request $request)
    {
        $validated = $request->validate([
            'advisers' => 'required|array',
            'advisers.*.strand_code' => 'required|string',
            'advisers.*.section_number' => 'required|integer|between:1,4',
            'advisers.*.teacher_id' => 'required|exists:teachers,id',
        ]);
        
        // Store adviser assignments in session
        session(['adviser_assignments' => $validated['advisers']]);
        
        // Also persist to database for FK requirements
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            foreach ($validated['advisers'] as $row) {
                $strand = Strand::where('code', $row['strand_code'])->first();
                if ($strand) {
                    \App\Models\AcademicYearStrandAdviser::updateOrCreate(
                        [
                            'academic_year_id' => $activeYear->id,
                            'strand_id' => $strand->id,
                        ],
                        [
                            'teacher_id' => $row['teacher_id'],
                        ]
                    );
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Adviser assignments saved successfully!',
            'count' => count($validated['advisers'])
        ]);
    }
    
    /**
     * Get student details for viewing section
     */
    public function getStudents(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer',
        ]);
        
        // Fetch student details
        $students = Student::whereIn('id', $validated['student_ids'])
            ->select('id', 'student_number', 'first_name', 'last_name', 'middle_name', 'program', 'academic_year')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Remove a student from a section assignment stored in session.
     */
    public function removeStudent(Request $request)
    {
        $validated = $request->validate([
            'strand_code' => 'required|string',
            'section_number' => 'required|integer|between:1,4',
            'student_id' => 'required|integer'
        ]);

        $assignments = session('student_assignments', []);

        // Filter out the matched assignment
        $before = count($assignments);
        $assignments = array_values(array_filter($assignments, function ($a) use ($validated) {
            return !(
                (string)($a['strand_code'] ?? '') === (string)$validated['strand_code'] &&
                intval($a['section_number'] ?? 0) === intval($validated['section_number']) &&
                intval($a['student_id'] ?? 0) === intval($validated['student_id'])
            );
        }));
        $after = count($assignments);

        session(['student_assignments' => $assignments]);

        return response()->json([
            'success' => true,
            'removed' => $before - $after,
            'remaining' => $after,
        ]);
    }

    /**
     * Get subjects per strand and grade level
     */
    public function getSubjects(Request $request)
    {
        $validated = $request->validate([
            'strand_code' => 'required|string',
            'grade_level' => 'required|in:11,12'
        ]);

        $strand = Strand::where('code', $validated['strand_code'])->first();
        if (!$strand) {
            return response()->json(['success' => false, 'message' => 'Strand not found'], 404);
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

        // Join StrandSubject with Subject and filter by strand and grade_level if present
        $strandSubjects = StrandSubject::with('subject', 'strand')
            ->where('strand_id', $strand->id)
            ->when(Schema::hasColumn('strand_subjects', 'grade_level'), function ($q) use ($validated) {
                $q->where('grade_level', $validated['grade_level']);
            })
            ->orderBy('id', 'asc')
            ->get();

        // Map to a simple structure with assigned teacher
        $subjects = $strandSubjects->map(function ($ss) use ($activeYear, $strand) {
            $assigned = null;
            if ($activeYear) {
                $assignment = \App\Models\AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
                    ->where('strand_id', $strand->id)
                    ->where('subject_id', $ss->subject->id)
                    ->with('teacher')
                    ->first();
                if ($assignment && $assignment->teacher) {
                    $assigned = [
                        'id' => $assignment->teacher->id,
                        'name' => $assignment->teacher->first_name . ' ' . $assignment->teacher->last_name
                    ];
                }
            }
            
            return [
                'id' => $ss->subject->id ?? null,
                'code' => $ss->subject->code ?? '',
                'name' => $ss->subject->name ?? '',
                'type' => $ss->subject->type ?? null,
                'semester' => $ss->subject->semester ?? null,
                'grade_level' => $ss->grade_level ?? null,
                'assigned_teacher' => $assigned,
            ];
        })->filter(fn($s) => $s['id'] !== null)->values();

        return response()->json([
            'success' => true,
            'strand' => $strand->code,
            'grade_level' => $validated['grade_level'],
            'count' => $subjects->count(),
            'subjects' => $subjects
        ]);
    }

    /**
     * Get teachers eligible for a subject (must be profiled for that subject)
     */
    public function subjectTeachers(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $teachers = Teacher::where('status', 'active')
            ->whereHas('subjects', function ($q) use ($validated) {
                $q->where('subject_id', $validated['subject_id']);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->first_name . ' ' . $t->last_name,
                'employee_number' => $t->employee_number
            ]);

        return response()->json([
            'success' => true,
            'count' => $teachers->count(),
            'teachers' => $teachers
        ]);
    }

    /**
     * Save teacher assignment for a subject
     */
    public function saveSubjectTeacher(Request $request)
    {
        Log::info('=== Save Subject Teacher Request ===', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);

        $validated = $request->validate([
            'strand_code' => 'required|string',
            'grade_level' => 'required|in:11,12',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        Log::info('Validated data', $validated);

        $strand = Strand::where('code', $validated['strand_code'])->first();
        if (!$strand) {
            Log::warning('Strand not found', ['code' => $validated['strand_code']]);
            return response()->json(['success' => false, 'message' => 'Strand not found.'], 404);
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            Log::warning('No active academic year');
            return response()->json(['success' => false, 'message' => 'No active academic year.'], 422);
        }

        Log::info('Found strand and academic year', [
            'strand_id' => $strand->id,
            'strand_code' => $strand->code,
            'academic_year_id' => $activeYear->id
        ]);

        // Validate teacher is profiled for subject
        if (!empty($validated['teacher_id'])) {
            $handles = Teacher::where('id', $validated['teacher_id'])
                ->whereHas('subjects', function ($q) use ($validated) {
                    $q->where('subject_id', $validated['subject_id']);
                })
                ->exists();
            if (!$handles) {
                Log::warning('Teacher not profiled for subject', [
                    'teacher_id' => $validated['teacher_id'],
                    'subject_id' => $validated['subject_id']
                ]);
                return response()->json(['success' => false, 'message' => 'Teacher not profiled for this subject.'], 422);
            }
            Log::info('Teacher is profiled for subject');
        }

        // Require adviser to exist
        $adviser = \App\Models\AcademicYearStrandAdviser::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->first();
        if (!$adviser) {
            Log::warning('No adviser found for strand', [
                'academic_year_id' => $activeYear->id,
                'strand_id' => $strand->id
            ]);
            return response()->json(['success' => false, 'message' => 'Please assign an adviser first.'], 422);
        }

        Log::info('Found adviser', ['adviser_id' => $adviser->id]);

        // Clear or assign
        if (empty($validated['teacher_id'])) {
            \App\Models\AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
                ->where('strand_id', $strand->id)
                ->where('subject_id', $validated['subject_id'])
                ->delete();
            Log::info('Assignment cleared');
            return response()->json(['success' => true, 'message' => 'Assignment cleared.']);
        }

        $assignment = \App\Models\AcademicYearStrandSubject::updateOrCreate(
            [
                'academic_year_id' => $activeYear->id,
                'strand_id' => $strand->id,
                'subject_id' => $validated['subject_id'],
            ],
            [
                'academic_year_strand_adviser_id' => $adviser->id,
                'teacher_id' => $validated['teacher_id'],
            ]
        );

        Log::info('Assignment saved', [
            'assignment_id' => $assignment->id,
            'was_recently_created' => $assignment->wasRecentlyCreated
        ]);

        return response()->json(['success' => true, 'message' => 'Teacher assigned successfully.']);
    }
}
