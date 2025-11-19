<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\SubjectEnrollment;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        // Get all students under this guardian
        $students = $guardian->students()->get();
        
        // Initialize statistics
        $totalStudents = $students->count();
        $studentsWithGrades = 0;
        $overallAverageGrade = 0;
        $totalSubjects = 0;
        
        // Student performance data
        $studentPerformance = [];
        
        foreach ($students as $student) {
            $studentData = [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'student_number' => $student->student_number,
                'grade_level' => $student->grade_level ?? 'N/A',
                'program' => $student->program ?? 'N/A',
                'average' => 0,
                'subjects_count' => 0,
                'status' => 'No Enrollment',
                'passing_subjects' => 0,
                'failing_subjects' => 0,
            ];
            
            // Get current enrollment
            $enrollment = $student->studentEnrollments()
                ->with('academicYear', 'section')
                ->latest()
                ->first();
            
            if ($enrollment) {
                $studentData['status'] = 'Enrolled';
                $studentData['section'] = $enrollment->section->section_name ?? 'N/A';
                $studentData['academic_year'] = $enrollment->academicYear->year_name ?? 'N/A';
                
                // Get subject enrollments with grades
                $subjectEnrollments = SubjectEnrollment::where('student_enrollment_id', $enrollment->id)
                    ->get();
                
                $gradesCount = 0;
                $totalGrades = 0;
                
                foreach ($subjectEnrollments as $se) {
                    // Calculate final grade (average of all quarters)
                    $grades = array_filter([
                        $se->fq_grade,
                        $se->sq_grade,
                        $se->a_grade,
                        $se->f_grade
                    ]);
                    
                    if (count($grades) > 0) {
                        $finalGrade = array_sum($grades) / count($grades);
                        $totalGrades += $finalGrade;
                        $gradesCount++;
                        
                        // Count passing/failing
                        if ($finalGrade >= 75) {
                            $studentData['passing_subjects']++;
                        } else {
                            $studentData['failing_subjects']++;
                        }
                    }
                }
                
                if ($gradesCount > 0) {
                    $average = $totalGrades / $gradesCount;
                    
                    $studentData['average'] = round($average, 2);
                    $studentData['subjects_count'] = $gradesCount;
                    $studentData['status'] = $average >= 75 ? 'Passing' : 'Needs Attention';
                    
                    $studentsWithGrades++;
                    $overallAverageGrade += $average;
                    $totalSubjects += $gradesCount;
                }
            }
            
            $studentPerformance[] = $studentData;
        }
        
        // Calculate overall average
        if ($studentsWithGrades > 0) {
            $overallAverageGrade = round($overallAverageGrade / $studentsWithGrades, 2);
        }
        
        // Get recent activities (messages received)
        $recentMessages = Message::whereHas('recipients', function($query) use ($user) {
                $query->where('recipient_id', $user->id);
            })
            ->with(['sender', 'recipients' => function($query) use ($user) {
                $query->where('recipient_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('guardian.dashboard', compact(
            'guardian',
            'totalStudents',
            'studentsWithGrades',
            'overallAverageGrade',
            'totalSubjects',
            'studentPerformance',
            'recentMessages'
        ));
    }
}
