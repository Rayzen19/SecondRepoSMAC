@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-calendar-event me-2"></i>
                    Academic Year |
                    <span class="text-primary fw-semibold"> {{ $year->name ?? '—' }} </span>
                    <small class="text-muted">/ {{ $year->semester ?? '—' }} Semester / </small>
                    <span class="{{ $year->academic_status ? (strtolower($year->academic_status) === 'ongoing enrollment' ? 'text-warning' : (strtolower($year->academic_status) === 'ongoing school year' ? 'text-success' : 'text-muted')) : 'text-muted' }}"> {{ $year->academic_status ? ucwords(strtolower($year->academic_status)) : '—' }}</span>
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="d-flex my-xl-auto right-content align-items-center flex-wrap "> -->
        <!-- <div class="mb-2">
            <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div> -->
        <!-- <div class="mb-2 ms-2">
            <a href="{{ route('admin.academic-year-strand-subjects.create', ['academic_year' => $year->id]) }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-book-plus me-2"></i>Add Subject</a>
        </div> -->
        <!-- <div class="mb-2 ms-2">
            <a href="{{ route('admin.academic-year-strand-advisers.create', ['academic_year' => $year->id]) }}" class="btn btn-outline-primary d-flex align-items-center"><i class="ti ti-user-plus me-2"></i>Register Strand</a>
        </div> -->
    <!-- </div> -->
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabContainer = document.getElementById('ayTab');
    if (!tabContainer) return;

    // Update URL hash when a tab is shown
    tabContainer.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (el) {
        el.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute('data-bs-target') || event.target.getAttribute('href');
            if (target && target.startsWith('#')) {
                if (history.replaceState) {
                    history.replaceState(null, '', target);
                } else {
                    location.hash = target;
                }
            }
        });
    });

    // On load, activate tab from hash if present
    const hash = window.location.hash;
    if (hash) {
        const trigger = tabContainer.querySelector(`[data-bs-target="${hash}"], a[href="${hash}"]`);
        if (trigger) {
            if (window.bootstrap && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(trigger).show();
            } else {
                trigger.click();
            }
        }
    }
});
</script>

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-5">
            <div class="max-w-2xl mx-auto bg-white p-6">



                @php
                $status = strtolower($year->academic_status ?? '');
                @endphp
                <ul class="nav nav-pills rounded-pill bg-light p-1" id="ayTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 rounded-pill active d-flex align-items-center" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="ti ti-info-circle me-2"></i>
                            General
                        </button>
                    </li>
                    <!-- <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab" aria-controls="subjects" aria-selected="false">
                            <i class="ti ti-books me-2"></i>
                            Subjects
                            <span class="badge bg-white text-muted ms-2">{{ $year->strandSubjects->count() ?? 0 }}</span>
                        </button>
                    </li> -->
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="advisers-tab" data-bs-toggle="tab" data-bs-target="#advisers" type="button" role="tab" aria-controls="advisers" aria-selected="false">
                            <i class="ti ti-user-shield me-2"></i>
                            Strands
                            <span class="badge bg-white text-muted ms-2">{{ $year->strandAdvisers->count() ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button" role="tab" aria-controls="sections" aria-selected="false">
                            <i class="ti ti-grid-dots me-2"></i>
                            Sections
                            <span class="badge bg-white text-muted ms-2">{{ $year->strandSections->count() ?? 0 }}</span>
                        </button>
                    </li>
                    @if($status !== 'pending')
                        <li class="nav-item ms-2" role="presentation">
                            <button class="nav-link px-4 rounded-pill d-flex align-items-center" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="false">
                                <i class="ti ti-users me-2"></i>
                                {{ $status === 'ongoing enrollment' ? 'Enrollment' : 'Student' }}
                                <span class="badge bg-white text-muted ms-2">{{ isset($enrollments) ? $enrollments->count() : 0 }}</span>
                            </button>
                        </li>
                    @endif
                </ul>

                <div class="tab-content pt-4" id="ayTabContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                         <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-sm btn-primary"><i class="ti ti-edit me-2"></i>Edit</a>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year</label>
                                    <input type="text" class="form-control" value="{{ $year->name ?? '—' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <input type="text" class="form-control" value="{{ $year->semester ?? '—' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" value="{{ $year->is_active ? 'Active' : 'Inactive' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Academic Status</label>
                                    <input type="text" class="form-control" value="{{ $year->status }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($status !== 'pending')
                        <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab">
                            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-2">
                                <a href="{{ route('admin.student-enrollments.index', ['academic_year_id' => $year->id]) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-list me-1"></i>View Enrollments</a>
                                <a href="{{ route('admin.student-enrollments.create', ['academic_year_id' => $year->id]) }}" class="btn btn-sm btn-primary"><i class="ti ti-user-plus me-1"></i>New Enrollment</a>
                                <form method="POST" action="{{ route('admin.academic-years.sync-subject-enrollments', $year) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Sync subject enrollments for all students in this Academic Year?')">
                                        <i class="ti ti-refresh me-1"></i>Sync Subject Enrollments
                                    </button>
                                </form>
                            </div>
                            @php $rows = $enrollments ?? collect(); @endphp
                            @if($rows->isNotEmpty())
                                <div class="table-responsive mt-2">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 160px;">Reg. #</th>
                                                <th style="width: 180px;">Student #</th>
                                                <th>Name</th>
                                                <th>Strand</th>
                                                <th>Section</th>
                                                <th>Status</th>
                                                <th style="width: 140px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    <td>{{ $row->registration_number }}</td>
                                                    <td>{{ $row->student?->student_number }}</td>
                                                    <td>{{ $row->student?->name }}</td>
                                                    <td>{{ $row->strand?->code }}</td>
                                                    <td>{{ $row->academicYearStrandSection?->section?->grade }} {{ $row->academicYearStrandSection?->section?->name }}</td>
                                                    <td>
                                                        @php
                                                            $badge = $row->status === 'enrolled' ? 'success' : ($row->status === 'completed' ? 'primary' : 'secondary');
                                                        @endphp
                                                        <span class="badge bg-{{ $badge }}">{{ ucfirst($row->status) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.student-enrollments.show', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                        <a href="{{ route('admin.student-enrollments.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="mt-3 mb-0">No students yet.</p>
                            @endif
                        </div>
                    @endif

                    <div class="tab-pane fade" id="advisers" role="tabpanel" aria-labelledby="advisers-tab">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('admin.academic-year-strand-advisers.create', ['academic_year' => $year->id]) }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-2"></i>Assign Strand</a>
                        </div>
                        @php $advisers = $year->strandAdvisers ?? collect(); @endphp
                        @if($advisers->isNotEmpty())
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Strand</th>
                                        <th>Adviser Teacher</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($advisers as $row)
                                    <tr>
                                        <td>{{ $row->strand?->code }} — {{ $row->strand?->name }}</td>
                                        <td>{{ $row->teacher?->employee_number }} — {{ $row->teacher?->first_name }} {{ $row->teacher?->last_name }}</td>
                                        <td>
                                            <a href="{{ route('admin.academic-year-strand-advisers.edit', $row) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="{{ route('admin.academic-year-strand-advisers.show', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="mt-3 mb-0">No strand assigned yet.</p>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="sections" role="tabpanel" aria-labelledby="sections-tab">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('admin.academic-year-strand-sections.create', ['academic_year' => $year->id]) }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-2"></i>Assign Section</a>
                        </div>
                        @php $strandSections = $year->strandSections ?? collect(); @endphp
                        @if($strandSections->isNotEmpty())
                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Strand</th>
                                        <th>Section</th>
                                        <th>Adviser</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($strandSections as $row)
                                    <tr>
                                        <td>{{ $row->strand?->code }} — {{ $row->strand?->name }}</td>
                                        <td>{{ $row->section?->grade }} — {{ $row->section?->name }}</td>
                                        <td>
                                            {{ $row->adviserTeacher?->employee_number }}
                                            @if($row->adviserTeacher)
                                                — {{ $row->adviserTeacher?->last_name }}, {{ $row->adviserTeacher?->first_name }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->is_active)
                                            <span class="badge bg-success">Active</span>
                                            @else
                                            <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.academic-year-strand-sections.show', $row) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="{{ route('admin.academic-year-strand-sections.edit', $row) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="mt-3 mb-0">No sections assigned yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection