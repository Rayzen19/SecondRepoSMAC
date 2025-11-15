<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoresController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { 
            abort(401); 
        }

        // Resolve student ID
        $studentId = null;
        $student = null;

        // Dev-only impersonation
        if (config('app.debug') && $request->filled('as')) {
            $student = Student::where('student_number', $request->string('as'))->first();
            $studentId = $student?->id;
        }

        if (!$studentId) {
            if ($user instanceof \App\Models\Student) {
                $student = $user;
                $studentId = $user->id;
            } else {
                $studentId = $user->user_pk_id ?? null;
                if ($studentId) {
                    $student = Student::find($studentId);
                }
            }
        }

        if (!$studentId) { 
            abort(401, 'Student not linked'); 
        }

        // Get all academic years
        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        // Get selected filters
        $selectedYearId = $request->get('academic_year_id');
        $selectedTerm = $request->get('term');
        $selectedSubjectId = $request->get('subject_id');
        
        // Find all years where this student has enrollments
        $studentYearEnrollments = StudentEnrollment::withCount('subjectEnrollments')
            ->where('student_id', $studentId)
            ->get();
        $yearsWithData = $studentYearEnrollments
            ->where('subject_enrollments_count', '>', 0)
            ->pluck('academic_year_id')
            ->unique()
            ->values();

        // Auto-select year and term if not set
        if (!$selectedYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            if ($activeYear && $yearsWithData->contains($activeYear->id)) {
                $selectedYearId = $activeYear->id;
                $selectedTerm = $selectedTerm ?: (strtolower($activeYear->semester) === '2nd' ? 'finals' : 'midterm');
            } elseif ($yearsWithData->isNotEmpty()) {
                $latestYearWithData = $academicYears->firstWhere('id', $yearsWithData->sortDesc()->first());
                $selectedYearId = $latestYearWithData?->id;
                $selectedTerm = $selectedTerm ?: (strtolower($latestYearWithData?->semester ?? '1st') === '2nd' ? 'finals' : 'midterm');
            }
        }

        // Filter years for dropdown
        $academicYearsForView = $academicYears;
        if ($yearsWithData->isNotEmpty()) {
            $academicYearsForView = $academicYears->filter(function ($y) use ($yearsWithData) {
                return $yearsWithData->contains($y->id);
            })->values();
        }

        // Initialize data collections
        $scoresBySubject = collect();
        $summary = [
            'total_assessments' => 0,
            'completed_assessments' => 0,
            'average_percentage' => 0,
            'total_points_earned' => 0,
            'total_points_possible' => 0,
        ];
        
        // Get all subjects for this student in the selected year (for dropdown)
        $availableSubjects = collect();
        if ($selectedYearId) {
            $availableSubjects = SubjectEnrollment::with('academicYearStrandSubject.subject')
                ->whereHas('studentEnrollment', function ($q) use ($studentId, $selectedYearId) {
                    $q->where('student_id', $studentId)
                      ->where('academic_year_id', $selectedYearId);
                })
                ->get()
                ->map(function ($se) {
                    return [
                        'id' => $se->academicYearStrandSubject->subject->id,
                        'name' => $se->academicYearStrandSubject->subject->name,
                        'code' => $se->academicYearStrandSubject->subject->code,
                    ];
                })
                ->unique('id')
                ->sortBy('name')
                ->values();
        }

        if ($selectedYearId && $selectedTerm) {
            // Get subject enrollments
            $subjectEnrollments = SubjectEnrollment::with([
                'academicYearStrandSubject.subject',
                'academicYearStrandSubject.teacher',
            ])
            ->whereHas('studentEnrollment', function ($q) use ($studentId, $selectedYearId) {
                $q->where('student_id', $studentId)
                  ->where('academic_year_id', $selectedYearId);
            })
            ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                $q->whereHas('academicYearStrandSubject.subject', function ($qq) use ($selectedSubjectId) {
                    $qq->where('id', $selectedSubjectId);
                });
            })
            ->get();

            // Determine quarter based on term
            $quarter = $selectedTerm === 'finals' ? '2nd' : '1st';

            foreach ($subjectEnrollments as $se) {
                $aysId = $se->academic_year_strand_subject_id;
                $subject = $se->academicYearStrandSubject->subject;
                $teacher = $se->academicYearStrandSubject->teacher;

                // Get all subject records for this subject and quarter
                $records = SubjectRecord::where('academic_year_strand_subject_id', $aysId)
                    ->where('quarter', $quarter)
                    ->orderBy('date_given', 'desc')
                    ->orderBy('type', 'asc')
                    ->get();

                if ($records->isEmpty()) {
                    continue;
                }

                // Get student results for these records
                $results = SubjectRecordResult::whereIn('subject_record_id', $records->pluck('id'))
                    ->where('student_id', $studentId)
                    ->get()
                    ->keyBy('subject_record_id');

                $assessments = [];
                $subjectTotalScore = 0;
                $subjectTotalMaxScore = 0;
                $completedCount = 0;

                foreach ($records as $record) {
                    $result = $results->get($record->id);
                    $score = $result ? (float)$result->raw_score : null;
                    $maxScore = (float)$record->max_score;
                    $percentage = ($score !== null && $maxScore > 0) ? round(($score / $maxScore) * 100, 2) : null;
                    
                    $assessments[] = [
                        'name' => $record->name,
                        'type' => $record->type,
                        'date' => $record->date_given,
                        'score' => $score,
                        'max_score' => $maxScore,
                        'percentage' => $percentage,
                        'status' => $score !== null ? 'completed' : 'pending',
                    ];

                    if ($score !== null) {
                        $subjectTotalScore += $score;
                        $subjectTotalMaxScore += $maxScore;
                        $completedCount++;
                    }

                    $summary['total_assessments']++;
                }

                $subjectAverage = $subjectTotalMaxScore > 0 
                    ? round(($subjectTotalScore / $subjectTotalMaxScore) * 100, 2) 
                    : 0;

                $scoresBySubject->push([
                    'subject_name' => $subject->name,
                    'subject_code' => $subject->code,
                    'teacher_name' => $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'N/A',
                    'assessments' => $assessments,
                    'total_assessments' => count($assessments),
                    'completed_assessments' => $completedCount,
                    'total_score' => $subjectTotalScore,
                    'total_max_score' => $subjectTotalMaxScore,
                    'average_percentage' => $subjectAverage,
                ]);

                $summary['completed_assessments'] += $completedCount;
                $summary['total_points_earned'] += $subjectTotalScore;
                $summary['total_points_possible'] += $subjectTotalMaxScore;
            }

            // Calculate overall average
            if ($summary['total_points_possible'] > 0) {
                $summary['average_percentage'] = round(
                    ($summary['total_points_earned'] / $summary['total_points_possible']) * 100, 
                    2
                );
            }
        }

        return view('student.scores.index', [
            'academicYears' => $academicYearsForView,
            'selectedYearId' => $selectedYearId,
            'selectedTerm' => $selectedTerm,
            'selectedSubjectId' => $selectedSubjectId,
            'availableSubjects' => $availableSubjects,
            'student' => $student,
            'scoresBySubject' => $scoresBySubject,
            'summary' => $summary,
        ]);
    }
}
