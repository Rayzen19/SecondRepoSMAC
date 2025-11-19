<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\SubjectEnrollment;

$studentNumber = '2025-00021';
$student = Student::where('student_number', $studentNumber)->first();
if (!$student) { echo "Student not found: $studentNumber\n"; exit(1); }

$ses = SubjectEnrollment::whereHas('studentEnrollment', function($q) use ($student){
    $q->where('student_id', $student->id);
})->with(['studentEnrollment.academicYear','studentEnrollment.academicYearStrandSection.section','academicYearStrandSubject.academicYear','academicYearStrandSubject.subject'])
->get();

echo "Found: " . $ses->count() . " SubjectEnrollments for student {$student->student_number} (ID: {$student->id})\n";
foreach ($ses as $se) {
    $ays = $se->academicYearStrandSubject;
    $aysAy = $ays?->academic_year_id ?? 'null';
    $subjectName = $ays?->subject?->name ?? 'N/A';
    echo "SE ID: {$se->id} | AYS ID: {$se->academic_year_strand_subject_id} | AYS_AY: {$aysAy} | Subject: {$subjectName} | StudentEnrollment ID: {$se->student_enrollment_id}\n";
}

return 0;
