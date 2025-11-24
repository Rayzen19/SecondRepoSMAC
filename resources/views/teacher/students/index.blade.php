@extends('teacher.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <h3 class="page-title">My Students</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Students</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h5 class="mb-0">
                            <i class="ti ti-users me-2"></i>Student List
                            <span class="badge bg-primary ms-2">{{ $totalStudents }}</span>
                        </h5>
                        <small class="text-muted" id="selectedCount" style="display: none;">
                            <span id="selectedNumber">0</span> selected
                        </small>
                    </div>
                    <div class="col-md-9">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <form method="GET" action="{{ route('teacher.students.index') }}" class="row g-2" id="filterForm">
                                    <div class="col-md-2">
                                        <select name="year_level" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Year Levels</option>
                                            @foreach($yearLevels as $level)
                                                <option value="{{ $level }}" {{ $selectedYearLevel == $level ? 'selected' : '' }}>
                                                    Grade {{ $level }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="strand" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Strands</option>
                                            @foreach($strands as $strandId => $strandCode)
                                                <option value="{{ $strandId }}" {{ $selectedStrand == $strandId ? 'selected' : '' }}>
                                                    {{ $strandCode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="section" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Sections</option>
                                            @foreach($sections as $sectionId => $sectionName)
                                                <option value="{{ $sectionId }}" {{ $selectedSection == $sectionId ? 'selected' : '' }}>
                                                    {{ $sectionName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="subject" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Subjects</option>
                                            @foreach($subjects as $subjectId => $subjectName)
                                                <option value="{{ $subjectId }}" {{ $selectedSubject == $subjectId ? 'selected' : '' }}>
                                                    {{ $subjectName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control form-control-sm" 
                                               placeholder="Search..." 
                                               value="{{ $search }}">
                                    </div>
                                    <div class="col-md-1">
                                        @if($selectedYearLevel || $selectedStrand || $selectedSection || $selectedSubject || $search)
                                            <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-outline-secondary w-100" title="Clear Filters">
                                                <i class="ti ti-x"></i>
                                            </a>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-info w-100" title="Search">
                                                <i class="ti ti-search"></i>
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary assessment-btn" onclick="addAssessment('WW')" disabled id="btnAddWW" title="Add Written Works - Select students first">
                                        <small>Add WW</small>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success assessment-btn" onclick="addAssessment('PT')" disabled id="btnAddPT" title="Add Performance Task - Select students first">
                                        <small>Add PT</small>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning assessment-btn" onclick="addAssessment('QA')" disabled id="btnAddQA" title="Add Quarterly Assessment - Select students first">
                                        <small>Add QA</small>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle w-100" type="button" id="bulkActions" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                        <i class="ti ti-dots me-1"></i>More
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="bulkActions">
                                        <li><a class="dropdown-item" href="#" onclick="exportSelected(); return false;">
                                            <i class="ti ti-file-export me-2"></i>Export Selected
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="printSelected(); return false;">
                                            <i class="ti ti-printer me-2"></i>Print List
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="clearSelection(); return false;">
                                            <i class="ti ti-x me-2"></i>Clear Selection
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="studentsTable">
                            <thead>
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th width="5%">#</th>
                                    <th width="10%">Student No.</th>
                                    <th width="20%">Name</th>
                                    <th width="8%">Gender</th>
                                    <th width="8%">Year Level</th>
                                    <th width="8%">Strand</th>
                                    <th width="8%">Section</th>
                                    <th width="20%">Email</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input student-checkbox" 
                                               value="{{ $student['id'] }}" 
                                               data-student-name="{{ $student['full_name'] }}"
                                               data-student-number="{{ $student['student_number'] }}">
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $student['student_number'] }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-title rounded-circle bg-primary text-white">
                                                    {{ strtoupper(substr($student['first_name'], 0, 1)) }}{{ strtoupper(substr($student['last_name'], 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $student['full_name'] }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(strtolower($student['gender']) == 'male')
                                            <span class="badge bg-info">
                                                <i class="ti ti-user me-1"></i>Male
                                            </span>
                                        @elseif(strtolower($student['gender']) == 'female')
                                            <span class="badge bg-danger">
                                                <i class="ti ti-user me-1"></i>Female
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Grade {{ $student['grade_level'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $student['strand'] }}</span>
                                    </td>
                                    <td>{{ $student['section'] }}</td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="ti ti-mail me-1"></i>{{ $student['email'] }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($student['assignment_id'])
                                            <a href="{{ route('teacher.class-records.show', ['assignment' => $student['assignment_id']]) }}" 
                                               class="btn btn-sm btn-warning text-white" 
                                               title="View Class Records">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="No records available">
                                                <i class="ti ti-eye-off me-1"></i>No Records
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="mb-0">{{ $totalStudents }}</h4>
                                        <small>Total Students</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="mb-0">{{ $students->where('gender', 'Male')->count() }}</h4>
                                        <small>Male Students</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="mb-0">{{ $students->where('gender', 'Female')->count() }}</h4>
                                        <small>Female Students</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="mb-0">{{ $yearLevels->count() }}</h4>
                                        <small>Year Levels</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-users-off" style="font-size: 64px; color: #ccc;"></i>
                        <h4 class="mt-3 text-muted">No Students Found</h4>
                        @if($selectedYearLevel || $selectedStrand || $selectedSection || $selectedSubject || $search)
                            <p class="text-muted">No students match the selected filters.</p>
                            <a href="{{ route('teacher.students.index') }}" class="btn btn-info">
                                <i class="ti ti-refresh me-1"></i>Clear Filters
                            </a>
                        @else
                            <p class="text-muted">You don't have any students assigned yet.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Assessment Modal -->
<div class="modal fade" id="addAssessmentModal" tabindex="-1" aria-labelledby="addAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addAssessmentModalLabel">
                    Add <span id="assessmentType"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="assessmentForm">
                    <!-- Assessment Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" class="form-control" id="assessmentTitle" required placeholder="Assessment name (e.g. WW1)">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="assessmentDescription" rows="3" placeholder="Optional description"></textarea>
                    </div>

                    <!-- Date, Over (Max Score), Grade Level -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="assessmentDueDate" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Over (Max Score)</label>
                            <input type="number" class="form-control" id="assessmentMaxScore" required min="1" value="100" placeholder="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Grade Level</label>
                            <select class="form-select" id="assessmentGradeLevel" required>
                                <option value="">Select Grade</option>
                                <option value="11">Grade 11</option>
                                <option value="12">Grade 12</option>
                            </select>
                        </div>
                    </div>

                    <!-- Term -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Term</label>
                        <select class="form-select" id="assessmentQuarter" required>
                            <option value="">Select Term</option>
                            <option value="1">First Sem</option>
                            <option value="2">Second Sem</option>
                        </select>
                    </div>

                    <!-- Selected Students Section -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selected Students (<span id="selectedStudentsCount">0</span>)</label>
                        <div class="border rounded p-3 bg-light" id="selectedStudentsList" style="max-height: 150px; overflow-y: auto;">
                            <!-- Students will be listed here -->
                        </div>
                    </div>

                    <input type="hidden" id="assessmentTypeInput">
                    <input type="hidden" id="selectedStudentIds">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="submitAssessment()">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 12px;
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Actions column */
.table td:last-child {
    white-space: nowrap;
}

.btn-sm i {
    font-size: 14px;
}

/* Assessment button styles */
.assessment-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    pointer-events: none;
}

.assessment-btn:not(:disabled) {
    cursor: pointer !important;
    transition: all 0.3s ease;
}

.assessment-btn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Select All functionality
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.student-checkbox').prop('checked', isChecked);
        updateSelectedCount();
        toggleBulkActionsButton();
    });

    // Individual checkbox change
    $('.student-checkbox').on('change', function() {
        updateSelectAllCheckbox();
        updateSelectedCount();
        toggleBulkActionsButton();
    });

    // Update "Select All" checkbox state
    function updateSelectAllCheckbox() {
        const total = $('.student-checkbox').length;
        const checked = $('.student-checkbox:checked').length;
        $('#selectAll').prop('checked', total > 0 && checked === total);
    }

    // Update selected count display
    function updateSelectedCount() {
        const count = $('.student-checkbox:checked').length;
        $('#selectedNumber').text(count);
        if (count > 0) {
            $('#selectedCount').show();
        } else {
            $('#selectedCount').hide();
        }
    }

    // Enable/disable bulk actions button
    function toggleBulkActionsButton() {
        const count = $('.student-checkbox:checked').length;
        console.log('Selected students:', count); // Debug log
        $('#bulkActions').prop('disabled', count === 0);
        
        // Enable/disable assessment buttons with visual feedback
        if (count > 0) {
            $('#btnAddWW, #btnAddPT, #btnAddQA')
                .prop('disabled', false)
                .removeClass('disabled')
                .css('cursor', 'pointer');
        } else {
            $('#btnAddWW, #btnAddPT, #btnAddQA')
                .prop('disabled', true)
                .addClass('disabled')
                .css('cursor', 'not-allowed');
        }
    }

    // Initialize DataTable if available
    if ($.fn.DataTable) {
        $('#studentsTable').DataTable({
            "paging": true,
            "pageLength": 25,
            "ordering": true,
            "info": true,
            "searching": false, // We have our own search
            "order": [[2, 'asc']], // Sort by student number
            "columnDefs": [
                { "orderable": false, "targets": 0 } // Disable sorting on checkbox column
            ],
            "language": {
                "lengthMenu": "Show _MENU_ students per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "No students available",
                "infoFiltered": "(filtered from _MAX_ total students)"
            },
            "drawCallback": function() {
                // Reattach event handlers after table redraw
                $('.student-checkbox').off('change').on('change', function() {
                    updateSelectAllCheckbox();
                    updateSelectedCount();
                    toggleBulkActionsButton();
                });
            }
        });
    }
});

// Clear selection
function clearSelection() {
    $('.student-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    $('#selectedCount').hide();
    $('#bulkActions').prop('disabled', true);
    $('#btnAddWW').prop('disabled', true);
    $('#btnAddPT').prop('disabled', true);
    $('#btnAddQA').prop('disabled', true);
}

// Add Assessment
function addAssessment(type) {
    const selected = [];
    $('.student-checkbox:checked').each(function() {
        selected.push({
            id: $(this).val(),
            name: $(this).data('student-name'),
            number: $(this).data('student-number')
        });
    });

    if (selected.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    // Set assessment type
    let typeName = '';
    switch(type) {
        case 'WW':
            typeName = 'Written Work';
            break;
        case 'PT':
            typeName = 'Performance Task';
            break;
        case 'QA':
            typeName = 'Quarterly Assessment';
            break;
    }

    $('#assessmentType').text(typeName);
    $('#assessmentTypeInput').val(type);
    $('#selectedStudentsCount').text(selected.length);

    // Build student list with names
    let studentListHtml = '<div class="row g-2">';
    selected.forEach((student, index) => {
        studentListHtml += `
            <div class="col-12">
                <div class="d-flex align-items-center p-2 bg-white rounded border">
                    <span class="badge bg-primary me-2">${index + 1}</span>
                    <div class="flex-grow-1">
                        <strong class="d-block">${student.name}</strong>
                        <small class="text-muted">${student.number}</small>
                    </div>
                </div>
            </div>
        `;
    });
    studentListHtml += '</div>';

    $('#selectedStudentsList').html(studentListHtml);
    $('#selectedStudentIds').val(selected.map(s => s.id).join(','));

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addAssessmentModal'));
    modal.show();
}

// Submit Assessment
function submitAssessment() {
    const form = document.getElementById('assessmentForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const studentIdsArray = $('#selectedStudentIds').val().split(',').filter(id => id.trim() !== '');
    
    // Build FormData with array support
    const formData = new FormData();
    formData.append('type', $('#assessmentTypeInput').val());
    formData.append('title', $('#assessmentTitle').val());
    formData.append('term', $('#assessmentQuarter').val());
    formData.append('grade_level', $('#assessmentGradeLevel').val());
    formData.append('max_score', $('#assessmentMaxScore').val());
    formData.append('description', $('#assessmentDescription').val() || '');
    formData.append('date', $('#assessmentDueDate').val());
    
    // Add each student ID as array element
    studentIdsArray.forEach(id => {
        formData.append('student_ids[]', id.trim());
    });

    // Show loading
    const submitBtn = document.querySelector('#addAssessmentModal .btn-info');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';

    // Send AJAX request to backend
    $.ajax({
        url: '{{ route("teacher.assessments.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            alert(response.message || `Successfully created assessment for ${studentIdsArray.length} student(s)!`);
            
            // Reset form
            form.reset();
            $('#selectedStudentsList').html('');
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addAssessmentModal')).hide();
            
            // Clear selection
            clearSelection();
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        },
        error: function(xhr) {
            let errorMessage = 'Failed to create assessment. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Export selected students
function exportSelected() {
    const selected = [];
    $('.student-checkbox:checked').each(function() {
        selected.push({
            id: $(this).val(),
            name: $(this).data('student-name'),
            number: $(this).data('student-number')
        });
    });

    if (selected.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    // Create CSV content
    let csv = 'Student Number,Name,Email,Gender,Year Level,Strand,Section\n';
    
    selected.forEach(student => {
        const row = $('input[value="' + student.id + '"]').closest('tr');
        const studentNumber = row.find('td:eq(2)').text().trim();
        const name = student.name;
        const email = row.find('td:eq(8)').text().trim();
        const gender = row.find('td:eq(4)').text().trim();
        const yearLevel = row.find('td:eq(5)').text().trim();
        const strand = row.find('td:eq(6)').text().trim();
        const section = row.find('td:eq(7)').text().trim();
        
        csv += `"${studentNumber}","${name}","${email}","${gender}","${yearLevel}","${strand}","${section}"\n`;
    });

    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students_' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Print selected students
function printSelected() {
    const selected = [];
    $('.student-checkbox:checked').each(function() {
        const row = $(this).closest('tr');
        selected.push({
            number: row.find('td:eq(1)').text().trim(),
            studentNumber: row.find('td:eq(2)').text().trim(),
            name: $(this).data('student-name'),
            gender: row.find('td:eq(4)').text().trim(),
            yearLevel: row.find('td:eq(5)').text().trim(),
            strand: row.find('td:eq(6)').text().trim(),
            section: row.find('td:eq(7)').text().trim(),
            email: row.find('td:eq(8)').text().trim()
        });
    });

    if (selected.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    // Create print window
    const printWindow = window.open('', '', 'width=800,height=600');
    let content = `
        <html>
        <head>
            <title>Student List</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h2 { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                @media print {
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <h2>Student List</h2>
            <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            <p><strong>Total Students:</strong> ${selected.length}</p>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Number</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Year Level</th>
                        <th>Strand</th>
                        <th>Section</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
    `;

    selected.forEach((student, index) => {
        content += `
            <tr>
                <td>${index + 1}</td>
                <td>${student.studentNumber}</td>
                <td>${student.name}</td>
                <td>${student.gender}</td>
                <td>${student.yearLevel}</td>
                <td>${student.strand}</td>
                <td>${student.section}</td>
                <td>${student.email}</td>
            </tr>
        `;
    });

    content += `
                </tbody>
            </table>
            <br>
            <button onclick="window.print()">Print</button>
            <button onclick="window.close()">Close</button>
        </body>
        </html>
    `;

    printWindow.document.write(content);
    printWindow.document.close();
}
</script>
@endpush
@endsection
