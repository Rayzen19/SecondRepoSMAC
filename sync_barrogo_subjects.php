<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentEnrollment;

echo "=== Syncing Subject Enrollments for John Raymond Barrogo ===\n\n";

// Find the student enrollment (we know it's ID 455 from the previous check)
$enrollment = StudentEnrollment::find(455);

if (!$enrollment) {
    echo "❌ Student enrollment not found!\n";
    exit;
}

echo "✅ Student Enrollment Found:\n";
echo "   ID: {$enrollment->id}\n";
echo "   Student ID: {$enrollment->student_id}\n";
echo "   Academic Year ID: {$enrollment->academic_year_id}\n";
echo "   Section ID: {$enrollment->academic_year_strand_section_id}\n\n";

echo "🔄 Syncing subject enrollments...\n";

$created = $enrollment->syncSubjectEnrollments();

echo "\n✅ Sync Complete!\n";
echo "   Created {$created} new subject enrollments\n\n";

// Reload and check
$enrollment->load('subjectEnrollments.academicYearStrandSubject.subject');

echo "📚 Current Subject Enrollments: {$enrollment->subjectEnrollments->count()}\n\n";

if ($enrollment->subjectEnrollments->count() > 0) {
    echo "Enrolled Subjects:\n";
    foreach ($enrollment->subjectEnrollments as $se) {
        $subject = $se->academicYearStrandSubject->subject;
        echo "   - [{$subject->code}] {$subject->name} ({$subject->type})\n";
    }
}
