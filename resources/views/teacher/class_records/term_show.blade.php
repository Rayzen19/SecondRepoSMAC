@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Class Record — {{ $termLabel }}</h4>
        <div class="text-muted small">{{ $details['school_year'] ?? '—' }} • Strand: {{ $details['strand'] ?? '—' }} • Subject: {{ $details['subject'] ?? '—' }}</div>
    </div>
    <div>
        <a href="{{ route('teacher.class-records.show', $assignment) }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
    <div class="fw-semibold mb-1">There were problems with your submission:</div>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Strand</div>
                <div class="fw-semibold">{{ $details['strand'] ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Section</div>
                <div class="fw-semibold">{{ $details['section'] ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Grade</div>
                <div class="fw-semibold">{{ $details['grade'] ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Subject</div>
                <div class="fw-semibold">{{ $details['subject'] ?? '—' }} <span class="text-muted">{{ $details['subject_code'] ? '(' . $details['subject_code'] . ')' : '' }}</span></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Subject Teacher</div>
                <div class="fw-semibold">{{ $details['subject_teacher'] ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Adviser</div>
                <div class="fw-semibold">{{ $details['adviser'] ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">School Year</div>
                <div class="fw-semibold">{{ $details['school_year'] ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Semester</div>
                <div class="fw-semibold">{{ $details['semester'] ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted mb-2">Overview</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-dark rounded-circle"><i class="ti ti-users"></i></span>
                    <div>
                        <div class="text-muted small">Total Students</div>
                        <div class="h4 mb-0">{{ $counts['total'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-success rounded-circle"><i class="ti ti-mood-boy"></i></span>
                    <div>
                        <div class="text-muted small">Boys</div>
                        <div class="h4 mb-0">{{ $counts['male'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-info rounded-circle"><i class="ti ti-mood-girl"></i></span>
                    <div>
                        <div class="text-muted small">Girls</div>
                        <div class="h4 mb-0">{{ $counts['female'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted mb-2">By Status</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-primary rounded-circle"><i class="ti ti-badge"></i></span>
                    <div>
                        <div class="text-muted small">Active</div>
                        <div class="h4 mb-0">{{ $counts['active'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-primary-subtle rounded-circle"><i class="ti ti-certificate"></i></span>
                    <div>
                        <div class="text-muted small">Graduated</div>
                        <div class="h4 mb-0">{{ $counts['graduated'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-md bg-warning rounded-circle"><i class="ti ti-user-minus"></i></span>
                    <div>
                        <div class="text-muted small">Dropped</div>
                        <div class="h4 mb-0">{{ $counts['dropped'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

    .table-border-black .text-nowrap {
        white-space: nowrap;
    }
</style>
<div class="card">
    <div class="card-body p-0">
        <div class="d-flex justify-content-end p-2">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSubmitFinalGrades">
                <i class="ti ti-check me-1"></i> Submit Final Grades
            </button>
        </div>

        @if($term !== 'semester-final')
        <form action="{{ route('teacher.class-records.scores.store', $assignment) }}" method="post">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <div class="d-flex justify-content-end p-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti ti-device-floppy me-1"></i> Save Scores
                </button>
            </div>
            <div class="table-responsive">

                <table
                    id="classRecordTable"
                    class="table table-sm align-middle mb-0 table-bordered table-border-black"
                    data-ww-max-total="{{ (float)($wwMaxTotal ?? 0) }}"
                    data-pt-max-total="{{ (float)($ptMaxTotal ?? 0) }}"
                    data-qa-max-total="{{ (float)($qaMaxTotal ?? 0) }}"
                    data-ww-weight="{{ (float)(($assignment->written_works_percentage ?? 0) / 100) }}"
                    data-pt-weight="{{ (float)(($assignment->performance_tasks_percentage ?? 0) / 100) }}"
                    data-qa-weight="{{ (float)(($assignment->quarterly_assessment_percentage ?? 0) / 100) }}">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center">No.</th>
                            <th colspan="5" class="text-center">Name of Student</th>
                            <th colspan="{{ ($wwRecords->count() ?? 0) + 3 }}" class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span>Written Work ({{ $assignment->written_works_percentage ?? 0 }}%)</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-category="written_work" data-title="Add Written Work">
                                        <i class="ti ti-plus"></i> Add
                                    </button>
                                </div>
                            </th>
                            <th colspan="{{ ($ptRecords->count() ?? 0) + 3 }}" class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span>Performance Task ({{ $assignment->performance_tasks_percentage ?? 0 }}%)</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-category="performance_task" data-title="Add Performance Task">
                                        <i class="ti ti-plus"></i> Add
                                    </button>
                                </div>
                            </th>
                            <th colspan="{{ ($qaRecords->count() ?? 0) + 3 }}" class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span>Quarterly Assessment ({{ $assignment->quarterly_assessment_percentage ?? 0 }}%)</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-category="quarterly_assessment" data-title="Add Quarterly Assessment">
                                        <i class="ti ti-plus"></i> Add
                                    </button>
                                </div>
                            </th>
                            <th colspan="1" class="text-center">Initial Grade</th>
                            <th rowspan="2" class="text-center">{{ $term === 'semester-final' ? 'Final Grade' : 'Quarterly Grade' }}</th>
                            <th rowspan="2" class="text-center">Description</th>
                            <th rowspan="2" class="text-center">Action</th>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Family Name</th>
                            <th class="text-nowrap">,</th>
                            <th class="text-nowrap">First Name</th>
                            <th class="text-nowrap">Middle Name</th>
                            <th class="text-nowrap">Sex</th>
                            <!-- load written works -->
                            @foreach(($wwRecords ?? collect()) as $rec)
                            @php
                            $day = optional($rec->date_given)->format('l');
                            @endphp
                            <th class="text-center">
                                <button type="button"
                                    class="btn btn-link p-0 text-info btn-assessment-info"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ $rec->name }}"
                                    aria-label="Show details for {{ $rec->name }}"
                                    data-assessment-id="{{ $rec->id }}"
                                    data-assessment-index="{{ $loop->iteration }}"
                                    data-assessment-name="{{ $rec->name }}"
                                    data-assessment-date="{{ optional($rec->date_given)->format('Y-m-d') }}"
                                    data-assessment-day="{{ $day }}"
                                    data-assessment-max="{{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}"
                                    data-assessment-desc="{{ $rec->description }}"
                                    data-assessment-type="{{ $rec->type }}"
                                    data-assessment-quarter="{{ $rec->quarter ?? ($term === 'semester-final' ? 'Final' : '') }}">
                                    #{{ $loop->iteration }}
                                </button>
                                / {{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}
                            </th>
                            @endforeach
                            <th class="text-nowrap">Total ({{ rtrim(rtrim(number_format($wwMaxTotal ?? 0, 2), '0'), '.') }})</th>
                            <th class="text-nowrap">PS</th>
                            <th class="text-nowrap">WS</th>
                            <!-- load performance tasks -->
                            @foreach(($ptRecords ?? collect()) as $rec)
                            @php
                            $day = optional($rec->date_given)->format('l');
                            @endphp
                            <th class="text-center">
                                <button type="button"
                                    class="btn btn-link p-0 text-info btn-assessment-info"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ $rec->name }}"
                                    aria-label="Show details for {{ $rec->name }}"
                                    data-assessment-id="{{ $rec->id }}"
                                    data-assessment-index="{{ $loop->iteration }}"
                                    data-assessment-name="{{ $rec->name }}"
                                    data-assessment-date="{{ optional($rec->date_given)->format('Y-m-d') }}"
                                    data-assessment-day="{{ $day }}"
                                    data-assessment-max="{{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}"
                                    data-assessment-desc="{{ $rec->description }}"
                                    data-assessment-type="{{ $rec->type }}"
                                    data-assessment-quarter="{{ $rec->quarter ?? ($term === 'semester-final' ? 'Final' : '') }}">
                                    #{{ $loop->iteration }}
                                </button>
                                / {{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}
                            </th>
                            @endforeach
                            <th class="text-nowrap">Total ({{ rtrim(rtrim(number_format($ptMaxTotal ?? 0, 2), '0'), '.') }})</th>
                            <th class="text-nowrap">PS</th>
                            <th class="text-nowrap">WS</th>
                            <!-- load quarterly assessments -->
                            @foreach(($qaRecords ?? collect()) as $rec)
                            @php
                            $day = optional($rec->date_given)->format('l');
                            @endphp
                            <th class="text-center">
                                <button type="button"
                                    class="btn btn-link p-0 text-info btn-assessment-info"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="{{ $rec->name }}"
                                    aria-label="Show details for {{ $rec->name }}"
                                    data-assessment-id="{{ $rec->id }}"
                                    data-assessment-index="{{ $loop->iteration }}"
                                    data-assessment-name="{{ $rec->name }}"
                                    data-assessment-date="{{ optional($rec->date_given)->format('Y-m-d') }}"
                                    data-assessment-day="{{ $day }}"
                                    data-assessment-max="{{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}"
                                    data-assessment-desc="{{ $rec->description }}"
                                    data-assessment-type="{{ $rec->type }}"
                                    data-assessment-quarter="{{ $rec->quarter ?? ($term === 'semester-final' ? 'Final' : '') }}">
                                    #{{ $loop->iteration }}
                                </button>
                                / {{ rtrim(rtrim(number_format($rec->max_score, 2), '0'), '.') }}
                            </th>
                            @endforeach
                            <th class="text-nowrap">Total ({{ rtrim(rtrim(number_format($qaMaxTotal ?? 0, 2), '0'), '.') }})</th>
                            <th class="text-nowrap">PS</th>
                            <th class="text-nowrap">WS</th>
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
                            @foreach(($wwRecords ?? collect()) as $rec)
                            @php
                            $score = $scoresByRecord[$rec->id][$s['id']] ?? null;
                            @endphp
                            <td class="text-center">
                                <input
                                    type="number"
                                    name="scores[{{ $rec->id }}][{{ $s['id'] }}]"
                                    class="form-control form-control-sm text-center score-input"
                                    value="{{ $score['raw_score'] ?? 0 }}"
                                    min="0"
                                    max="{{ $rec->max_score }}"
                                    step="1"
                                    inputmode="numeric"
                                    data-cat="ww"
                                    data-record-id="{{ $rec->id }}"
                                    data-student-id="{{ $s['id'] }}"
                                    style="max-width: 80px;">
                            </td>
                            @endforeach
                            @php
                            $wwRaw = ($wwRecords ?? collect())->sum(function($rec) use($scoresByRecord, $s){ $v = $scoresByRecord[$rec->id][$s['id']] ?? null; return $v ? (float)$v['raw_score'] : 0; });
                            @endphp
                            <td class="text-center js-total" data-cat="ww" data-kind="raw">{{ $wwRaw > 0 ? rtrim(rtrim(number_format($wwRaw, 2), '0'), '.') : '—' }}</td>
                            @php
                            $wwPS = ($wwMaxTotal ?? 0) > 0 ? ($wwRaw / $wwMaxTotal) * 100 : null;
                            $wwWS = isset($wwPS) ? ($wwPS * (($assignment->written_works_percentage ?? 0) / 100)) : null;
                            @endphp
                            <td class="text-center js-total" data-cat="ww" data-kind="ps">{{ isset($wwPS) ? rtrim(rtrim(number_format($wwPS, 2), '0'), '.') : '—' }}</td>
                            <td class="text-center js-total" data-cat="ww" data-kind="ws">{{ isset($wwWS) ? rtrim(rtrim(number_format($wwWS, 2), '0'), '.') : '—' }}</td>
                            @foreach(($ptRecords ?? collect()) as $rec)
                            @php
                            $score = $scoresByRecord[$rec->id][$s['id']] ?? null;
                            @endphp
                            <td class="text-center">
                                <input
                                    type="number"
                                    name="scores[{{ $rec->id }}][{{ $s['id'] }}]"
                                    class="form-control form-control-sm text-center score-input"
                                    value="{{ $score['raw_score'] ?? 0 }}"
                                    min="0"
                                    max="{{ $rec->max_score }}"
                                    step="1"
                                    inputmode="numeric"
                                    data-cat="pt"
                                    data-record-id="{{ $rec->id }}"
                                    data-student-id="{{ $s['id'] }}"
                                    style="max-width: 80px;">
                            </td>
                            @endforeach
                            @php
                            $ptRaw = ($ptRecords ?? collect())->sum(function($rec) use($scoresByRecord, $s){ $v = $scoresByRecord[$rec->id][$s['id']] ?? null; return $v ? (float)$v['raw_score'] : 0; });
                            @endphp
                            <td class="text-center js-total" data-cat="pt" data-kind="raw">{{ $ptRaw > 0 ? rtrim(rtrim(number_format($ptRaw, 2), '0'), '.') : '—' }}</td>
                            @php
                            $ptPS = ($ptMaxTotal ?? 0) > 0 ? ($ptRaw / $ptMaxTotal) * 100 : null;
                            $ptWS = isset($ptPS) ? ($ptPS * (($assignment->performance_tasks_percentage ?? 0) / 100)) : null;
                            @endphp
                            <td class="text-center js-total" data-cat="pt" data-kind="ps">{{ isset($ptPS) ? rtrim(rtrim(number_format($ptPS, 2), '0'), '.') : '—' }}</td>
                            <td class="text-center js-total" data-cat="pt" data-kind="ws">{{ isset($ptWS) ? rtrim(rtrim(number_format($ptWS, 2), '0'), '.') : '—' }}</td>
                            @foreach(($qaRecords ?? collect()) as $rec)
                            @php
                            $score = $scoresByRecord[$rec->id][$s['id']] ?? null;
                            @endphp
                            <td class="text-center">
                                <input
                                    type="number"
                                    name="scores[{{ $rec->id }}][{{ $s['id'] }}]"
                                    class="form-control form-control-sm text-center score-input"
                                    value="{{ $score['raw_score'] ?? 0 }}"
                                    min="0"
                                    max="{{ $rec->max_score }}"
                                    step="1"
                                    inputmode="numeric"
                                    data-cat="qa"
                                    data-record-id="{{ $rec->id }}"
                                    data-student-id="{{ $s['id'] }}"
                                    style="max-width: 80px;">
                            </td>
                            @endforeach
                            @php
                            $qaRaw = ($qaRecords ?? collect())->sum(function($rec) use($scoresByRecord, $s){ $v = $scoresByRecord[$rec->id][$s['id']] ?? null; return $v ? (float)$v['raw_score'] : 0; });
                            @endphp
                            <td class="text-center js-total" data-cat="qa" data-kind="raw">{{ $qaRaw > 0 ? rtrim(rtrim(number_format($qaRaw, 2), '0'), '.') : '—' }}</td>
                            @php
                            $qaPS = ($qaMaxTotal ?? 0) > 0 ? ($qaRaw / $qaMaxTotal) * 100 : null;
                            $qaWS = isset($qaPS) ? ($qaPS * (($assignment->quarterly_assessment_percentage ?? 0) / 100)) : null;
                            @endphp
                            <td class="text-center js-total" data-cat="qa" data-kind="ps">{{ isset($qaPS) ? rtrim(rtrim(number_format($qaPS, 2), '0'), '.') : '—' }}</td>
                            <td class="text-center js-total" data-cat="qa" data-kind="ws">{{ isset($qaWS) ? rtrim(rtrim(number_format($qaWS, 2), '0'), '.') : '—' }}</td>
                            @php
                            $initialTotal = (isset($wwWS) ? $wwWS : 0) + (isset($ptWS) ? $ptWS : 0) + (isset($qaWS) ? $qaWS : 0);
                            $initialPS = $initialTotal; // already in percent sum of weights
                            $initialWS = $initialTotal; // WS equals weighted sum across categories
                            @endphp
                            <td class="text-center js-initial" data-kind="total">{{ $initialTotal > 0 ? rtrim(rtrim(number_format($initialTotal, 2), '0'), '.') : '—' }}</td>
                            <td class="text-center js-initial" data-kind="total">{{ $initialTotal > 0 ? rtrim(rtrim(number_format($initialTotal, 2), '0'), '.') : '—' }}</td>
                            @php
                                $description = '0';
                                if ($initialTotal > 89) {
                                    $description = 'Outstanding';
                                } elseif ($initialTotal > 84) {
                                    $description = 'Very Satisfactory';
                                } elseif ($initialTotal > 79) {
                                    $description = 'Satisfactory';
                                } elseif ($initialTotal > 74) {
                                    $description = 'Fairly Satisfactory';
                                } elseif ($initialTotal > 59) {
                                    $description = 'Did Not Meet Expectations';
                                }
                            @endphp
                            <td class="text-center js-description">{{ $description }}</td>
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
            </div>
            <div class="d-flex justify-content-end p-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti ti-device-floppy me-1"></i> Save Scores
                </button>
            </div>
        </form>
        @endif

        @if($term === 'semester-final')
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0 table-bordered table-border-black">
                <thead>
                    <tr>
                        <th colspan="6" class="text-center">Names</th>
                        <th colspan="2" class="text-center">Grading Period</th>
                        <th rowspan="2" class="text-center">Average</th>
                        <th rowspan="2" class="text-center">Final Grade</th>
                        <th rowspan="2" class="text-center">Remarks</th>
                        <th rowspan="2" class="text-center">Description</th>
                        <th rowspan="2" class="text-center">Action</th>
                    </tr>
                    <tr>
                        <th class="text-nowrap text-center">No.</th>
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
                    @php $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; }; @endphp
                    @forelse($students as $i => $s)
                    @php
                        $sid = $s['id'];
                        $firstInit = $firstSemInitials[$sid] ?? null;
                        $secondInit = $secondSemInitials[$sid] ?? null;
                        $avg = ($firstInit !== null && $secondInit !== null) ? (($firstInit + $secondInit) / 2) : null;
                        $final = $avg;
                        $remarks = $final !== null ? ($final >= 75 ? 'Passed' : 'Failed') : null;
                        $desc = null;
                        if ($final !== null) {
                            if ($final > 89) { $desc = 'Outstanding'; }
                            elseif ($final > 84) { $desc = 'Very Satisfactory'; }
                            elseif ($final > 79) { $desc = 'Satisfactory'; }
                            elseif ($final > 74) { $desc = 'Fairly Satisfactory'; }
                            elseif ($final > 59) { $desc = 'Did Not Meet Expectations'; }
                            else { $desc = '0'; }
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $s['last_name'] }}</td>
                        <td>,</td>
                        <td>{{ $s['first_name'] }}</td>
                        <td>{{ $s['middle_name'] }}</td>
                        <td class="text-center text-uppercase">{{ $s['gender'] === 'male' ? 'M' : 'F' }}</td>
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
        </div>

        @endif
    </div>
</div>
@endsection

@push('modals')

<div class="modal fade" id="modalSubmitFinalGrades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Final Grades</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('teacher.class-records.final-grades.submit', $assignment) }}">
                @csrf
                <div class="modal-body">
                    <p class="mb-0">This will save the First Semester, Second Semester, Average, and Final Grades to the subject enrollments for all listed students. Proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes, Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddAssessment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddAssessmentTitle">Add Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddAssessment" action="{{ route('teacher.class-records.assessments.store', $assignment) }}" method="post">
                @csrf
                <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                <input type="hidden" name="term" value="{{ $term }}">
                <input type="hidden" name="category" id="modalAddAssessmentCategory" value="written_work">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Quiz #1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional details"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date Given</label>
                            <input type="date" name="date_given" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Score or Over Score</label>
                            <input type="number" name="max_score" min="1" class="form-control" placeholder="e.g., 20" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAssessmentInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assessment Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">#</dt>
                    <dd class="col-sm-8" id="infoIndex">—</dd>
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8" id="infoName">—</dd>

                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8" id="infoType">—</dd>

                    <dt class="col-sm-4">Quarter</dt>
                    <dd class="col-sm-8" id="infoQuarter">—</dd>

                    <dt class="col-sm-4">Date Given</dt>
                    <dd class="col-sm-8" id="infoDate">—</dd>

                    <dt class="col-sm-4">Day</dt>
                    <dd class="col-sm-8" id="infoDay">—</dd>

                    <dt class="col-sm-4">Max Score</dt>
                    <dd class="col-sm-8" id="infoMax">—</dd>

                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8" id="infoDesc">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    (function() {
        const modalEl = document.getElementById('modalAddAssessment');
        if (!modalEl) return;
        modalEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            const category = button.getAttribute('data-category');
            const title = button.getAttribute('data-title');
            document.getElementById('modalAddAssessmentTitle').textContent = title;
            document.getElementById('modalAddAssessmentCategory').value = category;
        });
    })();

    (function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-compute per-row totals/PS/WS as scores change
        const table = document.getElementById('classRecordTable');
        if (table) {
            const wwMaxTotal = parseFloat(table.dataset.wwMaxTotal || '0') || 0;
            const ptMaxTotal = parseFloat(table.dataset.ptMaxTotal || '0') || 0;
            const qaMaxTotal = parseFloat(table.dataset.qaMaxTotal || '0') || 0;
            const WW_WEIGHT = parseFloat(table.dataset.wwWeight || '0') || 0;
            const PT_WEIGHT = parseFloat(table.dataset.ptWeight || '0') || 0;
            const QA_WEIGHT = parseFloat(table.dataset.qaWeight || '0') || 0;

            function fmt(n) {
                if (n === null || isNaN(n)) return '—';
                const s = n.toFixed(2).replace(/\.00$/, '');
                return s.replace(/(\.[1-9])0$/, '$1');
            }

            function recomputeRow(tr) {
                const cats = ['ww', 'pt', 'qa'];
                const catMax = {
                    ww: wwMaxTotal,
                    pt: ptMaxTotal,
                    qa: qaMaxTotal
                };
                const weights = {
                    ww: WW_WEIGHT,
                    pt: PT_WEIGHT,
                    qa: QA_WEIGHT
                };
                let initialSum = 0;

                cats.forEach(cat => {
                    // Sum all inputs for this category in the row
                    const inputs = tr.querySelectorAll(`input.score-input[data-cat="${cat}"]`);
                    let raw = 0;
                    inputs.forEach(inp => {
                        const v = parseFloat(inp.value);
                        if (!isNaN(v)) raw += v;
                    });
                    const maxTotal = catMax[cat] || 0;
                    const ps = maxTotal > 0 ? (raw / maxTotal) * 100 : null;
                    const ws = ps !== null ? ps * weights[cat] : null;
                    if (ws !== null) initialSum += ws;

                    const rawCell = tr.querySelector(`.js-total[data-cat="${cat}"][data-kind="raw"]`);
                    const psCell = tr.querySelector(`.js-total[data-cat="${cat}"][data-kind="ps"]`);
                    const wsCell = tr.querySelector(`.js-total[data-cat="${cat}"][data-kind="ws"]`);
                    if (rawCell) rawCell.textContent = raw > 0 ? fmt(raw) : '—';
                    if (psCell) psCell.textContent = ps !== null ? fmt(ps) : '—';
                    if (wsCell) wsCell.textContent = ws !== null ? fmt(ws) : '—';
                });

                // Initial grade cells (Total/PS/WS are same number per current policy)
                const initTotal = initialSum;
                tr.querySelectorAll('.js-initial').forEach(cell => {
                    cell.textContent = initTotal > 0 ? fmt(initTotal) : '—';
                });

                // Description mapping based on initial grade thresholds
                const descCell = tr.querySelector('.js-description');
                if (descCell) {
                    let desc = '0';
                    if (initTotal > 89) {
                        desc = 'Outstanding';
                    } else if (initTotal > 84) {
                        desc = 'Very Satisfactory';
                    } else if (initTotal > 79) {
                        desc = 'Satisfactory';
                    } else if (initTotal > 74) {
                        desc = 'Fairly Satisfactory';
                    } else {
                        desc = 'Did Not Meet Expectations';
                    }
                    descCell.textContent = desc;
                }
            }

            // Prevent invalid characters and enforce min/max during typing
            table.addEventListener('keydown', function(e) {
                const inp = e.target.closest('input.score-input');
                if (!inp) return;
                // Allow navigation keys and control combos
                const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End'];
                if (allowedKeys.includes(e.key) || (e.ctrlKey || e.metaKey)) return;
                // Block non-digits and block period, minus, and exponent
                if (!/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Enforce min/max and recompute on input
            table.addEventListener('input', function(e) {
                const inp = e.target.closest('input.score-input');
                if (!inp) return;
                const min = parseInt(inp.getAttribute('min') || '0', 10);
                const max = parseInt(inp.getAttribute('max') || '0', 10);
                // Strip non-digits
                inp.value = (inp.value || '').replace(/[^0-9]/g, '');
                // Clamp
                let val = inp.value === '' ? NaN : parseInt(inp.value, 10);
                if (!isNaN(val)) {
                    if (val < min) val = min;
                    if (max > 0 && val > max) val = max;
                    inp.value = String(val);
                }
                const tr = inp.closest('tr');
                if (tr) recomputeRow(tr);
            });

            // Clamp on blur to ensure empty becomes 0 within bounds
            table.addEventListener('blur', function(e) {
                const inp = e.target.closest('input.score-input');
                if (!inp) return;
                const min = parseInt(inp.getAttribute('min') || '0', 10);
                const max = parseInt(inp.getAttribute('max') || '0', 10);
                let val = inp.value === '' ? NaN : parseInt(inp.value, 10);
                if (isNaN(val)) val = 0;
                if (val < min) val = min;
                if (max > 0 && val > max) val = max;
                inp.value = String(val);
                const tr = inp.closest('tr');
                if (tr) recomputeRow(tr);
            }, true);

            // Initial pass to compute all rows at load
            table.querySelectorAll('tbody tr').forEach(recomputeRow);
        }

        // Click handler to open info modal
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-assessment-info');
            if (!btn) return;
            const idx = btn.getAttribute('data-assessment-index') || '—';
            const name = btn.getAttribute('data-assessment-name') || '—';
            const type = btn.getAttribute('data-assessment-type') || '—';
            const quarter = btn.getAttribute('data-assessment-quarter') || '—';
            const date = btn.getAttribute('data-assessment-date') || '—';
            const day = btn.getAttribute('data-assessment-day') || '—';
            const max = btn.getAttribute('data-assessment-max') || '—';
            const desc = btn.getAttribute('data-assessment-desc') || '—';

            document.getElementById('infoIndex').textContent = idx;
            document.getElementById('infoName').textContent = name;
            document.getElementById('infoType').textContent = type;
            document.getElementById('infoQuarter').textContent = quarter || '—';
            document.getElementById('infoDate').textContent = date;
            document.getElementById('infoDay').textContent = day;
            document.getElementById('infoMax').textContent = max;
            document.getElementById('infoDesc').textContent = desc || '—';

            const infoModal = new bootstrap.Modal(document.getElementById('modalAssessmentInfo'));
            infoModal.show();
        });
    })();
</script>
@endpush