@extends('teacher.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">All Subject Assignments</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('teacher.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('teacher.profile.show') }}">Profile</a>
                </li>
                <li class="breadcrumb-item active">All Subjects</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <a href="{{ route('teacher.profile.show') }}" class="btn btn-outline-primary">
            <i class="ti ti-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Teacher Info Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-xxl me-3">
                    @if($teacher->profile_picture)
                        <img src="{{ Storage::url($teacher->profile_picture) }}" alt="{{ $teacher->first_name }}" class="rounded-circle">
                    @else
                        <div class="avatar-title bg-primary text-white rounded-circle fs-1">
                            {{ strtoupper(substr($teacher->first_name, 0, 1)) }}{{ strtoupper(substr($teacher->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="mb-1">{{ $teacher->last_name }}, {{ $teacher->first_name }} {{ $teacher->middle_name }}</h4>
                    <p class="text-muted mb-0">
                        <i class="ti ti-id me-1"></i>{{ $teacher->employee_number }}
                        <span class="mx-2">|</span>
                        <i class="ti ti-building me-1"></i>{{ $teacher->department }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($assignmentsByYear->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="ti ti-info-circle me-2"></i>
                    No subject assignments yet. Once an admin assigns you to a subject, it will appear here.
                </div>
            </div>
        </div>
    @else
        @foreach($assignmentsByYear as $yearId => $assignments)
            @php
                $academicYear = $assignments->first()->academicYear;
            @endphp
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-calendar me-2"></i>{{ $academicYear->name ?? 'Academic Year' }}
                        <span class="badge bg-white text-primary ms-2">{{ $assignments->count() }} Subject{{ $assignments->count() > 1 ? 's' : '' }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 25%;">Subject</th>
                                    <th style="width: 15%;">Strand</th>
                                    <th style="width: 15%;">Section</th>
                                    <th style="width: 10%;">Grade</th>
                                    <th style="width: 10%;">Students</th>
                                    <th style="width: 20%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $index => $assignment)
                                    @php
                                        // Get section information - try multiple sources
                                        $sectionName = data_get($assignment, 'sectionAssignment.section.name');
                                        $sectionGrade = data_get($assignment, 'sectionAssignment.section.grade');
                                        
                                        // Debug: Log what we have
                                        $debugSectionId = data_get($assignment, 'academic_year_strand_section_id');
                                        $debugSectionAssignment = $assignment->sectionAssignment ?? null;
                                        
                                        // Fallback to student enrollment data if section not directly assigned
                                        if (!$sectionName && $assignment->subjectEnrollments->isNotEmpty()) {
                                            // Try to get section from first enrolled student
                                            $sectionName = data_get($assignment, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.name');
                                            $sectionGrade = data_get($assignment, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.grade');
                                        }
                                        
                                        // If still no section, check if there's a unique section for this strand+grade+year
                                        if (!$sectionName && !$sectionGrade && $assignment->strand) {
                                            // Try to find sections from AcademicYearStrandSection for this assignment
                                            $possibleSections = \App\Models\AcademicYearStrandSection::where('academic_year_id', $assignment->academic_year_id)
                                                ->where('strand_id', $assignment->strand_id)
                                                ->with('section')
                                                ->get();
                                            
                                            // If there's only one section for this strand/year combo, use it
                                            if ($possibleSections->count() === 1) {
                                                $sectionName = $possibleSections->first()->section->name ?? null;
                                                $sectionGrade = $possibleSections->first()->section->grade ?? null;
                                            }
                                        }
                                        
                                        // If still no grade, try to infer from strand_subjects table via grade_level
                                        if (!$sectionGrade && $assignment->strand && $assignment->subject) {
                                            $strandSubject = \App\Models\StrandSubject::where('strand_id', $assignment->strand_id)
                                                ->where('subject_id', $assignment->subject_id)
                                                ->first();
                                            if ($strandSubject && isset($strandSubject->grade_level)) {
                                                $sectionGrade = $strandSubject->grade_level;
                                            }
                                        }
                                        
                                        // Count students
                                        $studentCount = $assignment->subjectEnrollments->filter(function($se) {
                                            return data_get($se, 'studentEnrollment.student');
                                        })->count();
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ data_get($assignment, 'subject.name', 'N/A') }}</strong>
                                                @if(data_get($assignment, 'subject.code'))
                                                    <small class="text-muted">{{ data_get($assignment, 'subject.code') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ data_get($assignment, 'strand.code', 'N/A') }}
                                            </span>
                                            <div class="small text-muted">{{ data_get($assignment, 'strand.name', 'N/A') }}</div>
                                        </td>
                                        <td>
                                            @if($sectionName)
                                                <span class="badge bg-info text-dark">
                                                    {{ $sectionName }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="ti ti-layout-grid me-1"></i>All Sections
                                                </span>
                                                {{-- Debug info --}}
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                    (ID: {{ $debugSectionId ?? 'NULL' }} | 
                                                    SA: {{ $debugSectionAssignment ? 'Yes' : 'No' }} | 
                                                    Students: {{ $assignment->subjectEnrollments->count() }})
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sectionGrade)
                                                <span class="badge bg-primary">Grade {{ $sectionGrade }}</span>
                                            @else
                                                <span class="text-muted small">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="ti ti-users me-1"></i>{{ $studentCount }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @if($studentCount > 0)
                                                    <a href="{{ route('teacher.class-records.show', $assignment->id) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Class Records"
                                                       data-bs-toggle="tooltip">
                                                        <i class="ti ti-list-details"></i>
                                                    </a>
                                                @endif
                                                <button type="button" 
                                                        class="btn btn-sm btn-info edit-btn" 
                                                        title="View Details"
                                                        data-bs-toggle="tooltip"
                                                        data-assignment-id="{{ $assignment->id }}"
                                                        data-subject-name="{{ $assignment->subject->name ?? 'N/A' }}"
                                                        data-subject-code="{{ $assignment->subject->code ?? '' }}"
                                                        data-strand-name="{{ $assignment->strand->name ?? 'N/A' }}"
                                                        data-strand-code="{{ $assignment->strand->code ?? 'N/A' }}"
                                                        data-section-name="{{ $sectionName ?? 'All Sections' }}"
                                                        data-section-grade="{{ $sectionGrade ?? 'N/A' }}"
                                                        data-student-count="{{ $studentCount }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-btn" 
                                                        title="Request Removal"
                                                        data-bs-toggle="tooltip"
                                                        data-assignment-id="{{ $assignment->id }}"
                                                        data-subject-name="{{ $assignment->subject->name ?? 'N/A' }}"
                                                        data-strand-code="{{ $assignment->strand->code ?? 'N/A' }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-2">
                                <h5 class="mb-0 text-primary">{{ $assignments->count() }}</h5>
                                <small class="text-muted">Total Subjects</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2">
                                @php
                                    $uniqueSections = $assignments->map(function($a) {
                                        return data_get($a, 'sectionAssignment.section.id') 
                                            ?: data_get($a, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.id');
                                    })->filter()->unique()->count();
                                @endphp
                                <h5 class="mb-0 text-info">{{ $uniqueSections }}</h5>
                                <small class="text-muted">Unique Sections</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2">
                                @php
                                    $totalStudents = $assignments->sum(function($a) {
                                        return $a->subjectEnrollments->filter(function($se) {
                                            return data_get($se, 'studentEnrollment.student');
                                        })->count();
                                    });
                                @endphp
                                <h5 class="mb-0 text-success">{{ $totalStudents }}</h5>
                                <small class="text-muted">Total Students</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Overall Summary -->
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="ti ti-chart-bar me-2"></i>Overall Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="avatar avatar-lg bg-primary text-white mb-2 mx-auto">
                                <i class="ti ti-calendar fs-4"></i>
                            </div>
                            <h4 class="mb-0 text-primary">{{ $assignmentsByYear->count() }}</h4>
                            <small class="text-muted">Academic Year{{ $assignmentsByYear->count() > 1 ? 's' : '' }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="avatar avatar-lg bg-info text-white mb-2 mx-auto">
                                <i class="ti ti-book fs-4"></i>
                            </div>
                            @php
                                $totalAssignments = $assignmentsByYear->flatten()->count();
                            @endphp
                            <h4 class="mb-0 text-info">{{ $totalAssignments }}</h4>
                            <small class="text-muted">Total Assignments</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="avatar avatar-lg bg-warning text-white mb-2 mx-auto">
                                <i class="ti ti-school fs-4"></i>
                            </div>
                            @php
                                $uniqueSubjects = $assignmentsByYear->flatten()->pluck('subject.id')->unique()->count();
                            @endphp
                            <h4 class="mb-0 text-warning">{{ $uniqueSubjects }}</h4>
                            <small class="text-muted">Unique Subjects</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="avatar avatar-lg bg-success text-white mb-2 mx-auto">
                                <i class="ti ti-users fs-4"></i>
                            </div>
                            @php
                                $overallTotalStudents = $assignmentsByYear->flatten()->sum(function($a) {
                                    return $a->subjectEnrollments->filter(function($se) {
                                        return data_get($se, 'studentEnrollment.student');
                                    })->count();
                                });
                            @endphp
                            <h4 class="mb-0 text-success">{{ $overallTotalStudents }}</h4>
                            <small class="text-muted">All Students</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Details Modal -->
<div class="modal fade" id="assignmentDetailsModal" tabindex="-1" aria-labelledby="assignmentDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentDetailsLabel"><i class="ti ti-book me-2"></i>Assignment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Subject</label>
                        <div class="fw-semibold" id="detailSubject">—</div>
                        <div class="text-muted small" id="detailSubjectCode"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Strand</label>
                        <div class="fw-semibold"><span class="badge bg-secondary" id="detailStrandCode">—</span> <span id="detailStrand">—</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Section</label>
                        <div class="fw-semibold" id="detailSection">—</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Students</label>
                        <div class="fw-semibold"><span class="badge bg-success" id="detailStudents">0</span></div>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        Editing of assignments is managed by Admin under Section & Advisers. If something looks wrong, please contact the registrar.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
 </div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-labelledby="deleteAssignmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAssignmentLabel"><i class="ti ti-alert-triangle text-danger me-2"></i>Remove Teaching Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">You're about to remove your assignment for:</p>
                <ul class="mb-0">
                    <li><strong>Subject:</strong> <span id="delSubject">—</span></li>
                    <li><strong>Strand:</strong> <span id="delStrand">—</span></li>
                </ul>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="ti ti-info-circle me-2"></i>This will only detach you from the subject. Students and records remain. You can be re-assigned by Admin later.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteAssignmentForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i>Remove</button>
                </form>
            </div>
        </div>
    </div>
 </div>

<script>
    (function() {
        const baseDeleteUrl = "{{ url('/teacher/profile/teaching') }}"; // DELETE /teacher/profile/teaching/{assignment}

        // Details modal
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const subject = btn.getAttribute('data-subject-name') || '—';
                const subjectCode = btn.getAttribute('data-subject-code') || '';
                const strand = btn.getAttribute('data-strand-name') || '—';
                const strandCode = btn.getAttribute('data-strand-code') || '—';
                const section = btn.getAttribute('data-section-name') || 'All Sections';
                const students = btn.getAttribute('data-student-count') || '0';

                document.getElementById('detailSubject').textContent = subject;
                document.getElementById('detailSubjectCode').textContent = subjectCode ? '(' + subjectCode + ')' : '';
                document.getElementById('detailStrand').textContent = strand;
                document.getElementById('detailStrandCode').textContent = strandCode;
                document.getElementById('detailSection').textContent = section;
                document.getElementById('detailStudents').textContent = students;

                const modal = new bootstrap.Modal(document.getElementById('assignmentDetailsModal'));
                modal.show();
            });
        });

        // Delete modal
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-assignment-id');
                const subject = btn.getAttribute('data-subject-name') || '—';
                const strandCode = btn.getAttribute('data-strand-code') || '—';

                document.getElementById('delSubject').textContent = subject;
                document.getElementById('delStrand').textContent = strandCode;

                const form = document.getElementById('deleteAssignmentForm');
                form.action = baseDeleteUrl + '/' + id;

                const modal = new bootstrap.Modal(document.getElementById('deleteAssignmentModal'));
                modal.show();
            });
        });
    })();
</script>
@endsection

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
}

.avatar.avatar-xxl {
    width: 5rem;
    height: 5rem;
}

.avatar.avatar-lg {
    width: 3.5rem;
    height: 3.5rem;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.btn {
    border-radius: 0.25rem;
}

.badge {
    font-weight: 500;
}
</style>
