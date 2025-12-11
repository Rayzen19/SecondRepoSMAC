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
use App\Mail\ScoreNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        // Get selected filters
        $selectedYearId = $request->get('academic_year_id');
        $selectedSubjectId = $request->get('subject_id');
        $selectedSectionId = $request->get('section_id');
        $selectedTerm = $request->get('term', 'midterm');
        $selectedAssessmentId = $request->get('assessment_id');
        $selectedStudentId = $request->get('student_id');

        // Get all academic years
        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();
        
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
                
                // Verify the selected subject belongs to the current academic year
                if ($currentAssignment && $currentAssignment->academic_year_id != $selectedYearId) {
                    // Subject doesn't exist in this academic year, clear selection
                    $selectedSubjectId = null;
                    $currentAssignment = null;
                }
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
                    $student = optional($se->studentEnrollment)->student;
                    if (!$student) {
                        return null;
                    }
                    return [
                        'id' => $student->id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'student_number' => $student->student_number,
                    ];
                })
                ->filter()
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
                ->orderBy('created_at', 'desc')
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
                    $student = optional($se->studentEnrollment)->student;
                    if (!$student) {
                        // Skip enrollments that no longer have a student enrollment relation
                        continue;
                    }

                    // Get all results for this student, ordered by recently created
                    $results = SubjectRecordResult::whereIn('subject_record_id', $assessments->pluck('id'))
                        ->where('student_id', $student->id)
                        ->orderBy('created_at', 'desc')
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
        $notificationsSummary = [
            'emails_sent' => 0,
            'sms_sent' => 0,
        ];

        foreach ($validated['scores'] as $scoreData) {
            try {
                // Log the score data being processed
                Log::info('Processing score', [
                    'student_id' => $scoreData['student_id'],
                    'assessment_id' => $scoreData['assessment_id'],
                    'raw_score' => $scoreData['raw_score']
                ]);

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

                // Check if score already exists
                $existingScore = SubjectRecordResult::where('subject_record_id', $scoreData['assessment_id'])
                    ->where('student_id', $scoreData['student_id'])
                    ->first();
                
                $isNewScore = !$existingScore;
                $isScoreChanged = false;
                
                if ($existingScore) {
                    // Check if the score actually changed
                    $isScoreChanged = $existingScore->raw_score != $scoreData['raw_score'];
                }
                
                // Update or create the score
                $scoreResult = SubjectRecordResult::updateOrCreate(
                    [
                        'subject_record_id' => $scoreData['assessment_id'],
                        'student_id' => $scoreData['student_id'],
                    ],
                    [
                        'raw_score' => $scoreData['raw_score'],
                    ]
                );

                // Send email notification ONLY for quarterly assessment and if this is a new score or the score was changed
                if (($isNewScore || $isScoreChanged) && $assessment->type === 'quarterly assessment') {
                    Log::info("Score was " . ($isNewScore ? 'newly added' : 'changed') . " for quarterly assessment - sending notifications");
                    $result = $this->sendScoreNotification($scoreData['student_id'], $assessment, $scoreData['raw_score'], $scoreData['max_score'], $assignment);
                    if (is_array($result)) {
                        $notificationsSummary['emails_sent'] += $result['emails_sent'] ?? 0;
                        $notificationsSummary['sms_sent'] += $result['sms_sent'] ?? 0;
                    }
                } else {
                    if ($assessment->type !== 'quarterly assessment') {
                        Log::info("Score for {$assessment->type} - skipping notification (only quarterly assessments send notifications)");
                    } else {
                        Log::info("Score unchanged for student {$scoreData['student_id']} - skipping notification");
                    }
                }

                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error saving score: " . $e->getMessage();
            }
        }

        if ($savedCount > 0) {
            return response()->json([
                'success' => true, 
                'message' => "Successfully saved {$savedCount} score(s)",
                'errors' => $errors,
                'notifications' => $notificationsSummary,
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save scores',
                'errors' => $errors
            ], 400);
        }
    }

    /**
     * Send score notification email to guardian(s)
     */
    private function sendScoreNotification($studentId, $assessment, $rawScore, $maxScore, $assignment)
    {
        try {
            $smsEnabled = !empty(config('services.semaphore.api_key'));
            $smsService = $smsEnabled ? app(\App\Services\SemaphoreSmsService::class) : null;
            // Get student with fresh guardians relationship - ensure we're getting the correct student
            $student = Student::with(['guardians' => function($query) {
                $query->whereNull('guardian_students.deleted_at');
            }])->find($studentId);
            
            if (!$student) {
                Log::warning("Student not found for ID: {$studentId}");
                return;
            }

            // Log which student we're processing with full details
            Log::info("Sending score notification for student: {$student->first_name} {$student->last_name} (ID: {$studentId}, Student Number: {$student->student_number})");

            // Get academic year info
            $academicYear = AcademicYear::find($assignment->academic_year_id);
            $subject = $assignment->subject;
            
            // Prepare email data
            $studentName = $student->first_name . ' ' . $student->last_name;
            $assessmentName = $assessment->name;
            $assessmentType = $assessment->type;
            $subjectName = $subject->code . ' - ' . $subject->name;
            $academicYearName = $academicYear ? $academicYear->name . ' - ' . ucfirst($academicYear->semester) . ' Semester' : 'N/A';
            $term = $assessment->quarter === '1st' ? '1st Quarter (Midterm)' : '2nd Quarter (Finals)';
            $dateGiven = $assessment->date_given ? date('F d, Y', strtotime($assessment->date_given)) : 'N/A';

            // Send email to all guardians linked via guardian_students table
            $emailsSent = 0;
            $smsSent = 0;
            if ($student->guardians && $student->guardians->count() > 0) {
                Log::info("Found {$student->guardians->count()} guardian(s) for student ID: {$studentId}");
                
                foreach ($student->guardians as $guardian) {
                    if ($guardian->email && filter_var($guardian->email, FILTER_VALIDATE_EMAIL)) {
                        Log::info("✉️ Sending score email:", [
                            'student_id' => $studentId,
                            'student_name' => $studentName,
                            'student_number' => $student->student_number,
                            'guardian_id' => $guardian->id,
                            'guardian_name' => $guardian->first_name . ' ' . $guardian->last_name,
                            'guardian_email' => $guardian->email,
                            'assessment' => $assessmentName,
                            'score' => "{$rawScore}/{$maxScore}"
                        ]);
                        
                        Mail::to($guardian->email)->send(
                            new ScoreNotification(
                                $studentName,
                                $assessmentName,
                                $assessmentType,
                                $rawScore,
                                $maxScore,
                                $subjectName,
                                $academicYearName,
                                $term,
                                $dateGiven
                            )
                        );
                        
                        Log::info("✅ Email sent successfully to {$guardian->email}");
                        $emailsSent++;
                    } else {
                        Log::warning("⚠️ Guardian {$guardian->id} has invalid or missing email for student {$studentId}");
                    }

                    // Send SMS if enabled and guardian has mobile_number
                    if ($smsEnabled && !empty($guardian->mobile_number)) {
                        $smsMessage = "SMAC: New score for {$studentName} - {$subjectName} ({$assessmentType} {$assessmentName}) = {$rawScore}/{$maxScore}. {$academicYearName}";
                        $result = $smsService->sendSms($guardian->mobile_number, $smsMessage);
                        if (is_array($result) && empty($result['error'])) {
                            Log::info("📱 SMS queued to guardian {$guardian->mobile_number}", ['response' => $result]);
                            $smsSent++;
                        } else {
                            Log::warning("SMS failed for guardian {$guardian->mobile_number}", ['response' => $result]);
                        }
                    }
                }
            } else {
                Log::info("No guardians found in guardians relationship for student ID: {$studentId}");
            }

            // Also send to guardian_email field if it exists (legacy support)
            if ($student->guardian_email && filter_var($student->guardian_email, FILTER_VALIDATE_EMAIL)) {
                Log::info("Sending email to legacy guardian email: {$student->guardian_email} for student: {$studentName}");
                
                Mail::to($student->guardian_email)->send(
                    new ScoreNotification(
                        $studentName,
                        $assessmentName,
                        $assessmentType,
                        $rawScore,
                        $maxScore,
                        $subjectName,
                        $academicYearName,
                        $term,
                        $dateGiven
                    )
                );
                $emailsSent++;
            }

            // Also send SMS to legacy guardian_contact if present and SMS enabled
            if ($smsEnabled && !empty($student->guardian_contact)) {
                $smsMessage = "SMAC: New score for {$studentName} - {$subjectName} ({$assessmentType} {$assessmentName}) = {$rawScore}/{$maxScore}. {$academicYearName}";
                $result = $smsService->sendSms($student->guardian_contact, $smsMessage);
                if (is_array($result) && empty($result['error'])) {
                    Log::info("📱 SMS queued to legacy guardian contact {$student->guardian_contact}", ['response' => $result]);
                    $smsSent++;
                } else {
                    Log::warning("SMS failed for legacy guardian contact {$student->guardian_contact}", ['response' => $result]);
                }
            }
            return ['emails_sent' => $emailsSent, 'sms_sent' => $smsSent];
        } catch (\Exception $e) {
            // Log error but don't fail the score save operation
            Log::error('Failed to send score notification for student ID ' . $studentId . ': ' . $e->getMessage());
            return ['emails_sent' => 0, 'sms_sent' => 0];
        }
    }
}
