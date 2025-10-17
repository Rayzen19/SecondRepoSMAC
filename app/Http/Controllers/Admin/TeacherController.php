<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TeacherWelcome;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\StudentEnrollment;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Strand;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('subjects')->orderBy('last_name')->paginate(15);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function assignments(Teacher $teacher)
    {
        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('name')->get();
        $strands = Strand::where('is_active', true)->orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        // Get existing assignments for this teacher
        $existingAssignments = AcademicYearStrandSubject::with(['academicYear', 'strand', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.teachers.assignments', compact('teacher', 'academicYears', 'strands', 'sections', 'subjects', 'existingAssignments'));
    }

    public function storeAssignment(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'strand_id' => 'required|exists:strands,id',
            'subject_id' => 'required|exists:subjects,id',
            'written_works_percentage' => 'nullable|numeric|min:0|max:100',
            'performance_tasks_percentage' => 'nullable|numeric|min:0|max:100',
            'quarterly_assessment_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if assignment already exists
        $exists = AcademicYearStrandSubject::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('strand_id', $data['strand_id'])
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This assignment already exists for this teacher.');
        }

        // Create the assignment
        AcademicYearStrandSubject::create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $data['academic_year_id'],
            'strand_id' => $data['strand_id'],
            'subject_id' => $data['subject_id'],
            'written_works_percentage' => $data['written_works_percentage'] ?? 30,
            'performance_tasks_percentage' => $data['performance_tasks_percentage'] ?? 50,
            'quarterly_assessment_percentage' => $data['quarterly_assessment_percentage'] ?? 20,
        ]);

        return redirect()->route('admin.teachers.assignments', $teacher)
            ->with('success', 'Assignment created successfully.');
    }

    public function deleteAssignment(Teacher $teacher, AcademicYearStrandSubject $assignment)
    {
        // Verify the assignment belongs to this teacher
        if ($assignment->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->delete();

        return redirect()->route('admin.teachers.assignments', $teacher)
            ->with('success', 'Assignment deleted successfully.');
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('admin.teachers.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_number' => 'required|string|max:50|unique:teachers,employee_number',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:255|unique:teachers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'department' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'term' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,retired,resigned',
            'profile_picture' => 'nullable|string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $teacher = Teacher::create($data);

        // Attach subjects the teacher can teach
        if (isset($data['subjects'])) {
            $teacher->subjects()->attach($data['subjects']);
        }

        // Create corresponding auth user so the teacher can log in
        $initialPassword = Str::random(12);
        $user = User::updateOrCreate(
            ['email' => $teacher->email],
            [
                'name' => $teacher->name,
                'password' => Hash::make($initialPassword),
                'type' => 'teacher',
                'user_pk_id' => $teacher->id,
                'email_verified_at' => now(),
            ]
        );

        // Send welcome email to teacher with login credentials
        try {
            Mail::to($teacher->email)->send(new TeacherWelcome($teacher->name, $teacher->email, $initialPassword));
            $emailStatus = 'Welcome email sent successfully to ' . $teacher->email;
        } catch (\Exception $e) {
            $emailStatus = 'Teacher created but email failed to send: ' . $e->getMessage();
        }

        return redirect()->route('admin.teachers.show', $teacher)
            ->with('success', 'Teacher created successfully. ' . $emailStatus)
            ->with('initial_password', $initialPassword);
    }

    public function show(Teacher $teacher)
    {
        // Academic years for left-nav (active first)
        $academicYears = AcademicYear::orderByDesc('is_active')
            ->orderByDesc('name')
            ->get();

        // Subject assignments taught by this teacher, grouped by AY
        $subjectAssignments = AcademicYearStrandSubject::with([
                'academicYear',
                'strand',
                'subject',
                'subjectEnrollments.studentEnrollment.student',
                'subjectEnrollments.studentEnrollment.academicYearStrandSection.section',
            ])
            ->where('teacher_id', $teacher->id)
            ->get()
            ->groupBy('academic_year_id');

        // Adviser sections handled by this teacher, grouped by AY
        $adviserSections = AcademicYearStrandSection::with(['academicYear', 'strand', 'section'])
            ->where('adviser_teacher_id', $teacher->id)
            ->get()
            ->groupBy('academic_year_id');

        // Preload students per advised section for quick inline display
        $adviserSectionIds = $adviserSections->flatten()->pluck('id')->all();
        $studentsBySection = collect();
        if (!empty($adviserSectionIds)) {
            $enrollments = StudentEnrollment::with(['student'])
                ->whereIn('academic_year_strand_section_id', $adviserSectionIds)
                ->get()
                ->groupBy('academic_year_strand_section_id');
            $studentsBySection = $enrollments;
        }

        // Overview stats
        $allAssignments = $subjectAssignments->flatten();
        $uniqueStudents = $allAssignments
            ->flatMap(function ($asmt) {
                return $asmt->subjectEnrollments->map(function ($se) {
                    return optional(optional($se->studentEnrollment)->student)->id;
                });
            })
            ->filter()
            ->unique();

        $totalSubjects = $allAssignments->count();
        $totalAdvisedSections = $adviserSections->flatten()->count();
        $totalStudentsTaught = $uniqueStudents->count();

        return view('admin.teachers.show', compact(
            'teacher',
            'academicYears',
            'subjectAssignments',
            'adviserSections',
            'studentsBySection',
            'totalSubjects',
            'totalAdvisedSections',
            'totalStudentsTaught'
        ));
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::orderBy('name')->get();
        return view('admin.teachers.edit', compact('teacher', 'subjects'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'employee_number' => 'required|string|max:50|unique:teachers,employee_number,' . $teacher->id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:255|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'department' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'term' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,retired,resigned',
            'profile_picture' => 'nullable|string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $teacher->update($data);

        // Sync the subjects the teacher can teach
        if (isset($data['subjects'])) {
            $teacher->subjects()->sync($data['subjects']);
        } else {
            $teacher->subjects()->sync([]);
        }

        // Keep auth user in sync if present
        $user = User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->first();
        if ($user) {
            $user->name = $teacher->name;
            $user->email = $teacher->email;
            $user->save();
        }

        return redirect()->route('admin.teachers.show', $teacher)->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        // Remove linked auth user if present
        $user = User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->first();
        if ($user) {
            $user->delete();
        }

        // Delete the teacher record
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function subjectStudents(Teacher $teacher, AcademicYear $academicYear, AcademicYearStrandSubject $assignment)
    {
        // Guard: ensure mapping is valid
        abort_unless($assignment->teacher_id === $teacher->id && $assignment->academic_year_id === $academicYear->id, 404);

        $assignment->load(['academicYear', 'strand', 'subject']);

        $subjectEnrollments = $assignment->subjectEnrollments()
            ->with(['studentEnrollment.student', 'studentEnrollment.academicYearStrandSection.section'])
            ->get();

        $students = $subjectEnrollments->map(function ($se) {
            return [
                'student' => optional(optional($se->studentEnrollment)->student),
                'section' => optional(optional($se->studentEnrollment)->academicYearStrandSection)->section,
                'registration_number' => optional($se->studentEnrollment)->registration_number,
            ];
        })->filter(fn($row) => !is_null($row['student']))->values();

        $total = $students->count();
        $male = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'male')->count();
        $female = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'female')->count();

        return view('admin.teachers.subject_students', [
            'teacher' => $teacher,
            'academicYear' => $academicYear,
            'assignment' => $assignment,
            'students' => $students,
            'total' => $total,
            'male' => $male,
            'female' => $female,
        ]);
    }

    public function sectionStudents(Teacher $teacher, AcademicYear $academicYear, AcademicYearStrandSection $sectionAssignment)
    {
        // Validate adviser ownership and year
        abort_unless($sectionAssignment->adviser_teacher_id === $teacher->id && $sectionAssignment->academic_year_id === $academicYear->id, 404);

        $sectionAssignment->load(['academicYear', 'strand', 'section']);

        $enrollments = StudentEnrollment::with(['student'])
            ->where('academic_year_strand_section_id', $sectionAssignment->id)
            ->get();

        $students = $enrollments->map(function ($en) {
            return [
                'student' => $en->student,
                'registration_number' => $en->registration_number,
            ];
        })->filter(fn($row) => !is_null($row['student']))->values();

        $total = $students->count();
        $male = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'male')->count();
        $female = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'female')->count();

        return view('admin.teachers.section_students', [
            'teacher' => $teacher,
            'academicYear' => $academicYear,
            'sectionAssignment' => $sectionAssignment,
            'students' => $students,
            'total' => $total,
            'male' => $male,
            'female' => $female,
        ]);
    }

    public function exportSubjectStudents(Teacher $teacher, AcademicYear $academicYear, AcademicYearStrandSubject $assignment)
    {
        abort_unless($assignment->teacher_id === $teacher->id && $assignment->academic_year_id === $academicYear->id, 404);

        $assignment->load(['academicYear', 'strand', 'subject']);

        $subjectEnrollments = $assignment->subjectEnrollments()
            ->with(['studentEnrollment.student', 'studentEnrollment.academicYearStrandSection.section'])
            ->get();

        $rows = $subjectEnrollments->map(function ($se) {
            $student = optional(optional($se->studentEnrollment)->student);
            $section = optional(optional($se->studentEnrollment)->academicYearStrandSection)->section;
            return [
                'student_number' => $student->student_number ?? '',
                'last_name' => $student->last_name ?? '',
                'first_name' => $student->first_name ?? '',
                'gender' => $student->gender ? ucfirst($student->gender) : '',
                'section' => $section->name ?? '',
                'registration_number' => optional($se->studentEnrollment)->registration_number ?? '',
            ];
        })->filter(fn($r) => !empty($r['student_number']))->values();

        $filename = 'subject-students-'.$teacher->id.'-'.$assignment->id.'-'.$academicYear->id.'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Student #', 'Last Name', 'First Name', 'Gender', 'Section', 'Registration #']);
            $i = 1;
            foreach ($rows as $row) {
                fputcsv($out, [
                    $i++,
                    $row['student_number'],
                    $row['last_name'],
                    $row['first_name'],
                    $row['gender'],
                    $row['section'],
                    $row['registration_number'],
                ]);
            }
            fclose($out);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function exportSectionStudents(Teacher $teacher, AcademicYear $academicYear, AcademicYearStrandSection $sectionAssignment)
    {
        abort_unless($sectionAssignment->adviser_teacher_id === $teacher->id && $sectionAssignment->academic_year_id === $academicYear->id, 404);

        $enrollments = StudentEnrollment::with(['student'])
            ->where('academic_year_strand_section_id', $sectionAssignment->id)
            ->get();

        $rows = $enrollments->map(function ($en) {
            $student = $en->student;
            return [
                'student_number' => $student->student_number ?? '',
                'last_name' => $student->last_name ?? '',
                'first_name' => $student->first_name ?? '',
                'gender' => $student->gender ? ucfirst($student->gender) : '',
                'registration_number' => $en->registration_number ?? '',
            ];
        })->filter(fn($r) => !empty($r['student_number']))->values();

        $filename = 'section-students-'.$teacher->id.'-'.$sectionAssignment->id.'-'.$academicYear->id.'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Student #', 'Last Name', 'First Name', 'Gender', 'Registration #']);
            $i = 1;
            foreach ($rows as $row) {
                fputcsv($out, [
                    $i++,
                    $row['student_number'],
                    $row['last_name'],
                    $row['first_name'],
                    $row['gender'],
                    $row['registration_number'],
                ]);
            }
            fclose($out);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
