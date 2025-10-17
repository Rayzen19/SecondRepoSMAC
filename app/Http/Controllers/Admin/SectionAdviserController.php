<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strand;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class SectionAdviserController extends Controller
{
    /**
     * Display the section and adviser assignment page.
     */
    public function index()
    {
        // Get all active strands
        $strands = Strand::where('is_active', true)->orderBy('code')->get();
        
        // Get all active teachers for adviser assignment
        $teachers = Teacher::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get saved adviser assignments from session
        $savedAdvisers = session('adviser_assignments', []);
        
        return view('admin.section_advisers.index', compact('strands', 'teachers', 'savedAdvisers'));
    }
    
    /**
     * Save adviser assignments
     */
    public function saveAdvisers(Request $request)
    {
        $validated = $request->validate([
            'advisers' => 'required|array',
            'advisers.*.strand_code' => 'required|string',
            'advisers.*.section_number' => 'required|integer|between:1,4',
            'advisers.*.teacher_id' => 'required|exists:teachers,id',
        ]);
        
        // Store adviser assignments in session
        session(['adviser_assignments' => $validated['advisers']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Adviser assignments saved successfully!',
            'count' => count($validated['advisers'])
        ]);
    }
    
    /**
     * Get student details for viewing section
     */
    public function getStudents(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer',
        ]);
        
        // Fetch student details
        $students = Student::whereIn('id', $validated['student_ids'])
            ->select('id', 'student_number', 'first_name', 'last_name', 'middle_name', 'program', 'academic_year')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Remove a student from a section assignment stored in session.
     */
    public function removeStudent(Request $request)
    {
        $validated = $request->validate([
            'strand_code' => 'required|string',
            'section_number' => 'required|integer|between:1,4',
            'student_id' => 'required|integer'
        ]);

        $assignments = session('student_assignments', []);

        // Filter out the matched assignment
        $before = count($assignments);
        $assignments = array_values(array_filter($assignments, function ($a) use ($validated) {
            return !(
                (string)($a['strand_code'] ?? '') === (string)$validated['strand_code'] &&
                intval($a['section_number'] ?? 0) === intval($validated['section_number']) &&
                intval($a['student_id'] ?? 0) === intval($validated['student_id'])
            );
        }));
        $after = count($assignments);

        session(['student_assignments' => $assignments]);

        return response()->json([
            'success' => true,
            'removed' => $before - $after,
            'remaining' => $after,
        ]);
    }
}
