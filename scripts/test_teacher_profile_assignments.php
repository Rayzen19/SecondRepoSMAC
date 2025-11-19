<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\AcademicYearStrandSubject;

echo "=== Testing Teacher Profile Subject Assignments ===\n\n";

// Get a random teacher
$teacher = Teacher::first();
if (!$teacher) {
    echo "❌ No teachers found in database\n";
    exit(1);
}

echo "Teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n";
echo "Employee #: {$teacher->employee_number}\n\n";

// Query subject assignments exactly as TeacherController does
$subjectAssignments = AcademicYearStrandSubject::with([
        'academicYear',
        'strand',
        'subject',
        'subjectEnrollments.studentEnrollment.student',
        'subjectEnrollments.studentEnrollment.academicYearStrandSection.section',
    ])
    ->where('teacher_id', $teacher->id)
    ->get();

echo "Total Subject Assignments: " . $subjectAssignments->count() . "\n\n";

if ($subjectAssignments->isEmpty()) {
    echo "⚠️  No subject assignments found for this teacher.\n";
    echo "This is expected if you haven't assigned any subjects via Section & Advisers interface yet.\n\n";
    echo "To assign:\n";
    echo "1. Go to Section & Advisers management\n";
    echo "2. Assign an adviser to a strand\n";
    echo "3. Click 'Assign Teacher' button\n";
    echo "4. Assign this teacher to subjects\n";
    echo "5. Click Save\n";
    echo "6. Refresh the teacher profile page\n";
} else {
    echo "✅ Subject Assignments:\n\n";
    foreach ($subjectAssignments as $assignment) {
        $subject = $assignment->subject;
        $strand = $assignment->strand;
        $year = $assignment->academicYear;
        $studentCount = $assignment->subjectEnrollments->count();
        
        echo "- {$subject->name} ({$subject->code})\n";
        echo "  Strand: {$strand->name}\n";
        echo "  Academic Year: {$year->name}\n";
        echo "  Students Enrolled: {$studentCount}\n\n";
    }
}

echo "\n=== Test Complete ===\n";
