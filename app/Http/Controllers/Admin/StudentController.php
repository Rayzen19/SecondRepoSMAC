<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandAdviser;
use App\Models\Auth\StudentUser;
use App\Models\Strand;
use App\Models\Student;
use App\Models\User as SystemUser;
use App\Models\StudentEnrollment;
use App\Models\SubjectRecord;
use App\Mail\StudentWelcome;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class StudentController extends Controller
{
    // Display a listing of students
    public function index(Request $request)
    {
        $students = Student::all();
        $no_students = $students->count();
        $no_active_students = $students->where('status', 'active')->count();
        $no_dropped_students = $students->where('status', 'dropped')->count();
        $no_graduated_students = $students->where('status', 'graduated')->count();
        $no_new_students = $students->where('created_at', '>=', now()->startOfYear())->count();
        return view('admin.students.index', compact('students', 'no_students', 'no_active_students', 'no_dropped_students', 'no_graduated_students', 'no_new_students'));
    }

    // Generate or regenerate a student's password and store encrypted copy
    public function generatePassword(Student $student)
    {
        // Generate a strong password
        $plainPassword = Str::password(12, symbols: true);

        // Update or create the linked StudentUser
        $existingUser = SystemUser::where('email', $student->email)->first();
        if (!$existingUser) {
            $existingUser = StudentUser::query()->withoutGlobalScopes()->create([
                'name' => $student->name,
                'email' => $student->email,
                'password' => Hash::make($plainPassword),
                'type' => 'student',
                'user_pk_id' => $student->id,
            ]);
        } else {
            $existingUser->forceFill([
                'name' => $student->name,
                'password' => Hash::make($plainPassword),
                'user_pk_id' => $student->id,
            ])->save();
        }

        // Store encrypted copy on Student
        $student->forceFill([
            'generated_password_encrypted' => Crypt::encryptString($plainPassword),
        ])->save();

        // Optionally send the welcome email again
        try {
            Mail::to($student->email)->send(new StudentWelcome($student->name, $student->email, $plainPassword));
        } catch (\Throwable $e) {
            Log::warning('Failed to send student password generation email', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', 'New password generated and saved.');
    }

    // Show the form for creating a new student
    public function create()
    {
        // Prefill the form with the active Academic Year (fallback to latest if none active)
        $activeYear = AcademicYear::where('is_active', true)->orderByDesc('id')->first()
            ?? AcademicYear::orderByDesc('id')->first();

        // Active strands for the active year based on configured subjects
        $activeStrands = collect();
        if ($activeYear) {
            $activeStrands = AcademicYearStrandAdviser::with('strand')
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->pluck('strand')
                ->filter()
                ->unique('id')
                ->values();
        }

        // Fallback to all strands if none configured for the year
        if ($activeStrands->isEmpty()) {
            $activeStrands = Strand::where('is_active', true)->orderBy('name')->get();
        }

        return view('admin.students.create', compact('activeYear', 'activeStrands'));
    }

    // Store a newly created student in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'email' => 'required|email|unique:students,email',
            'mobile_number' => 'required|string|unique:students,mobile_number',
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'required|string|unique:students,guardian_contact',
            'guardian_email' => 'required|email|unique:students,guardian_email',
            'program' => 'required|string|max:255',
            'academic_year' => 'required|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|in:active,graduated,dropped',
        ]);

        // Prevent using an email that's already taken by a non-student user account
        $conflictUser = SystemUser::where('email', $validated['email'])
            ->where('type', '!=', 'student')
            ->first();
        if ($conflictUser) {
            return back()->withErrors(['email' => 'This email is already used by another account ('.$conflictUser->type.'). Please use a different email for the student.'])->withInput();
        }

        // Resolve academic_year_id
        $yearModel = AcademicYear::where('name', $validated['academic_year'])->first()
            ?? AcademicYear::where('is_active', true)->orderByDesc('id')->first()
            ?? AcademicYear::orderByDesc('id')->first();
        if (!$yearModel) {
            return back()->withErrors(['academic_year' => 'No Academic Year is configured. Please create one first.'])->withInput();
        }
        $validated['academic_year_id'] = $yearModel->id;

        // Auto-generate student number per current year.
        // Include soft-deleted records when calculating the next sequence so the
        // generated student_number won't collide with trashed rows that still
        // exist in the database (unique index applies to deleted rows too).
        $year = now()->year;
        $lastStudentNumber = Student::withTrashed()
            ->where('student_number', 'like', $year . '-%')
            ->orderByDesc('student_number')
            ->value('student_number');

        if ($lastStudentNumber) {
            $parts = explode('-', $lastStudentNumber, 2);
            $lastSeq = isset($parts[1]) ? (int) $parts[1] : 0;
        } else {
            $lastSeq = 0;
        }

        $validated['student_number'] = $year . '-' . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

    // Create the Student record
    $student = Student::create($validated);

        // Create a corresponding user account with generated password
        // Generate a strong random password
        $plainPassword = Str::password(12, symbols: true);

        // Store encrypted copy of the generated password on the student profile (for display)
        try {
            $student->forceFill([
                'generated_password_encrypted' => Crypt::encryptString($plainPassword),
            ])->save();
        } catch (\Throwable $e) {
            Log::warning('Failed to encrypt/store generated password for student', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Create or update a StudentUser linked via user_pk_id
        $existingUser = SystemUser::where('email', $student->email)->first();
        if (!$existingUser) {
            $user = StudentUser::query()->withoutGlobalScopes()->create([
                'name' => $student->name,
                'email' => $student->email,
                'password' => Hash::make($plainPassword),
                'type' => 'student',
                'user_pk_id' => $student->id,
            ]);
        } else {
            // If a student user already exists for this email, update credentials and link
            if ($existingUser->type === 'student') {
                $existingUser->forceFill([
                    'name' => $student->name,
                    'password' => Hash::make($plainPassword),
                    'user_pk_id' => $student->id,
                ])->save();
                $user = $existingUser;
            } else {
                // Should not happen due to pre-check; guard anyway
                Log::warning('Email conflict when creating student user', [
                    'student_id' => $student->id,
                    'email' => $student->email,
                    'existing_user_type' => $existingUser->type,
                ]);
                return redirect()->route('admin.students.index')
                    ->with('warning', 'Student created, but account not created because the email is used by another user.');
            }
        }

        // Send welcome email with credentials
        try {
            Mail::to($student->email)->send(new StudentWelcome($student->name, $student->email, $plainPassword));
        } catch (\Throwable $e) {
            // Log but don't block creation if mail fails
            Log::error('Failed sending student welcome email', [
                'student_id' => $student->id,
                'email' => $student->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully and login details emailed.');
    }

    // Display the specified student
    public function show(Student $student)
    {
        // Load enrollments grouped by academic year, with sections and subjects
        $enrollments = StudentEnrollment::with([
                'academicYear',
                'strand',
                'academicYearStrandSection.section',
                'subjectEnrollments.academicYearStrandSubject.subject',
                'subjectEnrollments.academicYearStrandSubject.teacher',
                'subjectEnrollments.subjectRecords.results',
            ])
            ->where('student_id', $student->id)
            ->orderByDesc('academic_year_id')
            ->get()
            ->groupBy('academic_year_id');

        $academicYears = AcademicYear::whereIn('id', $enrollments->keys()->all())
            ->orderByDesc('is_active')->orderByDesc('id')->get();

        // Summary stats
        $totalEnrollments = $enrollments->flatten()->count();
        $totalSubjects = $enrollments->flatten()->flatMap(fn($e) => $e->subjectEnrollments)->count();

        return view('admin.students.show', compact('student', 'enrollments', 'academicYears', 'totalEnrollments', 'totalSubjects'));
    }

    public function exportSubjectResults(Student $student, StudentEnrollment $enrollment, SubjectRecord $subjectRecord)
    {
        // Guard
        abort_unless($enrollment->student_id === $student->id && $subjectRecord->subject_enrollment_id === optional($enrollment->subjectEnrollments->first())->id, 404);

        $subjectRecord->load(['results.student']);

        $rows = $subjectRecord->results->map(function ($res) {
            $student = $res->student;
            return [
                'student_number' => $student->student_number ?? '',
                'last_name' => $student->last_name ?? '',
                'first_name' => $student->first_name ?? '',
                'gender' => $student->gender ? ucfirst($student->gender) : '',
                'raw_score' => $res->raw_score,
                'base_score' => $res->base_score,
                'final_score' => $res->final_score,
                'date_submitted' => optional($res->date_submitted)?->format('Y-m-d'),
                'remarks' => $res->remarks,
            ];
        });

        $filename = 'subject-record-results-'.$student->id.'-'.$subjectRecord->id.'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Student #', 'Last Name', 'First Name', 'Gender', 'Raw', 'Base', 'Final', 'Date Submitted', 'Remarks']);
            $i = 1;
            foreach ($rows as $row) {
                fputcsv($out, [
                    $i++,
                    $row['student_number'],
                    $row['last_name'],
                    $row['first_name'],
                    $row['gender'],
                    $row['raw_score'],
                    $row['base_score'],
                    $row['final_score'],
                    $row['date_submitted'],
                    $row['remarks'],
                ]);
            }
            fclose($out);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // Show the form for editing the specified student
    public function edit(Student $student)
    {
        // For selects
        $activeYear = AcademicYear::where('is_active', true)->orderByDesc('id')->first()
            ?? AcademicYear::orderByDesc('id')->first();
        $activeStrands = collect();
        if ($activeYear) {
            $activeStrands = \App\Models\AcademicYearStrandSubject::with('strand')
                ->where('academic_year_id', $activeYear->id)
                ->get()
                ->pluck('strand')
                ->filter()
                ->unique('id')
                ->values();
        }
        if ($activeStrands->isEmpty()) {
            $activeStrands = \App\Models\Strand::where('is_active', true)->orderBy('name')->get();
        }
        return view('admin.students.edit', compact('student', 'activeYear', 'activeStrands'));
    }

    // Update the specified student in storage
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'mobile_number' => 'required|string|unique:students,mobile_number,' . $student->id,
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'required|string|unique:students,guardian_contact,' . $student->id,
            'guardian_email' => 'required|email|unique:students,guardian_email,' . $student->id,
            'program' => 'required|string|max:255',
            'status' => 'required|in:active,graduated,dropped',
        ]);


        $student->update($validated);
        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(Student $student)
    {
        // Permanently delete the student (bypass soft deletes)
        if (method_exists($student, 'forceDelete')) {
            $student->forceDelete();
        } else {
            // Fallback in case the model does not support forceDelete
            $student->delete();
        }
        return redirect()->route('admin.students.index')->with('success', 'Student permanently deleted.');
    }
}
