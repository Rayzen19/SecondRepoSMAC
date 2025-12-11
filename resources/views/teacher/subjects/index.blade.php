@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Classes</h4>
        <div class="text-muted small">Academic Year: {{ $activeYear?->display_name ?? 'No active year' }}</div>
    </div>
</div>

@php
    $user = Auth::guard('teacher')->user();
    $teacher = \App\Models\Teacher::with([
        'advisedSections' => function ($query) use ($activeYear) {
            $query->where('academic_year_id', $activeYear?->id)
                ->with(['academicYear', 'strand', 'section']);
        },
        'teachingAssignments' => function ($query) use ($activeYear) {
            $query->where('academic_year_id', $activeYear?->id)
                ->with([
                    'academicYear',
                    'strand',
                    'subject',
                    'sectionAssignment.section',
                    'sectionAssignment.academicYear',
                    'sectionAssignment.strand',
                    'subjectEnrollments.studentEnrollment.academicYearStrandSection.section'
                ]);
        }
    ])->findOrFail($user->user_pk_id);

    // Build a unified list of all sections handled (as adviser or subject teacher)
    $advisedSections = $teacher->advisedSections;
    $teachingAssignments = $teacher->teachingAssignments;

    $sectionsHandled = collect();

    // From advised sections
    foreach ($advisedSections as $advisedSection) {
        if ($advisedSection->section) {
            $key = 'section_' . $advisedSection->id; // AYS section id for uniqueness per AY+Strand+Section
            $sectionsHandled->put($key, [
                'section_assignment_id' => $advisedSection->id,
                'grade' => $advisedSection->section->grade,
                'section_name' => $advisedSection->section->name,
                'strand' => $advisedSection->strand->name ?? 'N/A',
                'strand_code' => $advisedSection->strand->code ?? '',
                'is_adviser' => true,
                'subjects' => collect(),
                'teaching_assignment_ids' => collect(),
                'student_count' => 0,
                'academic_year' => $advisedSection->academicYear->name ?? 'N/A',
                'sort_key' => $advisedSection->section->grade . '-' . $advisedSection->section->name
            ]);
        }
    }

    // From teaching assignments - prefer explicit section assignment; else derive
    foreach ($teachingAssignments as $assignment) {
        if ($assignment->strand && $assignment->subject) {
            // Prefer explicit section chosen during subject-teacher assignment
            $enrolledSections = collect();
            if ($assignment->sectionAssignment) {
                $enrolledSections = collect([$assignment->sectionAssignment]);
            } else {
                // Derive unique sections from enrolled students
                $enrolledSections = $assignment->subjectEnrollments
                    ->pluck('studentEnrollment.academicYearStrandSection')
                    ->filter()
                    ->unique('id');

                // Fallback: include ALL sections for this AY+Strand if still empty
                if ($enrolledSections->isEmpty()) {
                    $fallbackSections = \App\Models\AcademicYearStrandSection::with(['section','strand','academicYear'])
                        ->where('academic_year_id', $assignment->academic_year_id)
                        ->where('strand_id', $assignment->strand_id)
                        ->get();
                    if ($fallbackSections->isNotEmpty()) {
                        $enrolledSections = $fallbackSections;
                    }
                }
            }

            foreach ($enrolledSections as $sectionAssignment) {
                if ($sectionAssignment && $sectionAssignment->section) {
                    $key = 'section_' . $sectionAssignment->id; // AYS section id

                    if (!$sectionsHandled->has($key)) {
                        $sectionsHandled->put($key, [
                            'section_assignment_id' => $sectionAssignment->id,
                            'grade' => $sectionAssignment->section->grade,
                            'section_name' => $sectionAssignment->section->name,
                            'strand' => $sectionAssignment->strand->name ?? 'N/A',
                            'strand_code' => $sectionAssignment->strand->code ?? '',
                            'is_adviser' => (int)($sectionAssignment->adviser_teacher_id ?? 0) === (int)($teacher->id ?? 0),
                            'subjects' => collect(),
                            'teaching_assignment_ids' => collect(),
                            'student_count' => 0,
                            'academic_year' => ($assignment->academicYear->name ?? ($sectionAssignment->academicYear->name ?? 'N/A')),
                            'sort_key' => $sectionAssignment->section->grade . '-' . $sectionAssignment->section->name
                        ]);
                    }

                    // Add subject and assignment ID to this section
                    $sectionsHandled->get($key)['subjects']->push($assignment->subject->name);
                    $sectionsHandled->get($key)['teaching_assignment_ids']->push($assignment->id);

                    // Count students in this section for this subject
                    $studentCount = $assignment->subjectEnrollments
                        ->where('studentEnrollment.academic_year_strand_section_id', $sectionAssignment->id)
                        ->count();
                    $sectionsHandled->get($key)['student_count'] += $studentCount;
                }
            }
        }
    }

    // Sort by grade and section name
    $sectionsHandled = $sectionsHandled->sortBy('sort_key')->values();
@endphp

<!-- Subject Assignments Section -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            <i class="ti ti-chalkboard me-2"></i>Subject Assignments
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.class-records.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="ti ti-list-details me-1"></i>Class Records
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($sectionsHandled->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No subject assignments yet. Once an admin assigns you to a subject, it will appear here.
            </div>
        @else
            <div class="row g-3">
                @foreach($sectionsHandled as $section)
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 position-relative section-card" style="background-color: #f8f9fa;">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <span class="badge bg-warning text-dark me-2">Grade {{ $section['grade'] }}</span>
                                        <strong>Section {{ $section['section_name'] }}</strong>
                                    </h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="ti ti-books me-1"></i>
                                        {{ $section['strand'] }}
                                        @if($section['strand_code'])
                                            <span class="badge bg-secondary ms-1">{{ $section['strand_code'] }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($section['is_adviser'])
                                        <span class="badge bg-success">
                                            <i class="ti ti-star me-1"></i>Adviser
                                        </span>
                                    @endif
                                    <a href="{{ route('teacher.class-records.index') }}" class="btn btn-sm btn-primary" title="View class records">
                                        <i class="ti ti-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>

                            @if($section['subjects']->isNotEmpty())
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted d-block mb-2"><strong>Subjects Teaching:</strong></small>
                                    <div class="subjects-list" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($section['subjects']->unique() as $subject)
                                            <div class="mb-1">
                                                <span class="badge bg-info text-dark w-100 text-start" style="white-space: normal; padding: 0.5rem;">
                                                    <i class="ti ti-book me-1"></i>{{ $subject }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($section['student_count'] > 0)
                                <div class="mt-2">
                                    <small class="text-primary">
                                        <i class="ti ti-users me-1"></i>{{ $section['student_count'] }} student{{ $section['student_count'] > 1 ? 's' : '' }}
                                    </small>
                                </div>
                            @endif

                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-calendar me-1"></i>{{ $section['academic_year'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="mt-3 pt-3 border-top">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-2">
                            <h4 class="mb-0 text-warning">{{ $sectionsHandled->count() }}</h4>
                            <small class="text-muted">Total Sections</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2">
                            <h4 class="mb-0 text-success">{{ $sectionsHandled->where('is_adviser', true)->count() }}</h4>
                            <small class="text-muted">As Adviser</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2">
                            <h4 class="mb-0 text-primary">{{ $sectionsHandled->sum('student_count') }}</h4>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.section-card {
    position: relative;
    transition: all 0.3s ease !important;
}

.section-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    background-color: #e9ecef !important;
    border-color: #0d6efd !important;
}

.section-card:hover .text-primary {
    font-weight: 600;
}
</style>

@endsection
