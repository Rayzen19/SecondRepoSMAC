<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Student Assignment Status ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: " . ($activeYear ? $activeYear->name : 'None') . "\n\n";

$totalStudents = \App\Models\Student::where('status', 'active')->count();
echo "Total Active Students: $totalStudents\n";

$enrolledStudents = \App\Models\StudentEnrollment::where('academic_year_id', $activeYear->id)->count();
echo "Students Enrolled for {$activeYear->name}: $enrolledStudents\n";

$notEnrolled = $totalStudents - $enrolledStudents;
echo "Students NOT Yet Enrolled: $notEnrolled\n\n";

if ($notEnrolled > 0) {
    echo "⚠️  You have $notEnrolled active students that need to be assigned to sections!\n\n";
    
    echo "Sample students needing assignment:\n";
    $unenrolled = \App\Models\Student::where('status', 'active')
        ->whereNotIn('id', function($query) use ($activeYear) {
            $query->select('student_id')
                ->from('student_enrollments')
                ->where('academic_year_id', $activeYear->id);
        })
        ->take(10)
        ->get();
    
    foreach ($unenrolled as $student) {
        echo "  - {$student->student_number}: {$student->first_name} {$student->last_name} ({$student->program}, Grade {$student->grade_level})\n";
    }
    
    echo "\n";
    echo "📝 TO FIX THIS:\n";
    echo "1. Go to the 'Assigning List' page\n";
    echo "2. Filter by strand and grade level\n";
    echo "3. Check the students you want to assign\n";
    echo "4. Click the section button to assign them\n";
    echo "5. Click 'Save Assignments' button\n";
} else {
    echo "✅ All active students are enrolled for the current academic year!\n";
}

echo "\n=== Section Student Counts ===\n";
$sections = \App\Models\Section::with('strand')->whereNotNull('strand_id')->get();

foreach ($sections->take(10) as $section) {
    $ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $section->strand_id)
        ->where('section_id', $section->id)
        ->first();
    
    $count = 0;
    if ($ayss) {
        $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
    }
    
    echo sprintf("%-20s %-10s %-10s %d students\n", 
        $section->name, 
        $section->grade, 
        $section->strand->code,
        $count
    );
}

echo "\nDone!\n";
