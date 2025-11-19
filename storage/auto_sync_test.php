<?php
require __DIR__ . '/..//vendor/autoload.php';
$app = require_once __DIR__ . '/..//bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\AcademicYearStrandSubject;
use App\Models\StudentEnrollment;
use App\Models\SubjectEnrollment;

$student = Student::where('student_number','2025-00021')->first();
echo "Student id: " . ($student->id ?? 'none') . PHP_EOL;

$ay = AcademicYear::where('is_active', true)->first();
echo "Active AY: " . ($ay->id ?? 'none') . PHP_EOL;

$str = Strand::first();
$sub = Subject::first();

// Use an existing teacher (or admin) to satisfy NOT NULL constraint on teacher_id
$teacher = \App\Models\Teacher::first();
$assignment = AcademicYearStrandSubject::create([
    'teacher_id' => $teacher->id ?? 1,
    'academic_year_id' => $ay->id,
    'strand_id' => $str->id,
    'subject_id' => $sub->id,
    'academic_year_strand_adviser_id' => 1,
]);

echo "Created assignment id: " . ($assignment->id ?? 'none') . PHP_EOL;

$en = StudentEnrollment::where('student_id', $student->id)->where('academic_year_id', $ay->id)->first();
echo "Enrollment id: " . ($en->id ?? 'none') . PHP_EOL;

$se = SubjectEnrollment::where('student_enrollment_id', $en->id)->where('academic_year_strand_subject_id', $assignment->id)->first();
echo "SubjectEnrollment exists? " . ($se ? 'yes' : 'no') . PHP_EOL;

// Cleanup: do not delete assignment to avoid interfering with app; just exit

return 0;
