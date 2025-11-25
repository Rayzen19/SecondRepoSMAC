<?php

namespace App\Observers;

use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\StudentEnrollment;
use App\Models\Strand;
use App\Models\Student;

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

        // Try to resolve strand from student's program
        $strandId = null;
        if (!empty($student->program)) {
            $strand = Strand::where('code', $student->program)->first();
            if ($strand) {
                $strandId = $strand->id;
            }
        }

        // Try to find an AYSS for this strand
        $ayssId = null;
        if ($strandId) {
            $ayss = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                ->where('strand_id', $strandId)
                ->first();
            if ($ayss) {
                $ayssId = $ayss->id;
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

        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'strand_id' => $strandId,
            'academic_year_id' => $activeYear->id,
            'academic_year_strand_section_id' => $ayssId,
            'registration_number' => $registrationNumber,
            'status' => 'enrolled',
        ]);

        // Sync subject enrollments if available
        try {
            $enrollment->syncSubjectEnrollments();
        } catch (\Throwable $e) {
            // noop - not critical
        }
    }
}
