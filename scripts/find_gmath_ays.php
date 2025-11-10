<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\AcademicYearStrandSubject;
use App\Models\SubjectEnrollment;

$studentNumber = '2025-00021';
$keyword = 'General Mathematics';

$student = Student::where('student_number', $studentNumber)->first();
if (!$student) { echo "Student not found: $studentNumber\n"; exit(1); }

echo "Student ID: {$student->id} ({$student->student_number})\n\n";

$aysList = AcademicYearStrandSubject::with(['academicYear','subject','sectionAssignment'])
    ->whereHas('subject', function($q) use ($keyword) {
        $q->where('name', 'like', "%$keyword%");
    })->get();

if ($aysList->isEmpty()) {
    echo "No AcademicYearStrandSubject records found matching: {$keyword}\n";
    exit(0);
}

foreach ($aysList as $ays) {
    $aysId = $ays->id;
    $ayName = $ays->academicYear?->name ?? 'N/A';
    $subjectName = $ays->subject?->name ?? 'N/A';
    $sectionName = $ays->sectionAssignment?->section?->name ?? 'N/A';

    $seCount = SubjectEnrollment::where('academic_year_strand_subject_id', $aysId)->count();
    $studentSe = SubjectEnrollment::where('academic_year_strand_subject_id', $aysId)
        ->whereHas('studentEnrollment', function($q) use ($student){ $q->where('student_id', $student->id); })
        ->with('studentEnrollment')
        ->first();

    echo "AYS ID: {$aysId} | AY: {$ayName} | Subject: {$subjectName} | SectionAssignment Section: {$sectionName}\n";
    echo "  Total SubjectEnrollments for this AYS: {$seCount}\n";
    if ($studentSe) {
        echo "  Student has a SubjectEnrollment (SE ID: {$studentSe->id}) under StudentEnrollment ID: {$studentSe->student_enrollment_id}\n";
    } else {
        echo "  Student does NOT have a SubjectEnrollment for this AYS.\n";
    }
    echo "\n";
}

return 0;
