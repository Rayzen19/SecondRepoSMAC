<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function section($sectionAssignmentId)
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;
        
        // Get the section assignment
        $sectionAssignment = AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
            ->where('id', $sectionAssignmentId)
            ->where('adviser_teacher_id', $teacherId)
            ->firstOrFail();
        
        // Get all students enrolled in this section
        $students = StudentEnrollment::with(['student'])
            ->where('academic_year_strand_section_id', $sectionAssignment->id)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'student' => $enrollment->student,
                    'registration_number' => $enrollment->registration_number,
                    'status' => $enrollment->status,
                ];
            })
            ->filter(fn($row) => !is_null($row['student']))
            ->values();
        
        $total = $students->count();
        $male = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'male')->count();
        $female = $students->filter(fn($row) => strtolower($row['student']->gender ?? '') === 'female')->count();
        
        return view('teacher.students.section', [
            'sectionAssignment' => $sectionAssignment,
            'students' => $students,
            'total' => $total,
            'male' => $male,
            'female' => $female,
        ]);
    }
    
    public function allSections()
    {
        $user = auth('teacher')->user();
        $teacherId = $user->user_pk_id;
        
        // Get current active academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$currentAcademicYear) {
            return view('teacher.students.all_sections', [
                'groupedSections' => collect(),
                'currentAcademicYear' => null,
            ]);
        }
        
        // Get all section assignments for the current academic year
        $assignments = AcademicYearStrandSection::with(['strand', 'section', 'adviserTeacher'])
            ->where('academic_year_id', $currentAcademicYear->id)
            ->get();
        
        // Group sections by strand and then by grade
        $groupedSections = $assignments->map(function ($assignment) use ($teacherId) {
            $studentsCount = StudentEnrollment::where('academic_year_strand_section_id', $assignment->id)
                ->count();
            
            return [
                'assignment_id' => $assignment->id,
                'section_name' => $assignment->section->name ?? 'N/A',
                'section_grade' => $assignment->section->grade ?? 'N/A',
                'strand_id' => $assignment->strand->id ?? 0,
                'strand_name' => $assignment->strand->name ?? 'N/A',
                'strand_code' => $assignment->strand->code ?? 'N/A',
                'adviser_name' => $assignment->adviserTeacher ? 
                    $assignment->adviserTeacher->last_name . ', ' . $assignment->adviserTeacher->first_name : 
                    'No Adviser',
                'is_my_section' => $assignment->adviser_teacher_id === $teacherId,
                'students_count' => $studentsCount,
            ];
        })
        ->groupBy('strand_code') // Group by strand
        ->map(function ($strandSections) {
            // Within each strand, group by grade
            return [
                'strand_name' => $strandSections->first()['strand_name'],
                'strand_code' => $strandSections->first()['strand_code'],
                'grades' => $strandSections->groupBy('section_grade')
                    ->map(function ($gradeSections) {
                        return $gradeSections->sortBy('section_name')->values();
                    })
            ];
        });
        
        return view('teacher.students.all_sections', [
            'groupedSections' => $groupedSections,
            'currentAcademicYear' => $currentAcademicYear,
        ]);
    }
}
