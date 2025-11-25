<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreEnrollment;
use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreEnrollmentController extends Controller
{
    /**
     * Display a listing of pre-enrollment submissions
     */
    public function index(Request $request)
    {
        $query = PreEnrollment::with([
            'student',
            'currentAcademicYear',
            'targetAcademicYear',
            'strand',
            'section'
        ])->orderBy('submitted_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Filter by strand
        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->strand_id);
        }

        // Search by student name or number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        $preEnrollments = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => PreEnrollment::count(),
            'pending' => PreEnrollment::where('status', 'pending')->count(),
            'approved' => PreEnrollment::where('status', 'approved')->count(),
            'rejected' => PreEnrollment::where('status', 'rejected')->count(),
            'enrolled' => PreEnrollment::where('status', 'enrolled')->count(),
        ];

        // Get all strands for filter
        $strands = \App\Models\Strand::orderBy('name')->get();

        return view('admin.pre-enrollments.index', compact('preEnrollments', 'stats', 'strands'));
    }

    /**
     * Display the specified pre-enrollment submission
     */
    public function show(PreEnrollment $preEnrollment)
    {
        $preEnrollment->load([
            'student',
            'currentAcademicYear',
            'targetAcademicYear',
            'strand',
            'section',
            'processedBy'
        ]);

        return view('admin.pre-enrollments.show', compact('preEnrollment'));
    }

    /**
     * Approve a pre-enrollment submission and create actual enrollment
     */
    public function approve(PreEnrollment $preEnrollment)
    {
        // Check if this pre-enrollment was already processed
        if (in_array($preEnrollment->status, ['enrolled', 'rejected'])) {
            return back()->with('error', 'This pre-enrollment has already been processed (Status: ' . $preEnrollment->status . ').');
        }

        DB::beginTransaction();
        try {
            // Find or use the current academic year if no target year is set
            $targetAcademicYearId = $preEnrollment->target_academic_year_id ?? $preEnrollment->current_academic_year_id;
            
            // Check if section is selected, otherwise find available section
            $sectionId = $preEnrollment->section_id;
            
            if (!$sectionId) {
                // Find first available section for this strand and grade level
                $availableSection = \App\Models\Section::where('strand_id', $preEnrollment->strand_id)
                    ->where('grade', $preEnrollment->grade_level)
                    ->first();
                
                if ($availableSection) {
                    $sectionId = $availableSection->id;
                } else {
                    throw new \Exception('No section found for strand ' . $preEnrollment->strand->code . ' and grade ' . $preEnrollment->grade_level);
                }
            }
            
            // Find the section assignment for the target year
            $sectionAssignment = \App\Models\AcademicYearStrandSection::where('academic_year_id', $targetAcademicYearId)
                ->where('strand_id', $preEnrollment->strand_id)
                ->where('section_id', $sectionId)
                ->first();

            if (!$sectionAssignment) {
                throw new \Exception('Section assignment not found for the academic year. Please ensure the section is assigned to this strand in the Academic Year management.');
            }

            // Check if student already has enrollment for this academic year
            $existingEnrollment = StudentEnrollment::where('student_id', $preEnrollment->student_id)
                ->where('academic_year_id', $targetAcademicYearId)
                ->first();

            if ($existingEnrollment) {
                // If an enrollment already exists, ensure it's linked to the correct
                // academic_year_strand_section so the student appears in Section & Advisers
                $updateData = [];
                if (empty($existingEnrollment->academic_year_strand_section_id) || $existingEnrollment->academic_year_strand_section_id != $sectionAssignment->id) {
                    $updateData['academic_year_strand_section_id'] = $sectionAssignment->id;
                }
                if (empty($existingEnrollment->strand_id) || $existingEnrollment->strand_id != $preEnrollment->strand_id) {
                    $updateData['strand_id'] = $preEnrollment->strand_id;
                }

                if (!empty($updateData)) {
                    $existingEnrollment->update($updateData);
                }

                // Ensure subject enrollments are synced for the (possibly updated) enrollment
                try {
                    $existingEnrollment->syncSubjectEnrollments();
                } catch (\Throwable $e) {
                    // Don't block the flow if sync fails; log silently
                }

                // Mark this pre-enrollment as enrolled and link to existing enrollment
                $preEnrollment->update([
                    'status' => 'enrolled',
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                    'remarks' => 'Student already enrolled - linked to existing enrollment ID: ' . $existingEnrollment->id,
                    'section_id' => $sectionId, // ensure pre-enrollment records the section used
                ]);

                DB::commit();
                return back()->with('success', 'Student already has an enrollment for this academic year. Existing enrollment updated and pre-enrollment marked as enrolled.');
            }

            // Generate registration number
            $year = date('Y');
            $prefix = "REG-{$year}-";
            
            $maxReg = DB::table('student_enrollments')
                ->where('registration_number', 'like', "{$prefix}%")
                ->orderByRaw('CAST(SUBSTRING(registration_number, LENGTH(?)+1) AS UNSIGNED) DESC', [$prefix])
                ->value('registration_number');
            
            if ($maxReg) {
                $lastNumber = (int) str_replace($prefix, '', $maxReg);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $registrationNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
            // Create the actual enrollment
            $enrollment = StudentEnrollment::create([
                'student_id' => $preEnrollment->student_id,
                'academic_year_id' => $targetAcademicYearId,
                'strand_id' => $preEnrollment->strand_id,
                'academic_year_strand_section_id' => $sectionAssignment->id,
                'registration_number' => $registrationNumber,
                'status' => 'enrolled',
            ]);

            // Sync subject enrollments
            $enrollment->syncSubjectEnrollments();

            // Update pre-enrollment status to enrolled
            $preEnrollment->update([
                'status' => 'enrolled',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'section_id' => $sectionId, // Update with actual section used
            ]);

            DB::commit();

            return back()->with('success', 'Pre-enrollment approved and enrollment created successfully. Student has been added to ' . $sectionAssignment->section->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve pre-enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pre-enrollment submission
     */
    public function reject(Request $request, PreEnrollment $preEnrollment)
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $preEnrollment->update([
                'status' => 'rejected',
                'remarks' => $request->remarks,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Pre-enrollment rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject pre-enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Process approved pre-enrollments and create actual enrollments
     */
    public function process(PreEnrollment $preEnrollment)
    {
        if ($preEnrollment->status !== 'approved') {
            return back()->with('error', 'Only approved pre-enrollments can be processed.');
        }

        DB::beginTransaction();
        try {
            // Find the section assignment for the target year
            $sectionAssignment = \App\Models\AcademicYearStrandSection::where('academic_year_id', $preEnrollment->target_academic_year_id)
                ->where('strand_id', $preEnrollment->strand_id)
                ->where('section_id', $preEnrollment->section_id)
                ->first();

            if (!$sectionAssignment) {
                throw new \Exception('Section assignment not found for the target academic year.');
            }

            // Create the actual enrollment
            $enrollment = StudentEnrollment::create([
                'student_id' => $preEnrollment->student_id,
                'academic_year_id' => $preEnrollment->target_academic_year_id,
                'academic_year_strand_section_id' => $sectionAssignment->id,
                'enrollment_date' => now(),
                'status' => 'enrolled',
            ]);

            // Update pre-enrollment status
            $preEnrollment->update([
                'status' => 'enrolled',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Pre-enrollment processed successfully. Student enrollment created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process pre-enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve pre-enrollments and create enrollments
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pre_enrollments,id',
        ]);

        DB::beginTransaction();
        try {
            $preEnrollments = PreEnrollment::with(['strand', 'section'])
                ->whereIn('id', $request->ids)
                ->where('status', 'pending')
                ->get();

            $successCount = 0;
            $errors = [];

            foreach ($preEnrollments as $preEnrollment) {
                try {
                    // Find or use the current academic year if no target year is set
                    $targetAcademicYearId = $preEnrollment->target_academic_year_id ?? $preEnrollment->current_academic_year_id;
                    
                    // Check if section is selected, otherwise find available section
                    $sectionId = $preEnrollment->section_id;
                    
                    if (!$sectionId) {
                        // Find first available section for this strand and grade level
                        $availableSection = \App\Models\Section::where('strand_id', $preEnrollment->strand_id)
                            ->where('grade', $preEnrollment->grade_level)
                            ->first();
                        
                        if ($availableSection) {
                            $sectionId = $availableSection->id;
                        } else {
                            throw new \Exception('No section found');
                        }
                    }
                    
                    // Find the section assignment for the target year
                    $sectionAssignment = \App\Models\AcademicYearStrandSection::where('academic_year_id', $targetAcademicYearId)
                        ->where('strand_id', $preEnrollment->strand_id)
                        ->where('section_id', $sectionId)
                        ->first();

                    if (!$sectionAssignment) {
                        throw new \Exception('Section assignment not found');
                    }

                    // Check if student already has enrollment for this academic year
                    $existingEnrollment = StudentEnrollment::where('student_id', $preEnrollment->student_id)
                        ->where('academic_year_id', $targetAcademicYearId)
                        ->first();

                    if ($existingEnrollment) {
                        // Link existing enrollment to the correct academic_year_strand_section
                        $updateData = [];
                        if (empty($existingEnrollment->academic_year_strand_section_id) || $existingEnrollment->academic_year_strand_section_id != $sectionAssignment->id) {
                            $updateData['academic_year_strand_section_id'] = $sectionAssignment->id;
                        }
                        if (empty($existingEnrollment->strand_id) || $existingEnrollment->strand_id != $preEnrollment->strand_id) {
                            $updateData['strand_id'] = $preEnrollment->strand_id;
                        }

                        if (!empty($updateData)) {
                            $existingEnrollment->update($updateData);
                        }

                        try { $existingEnrollment->syncSubjectEnrollments(); } catch (\Throwable $e) { }

                        // Update pre-enrollment and count as success
                        $preEnrollment->update([
                            'status' => 'enrolled',
                            'processed_at' => now(),
                            'processed_by' => auth()->id(),
                            'section_id' => $sectionId,
                        ]);

                        $successCount++;
                        continue;
                    }

                    // Generate registration number
                    $year = date('Y');
                    $prefix = "REG-{$year}-";
                    
                    $maxReg = DB::table('student_enrollments')
                        ->where('registration_number', 'like', "{$prefix}%")
                        ->orderByRaw('CAST(SUBSTRING(registration_number, LENGTH(?)+1) AS UNSIGNED) DESC', [$prefix])
                        ->value('registration_number');
                    
                    if ($maxReg) {
                        $lastNumber = (int) str_replace($prefix, '', $maxReg);
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1;
                    }
                    
                    $registrationNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
                    
                    // Create the actual enrollment
                    $enrollment = StudentEnrollment::create([
                        'student_id' => $preEnrollment->student_id,
                        'academic_year_id' => $targetAcademicYearId,
                        'strand_id' => $preEnrollment->strand_id,
                        'academic_year_strand_section_id' => $sectionAssignment->id,
                        'registration_number' => $registrationNumber,
                        'status' => 'enrolled',
                    ]);

                    // Sync subject enrollments
                    $enrollment->syncSubjectEnrollments();

                    // Update pre-enrollment status to enrolled
                    $preEnrollment->update([
                        'status' => 'enrolled',
                        'processed_at' => now(),
                        'processed_by' => auth()->id(),
                        'section_id' => $sectionId,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $studentName = $preEnrollment->student->first_name . ' ' . $preEnrollment->student->last_name;
                    $errors[] = "{$studentName}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "Successfully approved and enrolled {$successCount} student(s).";
            if (count($errors) > 0) {
                $message .= " Failed: " . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve pre-enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Delete a pre-enrollment submission
     */
    public function destroy(PreEnrollment $preEnrollment)
    {
        DB::beginTransaction();
        try {
            $studentName = $preEnrollment->student->first_name . ' ' . $preEnrollment->student->last_name;
            
            $preEnrollment->delete();

            DB::commit();

            return back()->with('success', "Pre-enrollment for {$studentName} has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete pre-enrollment: ' . $e->getMessage());
        }
    }
}
