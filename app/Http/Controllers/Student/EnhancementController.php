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

class EnhancementController extends Controller
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
        }

        // Generate Decision Support System recommendations
        $dssRecommendations = $this->generateDSSRecommendations(
            $performanceByType,
            $performanceBySubject,
            $performanceSummary
        );

        return view('student.enhancement.index', [
            'academicYears' => $academicYearsForView,
            'selectedYearId' => $selectedYearId,
            'selectedTerm' => $selectedTerm,
            'student' => $student,
            'performanceBySubject' => $performanceBySubject,
            'performanceSummary' => $performanceSummary,
            'performanceByType' => $performanceByType,
            'dssRecommendations' => $dssRecommendations,
        ]);
    }

    /**
     * Generate Decision Support System recommendations based on performance data
     */
    private function generateDSSRecommendations($performanceByType, $performanceBySubject, $performanceSummary)
    {
        $recommendations = [
            'overall_status' => 'good',
            'overall_message' => '',
            'areas_to_improve' => [],
            'strengths' => [],
            'assessment_type_analysis' => [],
            'subject_analysis' => [],
            'priority_actions' => [],
        ];

        // Overall status determination
        $avgScore = $performanceSummary['average_score'];
        if ($avgScore >= 90) {
            $recommendations['overall_status'] = 'excellent';
            $recommendations['overall_message'] = 'Excellent performance! Keep up the great work!';
        } elseif ($avgScore >= 80) {
            $recommendations['overall_status'] = 'good';
            $recommendations['overall_message'] = 'Good performance overall. Focus on areas below 80% for improvement.';
        } elseif ($avgScore >= 75) {
            $recommendations['overall_status'] = 'satisfactory';
            $recommendations['overall_message'] = 'Satisfactory performance. You have significant room for improvement.';
        } else {
            $recommendations['overall_status'] = 'needs_improvement';
            $recommendations['overall_message'] = 'Your performance needs improvement. Focus on the recommendations below.';
        }

        // Assessment Type Analysis
        foreach ($performanceByType as $type) {
            $analysis = [
                'type' => ucwords(str_replace('_', ' ', $type['type'])),
                'percentage' => $type['percentage'],
                'count' => $type['count'],
                'status' => '',
                'recommendation' => '',
            ];

            if ($type['percentage'] >= 90) {
                $analysis['status'] = 'excellent';
                $analysis['recommendation'] = 'Outstanding! Continue this excellent performance.';
                $recommendations['strengths'][] = $analysis['type'];
            } elseif ($type['percentage'] >= 80) {
                $analysis['status'] = 'good';
                $analysis['recommendation'] = 'Good work! A little more effort can make it excellent.';
            } elseif ($type['percentage'] >= 75) {
                $analysis['status'] = 'needs_attention';
                $analysis['recommendation'] = 'This area needs attention. Review your study methods for this assessment type.';
                $recommendations['areas_to_improve'][] = [
                    'area' => $analysis['type'],
                    'percentage' => $type['percentage'],
                    'priority' => 'medium',
                ];
            } else {
                $analysis['status'] = 'critical';
                $analysis['recommendation'] = 'Critical! Prioritize improvement in this assessment type immediately.';
                $recommendations['areas_to_improve'][] = [
                    'area' => $analysis['type'],
                    'percentage' => $type['percentage'],
                    'priority' => 'high',
                ];
            }

            $recommendations['assessment_type_analysis'][] = $analysis;
        }

        // Subject Analysis
        foreach ($performanceBySubject as $subject) {
            $analysis = [
                'subject' => $subject['subject_name'],
                'code' => $subject['subject_code'],
                'percentage' => $subject['percentage'],
                'assessments' => $subject['total_assessments'],
                'status' => '',
                'recommendation' => '',
                'weak_types' => [],
            ];

            if ($subject['percentage'] >= 90) {
                $analysis['status'] = 'excellent';
                $analysis['recommendation'] = 'Excellent mastery of this subject!';
                if (!in_array($subject['subject_name'], $recommendations['strengths'])) {
                    $recommendations['strengths'][] = $subject['subject_name'];
                }
            } elseif ($subject['percentage'] >= 80) {
                $analysis['status'] = 'good';
                $analysis['recommendation'] = 'Good understanding. Keep practicing to reach excellence.';
            } elseif ($subject['percentage'] >= 75) {
                $analysis['status'] = 'needs_attention';
                $analysis['recommendation'] = 'Requires more focus. Review key concepts and practice more.';
                $recommendations['areas_to_improve'][] = [
                    'area' => $subject['subject_name'],
                    'percentage' => $subject['percentage'],
                    'priority' => 'medium',
                ];
            } else {
                $analysis['status'] = 'critical';
                $analysis['recommendation'] = 'Critical! Seek help from teachers and study more on this subject.';
                $recommendations['areas_to_improve'][] = [
                    'area' => $subject['subject_name'],
                    'percentage' => $subject['percentage'],
                    'priority' => 'high',
                ];
            }

            // Identify weak assessment types within this subject
            if (!empty($subject['by_type'])) {
                foreach ($subject['by_type'] as $typeName => $typeData) {
                    $typePercentage = $typeData['max_score'] > 0 
                        ? round(($typeData['score'] / $typeData['max_score']) * 100, 2) 
                        : 0;
                    
                    if ($typePercentage < 75) {
                        $analysis['weak_types'][] = [
                            'type' => ucwords(str_replace('_', ' ', $typeName)),
                            'percentage' => $typePercentage,
                        ];
                    }
                }
            }

            $recommendations['subject_analysis'][] = $analysis;
        }

        // Generate Priority Actions
        $sortedAreas = collect($recommendations['areas_to_improve'])
            ->sortBy([
                fn($a, $b) => $a['priority'] === 'high' ? -1 : ($b['priority'] === 'high' ? 1 : 0),
                fn($a, $b) => $a['percentage'] <=> $b['percentage']
            ])
            ->take(5);

        foreach ($sortedAreas as $area) {
            $action = [
                'title' => "Improve {$area['area']}",
                'description' => '',
                'priority' => $area['priority'],
                'percentage' => $area['percentage'],
            ];

            if ($area['percentage'] < 60) {
                $action['description'] = "Your performance in {$area['area']} is critically low at {$area['percentage']}%. Schedule extra study time, seek help from teachers, and practice regularly.";
            } elseif ($area['percentage'] < 70) {
                $action['description'] = "With {$area['percentage']}% in {$area['area']}, you need significant improvement. Review materials, practice problems, and consider study groups.";
            } else {
                $action['description'] = "You scored {$area['percentage']}% in {$area['area']}. A bit more focus and practice will help you reach 80% and above.";
            }

            $recommendations['priority_actions'][] = $action;
        }

        // If no areas to improve, encourage maintaining excellence
        if (empty($recommendations['priority_actions'])) {
            $recommendations['priority_actions'][] = [
                'title' => 'Maintain Excellence',
                'description' => 'Your performance is excellent across all areas. Continue your current study habits and help peers who may be struggling.',
                'priority' => 'low',
                'percentage' => $avgScore,
            ];
        }

        // Add specific study tips based on weakest assessment types
        $weakestTypes = $performanceByType->filter(function ($type) {
            return $type['percentage'] < 75;
        })->sortBy('percentage')->take(3);

        foreach ($weakestTypes as $type) {
            $typeName = ucwords(str_replace('_', ' ', $type['type']));
            $recommendations['priority_actions'][] = [
                'title' => "Focus on {$typeName} Assessments",
                'description' => "You're scoring {$type['percentage']}% on {$typeName}. Practice this type of assessment more frequently and review feedback from previous {$typeName}s.",
                'priority' => $type['percentage'] < 60 ? 'high' : 'medium',
                'percentage' => $type['percentage'],
            ];
        }

        return $recommendations;
    }
}
