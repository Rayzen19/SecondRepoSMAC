@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Classes</h4>
        <div class="text-muted small">Academic Year: {{ $activeYear?->display_name ?? 'No active year' }}</div>
    </div>
</div>

@if($subjects->isEmpty())
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <h5 class="mb-2">No subjects assigned yet</h5>
            <p class="text-muted mb-0">When an administrator assigns you to subjects for the active academic year, they will appear here.</p>
        </div>
    </div>
@else
    <!-- Subject Assignments Section -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">
                <i class="ti ti-clipboard-text me-2"></i>Subject Assignments
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.class-records.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-list-details me-1"></i>Class Records
                </a>
            </div>
        </div>

        @php
            $user = Auth::guard('teacher')->user();
            // Group assignments by section
            $groupedBySections = collect();
            $adviserSections = collect();
            
            if ($activeYear && $user) {
                // Get sections where teacher is adviser
                $adviserSections = \App\Models\AcademicYearStrandSection::with(['section', 'strand', 'adviserTeacher'])
                    ->where('academic_year_id', $activeYear->id)
                    ->where('adviser_teacher_id', $user->user_pk_id)
                    ->get();
                
                // Get all teaching assignments
                $teachingAssignments = \App\Models\AcademicYearStrandSubject::with(['subject', 'strand'])
                    ->where('academic_year_id', $activeYear->id)
                    ->where('teacher_id', $user->user_pk_id)
                    ->get();
                
                // Group by strand
                $groupedByStrand = $teachingAssignments->groupBy('strand_id');
                
                foreach ($groupedByStrand as $strandId => $assignments) {
                    $strand = $assignments->first()->strand;
                    
                    // Find section for this strand (prefer adviser section)
                    $sectionAssignment = \App\Models\AcademicYearStrandSection::with('section')
                        ->where('academic_year_id', $activeYear->id)
                        ->where('strand_id', $strandId)
                        ->where(function($q) use ($user) {
                            $q->where('adviser_teacher_id', $user->user_pk_id)
                              ->orWhere('is_active', true);
                        })
                        ->orderByRaw('CASE WHEN adviser_teacher_id = ? THEN 0 ELSE 1 END', [$user->user_pk_id])
                        ->first();
                    
                    $groupedBySections->push([
                        'section' => $sectionAssignment,
                        'strand' => $strand,
                        'assignments' => $assignments,
                        'is_adviser' => $sectionAssignment && $sectionAssignment->adviser_teacher_id == $user->user_pk_id,
                    ]);
                }
            }
            
            $totalSections = $groupedBySections->count();
            $totalAsAdviser = $adviserSections->count();
            $totalStudents = $subjects->sum('counts.total');
        @endphp

        <div class="row g-3 mb-4">
            @forelse($groupedBySections as $group)
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-warning text-dark mb-2">Grade {{ $group['section']->section->grade_level ?? 'N/A' }}</span>
                                    <h6 class="mb-1">Section {{ $group['section']->section->name ?? 'N/A' }}</h6>
                                </div>
                                @if($group['is_adviser'])
                                    <span class="badge bg-success">
                                        <i class="ti ti-star me-1"></i>Adviser
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small mb-1">Strand</div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-book-2"></i>
                                    <span class="badge bg-info-subtle text-info">{{ $group['strand']->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small mb-1">Subjects Teaching</div>
                                @foreach($group['assignments'] as $assignment)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="ti ti-point-filled text-primary" style="font-size: 0.75rem;"></i>
                                        <span class="small">{{ $assignment->subject->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="ti ti-calendar me-1"></i>
                                        {{ $activeYear->name }}
                                    </div>
                                    <a href="{{ route('teacher.class-records.index') }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <i class="ti ti-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">No subject assignments found</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Summary Statistics -->
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-warning mb-2">{{ $totalSections }}</h2>
                        <p class="text-muted mb-0 small">Total Sections</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-success mb-2">{{ $totalAsAdviser }}</h2>
                        <p class="text-muted mb-0 small">As Adviser</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-primary mb-2">{{ $totalStudents }}</h2>
                        <p class="text-muted mb-0 small">Total Students</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
