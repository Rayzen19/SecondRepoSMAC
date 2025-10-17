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
                        <div class="list-group list-group-flush">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="list-group-item px-2 py-2 section-item" data-section="{{ $i }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge 
                                                @if($i == 1) bg-success
                                                @elseif($i == 2) bg-info
                                                @elseif($i == 3) bg-warning
                                                @else bg-danger
                                                @endif">
                                                Section {{ $i }}
                                            </span>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary ms-2" 
                                                    onclick="viewSectionDetails('{{ $strand->code }}', {{ $i }})"
                                                    title="View students">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted student-count-{{ $strand->code }}-{{ $i }}">
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
                                                data-section="{{ $i }}"
                                                onchange="assignAdviser('{{ $strand->code }}', {{ $i }}, this)">
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
                                        <small class="text-muted adviser-display-{{ $strand->code }}-{{ $i }}" style="display: none;">
                                            <i class="ti ti-check text-success"></i>
                                            <span class="adviser-name"></span>
                                        </small>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

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

<script>
    // Store adviser assignments
    let adviserAssignments = {};
    
    // Store student assignments from session
    let studentAssignments = @json(session('student_assignments', []));

    // Filter sections by grade level
    function filterSections() {
        const gradeLevel = document.getElementById('gradeLevelFilter').value;
        const strandCards = document.querySelectorAll('.strand-card');
        
        strandCards.forEach(card => {
            const strand = card.dataset.strand;
            
            if (gradeLevel === 'all') {
                card.style.display = 'block';
            } else {
                // Show sections based on grade level logic
                // Typically Grade 11 = Sections 1-2, Grade 12 = Sections 3-4
                // But you can adjust this logic based on your needs
                const sections = card.querySelectorAll('.section-item');
                let hasVisibleSections = false;
                
                sections.forEach((section, index) => {
                    const sectionNum = parseInt(section.dataset.section);
                    
                    if (gradeLevel === '11' && (sectionNum === 1 || sectionNum === 2)) {
                        section.style.display = 'block';
                        hasVisibleSections = true;
                    } else if (gradeLevel === '12' && (sectionNum === 3 || sectionNum === 4)) {
                        section.style.display = 'block';
                        hasVisibleSections = true;
                    } else {
                        section.style.display = 'none';
                    }
                });
                
                card.style.display = hasVisibleSections ? 'block' : 'none';
            }
        });
    }

    // View section details with students and adviser
    function viewSectionDetails(strandCode, sectionNumber) {
        // Get adviser info
        const adviserKey = `${strandCode}-${sectionNumber}`;
        const adviser = adviserAssignments[adviserKey];
        
        // Update modal title
        document.getElementById('modalSectionTitle').textContent = 
            `${strandCode} - Section ${sectionNumber}`;
        
        // Update adviser info
        const adviserInfo = document.getElementById('adviserInfo');
        const adviserName = document.getElementById('adviserName');
        
        if (adviser && adviser.teacherName) {
            adviserInfo.style.display = 'block';
            adviserInfo.className = 'alert alert-success mb-3';
            adviserName.textContent = adviser.teacherName;
        } else {
            adviserInfo.style.display = 'block';
            adviserInfo.className = 'alert alert-warning mb-3';
            adviserName.textContent = 'No adviser assigned';
        }
        
        // Get students for this section (coerce types to avoid strict mismatch)
        const secNum = parseInt(sectionNumber, 10);
        const students = (studentAssignments || []).filter(a => 
            String(a.strand_code) === String(strandCode) && 
            parseInt(a.section_number, 10) === secNum
        );
        
        // Display students list
        const studentsList = document.getElementById('studentsList');

        if (students.length === 0) {
            studentsList.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="ti ti-users-off mb-2" style="font-size: 3rem;"></i>
                    <p class="mb-0">No students assigned to this section yet</p>
                    <small>Go to Assigning List to assign students</small>
                </div>
            `;
        } else {
            // Show loading state immediately
            studentsList.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="ti ti-loader ti-spin mb-2" style="font-size: 3rem;"></i>
                    <p>Loading ${students.length} student(s)...</p>
                </div>
            `;

            // Fetch student details via AJAX
            const ids = students.map(s => s.student_id);
            fetchStudentDetails(ids)
                .then(studentDetails => {
                    // Fallback if no details were returned
                    if (!Array.isArray(studentDetails) || studentDetails.length === 0) {
                        studentsList.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-circle me-2"></i>
                                No student records returned for IDs: <code>${ids.join(', ')}</code>.
                                <br><small>Make sure assignments were saved and the IDs exist.</small>
                            </div>
                        `;
                        return;
                    }

                    let html = '<div class="list-group">';
                    students.forEach((assignment, index) => {
                        // Normalize id comparison (string vs number)
                        const student = studentDetails.find(s => String(s.id) === String(assignment.student_id));
                        if (student) {
                            html += `
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">${index + 1}. ${student.first_name} ${student.last_name}</div>
                                            <small class="text-muted">
                                                <span class="badge bg-secondary">${student.student_number}</span>
                                                <span class="badge bg-primary-subtle text-primary ms-1">${student.program}</span>
                                                <span class="badge bg-info-subtle text-info ms-1">${student.academic_year}</span>
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-3" title="Remove from section" onclick="removeStudentFromSection('${strandCode}', ${secNum}, ${student.id})">
                                            <i class="ti ti-user-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    html += '</div>';
                    studentsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching student details:', error);
                    studentsList.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle me-2"></i>
                            Error loading student details. Please try again.<br>
                            <small>${error?.message || ''}</small>
                        </div>
                    `;
                });
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('sectionDetailsModal'));
        modal.show();
    }

    // Fetch student details from server
    async function fetchStudentDetails(studentIds) {
        const response = await fetch('{{ route('admin.section-advisers.get-students') }}', {
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
    function updateStudentCounts() {
        // Reset all counts
        document.querySelectorAll('[class*="student-count-"]').forEach(el => {
            el.textContent = '0 students';
        });

        if (!Array.isArray(studentAssignments)) {
            return;
        }
        
        // Count students per section (coerce section number to integer)
        studentAssignments.forEach(assignment => {
            const sec = parseInt(assignment.section_number, 10);
            const countEl = document.querySelector(`.student-count-${assignment.strand_code}-${sec}`);
            if (countEl) {
                const currentCount = parseInt(countEl.textContent) || 0;
                const next = currentCount + 1;
                countEl.textContent = `${next} student${next !== 1 ? 's' : ''}`;
            }
        });
    }

    // Remove a student from a section and update session/state
    async function removeStudentFromSection(strandCode, sectionNumber, studentId) {
        if (!confirm('Remove this student from the section?')) return;

        try {
            const response = await fetch('{{ route('admin.section-advisers.remove-student') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    strand_code: strandCode,
                    section_number: sectionNumber,
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
                    parseInt(a.section_number, 10) === parseInt(sectionNumber, 10) &&
                    String(a.student_id) === String(studentId)
                ));
            }

            // Refresh counts and current modal view
            updateStudentCounts();
            // Re-open the current modal content by re-calling viewSectionDetails
            viewSectionDetails(strandCode, sectionNumber);

            showAlert('Student removed from section.', 'success');
        } catch (e) {
            console.error(e);
            showAlert('Could not remove student. Please try again.', 'danger');
        }
    }

    // Load saved advisers on page load
    function loadSavedAdvisers() {
        const savedAdvisers = @json($savedAdvisers ?? []);
        
        if (savedAdvisers && savedAdvisers.length > 0) {
            savedAdvisers.forEach(adviser => {
                const key = `${adviser.strand_code}-${adviser.section_number}`;
                const selectElement = document.querySelector(`.adviser-select[data-strand="${adviser.strand_code}"][data-section="${adviser.section_number}"]`);
                
                if (selectElement) {
                    selectElement.value = adviser.teacher_id;
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const teacherName = selectedOption.dataset.teacherName;
                    
                    // Store in memory
                    adviserAssignments[key] = {
                        teacherId: adviser.teacher_id,
                        teacherName: teacherName
                    };
                    
                    // Update display
                    const adviserDisplay = document.querySelector(`.adviser-display-${adviser.strand_code}-${adviser.section_number}`);
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
    function assignAdviser(strandCode, sectionNumber, selectElement) {
        const teacherId = selectElement.value;
        const key = `${strandCode}-${sectionNumber}`;
        
        if (teacherId) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const teacherName = selectedOption.dataset.teacherName;
            
            // Store adviser assignment
            adviserAssignments[key] = {
                teacherId: teacherId,
                teacherName: teacherName
            };
            
            // Update display
            const adviserDisplay = document.querySelector(`.adviser-display-${strandCode}-${sectionNumber}`);
            if (adviserDisplay) {
                adviserDisplay.style.display = 'block';
                adviserDisplay.querySelector('.adviser-name').textContent = teacherName;
            }
            
            showAlert(`${teacherName} assigned as adviser for ${strandCode} - Section ${sectionNumber}`, 'success');
        } else {
            // Remove adviser assignment
            delete adviserAssignments[key];
            
            const adviserDisplay = document.querySelector(`.adviser-display-${strandCode}-${sectionNumber}`);
            if (adviserDisplay) {
                adviserDisplay.style.display = 'none';
            }
            
            showAlert(`Adviser removed from ${strandCode} - Section ${sectionNumber}`, 'info');
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
            const [strandCode, sectionNumber] = key.split('-');
            advisers.push({
                strand_code: strandCode,
                section_number: parseInt(sectionNumber),
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
            const response = await fetch('{{ route('admin.section-advisers.save-advisers') }}', {
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
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Load saved advisers on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSavedAdvisers();
        updateStudentCounts(); // Update student counts on page load
    });
</script>
@endsection
