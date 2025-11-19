@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Assigning List</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Assigning List</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="ti ti-filter me-2"></i>Filter Students</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.assigning-list.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Student name or number..." 
                               value="{{ request('search') }}">
                    </div>

                    <!-- Strand Filter -->
                    <div class="col-md-4">
                        <label class="form-label">Strand</label>
                        <select name="strand" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" {{ request('strand') == 'all' || !request('strand') ? 'selected' : '' }}>All Strands</option>
                            @foreach($strands as $strand)
                                <option value="{{ $strand->code }}" {{ request('strand') == $strand->code ? 'selected' : '' }}>
                                    {{ $strand->code }} - {{ $strand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Grade Level Filter -->
                    <div class="col-md-4">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" {{ request('grade_level') == 'all' || !request('grade_level') ? 'selected' : '' }}>All Grade Levels</option>
                            @foreach($gradeLevels as $level)
                                <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                                    Grade {{ $level }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-info me-2">
                            <i class="ti ti-search me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.assigning-list.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students List Card -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="ti ti-list-check me-2"></i>Students List 
                    <span class="badge bg-primary ms-2">{{ $students->total() }} Total</span>
                </h5>
                <div class="text-muted small">
                    @if(request('strand') && request('strand') !== 'all')
                        Filtered by Strand: <strong>{{ request('strand') }}</strong>
                    @endif
                    @if(request('grade_level') && request('grade_level') !== 'all')
                        | Grade: <strong>{{ request('grade_level') }}</strong>
                    @endif
                </div>
            </div>
            
            <!-- Filter Info Message -->
            @if((request('strand') && request('strand') !== 'all') || (request('grade_level') && request('grade_level') !== 'all'))
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Filter Active:</strong> 
                    Section buttons are now filtered to match your selected 
                    @if(request('strand') && request('strand') !== 'all')
                        <strong>Strand ({{ request('strand') }})</strong>
                    @endif
                    @if((request('strand') && request('strand') !== 'all') && (request('grade_level') && request('grade_level') !== 'all'))
                        and
                    @endif
                    @if(request('grade_level') && request('grade_level') !== 'all')
                        <strong>Grade Level ({{ request('grade_level') }})</strong>
                    @endif
                    criteria.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Section Assignment Controls -->
            <div class="d-flex gap-2 align-items-center flex-wrap mb-3">
                <div class="btn-group" role="group">
                    <input type="checkbox" class="btn-check" id="selectAll" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="selectAll">
                        <i class="ti ti-checkbox me-1"></i>Select All
                    </label>
                </div>
                <button type="button" class="btn btn-success btn-sm ms-auto" id="saveAssignmentsBtn" onclick="saveAllAssignments()">
                    <i class="ti ti-device-floppy me-1"></i>Save Assignments
                </button>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="text-muted small mb-0">Assign to Section:</label>
                    @php
                        $colors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'purple'];
                        $colorIndex = 0;
                    @endphp
                    @forelse($sections as $section)
                        @php
                            $color = $colors[$colorIndex % count($colors)];
                            $colorIndex++;
                            $strandCode = $section->strand ? $section->strand->code : 'N/A';
                            $strandName = $section->strand ? $section->strand->name : 'No Strand';
                            
                            // Count current students in this section
                            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
                            $studentCount = 0;
                            if ($activeYear) {
                                $sectionAssignment = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                                    ->where('section_id', $section->id)
                                    ->first();
                                if ($sectionAssignment) {
                                    $studentCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $sectionAssignment->id)
                                        ->where('academic_year_id', $activeYear->id)
                                        ->count();
                                }
                            }
                            $isFull = $studentCount >= 30;
                        @endphp
                        <button type="button" 
                                class="btn btn-sm text-white section-btn" 
                                style="background-color: #353535;"
                                data-section-id="{{ $section->id }}"
                                data-strand-code="{{ $strandCode }}"
                                data-student-count="{{ $studentCount }}"
                                onclick="assignToSection({{ $section->id }}, '{{ $section->grade }} {{ $section->name }}', '{{ $strandCode }}', '{{ $color }}')"
                                title="{{ $strandName }} - Grade {{ $section->grade }} ({{ $studentCount }}/30 students)"
                                @if($isFull) disabled @endif>
                            <i class="ti ti-users me-1"></i>G{{ $section->grade }} {{ $section->name }}
                            @if($strandCode !== 'N/A')
                                <span class="badge bg-light text-dark ms-1" style="font-size: 0.7rem;">{{ $strandCode }}</span>
                            @endif
                            <span class="badge ms-1 {{ $isFull ? 'bg-danger' : ($studentCount >= 24 ? 'bg-warning' : 'bg-success') }}" style="font-size: 0.7rem;">
                                {{ $studentCount }}/30
                            </span>
                            @if($isFull)
                                <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">FULL</span>
                            @endif
                        </button>
                    @empty
                        <span class="text-muted small">
                            <i class="ti ti-info-circle me-1"></i>
                            @if((request('strand') && request('strand') !== 'all') || (request('grade_level') && request('grade_level') !== 'all'))
                                No sections match the selected filters
                            @else
                                No sections available
                            @endif
                        </span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                </div>
                            </th>
                            <th style="width: 60px;">#</th>
                            <th style="width: 130px;">Student No.</th>
                            <th>Name</th>
                            <th style="width: 100px;">Sex</th>
                            <th>Strand/Program</th>
                            <th>Grade Level</th>
                            <th style="width: 120px;">Section</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input student-checkbox" 
                                               type="checkbox" 
                                               value="{{ $student->id }}" 
                                               data-student-id="{{ $student->id }}"
                                               data-student-name="{{ $student->first_name }} {{ $student->last_name }}"
                                               data-student-no="{{ $student->student_number }}"
                                               data-student-strand="{{ $student->program }}">
                                    </div>
                                </td>
                                <td class="text-center">{{ $students->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary font-monospace">{{ $student->student_number }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            @if($student->profile_picture)
                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                                                     alt="{{ $student->first_name }}" 
                                                     class="rounded-circle">
                                            @else
                                                <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $student->last_name }}, {{ $student->first_name }} 
                                                @if($student->middle_name)
                                                    {{ substr($student->middle_name, 0, 1) }}.
                                                @endif
                                                @if($student->suffix)
                                                    {{ $student->suffix }}
                                                @endif
                                            </div>
                                            @if($student->email)
                                                <small class="text-muted">{{ $student->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(strtolower($student->gender) === 'male')
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ti ti-gender-male me-1"></i>Male
                                        </span>
                                    @elseif(strtolower($student->gender) === 'female')
                                        <span class="badge bg-pink-subtle text-pink" style="background-color: #fce4ec !important; color: #ec407a !important;">
                                            <i class="ti ti-gender-female me-1"></i>Female
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->program)
                                        <span class="badge bg-primary-subtle text-primary">{{ $student->program }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->academic_year)
                                        <span class="badge bg-success-subtle text-success">{{ $student->academic_year }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="section-display-{{ $student->id }} text-muted small">Not assigned</span>
                                </td>
                                <td>
                                    @if($student->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($student->status == 'inactive')
                                        <span class="badge bg-warning">Inactive</span>
                                    @elseif($student->status == 'graduated')
                                        <span class="badge bg-info">Graduated</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.students.show', $student->student_number) }}">
                                                    <i class="ti ti-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.students.edit', $student->student_number) }}">
                                                    <i class="ti ti-edit me-2"></i>Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ti ti-folder-off mb-2" style="font-size: 3rem;"></i>
                                        <p class="mb-0">No students found</p>
                                        @if(request('strand') || request('grade_level') || request('search'))
                                            <small>Try adjusting your filters</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-2">
                <div class="text-muted small">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                </div>
                <nav aria-label="Page navigation">
                    {{ $students->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal for Assignment Confirmation -->
<div class="modal fade" id="assignmentConfirmModal" tabindex="-1" aria-labelledby="assignmentConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="assignmentConfirmModalLabel">
                    <i class="ti ti-device-floppy me-2"></i>
                    Save Assignment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="ti ti-alert-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-3" id="confirmMessage"></p>
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Current Capacity</small>
                                    <h4 class="mb-0" id="currentCapacity">0/30</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">After Assignment</small>
                                    <h4 class="mb-0 text-primary" id="afterCapacity">0/30</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-info" id="confirmAssignmentBtn" data-bs-dismiss="modal">
                    <i class="ti ti-device-floppy me-1"></i>Save Assignment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing Section Students -->
<div class="modal fade" id="sectionStudentsModal" tabindex="-1" aria-labelledby="sectionStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionStudentsModalLabel">
                    <i class="ti ti-users me-2"></i>
                    <span id="modalSectionTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="sectionStudentsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Store section assignments in memory
    // Structure: { 'STRAND-SECTION': [{ id, name, studentNo, program }, ...] }
    const sectionAssignments = {};
    
    // Maximum students per section
    const MAX_STUDENTS_PER_SECTION = 30;

    // Check/Uncheck all checkboxes
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Also handle the Select All button
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('checkAll').checked = this.checked;
    });

    // Assign selected students to a section
    function assignToSection(sectionId, sectionName, strandCode, badgeColor) {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            alert('Please select at least one student to assign to a section.');
            return;
        }

        // Get current section count
        const key = `${strandCode}-${sectionId}`;
        const currentCount = sectionAssignments[key] ? sectionAssignments[key].length : 0;
        
        // Count how many students are not already in this section
        let newStudentsCount = 0;
        checkedBoxes.forEach(checkbox => {
            const studentId = checkbox.dataset.studentId;
            const studentStrand = checkbox.dataset.studentStrand;
            if (!sectionAssignments[key] || !sectionAssignments[key].find(s => s.id === studentId)) {
                newStudentsCount++;
            }
        });
        
        // Check if adding these students would exceed the limit
        if (currentCount + newStudentsCount > MAX_STUDENTS_PER_SECTION) {
            const remaining = MAX_STUDENTS_PER_SECTION - currentCount;
            alert(`Cannot assign students: Section ${sectionName} is full or would exceed maximum capacity.\n\nCurrent: ${currentCount}/${MAX_STUDENTS_PER_SECTION} students\nTrying to add: ${newStudentsCount} students\nRemaining capacity: ${remaining} students`);
            return;
        }

        // Perform assignment directly without confirmation
        const assignmentData = {
            sectionId,
            sectionName,
            strandCode,
            badgeColor,
            checkedBoxes: Array.from(checkedBoxes)
        };
        
        performAssignment(assignmentData);
    }

    function performAssignment(data) {
        const { sectionId, sectionName, strandCode, badgeColor, checkedBoxes } = data;
        const sectionBadgeClass = `bg-${badgeColor}`;

        checkedBoxes.forEach(checkbox => {
            const studentId = checkbox.dataset.studentId;
            const studentName = checkbox.dataset.studentName;
            const studentNo = checkbox.dataset.studentNo;
            const studentStrand = checkbox.dataset.studentStrand;
            const sectionDisplay = document.querySelector(`.section-display-${studentId}`);
            
            // Update display
            if (sectionDisplay) {
                sectionDisplay.innerHTML = `<span class="badge ${sectionBadgeClass}">${sectionName}</span>`;
                sectionDisplay.classList.remove('text-muted', 'small');
                sectionDisplay.dataset.section = sectionId;
                sectionDisplay.dataset.strand = strandCode;
            }

            // Store in memory - USE SECTION'S STRAND CODE, NOT STUDENT'S PROGRAM
            const key = `${strandCode}-${sectionId}`;
            if (!sectionAssignments[key]) {
                sectionAssignments[key] = [];
            }
            
            // Remove student from other sections of same strand
            Object.keys(sectionAssignments).forEach(k => {
                if (k.startsWith(strandCode + '-') && k !== key) {
                    sectionAssignments[k] = sectionAssignments[k].filter(s => s.id !== studentId);
                }
            });
            
            // Add to current section if not already there
            if (!sectionAssignments[key].find(s => s.id === studentId)) {
                sectionAssignments[key].push({
                    id: studentId,
                    name: studentName,
                    studentNo: studentNo,
                    program: studentStrand,
                    sectionId: sectionId,
                    sectionName: sectionName
                });
            }
        });

        // Update section counts
        updateSectionCounts();

        // Uncheck all checkboxes after assignment
        checkedBoxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('checkAll').checked = false;
        document.getElementById('selectAll').checked = false;

        // Show success message
        const successMsg = `Successfully assigned ${checkedBoxes.length} student(s) to ${sectionName}`;
        showAlert(successMsg, 'success');
        
        // Note: Use the "Save Assignments" button to save changes to the database
    }

    // View students in a specific section
    function viewSectionStudents(strandCode, sectionId, sectionName) {
        const key = `${strandCode}-${sectionId}`;
        const students = sectionAssignments[key] || [];
        
        const modalTitle = document.getElementById('modalSectionTitle');
        const studentsList = document.getElementById('sectionStudentsList');
        
        // Build modal title with capacity indicator
        const capacityColor = students.length >= MAX_STUDENTS_PER_SECTION ? 'danger' : (students.length >= MAX_STUDENTS_PER_SECTION * 0.8 ? 'warning' : 'success');
        modalTitle.innerHTML = `${sectionName} 
            <span class="badge bg-${capacityColor}">${students.length}/${MAX_STUDENTS_PER_SECTION} students</span>`;
        
        if (students.length === 0) {
            studentsList.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="ti ti-users-off mb-2" style="font-size: 3rem;"></i>
                    <p class="mb-0">No students assigned to this section yet</p>
                </div>
            `;
        } else {
            let html = '<div class="list-group">';
            students.forEach((student, index) => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">${index + 1}. ${student.name}</div>
                                <small class="text-muted">
                                    <span class="badge bg-secondary">${student.studentNo}</span>
                                    <span class="badge bg-primary-subtle text-primary ms-1">${student.program}</span>
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="removeFromSection('${strandCode}', ${sectionId}, '${student.id}', '${sectionName}')">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            studentsList.innerHTML = html;
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('sectionStudentsModal'));
        modal.show();
    }

    // Remove student from section
    function removeFromSection(strandCode, sectionId, studentId, sectionName) {
        const key = `${strandCode}-${sectionId}`;
        if (sectionAssignments[key]) {
            sectionAssignments[key] = sectionAssignments[key].filter(s => s.id !== studentId);
        }
        
        // Update UI
        const sectionDisplay = document.querySelector(`.section-display-${studentId}`);
        if (sectionDisplay && sectionDisplay.dataset.strand === strandCode && parseInt(sectionDisplay.dataset.section) === sectionId) {
            sectionDisplay.innerHTML = '<span class="text-muted small">Not assigned</span>';
            sectionDisplay.classList.add('text-muted', 'small');
            delete sectionDisplay.dataset.section;
            delete sectionDisplay.dataset.strand;
        }
        
        // Update counts and refresh modal
        updateSectionCounts();
        viewSectionStudents(strandCode, sectionId, sectionName);
        showAlert('Student removed from section', 'info');
    }

    // Update section counts
    function updateSectionCounts() {
        // Update the count badges on section buttons
        document.querySelectorAll('.section-btn').forEach(button => {
            const sectionId = button.dataset.sectionId;
            const strandCode = button.dataset.strandCode;
            const key = `${strandCode}-${sectionId}`;
            
            // Get current count from backend (database count)
            let databaseCount = parseInt(button.dataset.studentCount) || 0;
            
            // Get the count from local assignments (unsaved changes)
            const localCount = sectionAssignments[key] ? sectionAssignments[key].length : 0;
            
            // Use local count if it exists (user has made assignments), otherwise use database count
            const displayCount = localCount > 0 ? localCount : databaseCount;
            
            // Find the count badge and update it
            const countBadge = button.querySelector('.badge:not(.bg-light):not(.bg-danger)');
            if (countBadge) {
                countBadge.textContent = `${displayCount}/${MAX_STUDENTS_PER_SECTION}`;
                
                // Update badge color based on capacity
                countBadge.classList.remove('bg-success', 'bg-warning', 'bg-danger');
                if (displayCount >= MAX_STUDENTS_PER_SECTION) {
                    countBadge.classList.add('bg-danger');
                    button.disabled = true;
                    
                    // Show FULL badge if not exists
                    let fullBadge = button.querySelector('.badge.bg-danger:last-child');
                    if (!fullBadge || !fullBadge.textContent.includes('FULL')) {
                        const newFullBadge = document.createElement('span');
                        newFullBadge.className = 'badge bg-danger ms-1';
                        newFullBadge.style.fontSize = '0.7rem';
                        newFullBadge.textContent = 'FULL';
                        button.appendChild(newFullBadge);
                    }
                } else if (displayCount >= MAX_STUDENTS_PER_SECTION * 0.8) {
                    countBadge.classList.add('bg-warning');
                    button.disabled = false;
                    
                    // Remove FULL badge if exists
                    const fullBadge = button.querySelector('.badge.bg-danger:last-child');
                    if (fullBadge && fullBadge.textContent.includes('FULL')) {
                        fullBadge.remove();
                    }
                } else {
                    countBadge.classList.add('bg-success');
                    button.disabled = false;
                    
                    // Remove FULL badge if exists
                    const fullBadge = button.querySelector('.badge.bg-danger:last-child');
                    if (fullBadge && fullBadge.textContent.includes('FULL')) {
                        fullBadge.remove();
                    }
                }
            }
        });
        
        console.log('Section assignments updated. Local changes:', Object.keys(sectionAssignments).length, 'sections');
    }

    // Show alert message
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.content').insertBefore(alertDiv, document.querySelector('.row'));
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Save all student assignments to the server
    async function saveAllAssignments() {
        // Collect all student assignments
        const assignments = [];
        for (const key in sectionAssignments) {
            const students = sectionAssignments[key];
            
            students.forEach(student => {
                assignments.push({
                    student_id: student.id,
                    strand_code: student.program,
                    section_id: student.sectionId
                });
            });
        }
        
        if (assignments.length === 0) {
            console.warn('No assignments to save');
            showAlert('⚠️ No student assignments to save. Please select students and assign them to sections first.', 'warning');
            return;
        }
        
        console.log('Saving assignments:', assignments);
        console.log('Total assignments to save:', assignments.length);
        
        // Show loading state
        const saveButton = document.getElementById('saveAssignmentsBtn');
        let originalText = '<i class="ti ti-device-floppy me-1"></i>Save Assignments';
        
        if (saveButton) {
            originalText = saveButton.innerHTML;
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Saving...';
        }
        
        try {
            const response = await fetch('{{ route('admin.assigning-list.save-assignments') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ assignments })
            });
            
            const data = await response.json();
            console.log('Save response:', data);
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            if (response.ok && data.success) {
                showAlert(`✅ Successfully saved ${data.count} student assignment(s) to database! The assignments will now appear in the Section & Advisers page.`, 'success');
                // Clear the local assignments after successful save
                Object.keys(sectionAssignments).forEach(key => delete sectionAssignments[key]);
                // Reload page to show fresh data from database
                setTimeout(() => window.location.reload(), 2000);
            } else {
                console.error('Save failed:', data);
                console.error('Full response:', response);
                const errorMsg = data.message || 'Failed to save assignments. Please try again.';
                const errorDetails = data.errors ? '\nDetails: ' + JSON.stringify(data.errors) : '';
                showAlert(`❌ ${errorMsg}${errorDetails}`, 'danger');
            }
        } catch (error) {
            console.error('Error saving assignments:', error);
            console.error('Error stack:', error.stack);
            showAlert('❌ An error occurred while saving. Check the console for details.', 'danger');
        } finally {
            // Restore button state
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = originalText;
            }
        }
    }

    // Load saved assignments from session and database
    function loadSavedAssignments() {
        // First, load from database (existing enrollments)
        const existingAssignments = @json($existingAssignments ?? []);
        const savedAssignments = @json(session('student_assignments', []));
        
        // Combine both sources, prioritizing session data
        const allAssignments = [...existingAssignments];
        savedAssignments.forEach(sessionAssignment => {
            const exists = allAssignments.find(a => a.student_id === sessionAssignment.student_id);
            if (!exists) {
                allAssignments.push(sessionAssignment);
            }
        });
        
        const sectionsData = @json($sections);
        
        // Create a map of section IDs to section info for easy lookup
        const sectionsMap = {};
        sectionsData.forEach(section => {
            sectionsMap[section.id] = {
                name: `${section.grade} ${section.name}`,
                strandCode: section.strand ? section.strand.code : 'N/A'
            };
        });
        
        if (allAssignments.length > 0) {
            // Group assignments by strand-section
            allAssignments.forEach(assignment => {
                const key = `${assignment.strand_code}-${assignment.section_id}`;
                
                if (!sectionAssignments[key]) {
                    sectionAssignments[key] = [];
                }
                
                // Add student if not already in the section
                if (!sectionAssignments[key].find(s => s.id === assignment.student_id)) {
                    // Find student data from the page
                    const studentCheckbox = document.querySelector(`.student-checkbox[data-student-id="${assignment.student_id}"]`);
                    if (studentCheckbox) {
                        const sectionInfo = sectionsMap[assignment.section_id];
                        
                        sectionAssignments[key].push({
                            id: assignment.student_id,
                            name: studentCheckbox.dataset.studentName,
                            studentNo: studentCheckbox.dataset.studentNo,
                            program: studentCheckbox.dataset.studentStrand,
                            sectionId: assignment.section_id,
                            sectionName: sectionInfo ? sectionInfo.name : 'Unknown Section'
                        });
                        
                        // Update display on the page
                        const sectionDisplay = document.querySelector(`.section-display-${assignment.student_id}`);
                        if (sectionDisplay && sectionInfo) {
                            // Determine badge color based on section index (similar to button colors)
                            const colors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'purple'];
                            const sectionIndex = sectionsData.findIndex(s => s.id === assignment.section_id);
                            const badgeClass = `bg-${colors[sectionIndex % colors.length]}`;
                            
                            sectionDisplay.innerHTML = `<span class="badge ${badgeClass}">${sectionInfo.name}</span>`;
                            sectionDisplay.classList.remove('text-muted', 'small');
                            sectionDisplay.dataset.section = assignment.section_id;
                            sectionDisplay.dataset.strand = assignment.strand_code;
                        }
                    }
                }
            });
            
            // Update section counts
            updateSectionCounts();
            
            console.log('Loaded existing and saved assignments:', allAssignments.length);
        }
    }

    // Load saved assignments when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadSavedAssignments();
    });
</script>

<style>
    /* Custom pagination styling */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    
    .pagination .page-item {
        margin: 0;
    }
    
    .pagination .page-link {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        color: #6c757d;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        min-width: 38px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #adb5bd;
        cursor: not-allowed;
    }
    
    .pagination .page-link:hover:not(.disabled) {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #495057;
    }
    
    .pagination .page-item.active .page-link:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    /* Responsive pagination */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.8125rem;
            min-width: 32px;
        }
        
        .pagination {
            gap: 2px;
        }
    }

    /* Checkbox and row selection styling */
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .student-checkbox:checked ~ * {
        background-color: #f8f9fa;
    }

    tr:has(.student-checkbox:checked) {
        background-color: #e7f3ff !important;
    }

    /* Section button hover effects */
    .btn-outline-success:hover,
    .btn-outline-info:hover,
    .btn-outline-warning:hover,
    .btn-outline-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Smooth transitions */
    .badge, .btn {
        transition: all 0.2s ease;
    }

    /* Section overview cards */
    .card.shadow-sm {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card.shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
    }

    .list-group-item:first-child {
        border-top: none;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    /* Modal improvements */
    .modal-body .list-group-item {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }

    .modal-body .list-group-item:last-child {
        margin-bottom: 0;
    }
    
    /* Loading spinner animation */
    .ti-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Custom purple badge color */
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    
    .btn-outline-purple:hover {
        color: #fff;
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
        color: white !important;
    }
</style>

@endsection
