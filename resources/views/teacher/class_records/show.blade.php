@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Class Record: {{ $assignment->subject?->name ?? '—' }}</h4>
        <div class="text-muted small">{{ $assignment->academicYear?->display_name ?? ($assignment->academicYear?->name) }} • Strand: {{ $assignment->strand?->name ?? '—' }}</div>
    </div>
    <div>
        <a href="{{ route('teacher.class-records.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if(isset($classDetails))
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">Strand</div><div class="fw-semibold">{{ $classDetails['strand'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Section</div><div class="fw-semibold">{{ $classDetails['section'] ?? '—' }}</div></div>
            <div class="col-md-2"><div class="text-muted small">Grade</div><div class="fw-semibold">{{ $classDetails['grade'] ?? '—' }}</div></div>
            <div class="col-md-4"><div class="text-muted small">Subject</div><div class="fw-semibold">{{ $classDetails['subject'] ?? '—' }} <span class="text-muted">{{ $classDetails['subject_code'] ? '(' . $classDetails['subject_code'] . ')' : '' }}</span></div></div>
            <div class="col-md-3"><div class="text-muted small">Subject Teacher</div><div class="fw-semibold">{{ $classDetails['subject_teacher'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Adviser</div><div class="fw-semibold">{{ $classDetails['adviser'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">School Year</div><div class="fw-semibold">{{ $classDetails['school_year'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Semester</div><div class="fw-semibold">{{ $classDetails['semester'] ?? '—' }}</div></div>
        </div>
    </div>
</div>
@endif

<h6 class="text-muted mb-2">Overview</h6>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mx-1 mx-md-0" role="alert">
    <i class="ti ti-check me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-dark rounded-circle"><i class="ti ti-users"></i></span><div><div class="text-muted small">Total Students</div><div class="h4 mb-0">{{ $counts['total'] }}</div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-success rounded-circle"><i class="ti ti-mood-boy"></i></span><div><div class="text-muted small">Boys</div><div class="h4 mb-0">{{ $counts['male'] }}</div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-info rounded-circle"><i class="ti ti-mood-girl"></i></span><div><div class="text-muted small">Girls</div><div class="h4 mb-0">{{ $counts['female'] }}</div></div></div></div></div>
    </div>
</div>

<h6 class="text-muted mb-2">By Status</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-primary rounded-circle"><i class="ti ti-badge"></i></span><div><div class="text-muted small">Active</div><div class="h4 mb-0">{{ $counts['active'] }}</div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-primary-subtle rounded-circle"><i class="ti ti-certificate"></i></span><div><div class="text-muted small">Graduated</div><div class="h4 mb-0">{{ $counts['graduated'] }}</div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-warning rounded-circle"><i class="ti ti-user-minus"></i></span><div><div class="text-muted small">Dropped</div><div class="h4 mb-0">{{ $counts['dropped'] }}</div></div></div></div></div>
    </div>
</div>

<h6 class="text-muted mb-2">Students</h6>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boys as $i => $s)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $s['student_number'] }}</td>
                                    <td>{{ $s['name'] }}</td>
                                    <td>
                                        @php $st = $s['status']; @endphp
                                        @switch($st)
                                            @case('active')
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                @break
                                            @case('graduated')
                                                <span class="badge bg-primary-subtle text-primary">Graduated</span>
                                                @break
                                            @case('dropped')
                                                <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-muted border">N/A</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><strong>Boys</strong></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($girls as $i => $s)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $s['student_number'] }}</td>
                                    <td>{{ $s['name'] }}</td>
                                    <td>
                                        @php $st = $s['status']; @endphp
                                        @switch($st)
                                            @case('active')
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                @break
                                            @case('graduated')
                                                <span class="badge bg-primary-subtle text-primary">Graduated</span>
                                                @break
                                            @case('dropped')
                                                <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-muted border">N/A</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><strong>Girls</strong></div>
        </div>
    </div>
</div>

<h6 class="text-muted mb-2">Class Record</h6>
<style>
    /* Scoped to class record tables only */
    .table-border-black {
        border: 2px solid #000 !important;
        border-collapse: collapse !important;
        background: #fff;
    }
    .table-border-black th,
    .table-border-black td {
        border: 1px solid #000 !important;
    }
    .table-border-black thead th {
        border-bottom: 2px solid #000 !important;
    }
    .table-border-black thead tr:first-child th {
        text-align: center;
    }
    .table-border-black .text-nowrap { white-space: nowrap; }
</style>
<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-first-semester" data-bs-toggle="tab" data-bs-target="#panel-first-semester" type="button" role="tab" aria-controls="panel-first-semester" aria-selected="true">First Semester</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-second-semester" data-bs-toggle="tab" data-bs-target="#panel-second-semester" type="button" role="tab" aria-controls="panel-second-semester" aria-selected="false">Second Semester</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-semester-final" data-bs-toggle="tab" data-bs-target="#panel-semester-final" type="button" role="tab" aria-controls="panel-semester-final" aria-selected="false">Semester Final Grade</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            @php
                $tabs = [
                    ['id' => 'first-semester', 'label' => 'First Semester', 'grade_label' => 'Quarterly Grade'],
                    ['id' => 'second-semester', 'label' => 'Second Semester', 'grade_label' => 'Quarterly Grade'],
                    ['id' => 'semester-final', 'label' => 'Semester Final Grade', 'grade_label' => 'Final Grade'],
                ];
            @endphp
            @foreach($tabs as $ti => $t)
                <div class="tab-pane fade {{ $ti === 0 ? 'show active' : '' }}" id="panel-{{ $t['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $t['id'] }}">

                    <div class="d-flex justify-content-end py-2 px-3">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.class-records.term.show', ['assignment' => $assignment->id, 'term' => $t['id']]) }}">
                            <i class="ti ti-external-link me-1"></i> View {{ $t['label'] }}
                        </a>
                    </div>
                    
                    <div class="custom-datatable-filter table-responsive">
                        @php $tt = $t['id']; $ts = $termSummaries[$tt] ?? null; @endphp
                        
                        @if ($tt === 'first-semester' || $tt === 'second-semester')
                        <div class="d-flex justify-content-end py-2 px-3">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSubmitFinalGrades">
                                <i class="ti ti-check me-1"></i> Submit Final Grades
                            </button>
                        </div>
                        <table class="table table-sm align-middle mb-0 table-bordered table-border-black">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">No.</th>
                                    <th colspan="5" class="text-center">Name of Student</th>
                                    <th colspan="3" class="text-center">Written Work ({{ rtrim(rtrim(number_format(($weights['ww'] ?? 0) * 100, 2), '0'), '.') }}%)</th>
                                    <th colspan="3" class="text-center">Performance Task ({{ rtrim(rtrim(number_format(($weights['pt'] ?? 0) * 100, 2), '0'), '.') }}%)</th>
                                    <th colspan="3" class="text-center">Quarterly Assessment ({{ rtrim(rtrim(number_format(($weights['qa'] ?? 0) * 100, 2), '0'), '.') }}%)</th>
                                    <th colspan="1" class="text-center">Initial Grade</th>
                                    <th rowspan="2" class="text-center">{{ $t['grade_label'] }}</th>
                                    <th rowspan="2" class="text-center">Action</th>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Family Name</th>
                                    <th class="text-nowrap">,</th>
                                    <th class="text-nowrap">First Name</th>
                                    <th class="text-nowrap">Middle Name</th>
                                    <th class="text-nowrap">Sex</th>
                                    <th class="text-nowrap text-center">Total ({{ isset($ts['wwMaxTotal']) ? rtrim(rtrim(number_format($ts['wwMaxTotal'], 2), '0'), '.') : '—' }})</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">Total ({{ isset($ts['ptMaxTotal']) ? rtrim(rtrim(number_format($ts['ptMaxTotal'], 2), '0'), '.') : '—' }})</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">Total ({{ isset($ts['qaMaxTotal']) ? rtrim(rtrim(number_format($ts['qaMaxTotal'], 2), '0'), '.') : '—' }})</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">100</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $i => $s)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $s['last_name'] }}</td>
                                        <td>,</td>
                                        <td>{{ $s['first_name'] }}</td>
                                        <td>{{ $s['middle_name'] }}</td>
                                        <td class="text-center text-uppercase">{{ $s['gender'] === 'male' ? 'M' : 'F' }}</td>
                                        @php
                                            $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                            $sid = $s['id'];
                                            $sum = $termSummaries[$tt]['perStudent'][$sid] ?? null;
                                            $wwRaw = $sum['wwRaw'] ?? null; $wwPS = $sum['wwPS'] ?? null; $wwWS = $sum['wwWS'] ?? null;
                                            $ptRaw = $sum['ptRaw'] ?? null; $ptPS = $sum['ptPS'] ?? null; $ptWS = $sum['ptWS'] ?? null;
                                            $qaRaw = $sum['qaRaw'] ?? null; $qaPS = $sum['qaPS'] ?? null; $qaWS = $sum['qaWS'] ?? null;
                                            $initial = $sum['initialTotal'] ?? null;
                                        @endphp
                                        <!-- WW -->
                                        <td class="text-center">{{ $fmt($wwRaw) }}</td>
                                        <td class="text-center">{{ $fmt($wwPS) }}</td>
                                        <td class="text-center">{{ $fmt($wwWS) }}</td>
                                        <!-- PT -->
                                        <td class="text-center">{{ $fmt($ptRaw) }}</td>
                                        <td class="text-center">{{ $fmt($ptPS) }}</td>
                                        <td class="text-center">{{ $fmt($ptWS) }}</td>
                                        <!-- QA -->
                                        <td class="text-center">{{ $fmt($qaRaw) }}</td>
                                        <td class="text-center">{{ $fmt($qaPS) }}</td>
                                        <td class="text-center">{{ $fmt($qaWS) }}</td>
                                        <!-- Initial Grade (Total/PS/100) -->
                                        <td class="text-center">{{ $fmt($initial) }}</td>
                                        <!-- Quarterly Grade equals Initial Grade here -->
                                        <td class="text-center">{{ $fmt($initial) }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="{{ route('teacher.class-records.students.show', ['assignment' => $assignment->id, 'student' => $s['student_number']]) }}">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>

                        
                        @endif

                        @if ($tt === 'semester-final')
                        <table class="table table-sm align-middle mb-0 table-bordered table-border-black">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center">Names</th>
                                    <th colspan="2" class="text-center">Grading Period</th>
                                    <th rowspan="2" class="text-center">Average</th>
                                    <th rowspan="2" class="text-center">Final Grade</th>
                                    <th rowspan="2" class="text-center">Remarks</th>
                                    <th rowspan="2" class="text-center">Description</th>
                                    <th rowspan="2" class="text-center"></th>
                                </tr>
                                <tr>
                                    <th class="text-nowrap"></th>
                                    <th class="text-nowrap">Family Name</th>
                                    <th class="text-nowrap">,</th>
                                    <th class="text-nowrap">First Name</th>
                                    <th class="text-nowrap">Middle Name</th>
                                    <th class="text-nowrap text-center">Sex</th>
                                    <th class="text-nowrap text-center">First Quarter</th>
                                    <th class="text-nowrap text-center">Second Quarter</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $i => $s)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $s['last_name'] }}</td>
                                        <td>,</td>
                                        <td>{{ $s['first_name'] }}</td>
                                        <td>{{ $s['middle_name'] }}</td>
                                        <td class="text-center text-uppercase">{{ $s['gender'] === 'male' ? 'M' : 'F' }}</td>
                                        @php
                                            $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                            $sid = $s['id'];
                                            $firstInit = $termSummaries['first-semester']['perStudent'][$sid]['initialTotal'] ?? null;
                                            $secondInit = $termSummaries['second-semester']['perStudent'][$sid]['initialTotal'] ?? null;
                                            $avg = ($firstInit !== null && $secondInit !== null) ? (($firstInit + $secondInit) / 2) : null;
                                            $final = $avg;
                                            $remarks = null;
                                            if ($final !== null) { $remarks = $final >= 75 ? 'Passed' : 'Failed'; }
                                            $desc = null;
                                            if ($final !== null) {
                                                if ($final >= 90) { $desc = 'Outstanding'; }
                                                elseif ($final >= 85) { $desc = 'Very Satisfactory'; }
                                                elseif ($final >= 80) { $desc = 'Satisfactory'; }
                                                elseif ($final >= 75) { $desc = 'Fairly Satisfactory'; }
                                                elseif ($final >= 60) { $desc = 'Did Not Meet Expectations'; }
                                                else { $desc = '—'; }
                                            }
                                        @endphp
                                        <td class="text-center">{{ $fmt($firstInit) }}</td>
                                        <td class="text-center">{{ $fmt($secondInit) }}</td>
                                        <td class="text-center">{{ $fmt($avg) }}</td>
                                        <td class="text-center">{{ $fmt($final) }}</td>
                                        <td class="text-center">{{ $remarks ?? '—' }}</td>
                                        <td class="text-center">{{ $desc ?? '—' }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="{{ route('teacher.class-records.students.show', ['assignment' => $assignment->id, 'student' => $s['student_number']]) }}">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modalSubmitFinalGrades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-checks me-2 text-success"></i>Submit Final Grades</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('teacher.class-records.final-grades.submit', $assignment) }}" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Submitting...';">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
                        <i class="ti ti-alert-triangle mt-1"></i>
                        <div>
                            You are about to write final grades to Subject Enrollments for all listed students. You can re-submit later to update the values.
                        </div>
                    </div>
                    <div class="mb-2 small text-muted">Students affected: <strong>{{ isset($students) ? $students->count() : 0 }}</strong></div>
                    <ul class="mb-0 small">
                        <li><strong>First Semester (FQ)</strong> = Initial Grade from First Semester</li>
                        <li><strong>Second Semester (SQ)</strong> = Initial Grade from Second Semester</li>
                        <li><strong>Average (A)</strong> = (FQ + SQ) / 2</li>
                        <li><strong>Final Grade (F)</strong> = Average</li>
                        <li><strong>Remarks</strong> = Passed if Final ≥ 75, else Failed</li>
                        <li><strong>Description</strong> =
                            <span class="d-inline-block">
                                ≥90 Outstanding; ≥85 Very Satisfactory; ≥80 Satisfactory; ≥75 Fairly Satisfactory; ≥60 Did Not Meet Expectations; else —
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> Yes, Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endpush
