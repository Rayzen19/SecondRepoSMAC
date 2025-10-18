@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Section & Advisers Management</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Section & Advisers</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Grade Level Filter -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="ti ti-filter me-2"></i>Filter by Grade Level</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Grade Level</label>
                    <select class="form-select" id="gradeLevelFilter" onchange="filterSections()">
                        <option value="all">All Grade Levels</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Note:</strong> Filter sections by grade level to view only relevant sections and their assigned students.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sections Overview by Strand -->
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-building-community me-2"></i>Section Advisers</h5>
                    <small class="text-muted">
                        <span id="assignedCount">0</span> section(s) with advisers assigned
                    </small>
                </div>
                <button type="button" class="btn btn-success" onclick="saveAllAdvisers()" id="saveAdvisersBtn">
                    <i class="ti ti-device-floppy me-2"></i>Save All Adviser Assignments
                </button>
            </div>
        </div>
        @foreach($strands as $strand)
            <div class="col-lg-6 col-xl-3 mb-3 strand-card" data-strand="{{ $strand->code }}">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header text-white" style="background-color: #467fcf;">
                        <h6 class="mb-0 text-white">
                            <i class="ti ti-building me-2"></i>{{ $strand->code }} - {{ Str::limit($strand->name, 30) }}
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <div class="small text-muted">Subjects</div>
                            <div class="btn-group" role="group" aria-label="View subjects">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewStrandSubjects('{{ $strand->code }}', '11')" title="View Grade 11 Subjects">
                                    <i class="ti ti-eye me-1"></i>G11
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewStrandSubjects('{{ $strand->code }}', '12')" title="View Grade 12 Subjects">
                                    <i class="ti ti-eye me-1"></i>G12
                                </button>
                            </div>
                        </div>
                        <div class="list-group list-group-flush">
                            @php
                                $strandSections = $sections[$strand->code] ?? collect();
                                $grade11Sections = $strandSections->filter(fn($s) => $s->grade === 'G-11');
                                $grade12Sections = $strandSections->filter(fn($s) => $s->grade === 'G-12');
                                $colors = ['success', 'info', 'warning', 'danger'];
                            @endphp
                            
                            @if($grade11Sections->isNotEmpty() || $grade12Sections->isNotEmpty())
                                <!-- Grade 11 Sections -->
                                @if($grade11Sections->isNotEmpty())
                                    <div class="list-group-item px-2 py-1 bg-light">
                                        <small class="fw-bold text-primary">
                                            <i class="ti ti-school me-1"></i>Grade 11 Sections
                                        </small>
                                    </div>
                                    @foreach($grade11Sections as $index => $section)
                                        @php
                                            $color = $colors[$index % count($colors)];
                                        @endphp
                                        <div class="list-group-item px-2 py-2 section-item" data-section="{{ $section->id }}" data-grade="11">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <span class="badge bg-{{ $color }}">
                                                        {{ $section->grade }} {{ $section->name }}
                                                    </span>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary ms-2 view-section-btn" 
                                                            data-strand="{{ $strand->code }}" 
                                                            data-section="{{ $section->id }}"
                                                            data-section-name="{{ $section->grade }} {{ $section->name }}"
                                                            title="View students">
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success ms-1 assign-teacher-btn" 
                                                            data-strand="{{ $strand->code }}" 
                                                            data-section="{{ $section->id }}"
                                                            data-section-name="{{ $section->grade }} {{ $section->name }}"
                                                            data-grade="{{ str_replace('G-', '', $section->grade) }}"
                                                            title="Assign teachers to subjects">
                                                        <i class="ti ti-users"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted student-count-{{ $strand->code }}-{{ $section->id }}">
                                                    0 students
                                                </small>
                                            </div>
                                            <!-- Adviser Selection -->
                                            <div class="mt-2">
                                                <label class="form-label small mb-1 text-muted">
                                                    <i class="ti ti-user-check me-1"></i>Adviser:
                                                </label>
                                                <select class="form-select form-select-sm adviser-select" 
                                                        data-strand="{{ $strand->code }}" 
                                                        data-section="{{ $section->id }}">
                                                    <option value="">-- Select Adviser --</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" 
                                                                data-teacher-name="{{ $teacher->last_name }}, {{ $teacher->first_name }}">
                                                            {{ $teacher->last_name }}, {{ $teacher->first_name }}
                                                            @if($teacher->middle_name)
                                                                {{ substr($teacher->middle_name, 0, 1) }}.
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted adviser-display-{{ $strand->code }}-{{ $section->id }}" style="display: none;">
                                                    <i class="ti ti-check text-success"></i>
                                                    <span class="adviser-name"></span>
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                <!-- Grade 12 Sections -->
                                @if($grade12Sections->isNotEmpty())
                                    <div class="list-group-item px-2 py-1 bg-light">
                                        <small class="fw-bold text-success">
                                            <i class="ti ti-school me-1"></i>Grade 12 Sections
                                        </small>
                                    </div>
                                    @foreach($grade12Sections as $index => $section)
                                        @php
                                            $color = $colors[$index % count($colors)];
                                        @endphp
                                        <div class="list-group-item px-2 py-2 section-item" data-section="{{ $section->id }}" data-grade="12">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <span class="badge bg-{{ $color }}">
                                                        {{ $section->grade }} {{ $section->name }}
                                                    </span>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary ms-2 view-section-btn" 
                                                            data-strand="{{ $strand->code }}" 
                                                            data-section="{{ $section->id }}"
                                                            data-section-name="{{ $section->grade }} {{ $section->name }}"
                                                            title="View students">
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success ms-1 assign-teacher-btn" 
                                                            data-strand="{{ $strand->code }}" 
                                                            data-section="{{ $section->id }}"
                                                            data-section-name="{{ $section->grade }} {{ $section->name }}"
                                                            data-grade="{{ str_replace('G-', '', $section->grade) }}"
                                                            title="Assign teachers to subjects">
                                                        <i class="ti ti-users"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted student-count-{{ $strand->code }}-{{ $section->id }}">
                                                    0 students
                                                </small>
                                            </div>
                                            <!-- Adviser Selection -->
                                            <div class="mt-2">
                                                <label class="form-label small mb-1 text-muted">
                                                    <i class="ti ti-user-check me-1"></i>Adviser:
                                                </label>
                                                <select class="form-select form-select-sm adviser-select" 
                                                        data-strand="{{ $strand->code }}" 
                                                        data-section="{{ $section->id }}">
                                                    <option value="">-- Select Adviser --</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" 
                                                                data-teacher-name="{{ $teacher->last_name }}, {{ $teacher->first_name }}">
                                                            {{ $teacher->last_name }}, {{ $teacher->first_name }}
                                                            @if($teacher->middle_name)
                                                                {{ substr($teacher->middle_name, 0, 1) }}.
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted adviser-display-{{ $strand->code }}-{{ $section->id }}" style="display: none;">
                                                    <i class="ti ti-check text-success"></i>
                                                    <span class="adviser-name"></span>
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @else
                                <div class="list-group-item px-2 py-2 text-center text-muted">
                                    <small>No sections available for this strand</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Hidden data containers for JavaScript -->
<div id="studentAssignmentsData" style="display:none;">@json(session('student_assignments', []))</div>
<div id="savedAdvisersData" style="display:none;">@json($savedAdvisers ?? [])</div>

<!-- Modal for viewing section details -->
<div class="modal fade" id="sectionDetailsModal" tabindex="-1" aria-labelledby="sectionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionDetailsModalLabel">
                    <i class="ti ti-users me-2"></i><span id="modalSectionTitle">Section Details</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Adviser Info -->
                <div id="adviserInfo" class="alert alert-info mb-3" style="display: none;">
                    <i class="ti ti-user-check me-2"></i>
                    <strong>Adviser:</strong> <span id="adviserName">Not assigned</span>
                </div>
                
                <!-- Students List -->
                <div id="studentsList">
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-loader ti-spin mb-2" style="font-size: 3rem;"></i>
                        <p>Loading students...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

        <!-- Modal for viewing subjects per strand and grade level -->
        <div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="subjectsModalLabel">
                            <i class="ti ti-books me-2"></i><span id="modalSubjectsTitle">Subjects</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="subjectsList">
                            <div class="text-center text-muted py-5">
                                <i class="ti ti-loader ti-spin mb-2" style="font-size: 3rem;"></i>
                                <p>Loading subjects...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for assigning teachers to subjects per section -->
        <div class="modal fade" id="subjectTeacherModal" tabindex="-1" aria-labelledby="subjectTeacherModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div>
                            <h5 class="modal-title mb-1" id="subjectTeacherModalLabel">
                                <i class="ti ti-school me-2"></i><span id="modalSubjectTeacherTitle">Assign Teachers to Subjects</span>
                            </h5>
                            <small class="opacity-75" id="modalSubjectTeacherSubtitle">Manage subject-teacher assignments</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background-color: #f8f9fa;">
                        <!-- Info Banner -->
                        <div class="alert border-0 shadow-sm mb-3" style="background-color: #e3f2fd; border-left: 4px solid #2196F3 !important;">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle me-3 mt-1" style="font-size: 1.5rem; color: #1976D2;"></i>
                                <div>
                                    <strong style="color: #1565C0;">Important Information</strong>
                                    <p class="mb-0 mt-1" style="color: #424242; font-size: 0.9rem;">Teachers must be profiled for subjects in <strong>Admin → Teachers</strong> before they appear in the assignment dropdowns.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row g-3 mb-3" id="assignmentStats" style="display: none;">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="background-color: #8b5cf6;">
                                    <div class="card-body text-center text-white">
                                        <i class="ti ti-books mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="mb-0 fw-bold" id="totalSubjects">0</h3>
                                        <small class="opacity-75">Total Subjects</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="background-color: #10b981;">
                                    <div class="card-body text-center text-white">
                                        <i class="ti ti-user-check mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="mb-0 fw-bold" id="assignedSubjects">0</h3>
                                        <small class="opacity-75">Teachers Assigned</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="background-color: #0ea5e9;">
                                    <div class="card-body text-center text-white">
                                        <i class="ti ti-alert-circle mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="mb-0 fw-bold" id="unassignedSubjects">0</h3>
                                        <small class="opacity-75">Still Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subject List -->
                        <div id="subjectTeacherList">
                            <div class="text-center text-muted py-5">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="fw-semibold">Loading subjects...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <small class="text-muted">
                                <i class="ti ti-clock me-1"></i>Last updated: <span id="lastUpdated">Just now</span>
                            </small>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<style>
    /* Animation for fade-in effect */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Enhanced table styling */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Button hover effects */
    .btn-primary {
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    /* Select dropdown focus */
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    
    /* Statistics card animations */
    @keyframes countUp {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    #assignmentStats .card-body h3 {
        animation: countUp 0.5s ease-out;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Modal entrance animation */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: scale(0.9);
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
    }
    
    /* Spinner border color */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
    
    /* Alert animations */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .alert {
        animation: slideInDown 0.3s ease-out;
    }
    
    /* Grade level header styling */
    .list-group-item.bg-light {
        background-color: #f8f9fa !important;
        border-top: 2px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    
    .list-group-item.bg-light:first-of-type {
        border-top: none;
    }
    
    .list-group-item.bg-light small {
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>

<script>
    // Laravel Routes Configuration
    const routes = {
        getStudents: "{!! route('admin.section-advisers.get-students') !!}",
        getSectionStudents: "{!! route('admin.section-advisers.get-section-students') !!}",
        getSectionCounts: "{!! route('admin.section-advisers.get-section-counts') !!}",
        removeStudent: "{!! route('admin.section-advisers.remove-student') !!}",
        saveAdvisers: "{!! route('admin.section-advisers.save-advisers') !!}",
        getSubjects: "{!! route('admin.section-advisers.get-subjects') !!}",
        subjectTeachers: "{!! route('admin.section-advisers.subject-teachers') !!}",
        saveSubjectTeacher: "{!! route('admin.section-advisers.save-subject-teacher') !!}"
    };

    // Store adviser assignments
    let adviserAssignments = {};
    
    // Store student assignments from session
    let studentAssignments = [];
    try {
        const dataEl = document.getElementById('studentAssignmentsData');
        if (dataEl && dataEl.textContent) {
            studentAssignments = JSON.parse(dataEl.textContent);
        }
    } catch(e) {
        console.error('Error parsing student assignments:', e);
    }

    // Filter sections by grade level
    function filterSections() {
        const gradeLevel = document.getElementById('gradeLevelFilter').value;
        const strandCards = document.querySelectorAll('.strand-card');
        
        strandCards.forEach(card => {
            const strand = card.dataset.strand;
            
            if (gradeLevel === 'all') {
                // Show all sections and headers
                card.style.display = 'block';
                card.querySelectorAll('.section-item, .list-group-item.bg-light').forEach(el => {
                    el.style.display = 'block';
                });
            } else {
                // Filter sections based on grade level
                const sections = card.querySelectorAll('.section-item');
                const grade11Header = card.querySelector('.list-group-item.bg-light small.text-primary')?.closest('.list-group-item');
                const grade12Header = card.querySelector('.list-group-item.bg-light small.text-success')?.closest('.list-group-item');
                let hasVisibleSections = false;
                
                sections.forEach(section => {
                    const sectionGrade = section.dataset.grade;
                    
                    if (gradeLevel === sectionGrade) {
                        section.style.display = 'block';
                        hasVisibleSections = true;
                    } else {
                        section.style.display = 'none';
                    }
                });
                
                // Show/hide grade headers based on filter
                if (grade11Header) {
                    grade11Header.style.display = (gradeLevel === '11') ? 'block' : 'none';
                }
                if (grade12Header) {
                    grade12Header.style.display = (gradeLevel === '12') ? 'block' : 'none';
                }
                
                // Hide strand card if no sections match the filter
                card.style.display = hasVisibleSections ? 'block' : 'none';
            }
        });
    }

    // View section details with students and adviser
    async function viewSectionDetails(strandCode, sectionId, sectionName) {
        // Get adviser info
        const adviserKey = `${strandCode}-${sectionId}`;
        const adviser = adviserAssignments[adviserKey];
        
        // Update modal title
        document.getElementById('modalSectionTitle').textContent = sectionName;
        
        // Update adviser info
        const adviserInfo = document.getElementById('adviserInfo');
        const adviserNameEl = document.getElementById('adviserName');
        
        if (adviser && adviser.teacherName) {
            adviserInfo.style.display = 'block';
            adviserInfo.className = 'alert alert-success mb-3';
            adviserNameEl.textContent = adviser.teacherName;
        } else {
            adviserInfo.style.display = 'block';
            adviserInfo.className = 'alert alert-warning mb-3';
            adviserNameEl.textContent = 'No adviser assigned';
        }
        
        // Display students list
        const studentsList = document.getElementById('studentsList');
        
        // Show loading state
        studentsList.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="ti ti-loader ti-spin mb-2" style="font-size: 3rem;"></i>
                <p>Loading students from database...</p>
            </div>
        `;
        
        // Show modal immediately
        const modal = new bootstrap.Modal(document.getElementById('sectionDetailsModal'));
        modal.show();

        try {
            // Fetch students enrolled in this section from database
            const response = await fetch(routes.getSectionStudents, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    strand_code: strandCode,
                    section_id: sectionId
                })
            });

            if (!response.ok) {
                throw new Error('Failed to fetch section students');
            }

            const data = await response.json();
            const students = data.students || [];

            if (students.length === 0) {
                studentsList.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-users-off mb-2" style="font-size: 3rem;"></i>
                        <p class="mb-0">No students enrolled in this section yet</p>
                        <small>Go to Assigning List to assign students</small>
                    </div>
                `;
            } else {
                let html = '<div class="list-group">';
                students.forEach((student, index) => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">${index + 1}. ${student.first_name} ${student.last_name}</div>
                                    <small class="text-muted">
                                        <span class="badge bg-secondary">${student.student_number}</span>
                                        <span class="badge bg-primary-subtle text-primary ms-1">${student.program}</span>
                                        <span class="badge bg-info-subtle text-info ms-1">${student.academic_year}</span>
                                        ${student.registration_number ? `<span class="badge bg-success-subtle text-success ms-1">REG: ${student.registration_number}</span>` : ''}
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-3" title="Remove from section" onclick="removeStudentFromSection('${strandCode}', ${sectionId}, ${student.id})">
                                    <i class="ti ti-user-minus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                studentsList.innerHTML = html;
            }
        } catch (error) {
            console.error('Error fetching section students:', error);
            studentsList.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ti ti-alert-circle me-2"></i>
                    Error loading students from database. Please try again.<br>
                    <small>${error?.message || ''}</small>
                </div>
            `;
        }
    }

    // Fetch student details from server
    async function fetchStudentDetails(studentIds) {
        const response = await fetch(routes.getStudents, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ student_ids: studentIds })
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch student details');
        }
        
        const data = await response.json();
        return data.students;
    }

    // Update student counts for each section
    async function updateStudentCounts() {
        try {
            const response = await fetch(routes.getSectionCounts, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch section counts');
            }

            const data = await response.json();
            const counts = data.counts || {};

            // Update all count elements
            document.querySelectorAll('[class*="student-count-"]').forEach(el => {
                el.textContent = '0 students';
            });

            // Set actual counts from database
            for (const [key, count] of Object.entries(counts)) {
                const countEl = document.querySelector(`.student-count-${key}`);
                if (countEl) {
                    countEl.textContent = `${count} student${count !== 1 ? 's' : ''}`;
                }
            }
        } catch (error) {
            console.error('Error updating student counts:', error);
        }
    }

    // Remove a student from a section and update session/state
    async function removeStudentFromSection(strandCode, sectionId, studentId) {
        if (!confirm('Remove this student from the section?')) return;

        try {
            const response = await fetch(routes.removeStudent, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    strand_code: strandCode,
                    section_id: sectionId,
                    student_id: studentId
                })
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error('Failed to remove student');
            }

            // Update local session cache in JS
            if (Array.isArray(studentAssignments)) {
                studentAssignments = studentAssignments.filter(a => !(
                    String(a.strand_code) === String(strandCode) &&
                    parseInt(a.section_id, 10) === parseInt(sectionId, 10) &&
                    String(a.student_id) === String(studentId)
                ));
            }

            // Refresh counts and current modal view
            updateStudentCounts();
            
            // Get section name from button to refresh modal
            const sectionBtn = document.querySelector(`.view-section-btn[data-strand="${strandCode}"][data-section="${sectionId}"]`);
            const sectionName = sectionBtn ? sectionBtn.dataset.sectionName : `${strandCode} - Section ${sectionId}`;
            viewSectionDetails(strandCode, sectionId, sectionName);

            showAlert('Student removed from section.', 'success');
        } catch (e) {
            console.error(e);
            showAlert('Could not remove student. Please try again.', 'danger');
        }
    }

    // Load saved advisers on page load
    function loadSavedAdvisers() {
        let savedAdvisers = [];
        try {
            const dataEl = document.getElementById('savedAdvisersData');
            if (dataEl && dataEl.textContent) {
                savedAdvisers = JSON.parse(dataEl.textContent);
            }
        } catch(e) {
            console.error('Error parsing saved advisers:', e);
        }
        
        if (savedAdvisers && savedAdvisers.length > 0) {
            savedAdvisers.forEach(adviser => {
                const key = `${adviser.strand_code}-${adviser.section_id}`;
                const selectElement = document.querySelector(`.adviser-select[data-strand="${adviser.strand_code}"][data-section="${adviser.section_id}"]`);
                
                if (selectElement) {
                    selectElement.value = adviser.teacher_id;
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const teacherName = selectedOption.dataset.teacherName;
                    
                    // Store in memory
                    adviserAssignments[key] = {
                        teacherId: adviser.teacher_id,
                        teacherName: teacherName,
                        sectionId: adviser.section_id
                    };
                    
                    // Update display
                    const adviserDisplay = document.querySelector(`.adviser-display-${adviser.strand_code}-${adviser.section_id}`);
                    if (adviserDisplay) {
                        adviserDisplay.style.display = 'block';
                        adviserDisplay.querySelector('.adviser-name').textContent = teacherName;
                    }
                }
            });
            
            // Update assigned count
            updateAssignedCount();
            
            console.log('📋 Loaded saved adviser assignments:', savedAdvisers.length);
        }
    }

    // Assign a teacher as adviser to a section
    function assignAdviser(strandCode, sectionId, selectElement) {
        const teacherId = selectElement.value;
        const key = `${strandCode}-${sectionId}`;
        
        // Get section name from button
        const sectionBtn = document.querySelector(`.view-section-btn[data-strand="${strandCode}"][data-section="${sectionId}"]`);
        const sectionName = sectionBtn ? sectionBtn.dataset.sectionName : `Section ${sectionId}`;
        
        if (teacherId) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const teacherName = selectedOption.dataset.teacherName;
            
            // Store adviser assignment
            adviserAssignments[key] = {
                teacherId: teacherId,
                teacherName: teacherName,
                sectionId: sectionId
            };
            
            // Update display
            const adviserDisplay = document.querySelector(`.adviser-display-${strandCode}-${sectionId}`);
            if (adviserDisplay) {
                adviserDisplay.style.display = 'block';
                adviserDisplay.querySelector('.adviser-name').textContent = teacherName;
            }
            
            showAlert(`${teacherName} assigned as adviser for ${sectionName}`, 'success');
        } else {
            // Remove adviser assignment
            delete adviserAssignments[key];
            
            const adviserDisplay = document.querySelector(`.adviser-display-${strandCode}-${sectionId}`);
            if (adviserDisplay) {
                adviserDisplay.style.display = 'none';
            }
            
            showAlert(`Adviser removed from ${sectionName}`, 'info');
        }
        
        // Update assigned count
        updateAssignedCount();
    }

    // Update the count of assigned advisers
    function updateAssignedCount() {
        const count = Object.keys(adviserAssignments).length;
        const countElement = document.getElementById('assignedCount');
        if (countElement) {
            countElement.textContent = count;
        }
    }

    // Save all adviser assignments to the server
    async function saveAllAdvisers() {
        // Collect all adviser assignments
        const advisers = [];
        for (const key in adviserAssignments) {
            const [strandCode, sectionId] = key.split('-');
            advisers.push({
                strand_code: strandCode,
                section_id: parseInt(sectionId),
                teacher_id: adviserAssignments[key].teacherId
            });
        }
        
        if (advisers.length === 0) {
            showAlert('No adviser assignments to save. Please assign advisers first.', 'warning');
            return;
        }
        
        // Show loading state
        const saveButton = document.getElementById('saveAdvisersBtn');
        const originalText = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="ti ti-loader ti-spin me-2"></i>Saving...';
        
        try {
            const response = await fetch(routes.saveAdvisers, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ advisers })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showAlert(`✅ Successfully saved ${data.count} adviser assignment(s)!`, 'success');
            } else {
                showAlert('❌ Failed to save adviser assignments. Please try again.', 'danger');
            }
        } catch (error) {
            console.error('Error saving advisers:', error);
            showAlert('❌ An error occurred while saving. Please try again.', 'danger');
        } finally {
            // Restore button state
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
        }
    }

    // Show alert message
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        const alert = document.createElement('div');
        alert.id = alertId;
        alert.className = `alert alert-${type} alert-dismissible fade show border-0 shadow-sm`;
        alert.style.cssText = 'border-left: 4px solid currentColor; margin-bottom: 1rem;';
        
        // Icon mapping
        const icons = {
            'success': 'ti-circle-check',
            'danger': 'ti-alert-circle',
            'warning': 'ti-alert-triangle',
            'info': 'ti-info-circle'
        };
        
        const icon = icons[type] || 'ti-info-circle';
        
        alert.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="ti ${icon} me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.classList.remove('show');
                setTimeout(() => alertElement.remove(), 150);
            }
        }, 5000);
    }

    // Load saved advisers on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSavedAdvisers();
        updateStudentCounts(); // Update student counts on page load
        
        // Attach event listeners for view section buttons
        document.querySelectorAll('.view-section-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const strand = this.dataset.strand;
                const section = this.dataset.section;
                const sectionName = this.dataset.sectionName;
                viewSectionDetails(strand, section, sectionName);
            });
        });
        
        // Attach event listeners for assign teacher buttons
        document.querySelectorAll('.assign-teacher-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const strand = this.dataset.strand;
                const section = this.dataset.section;
                const sectionName = this.dataset.sectionName;
                const gradeLevel = this.dataset.grade;
                openSubjectTeacherAssignment(strand, section, sectionName, gradeLevel);
            });
        });
        
        // Attach event listeners for adviser select dropdowns
        document.querySelectorAll('.adviser-select').forEach(select => {
            select.addEventListener('change', function() {
                const strand = this.dataset.strand;
                const section = this.dataset.section;
                assignAdviser(strand, section, this);
            });
        });
    });

    // View subjects per strand and grade level
    async function viewStrandSubjects(strandCode, gradeLevel) {
        document.getElementById('modalSubjectsTitle').textContent = `${strandCode} - Grade ${gradeLevel} Subjects`;
        const subjectsList = document.getElementById('subjectsList');
        subjectsList.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="ti ti-loader ti-spin mb-2" style="font-size: 3rem;"></i>
                <p>Loading subjects...</p>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('subjectsModal'));
        modal.show();

        try {
            const response = await fetch(routes.getSubjects, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ strand_code: strandCode, grade_level: gradeLevel })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Failed to load subjects');
            }

            if (!data.subjects || data.subjects.length === 0) {
                subjectsList.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="ti ti-alert-circle me-2"></i>
                        No subjects found for ${strandCode} - Grade ${gradeLevel}.
                    </div>
                `;
                return;
            }

            let html = '<div class="list-group">';
            data.subjects.forEach((s, idx) => {
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">${idx + 1}. ${s.name}</div>
                            <small class="text-muted">
                                <span class="badge bg-secondary me-1">${s.code}</span>
                                ${s.type ? `<span class="badge bg-primary-subtle text-primary me-1">${s.type}</span>` : ''}
                                ${s.semester ? `<span class="badge bg-info-subtle text-info">${s.semester}</span>` : ''}
                            </small>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            subjectsList.innerHTML = html;
        } catch (err) {
            console.error(err);
            subjectsList.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="ti ti-alert-circle me-2"></i>
                    Error loading subjects. ${err?.message || ''}
                </div>
            `;
        }
    }

    // Open subject-teacher assignment modal
    async function openSubjectTeacherAssignment(strandCode, sectionId, sectionName, gradeLevel) {
        // Use the provided grade level from the data attribute
        
        const modalTitle = document.getElementById('modalSubjectTeacherTitle');
        const modalSubtitle = document.getElementById('modalSubjectTeacherSubtitle');
        modalTitle.textContent = sectionName;
        modalSubtitle.textContent = `Grade ${gradeLevel} Subject-Teacher Assignments`;
        
        const modal = new bootstrap.Modal(document.getElementById('subjectTeacherModal'));
        modal.show();
        
        const listContainer = document.getElementById('subjectTeacherList');
        listContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="fw-semibold text-muted">Loading subjects...</p>
            </div>`;
        
        try {
            // First, ensure adviser is saved
            const adviserSelect = document.querySelector(`.adviser-select[data-strand="${strandCode}"][data-section="${sectionId}"]`);
            const adviserId = adviserSelect?.value;
            
            if (adviserId) {
                await saveAdvisersToDb([{strand_code: strandCode, section_id: parseInt(sectionId), teacher_id: parseInt(adviserId)}]);
            }
            
            // Fetch subjects
            const response = await fetch(routes.getSubjects, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ strand_code: strandCode, grade_level: gradeLevel })
            });
            
            const data = await response.json();
            
            if (!data.success || !data.subjects || data.subjects.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="ti ti-book-off text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted">No Subjects Found</h5>
                        <p class="text-muted small">No subjects are configured for ${strandCode} Grade ${gradeLevel}</p>
                    </div>`;
                document.getElementById('assignmentStats').style.display = 'none';
                return;
            }
            
            // Update statistics
            const totalCount = data.subjects.length;
            const assignedCount = data.subjects.filter(s => s.assigned_teacher).length;
            const unassignedCount = totalCount - assignedCount;
            
            document.getElementById('totalSubjects').textContent = totalCount;
            document.getElementById('assignedSubjects').textContent = assignedCount;
            document.getElementById('unassignedSubjects').textContent = unassignedCount;
            document.getElementById('assignmentStats').style.display = 'flex';
            
            // Build enhanced table
            let html = '<div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive">';
            html += '<table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">';
            html += '<thead style="background: linear-gradient(to right, #f8f9fa, #e9ecef); border-bottom: 2px solid #dee2e6;"><tr>';
            html += '<th class="text-center fw-bold" style="width: 5%; padding: 1rem; color: #495057;">#</th>';
            html += '<th class="fw-bold" style="width: 12%; padding: 1rem; color: #495057;"><i class="ti ti-tag me-2"></i>Code</th>';
            html += '<th class="fw-bold" style="width: 30%; padding: 1rem; color: #495057;"><i class="ti ti-book me-2"></i>Subject Name</th>';
            html += '<th class="fw-bold" style="width: 25%; padding: 1rem; color: #495057;"><i class="ti ti-user me-2"></i>Assigned Teacher</th>';
            html += '<th class="fw-bold" style="width: 28%; padding: 1rem; color: #495057;"><i class="ti ti-settings me-2"></i>Actions</th>';
            html += '</tr></thead><tbody>';
            
            for (let i = 0; i < data.subjects.length; i++) {
                const subj = data.subjects[i];
                const rowId = `subj-${strandCode}-${gradeLevel}-${subj.id}`;
                const isAssigned = subj.assigned_teacher;
                const currentTeacher = isAssigned ? subj.assigned_teacher.name : '<span class="text-muted fst-italic">Not assigned yet</span>';
                
                html += `<tr class="subject-row" style="transition: all 0.3s ease;" data-assigned="${isAssigned ? 'true' : 'false'}">
                    <td class="text-center fw-bold text-muted" style="padding: 1rem; font-size: 0.9rem;">${i + 1}</td>
                    <td style="padding: 1rem;">
                        <span class="badge px-3 py-2" style="background-color: #6c757d; font-size: 0.875rem; font-weight: 500;">${subj.code}</span>
                    </td>
                    <td style="padding: 1rem;">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold mb-1">${subj.name}</span>
                            <div>
                                ${subj.type ? `<span class="badge bg-light text-dark me-1" style="font-size: 0.75rem;"><i class="ti ti-category me-1"></i>${subj.type}</span>` : ''}
                                ${subj.semester ? `<span class="badge bg-info-subtle text-info" style="font-size: 0.75rem;"><i class="ti ti-calendar me-1"></i>${subj.semester}</span>` : ''}
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div id="${rowId}-current" class="d-flex align-items-center">
                            ${isAssigned ? `
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="ti ti-user-check text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-success">${currentTeacher}</div>
                                        <small class="text-muted">Currently assigned</small>
                                    </div>
                                </div>
                            ` : `
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="ti ti-alert-circle text-white"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fst-italic">Not assigned yet</div>
                                        <small class="text-muted">Please assign a teacher</small>
                                    </div>
                                </div>
                            `}
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div class="d-flex gap-2 align-items-center">
                            <select id="${rowId}-teacher" class="form-select form-select-sm shadow-sm" style="flex: 1; min-width: 150px; border: 2px solid #dee2e6;">
                                <option value="">Loading teachers...</option>
                            </select>
                            <button class="btn btn-sm btn-primary shadow-sm px-3" id="${rowId}-btn" 
                                    onclick="saveSubjectTeacher('${rowId}', '${strandCode}', '${gradeLevel}', ${subj.id})"
                                    style="white-space: nowrap; min-width: 85px; font-weight: 500;">
                                <i class="ti ti-device-floppy me-1"></i>Save
                            </button>
                            ${isAssigned ? `
                                <button class="btn btn-sm btn-danger shadow-sm px-3" id="${rowId}-del-btn" 
                                        onclick="deleteSubjectTeacher('${rowId}', '${strandCode}', '${gradeLevel}', ${subj.id})"
                                        style="white-space: nowrap; font-weight: 500;"
                                        title="Remove assigned teacher">
                                    <i class="ti ti-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>`;
            }
            
            html += '</tbody></table></div></div></div>';
            listContainer.innerHTML = html;
            
            // Fetch teachers for each subject
            for (const subj of data.subjects) {
                const rowId = `subj-${strandCode}-${gradeLevel}-${subj.id}`;
                await fetchTeachersForSubject(subj.id, rowId, subj.assigned_teacher?.id);
            }
            
            // Update last updated time
            document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
            
            // Add row hover effects
            addRowHoverEffects();
            
        } catch (err) {
            console.error(err);
            listContainer.innerHTML = `
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle me-3" style="font-size: 2rem;"></i>
                        <div>
                            <strong>Error Loading Subjects</strong>
                            <p class="mb-0 mt-1 small">${err.message || 'Please try again later.'}</p>
                        </div>
                    </div>
                </div>`;
            document.getElementById('assignmentStats').style.display = 'none';
        }
    }
    
    // Add hover effects to table rows
    function addRowHoverEffects() {
        const rows = document.querySelectorAll('.subject-row');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
    }

    // Fetch eligible teachers for a subject
    async function fetchTeachersForSubject(subjectId, rowId, currentTeacherId = null) {
        console.log(`Fetching teachers for subject ${subjectId}, rowId: ${rowId}, current: ${currentTeacherId}`);
        
        const select = document.getElementById(`${rowId}-teacher`);
        if (!select) {
            console.error(`Select element not found: ${rowId}-teacher`);
            return;
        }
        
        try {
            const response = await fetch(routes.subjectTeachers, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ subject_id: subjectId })
            });
            
            const data = await response.json();
            console.log(`Teachers API response for subject ${subjectId}:`, data);
            
            select.innerHTML = '<option value="">-- Select Teacher --</option>';
            
            if (data.success && data.teachers && data.teachers.length > 0) {
                console.log(`Found ${data.teachers.length} teacher(s) for subject ${subjectId}`);
                data.teachers.forEach(t => {
                    const option = document.createElement('option');
                    option.value = t.id;
                    option.textContent = t.name;
                    if (currentTeacherId && t.id == currentTeacherId) {
                        option.selected = true;
                        console.log(`Pre-selected teacher: ${t.name}`);
                    }
                    select.appendChild(option);
                });
            } else {
                console.warn(`No teachers profiled for subject ${subjectId}`);
                select.innerHTML += '<option disabled>No teachers profiled for this subject</option>';
            }
        } catch (err) {
            console.error(`Error fetching teachers for subject ${subjectId}:`, err);
            select.innerHTML = '<option value="">Error loading</option>';
        }
    }

    // Save subject-teacher assignment
    async function saveSubjectTeacher(rowId, strandCode, gradeLevel, subjectId) {
        console.log('=== Save Subject Teacher ===');
        console.log('Row ID:', rowId);
        console.log('Strand:', strandCode);
        console.log('Grade:', gradeLevel);
        console.log('Subject ID:', subjectId);
        
        const select = document.getElementById(`${rowId}-teacher`);
        const teacherIdRaw = select?.value || '';
        const teacherId = (teacherIdRaw && teacherIdRaw !== '' && teacherIdRaw !== 'null') ? parseInt(teacherIdRaw, 10) : null;
        const btn = document.getElementById(`${rowId}-btn`);
        const currentEl = document.getElementById(`${rowId}-current`);
        
        console.log('Select value (raw):', teacherIdRaw);
        console.log('Selected Teacher ID (parsed):', teacherId);
        console.log('Select element:', select);
        console.log('Button element:', btn);
        
        if (!teacherId) {
            console.warn('No teacher selected');
            showAlert('⚠️ Please select a teacher first', 'warning');
            return;
        }
        
        // Show loading state with animation
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            btn.classList.add('disabled');
        }
        
        // Add loading animation to current display
        if (currentEl) {
            currentEl.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-muted">Updating assignment...</span>
                </div>`;
        }
        
        try {
            const payload = {
                strand_code: strandCode,
                grade_level: gradeLevel,
                subject_id: subjectId,
                teacher_id: teacherId
            };
            console.log('Payload:', JSON.stringify(payload, null, 2));
            
            const url = routes.saveSubjectTeacher;
            console.log('Posting to URL:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            console.log('Response headers:', [...response.headers.entries()]);
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                throw new Error('Invalid JSON response: ' + responseText.substring(0, 100));
            }
            
            console.log('Response data:', data);
            
            if (data.success) {
                // Get the row element FIRST
                const row = btn?.closest('tr');
                console.log('Found row element:', row);
                
                // Update row data attribute FIRST
                if (row) {
                    row.setAttribute('data-assigned', 'true');
                    console.log('Set data-assigned=true on row');
                } else {
                    console.error('❌ Could not find row element to update!');
                }
                
                // Update the current teacher display with success
                if (currentEl) {
                    const teacherName = select.options[select.selectedIndex]?.text || 'Assigned';
                    currentEl.innerHTML = `
                        <div class="d-flex align-items-center animate-fade-in">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="ti ti-user-check text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-success">${teacherName}</div>
                                <small class="text-muted">Currently assigned</small>
                            </div>
                        </div>
                    `;
                    console.log('Updated current teacher to:', teacherName);
                }
                
                // Update select styling
                select.style.borderColor = '#28a745';
                select.style.borderWidth = '2px';
                
                // Add delete button if it doesn't exist
                const deleteBtn = document.getElementById(`${rowId}-del-btn`);
                if (!deleteBtn) {
                    const btnContainer = btn.parentElement;
                    const newDeleteBtn = document.createElement('button');
                    newDeleteBtn.id = `${rowId}-del-btn`;
                    newDeleteBtn.className = 'btn btn-sm btn-danger shadow-sm px-3';
                    newDeleteBtn.style.cssText = 'white-space: nowrap; font-weight: 500;';
                    newDeleteBtn.title = 'Remove assigned teacher';
                    newDeleteBtn.innerHTML = '<i class="ti ti-trash"></i>';
                    newDeleteBtn.onclick = function() {
                        deleteSubjectTeacher(rowId, strandCode, gradeLevel, subjectId);
                    };
                    btnContainer.appendChild(newDeleteBtn);
                }
                
                // Update statistics AFTER DOM updates are complete
                setTimeout(() => {
                    updateAssignmentStatistics();
                }, 100);
                
                // Show success alert
                showAlert('✅ ' + data.message, 'success');
                console.log('✅ Save successful');
                
                // Update last updated time
                document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
            } else {
                showAlert('❌ ' + (data.message || 'Failed to save'), 'danger');
                console.error('❌ Save failed:', data.message);
                
                // Restore previous display on error
                if (currentEl) {
                    currentEl.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="ti ti-alert-circle text-white"></i>
                            </div>
                            <div>
                                <div class="text-danger">Error - Please try again</div>
                                <small class="text-muted">Failed to assign</small>
                            </div>
                        </div>
                    `;
                }
            }
        } catch (err) {
            console.error('❌ Exception occurred:', err);
            console.error('Error stack:', err.stack);
            showAlert('❌ Error saving assignment: ' + (err.message || 'Unknown error'), 'danger');
            
            // Restore on error
            if (currentEl) {
                currentEl.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="ti ti-x text-white"></i>
                        </div>
                        <div>
                            <div class="text-danger">Connection error</div>
                            <small class="text-muted">Please try again</small>
                        </div>
                    </div>
                `;
            }
        } finally {
            // Restore button state
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Save';
                btn.classList.remove('disabled');
            }
            console.log('=== Save Subject Teacher Complete ===');
        }
    }
    
    // Update assignment statistics
    function updateAssignmentStatistics() {
        console.log('🔄 Updating assignment statistics...');
        
        const rows = document.querySelectorAll('.subject-row');
        const total = rows.length;
        let assigned = 0;
        
        rows.forEach((row, index) => {
            const isAssigned = row.getAttribute('data-assigned') === 'true';
            console.log(`Row ${index + 1}: data-assigned="${row.getAttribute('data-assigned')}" (${isAssigned ? 'ASSIGNED' : 'NOT ASSIGNED'})`);
            if (isAssigned) {
                assigned++;
            }
        });
        
        const unassigned = total - assigned;
        
        console.log(`📊 Statistics: Total=${total}, Assigned=${assigned}, Unassigned=${unassigned}`);
        
        // Update with animation
        const totalEl = document.getElementById('totalSubjects');
        const assignedEl = document.getElementById('assignedSubjects');
        const unassignedEl = document.getElementById('unassignedSubjects');
        
        if (totalEl) {
            totalEl.style.transform = 'scale(1.2)';
            totalEl.textContent = total;
            setTimeout(() => totalEl.style.transform = 'scale(1)', 200);
        }
        
        if (assignedEl) {
            assignedEl.style.transform = 'scale(1.2)';
            assignedEl.textContent = assigned;
            setTimeout(() => assignedEl.style.transform = 'scale(1)', 200);
        }
        
        if (unassignedEl) {
            unassignedEl.style.transform = 'scale(1.2)';
            unassignedEl.textContent = unassigned;
            setTimeout(() => unassignedEl.style.transform = 'scale(1)', 200);
        }
        
        console.log('✅ Statistics updated successfully');
    }
    
    // Delete/Clear subject-teacher assignment
    async function deleteSubjectTeacher(rowId, strandCode, gradeLevel, subjectId) {
        if (!confirm('Are you sure you want to remove the assigned teacher from this subject?')) {
            return;
        }
        
        console.log('=== Delete Subject Teacher ===');
        console.log('Row ID:', rowId);
        console.log('Strand:', strandCode);
        console.log('Grade:', gradeLevel);
        console.log('Subject ID:', subjectId);
        
        const btn = document.getElementById(`${rowId}-del-btn`);
        const currentEl = document.getElementById(`${rowId}-current`);
        const select = document.getElementById(`${rowId}-teacher`);
        
        // Show loading state
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
        
        if (currentEl) {
            currentEl.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-muted">Removing assignment...</span>
                </div>`;
        }
        
        try {
            const payload = {
                strand_code: strandCode,
                grade_level: gradeLevel,
                subject_id: subjectId,
                teacher_id: ''  // Send empty string to clear the assignment
            };
            
            console.log('Delete payload:', JSON.stringify(payload, null, 2));
            
            const url = routes.saveSubjectTeacher;
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            
            const responseText = await response.text();
            let data;
            
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                throw new Error('Invalid JSON response');
            }
            
            if (data.success) {
                // Get the row element FIRST before any DOM manipulation
                const saveBtn = document.getElementById(`${rowId}-btn`);
                const row = (btn?.closest('tr') || saveBtn?.closest('tr'));
                
                console.log('Found row element:', row);
                
                // Update row data attribute FIRST
                if (row) {
                    row.setAttribute('data-assigned', 'false');
                    console.log('Set data-assigned=false on row');
                } else {
                    console.error('❌ Could not find row element to update!');
                }
                
                // Update display to unassigned state
                if (currentEl) {
                    currentEl.innerHTML = `
                        <div class="d-flex align-items-center animate-fade-in">
                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="ti ti-alert-circle text-white"></i>
                            </div>
                            <div>
                                <div class="text-muted fst-italic">Not assigned yet</div>
                                <small class="text-muted">Please assign a teacher</small>
                            </div>
                        </div>
                    `;
                }
                
                // Reset select dropdown
                if (select) {
                    select.value = '';
                    select.style.borderColor = '#dee2e6';
                    select.style.borderWidth = '2px';
                }
                
                // Remove delete button
                if (btn) {
                    btn.remove();
                }
                
                // Update statistics AFTER DOM updates are complete
                setTimeout(() => {
                    updateAssignmentStatistics();
                }, 100);
                
                showAlert('✅ Teacher assignment removed successfully', 'success');
                console.log('✅ Delete successful');
                
                // Update last updated time
                document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
            } else {
                showAlert('❌ ' + (data.message || 'Failed to remove assignment'), 'danger');
                console.error('❌ Delete failed:', data.message);
            }
        } catch (err) {
            console.error('❌ Exception occurred:', err);
            showAlert('❌ Error removing assignment: ' + (err.message || 'Unknown error'), 'danger');
        } finally {
            // Restore button if still exists
            if (btn && document.getElementById(`${rowId}-del-btn`)) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-trash"></i>';
            }
            console.log('=== Delete Subject Teacher Complete ===');
        }
    }

    // Helper to save advisers to database
    async function saveAdvisersToDb(advisers) {
        try {
            const response = await fetch(routes.saveAdvisers, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ advisers })
            });
            return await response.json();
        } catch (err) {
            console.error('Error saving advisers:', err);
            return { success: false };
        }
    }
</script>
@endsection

