<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Strand;
use Illuminate\Console\Command;

class EnrollAllStudents extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'enroll:all-students {--only-active : Only enroll students with status=active}';

    /**
     * The console command description.
     */
    protected $description = 'Enroll all students into the current active academic year (skips already enrolled)';

    public function handle(): int
    {
        $this->info('Starting enrollment of all students...');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->error('No active academic year found. Aborting.');
            return self::FAILURE;
        }

        $this->info('Active academic year: ' . $activeYear->name);

        $query = Student::query();
        if ($this->option('only-active')) {
            $query->where('status', 'active');
        }

        $students = $query->orderBy('student_number')->get();
        $total = $students->count();
        $this->info("Found {$total} students to process.");

        if ($total === 0) {
            $this->info('Nothing to do.');
            return self::SUCCESS;
        }

        // Prepare registration number prefix
        $year = date('Y');
        $prefix = "REG-{$year}-";

        $lastEnrollment = StudentEnrollment::withTrashed()
            ->where('registration_number', 'like', "{$prefix}%")
            ->orderBy('registration_number', 'desc')
            ->first();

        $currentNumber = 0;
        if ($lastEnrollment) {
            $currentNumber = (int) str_replace($prefix, '', $lastEnrollment->registration_number);
            $this->info('Starting registration number from ' . ($currentNumber + 1));
        } else {
            $this->info('No existing registration numbers found, starting from 1');
        }

        $enrolled = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($students as $student) {
            try {
                $exists = StudentEnrollment::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

                if ($exists) {
                    $this->line("[SKIP] {$student->student_number} - already enrolled for {$activeYear->name}");
                    $skipped++;
                    continue;
                }

                // Attempt to resolve strand from student's program
                $strandId = null;
                if (!empty($student->program)) {
                    $strand = Strand::where('code', $student->program)->first();
                    if ($strand) {
                        $strandId = $strand->id;
                    }
                }

                // Try to find a matching AYSS for this strand in active year
                $ayssId = null;
                if ($strandId) {
                    $ayss = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                        ->where('strand_id', $strandId)
                        ->first();
                    if ($ayss) {
                        $ayssId = $ayss->id;
                    }
                }

                $currentNumber++;
                $registrationNumber = $prefix . str_pad($currentNumber, 5, '0', STR_PAD_LEFT);

                $enrollment = StudentEnrollment::create([
                    'student_id' => $student->id,
                    'strand_id' => $strandId,
                    'academic_year_id' => $activeYear->id,
                    'academic_year_strand_section_id' => $ayssId,
                    'registration_number' => $registrationNumber,
                    'status' => 'enrolled',
                ]);

                // Ensure subject enrollments are created
                try {
                    $createdSubjects = $enrollment->syncSubjectEnrollments();
                } catch (\Throwable $e) {
                    // Not fatal for enrollment - log and continue
                    $this->warn("  Could not sync subjects for {$student->student_number}: {$e->getMessage()}");
                }

                $this->line("[OK] {$student->student_number} - Enrolled (Reg: {$registrationNumber})");
                $enrolled++;
            } catch (\Throwable $e) {
                $this->error("[ERR] {$student->student_number} - {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info('');
        $this->info('Enrollment run complete.');
        $this->info("Enrolled: {$enrolled}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        return self::SUCCESS;
    }
}
