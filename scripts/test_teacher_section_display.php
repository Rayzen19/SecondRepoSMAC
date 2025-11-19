<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\AcademicYearStrandSubject;

echo "=== Testing Teacher Profile - Subject & Section Display ===\n\n";

// Get a teacher with assignments
$assignmentExists = AcademicYearStrandSubject::whereNotNull('teacher_id')->first();
if (!$assignmentExists) {
    echo "❌ No subject assignments found in database\n";
    echo "Please assign a teacher to subjects via Section & Advisers interface first.\n";
    exit(1);
}

$teacher = Teacher::find($assignmentExists->teacher_id);
if (!$teacher) {
    echo "❌ Teacher not found\n";
    exit(1);
}

echo "Teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n";
echo "Employee #: {$teacher->employee_number}\n\n";

// Query exactly as updated controller does
$subjectAssignments = AcademicYearStrandSubject::with([
        'academicYear',
        'strand',
        'subject',
        'sectionAssignment.section',
        'subjectEnrollments.studentEnrollment.student',
        'subjectEnrollments.studentEnrollment.academicYearStrandSection.section',
    ])
    ->where('teacher_id', $teacher->id)
    ->get();

echo "Total Subject Assignments: " . $subjectAssignments->count() . "\n\n";

if ($subjectAssignments->isEmpty()) {
    echo "⚠️  No subject assignments found for this teacher.\n";
} else {
    echo "✅ Subject Assignments (as they will appear on profile):\n\n";
    
    foreach ($subjectAssignments as $asmt) {
        $subject = $asmt->subject;
        $strand = $asmt->strand;
        $year = $asmt->academicYear;
        
        // Get section from direct assignment or from student enrollments
        $sectionName = data_get($asmt, 'sectionAssignment.section.name');
        if (!$sectionName) {
            $sectionName = data_get($asmt, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.name');
        }
        
        $studentCount = $asmt->subjectEnrollments->filter(fn($se) => data_get($se, 'studentEnrollment.student'))->count();
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📚 {$subject->name}";
        if ($subject->code) {
            echo " ({$subject->code})";
        }
        echo "\n";
        echo "   Strand: {$strand->name}";
        if ($sectionName) {
            echo " • Section {$sectionName}";
        }
        echo "\n";
        echo "   Academic Year: {$year->name}\n";
        echo "   Students Enrolled: {$studentCount}\n";
        
        // Show section assignment details
        if ($asmt->academic_year_strand_section_id) {
            echo "   ✓ Assigned to specific section (ID: {$asmt->academic_year_strand_section_id})\n";
        } else {
            echo "   ℹ General assignment (no specific section)\n";
        }
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}

echo "\n=== Display Preview ===\n\n";
echo "The teacher profile will show:\n\n";
foreach ($subjectAssignments as $asmt) {
    $sectionName = data_get($asmt, 'sectionAssignment.section.name');
    if (!$sectionName) {
        $sectionName = data_get($asmt, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.name');
    }
    
    echo "┌─────────────────────────────────────────────┐\n";
    echo "│ " . $asmt->subject->name;
    if ($asmt->subject->code) {
        echo " ({$asmt->subject->code})";
    }
    echo str_repeat(" ", max(0, 43 - strlen($asmt->subject->name . ($asmt->subject->code ? " ({$asmt->subject->code})" : "")))) . "│\n";
    
    $info = $asmt->strand->name;
    if ($sectionName) {
        $info .= " • Section {$sectionName}";
    }
    echo "│ " . $info . str_repeat(" ", max(0, 43 - strlen($info))) . "│\n";
    echo "└─────────────────────────────────────────────┘\n\n";
}

echo "\n=== Test Complete ===\n";
echo "\n✅ Changes Applied:\n";
echo "1. Controller now loads 'sectionAssignment.section' relationship\n";
echo "2. View displays subject name prominently\n";
echo "3. Section name appears in subtitle with strand\n";
echo "4. Fallback to student enrollments if no direct section assignment\n";
echo "\n🔄 Next Step: Refresh the teacher profile page to see the changes!\n";
