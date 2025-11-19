<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Strand;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;

        // Get filter parameters
        $yearLevel = $request->get('year_level');
        $strand = $request->get('strand');
        $section = $request->get('section');
        $subject = $request->get('subject');
        $search = $request->get('search');

        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        if (!$currentAcademicYear) {
            return view('teacher.students.index', [
                'students' => collect(),
                'yearLevels' => collect(),
                'strands' => collect(),
                'sections' => collect(),
                'subjects' => collect(),
                'selectedYearLevel' => null,
                'selectedStrand' => null,
                'selectedSection' => null,
                'selectedSubject' => null,
                'search' => null,
                'totalStudents' => 0,
            ]);
        }

        // Get all section assignments where this teacher is teaching or advising
        $teachingSections = AcademicYearStrandSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->whereNotNull('academic_year_strand_section_id')
            ->pluck('academic_year_strand_section_id')
            ->unique();

        $advisingSections = AcademicYearStrandSection::where('adviser_teacher_id', $teacherId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->pluck('id');

        $sectionIds = $teachingSections->merge($advisingSections)->unique();

        if ($sectionIds->isEmpty()) {
            return view('teacher.students.index', [
                'students' => collect(),
                'yearLevels' => collect(),
                'strands' => collect(),
                'sections' => collect(),
                'subjects' => collect(),
                'selectedYearLevel' => null,
                'selectedStrand' => null,
                'selectedSection' => null,
                'selectedSubject' => null,
                'search' => null,
                'totalStudents' => 0,
            ]);
        }

        // Get unique year levels from sections
        $yearLevels = Section::whereIn('id', function ($query) use ($sectionIds) {
                $query->select('section_id')
                    ->from('academic_year_strand_sections')
                    ->whereIn('id', $sectionIds);
            })
            ->distinct()
            ->pluck('grade')
            ->filter()
            ->sort()
            ->values();

        // Get unique strands
        $strands = Strand::whereIn('id', function ($query) use ($sectionIds) {
                $query->select('strand_id')
                    ->from('academic_year_strand_sections')
                    ->whereIn('id', $sectionIds);
            })
            ->pluck('code', 'id');

        // Get unique sections
        $sections = Section::whereIn('id', function ($query) use ($sectionIds) {
                $query->select('section_id')
                    ->from('academic_year_strand_sections')
                    ->whereIn('id', $sectionIds);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        // Get subjects that the teacher handles
        $subjectQuery = AcademicYearStrandSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->with('subject');
        
        // If subject filter is applied, use only that subject for student filtering
        if ($subject) {
            $subjectQuery->where('subject_id', $subject);
        }
        
        $subjects = \App\Models\Subject::whereIn('id', function ($query) use ($teacherId, $currentAcademicYear) {
                $query->select('subject_id')
                    ->from('academic_year_strand_subjects')
                    ->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $currentAcademicYear->id);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        // Build student query
        $studentsQuery = Student::whereHas('studentEnrollments', function ($query) use ($sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds);
            })
            ->with(['studentEnrollments' => function ($query) use ($sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds)
                    ->with(['academicYearStrandSection.section', 'academicYearStrandSection.strand']);
            }]);

        // Apply filters
        if ($yearLevel) {
            $studentsQuery->whereHas('studentEnrollments', function ($query) use ($yearLevel, $sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds)
                    ->whereHas('academicYearStrandSection.section', function ($q) use ($yearLevel) {
                        $q->where('grade', $yearLevel);
                    });
            });
        }

        if ($strand) {
            $studentsQuery->whereHas('studentEnrollments', function ($query) use ($strand, $sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds)
                    ->whereHas('academicYearStrandSection', function ($q) use ($strand) {
                        $q->where('strand_id', $strand);
                    });
            });
        }

        if ($section) {
            $studentsQuery->whereHas('studentEnrollments', function ($query) use ($section, $sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds)
                    ->whereHas('academicYearStrandSection', function ($q) use ($section) {
                        $q->where('section_id', $section);
                    });
            });
        }

        // Apply subject filter
        if ($subject) {
            // Get section IDs for this subject
            $subjectSectionIds = AcademicYearStrandSubject::where('teacher_id', $teacherId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('subject_id', $subject)
                ->whereNotNull('academic_year_strand_section_id')
                ->pluck('academic_year_strand_section_id')
                ->unique();
            
            // Also get strand IDs for sections where subject is assigned without specific section
            $subjectStrandIds = AcademicYearStrandSubject::where('teacher_id', $teacherId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('subject_id', $subject)
                ->whereNull('academic_year_strand_section_id')
                ->pluck('strand_id')
                ->unique();

            $studentsQuery->whereHas('studentEnrollments', function ($query) use ($subjectSectionIds, $subjectStrandIds, $sectionIds) {
                $query->whereIn('academic_year_strand_section_id', $sectionIds)
                    ->where(function ($q) use ($subjectSectionIds, $subjectStrandIds) {
                        // Match students in sections where subject is specifically assigned
                        if ($subjectSectionIds->isNotEmpty()) {
                            $q->whereIn('academic_year_strand_section_id', $subjectSectionIds);
                        }
                        // OR match students in strands where subject is assigned to all sections
                        if ($subjectStrandIds->isNotEmpty()) {
                            $q->orWhereHas('academicYearStrandSection', function ($sq) use ($subjectStrandIds) {
                                $sq->whereIn('strand_id', $subjectStrandIds);
                            });
                        }
                    });
            });
        }

        // Apply search filter
        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Get students
        $students = $studentsQuery->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($student) use ($teacherId, $currentAcademicYear) {
                $latestEnrollment = $student->studentEnrollments->first();
                $ayss = $latestEnrollment ? $latestEnrollment->academicYearStrandSection : null;

                // Find the teacher's subject assignment for this student
                $assignmentId = null;
                if ($ayss) {
                    $assignment = AcademicYearStrandSubject::where('teacher_id', $teacherId)
                        ->where('academic_year_id', $currentAcademicYear->id)
                        ->where('strand_id', $ayss->strand_id)
                        ->where(function ($q) use ($ayss) {
                            $q->whereNull('academic_year_strand_section_id')
                              ->orWhere('academic_year_strand_section_id', $ayss->id);
                        })
                        ->first();
                    
                    $assignmentId = $assignment ? $assignment->id : null;
                }

                return [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'middle_name' => $student->middle_name,
                    'full_name' => $student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ? substr($student->middle_name, 0, 1) . '.' : ''),
                    'grade_level' => $ayss && $ayss->section ? $ayss->section->grade : 'N/A',
                    'strand' => $ayss && $ayss->strand ? $ayss->strand->code : 'N/A',
                    'section' => $ayss && $ayss->section ? $ayss->section->name : 'N/A',
                    'email' => $student->email ?? 'N/A',
                    'gender' => $student->gender ?? 'N/A',
                    'assignment_id' => $assignmentId,
                ];
            });

        return view('teacher.students.index', [
            'students' => $students,
            'yearLevels' => $yearLevels,
            'strands' => $strands,
            'sections' => $sections,
            'subjects' => $subjects,
            'selectedYearLevel' => $yearLevel,
            'selectedStrand' => $strand,
            'selectedSection' => $section,
            'selectedSubject' => $subject,
            'search' => $search,
            'totalStudents' => $students->count(),
        ]);
    }

    public function section($sectionAssignmentId)
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;

        // Get the section assignment (don't filter by adviser here)
        $sectionAssignment = AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
            ->where('id', $sectionAssignmentId)
            ->firstOrFail();

        // Authorization: allow if adviser OR if teacher has a subject assignment in this AY+Strand
        $isAdviser = (int)($sectionAssignment->adviser_teacher_id ?? 0) === (int)$teacherId;

        $teachesThisSection = AcademicYearStrandSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $sectionAssignment->academic_year_id)
            ->where('strand_id', $sectionAssignment->strand_id)
            ->where(function ($q) use ($sectionAssignment) {
                $q->whereNull('academic_year_strand_section_id')
                  ->orWhere('academic_year_strand_section_id', $sectionAssignment->id);
            })
            ->exists();

        if (!$isAdviser && !$teachesThisSection) {
            abort(403, 'You are not authorized to view this section.');
        }

        // Get all students enrolled in this section
        $students = StudentEnrollment::with(['student'])
            ->where('academic_year_strand_section_id', $sectionAssignment->id)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'student' => $enrollment->student,
                    'registration_number' => $enrollment->registration_number,
                    'status' => $enrollment->status,
                ];
            })
            ->filter(fn($row) => !is_null($row['student']))
            ->values();
        
        // Resolve the subject assignment(s) of the current teacher for this AY + Strand (and section if mapped)
        $assignmentOptions = AcademicYearStrandSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->where('academic_year_id', $sectionAssignment->academic_year_id)
            ->where('strand_id', $sectionAssignment->strand_id)
            ->where(function ($q) use ($sectionAssignment) {
                $q->whereNull('academic_year_strand_section_id')
                  ->orWhere('academic_year_strand_section_id', $sectionAssignment->id);
            })
            ->orderBy('id')
            ->get();

        $total = $students->count();
        $male = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'male')->count();
        $female = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'female')->count();
        
        return view('teacher.students.section', [
            'sectionAssignment' => $sectionAssignment,
            'students' => $students,
            'assignmentOptions' => $assignmentOptions,
            'total' => $total,
            'male' => $male,
            'female' => $female,
        ]);
    }
    
    public function allSections()
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;
        
        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            return view('teacher.students.all_sections', [
                'groupedSections' => collect(),
                'currentAcademicYear' => null,
            ]);
        }
        
        // Get all section assignments for the current academic year
        $assignments = AcademicYearStrandSection::with(['strand', 'section', 'adviserTeacher'])
            ->where('academic_year_id', $currentAcademicYear->id)
            ->get();
        
        // Group sections by strand and then by grade
    $groupedSections = $assignments->map(function ($assignment) use ($teacherId) {
            $studentsCount = StudentEnrollment::where('academic_year_strand_section_id', $assignment->id)
                ->count();
            
            return (object) [
                'assignment_id' => $assignment->id,
                'section_name' => $assignment->section->name ?? 'N/A',
                'section_grade' => $assignment->section->grade ?? 'N/A',
                'strand_id' => $assignment->strand->id ?? 0,
                'strand_name' => $assignment->strand->name ?? 'N/A',
                'strand_code' => $assignment->strand->code ?? 'N/A',
                'adviser_name' => $assignment->adviserTeacher ? 
                    $assignment->adviserTeacher->last_name . ', ' . $assignment->adviserTeacher->first_name : 
                    'No Adviser',
                'is_my_section' => $assignment->adviser_teacher_id === $teacherId,
                'students_count' => $studentsCount,
            ];
        })
        ->groupBy('strand_code') // Group by strand
        ->map(function ($strandSections) {
            // Within each strand, group by grade
            $first = $strandSections->first();
            return [
                'strand_name' => $first->strand_name,
                'strand_code' => $first->strand_code,
                'grades' => $strandSections->groupBy('section_grade')
                    ->map(function ($gradeSections) {
                        return $gradeSections->sortBy('section_name')->values();
                    })
            ];
        });
        
        return view('teacher.students.all_sections', [
            'groupedSections' => $groupedSections,
            'currentAcademicYear' => $currentAcademicYear,
        ]);
    }

    public function storeAssessment(Request $request)
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;

        // Validate the request
        $validated = $request->validate([
            'type' => 'required|in:WW,PT,QA',
            'title' => 'required|string|max:255',
            'term' => 'required|in:1,2',
            'grade_level' => 'required|in:11,12',
            'max_score' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'student_ids' => 'required|array', // Changed to array
            'student_ids.*' => 'required|integer|exists:students,id', // Validate each ID
        ]);

        // Get student IDs
        $studentIds = $validated['student_ids'];
        
        if (empty($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No students selected.'
            ], 400);
        }

        // Map assessment type
        $typeMap = [
            'WW' => 'written work',
            'PT' => 'performance task',
            'QA' => 'quarterly assessment',
        ];
        
        $quarterMap = [
            '1' => '1st',
            '2' => '2nd',
        ];

        $assessmentType = $typeMap[$validated['type']];
        $quarter = $quarterMap[$validated['term']];

        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic year found.'
            ], 400);
        }

        $createdAssessments = [];
        $errors = [];

        // Group students by their section to find the correct subject assignment
        $students = Student::whereIn('id', $studentIds)
            ->with(['studentEnrollments' => function($query) use ($currentAcademicYear) {
                $query->whereHas('academicYearStrandSection', function($q) use ($currentAcademicYear) {
                    $q->where('academic_year_id', $currentAcademicYear->id);
                })->with('academicYearStrandSection.strand');
            }])
            ->get();

        foreach ($students as $student) {
            $enrollment = $student->studentEnrollments->first();
            
            if (!$enrollment || !$enrollment->academicYearStrandSection) {
                $errors[] = "Student {$student->full_name} has no active enrollment.";
                continue;
            }

            $ayss = $enrollment->academicYearStrandSection;
            $strandId = $ayss->strand_id;

            // Find teacher's subject assignment for this strand
            $assignment = AcademicYearStrandSubject::where('teacher_id', $teacherId)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('strand_id', $strandId)
                ->where(function ($q) use ($ayss) {
                    $q->whereNull('academic_year_strand_section_id')
                      ->orWhere('academic_year_strand_section_id', $ayss->id);
                })
                ->first();

            if (!$assignment) {
                $errors[] = "No subject assignment found for student {$student->full_name}.";
                continue;
            }

            // Create or find the SubjectRecord (assessment)
            $subjectRecord = SubjectRecord::firstOrCreate([
                'academic_year_strand_subject_id' => $assignment->id,
                'name' => $validated['title'],
                'type' => $assessmentType,
                'quarter' => $quarter,
                'date_given' => $validated['date'],
            ], [
                'description' => $validated['description'] ?? null,
                'max_score' => $validated['max_score'],
                'remarks' => null,
            ]);

            // Create SubjectRecordResult for this student (placeholder with null scores)
            SubjectRecordResult::firstOrCreate([
                'subject_record_id' => $subjectRecord->id,
                'student_id' => $student->id,
            ], [
                'raw_score' => null,
                'base_score' => null,
                'final_score' => null,
                'remarks' => null,
                'description' => null,
                'date_submitted' => null,
            ]);

            $createdAssessments[] = [
                'student' => $student->full_name,
                'assessment' => $subjectRecord->name,
            ];
        }

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Some assessments could not be created.',
                'errors' => $errors,
                'created' => $createdAssessments,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully created {$validated['type']} assessment \"{$validated['title']}\" for " . count($studentIds) . " student(s)!",
            'created' => $createdAssessments,
        ]);
    }

    public function showGrades($studentId)
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;

        Log::info('showGrades called', [
            'student_id' => $studentId,
            'teacher_id' => $teacherId
        ]);

        // Find the student
        $student = Student::findOrFail($studentId);

        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            Log::error('No active academic year');
            return redirect()->route('teacher.students.index')
                ->with('error', 'No active academic year found.');
        }

        // Get student's enrollment
        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->whereHas('academicYearStrandSection', function($q) use ($currentAcademicYear) {
                $q->where('academic_year_id', $currentAcademicYear->id);
            })
            ->with(['academicYearStrandSection.strand', 'academicYearStrandSection.section'])
            ->first();

        if (!$enrollment) {
            Log::error('No enrollment found', ['student_id' => $student->id]);
            return redirect()->route('teacher.students.index')
                ->with('error', 'Student enrollment not found for current academic year.');
        }

        $ayss = $enrollment->academicYearStrandSection;

        // Find ALL teacher's subject assignments for this student's strand
        $assignments = AcademicYearStrandSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('strand_id', $ayss->strand_id)
            ->where(function ($q) use ($ayss) {
                $q->whereNull('academic_year_strand_section_id')
                  ->orWhere('academic_year_strand_section_id', $ayss->id);
            })
            ->with(['subject', 'academicYear'])
            ->get();

        if ($assignments->isEmpty()) {
            Log::error('No assignments found', [
                'teacher_id' => $teacherId,
                'strand_id' => $ayss->strand_id
            ]);
            return redirect()->route('teacher.students.index')
                ->with('error', 'You do not have a subject assignment for this student\'s strand.');
        }

        // Use the first assignment found
        $assignment = $assignments->first();

        Log::info('Redirecting to class records', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id
        ]);

        // Redirect to the existing class records student view
        return redirect()->route('teacher.class-records.students.show', [
            'assignment' => $assignment->id,
            'student' => $student->id
        ]);
    }
}
