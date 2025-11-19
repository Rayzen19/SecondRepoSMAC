<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSubject;
use App\Models\SubjectEnrollment;

$studentNumber = $argv[1] ?? '2025-00021';
$limitAcademicYearId = $argv[2] ?? null; // optional: academic_year_id to limit

$student = Student::where('student_number', $studentNumber)->first();
if (!$student) {
    echo "Student not found: {$studentNumber}\n";
    exit(1);
}

$studentEnrollments = StudentEnrollment::where('student_id', $student->id)->get();
if ($studentEnrollments->isEmpty()) {
    echo "No StudentEnrollment found for student {$student->student_number} (ID: {$student->id})\n";
    exit(1);
}

$created = 0;
foreach ($studentEnrollments as $se) {
    echo "Processing StudentEnrollment ID: {$se->id} AY ID: {$se->academic_year_id} SectionAssignment ID: {$se->academic_year_strand_section_id}\n";

    $query = AcademicYearStrandSubject::where('academic_year_id', $se->academic_year_id)
        ->where('academic_year_strand_section_id', $se->academic_year_strand_section_id);

    if ($limitAcademicYearId) {
        $query->where('academic_year_id', $limitAcademicYearId);
    }

    $aysList = $query->with('subject','sectionAssignment')->get();

    foreach ($aysList as $ays) {
        // check if student already has SubjectEnrollment for this AYS
        $exists = SubjectEnrollment::where('student_enrollment_id', $se->id)
            ->where('academic_year_strand_subject_id', $ays->id)
            ->exists();

        if ($exists) {
            echo "  - Already enrolled in AYS ID {$ays->id} ({$ays->subject?->name})\n";
            continue;
        }

        // create enrollment
        $seModel = SubjectEnrollment::create([
            'student_enrollment_id' => $se->id,
            'academic_year_strand_subject_id' => $ays->id,
        ]);

        if ($seModel) {
            $created++;
            echo "  + Created SubjectEnrollment ID {$seModel->id} for AYS ID {$ays->id} ({$ays->subject?->name})\n";
        }
    }
}

echo "Done. Created {$created} SubjectEnrollment(s).\n";

return 0;
