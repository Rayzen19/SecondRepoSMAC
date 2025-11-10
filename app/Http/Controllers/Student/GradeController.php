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
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('student')->user();
        if (!$user) { abort(401); }

        // Resolve domain student id (because the guard uses users table with user_pk_id link)
        $studentId = null;   // PK from students table
        $student = null;     // App\Models\Student instance

        // Dev-only impersonation for quick preview (e.g., ?as=2025-00021)
        if (config('app.debug') && $request->filled('as')) {
            $student = Student::where('student_number', $request->string('as'))->first();
            $studentId = $student?->id;
        }

        if (!$studentId) {
            // If authenticated guard is Student model directly
            if ($user instanceof \App\Models\Student) {
                $student = $user;
                $studentId = $user->id;
            } else {
                // Guard is StudentUser (users table). Link via user_pk_id
                $studentId = $user->user_pk_id ?? null;
                if ($studentId) {
                    $student = Student::find($studentId);
                }
            }
        }

        if (!$studentId) { abort(401, 'Student not linked'); }

        $academicYears = AcademicYear::orderBy('name', 'desc')
            ->orderBy('semester', 'asc')
            ->get();
        
    // Get selected filters
    $selectedYearId = $request->get('academic_year_id');
    // term: midterm|finals|final (default derive from active year semester)
    $selectedTerm = $request->get('term');
    // grade level: 11|12|all
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
            // Prefer active year if student has data there
            $activeYear = AcademicYear::where('is_active', true)->first();
            if ($activeYear && $yearsWithData->contains($activeYear->id)) {
                $selectedYearId = $activeYear->id;
                $selectedTerm = $selectedTerm ?: (strtolower($activeYear->semester) === '2nd' ? 'finals' : 'midterm');
            } elseif ($yearsWithData->isNotEmpty()) {
                // Otherwise pick the latest academic year (by name desc, semester asc) that has data
                $latestYearWithData = $academicYears->firstWhere('id', $yearsWithData->sortDesc()->first());
                if (!$latestYearWithData) {
                    // If the simple match above didn't find the year due to ordering, match by scanning
                    $latestYearWithData = $academicYears->first(function ($y) use ($yearsWithData) {
                        return $yearsWithData->contains($y->id);
                    });
                }
                $selectedYearId = $latestYearWithData?->id ?? $activeYear?->id;
                $selectedTerm = $selectedTerm ?: (strtolower(($latestYearWithData?->semester ?? $activeYear?->semester ?? '1st')) === '2nd' ? 'finals' : 'midterm');
            } else {
                // Fallback to active year even if no data
                $selectedYearId = $activeYear?->id;
                $selectedTerm = $selectedTerm ?: (strtolower($activeYear?->semester ?? '1st') === '2nd' ? 'finals' : 'midterm');
            }
        }

        // Filter dropdown to only years where this student has data (fallback: show all if none)
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
            // Get subject enrollments directly using whereHas - more reliable
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

            // Debug: Log the data
            Log::info('Grade Debug', [
                'student_id' => $studentId,
                'academic_year_id' => $selectedYearId,
                'subject_count' => $subjectEnrollments->count(),
                'selected_term' => $selectedTerm,
            ]);

            if ($subjectEnrollments->count() > 0) {
                // helper to compute an initial grade for a student & quarter when not yet submitted
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
                    // fetch weights
                    $assign = $recs->first()?->assignment; // may be null via relation not loaded; fallback query
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

                // map selected term to semester key used in computation
                $semesterKey = $selectedTerm === 'finals' ? '2nd' : '1st';
                $grades = $subjectEnrollments->map(function ($se) use ($semesterKey, $studentId, $computeInitial) {
                    $subject = $se->academicYearStrandSubject->subject;
                    
                    // Get appropriate grade based on semester
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

                // Calculate average - use final grade if available, otherwise use semester grade
                if ($grades->count() > 0) {
                    $gradesForAverage = $grades->map(function ($grade) {
                        return $grade['f_grade'] ?? $grade['grade'];
                    })->filter();
                    
                    if ($gradesForAverage->count() > 0) {
                        $average = round($gradesForAverage->avg(), 2);
                    }
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
        // FILTER: Only include published grades in overall calculations
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

        // Available grade levels (SHS)
        $gradeLevels = ['all' => 'All Levels', '11' => 'Grade 11', '12' => 'Grade 12'];

        return view('student.grades.index', [
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
