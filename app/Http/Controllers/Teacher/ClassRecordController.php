<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\Student;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        // Ensure student is enrolled in this class record
        $isEnrolled = SubjectEnrollment::where('academic_year_strand_subject_id', $assignment->id)
            ->whereHas('studentEnrollment', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->exists();
        if (!$isEnrolled) { abort(404, 'Student not enrolled in this class record'); }

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

        $details = [
            'strand' => $assignment->strand?->name,
            'section' => $sectionName,
            'grade' => $grade,
            'subject' => $assignment->subject?->name,
            'subject_teacher' => $subjectTeacher,
            'adviser' => $adviserName,
            'school_year' => $assignment->academicYear?->name,
            'semester' => $assignment->academicYear?->semester,
        ];

        // Load all SubjectRecords for this assignment
        $allRecords = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id)
            ->orderBy('date_given')
            ->orderBy('id')
            ->get();

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
            null => 'Semester Final Grade',
        ];

        $wwWeight = (float) (($assignment->written_works_percentage ?? 0) / 100);
        $ptWeight = (float) (($assignment->performance_tasks_percentage ?? 0) / 100);
        $qaWeight = (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100);

        $quartersData = [];
        foreach ($quarters as $qKey => $qLabel) {
            $qRecs = $allRecords->filter(function ($r) use ($qKey) {
                return $r->quarter === $qKey;
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
        ]);
    }

    public function termShow(Request $request, AcademicYearStrandSubject $assignment, string $term)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $validTerms = ['first-semester', 'second-semester', 'semester-final'];
        if (!in_array($term, $validTerms, true)) { abort(404); }

        $assignment->load(['academicYear', 'strand', 'subject', 'teacher']);

        // Resolve section/adviser similar to show()
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
        ]);
    }

    public function storeAssessment(Request $request, AcademicYearStrandSubject $assignment)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { abort(401); }
        if ($assignment->teacher_id !== $user->user_pk_id) { abort(403); }

        $data = $request->validate([
            'term' => ['required', 'in:first-semester,second-semester,semester-final'],
            'category' => ['required', 'in:written_work,performance_task,quarterly_assessment'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_given' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1'],
        ]);

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
        $quarter = $quarterMap[$data['term']] ?? null;

            // Create a single SubjectRecord linked to this assignment
            SubjectRecord::create([
                'academic_year_strand_subject_id' => $assignment->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'max_score' => $data['max_score'],
                'type' => $type,
                'quarter' => $quarter,
                'date_given' => $data['date_given'],
                'remarks' => null,
            ]);

        return back()->with('success', 'Assessment added successfully.');
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

        // Load enrolled students for validation
        $enrolledStudentIds = SubjectEnrollment::with('studentEnrollment')
            ->where('academic_year_strand_subject_id', $assignment->id)
            ->get()
            ->map(function ($se) { return optional($se->studentEnrollment)->student_id; })
            ->filter()->unique()->all();
        $enrolledSet = array_flip($enrolledStudentIds);

        $now = now();
        $upserts = [];
        foreach ($scores as $recordId => $row) {
            $recordId = (int)$recordId;
            $rec = $recordsById->get($recordId);
            if (!$rec) continue; // skip records not in this assignment
            $max = (float)$rec->max_score;

            foreach ((array)$row as $studentId => $val) {
                $studentId = (int)$studentId;
                if (!isset($enrolledSet[$studentId])) continue; // skip non-enrolled
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
            return $student ? $student->id : null;
        })->filter()->unique()->values();

        // Compute initial totals per student for 1st and 2nd using same logic as show()
        $weights = [
            'ww' => (float) (($assignment->written_works_percentage ?? 0) / 100),
            'pt' => (float) (($assignment->performance_tasks_percentage ?? 0) / 100),
            'qa' => (float) (($assignment->quarterly_assessment_percentage ?? 0) / 100),
        ];

        $computeInitials = function ($quarterKey) use ($assignment, $students, $weights) {
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
                ->whereIn('student_id', $students->all())
                ->get(['subject_record_id','student_id','raw_score']);
            $resByRecStu = [];
            foreach ($results as $r) { $resByRecStu[$r->subject_record_id][$r->student_id] = (float) ($r->raw_score ?? 0); }
            $initials = [];
            foreach ($students as $sid) {
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
        foreach ($students as $sid) {
            $fq = $first[$sid] ?? null;
            $sq = $second[$sid] ?? null;
            $avg = ($fq !== null && $sq !== null) ? (($fq + $sq) / 2) : null;
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
}
