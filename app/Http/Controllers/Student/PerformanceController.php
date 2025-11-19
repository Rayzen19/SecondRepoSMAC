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
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { abort(401); }

        // Resolve domain student id
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

        if (!$studentId) { abort(401, 'Student not linked'); }

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

        // Initialize performance data
        $performanceBySubject = collect();
        $performanceSummary = [
            'total_assessments' => 0,
            'completed_assessments' => 0,
            'average_score' => 0,
            'highest_score' => 0,
            'lowest_score' => 0,
        ];
        $performanceByType = collect();
        $trendData = collect();
        $allAssessments = collect();
        
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

        if ($selectedYearId) {
            // Get subject enrollments
            $subjectEnrollments = SubjectEnrollment::with([
                'academicYearStrandSubject.subject',
                'studentEnrollment.academicYearStrandSection.section',
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

                // Get all subject records for this subject and quarter
                $records = SubjectRecord::where('academic_year_strand_subject_id', $aysId)
                    ->where('quarter', $quarter)
                    ->get();

                if ($records->isEmpty()) continue;

                // Get student results for these records
                $results = SubjectRecordResult::whereIn('subject_record_id', $records->pluck('id'))
                    ->where('student_id', $studentId)
                    ->get()
                    ->keyBy('subject_record_id');

                // Collect all individual assessments for the doughnut chart
                foreach ($records as $record) {
                    $result = $results->get($record->id);
                    $score = $result ? (float)$result->raw_score : 0;
                    $maxScore = (float)$record->max_score;
                    $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
                    
                    $allAssessments->push([
                        'name' => $record->name,
                        'subject' => $subject->code,
                        'type' => $record->type,
                        'score' => $score,
                        'max_score' => $maxScore,
                        'percentage' => $percentage,
                        'date' => $record->date_recorded,
                    ]);
                }

                $subjectPerformance = [
                    'subject_name' => $subject->name,
                    'subject_code' => $subject->code,
                    'total_assessments' => $records->count(),
                    'completed_assessments' => $results->count(),
                    'total_score' => 0,
                    'total_max_score' => 0,
                    'percentage' => 0,
                    'by_type' => [],
                ];

                // Group by type
                foreach ($records as $record) {
                    $result = $results->get($record->id);
                    $score = $result ? (float)$result->raw_score : 0;
                    $maxScore = (float)$record->max_score;

                    $subjectPerformance['total_score'] += $score;
                    $subjectPerformance['total_max_score'] += $maxScore;

                    if (!isset($subjectPerformance['by_type'][$record->type])) {
                        $subjectPerformance['by_type'][$record->type] = [
                            'score' => 0,
                            'max_score' => 0,
                            'count' => 0,
                        ];
                    }

                    $subjectPerformance['by_type'][$record->type]['score'] += $score;
                    $subjectPerformance['by_type'][$record->type]['max_score'] += $maxScore;
                    $subjectPerformance['by_type'][$record->type]['count']++;

                    // Update performance by type summary
                    if (!$performanceByType->has($record->type)) {
                        $performanceByType->put($record->type, [
                            'type' => $record->type,
                            'score' => 0,
                            'max_score' => 0,
                            'count' => 0,
                        ]);
                    }
                    $typeData = $performanceByType->get($record->type);
                    $typeData['score'] += $score;
                    $typeData['max_score'] += $maxScore;
                    $typeData['count']++;
                    $performanceByType->put($record->type, $typeData);
                }

                // Calculate percentage
                if ($subjectPerformance['total_max_score'] > 0) {
                    $subjectPerformance['percentage'] = round(
                        ($subjectPerformance['total_score'] / $subjectPerformance['total_max_score']) * 100,
                        2
                    );
                }

                $performanceBySubject->push($subjectPerformance);

                // Update summary
                $performanceSummary['total_assessments'] += $subjectPerformance['total_assessments'];
                $performanceSummary['completed_assessments'] += $subjectPerformance['completed_assessments'];
            }

            // Calculate overall averages
            if ($performanceBySubject->isNotEmpty()) {
                $percentages = $performanceBySubject->pluck('percentage')->filter();
                if ($percentages->isNotEmpty()) {
                    $performanceSummary['average_score'] = round($percentages->avg(), 2);
                    $performanceSummary['highest_score'] = round($percentages->max(), 2);
                    $performanceSummary['lowest_score'] = round($percentages->min(), 2);
                }
            }

            // Calculate percentages for performance by type
            $performanceByType = $performanceByType->map(function ($item) {
                if ($item['max_score'] > 0) {
                    $item['percentage'] = round(($item['score'] / $item['max_score']) * 100, 2);
                } else {
                    $item['percentage'] = 0;
                }
                return $item;
            })->sortByDesc('percentage');
            
            // Sort all assessments by percentage descending
            $allAssessments = $allAssessments->sortByDesc('percentage')->values();
        }

        return view('student.performance.index', [
            'academicYears' => $academicYearsForView,
            'selectedYearId' => $selectedYearId,
            'selectedTerm' => $selectedTerm,
            'selectedSubjectId' => $selectedSubjectId,
            'availableSubjects' => $availableSubjects,
            'student' => $student,
            'performanceBySubject' => $performanceBySubject,
            'performanceSummary' => $performanceSummary,
            'performanceByType' => $performanceByType,
            'allAssessments' => $allAssessments,
        ]);
    }
}
