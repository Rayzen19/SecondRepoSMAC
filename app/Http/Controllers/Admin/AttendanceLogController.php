<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceLogController extends Controller
{
    /**
     * Display a listing of attendance logs.
     */
    public function index(Request $request)
    {
        $query = AttendanceLog::with(['student', 'academicYear'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');

        // Filter by student number/name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        // Filter by strand
        if ($request->filled('strand')) {
            $query->where('strand', $request->strand);
        }

        // Filter by year level
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by subject
        if ($request->filled('subject')) {
            $query->where('subject', 'like', "%{$request->subject}%");
        }

        // Filter by assessment type
        if ($request->filled('assessment')) {
            $query->where('assessment_type', $request->assessment);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);

        // Get filter options
        $strands = Strand::pluck('name')->unique();
        $yearLevels = ['11', '12'];
        $semesters = ['1st Semester', '2nd Semester'];
        $subjects = AttendanceLog::select('subject')->distinct()->pluck('subject')->filter();
        $assessmentTypes = AttendanceLog::select('assessment_type')->distinct()->pluck('assessment_type')->filter();

        return view('admin.attendance.index', compact('logs', 'strands', 'yearLevels', 'semesters', 'subjects', 'assessmentTypes'));
    }

    /**
     * Show the form for creating a new attendance log.
     */
    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('last_name')->get();
        $academicYears = AcademicYear::orderBy('is_active', 'desc')->orderBy('name', 'desc')->get();
        $strands = Strand::all();

        return view('admin.attendance.create', compact('students', 'academicYears', 'strands'));
    }

    /**
     * Store a newly created attendance log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:present,absent,late,excused',
            'subject' => 'nullable|string|max:255',
            'assessment_type' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Get student details
        $student = Student::findOrFail($validated['student_id']);
        
        $validated['student_number'] = $student->student_number;
        $validated['student_name'] = $student->first_name . ' ' . $student->last_name;
        $validated['recorded_by'] = Auth::guard('admin')->user()->name ?? 'Admin';

        // Get enrollment details if available
        if ($student->studentEnrollments()->exists()) {
            $enrollment = $student->studentEnrollments()->latest()->first();
            if ($enrollment && $enrollment->academicYearStrandSection) {
                $validated['strand'] = $enrollment->academicYearStrandSection->strand->name ?? null;
                $validated['year_level'] = $enrollment->academicYearStrandSection->year_level ?? null;
                $validated['section'] = $enrollment->academicYearStrandSection->section->name ?? null;
                $validated['semester'] = $enrollment->academicYearStrandSection->semester ?? null;
            }
        }

        AttendanceLog::create($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance log recorded successfully!');
    }

    /**
     * Display the specified attendance log.
     */
    public function show(AttendanceLog $log)
    {
        $log->load(['student', 'academicYear']);
        return view('admin.attendance.show', compact('log'));
    }

    /**
     * Show the form for editing the specified attendance log.
     */
    public function edit(AttendanceLog $log)
    {
        $students = Student::where('status', 'active')->orderBy('last_name')->get();
        $academicYears = AcademicYear::orderBy('is_active', 'desc')->orderBy('name', 'desc')->get();

        return view('admin.attendance.edit', compact('log', 'students', 'academicYears'));
    }

    /**
     * Update the specified attendance log.
     */
    public function update(Request $request, AttendanceLog $log)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:present,absent,late,excused',
            'subject' => 'nullable|string|max:255',
            'assessment_type' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $log->update($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance log updated successfully!');
    }

    /**
     * Remove the specified attendance log.
     */
    public function destroy(AttendanceLog $log)
    {
        $log->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance log deleted successfully!');
    }

    /**
     * Export attendance logs to CSV.
     */
    public function export(Request $request)
    {
        $query = AttendanceLog::orderBy('date', 'desc')->orderBy('time', 'desc');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('strand')) $query->where('strand', $request->strand);
        if ($request->filled('year_level')) $query->where('year_level', $request->year_level);
        if ($request->filled('semester')) $query->where('semester', $request->semester);
        if ($request->filled('subject')) $query->where('subject', 'like', "%{$request->subject}%");
        if ($request->filled('assessment')) $query->where('assessment_type', $request->assessment);
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('status', $request->status);

        $logs = $query->get();

        $filename = 'attendance-logs-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Strand',
                'Year Level',
                'Section',
                'Semester',
                'Subject',
                'Assessment',
                'Date',
                'Time',
                'Status',
                'Remarks',
                'Recorded By'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->student_number,
                    $log->student_name,
                    $log->strand,
                    $log->year_level,
                    $log->section,
                    $log->semester,
                    $log->subject,
                    $log->assessment_type,
                    $log->date->format('Y-m-d'),
                    $log->time->format('H:i'),
                    ucfirst($log->status),
                    $log->remarks,
                    $log->recorded_by
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
