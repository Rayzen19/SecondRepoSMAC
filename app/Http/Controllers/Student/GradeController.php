<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { 
            abort(401); 
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();
        
        // Get selected year/semester or default to active year
        $selectedYearId = $request->get('academic_year_id');
        $selectedSemester = $request->get('semester');
        
        if (!$selectedYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            $selectedYearId = $activeYear?->id;
            $selectedSemester = $activeYear?->semester ?? '1st';
        }

        $grades = collect();
        $average = null;
        $performanceData = [
            'activities' => 0,
            'quizzes' => 0,
            'assignment' => 0,
            'major_quiz' => 0,
            'exam' => 0,
            'recitation' => 0,
            'project' => 0,
        ];
        
        // Decision Support System data
        $overallAverage = 0;
        $totalSubjects = 0;
        $strengths = [];
        $weaknesses = [];
        $recommendations = [];

        if ($selectedYearId) {
            $enrollment = StudentEnrollment::where('student_id', $user->id)
                ->where('academic_year_id', $selectedYearId)
                ->first();

            if ($enrollment) {
                $subjectEnrollments = SubjectEnrollment::with([
                    'academicYearStrandSubject.subject',
                ])
                ->where('student_enrollment_id', $enrollment->id)
                ->get();

                $grades = $subjectEnrollments->map(function ($se) use ($selectedSemester) {
                    $subject = $se->academicYearStrandSubject->subject;
                    
                    // Get appropriate grade based on semester
                    $grade = null;
                    if ($selectedSemester === '1st') {
                        $grade = $se->fq_grade;
                    } elseif ($selectedSemester === '2nd') {
                        $grade = $se->sq_grade;
                    }
                    
                    return [
                        'subject_code' => $subject?->code,
                        'subject_name' => $subject?->name,
                        'grade' => $grade ?? 85, // Default if no grade yet
                    ];
                })->filter(function ($item) {
                    return $item['subject_code'] && $item['subject_name'];
                });

                // Calculate average
                if ($grades->count() > 0) {
                    $average = round($grades->avg('grade'), 2);
                }

                // Mock performance data (you can calculate from actual records)
                $performanceData = [
                    'activities' => 60,
                    'quizzes' => 60,
                    'assignment' => 60,
                    'major_quiz' => 60,
                    'exam' => 60,
                    'recitation' => 60,
                    'project' => 60,
                ];
            }
        }
        
        // Calculate Decision Support System data for all enrollments
        $allEnrollments = StudentEnrollment::with(['subjectEnrollments.academicYearStrandSubject.subject'])
            ->where('student_id', $user->id)
            ->get();

        foreach ($allEnrollments as $enrollment) {
            foreach ($enrollment->subjectEnrollments as $se) {
                $subject = $se->academicYearStrandSubject->subject;
                $avgGrade = collect([
                    $se->fq_grade,
                    $se->sq_grade,
                    $se->a_grade,
                    $se->f_grade
                ])->filter()->avg();

                if ($avgGrade) {
                    $overallAverage += $avgGrade;
                    $totalSubjects++;

                    // Identify strengths (>= 90)
                    if ($avgGrade >= 90) {
                        $strengths[] = [
                            'subject' => $subject->name,
                            'grade' => round($avgGrade, 2),
                        ];
                    }

                    // Identify weaknesses (< 80)
                    if ($avgGrade < 80) {
                        $weaknesses[] = [
                            'subject' => $subject->name,
                            'grade' => round($avgGrade, 2),
                        ];
                    }
                }
            }
        }

        if ($totalSubjects > 0) {
            $overallAverage = round($overallAverage / $totalSubjects, 2);
        }

        // Generate recommendations based on performance
        if ($overallAverage >= 90) {
            $recommendations[] = "Excellent performance! Continue maintaining your study habits.";
            $recommendations[] = "Consider taking advanced or honors classes to challenge yourself further.";
        } elseif ($overallAverage >= 80) {
            $recommendations[] = "Good performance! Focus on consistent study schedules.";
            $recommendations[] = "Consider joining study groups to improve weaker subjects.";
        } else {
            $recommendations[] = "Your performance needs improvement. Don't hesitate to ask for help.";
            $recommendations[] = "Create a structured study plan and allocate more time to challenging subjects.";
            $recommendations[] = "Consider seeking tutoring or additional support from teachers.";
        }

        if (count($weaknesses) > 0) {
            $weakSubjects = collect($weaknesses)->pluck('subject')->take(3)->join(', ');
            $recommendations[] = "Focus additional study time on: " . $weakSubjects;
        }

        return view('student.grades.index', [
            'academicYears' => $academicYears,
            'selectedYearId' => $selectedYearId,
            'selectedSemester' => $selectedSemester,
            'grades' => $grades,
            'average' => $average,
            'performanceData' => $performanceData,
            'overallAverage' => $overallAverage,
            'totalSubjects' => $totalSubjects,
            'strengths' => collect($strengths)->sortByDesc('grade')->take(5),
            'weaknesses' => collect($weaknesses)->sortBy('grade')->take(5),
            'recommendations' => $recommendations,
        ]);
    }
}
