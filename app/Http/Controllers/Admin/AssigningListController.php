<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Strand;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssigningListController extends Controller
{
    /**
     * Display the assigning list with all students and filters.
     */
    public function index(Request $request)
    {
        // Get all active strands for filter
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get sections filtered by strand and grade level
        $sectionsQuery = Section::with('strand')
            ->orderBy('grade')
            ->orderBy('name');
        
        // Filter sections by strand if selected
        if ($request->filled('strand') && $request->strand !== 'all') {
            $sectionsQuery->whereHas('strand', function($q) use ($request) {
                $q->where('code', $request->strand);
            });
        }
        
        // Filter sections by grade level if selected
        if ($request->filled('grade_level') && $request->grade_level !== 'all') {
            $gradeLevel = $request->grade_level;
            $sectionsQuery->where(function($q) use ($gradeLevel) {
                $q->where('grade', 'G-' . $gradeLevel)
                  ->orWhere('grade', 'Grade ' . $gradeLevel)
                  ->orWhere('grade', $gradeLevel);
            });
        }
        
        $sections = $sectionsQuery->get();
        
        // Get distinct grade levels from students
        $gradeLevels = ['11', '12']; // SHS Grade levels
        
        // Use the same approach as the main Student list: show students with
        // active/enrolled studentEnrollments for the active academic year.
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

        // If a strand code is provided, resolve the model to allow matching by
        // program (code/name) or by enrollment->strand_id
        $selectedStrand = null;
        if ($request->filled('strand') && $request->strand !== 'all') {
            $selectedStrand = Strand::where('code', $request->strand)->orWhere('name', $request->strand)->first();
        }

        $query = Student::with(['studentEnrollments' => function($q) use ($activeYear) {
                $q->where('status', 'enrolled')
                  ->when($activeYear, fn($sq) => $sq->where('academic_year_id', $activeYear->id))
                  ->with(['academicYearStrandSection.section', 'academicYear']);
        }])->orderBy('last_name')->orderBy('first_name');

        // Apply strand filter: match student.program OR their enrollment->strand
        if ($selectedStrand) {
            $query->where(function($q) use ($selectedStrand) {
                $q->where('program', $selectedStrand->code)
                  ->orWhere('program', $selectedStrand->name)
                  ->orWhereHas('studentEnrollments', function($sq) use ($selectedStrand) {
                      $sq->where('strand_id', $selectedStrand->id);
                  });
            });
        }

        // Apply grade level filter via enrollments' section grade
        if ($request->filled('grade_level') && $request->grade_level !== 'all') {
            $gradeLevel = $request->grade_level;
            $query->whereHas('studentEnrollments', function($sq) use ($gradeLevel, $activeYear) {
                $sq->where('status', 'enrolled')
                   ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                   ->whereHas('academicYearStrandSection', function($ssq) use ($gradeLevel) {
                       $ssq->whereHas('section', function($s) use ($gradeLevel) {
                           $s->where(function($q) use ($gradeLevel) {
                               $q->where('grade', 'G-' . $gradeLevel)
                                 ->orWhere('grade', 'Grade ' . $gradeLevel)
                                 ->orWhere('grade', $gradeLevel);
                           });
                       });
                   });
            });
        }

        // Apply section filter via enrollments
        if ($request->filled('section') && $request->section !== 'all') {
            $sectionId = $request->section;
            $query->whereHas('studentEnrollments', function($sq) use ($sectionId, $activeYear) {
                $sq->where('status', 'enrolled')
                   ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                   ->whereHas('academicYearStrandSection', function($ssq) use ($sectionId) {
                       $ssq->where('section_id', $sectionId);
                   });
            });
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(last_name, ', ', first_name)"), 'like', "%{$search}%");
            });
        }

        // Paginate results (preserve query string)
        $students = $query->paginate(20)->withQueryString();
        
        // Get active academic year
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        
        // Load existing student enrollments from database
        $existingAssignments = [];
        if ($activeYear) {
            $enrollments = \App\Models\StudentEnrollment::with([
                'student',
                'strand',
                'academicYearStrandSection.section'
            ])
            ->where('academic_year_id', $activeYear->id)
            ->whereNotNull('academic_year_strand_section_id')
            ->get();
            
            foreach ($enrollments as $enrollment) {
                if ($enrollment->student && $enrollment->strand && $enrollment->academicYearStrandSection && $enrollment->academicYearStrandSection->section) {
                    $existingAssignments[] = [
                        'student_id' => $enrollment->student_id,
                        'strand_code' => $enrollment->strand->code,
                        'section_id' => $enrollment->academicYearStrandSection->section_id,
                        'section_name' => $enrollment->academicYearStrandSection->section->name,
                        'section_grade' => $enrollment->academicYearStrandSection->section->grade,
                    ];
                }
            }
        }
        
        return view('admin.assigning_list.index', compact('students', 'strands', 'gradeLevels', 'sections', 'existingAssignments'));
    }

    /**
     * Save student section assignments
     */
    public function saveAssignments(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.student_id' => 'required|exists:students,id',
            'assignments.*.strand_code' => 'required|string',
            'assignments.*.section_id' => 'required|exists:sections,id',
        ]);
        
        // Get active academic year
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active academic year found. Please set an active academic year first.'
            ], 422);
        }
        
        // Maximum students per section
        $maxStudentsPerSection = 30;
        
        $savedCount = 0;
        $errors = [];
        $sectionCounts = []; // Track section counts during this operation
        
        foreach ($validated['assignments'] as $assignment) {
            try {
                // Find the strand
                $strand = \App\Models\Strand::where('code', $assignment['strand_code'])->first();
                if (!$strand) {
                    $errors[] = "Strand {$assignment['strand_code']} not found";
                    continue;
                }
                
                // Find or create the academic_year_strand_section record
                $academicYearStrandSection = \App\Models\AcademicYearStrandSection::firstOrCreate(
                    [
                        'academic_year_id' => $activeYear->id,
                        'strand_id' => $strand->id,
                        'section_id' => $assignment['section_id'],
                    ],
                    [
                        'is_active' => true,
                    ]
                );
                
                // Check if student is already enrolled
                $existingEnrollment = \App\Models\StudentEnrollment::where('student_id', $assignment['student_id'])
                    ->where('academic_year_id', $activeYear->id)
                    ->first();
                
                // Initialize section count if not set
                $sectionKey = $academicYearStrandSection->id;
                if (!isset($sectionCounts[$sectionKey])) {
                    // Get current student count in this section
                    $sectionCounts[$sectionKey] = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $academicYearStrandSection->id)
                        ->where('academic_year_id', $activeYear->id)
                        ->count();
                }
                
                // Check if this is a new enrollment or just moving sections
                $isNewEnrollment = !$existingEnrollment || $existingEnrollment->academic_year_strand_section_id !== $academicYearStrandSection->id;
                
                // If it's a new enrollment to this section, check capacity
                if ($isNewEnrollment) {
                    if ($sectionCounts[$sectionKey] >= $maxStudentsPerSection) {
                        $section = \App\Models\Section::find($assignment['section_id']);
                        $sectionName = $section ? "{$section->grade} {$section->name}" : "Section ID {$assignment['section_id']}";
                        $student = \App\Models\Student::find($assignment['student_id']);
                        $studentName = $student ? "{$student->first_name} {$student->last_name}" : "Student ID {$assignment['student_id']}";
                        $errors[] = "Cannot assign {$studentName} to {$sectionName}: Section is full (maximum {$maxStudentsPerSection} students)";
                        continue;
                    }
                    // If moving from another section, decrement that section's count
                    if ($existingEnrollment && $existingEnrollment->academic_year_strand_section_id) {
                        $oldSectionKey = $existingEnrollment->academic_year_strand_section_id;
                        if (isset($sectionCounts[$oldSectionKey])) {
                            $sectionCounts[$oldSectionKey]--;
                        }
                    }
                    // Increment the new section's count
                    $sectionCounts[$sectionKey]++;
                }
                
                if ($existingEnrollment) {
                    // Update existing enrollment
                    $existingEnrollment->update([
                        'strand_id' => $strand->id,
                        'academic_year_strand_section_id' => $academicYearStrandSection->id,
                        'status' => 'enrolled'
                    ]);
                    try {
                        $existingEnrollment->syncSubjectEnrollments();
                    } catch (\Throwable $e) {
                        Log::warning('Failed to sync subject enrollments for enrollment ID ' . ($existingEnrollment->id ?? 'unknown') . ': ' . $e->getMessage());
                    }
                } else {
                    // Generate registration number
                    $registrationNumber = $this->generateRegistrationNumber($activeYear->id);
                    
                    // Create new enrollment
                    \App\Models\StudentEnrollment::create([
                        'student_id' => $assignment['student_id'],
                        'strand_id' => $strand->id,
                        'academic_year_id' => $activeYear->id,
                        'academic_year_strand_section_id' => $academicYearStrandSection->id,
                        'registration_number' => $registrationNumber,
                        'status' => 'enrolled'
                    ]);
                    // After creating, attempt to sync subject enrollments for the new row
                    try {
                        $newEnroll = \App\Models\StudentEnrollment::where('student_id', $assignment['student_id'])
                            ->where('academic_year_id', $activeYear->id)
                            ->first();
                        if ($newEnroll) { $newEnroll->syncSubjectEnrollments(); }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to sync subject enrollments after creating enrollment for student ID ' . ($assignment['student_id'] ?? 'unknown') . ': ' . $e->getMessage());
                    }
                }
                
                $savedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error saving student ID {$assignment['student_id']}: " . $e->getMessage();
            }
        }
        
        // Store student assignments in session for reference
        session(['student_assignments' => $validated['assignments']]);
        
        if ($savedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No assignments were saved. ' . implode('; ', $errors)
            ], 422);
        }
        
        $message = "Successfully saved {$savedCount} student assignment(s) to database!";
        if (!empty($errors)) {
            $message .= " Some errors occurred: " . implode('; ', array_slice($errors, 0, 3));
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $savedCount,
            'errors' => $errors
        ]);
    }
    
    /**
     * Generate a unique registration number for student enrollment
     */
    private function generateRegistrationNumber($academicYearId)
    {
        $year = date('Y');
        $prefix = "REG-{$year}-";
        
        // Get the last registration number for this year
        $lastEnrollment = \App\Models\StudentEnrollment::where('academic_year_id', $academicYearId)
            ->where('registration_number', 'like', "{$prefix}%")
            ->orderBy('registration_number', 'desc')
            ->first();
        
        if ($lastEnrollment) {
            // Extract number and increment
            $lastNumber = (int) str_replace($prefix, '', $lastEnrollment->registration_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
