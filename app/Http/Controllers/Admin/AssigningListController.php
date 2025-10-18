<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Strand;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssigningListController extends Controller
{
    /**
     * Display the assigning list with all students and filters.
     */
    public function index(Request $request)
    {
        // Get all active strands for filter
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get all sections ordered by grade and name
        $sections = Section::with('strand')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
        
        // Get distinct grade levels from students
        $gradeLevels = ['11', '12']; // SHS Grade levels
        
        // Build the query
        $query = Student::query()
            ->select('students.*')
            ->whereNull('students.deleted_at')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name');
        
        // Apply strand filter
        if ($request->filled('strand') && $request->strand !== 'all') {
            $query->where('students.program', $request->strand);
        }
        
        // Apply grade level filter
        if ($request->filled('grade_level') && $request->grade_level !== 'all') {
            $query->where(function($q) use ($request) {
                $q->where('students.academic_year', 'like', '%Grade ' . $request->grade_level . '%')
                  ->orWhere('students.academic_year', 'like', '%G-' . $request->grade_level . '%')
                  ->orWhere('students.academic_year', '=', 'Grade ' . $request->grade_level)
                  ->orWhere('students.academic_year', '=', 'G-' . $request->grade_level);
            });
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('students.student_number', 'like', "%{$search}%")
                  ->orWhere('students.first_name', 'like', "%{$search}%")
                  ->orWhere('students.last_name', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(students.first_name, ' ', students.last_name)"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(students.last_name, ', ', students.first_name)"), 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $students = $query->paginate(20)->withQueryString();
        
        return view('admin.assigning_list.index', compact('students', 'strands', 'gradeLevels', 'sections'));
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
        
        $savedCount = 0;
        $errors = [];
        
        foreach ($validated['assignments'] as $assignment) {
            try {
                // Find the strand
                $strand = \App\Models\Strand::where('code', $assignment['strand_code'])->first();
                if (!$strand) {
                    $errors[] = "Strand {$assignment['strand_code']} not found";
                    continue;
                }
                
                // Find the academic_year_strand_section record
                $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                    ->where('strand_id', $strand->id)
                    ->where('section_id', $assignment['section_id'])
                    ->first();
                
                if (!$academicYearStrandSection) {
                    $errors[] = "Section assignment not found for strand {$assignment['strand_code']} and section ID {$assignment['section_id']}";
                    continue;
                }
                
                // Check if student is already enrolled
                $existingEnrollment = \App\Models\StudentEnrollment::where('student_id', $assignment['student_id'])
                    ->where('academic_year_id', $activeYear->id)
                    ->first();
                
                if ($existingEnrollment) {
                    // Update existing enrollment
                    $existingEnrollment->update([
                        'strand_id' => $strand->id,
                        'academic_year_strand_section_id' => $academicYearStrandSection->id,
                        'status' => 'enrolled'
                    ]);
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
