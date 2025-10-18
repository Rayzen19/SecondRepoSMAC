@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Grade 12 Section & Advisers</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.section-advisers.index') }}">Section & Advisers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Grade 12</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <a href="{{ route('admin.section-advisers.grade11') }}" class="btn btn-info">
            <i class="ti ti-school me-2"></i>View Grade 11 Sections
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Grade Level Info Banner -->
    <div class="alert alert-success mb-3 border-0 shadow-sm">
        <i class="ti ti-info-circle me-2"></i>
        <strong>Grade 12 Sections Only</strong> - Showing all Grade 12 sections across all strands.
    </div>

    <!-- Sections Overview by Strand -->
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-building-community me-2"></i>Section Advisers - Grade 12</h5>
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
            @php
                $strandSections = $sections[$strand->code] ?? collect();
            @endphp
            @if($strandSections->isNotEmpty())
            <div class="col-lg-6 col-xl-3 mb-3 strand-card" data-strand="{{ $strand->code }}">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header text-white" style="background-color: #28a745;">
                        <h6 class="mb-0 text-white">
                            <i class="ti ti-building me-2"></i>{{ $strand->code }} - {{ Str::limit($strand->name, 30) }}
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <div class="small text-muted">Subjects</div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewStrandSubjects('{{ $strand->code }}', '12')" title="View Grade 12 Subjects">
                                <i class="ti ti-eye me-1"></i>View Subjects
                            </button>
                        </div>
                        <div class="list-group list-group-flush">
                            @php
                                $colors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'purple'];
                            @endphp
                            
                            @foreach($strandSections as $index => $section)
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
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>

<!-- Hidden data containers for JavaScript -->
<div id="studentAssignmentsData" style="display:none;">@json(session('student_assignments', []))</div>
<div id="savedAdvisersData" style="display:none;">@json($savedAdvisers ?? [])</div>

<!-- Include modals from the main index page -->
@include('admin.section_advisers.partials.section_details_modal')
@include('admin.section_advisers.partials.subjects_modal')
@include('admin.section_advisers.partials.subject_teacher_modal')

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
</style>

<script src="{{ asset('js/section-advisers-common.js') }}"></script>
@endsection
