<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSubject;
use App\Models\AcademicYearStrandSection;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoresController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { 
            abort(401); 
        }

        // Get teacher
        $teacherId = $user->user_pk_id ?? $user->id;
        $teacher = Teacher::find($teacherId);
        
        if (!$teacher) {
            abort(401, 'Teacher not found');
        }

        // Get all academic years
        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        // Get selected filters
        $selectedYearId = $request->get('academic_year_id');
        $selectedSubjectId = $request->get('subject_id');
        $selectedSectionId = $request->get('section_id');
        $selectedTerm = $request->get('term', 'midterm');
        $selectedAssessmentId = $request->get('assessment_id');
        $selectedStudentId = $request->get('student_id');
        
        // Auto-select year if not set
        if (!$selectedYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            $selectedYearId = $activeYear?->id;
        }

        // Get teacher's subjects for the selected year
        $availableSubjects = collect();
        $currentAssignment = null;
        if ($selectedYearId) {
            $availableSubjects = AcademicYearStrandSubject::with('subject')
                ->where('teacher_id', $teacherId)
                ->where('academic_year_id', $selectedYearId)
                ->get()
                ->map(function ($ayss) {
                    return [
                        'id' => $ayss->id,
                        'subject_id' => $ayss->subject->id,
                        'name' => $ayss->subject->name,
                        'code' => $ayss->subject->code,
                    ];
                })
                ->sortBy('name')
                ->values();
            
            // Get current assignment to fetch sections
            if ($selectedSubjectId) {
                $currentAssignment = AcademicYearStrandSubject::find($selectedSubjectId);
            }
        }

        // Get available sections for the selected subject (only sections teacher handles)
        $availableSections = collect();
        if ($currentAssignment) {
            // Get sections where teacher is the adviser OR has students enrolled in their subject
            $sectionsQuery = AcademicYearStrandSection::with('section')
                ->where('academic_year_id', $currentAssignment->academic_year_id)
                ->where('strand_id', $currentAssignment->strand_id)
                ->where(function($q) use ($teacherId, $selectedSubjectId) {
                    // Teacher is adviser of the section
                    $q->where('adviser_teacher_id', $teacherId)
                      // OR there are students enrolled in this teacher's subject from this section
                      ->orWhereHas('studentEnrollments.subjectEnrollments', function($qq) use ($selectedSubjectId) {
                          $qq->where('academic_year_strand_subject_id', $selectedSubjectId);
                      });
                });
            
            $availableSections = $sectionsQuery->get()
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
                ->sortBy('name')
                ->values();
        }

        // Get students for selected subject (and optionally filtered by section)
        $availableStudents = collect();
        if ($selectedSubjectId) {
            $studentsQuery = SubjectEnrollment::with(['studentEnrollment.student']);
            $studentsQuery->where('academic_year_strand_subject_id', $selectedSubjectId);
            
            // Filter by section if selected
            if ($selectedSectionId) {
                $studentsQuery->whereHas('studentEnrollment', function($q) use ($selectedSectionId) {
                    $q->where('academic_year_strand_section_id', $selectedSectionId);
                });
            }
            
            $availableStudents = $studentsQuery->get()
                ->map(function ($se) {
                    $student = $se->studentEnrollment->student;
                    return [
                        'id' => $student->id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'student_number' => $student->student_number,
                    ];
                })
                ->sortBy('name')
                ->values();
        }

        // Initialize data
        $studentScores = collect();
        $assessmentsList = collect();
        $summary = [
            'total_students' => 0,
            'total_assessments' => 0,
            'average_class_percentage' => 0,
            'highest_score' => 0,
            'lowest_score' => 0,
        ];

        if ($selectedSubjectId && $selectedYearId) {
            // Determine quarter based on term
            $quarter = $selectedTerm === 'finals' ? '2nd' : '1st';

            // Get all assessments for this subject and quarter
            $assessments = SubjectRecord::where('academic_year_strand_subject_id', $selectedSubjectId)
                ->where('quarter', $quarter)
                ->when($selectedAssessmentId, function ($q) use ($selectedAssessmentId) {
                    $q->where('id', $selectedAssessmentId);
                })
                ->orderBy('date_given', 'desc')
                ->orderBy('type', 'asc')
                ->get();

            $assessmentsList = $assessments->map(function ($record) {
                return [
                    'id' => $record->id,
                    'name' => $record->name,
                    'type' => $record->type,
                    'date' => $record->date_given,
                    'max_score' => $record->max_score,
                ];
            });

            $summary['total_assessments'] = $assessments->count();

            if ($assessments->isNotEmpty()) {
                // Get all students enrolled in this subject
                $subjectEnrollments = SubjectEnrollment::with('studentEnrollment.student')
                    ->where('academic_year_strand_subject_id', $selectedSubjectId)
                    ->when($selectedSectionId, function ($q) use ($selectedSectionId) {
                        $q->whereHas('studentEnrollment', function ($qq) use ($selectedSectionId) {
                            $qq->where('academic_year_strand_section_id', $selectedSectionId);
                        });
                    })
                    ->when($selectedStudentId, function ($q) use ($selectedStudentId) {
                        $q->whereHas('studentEnrollment', function ($qq) use ($selectedStudentId) {
                            $qq->where('student_id', $selectedStudentId);
                        });
                    })
                    ->get();

                $summary['total_students'] = $subjectEnrollments->count();

                $allPercentages = [];

                foreach ($subjectEnrollments as $se) {
                    $student = $se->studentEnrollment->student;

                    // Get all results for this student
                    $results = SubjectRecordResult::whereIn('subject_record_id', $assessments->pluck('id'))
                        ->where('student_id', $student->id)
                        ->get()
                        ->keyBy('subject_record_id');

                    $studentData = [
                        'student_id' => $student->id,
                        'student_number' => $student->student_number,
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'scores' => [],
                        'total_score' => 0,
                        'total_max_score' => 0,
                        'average_percentage' => 0,
                        'completed_count' => 0,
                    ];

                    foreach ($assessments as $assessment) {
                        $result = $results->get($assessment->id);
                        $score = $result ? (float)$result->raw_score : null;
                        $maxScore = (float)$assessment->max_score;
                        $percentage = ($score !== null && $maxScore > 0) ? round(($score / $maxScore) * 100, 2) : null;

                        $studentData['scores'][] = [
                            'assessment_id' => $assessment->id,
                            'score' => $score,
                            'max_score' => $maxScore,
                            'percentage' => $percentage,
                            'status' => $score !== null ? 'completed' : 'pending',
                        ];

                        if ($score !== null) {
                            $studentData['total_score'] += $score;
                            $studentData['total_max_score'] += $maxScore;
                            $studentData['completed_count']++;
                        }
                    }

                    if ($studentData['total_max_score'] > 0) {
                        $studentData['average_percentage'] = round(
                            ($studentData['total_score'] / $studentData['total_max_score']) * 100,
                            2
                        );
                        $allPercentages[] = $studentData['average_percentage'];
                    }

                    $studentScores->push($studentData);
                }

                // Calculate class statistics
                if (!empty($allPercentages)) {
                    $summary['average_class_percentage'] = round(array_sum($allPercentages) / count($allPercentages), 2);
                    $summary['highest_score'] = round(max($allPercentages), 2);
                    $summary['lowest_score'] = round(min($allPercentages), 2);
                }

                // Sort students by name
                $studentScores = $studentScores->sortBy('student_name')->values();
            }
        }

        return view('teacher.scores.index', [
            'academicYears' => $academicYears,
            'selectedYearId' => $selectedYearId,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedSectionId' => $selectedSectionId,
            'selectedTerm' => $selectedTerm,
            'selectedAssessmentId' => $selectedAssessmentId,
            'selectedStudentId' => $selectedStudentId,
            'availableSubjects' => $availableSubjects,
            'availableSections' => $availableSections,
            'availableStudents' => $availableStudents,
            'teacher' => $teacher,
            'studentScores' => $studentScores,
            'assessmentsList' => $assessmentsList,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        if (!$user) { 
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $teacherId = $user->user_pk_id ?? $user->id;

        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|integer',
            'scores.*.assessment_id' => 'required|integer',
            'scores.*.raw_score' => 'required|numeric|min:0',
            'scores.*.max_score' => 'required|numeric|min:0',
        ]);

        $savedCount = 0;
        $errors = [];

        foreach ($validated['scores'] as $scoreData) {
            try {
                // Verify the assessment belongs to teacher
                $assessment = SubjectRecord::find($scoreData['assessment_id']);
                if (!$assessment) {
                    $errors[] = "Assessment {$scoreData['assessment_id']} not found";
                    continue;
                }

                $assignment = AcademicYearStrandSubject::find($assessment->academic_year_strand_subject_id);
                if (!$assignment || $assignment->teacher_id != $teacherId) {
                    $errors[] = "Unauthorized to modify assessment {$scoreData['assessment_id']}";
                    continue;
                }

                // Validate score doesn't exceed max
                if ($scoreData['raw_score'] > $scoreData['max_score']) {
                    $errors[] = "Score for student {$scoreData['student_id']} exceeds max score";
                    continue;
                }

                // Update or create the score
                SubjectRecordResult::updateOrCreate(
                    [
                        'subject_record_id' => $scoreData['assessment_id'],
                        'student_id' => $scoreData['student_id'],
                    ],
                    [
                        'raw_score' => $scoreData['raw_score'],
                    ]
                );

                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error saving score: " . $e->getMessage();
            }
        }

        if ($savedCount > 0) {
            return response()->json([
                'success' => true, 
                'message' => "Successfully saved {$savedCount} score(s)",
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save scores',
                'errors' => $errors
            ], 400);
        }
    }
}
