<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('guardian')->user();
        $guardian = Guardian::find($user->user_pk_id);
        
        if (!$guardian) {
            abort(404, 'Guardian profile not found');
        }

        // Get all students linked to this guardian
        $students = $guardian->students;
        
        if ($students->isEmpty()) {
            return view('guardian.grades.index', [
                'students' => $students,
                'selectedStudentId' => null,
                'academicYears' => collect(),
                'selectedYearId' => null,
                'selectedTerm' => null,
                'selectedGradeLevel' => 'all',
                'gradeLevels' => ['all' => 'All Levels', '11' => 'Grade 11', '12' => 'Grade 12'],
                'student' => null,
                'grades' => collect(),
                'average' => null,
                'performanceData' => [],
                'overallAverage' => 0,
                'totalSubjects' => 0,
                'strengths' => collect(),
                'weaknesses' => collect(),
                'recommendations' => [],
            ]);
        }

        // Get selected student (default to first)
        $selectedStudentId = $request->get('student_id', $students->first()->id);
        $student = Student::find($selectedStudentId);
        
        if (!$student || !$students->contains('id', $selectedStudentId)) {
            abort(403, 'Unauthorized access to student data');
        }

        $studentId = $student->id;

        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();
        
        // Get selected filters
        $selectedYearId = $request->get('academic_year_id');
        $selectedTerm = $request->get('term');
        $selectedGradeLevel = $request->get('grade_level', 'all');

        // Find all years where this student has at least one subject enrollment
        $studentYearEnrollments = StudentEnrollment::withCount('subjectEnrollments')
            ->where('student_id', $studentId)
            ->get();
        $yearsWithData = $studentYearEnrollments
            ->where('subject_enrollments_count', '>', 0)
            ->pluck('academic_year_id')
            ->unique()
            ->values();

        if (!$selectedYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            if ($activeYear && $yearsWithData->contains($activeYear->id)) {
                $selectedYearId = $activeYear->id;
                $selectedTerm = $selectedTerm ?: (strtolower($activeYear->semester) === '2nd' ? 'finals' : 'midterm');
            } elseif ($yearsWithData->isNotEmpty()) {
                $latestYearWithData = $academicYears->firstWhere('id', $yearsWithData->sortDesc()->first());
                if (!$latestYearWithData) {
                    $latestYearWithData = $academicYears->first(function ($y) use ($yearsWithData) {
                        return $yearsWithData->contains($y->id);
                    });
                }
                $selectedYearId = $latestYearWithData?->id ?? $activeYear?->id;
                $selectedTerm = $selectedTerm ?: (strtolower(($latestYearWithData?->semester ?? $activeYear?->semester ?? '1st')) === '2nd' ? 'finals' : 'midterm');
            } else {
                $selectedYearId = $activeYear?->id;
                $selectedTerm = $selectedTerm ?: (strtolower($activeYear?->semester ?? '1st') === '2nd' ? 'finals' : 'midterm');
            }
        }

        // Filter dropdown to only years where this student has data
        $academicYearsForView = $academicYears;
        if ($yearsWithData->isNotEmpty()) {
            $academicYearsForView = $academicYears->filter(function ($y) use ($yearsWithData) {
                return $yearsWithData->contains($y->id);
            })->values();
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
            // FILTER: Only show subjects where grades_published = true
            $subjectEnrollments = SubjectEnrollment::with([
                'academicYearStrandSubject.subject',
                'studentEnrollment.academicYearStrandSection.section',
            ])
            ->whereHas('studentEnrollment', function ($q) use ($studentId, $selectedYearId) {
                $q->where('student_id', $studentId)
                  ->where('academic_year_id', $selectedYearId);
            })
            ->whereHas('academicYearStrandSubject', function($q) {
                $q->where('grades_published', true); // Only show published grades
            })
            ->when($selectedGradeLevel && $selectedGradeLevel !== 'all', function($q) use ($selectedGradeLevel) {
                $q->whereHas('studentEnrollment.academicYearStrandSection.section', function($qq) use ($selectedGradeLevel) {
                    $qq->where('grade', $selectedGradeLevel);
                });
            })
            ->get();

            if ($subjectEnrollments->count() > 0) {
                $computeInitial = function (int $aysId, int $studId, string $quarter) {
                    $recs = SubjectRecord::where('academic_year_strand_subject_id', $aysId)
                        ->when($quarter === null, function($q){ $q->whereNull('quarter'); }, function($q) use ($quarter){ $q->where('quarter', $quarter); })
                        ->get(['id','type','max_score']);
                    if ($recs->isEmpty()) return null;
                    $ww = $recs->where('type','written work');
                    $pt = $recs->where('type','performance task');
                    $qa = $recs->where('type','quarterly assessment');
                    $wwMax=(float)$ww->sum('max_score'); $ptMax=(float)$pt->sum('max_score'); $qaMax=(float)$qa->sum('max_score');
                    $results = SubjectRecordResult::whereIn('subject_record_id', $recs->pluck('id')->all())
                        ->where('student_id', $studId)
                        ->get(['subject_record_id','raw_score']);
                    $resByRec = [];
                    foreach ($results as $r) { $resByRec[$r->subject_record_id] = (float)($r->raw_score ?? 0); }
                    $sumRaw = function($col) use ($resByRec){ return (float) $col->sum(function($rec) use ($resByRec){ return $resByRec[$rec->id] ?? 0; }); };
                    $wwRaw=$sumRaw($ww); $ptRaw=$sumRaw($pt); $qaRaw=$sumRaw($qa);
                    $assign = $recs->first()?->assignment;
                    if (!$assign) { $assign = \App\Models\AcademicYearStrandSubject::find($aysId); }
                    $weights = [
                        'ww' => (float)(($assign->written_works_percentage ?? 0)/100),
                        'pt' => (float)(($assign->performance_tasks_percentage ?? 0)/100),
                        'qa' => (float)(($assign->quarterly_assessment_percentage ?? 0)/100),
                    ];
                    $wwPS = $wwMax>0 ? ($wwRaw/$wwMax)*100 : null;
                    $ptPS = $ptMax>0 ? ($ptRaw/$ptMax)*100 : null;
                    $qaPS = $qaMax>0 ? ($qaRaw/$qaMax)*100 : null;
                    $wwWS = isset($wwPS) ? $wwPS*$weights['ww'] : null;
                    $ptWS = isset($ptPS) ? $ptPS*$weights['pt'] : null;
                    $qaWS = isset($qaPS) ? $qaPS*$weights['qa'] : null;
                    $initial = (isset($wwWS)?$wwWS:0) + (isset($ptWS)?$ptWS:0) + (isset($qaWS)?$qaWS:0);
                    return round($initial, 2);
                };

                $semesterKey = $selectedTerm === 'finals' ? '2nd' : '1st';
                $grades = $subjectEnrollments->map(function ($se) use ($semesterKey, $studentId, $computeInitial) {
                    $subject = $se->academicYearStrandSubject->subject;
                    
                    $fq = $se->fq_grade ?? $computeInitial($se->academic_year_strand_subject_id, $studentId, '1st');
                    $sq = $se->sq_grade ?? $computeInitial($se->academic_year_strand_subject_id, $studentId, '2nd');
                    $grade = $semesterKey === '2nd' ? $sq : $fq;
                    
                    return [
                        'subject_code' => $subject?->code,
                        'subject_name' => $subject?->name,
                        'grade' => $grade ?? null,
                        'fq_grade' => $fq,
                        'sq_grade' => $sq,
                        'a_grade' => $se->a_grade ?? (($fq !== null && $sq !== null) ? round(($fq+$sq)/2,2) : null),
                        'f_grade' => $se->f_grade ?? (($fq !== null && $sq !== null) ? round(($fq+$sq)/2,2) : null),
                    ];
                })->filter(function ($item) {
                    return $item['subject_code'] && $item['subject_name'];
                });

                if ($grades->count() > 0) {
                    $gradesForAverage = $grades->map(function ($grade) {
                        return $grade['f_grade'] ?? $grade['grade'];
                    })->filter();
                    
                    if ($gradesForAverage->count() > 0) {
                        $average = round($gradesForAverage->avg(), 2);
                    }
                }

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
        
        // Calculate Decision Support System data
        $allEnrollments = StudentEnrollment::with(['subjectEnrollments.academicYearStrandSubject.subject'])
            ->where('student_id', $studentId)
            ->get();

        foreach ($allEnrollments as $enrollment) {
            foreach ($enrollment->subjectEnrollments as $se) {
                // Skip if grades are not published for this subject
                if (!$se->academicYearStrandSubject || !$se->academicYearStrandSubject->grades_published) {
                    continue;
                }
                
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

                    if ($avgGrade >= 90) {
                        $strengths[] = [
                            'subject' => $subject->name,
                            'grade' => round($avgGrade, 2),
                        ];
                    }

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

        // Generate recommendations
        if ($overallAverage >= 90) {
            $recommendations[] = "Excellent performance! Encourage your child to maintain their study habits.";
            $recommendations[] = "Consider discussing advanced or honors classes with teachers.";
        } elseif ($overallAverage >= 80) {
            $recommendations[] = "Good performance! Help your child maintain consistent study schedules.";
            $recommendations[] = "Consider organizing study groups to improve weaker subjects.";
        } else {
            $recommendations[] = "Your child's performance needs improvement. Offer support and encouragement.";
            $recommendations[] = "Help create a structured study plan with dedicated time for homework.";
            $recommendations[] = "Consider seeking tutoring or additional support from teachers.";
        }

        if (count($weaknesses) > 0) {
            $weakSubjects = collect($weaknesses)->pluck('subject')->take(3)->join(', ');
            $recommendations[] = "Focus additional support on: " . $weakSubjects;
        }

        $gradeLevels = ['all' => 'All Levels', '11' => 'Grade 11', '12' => 'Grade 12'];

        return view('guardian.grades.index', [
            'students' => $students,
            'selectedStudentId' => $selectedStudentId,
            'academicYears' => $academicYearsForView,
            'selectedYearId' => $selectedYearId,
            'selectedTerm' => $selectedTerm,
            'selectedGradeLevel' => $selectedGradeLevel,
            'gradeLevels' => $gradeLevels,
            'student' => $student,
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
