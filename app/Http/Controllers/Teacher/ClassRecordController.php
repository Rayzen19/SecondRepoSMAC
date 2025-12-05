<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Mail\GradePublishedNotification;
use App\Models\Student;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClassRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) {
            abort(401);
        }

        $activeYear = AcademicYear::where('is_active', true)->first();

        // Pull all AYS assignments for this teacher across all years, latest first
        $assignments = AcademicYearStrandSubject::with([
                'academicYear',
                'strand',
                'adviser',
                'adviser.teacher',
        'subject',
            ])
            ->withCount(['subjectEnrollments as students_count'])
            ->where('teacher_id', $user->user_pk_id)
            ->orderByDesc('academic_year_id')
            ->get();

    $rows = $assignments->map(function ($a) {
            $ay = $a->academicYear;
            $strandName = $a->strand?->name;
        $subjectName = $a->subject?->name;
        $subjectCode = $a->subject?->code;

            // Resolve section via AcademicYearStrandSection for this year+strand; prefer the row where adviser matches this teacher
            $sectionName = null;
            $adviserName = null;

            $sectionQuery = AcademicYearStrandSection::with('section', 'adviserTeacher')
                ->where('academic_year_id', $a->academic_year_id)
                ->where('strand_id', $a->strand_id);

            $preferred = (clone $sectionQuery)->where('adviser_teacher_id', $a->teacher_id)->first();
            $sectionAssignment = $preferred ?: $sectionQuery->first();

            if ($sectionAssignment) {
                $sectionName = optional($sectionAssignment->section)->name;
                $adviserName = optional($sectionAssignment->adviserTeacher)->last_name
                    ? ($sectionAssignment->adviserTeacher->last_name . ', ' . $sectionAssignment->adviserTeacher->first_name)
                    : null;
            }

            return [
                'id' => $a->id,
                'year' => $ay?->name,
                'semester' => $ay?->semester,
                'subject_name' => $subjectName,
                'subject_code' => $subjectCode,
                'strand' => $strandName,
                'section' => $sectionName,
                'adviser' => $adviserName,
                'ay_status' => $ay?->academic_status,
                'students_count' => (int) ($a->students_count ?? 0),
            ];
        });

        return view('teacher.class_records.index', [
            'activeYear' => $activeYear,
            'rows' => $rows,
        ]);
    }
    public function show(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $assignment->load(['academicYear', 'strand', 'subject', 'teacher']);

        // Resolve Section and Adviser for details card (prefer adviser's section for this teacher)
        $sectionName = null; $grade = null; $adviserName = null;
        $sectionQuery = AcademicYearStrandSection::with('section', 'adviserTeacher')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $assignment->strand_id);
        $preferred = (clone $sectionQuery)->where('adviser_teacher_id', $assignment->teacher_id)->first();
        $sectionAssignment = $preferred ?: $sectionQuery->first();
        if ($sectionAssignment) {
            $sectionName = optional($sectionAssignment->section)->name;
            $grade = optional($sectionAssignment->section)->grade;
            $adviserName = optional($sectionAssignment->adviserTeacher)->last_name
                ? ($sectionAssignment->adviserTeacher->last_name . ', ' . $sectionAssignment->adviserTeacher->first_name)
                : null;
        }

        $subjectTeacher = $assignment->teacher?->last_name
            ? ($assignment->teacher->last_name . ', ' . $assignment->teacher->first_name)
            : null;

        $enrollments = SubjectEnrollment::with(['studentEnrollment.student'])
            ->where('academic_year_strand_subject_id', $assignment->id)
            ->get();

        $students = $enrollments->map(function ($se) {
            $student = optional($se->studentEnrollment)->student;
            if (!$student) return null;
            return [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'name' => $student->last_name . ', ' . $student->first_name . ($student->middle_name ? ' ' . mb_substr($student->middle_name, 0, 1) . '.' : ''),
                'last_name' => $student->last_name,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'gender' => $student->gender,
                'status' => $student->status,
            ];
        })->filter();

        $boys = $students->where('gender', 'male')->values();
        $girls = $students->where('gender', 'female')->values();

        $counts = [
            'total' => $students->count(),
            'male' => $boys->count(),
            'female' => $girls->count(),
            'active' => $students->where('status', 'active')->count(),
            'graduated' => $students->where('status', 'graduated')->count(),
            'dropped' => $students->where('status', 'dropped')->count(),
        ];

        // Combined, sorted list for class record tabs
        $studentsAll = $students->sortBy([['last_name', 'asc'], ['first_name', 'asc']])->values();

        // Build summarized per-term overview (view-only)
        $termToQuarter = [
            'first-semester' => '1st',
            'second-semester' => '2nd',
            'semester-final' => null,
        ];

        $weights = [
            'ww' => (float) (($assignment->written_works_percentage ?? 0) / 100),
            'pt' => (float) (($assignment->performance_tasks_percentage ?? 0) / 100),
            'qa' => (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100),
        ];

        $studentsList = $studentsAll; // collection of arrays
        $studentIds = $studentsList->pluck('id')->all();

        $termSummaries = [];
        foreach ($termToQuarter as $termKey => $quarter) {
            $recQ = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id);
            if (is_null($quarter)) { $recQ->whereNull('quarter'); } else { $recQ->where('quarter', $quarter); }
            $recs = $recQ->orderBy('date_given')->orderBy('id')->get();

            $ww = $recs->where('type', 'written work')->values();
            $pt = $recs->where('type', 'performance task')->values();
            $qa = $recs->where('type', 'quarterly assessment')->values();

            $wwMax = (float) $ww->sum('max_score');
            $ptMax = (float) $pt->sum('max_score');
            $qaMax = (float) $qa->sum('max_score');

            // Load all results for these records and students
            $resByRecStu = [];
            if (!empty($studentIds) && $recs->isNotEmpty()) {
                $results = SubjectRecordResult::whereIn('subject_record_id', $recs->pluck('id')->all())
                    ->whereIn('student_id', $studentIds)
                    ->get(['subject_record_id','student_id','raw_score']);
                foreach ($results as $r) { $resByRecStu[$r->subject_record_id][$r->student_id] = (float) ($r->raw_score ?? 0); }
            }

            $perStudent = [];
            foreach ($studentsList as $stu) {
                $sid = $stu['id'];
                $sumRaw = function($col) use ($resByRecStu, $sid) {
                    return (float) $col->sum(function($rec) use ($resByRecStu, $sid){ return $resByRecStu[$rec->id][$sid] ?? 0; });
                };
                $wwRaw = $sumRaw($ww); $ptRaw = $sumRaw($pt); $qaRaw = $sumRaw($qa);
                $wwPS = $wwMax > 0 ? ($wwRaw / $wwMax) * 100 : null;
                $ptPS = $ptMax > 0 ? ($ptRaw / $ptMax) * 100 : null;
                $qaPS = $qaMax > 0 ? ($qaRaw / $qaMax) * 100 : null;
                $wwWS = isset($wwPS) ? $wwPS * $weights['ww'] : null;
                $ptWS = isset($ptPS) ? $ptPS * $weights['pt'] : null;
                $qaWS = isset($qaPS) ? $qaPS * $weights['qa'] : null;
                $initial = (isset($wwWS) ? $wwWS : 0) + (isset($ptWS) ? $ptWS : 0) + (isset($qaWS) ? $qaWS : 0);

                $perStudent[$sid] = [
                    'wwRaw' => $wwRaw, 'wwPS' => $wwPS, 'wwWS' => $wwWS,
                    'ptRaw' => $ptRaw, 'ptPS' => $ptPS, 'ptWS' => $ptWS,
                    'qaRaw' => $qaRaw, 'qaPS' => $qaPS, 'qaWS' => $qaWS,
                    'initialTotal' => $initial,
                ];
            }

            $termSummaries[$termKey] = [
                'wwMaxTotal' => $wwMax,
                'ptMaxTotal' => $ptMax,
                'qaMaxTotal' => $qaMax,
                'perStudent' => $perStudent,
            ];
        }

        return view('teacher.class_records.show', [
            'assignment' => $assignment,
            'counts' => $counts,
            'boys' => $boys,
            'girls' => $girls,
            'students' => $studentsAll,
            'classDetails' => [
                'strand' => $assignment->strand?->name,
                'section' => $sectionName,
                'grade' => $grade,
                'subject' => $assignment->subject?->name,
                'subject_code' => $assignment->subject?->code,
                'subject_teacher' => $subjectTeacher,
                'adviser' => $adviserName,
                'school_year' => $assignment->academicYear?->name,
                'semester' => $assignment->academicYear?->semester,
            ],
            'termSummaries' => $termSummaries,
            'weights' => $weights,
        ]);
    }

    public function studentShow(Request $request, AcademicYearStrandSubject $assignment, Student $student)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        
        Log::info('studentShow - Authorization check', [
            'assignment_id' => $assignment->id,
            'assignment_teacher_id' => $assignment->teacher_id,
            'user_pk_id' => $user->user_pk_id,
            'match' => $assignment->teacher_id === $user->user_pk_id
        ]);
        
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        // Note: We no longer hard-block students who don't yet have a SubjectEnrollment row
        // for this specific subject. Teachers may need to input scores first; final grades can
        // be synced later. We still keep authorization on the assignment's teacher.

        $assignment->load(['academicYear', 'strand', 'subject', 'teacher']);

        // Resolve Section and Adviser (prefer adviser's section for this teacher)
        $sectionName = null; $grade = null; $adviserName = null;
        
        $sectionQuery = AcademicYearStrandSection::with('section', 'adviserTeacher')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $assignment->strand_id);

        $preferred = (clone $sectionQuery)->where('adviser_teacher_id', $assignment->teacher_id)->first();
        $sectionAssignment = $preferred ?: $sectionQuery->first();
        if ($sectionAssignment) {
            $sectionName = optional($sectionAssignment->section)->name;
            $grade = optional($sectionAssignment->section)->grade;
            $adviserName = optional($sectionAssignment->adviserTeacher)->last_name
                ? ($sectionAssignment->adviserTeacher->last_name . ', ' . $sectionAssignment->adviserTeacher->first_name)
                : null;
        }

        $subjectTeacher = $assignment->teacher?->last_name
            ? ($assignment->teacher->last_name . ', ' . $assignment->teacher->first_name)
            : null;

        // Get optional filters from query parameters
        $selectedGrade = $request->query('grade_level');
        $selectedTerm = $request->query('term'); // 'midterm' or 'finals'

        // Load all SubjectRecords for this assignment with optional filters
        $recordsQuery = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id);
        
        // Apply grade level filter if specified
        if ($selectedGrade) {
            $recordsQuery->where('grade_level', $selectedGrade);
        }
        
        // Apply term filter if specified
        if ($selectedTerm && in_array($selectedTerm, ['midterm', 'finals'])) {
            $recordsQuery->where('term', $selectedTerm);
        }
        
        $allRecords = $recordsQuery->orderBy('date_given')
            ->orderBy('id')
            ->get();

        // Get grade level from the records (use the first available grade_level)
        $recordGrade = $allRecords->first()->grade_level ?? $grade;
        if ($recordGrade && is_numeric($recordGrade)) {
            $recordGrade = 'G-' . $recordGrade;
        }

        $details = [
            'strand' => $assignment->strand?->name,
            'section' => $sectionName,
            'grade' => $recordGrade,
            'subject' => $assignment->subject?->name,
            'subject_teacher' => $subjectTeacher,
            'adviser' => $adviserName,
            'school_year' => $assignment->academicYear?->name,
            'semester' => $assignment->academicYear?->semester,
        ];

        // Fetch this student's scores across all records in one go
        $scores = [];
        if ($allRecords->isNotEmpty()) {
            $results = SubjectRecordResult::whereIn('subject_record_id', $allRecords->pluck('id')->all())
                ->where('student_id', $student->id)
                ->get();
            foreach ($results as $res) {
                $scores[$res->subject_record_id] = (float) ($res->raw_score ?? 0);
            }
        }

        // Group records by quarter and type
        $quarters = [
            '1st' => 'First Semester',
            '2nd' => 'Second Semester',
        ];

        $wwWeight = (float) (($assignment->written_works_percentage ?? 0) / 100);
        $ptWeight = (float) (($assignment->performance_tasks_percentage ?? 0) / 100);
        $qaWeight = (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100);

        $quartersData = [];
        $semesterGrades = []; // Store semester grades for final computation
        
        foreach ($quarters as $qKey => $qLabel) {
            // Filter by quarter (semester) and optionally by term
            $qRecs = $allRecords->filter(function ($r) use ($qKey, $selectedTerm) {
                $matchesQuarter = $r->quarter === $qKey;
                
                // If term filter is active, also check term matches
                if ($selectedTerm && in_array($selectedTerm, ['midterm', 'finals'])) {
                    return $matchesQuarter && $r->term === $selectedTerm;
                }
                
                return $matchesQuarter;
            })->values();

            $ww = $qRecs->where('type', 'written work')->values();
            $pt = $qRecs->where('type', 'performance task')->values();
            $qa = $qRecs->where('type', 'quarterly assessment')->values();

            $wwMax = (float) $ww->sum('max_score');
            $ptMax = (float) $pt->sum('max_score');
            $qaMax = (float) $qa->sum('max_score');

            $wwRaw = (float) $ww->sum(function ($r) use ($scores) { return $scores[$r->id] ?? 0; });
            $ptRaw = (float) $pt->sum(function ($r) use ($scores) { return $scores[$r->id] ?? 0; });
            $qaRaw = (float) $qa->sum(function ($r) use ($scores) { return $scores[$r->id] ?? 0; });

            $wwPS = $wwMax > 0 ? ($wwRaw / $wwMax) * 100 : null;
            $ptPS = $ptMax > 0 ? ($ptRaw / $ptMax) * 100 : null;
            $qaPS = $qaMax > 0 ? ($qaRaw / $qaMax) * 100 : null;

            $wwWS = isset($wwPS) ? $wwPS * $wwWeight : null;
            $ptWS = isset($ptPS) ? $ptPS * $ptWeight : null;
            $qaWS = isset($qaPS) ? $qaPS * $qaWeight : null;

            $initial = (isset($wwWS) ? $wwWS : 0) + (isset($ptWS) ? $ptWS : 0) + (isset($qaWS) ? $qaWS : 0);

            // Store semester grades for final computation
            if ($qKey === '1st' || $qKey === '2nd') {
                $semesterGrades[$qKey] = $initial;
            }

            // Description mapping using provided logic
            $desc = '0';
            if ($initial > 89) {
                $desc = 'Outstanding';
            } elseif ($initial > 84) {
                $desc = 'Very Satisfactory';
            } elseif ($initial > 79) {
                $desc = 'Satisfactory';
            } elseif ($initial > 74) {
                $desc = 'Fairly Satisfactory';
            } elseif ($initial > 59) {
                $desc = 'Did Not Meet Expectations';
            }

            $mapRecords = function ($collection) use ($scores) {
                return $collection->map(function ($r) use ($scores) {
                    return [
                        'id' => $r->id,
                        'name' => $r->name,
                        'date' => optional($r->date_given)->format('Y-m-d'),
                        'day' => optional($r->date_given)->format('l'),
                        'max' => (float) $r->max_score,
                        'score' => (float) ($scores[$r->id] ?? 0),
                        'description' => $r->description,
                    ];
                })->values();
            };

            $quartersData[] = [
                'key' => $qKey,
                'label' => $qLabel,
                'ww' => [
                    'records' => $mapRecords($ww),
                    'max_total' => $wwMax,
                    'raw_total' => $wwRaw,
                    'ps' => $wwPS,
                    'ws' => $wwWS,
                ],
                'pt' => [
                    'records' => $mapRecords($pt),
                    'max_total' => $ptMax,
                    'raw_total' => $ptRaw,
                    'ps' => $ptPS,
                    'ws' => $ptWS,
                ],
                'qa' => [
                    'records' => $mapRecords($qa),
                    'max_total' => $qaMax,
                    'raw_total' => $qaRaw,
                    'ps' => $qaPS,
                    'ws' => $qaWS,
                ],
                'initial' => [
                    'total' => $initial,
                    'description' => $desc,
                ],
            ];
        }

        // Compute final grade as average of first and second semester
        $finalGrade = null;
        $finalDescription = '0';
        if (isset($semesterGrades['1st']) && isset($semesterGrades['2nd'])) {
            $finalGrade = ($semesterGrades['1st'] + $semesterGrades['2nd']) / 2;
            if ($finalGrade > 89) {
                $finalDescription = 'Outstanding';
            } elseif ($finalGrade > 84) {
                $finalDescription = 'Very Satisfactory';
            } elseif ($finalGrade > 79) {
                $finalDescription = 'Satisfactory';
            } elseif ($finalGrade > 74) {
                $finalDescription = 'Fairly Satisfactory';
            } elseif ($finalGrade > 59) {
                $finalDescription = 'Did Not Meet Expectations';
            }
        }

        return view('teacher.class_records.student_show', [
            'assignment' => $assignment,
            'student' => $student,
            'details' => $details,
            'quartersData' => $quartersData,
            'weights' => [
                'ww' => $wwWeight,
                'pt' => $ptWeight,
                'qa' => $qaWeight,
            ],
            'finalGrade' => $finalGrade,
            'finalDescription' => $finalDescription,
            'semesterGrades' => $semesterGrades,
            'selectedTerm' => $selectedTerm, // Pass selected term to view
            'selectedGrade' => $selectedGrade, // Pass selected grade to view
        ]);
    }

    /**
     * Show a full-page form to create a new student record scoped to this assignment.
     * Accepts optional query params: grade_level and term to prefill the form.
     */
    public function createStudent(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $gradeLevel = $request->query('grade_level');
        $term = $request->query('term');

        return view('teacher.class_records.students.create', [
            'assignment' => $assignment,
            'grade_level' => $gradeLevel,
            'term' => $term,
        ]);
    }

    /**
     * Store a new minimal Student record and redirect to the student's class-record page.
     * This keeps the implementation intentionally minimal: teachers can refine the student data later.
     */
    public function storeStudent(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $data = $request->validate([
            'last_name' => ['required','string','max:255'],
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'grade_level' => ['nullable','string','max:10'],
            'term' => ['nullable','string','max:50'],
        ]);

        // Generate a temporary, unique student number
        $studentNumber = 'TMP' . now()->format('YmdHis') . rand(100,999);

        $student = new Student();
        $student->student_number = $studentNumber;
        $student->last_name = $data['last_name'];
        $student->first_name = $data['first_name'];
        $student->middle_name = $data['middle_name'] ?? null;
        $student->status = 'active';
        $student->save();

        // If a term was provided, create placeholder assessment records for that term
        $termRaw = strtolower(trim($data['term'] ?? ''));
        if (!empty($termRaw)) {
            // Map common term strings to quarter values used by SubjectRecord
            $quarter = null;
            if (str_contains($termRaw, 'mid') || str_contains($termRaw, 'first') || str_contains($termRaw, '1st')) {
                $quarter = '1st';
            } elseif (str_contains($termRaw, 'final') || str_contains($termRaw, 'second') || str_contains($termRaw, '2nd')) {
                $quarter = '2nd';
            }

            $now = now()->toDateString();
            $desc = 'Auto-generated placeholder assessment for term: ' . ($data['term'] ?? $termRaw);

            // create one placeholder per assessment category
            try {
                $placeholders = [
                    ['type' => 'written work', 'name' => 'WW (auto)'],
                    ['type' => 'performance task', 'name' => 'PT (auto)'],
                    ['type' => 'quarterly assessment', 'name' => 'QA (auto)'],
                ];
                foreach ($placeholders as $ph) {
                    \App\Models\SubjectRecord::create([
                        'academic_year_strand_subject_id' => $assignment->id,
                        'name' => $ph['name'] . ' - ' . now()->format('YmdHis'),
                        'description' => $desc,
                        'max_score' => 100,
                        'type' => $ph['type'],
                        'quarter' => $quarter,
                        'date_given' => $now,
                        'remarks' => null,
                    ]);
                }
            } catch (\Exception $e) {
                // Log but do not block student creation if placeholder creation fails
                Log::warning('Failed to create placeholder assessments: ' . $e->getMessage());
            }
        }

        // Redirect to the student's class-record page (studentShow)
        return redirect()->route('teacher.class-records.students.show', ['assignment' => $assignment->id, 'student' => $student->id])
            ->with('success', 'Student record created. Placeholder assessments were added for the selected term. You can refine details later.');
    }

    /**
     * Create placeholder SubjectRecord entries for a given student and term.
     * This is triggered by the 'New Entry' modal to quickly add assessments for another term.
     */
    public function addStudentTerm(Request $request, AcademicYearStrandSubject $assignment, Student $student)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $data = $request->validate([
            'grade_level' => ['nullable','string','max:10'],
            'term' => ['required','string','max:50'],
        ]);

        $termRaw = strtolower(trim($data['term'] ?? ''));
        $quarter = null;
        if (str_contains($termRaw, 'mid') || str_contains($termRaw, 'first') || str_contains($termRaw, '1st')) {
            $quarter = '1st';
        } elseif (str_contains($termRaw, 'final') || str_contains($termRaw, 'second') || str_contains($termRaw, '2nd')) {
            $quarter = '2nd';
        }

        $now = now()->toDateString();
        $desc = 'Auto-generated placeholder assessment for term: ' . ($data['term'] ?? $termRaw) . ' (created for student: ' . ($student->last_name . ', ' . $student->first_name) . ')';

        try {
            $placeholders = [
                ['type' => 'written work', 'name' => 'WW (auto)'],
                ['type' => 'performance task', 'name' => 'PT (auto)'],
                ['type' => 'quarterly assessment', 'name' => 'QA (auto)'],
            ];
            foreach ($placeholders as $ph) {
                $sr = SubjectRecord::create([
                    'academic_year_strand_subject_id' => $assignment->id,
                    'name' => $ph['name'] . ' - ' . now()->format('YmdHis'),
                    'description' => $desc,
                    'max_score' => 100,
                    'type' => $ph['type'],
                    'quarter' => $quarter,
                    'date_given' => $now,
                    'remarks' => null,
                ]);

                // Create per-student placeholder result so the student immediately has a row to accept scores
                try {
                    \App\Models\SubjectRecordResult::create([
                        'subject_record_id' => $sr->id,
                        'student_id' => $student->id,
                        'raw_score' => 0,
                        'base_score' => null,
                        'final_score' => null,
                        'remarks' => null,
                        'description' => null,
                        'date_submitted' => now(),
                    ]);
                } catch (\Exception $e) {
                    // Log and continue; missing result row is non-blocking for the placeholder creation
                    Log::warning('Failed to create SubjectRecordResult for student ' . $student->id . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to create placeholder assessments for addStudentTerm: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create placeholder assessments.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Placeholder assessments created.']);
    }

    public function termShow(Request $request, AcademicYearStrandSubject $assignment, string $term)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $validTerms = ['first-semester', 'second-semester', 'semester-final'];
        if (!in_array($term, $validTerms, true)) { abort(404); }

        $assignment->load(['academicYear', 'strand', 'subject', 'teacher']);

        // Get sections that this teacher handles (adviser or has students enrolled in their subject)
        $allSections = AcademicYearStrandSection::with('section', 'adviserTeacher')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $assignment->strand_id)
            ->where(function($q) use ($user, $assignment) {
                // Teacher is adviser of the section
                $q->where('adviser_teacher_id', $user->user_pk_id)
                  // OR there are students enrolled in this teacher's subject from this section
                  ->orWhereHas('studentEnrollments.subjectEnrollments', function($qq) use ($assignment) {
                      $qq->where('academic_year_strand_subject_id', $assignment->id);
                  });
            })
            ->get()
            ->map(function($ayss) {
                return [
                    'id' => $ayss->id,
                    'name' => optional($ayss->section)->name,
                ];
            })
            ->filter(function($sec) {
                return !empty($sec['name']);
            })
            ->unique('id')
            ->values();

        // Get selected section from query parameter
        $selectedSectionId = $request->query('section');

        // Resolve section/adviser similar to show()
        $sectionName = null; $grade = null; $adviserName = null;
        $sectionQuery = AcademicYearStrandSection::with('section', 'adviserTeacher')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $assignment->strand_id);
        
        // If a section is selected, use that specific section
        if ($selectedSectionId) {
            $sectionAssignment = $sectionQuery->where('id', $selectedSectionId)->first();
        } else {
            // Otherwise, prefer adviser's section for this teacher
            $preferred = (clone $sectionQuery)->where('adviser_teacher_id', $assignment->teacher_id)->first();
            $sectionAssignment = $preferred ?: $sectionQuery->first();
        }
        
        if ($sectionAssignment) {
            $sectionName = optional($sectionAssignment->section)->name;
            $grade = optional($sectionAssignment->section)->grade;
            $adviserName = optional($sectionAssignment->adviserTeacher)->last_name
                ? ($sectionAssignment->adviserTeacher->last_name . ', ' . $sectionAssignment->adviserTeacher->first_name)
                : null;
        }

        $subjectTeacher = $assignment->teacher?->last_name
            ? ($assignment->teacher->last_name . ', ' . $assignment->teacher->first_name)
            : null;

        $details = [
            'strand' => $assignment->strand?->name,
            'section' => $sectionName,
            'grade' => $grade,
            'subject' => $assignment->subject?->name,
            'subject_code' => $assignment->subject?->code,
            'subject_teacher' => $subjectTeacher,
            'adviser' => $adviserName,
            'school_year' => $assignment->academicYear?->name,
            'semester' => $assignment->academicYear?->semester,
        ];

        // Fetch enrollments and filter by section if selected
        $enrollmentsQuery = SubjectEnrollment::with(['studentEnrollment.student', 'studentEnrollment.academicYearStrandSection'])
            ->where('academic_year_strand_subject_id', $assignment->id);

        // If a section is selected, filter students by that section
        if ($selectedSectionId) {
            $enrollmentsQuery->whereHas('studentEnrollment', function($q) use ($selectedSectionId) {
                $q->where('academic_year_strand_section_id', $selectedSectionId);
            });
        }

        $enrollments = $enrollmentsQuery->get();

        $students = $enrollments->map(function ($se) {
            $student = optional($se->studentEnrollment)->student;
            if (!$student) return null;
            return [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'name' => $student->last_name . ', ' . $student->first_name . ($student->middle_name ? ' ' . mb_substr($student->middle_name, 0, 1) . '.' : ''),
                'last_name' => $student->last_name,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'gender' => $student->gender,
                'status' => $student->status,
            ];
        })->filter()->sortBy([['last_name', 'asc'], ['first_name', 'asc']])->values();

        // Aggregate counts for header stats
        $counts = [
            'total' => $students->count(),
            'male' => $students->where('gender', 'male')->count(),
            'female' => $students->where('gender', 'female')->count(),
            'active' => $students->where('status', 'active')->count(),
            'graduated' => $students->where('status', 'graduated')->count(),
            'dropped' => $students->where('status', 'dropped')->count(),
        ];

        $labels = [
            'first-semester' => 'First Semester',
            'second-semester' => 'Second Semester',
            'semester-final' => 'Semester Final Grade',
        ];

        // Map term to quarter and load SubjectRecords for this assignment and quarter
        $quarterMap = [
            'first-semester' => '1st',
            'second-semester' => '2nd',
            'semester-final' => null,
        ];
        $quarter = $quarterMap[$term] ?? null;

        $recordsQuery = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id);
        if (is_null($quarter)) {
            $recordsQuery->whereNull('quarter');
        } else {
            $recordsQuery->where('quarter', $quarter);
        }
        $allRecords = $recordsQuery->orderBy('date_given')->orderBy('id')->get();

        $wwRecords = $allRecords->where('type', 'written work')->values();
        $ptRecords = $allRecords->where('type', 'performance task')->values();
        $qaRecords = $allRecords->where('type', 'quarterly assessment')->values();

        // Load per-student scores for these records
        $studentIds = $students->pluck('id')->all();
        $scoresByRecord = [];
        if (!empty($studentIds) && $allRecords->isNotEmpty()) {
            $results = SubjectRecordResult::whereIn('subject_record_id', $allRecords->pluck('id')->all())
                ->whereIn('student_id', $studentIds)
                ->get();
            foreach ($results as $res) {
                $scoresByRecord[$res->subject_record_id][$res->student_id] = [
                    'raw_score' => $res->raw_score,
                    'base_score' => $res->base_score,
                    'final_score' => $res->final_score,
                ];
            }
        }

        // When rendering the Semester Final view, we also need the Initial Grade per student
        // for the First and Second semesters, to compute their average and final grade.
        $firstSemInitials = [];
        $secondSemInitials = [];
        if ($term === 'semester-final') {
            $weights = [
                'ww' => (float) (($assignment->written_works_percentage ?? 0) / 100),
                'pt' => (float) (($assignment->performance_tasks_percentage ?? 0) / 100),
                'qa' => (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100),
            ];

            $computeInitials = function ($quarterKey) use ($assignment, $studentIds, $weights) {
                $query = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id)
                    ->orderBy('date_given')->orderBy('id');
                if ($quarterKey === null) { $query->whereNull('quarter'); } else { $query->where('quarter', $quarterKey); }
                $recs = $query->get();
                if ($recs->isEmpty() || empty($studentIds)) return [];

                $ww = $recs->where('type', 'written work')->values();
                $pt = $recs->where('type', 'performance task')->values();
                $qa = $recs->where('type', 'quarterly assessment')->values();

                $wwMax = (float) $ww->sum('max_score');
                $ptMax = (float) $pt->sum('max_score');
                $qaMax = (float) $qa->sum('max_score');

                $results = SubjectRecordResult::whereIn('subject_record_id', $recs->pluck('id')->all())
                    ->whereIn('student_id', $studentIds)
                    ->get(['subject_record_id','student_id','raw_score']);
                $resByRecStu = [];
                foreach ($results as $r) { $resByRecStu[$r->subject_record_id][$r->student_id] = (float) ($r->raw_score ?? 0); }

                $initials = [];
                foreach ($studentIds as $sid) {
                    $sumRaw = function($col) use ($resByRecStu, $sid) {
                        return (float) $col->sum(function($rec) use ($resByRecStu, $sid){ return $resByRecStu[$rec->id][$sid] ?? 0; });
                    };
                    $wwRaw = $sumRaw($ww); $ptRaw = $sumRaw($pt); $qaRaw = $sumRaw($qa);
                    $wwPS = $wwMax > 0 ? ($wwRaw / $wwMax) * 100 : null;
                    $ptPS = $ptMax > 0 ? ($ptRaw / $ptMax) * 100 : null;
                    $qaPS = $qaMax > 0 ? ($qaRaw / $qaMax) * 100 : null;
                    $wwWS = isset($wwPS) ? $wwPS * $weights['ww'] : null;
                    $ptWS = isset($ptPS) ? $ptPS * $weights['pt'] : null;
                    $qaWS = isset($qaPS) ? $qaPS * $weights['qa'] : null;
                    $initial = (isset($wwWS) ? $wwWS : 0) + (isset($ptWS) ? $ptWS : 0) + (isset($qaWS) ? $qaWS : 0);
                    $initials[$sid] = $initial;
                }
                return $initials;
            };

            $firstSemInitials = $computeInitials('1st');
            $secondSemInitials = $computeInitials('2nd');
        }

        return view('teacher.class_records.term_show', [
            'assignment' => $assignment,
            'details' => $details,
            'students' => $students,
            'counts' => $counts,
            'term' => $term,
            'termLabel' => $labels[$term],
            'wwRecords' => $wwRecords,
            'ptRecords' => $ptRecords,
            'qaRecords' => $qaRecords,
            'wwMaxTotal' => (float) $wwRecords->sum('max_score'),
            'ptMaxTotal' => (float) $ptRecords->sum('max_score'),
            'qaMaxTotal' => (float) $qaRecords->sum('max_score'),
            'scoresByRecord' => $scoresByRecord,
            'firstSemInitials' => $firstSemInitials,
            'secondSemInitials' => $secondSemInitials,
            'sections' => $allSections,
        ]);
    }

    public function storeAssessment(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $data = $request->validate([
            'term' => ['nullable', 'in:first-semester,second-semester,semester-final'],
            'term_type' => ['nullable', 'in:midterm,finals'], // New term type field
            'grade_level' => ['nullable', 'string', 'max:10'], // Grade level field
            'category' => ['required', 'in:written_work,performance_task,quarterly_assessment'],
            // name/date/max are optional; modal may omit them
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_given' => ['nullable', 'date'],
            'max_score' => ['nullable', 'numeric', 'min:1'],
        ]);

        // Provide sensible defaults for omitted fields
        if (empty($data['name'])) {
            // Generate a simple default name from category and timestamp
            $catLabel = isset($data['category']) ? str_replace('_', ' ', $data['category']) : 'Assessment';
            $data['name'] = ucfirst($catLabel) . ' - ' . now()->format('YmdHis');
        }
        if (empty($data['date_given'])) {
            $data['date_given'] = now()->toDateString();
        }
        if (empty($data['max_score'])) {
            $data['max_score'] = 100;
        }

        // Map category/term to admin SubjectRecord fields
        $typeMap = [
            'written_work' => 'written work',
            'performance_task' => 'performance task',
            'quarterly_assessment' => 'quarterly assessment',
        ];
        $quarterMap = [
            'first-semester' => '1st',
            'second-semester' => '2nd',
            'semester-final' => null,
        ];

    $type = $typeMap[$data['category']];
    $quarter = isset($data['term']) ? ($quarterMap[$data['term']] ?? null) : null;
    $termType = $data['term_type'] ?? null; // Get the term type (midterm/finals)
    $gradeLevel = $data['grade_level'] ?? null; // Get the grade level

            // Create a single SubjectRecord linked to this assignment
            SubjectRecord::create([
                'academic_year_strand_subject_id' => $assignment->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'max_score' => $data['max_score'],
                'type' => $type,
                'quarter' => $quarter,
                'term' => $termType, // Store the term type
                'grade_level' => $gradeLevel, // Store the grade level
                'date_given' => $data['date_given'],
                'remarks' => null,
            ]);

        return back()->with('success', 'Assessment added successfully.');
    }

    /**
     * Update an existing assessment (SubjectRecord) scoped to this teacher's assignment.
     */
    public function updateAssessment(Request $request, AcademicYearStrandSubject $assignment, SubjectRecord $subjectRecord)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        if ($subjectRecord->academic_year_strand_subject_id !== $assignment->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'date_given' => ['nullable','date'],
            'max_score' => ['required','numeric','min:1'],
        ]);

        $subjectRecord->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'date_given' => $data['date_given'] ?? $subjectRecord->date_given,
            'max_score' => $data['max_score'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Assessment updated successfully.');
    }

    /**
     * Delete an existing assessment (SubjectRecord) scoped to this teacher's assignment.
     */
    public function destroyAssessment(Request $request, AcademicYearStrandSubject $assignment, SubjectRecord $subjectRecord)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        if ($subjectRecord->academic_year_strand_subject_id !== $assignment->id) {
            abort(404);
        }

        // Store the name for the success message
        $assessmentName = $subjectRecord->name;

        // Delete the assessment
        $subjectRecord->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Assessment deleted successfully.']);
        }

        return back()->with('success', 'Assessment "' . $assessmentName . '" deleted successfully.');
    }

    /**
     * Show a full-page create assessment form. Prefills grade_level and term when provided via query string.
     */
    public function createAssessment(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $gradeLevel = $request->query('grade_level');
        $term = $request->query('term'); // expected values: first-semester, semester-final

        return view('teacher.class_records.assessments.create', [
            'assignment' => $assignment,
            'grade_level' => $gradeLevel,
            'term' => $term,
        ]);
    }

    public function storeScores(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        // Expect nested array: scores[subject_record_id][student_id] = int
        $data = $request->validate([
            'scores' => ['array'],
        ]);

        $scores = $data['scores'] ?? [];

        if (empty($scores)) {
            return back()->with('success', 'No changes to save.');
        }

        // Load records for this assignment to validate ownership and get max_score
        $recordIds = array_map('intval', array_keys($scores));
        $records = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id)
            ->whereIn('id', $recordIds)
            ->get(['id','max_score']);
        $recordsById = $records->keyBy('id');

        // Determine eligible students to accept scores for.
        // Prefer subject-specific enrollments; if absent, fall back to AY+Strand (and Section if mapped).
        $subjectEnrolledIds = SubjectEnrollment::with('studentEnrollment')
            ->where('academic_year_strand_subject_id', $assignment->id)
            ->get()
            ->map(function ($se) { return optional($se->studentEnrollment)->student_id; })
            ->filter()->unique()->all();

        $eligibleIds = $subjectEnrolledIds;

        if (empty($eligibleIds)) {
            // Fallback to all students enrolled in this AY + Strand (+ Section when specified)
            $eligibleIds = StudentEnrollment::query()
                ->where('academic_year_id', $assignment->academic_year_id)
                ->where('strand_id', $assignment->strand_id)
                ->when($assignment->academic_year_strand_section_id, function($q) use ($assignment){
                    $q->where('academic_year_strand_section_id', $assignment->academic_year_strand_section_id);
                })
                ->pluck('student_id')
                ->filter()
                ->unique()
                ->all();
        }

        $eligibleSet = array_flip($eligibleIds);

        $now = now();
        $upserts = [];
        foreach ($scores as $recordId => $row) {
            $recordId = (int)$recordId;
            $rec = $recordsById->get($recordId);
            if (!$rec) continue; // skip records not in this assignment
            $max = (float)$rec->max_score;

            foreach ((array)$row as $studentId => $val) {
                $studentId = (int)$studentId;
                if (!isset($eligibleSet[$studentId])) continue; // skip students not eligible for this class context
                // Sanitize to integer 0..max
                $raw = is_numeric($val) ? (int)$val : 0;
                if ($raw < 0) $raw = 0;
                if ($max > 0 && $raw > $max) $raw = (int)$max;

                $upserts[] = [
                    'subject_record_id' => $recordId,
                    'student_id' => $studentId,
                    'raw_score' => $raw,
                    'base_score' => null,
                    'final_score' => null,
                    'remarks' => null,
                    'description' => null,
                    'date_submitted' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ];
            }
        }

        if (!empty($upserts)) {
            // Upsert based on unique pair (subject_record_id, student_id)
            SubjectRecordResult::upsert(
                $upserts,
                ['subject_record_id','student_id'],
                ['raw_score','base_score','final_score','remarks','description','date_submitted','updated_at']
            );
        }

        return back()->with('success', 'Scores saved successfully.');
    }

    public function submitFinalGrades(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        // Load enrolled students for this assignment
        $enrollments = SubjectEnrollment::with('studentEnrollment')
            ->where('academic_year_strand_subject_id', $assignment->id)
            ->get();

        $students = $enrollments->map(function ($se) {
            $student = optional($se->studentEnrollment)->student;
            return $student ? (int)$student->id : null;
        })->filter()->unique()->values();

        // Compute initial totals per student for 1st and 2nd using same logic as show()
        $weights = [
            'ww' => (float) (($assignment->written_works_percentage ?? 0) / 100),
            'pt' => (float) (($assignment->performance_tasks_percentage ?? 0) / 100),
            'qa' => (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100),
        ];

        $studentIds = $students->map(fn($id) => (int)$id)->values();

        $computeInitials = function ($quarterKey) use ($assignment, $studentIds, $weights) {
            $recs = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id)
                ->when($quarterKey === null, function($q){ $q->whereNull('quarter'); }, function($q) use ($quarterKey){ $q->where('quarter', $quarterKey); })
                ->orderBy('date_given')->orderBy('id')->get();
            if ($recs->isEmpty()) return [];
            $ww = $recs->where('type', 'written work')->values();
            $pt = $recs->where('type', 'performance task')->values();
            $qa = $recs->where('type', 'quarterly assessment')->values();
            $wwMax = (float) $ww->sum('max_score');
            $ptMax = (float) $pt->sum('max_score');
            $qaMax = (float) $qa->sum('max_score');
            $results = SubjectRecordResult::whereIn('subject_record_id', $recs->pluck('id')->all())
                ->whereIn('student_id', $studentIds->all())
                ->get(['subject_record_id','student_id','raw_score']);
            $resByRecStu = [];
            foreach ($results as $r) { $resByRecStu[$r->subject_record_id][$r->student_id] = (float) ($r->raw_score ?? 0); }
            $initials = [];
            foreach ($studentIds as $sid) {
                $sumRaw = function($col) use ($resByRecStu, $sid){ return (float) $col->sum(function($rec) use ($resByRecStu, $sid){ return $resByRecStu[$rec->id][$sid] ?? 0; }); };
                $wwRaw = $sumRaw($ww); $ptRaw = $sumRaw($pt); $qaRaw = $sumRaw($qa);
                $wwPS = $wwMax > 0 ? ($wwRaw / $wwMax) * 100 : null;
                $ptPS = $ptMax > 0 ? ($ptRaw / $ptMax) * 100 : null;
                $qaPS = $qaMax > 0 ? ($qaRaw / $qaMax) * 100 : null;
                $wwWS = isset($wwPS) ? $wwPS * $weights['ww'] : null;
                $ptWS = isset($ptPS) ? $ptPS * $weights['pt'] : null;
                $qaWS = isset($qaPS) ? $qaPS * $weights['qa'] : null;
                $initial = (isset($wwWS) ? $wwWS : 0) + (isset($ptWS) ? $ptWS : 0) + (isset($qaWS) ? $qaWS : 0);
                $initials[$sid] = $initial;
            }
            return $initials;
        };

        $first = $computeInitials('1st');
        $second = $computeInitials('2nd');

        // Upsert into SubjectEnrollment per student for this assignment
        $now = now();
        $upserts = [];
        foreach ($studentIds as $sid) {
            $fq = $first[$sid] ?? null;
            $sq = $second[$sid] ?? null;
            // Normalize and round semester initials to 2 decimals for consistent storage
            $fq = isset($fq) ? round((float)$fq, 2) : null;
            $sq = isset($sq) ? round((float)$sq, 2) : null;
            $avg = ($fq !== null && $sq !== null) ? round((($fq + $sq) / 2), 2) : null;
            $final = $avg;
            $remarks = $final !== null ? ($final >= 75 ? 'Passed' : 'Failed') : null;
            $desc = null;
            if ($final !== null) {
                if ($final >= 90) { $desc = 'Outstanding'; }
                elseif ($final >= 85) { $desc = 'Very Satisfactory'; }
                elseif ($final >= 80) { $desc = 'Satisfactory'; }
                elseif ($final >= 75) { $desc = 'Fairly Satisfactory'; }
                elseif ($final >= 60) { $desc = 'Did Not Meet Expectations'; }
                else { $desc = '—'; }
            }

            // Find the SubjectEnrollment row to update for this student and assignment
            $se = SubjectEnrollment::with('studentEnrollment')
                ->where('academic_year_strand_subject_id', $assignment->id)
                ->whereHas('studentEnrollment', function($q) use ($sid){ $q->where('student_id', $sid); })
                ->first();

            if ($se) {
                $se->update([
                    'fq_grade' => $fq,
                    'sq_grade' => $sq,
                    'a_grade' => $final,
                    'f_grade' => $final,
                    'remarks' => $remarks,
                    'description' => $desc,
                ]);
            } else {
                // Try to find the student's enrollment for this academic year and strand
                $studEnroll = StudentEnrollment::where('student_id', $sid)
                    ->where('academic_year_id', $assignment->academic_year_id)
                    ->where('strand_id', $assignment->strand_id)
                    ->first();
                if ($studEnroll) {
                    SubjectEnrollment::create([
                        'student_enrollment_id' => $studEnroll->id,
                        'academic_year_strand_subject_id' => $assignment->id,
                        'fq_grade' => $fq,
                        'sq_grade' => $sq,
                        'a_grade' => $final,
                        'f_grade' => $final,
                        'remarks' => $remarks,
                        'description' => $desc,
                    ]);
                }
            }
        }

        return back()->with('success', 'Final grades submitted successfully.');
    }

    /**
     * Publish/unpublish grades to make them visible to students
     */
    public function toggleGradesPublication(AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) {
            abort(401);
        }

        // Verify this teacher owns this assignment
        if ($assignment->teacher_id !== $user->user_pk_id) {
            abort(403, 'Unauthorized');
        }

        // Toggle the publication status
        $assignment->update([
            'grades_published' => !$assignment->grades_published
        ]);

        $status = $assignment->grades_published ? 'published' : 'unpublished';

        // If publishing now, notify guardians linked to enrolled students
        if ($assignment->grades_published) {
            try {
                // Eager load students and their guardians for this assignment
                $subjectEnrollments = $assignment->subjectEnrollments()
                    ->with(['studentEnrollment.student.guardians', 'academicYearStrandSubject.subject', 'studentEnrollment.academicYear'])
                    ->get();

                $smsEnabled = !empty(config('services.semaphore.api_key'));
                $smsService = $smsEnabled ? app(\App\Services\SemaphoreSmsService::class) : null;

                foreach ($subjectEnrollments as $se) {
                    $student = optional($se->studentEnrollment)->student;
                    if (!$student) { continue; }

                    $subjectName = optional($assignment->subject)->name ?? 'Subject';
                    $semester = '1st'; // default; if app tracks per term, adjust accordingly

                    // Build common message
                    $emailMailable = new GradePublishedNotification(
                        $student->first_name . ' ' . $student->last_name,
                        $subjectName,
                        optional($assignment->strand)->name ?? 'Strand',
                        optional($assignment->academicYear)->name ?? 'School Year',
                        $semester
                    );

                    $smsMessage = "SMAC: Grades published for $subjectName. Please log in to view.";

                    // Send to guardians via relationship table
                    if ($student->guardians && $student->guardians->count() > 0) {
                        foreach ($student->guardians as $guardian) {
                            if ($guardian->email && filter_var($guardian->email, FILTER_VALIDATE_EMAIL)) {
                                try { Mail::to($guardian->email)->send($emailMailable); } catch (\Throwable $e) { Log::warning('Guardian email send failed', ['guardian_id' => $guardian->id, 'error' => $e->getMessage()]); }
                            }
                            if ($smsEnabled && $smsService && !empty($guardian->mobile_number)) {
                                try { $smsService->sendSms($guardian->mobile_number, $smsMessage); } catch (\Throwable $e) { Log::warning('Guardian SMS send failed', ['guardian_number' => $guardian->mobile_number, 'error' => $e->getMessage()]); }
                            }
                        }
                    } else {
                        Log::info('No guardians linked via pivot for student', ['student_id' => $student->id]);
                    }

                    // Legacy fields support
                    if (!empty($student->guardian_email) && filter_var($student->guardian_email, FILTER_VALIDATE_EMAIL)) {
                        try { Mail::to($student->guardian_email)->send($emailMailable); } catch (\Throwable $e) { Log::warning('Legacy guardian email send failed', ['email' => $student->guardian_email, 'error' => $e->getMessage()]); }
                    }
                    if ($smsEnabled && $smsService && !empty($student->guardian_contact)) {
                        try { $smsService->sendSms($student->guardian_contact, $smsMessage); } catch (\Throwable $e) { Log::warning('Legacy guardian SMS send failed', ['number' => $student->guardian_contact, 'error' => $e->getMessage()]); }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch guardian notifications for grade publication', ['assignment_id' => $assignment->id, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', "Grades have been {$status} successfully. Students can now view their grades. Guardians have been notified.");
    }

    /**
     * End of school year - finalize records for the class
     */
    public function endOfSchoolYear(AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) {
            abort(401);
        }

        // Verify this teacher owns this assignment
        if ($assignment->teacher_id !== $user->user_pk_id) {
            abort(403, 'Unauthorized');
        }

        // Mark the school year as ended for this assignment
        $assignment->update([
            'school_year_ended' => true,
            'school_year_ended_at' => now(),
        ]);

        return back()->with('success', 'School year has been successfully ended for this class. All records are now finalized. Pre-enrollment button has been disabled for students in this class.');
    }
}
