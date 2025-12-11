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

    <!-- Warning Alert for Inactive Status -->
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Inactive Status Notice -->
    @if($teacher->status !== 'active')
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="ti ti-alert-triangle me-2 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-1">Account Status: {{ ucfirst($teacher->status) }}</h6>
                <p class="mb-0">Your account is currently <strong>{{ $teacher->status }}</strong>. You have view-only access to your profile. All other features are restricted. Please contact the administrator for assistance.</p>
            </div>
        </div>
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
                        @if($teacher->status === 'active')
                        <a href="{{ route('teacher.profile.edit') }}" class="btn bg-info">
                            <i class="ti ti-edit me-1"></i>Edit Profile
                        </a>
                        @else
                        <button class="btn btn-secondary" disabled title="Editing disabled for {{ $teacher->status }} accounts">
                            <i class="ti ti-lock me-1"></i>Edit Profile (Disabled)
                        </button>
                        @endif
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
                    @if(!empty($profilePictureUrl))
                        <img src="{{ $profilePictureUrl }}" 
                             alt="Profile Picture" 
                             id="preview-image-teacher"
                             class="rounded-circle mb-3" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #ddd;">
                        <div id="preview-placeholder-teacher" class="d-none"></div>
                    @else
                        <div id="preview-placeholder-teacher" class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 200px; height: 200px; border: 3px solid #ddd;">
                            <i class="ti ti-user" style="font-size: 100px; color: white;"></i>
                        </div>
                        <img src="" id="preview-image-teacher" alt="Profile Picture" class="rounded-circle mb-3 d-none" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #ddd;">
                    @endif
                    
                    <form action="{{ route('teacher.profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        @method('POST')
                        <div class="mb-2">
                            <label for="profile_picture" class="btn bg-info btn-sm w-100 mb-2">
                                <i class="ti ti-photo me-1"></i> Choose File
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewImageTeacher(event)" required>
                            <div id="file-name-teacher" class="text-muted small mb-2">No file chosen</div>
                            @error('profile_picture')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-2" id="upload-btn-teacher" style="display: none;">
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
                    @php
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
                                    // This ensures that when a teacher handles multiple sections (e.g., MARCH & APRIL)
                                    // without explicit per-subject section mapping yet, each section still appears.
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
                            <p class="mb-0">{{ $teacher->phone ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small">Address</label>
                            <p class="mb-0">{{ $teacher->address ?? '-' }}</p>
                        </div>
                        @if($teacher->region || $teacher->province || $teacher->municipality || $teacher->barangay || $teacher->postal_code)
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Region</label>
                            <p class="mb-0">{{ $teacher->region ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Province</label>
                            <p class="mb-0">{{ $teacher->province ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Municipality</label>
                            <p class="mb-0">{{ $teacher->municipality ?? '-' }}</p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-muted small">Barangay</label>
                            <p class="mb-0">{{ $teacher->barangay ?? '-' }}</p>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-bold text-muted small">Postal Code</label>
                            <p class="mb-0">{{ $teacher->postal_code ?? '-' }}</p>
                        </div>
                        @endif
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
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Sections Handled</label>
                            @if($sectionsHandled->isEmpty())
                                <p class="mb-0">-</p>
                            @else
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($sectionsHandled as $sec)
                                        <span class="badge bg-light text-dark border">
                                            <span class="badge bg-primary me-1">G{{ $sec['grade'] }}</span>
                                            Section {{ $sec['section_name'] }}
                                            @if(!empty($sec['strand_code']))
                                                <span class="badge bg-secondary ms-1">{{ $sec['strand_code'] }}</span>
                                            @endif
                                            @if($sec['is_adviser'])
                                                <span class="badge bg-success ms-1"><i class="ti ti-star me-1"></i>Adviser</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
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

<script>
function previewImageTeacher(event) {
    const file = event.target.files[0];
    const fileNameDisplay = document.getElementById('file-name-teacher');
    const uploadBtn = document.getElementById('upload-btn-teacher');
    
    if (file) {
        // Update file name display
        fileNameDisplay.textContent = file.name;
        fileNameDisplay.classList.remove('text-muted');
        fileNameDisplay.classList.add('text-success');
        
        // Show upload button
        uploadBtn.style.display = 'block';
        
        // Preview the image
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview-image-teacher');
            const placeholder = document.getElementById('preview-placeholder-teacher');
            
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        }
        reader.readAsDataURL(file);
    } else {
        fileNameDisplay.textContent = 'No file chosen';
        fileNameDisplay.classList.add('text-muted');
        fileNameDisplay.classList.remove('text-success');
        uploadBtn.style.display = 'none';
    }
}
</script>

@endsection
