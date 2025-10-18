<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\StrandSubject;
use App\Models\Section;
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
        
        // Get all sections with their strand relationships, grouped by strand
        $sections = Section::with('strand')
            ->orderBy('grade')
            ->orderBy('name')
            ->get()
            ->groupBy(function($section) {
                return $section->strand ? $section->strand->code : 'N/A';
            });
        
        // Get all active teachers for adviser assignment
        $teachers = Teacher::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get saved adviser assignments from session
        $savedAdvisers = session('adviser_assignments', []);
        
        return view('admin.section_advisers.index', compact('strands', 'sections', 'teachers', 'savedAdvisers'));
    }
    
    /**
     * Display the Grade 11 sections page.
     */
    public function grade11()
    {
        // Get all active strands
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get only Grade 11 sections, grouped by strand
        $sections = Section::with('strand')
            ->where('grade', 'G-11')
            ->orderBy('name')
            ->get()
            ->groupBy(function($section) {
                return $section->strand ? $section->strand->code : 'N/A';
            });
        
        // Get all active teachers for adviser assignment
        $teachers = Teacher::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get saved adviser assignments from session
        $savedAdvisers = session('adviser_assignments', []);
        
        return view('admin.section_advisers.grade11', compact('strands', 'sections', 'teachers', 'savedAdvisers'));
    }
    
    /**
     * Display the Grade 12 sections page.
     */
    public function grade12()
    {
        // Get all active strands
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get only Grade 12 sections, grouped by strand
        $sections = Section::with('strand')
            ->where('grade', 'G-12')
            ->orderBy('name')
            ->get()
            ->groupBy(function($section) {
                return $section->strand ? $section->strand->code : 'N/A';
            });
        
        // Get all active teachers for adviser assignment
        $teachers = Teacher::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get saved adviser assignments from session
        $savedAdvisers = session('adviser_assignments', []);
        
        return view('admin.section_advisers.grade12', compact('strands', 'sections', 'teachers', 'savedAdvisers'));
    }
    
    /**
     * Save adviser assignments
     */
    public function saveAdvisers(Request $request)
    {
        $validated = $request->validate([
            'advisers' => 'required|array',
            'advisers.*.strand_code' => 'required|string',
            'advisers.*.section_id' => 'required|exists:sections,id',
            'advisers.*.teacher_id' => 'required|exists:teachers,id',
        ]);
        
        // Store adviser assignments in session
        session(['adviser_assignments' => $validated['advisers']]);
        
        // Also persist to database for FK requirements
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            foreach ($validated['advisers'] as $row) {
                $strand = Strand::where('code', $row['strand_code'])->first();
                $section = Section::find($row['section_id']);
                
                if ($strand && $section) {
                    \App\Models\AcademicYearStrandAdviser::updateOrCreate(
                        [
                            'academic_year_id' => $activeYear->id,
                            'strand_id' => $strand->id,
                            'section_id' => $section->id,
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
     * Get students enrolled in a specific section from database
     */
    public function getSectionStudents(Request $request)
    {
        $validated = $request->validate([
            'strand_code' => 'required|string',
            'section_id' => 'required|exists:sections,id',
        ]);

        // Get active academic year
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic year found'
            ], 422);
        }

        // Find the strand
        $strand = Strand::where('code', $validated['strand_code'])->first();
        if (!$strand) {
            return response()->json([
                'success' => false,
                'message' => 'Strand not found'
            ], 404);
        }

        // Find the academic_year_strand_section record
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->where('section_id', $validated['section_id'])
            ->first();

        if (!$academicYearStrandSection) {
            return response()->json([
                'success' => true,
                'students' => [],
                'count' => 0
            ]);
        }

        // Get enrolled students
        $enrollments = \App\Models\StudentEnrollment::with('student')
            ->where('academic_year_strand_section_id', $academicYearStrandSection->id)
            ->where('status', 'enrolled')
            ->get();

        $students = $enrollments->map(function($enrollment) {
            return [
                'id' => $enrollment->student->id,
                'student_number' => $enrollment->student->student_number,
                'first_name' => $enrollment->student->first_name,
                'last_name' => $enrollment->student->last_name,
                'middle_name' => $enrollment->student->middle_name,
                'program' => $enrollment->student->program,
                'academic_year' => $enrollment->student->academic_year,
                'registration_number' => $enrollment->registration_number
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $students,
            'count' => $students->count()
        ]);
    }

    /**
     * Get student counts for all sections
     */
    public function getSectionCounts(Request $request)
    {
        // Get active academic year
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic year found'
            ], 422);
        }

        // Get all sections with their student counts
        $sections = \App\Models\Section::with('strand')->get();
        $counts = [];

        foreach ($sections as $section) {
            if (!$section->strand) continue;

            $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                ->where('strand_id', $section->strand->id)
                ->where('section_id', $section->id)
                ->first();

            $count = 0;
            if ($academicYearStrandSection) {
                $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $academicYearStrandSection->id)
                    ->where('status', 'enrolled')
                    ->count();
            }

            $key = $section->strand->code . '-' . $section->id;
            $counts[$key] = $count;
        }

        return response()->json([
            'success' => true,
            'counts' => $counts
        ]);
    }

    /**
     * Remove a student from a section assignment.
     */
    public function removeStudent(Request $request)
    {
        $validated = $request->validate([
            'strand_code' => 'required|string',
            'section_id' => 'required|integer',
            'student_id' => 'required|integer'
        ]);

        // Get active academic year
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic year found'
            ], 422);
        }

        // Find the strand
        $strand = Strand::where('code', $validated['strand_code'])->first();
        if (!$strand) {
            return response()->json([
                'success' => false,
                'message' => 'Strand not found'
            ], 404);
        }

        // Find the academic_year_strand_section record
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->where('section_id', $validated['section_id'])
            ->first();

        if (!$academicYearStrandSection) {
            return response()->json([
                'success' => false,
                'message' => 'Section assignment not found'
            ], 404);
        }

        // Delete the student enrollment
        $deleted = \App\Models\StudentEnrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_strand_section_id', $academicYearStrandSection->id)
            ->delete();

        // Also remove from session for backward compatibility
        $assignments = session('student_assignments', []);
        $before = count($assignments);
        $assignments = array_values(array_filter($assignments, function ($a) use ($validated) {
            return !(
                (string)($a['strand_code'] ?? '') === (string)$validated['strand_code'] &&
                intval($a['section_id'] ?? 0) === intval($validated['section_id']) &&
                intval($a['student_id'] ?? 0) === intval($validated['student_id'])
            );
        }));
        $after = count($assignments);
        session(['student_assignments' => $assignments]);

        return response()->json([
            'success' => true,
            'message' => $deleted > 0 ? 'Student removed from section successfully' : 'Student was not enrolled in this section',
            'removed' => $before - $after,
            'remaining' => $after,
            'deleted_from_db' => $deleted
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
