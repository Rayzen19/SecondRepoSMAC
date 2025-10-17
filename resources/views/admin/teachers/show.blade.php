@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Teacher</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teacher</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.teachers.index') }}">Teacher Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Teacher Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')

<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-xl rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                            <i class="ti ti-user"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $teacher->last_name }}, {{ $teacher->first_name }} {{ $teacher->middle_name }} {{ $teacher->suffix }}</h3>
                            <div class="text-muted">Employee # {{ $teacher->employee_number }} • {{ ucfirst($teacher->gender) }} • {{ ucfirst($teacher->status) }}</div>
                            <div class="mt-1"><i class="ti ti-mail"></i> {{ $teacher->email }} • <i class="ti ti-phone"></i> {{ $teacher->phone }}</div>
                            <div class="small text-muted mt-1"><i class="ti ti-map-pin"></i> {{ $teacher->address }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Subjects</div>
                                <div class="fs-4 fw-bold">{{ $totalSubjects ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Advised Sections</div>
                                <div class="fs-4 fw-bold">{{ $totalAdvisedSections ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Students Taught</div>
                                <div class="fs-4 fw-bold">{{ $totalStudentsTaught ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Department</div>
                        <div class="fw-semibold">{{ $teacher->department }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Specialization</div>
                        <div class="fw-semibold">{{ $teacher->specialization }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Term</div>
                        <div class="fw-semibold">{{ $teacher->term }}</div>
                    </div>
                </div>
            </div>
            @if($teacher->subjects->isNotEmpty())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <div class="text-muted small mb-2">Qualified Subjects to Teach</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($teacher->subjects as $subject)
                                <span class="badge bg-info text-dark">
                                    {{ $subject->name }}@if($subject->code) ({{ $subject->code }})@endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="small text-muted mt-3">Last Updated: {{ $teacher->updated_at }}</div>
        </div>
    </div>

    <!-- Assignments by Academic Year -->
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Teaching & Advising Overview</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 border-end">
                    <div class="nav flex-column nav-pills" id="years-tab" role="tablist" aria-orientation="vertical">
                        @php $firstYearId = optional($academicYears->first())->id; @endphp
                        @foreach($academicYears as $year)
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="year-{{ $year->id }}-tab" data-bs-toggle="pill" data-bs-target="#year-{{ $year->id }}" type="button" role="tab" aria-controls="year-{{ $year->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <span class="d-flex align-items-center gap-2">
                                @if($year->is_active)
                                    <span class="badge bg-success">Active</span>
                                @endif
                                <span>{{ $year->display_name ?? $year->name }}</span>
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content" id="years-tabContent">
                        @foreach($academicYears as $year)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="year-{{ $year->id }}" role="tabpanel" aria-labelledby="year-{{ $year->id }}-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="text-muted">Adviser Sections</h6>
                                    @php $sections = ($adviserSections[$year->id] ?? collect()); @endphp
                                    @if($sections->isEmpty())
                                        <div class="text-muted">No adviser sections for this academic year.</div>
                                    @else
                                        <div class="list-group mb-3">
                                            @foreach($sections as $sec)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold">{{ data_get($sec, 'strand.name', 'Strand') }} • Section {{ data_get($sec, 'section.name', 'N/A') }}</div>
                                                            <div class="small text-muted">Adviser</div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-primary">
                                                                {{ ($studentsBySection[$sec->id] ?? collect())->count() }} Students
                                                            </span>
                                                            @php $secCollapseId = 'sec-students-'.$sec->id; @endphp
                                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $secCollapseId }}" aria-expanded="false" aria-controls="{{ $secCollapseId }}">
                                                                Show students
                                                            </button>
                                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.teachers.section-students', [$teacher, $year, $sec]) }}">
                                                                View students
                                                            </a>
                                                        </div>
                                                    </div>
                                                    @php $enrs = ($studentsBySection[$sec->id] ?? collect()); @endphp
                                                    @if($enrs->isNotEmpty())
                                                        <div class="mt-2 collapse" id="{{ $secCollapseId }}">
                                                            <div class="row g-2">
                                                                @foreach($enrs as $en)
                                                                    <div class="col-12 col-md-6">
                                                                        <div class="border rounded-3 p-2 d-flex justify-content-between">
                                                                            <span>{{ optional($en->student)->student_number }} — {{ optional($en->student)->last_name }}, {{ optional($en->student)->first_name }}</span>
                                                                            <span class="badge bg-light text-dark">{{ ucfirst(optional($en->student)->gender) }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <h6 class="text-muted">Subjects Taught</h6>
                                    @php $assignments = ($subjectAssignments[$year->id] ?? collect()); @endphp
                                    @if($assignments->isEmpty())
                                        <div class="text-muted">No subject assignments for this academic year.</div>
                                    @else
                                        <div class="list-group">
                                            @foreach($assignments as $asmt)
                                                @php
                                                    $secName = data_get($asmt, 'subjectEnrollments.0.studentEnrollment.academicYearStrandSection.section.name');
                                                    $countStudents = $asmt->subjectEnrollments->filter(fn($se) => data_get($se, 'studentEnrollment.student'))->count();
                                                @endphp
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold">{{ data_get($asmt, 'strand.name', 'Strand') }} • {{ data_get($asmt, 'subject.name', 'Subject') }}</div>
                                                            <div class="small text-muted">Section {{ $secName ?: '—' }}</div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-primary">{{ $countStudents }} Students</span>
                                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.teachers.subject-students', [$teacher, $year, $asmt]) }}">
                                                                View students
                                                            </a>
                                                            @php $asmtCollapseId = 'asmt-students-'.$asmt->id; @endphp
                                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $asmtCollapseId }}" aria-expanded="false" aria-controls="{{ $asmtCollapseId }}">
                                                                Quick peek
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if($asmt->subjectEnrollments->isNotEmpty())
                                                        <div class="mt-2 collapse" id="{{ $asmtCollapseId }}">
                                                            <div class="row g-2">
                                                                @foreach($asmt->subjectEnrollments as $se)
                                                                    @php $stud = optional($se->studentEnrollment)->student; @endphp
                                                                    @if($stud)
                                                                    <div class="col-12 col-md-6">
                                                                        <div class="border rounded-3 p-2 d-flex justify-content-between">
                                                                            <span>{{ $stud->student_number }} — {{ $stud->last_name }}, {{ $stud->first_name }}</span>
                                                                            <span class="badge bg-light text-dark">{{ ucfirst($stud->gender) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
