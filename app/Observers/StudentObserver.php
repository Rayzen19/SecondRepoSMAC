<?php

namespace App\Observers;

use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\StudentEnrollment;
use App\Models\Strand;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        // Auto-enroll newly created students into the active academic year.
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return;
        }

        // Skip if already enrolled for this academic year
        $existing = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->first();

        if ($existing) {
            return;
        }

        // Try to resolve strand from student's program (no section assignment)
        $strandId = null;
        if (!empty($student->program)) {
            $strand = Strand::where('code', $student->program)->first();
            if ($strand) {
                $strandId = $strand->id;
            }
        }

        // Generate a registration number
        $year = date('Y');
        $prefix = "REG-{$year}-";

        $lastEnrollment = StudentEnrollment::withTrashed()
            ->where('registration_number', 'like', "{$prefix}%")
            ->orderBy('registration_number', 'desc')
            ->first();

        $currentNumber = 0;
        if ($lastEnrollment) {
            $currentNumber = (int) str_replace($prefix, '', $lastEnrollment->registration_number);
        }
        $currentNumber++;
        $registrationNumber = $prefix . str_pad($currentNumber, 5, '0', STR_PAD_LEFT);

        // Auto-create enrollment WITH section assignment
        // Strategy: pick the first available AcademicYearStrandSection for the student's strand in the active year.
        // If strand is unknown, fall back to any section in the active year.
        $sectionAssignment = null;
        $sectionQuery = AcademicYearStrandSection::with(['section'])
            ->where('academic_year_id', $activeYear->id);
        if ($strandId) {
            $sectionQuery->where('strand_id', $strandId);
        }
        $sectionAssignment = $sectionQuery->orderBy('section_id')->first();

        // Create enrollment; if no section found, we still create without section to avoid blocking
        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'strand_id' => $strandId,
            'academic_year_id' => $activeYear->id,
            'academic_year_strand_section_id' => optional($sectionAssignment)->id,
            'registration_number' => $registrationNumber,
            'status' => 'enrolled',
        ]);

        Log::info('Auto-enrolled student', [
            'student_id' => $student->id,
            'academic_year_id' => $activeYear->id,
            'strand_id' => $strandId,
            'section_assignment_id' => optional($sectionAssignment)->id,
        ]);
    }
}
