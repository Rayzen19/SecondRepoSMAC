<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandAdviser;
use App\Models\AcademicYearStrandSection;
use App\Models\Auth\StudentUser;
use App\Models\Auth\GuardianUser;
use App\Models\Strand;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Section;
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
        $query = Student::with(['studentEnrollments' => function($q) {
            $q->where('status', 'enrolled')
              ->with(['academicYearStrandSection.section', 'academicYear'])
              ->whereHas('academicYear', function($aq) {
                  $aq->where('is_active', true);
              });
        }]);
        
        // Exclude inactive (archived) students from the main list
        $query->where('status', '!=', 'inactive');
        
        // Apply filters
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status !== 'inactive') { // Prevent showing inactive students even if filtered
                $query->where('status', $status);
            }
        }
        
        if ($request->filled('strand')) {
            $strandId = $request->input('strand');
            // Filter students by program/strand
            $strand = Strand::find($strandId);
            if ($strand) {
                $query->where(function($q) use ($strand) {
                    $q->where('program', $strand->code)
                      ->orWhere('program', $strand->name);
                });
            }
        }
        
        if ($request->filled('grade_level')) {
            $gradeLevel = $request->input('grade_level');
            // Filter students by grade level through their enrollments
            $query->whereHas('studentEnrollments', function($q) use ($gradeLevel) {
                $q->where('status', 'enrolled')
                  ->whereHas('academicYearStrandSection', function($sq) use ($gradeLevel) {
                      $sq->whereHas('section', function($ssq) use ($gradeLevel) {
                          $ssq->where('grade', $gradeLevel);
                      });
                  })
                  ->whereHas('academicYear', function($aq) {
                      $aq->where('is_active', true);
                  });
            });
        }
        
        if ($request->filled('section')) {
            $sectionId = $request->input('section');
            // Filter students by section through their enrollments
            $query->whereHas('studentEnrollments', function($q) use ($sectionId) {
                $q->where('status', 'enrolled')
                  ->whereHas('academicYearStrandSection', function($sq) use ($sectionId) {
                      $sq->where('section_id', $sectionId);
                  })
                  ->whereHas('academicYear', function($aq) {
                      $aq->where('is_active', true);
                  });
            });
        }
        
        $students = $query->get();
        $no_students = $students->count();
        $no_active_students = $students->where('status', 'active')->count();
        $no_dropped_students = $students->where('status', 'dropped')->count();
        $no_graduated_students = $students->where('status', 'graduated')->count();
        $no_new_students = $students->where('created_at', '>=', now()->startOfYear())->count();
        
        // Get all strands for filter dropdown
        $strands = Strand::where('is_active', true)->orderBy('name')->get();
        
        // Get sections based on filters
        $sectionsQuery = Section::query();
        if ($request->filled('grade_level')) {
            $sectionsQuery->where('grade', $request->input('grade_level'));
        }
        if ($request->filled('strand')) {
            $sectionsQuery->where('strand_id', $request->input('strand'));
        }
        $sections = $sectionsQuery->orderBy('name')->get();
        
        return view('admin.students.index', compact('students', 'no_students', 'no_active_students', 'no_dropped_students', 'no_graduated_students', 'no_new_students', 'strands', 'sections'));
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
            'grade_level' => 'required|string|max:50',
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

        // Note: Enrollment is automatically created by StudentObserver
        // No need to create it here to avoid duplication

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully. Login details have been emailed to student and guardian.');
    }

    // Show the form for editing the specified student
    public function edit(Student $student)
    {
        // Get the active Academic Year
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

        return view('admin.students.edit', compact('student', 'activeStrands'));
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
            'grade_level' => 'nullable|string|max:50',
            'status' => 'required|in:active,graduated,dropped,inactive',
        ]);

        // Update the student record
        $student->update($validated);

        // Update the linked StudentUser email if it changed
        if ($student->wasChanged('email')) {
            $user = SystemUser::where('type', 'student')->where('user_pk_id', $student->id)->first();
            if ($user) {
                $user->update([
                    'email' => $student->email,
                    'name' => $student->name,
                ]);
            }
        }

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
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

        // Get strand-specific subjects
        $strandSubjects = collect();
        if ($student->program) {
            // Find the strand by code or name
            $strand = Strand::where('code', $student->program)
                ->orWhere('name', $student->program)
                ->first();
            
            if ($strand) {
                // Get all subjects for this strand
                $strandSubjects = $strand->subjects()
                    ->wherePivot('is_active', true)
                    ->wherePivotNull('deleted_at')
                    ->get()
                    ->map(function($subject) {
                        return [
                            'id' => $subject->id,
                            'code' => $subject->code,
                            'name' => $subject->name,
                            'hours' => $subject->hours,
                            'units' => $subject->units,
                            'type' => $subject->type,
                            'semester' => $subject->semester,
                        ];
                    });
            }
        }

        return view('admin.students.show', compact('student', 'enrollments', 'academicYears', 'totalEnrollments', 'totalSubjects', 'strandSubjects'));
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

    // Remove the specified student from storage (Archive instead of permanent deletion)
    public function destroy(Student $student)
    {
        // Archive the student by setting status to 'inactive'
        $student->update(['status' => 'inactive']);

        return redirect()->route('admin.students.index')->with('success', 'Student has been archived. You can restore it from the archive page.');
    }

    /**
     * Add a subject to a student's active enrollment
     */
    public function addSubject(Request $request, Student $student)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Get the active enrollment for this student
        $activeEnrollment = StudentEnrollment::with(['subjectEnrollments.academicYearStrandSubject'])
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            })
            ->first();

        if (!$activeEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No active enrollment found for this student.'
            ], 400);
        }

        // Check if subject is already enrolled
        $existingSubject = $activeEnrollment->subjectEnrollments
            ->filter(function($enrollment) use ($validated) {
                return $enrollment->academicYearStrandSubject 
                    && $enrollment->academicYearStrandSubject->subject_id == $validated['subject_id'];
            })
            ->isNotEmpty();

        if ($existingSubject) {
            return response()->json([
                'success' => false,
                'message' => 'This subject is already enrolled for this student.'
            ], 400);
        }

        // Get the academic year strand subject record
        $academicYearStrandSubject = DB::table('academic_year_strand_subjects')
            ->where('academic_year_id', $activeEnrollment->academic_year_id)
            ->where('strand_id', $activeEnrollment->strand_id)
            ->where('subject_id', $validated['subject_id'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$academicYearStrandSubject) {
            return response()->json([
                'success' => false,
                'message' => 'Subject is not configured for this strand and academic year.'
            ], 400);
        }

        // Create subject enrollment
        try {
            DB::table('subject_enrollments')->insert([
                'student_enrollment_id' => $activeEnrollment->id,
                'academic_year_strand_subject_id' => $academicYearStrandSubject->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject added successfully to student enrollment.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to add subject to student', [
                'student_id' => $student->id,
                'subject_id' => $validated['subject_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add subject. Please try again.'
            ], 500);
        }
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
