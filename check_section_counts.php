<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Section Student Counts ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: " . ($activeYear ? $activeYear->name : 'None') . "\n\n";

if (!$activeYear) {
    echo "ERROR: No active academic year found!\n";
    exit(1);
}

// Get all sections
$sections = \App\Models\Section::with('strand')->orderBy('grade')->orderBy('name')->get();

echo "Total sections: " . $sections->count() . "\n\n";
echo "Section Details:\n";
echo str_repeat("-", 100) . "\n";
printf("%-20s %-10s %-15s %-15s %-15s\n", "Section Name", "Grade", "Strand Code", "Strand ID", "Student Count");
echo str_repeat("-", 100) . "\n";

foreach ($sections as $section) {
    $strandCode = $section->strand ? $section->strand->code : 'NULL';
    $strandId = $section->strand_id ?? 'NULL';
    
    if (!$section->strand) {
        printf("%-20s %-10s %-15s %-15s %-15s\n", 
            $section->name, 
            $section->grade, 
            'NO STRAND',
            $strandId,
            'N/A'
        );
        continue;
    }
    
    // Find academic_year_strand_section record
    $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $section->strand->id)
        ->where('section_id', $section->id)
        ->first();
    
    if (!$academicYearStrandSection) {
        // Try fallback
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('strand_id', $section->strand->id)
            ->where('section_id', $section->id)
            ->orderByDesc('id')
            ->first();
    }
    
    $studentCount = 0;
    if ($academicYearStrandSection) {
        $studentCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $academicYearStrandSection->id)
            ->count();
    }
    
    printf("%-20s %-10s %-15s %-15s %-15s\n", 
        $section->name, 
        $section->grade, 
        $strandCode,
        $strandId,
        $studentCount
    );
}

echo str_repeat("-", 100) . "\n";

echo "\n=== Sample Academic Year Strand Section Records ===\n";
$ayss = \App\Models\AcademicYearStrandSection::with(['section', 'strand'])
    ->where('academic_year_id', $activeYear->id)
    ->take(5)
    ->get();

foreach ($ayss as $record) {
    echo sprintf("AYSS ID: %d | Section: %s | Strand: %s | AY: %s\n",
        $record->id,
        $record->section ? $record->section->name : 'NULL',
        $record->strand ? $record->strand->code : 'NULL',
        $record->academicYear ? $record->academicYear->name : 'NULL'
    );
    
    $enrollmentCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $record->id)->count();
    echo "  -> Enrollments: $enrollmentCount\n";
}

echo "\n=== Check specific ABM G-11 sections ===\n";
$abmStrand = \App\Models\Strand::where('code', 'ABM')->first();
if ($abmStrand) {
    $abmG11Sections = \App\Models\Section::where('strand_id', $abmStrand->id)
        ->where('grade', 'G-11')
        ->get();
    
    foreach ($abmG11Sections as $section) {
        echo "\nSection: {$section->name} ({$section->grade})\n";
        
        $ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $abmStrand->id)
            ->where('section_id', $section->id)
            ->first();
        
        if ($ayss) {
            $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
            echo "  -> AYSS ID: {$ayss->id}, Students: $count\n";
            
            // Show first 3 students
            $students = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
                ->with('student')
                ->take(3)
                ->get();
            
            foreach ($students as $enrollment) {
                if ($enrollment->student) {
                    echo "     - {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
                }
            }
        } else {
            echo "  -> No AYSS record found\n";
        }
    }
}

echo "\nDone!\n";
