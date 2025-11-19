<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;

$studentNumber = '2025-00021';
$s = Student::where('student_number', $studentNumber)->first();
if (!$s) {
    echo "Student not found: $studentNumber\n";
    exit(1);
}

$en = StudentEnrollment::where('student_id', $s->id)
    ->with(['academicYear','subjectEnrollments.academicYearStrandSubject.subject','academicYearStrandSection.section'])
    ->get();

echo "Student ID: {$s->id} ({$s->student_number})\n";
foreach ($en as $e) {
    $ayName = $e->academicYear?->name ?? 'N/A';
    $sectionName = $e->academicYearStrandSection?->section?->name ?? 'N/A';
    echo "Enrollment ID: {$e->id} AY: {$ayName} ({$e->academic_year_id}) Section: {$sectionName}\n";
    foreach ($e->subjectEnrollments as $se) {
        $subName = $se->academicYearStrandSubject->subject?->name ?? 'N/A';
        $fq = $se->fq_grade ?? 'null';
        $sq = $se->sq_grade ?? 'null';
        echo "  SE ID: {$se->id} AYS ID: {$se->academic_year_strand_subject_id} Subject: {$subName} FQ: {$fq} SQ: {$sq}\n";
    }
}

return 0;
