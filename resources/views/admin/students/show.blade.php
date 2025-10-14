@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.students.index') }}">Student List</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
    </div>
  </div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-xl rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                            <i class="ti ti-user"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</h3>
                            <div class="text-muted">Student # {{ $student->student_number }} • {{ ucfirst($student->gender) }} • {{ ucfirst($student->status) }}</div>
                            <div class="mt-1"><i class="ti ti-mail"></i> {{ $student->email }} • <i class="ti ti-phone"></i> {{ $student->mobile_number }}</div>
                            <div class="small text-muted mt-1"><i class="ti ti-map-pin"></i> {{ $student->address }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Enrollments</div>
                                <div class="fs-4 fw-bold">{{ $totalEnrollments ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Subjects</div>
                                <div class="fs-4 fw-bold">{{ $totalSubjects ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded-3 text-center">
                                <div class="text-muted small">Guardian</div>
                                <div class="fw-semibold">{{ $student->guardian_name }} <span class="text-muted">•</span> {{ $student->guardian_contact }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Program</div>
                        <div class="fw-semibold">{{ $student->program }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Birthdate</div>
                        <div class="fw-semibold">{{ $student->birthdate }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ $student->email }}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="border rounded-3 p-3">
                        <div class="text-muted small">Generated Password (encrypted)</div>
                        @if(!empty($student->generated_password_encrypted))
                            <code class="d-block text-wrap">{{ $student->generated_password_encrypted }}</code>
                            <div class="small text-muted mt-1">Note: This is an encrypted value stored specifically for display purposes. The actual login password is securely hashed in the user account and cannot be reversed from the hash.</div>
                        @else
                            <div class="text-muted">No generated password stored.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="small text-muted mt-3">Last Updated: {{ $student->updated_at }}</div>
        </div>
    </div>

    <!-- Enrollments and Subjects by Academic Year -->
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Enrollment History</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 border-end">
                    <div class="nav flex-column nav-pills" id="student-years-tab" role="tablist" aria-orientation="vertical">
                        @foreach($academicYears as $year)
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="s-year-{{ $year->id }}-tab" data-bs-toggle="pill" data-bs-target="#s-year-{{ $year->id }}" type="button" role="tab" aria-controls="s-year-{{ $year->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
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
                    <div class="tab-content" id="student-years-tabContent">
                        @foreach($academicYears as $year)
                        @php $ens = ($enrollments[$year->id] ?? collect()); @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="s-year-{{ $year->id }}" role="tabpanel" aria-labelledby="s-year-{{ $year->id }}-tab" tabindex="0">
                            @if($ens->isEmpty())
                                <div class="text-muted">No enrollment records for this academic year.</div>
                            @else
                                @foreach($ens as $en)
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-semibold">{{ data_get($en, 'strand.name', 'Strand') }} • Section {{ data_get($en, 'academicYearStrandSection.section.name', '—') }}</div>
                                                <div class="small text-muted">Reg. # {{ $en->registration_number }} • Status: {{ ucfirst($en->status) }}</div>
                                            </div>
                                        </div>
                                        <div class="list-group">
                                            @foreach(($en->subjectEnrollments ?? []) as $se)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold">{{ data_get($se, 'academicYearStrandSubject.subject.name', 'Subject') }}</div>
                                                            <div class="small text-muted">Teacher: {{ data_get($se, 'academicYearStrandSubject.teacher.last_name') }}, {{ data_get($se, 'academicYearStrandSubject.teacher.first_name') }}</div>
                                                        </div>
                                                    </div>
                                                    @php $records = ($se->subjectRecords ?? collect()); @endphp
                                                    @if($records->isNotEmpty())
                                                    <div class="mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm align-middle">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:32px">#</th>
                                                                        <th>Record</th>
                                                                        <th>Type</th>
                                                                        <th>Quarter</th>
                                                                        <th>Date</th>
                                                                        <th>Max</th>
                                                                        <th>Results</th>
                                                                        <th style="width:1%"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($records as $idx => $rec)
                                                                    @php $resCount = ($rec->results ?? collect())->count(); @endphp
                                                                    <tr>
                                                                        <td>{{ $idx + 1 }}</td>
                                                                        <td>{{ $rec->name ?? '—' }}</td>
                                                                        <td><span class="badge bg-light text-dark">{{ ucfirst($rec->type) }}</span></td>
                                                                        <td>{{ $rec->quarter ?? '—' }}</td>
                                                                        <td>{{ optional($rec->date_given)?->format('Y-m-d') }}</td>
                                                                        <td>{{ $rec->max_score ?? '—' }}</td>
                                                                        <td>
                                                                            <span class="badge bg-primary">{{ $resCount }} entries</span>
                                                                        </td>
                                                                        <td class="text-end">
                                                                            <a href="{{ route('admin.students.export-subject-results', [$student, $en, $rec]) }}" class="btn btn-sm btn-outline-secondary">
                                                                                <i class="ti ti-download"></i> CSV
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    @else
                                                        <div class="text-muted small mt-2">No records yet.</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
