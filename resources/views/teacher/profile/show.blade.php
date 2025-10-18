@extends('teacher.components.template')

@section('content')
<div class="container-fluid">
    <!-- Success Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Profile Header -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">My Profile</h4>
                            <p class="text-muted mb-0">View and manage your personal information</p>
                        </div>
                        <a href="{{ route('teacher.profile.edit') }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Profile Picture Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Profile Picture</h5>
                    @if($teacher->profile_picture)
                        <img src="{{ asset('storage/' . $teacher->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3" 
                             style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 200px; height: 200px;">
                            <i class="ti ti-user" style="font-size: 100px; color: white;"></i>
                        </div>
                    @endif
                    
                    <form action="{{ route('teacher.profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        @method('POST')
                        <div class="mb-2">
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" required>
                            @error('profile_picture')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="ti ti-upload me-1"></i>Upload New Picture
                        </button>
                    </form>

                    @if($teacher->profile_picture)
                        <form action="{{ route('teacher.profile.picture.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="ti ti-trash me-1"></i>Remove Picture
                            </button>
                        </form>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted">Accepted: JPG, JPEG, PNG, GIF (Max: 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Security</h5>
                    <a href="{{ route('teacher.profile.password.edit') }}" class="btn btn-outline-primary w-100">
                        <i class="ti ti-lock me-1"></i>Change Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Employee Number</label>
                            <p class="mb-0">{{ $teacher->employee_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($teacher->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">First Name</label>
                            <p class="mb-0">{{ $teacher->first_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Middle Name</label>
                            <p class="mb-0">{{ $teacher->middle_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Last Name</label>
                            <p class="mb-0">{{ $teacher->last_name }}{{ $teacher->suffix ? ' ' . $teacher->suffix : '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Gender</label>
                            <p class="mb-0">{{ ucfirst($teacher->gender) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Email</label>
                            <p class="mb-0">{{ $teacher->email }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Phone</label>
                            <p class="mb-0">{{ $teacher->phone }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small">Address</label>
                            <p class="mb-0">{{ $teacher->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Professional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Department</label>
                            <p class="mb-0">{{ $teacher->department }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Term</label>
                            <p class="mb-0">{{ $teacher->term }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Specialization</label>
                            <p class="mb-0">{{ $teacher->specialization ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Handled -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-school me-2"></i>Sections Handled
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $advisedSections = $teacher->advisedSections;
                        $teachingAssignments = $teacher->teachingAssignments;
                        
                        // Collect all sections with details
                        $sections = collect();
                        
                        // From advised sections
                        foreach ($advisedSections as $advisedSection) {
                            if ($advisedSection->section) {
                                $key = 'section_' . $advisedSection->id;
                                $sections->put($key, [
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
                        
                        // From teaching assignments - get actual sections where teacher has students
                        foreach ($teachingAssignments as $assignment) {
                            if ($assignment->strand && $assignment->subject) {
                                // Get unique sections from enrolled students
                                $enrolledSections = $assignment->subjectEnrollments
                                    ->pluck('studentEnrollment.academicYearStrandSection')
                                    ->filter()
                                    ->unique('id');
                                
                                foreach ($enrolledSections as $sectionAssignment) {
                                    if ($sectionAssignment && $sectionAssignment->section) {
                                        $key = 'section_' . $sectionAssignment->id;
                                        
                                        if (!$sections->has($key)) {
                                            $sections->put($key, [
                                                'section_assignment_id' => $sectionAssignment->id,
                                                'grade' => $sectionAssignment->section->grade,
                                                'section_name' => $sectionAssignment->section->name,
                                                'strand' => $sectionAssignment->strand->name ?? 'N/A',
                                                'strand_code' => $sectionAssignment->strand->code ?? '',
                                                'is_adviser' => false,
                                                'subjects' => collect(),
                                                'teaching_assignment_ids' => collect(),
                                                'student_count' => 0,
                                                'academic_year' => $assignment->academicYear->name ?? 'N/A',
                                                'sort_key' => $sectionAssignment->section->grade . '-' . $sectionAssignment->section->name
                                            ]);
                                        }
                                        
                                        // Add subject and assignment ID to this section
                                        $sections->get($key)['subjects']->push($assignment->subject->name);
                                        $sections->get($key)['teaching_assignment_ids']->push($assignment->id);
                                        
                                        // Count students in this section for this subject
                                        $studentCount = $assignment->subjectEnrollments
                                            ->where('studentEnrollment.academic_year_strand_section_id', $sectionAssignment->id)
                                            ->count();
                                        $sections->get($key)['student_count'] += $studentCount;
                                    }
                                }
                            }
                        }
                        
                        // Sort by grade and section name
                        $sections = $sections->sortBy('sort_key')->values();
                    @endphp

                    @if($sections->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            No sections assigned yet. Please contact the administrator for section assignments.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($sections as $section)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 position-relative" style="background-color: #f8f9fa;">
                                        @if($section['is_adviser'] && isset($section['section_assignment_id']))
                                            <a href="{{ route('teacher.students.section', $section['section_assignment_id']) }}" class="text-decoration-none section-card-link">
                                                <div class="section-card" style="transition: all 0.3s ease; cursor: pointer;">
                                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">
                                                                <span class="badge bg-primary me-2">Grade {{ $section['grade'] }}</span>
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
                                                        @if($section['is_adviser'])
                                                            <span class="badge bg-success">
                                                                <i class="ti ti-star me-1"></i>Adviser
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($section['subjects']->isNotEmpty())
                                                        <div class="mt-2 pt-2 border-top">
                                                            <small class="text-muted d-block mb-1"><strong>Subjects Teaching:</strong></small>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach($section['subjects']->unique() as $subject)
                                                                    <span class="badge bg-info text-dark">{{ $subject }}</span>
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
                                                    
                                                    <div class="mt-2 pt-2 border-top">
                                                        <small class="text-primary">
                                                            <i class="ti ti-eye me-1"></i>Click to view students
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                        @else
                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <span class="badge bg-primary me-2">Grade {{ $section['grade'] }}</span>
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
                                                @if($section['is_adviser'])
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-star me-1"></i>Adviser
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($section['subjects']->isNotEmpty())
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted d-block mb-1"><strong>Subjects Teaching:</strong></small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($section['subjects']->unique() as $subject)
                                                            <span class="badge bg-info text-dark">{{ $subject }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($section['student_count'] > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="ti ti-users me-1"></i>{{ $section['student_count'] }} student{{ $section['student_count'] > 1 ? 's' : '' }}
                                                    </small>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="ti ti-calendar me-1"></i>{{ $section['academic_year'] }}
                                                </small>
                                            </div>
                                        @endif

                                        <!-- Remove Buttons -->
                                        <div class="mt-3 pt-3 border-top">
                                            @if($section['is_adviser'])
                                                <form action="{{ route('teacher.profile.adviser.remove', $section['section_assignment_id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove yourself as adviser from this section?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 mb-2">
                                                        <i class="ti ti-user-minus me-1"></i>Remove Adviser Role
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($section['teaching_assignment_ids']->isNotEmpty())
                                                @foreach($section['teaching_assignment_ids'] as $teachingId)
                                                    <form action="{{ route('teacher.profile.teaching.remove', $teachingId) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this teaching assignment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-warning w-100 mb-1">
                                                            <i class="ti ti-trash me-1"></i>Remove Subject Assignment
                                                        </button>
                                                    </form>
                                                @endforeach
                                            @endif
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
                                        <h4 class="mb-0 text-primary">{{ $sections->count() }}</h4>
                                        <small class="text-muted">Total Sections</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2">
                                        <h4 class="mb-0 text-success">{{ $sections->where('is_adviser', true)->count() }}</h4>
                                        <small class="text-muted">As Adviser</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2">
                                        <h4 class="mb-0 text-info">{{ $sections->sum('student_count') }}</h4>
                                        <small class="text-muted">Total Students</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-card-link {
    color: inherit;
    display: block;
}

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

.section-card-link:hover {
    text-decoration: none;
}
</style>

@endsection
