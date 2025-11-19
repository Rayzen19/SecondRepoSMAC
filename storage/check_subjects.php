<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

echo "Checking subjects with 'Oral' or 'Filipino' in name:\n";
echo str_repeat('=', 80) . "\n";

$subjects = Subject::where('name', 'like', '%Oral%')
    ->orWhere('name', 'like', '%Filipino%')
    ->get();

foreach ($subjects as $s) {
    echo "ID: {$s->id}\n";
    echo "Name: {$s->name}\n";
    echo "Code: {$s->code}\n";
    echo "Strand ID: " . ($s->strand_id ?? 'null') . "\n";
    
    if ($s->strand_id) {
        $strand = \App\Models\Strand::find($s->strand_id);
        echo "Strand: " . ($strand->name ?? 'unknown') . " ({$strand->code})\n";
    }
    
    echo str_repeat('-', 80) . "\n";
}

echo "\nNow checking student 2025-00021's subject enrollments:\n";
echo str_repeat('=', 80) . "\n";

$student = \App\Models\Student::where('student_number', '2025-00021')->first();
if ($student) {
    $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->where('academic_year_id', 5)
        ->first();
    
    if ($enrollment) {
        echo "Student: {$student->student_number} ({$student->first_name} {$student->last_name})\n";
        echo "Enrollment ID: {$enrollment->id}\n";
        echo "Strand ID: {$enrollment->strand_id}\n";
        
        $strand = \App\Models\Strand::find($enrollment->strand_id);
        echo "Enrolled Strand: " . ($strand->name ?? 'unknown') . " ({$strand->code})\n\n";
        
        $subjectEnrollments = \App\Models\SubjectEnrollment::with('academicYearStrandSubject.subject')
            ->where('student_enrollment_id', $enrollment->id)
            ->get();
        
        echo "Subject Enrollments ({$subjectEnrollments->count()}):\n";
        foreach ($subjectEnrollments as $se) {
            $subject = $se->academicYearStrandSubject->subject;
            echo "  - {$subject->name} ({$subject->code}) [Subject Strand: " . ($subject->strand_id ?? 'null') . "]\n";
        }
    }
}
