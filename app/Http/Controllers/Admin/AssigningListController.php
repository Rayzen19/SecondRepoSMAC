<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Strand;
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
        
        return view('admin.assigning_list.index', compact('students', 'strands', 'gradeLevels'));
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
            'assignments.*.section_number' => 'required|integer|between:1,4',
        ]);
        
        // Store student assignments in session
        // In a full implementation, you would save to database
        session(['student_assignments' => $validated['assignments']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Student assignments saved successfully!',
            'count' => count($validated['assignments'])
        ]);
    }
}
