<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandAdviser;
use App\Models\Auth\StudentUser;
use App\Models\Auth\GuardianUser;
use App\Models\Strand;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\User as SystemUser;
use App\Models\StudentEnrollment;
use App\Models\SubjectRecord;
use App\Mail\StudentWelcome;
use App\Mail\GuardianNotification;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        // Send notification to guardian as well
        if (!empty($student->guardian_email)) {
            // Get guardian and generate/retrieve password
            $guardian = $student->guardians()->first();
            $guardianPassword = 'Check your previous email';
            
            if ($guardian) {
                // Generate new guardian password
                $guardianPassword = Str::password(12, symbols: true);
                
                // Update guardian user password
                $guardianUser = SystemUser::where('email', $guardian->email)->where('type', 'guardian')->first();
                if ($guardianUser) {
                    $guardianUser->forceFill([
                        'password' => Hash::make($guardianPassword),
                    ])->save();
                    
                    // Store encrypted copy
                    $guardian->forceFill([
                        'generated_password_encrypted' => Crypt::encryptString($guardianPassword),
                    ])->save();
                }
            }
            
            try {
                Mail::to($student->guardian_email)->send(new GuardianNotification(
                    $student->guardian_name ?? 'Guardian',
                    $student->name,
                    $student->email,
                    $plainPassword,
                    $student->student_number,
                    $student->guardian_email,
                    $guardianPassword
                ));
            } catch (\Throwable $e) {
                Log::warning('Failed to send guardian password generation email', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'New password generated and saved. Emails sent to student and guardian.');
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
        // If a soft-deleted student exists with the same email, purge it to avoid unique constraint conflicts
        if ($request->filled('email')) {
            try {
                Student::onlyTrashed()->where('email', $request->input('email'))->forceDelete();
            } catch (\Throwable $e) {
                // non-blocking cleanup
            }
        }

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

        // Create or find Guardian and link to student
        $guardianData = null;
        if (!empty($validated['guardian_email'])) {
            $guardianData = $this->createOrFindGuardian($validated, $student);
            if ($guardianData) {
                // Link guardian to student
                $student->guardians()->attach($guardianData['guardian']->id);
            }
        }

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
        // Clean up any lingering auth user accounts that belong to previously deleted students with the same email
        SystemUser::where('email', $student->email)
            ->where('type', 'student')
            ->where(function($q) use ($student) {
                $q->whereNull('user_pk_id')->orWhere('user_pk_id', '!=', $student->id);
            })
            ->delete();
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

        // Send welcome email with credentials to student
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

        // Send notification email to guardian with student's credentials
        if (!empty($student->guardian_email) && $guardianData) {
            try {
                Mail::to($student->guardian_email)->send(new GuardianNotification(
                    $student->guardian_name ?? 'Guardian',
                    $student->name,
                    $student->email,
                    $plainPassword,
                    $student->student_number,
                    $guardianData['guardian']->email,
                    $guardianData['plainPassword'] ?? 'Already set - check your previous email'
                ));
            } catch (\Throwable $e) {
                // Log but don't block creation if mail fails
                Log::error('Failed sending guardian notification email', [
                    'student_id' => $student->id,
                    'guardian_email' => $student->guardian_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully. Login details have been emailed to student and guardian.');
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
        // If updating email, purge any soft-deleted student with the target email
        if ($request->filled('email') && $request->input('email') !== $student->email) {
            try {
                Student::onlyTrashed()->where('email', $request->input('email'))->forceDelete();
            } catch (\Throwable $e) {
                // non-blocking
            }
        }

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
            'grade_level' => 'required|string|in:Grade 11,Grade 12',
            'status' => 'required|in:active,graduated,dropped',
        ]);

        // Update the academic_year field with the selected grade_level
        $validated['academic_year'] = $validated['grade_level'];
        unset($validated['grade_level']);

        $student->update($validated);

        // Update or create guardian if guardian email changed
        if (!empty($validated['guardian_email'])) {
            // Check if the guardian email has changed
            $currentGuardian = $student->guardians()->first();
            
            if (!$currentGuardian || $currentGuardian->email !== $validated['guardian_email']) {
                // Detach old guardian if exists
                if ($currentGuardian) {
                    $student->guardians()->detach($currentGuardian->id);
                }
                
                // Create or find new guardian and attach
                $guardianData = $this->createOrFindGuardian($validated, $student);
                if ($guardianData) {
                    // Check if already attached (in case of re-linking)
                    if (!$student->guardians()->where('guardian_id', $guardianData['guardian']->id)->exists()) {
                        $student->guardians()->attach($guardianData['guardian']->id);
                    }
                }
            } else {
                // Same guardian, update their information
                $nameParts = $this->parseGuardianName($validated['guardian_name'] ?? $currentGuardian->name);
                $currentGuardian->update([
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                    'suffix' => $nameParts['suffix'],
                    'mobile_number' => $validated['guardian_contact'],
                    'address' => $validated['address'] ?? $currentGuardian->address,
                ]);
            }
        }

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    // Remove the specified student from storage
    public function destroy(Student $student)
    {
        // Permanently delete the student and linked auth account
        DB::transaction(function () use ($student) {
            // Detach guardians from student
            $student->guardians()->detach();

            // Delete linked auth user (student portal account)
            $user = SystemUser::where('type', 'student')->where('user_pk_id', $student->id)->first();
            if ($user) {
                $user->delete(); // User model does not use SoftDeletes -> hard delete
            }

            // Optionally remove any profile picture from storage if using a path
            try {
                if (!empty($student->profile_picture) && Storage::disk('public')->exists($student->profile_picture)) {
                    Storage::disk('public')->delete($student->profile_picture);
                }
            } catch (\Throwable $e) {
                // Non-blocking
            }

            // Force delete student (bypass soft deletes)
            if (method_exists($student, 'forceDelete')) {
                $student->forceDelete();
            } else {
                $student->delete();
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Student permanently deleted.');
    }

    /**
     * Create or find a guardian based on the provided data
     * Returns array with guardian and plainPassword
     */
    private function createOrFindGuardian(array $validatedData, Student $student): ?array
    {
        if (empty($validatedData['guardian_email'])) {
            return null;
        }

        // Check if guardian already exists by email
        $guardian = Guardian::where('email', $validatedData['guardian_email'])->first();
        $plainPassword = null;
        $isNewGuardian = false;

        if ($guardian) {
            // Guardian exists, generate new password if they don't have a user account
            $existingUser = SystemUser::where('email', $guardian->email)->where('type', 'guardian')->first();
            if (!$existingUser) {
                $plainPassword = Str::password(12, symbols: true);
                $isNewGuardian = true; // Need to create user account
            }
            
            Log::info('Using existing guardian', [
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
                'needs_user_account' => $isNewGuardian,
            ]);
        } else {
            // Create new guardian
            $isNewGuardian = true;
            $plainPassword = Str::password(12, symbols: true);
            
            // Parse guardian name into first, middle, last names
            $guardianName = $validatedData['guardian_name'] ?? 'Guardian';
            $nameParts = $this->parseGuardianName($guardianName);

            // Generate guardian number
            $year = now()->year;
            $lastGuardianNumber = Guardian::withTrashed()
                ->where('guardian_number', 'like', 'GRD-' . $year . '-%')
                ->orderByDesc('guardian_number')
                ->value('guardian_number');

            if ($lastGuardianNumber) {
                $parts = explode('-', $lastGuardianNumber);
                $lastSeq = isset($parts[2]) ? (int) $parts[2] : 0;
            } else {
                $lastSeq = 0;
            }

            $guardianNumber = 'GRD-' . $year . '-' . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

            // Create new guardian
            try {
                $guardian = Guardian::create([
                    'guardian_number' => $guardianNumber,
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                    'suffix' => $nameParts['suffix'],
                    'gender' => 'male', // Default, can be updated later
                    'email' => $validatedData['guardian_email'],
                    'mobile_number' => $validatedData['guardian_contact'],
                    'address' => $validatedData['address'] ?? null,
                    'status' => 'active',
                ]);

                Log::info('Created new guardian', [
                    'guardian_id' => $guardian->id,
                    'guardian_number' => $guardianNumber,
                    'student_id' => $student->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to create guardian', [
                    'error' => $e->getMessage(),
                    'student_id' => $student->id,
                    'guardian_email' => $validatedData['guardian_email'],
                ]);
                return null;
            }
        }

        // Create guardian user account if needed
        if ($isNewGuardian && $plainPassword) {
            try {
                // Store encrypted copy of the generated password on the guardian profile
                $guardian->forceFill([
                    'generated_password_encrypted' => Crypt::encryptString($plainPassword),
                ])->save();

                // Clean up any lingering auth user accounts
                SystemUser::where('email', $guardian->email)
                    ->where('type', 'guardian')
                    ->where(function($q) use ($guardian) {
                        $q->whereNull('user_pk_id')->orWhere('user_pk_id', '!=', $guardian->id);
                    })
                    ->delete();

                // Create or update GuardianUser
                $existingUser = SystemUser::where('email', $guardian->email)->first();
                if (!$existingUser) {
                    GuardianUser::query()->withoutGlobalScopes()->create([
                        'name' => $guardian->name,
                        'email' => $guardian->email,
                        'password' => Hash::make($plainPassword),
                        'type' => 'guardian',
                        'user_pk_id' => $guardian->id,
                    ]);
                } else {
                    if ($existingUser->type === 'guardian') {
                        $existingUser->forceFill([
                            'name' => $guardian->name,
                            'password' => Hash::make($plainPassword),
                            'user_pk_id' => $guardian->id,
                        ])->save();
                    }
                }

                Log::info('Created guardian user account', [
                    'guardian_id' => $guardian->id,
                    'guardian_email' => $guardian->email,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to create guardian user account', [
                    'error' => $e->getMessage(),
                    'guardian_id' => $guardian->id,
                ]);
            }
        }

        return [
            'guardian' => $guardian,
            'plainPassword' => $plainPassword,
        ];
    }

    /**
     * Parse guardian full name into components
     */
    private function parseGuardianName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName));
        $suffix = null;
        
        // Check for common suffixes
        $suffixes = ['Jr.', 'Jr', 'Sr.', 'Sr', 'II', 'III', 'IV', 'V'];
        $lastPart = end($parts);
        if (in_array($lastPart, $suffixes)) {
            $suffix = array_pop($parts);
        }

        $firstName = '';
        $middleName = null;
        $lastName = '';

        if (count($parts) === 1) {
            // Only one name, use as first name
            $firstName = $parts[0];
            $lastName = $parts[0];
        } elseif (count($parts) === 2) {
            // Two names: first and last
            $firstName = $parts[0];
            $lastName = $parts[1];
        } else {
            // Three or more names: first, middle(s), last
            $firstName = array_shift($parts);
            $lastName = array_pop($parts);
            $middleName = implode(' ', $parts);
        }

        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
        ];
    }
}
