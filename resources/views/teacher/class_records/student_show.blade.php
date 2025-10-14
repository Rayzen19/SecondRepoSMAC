@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Student Record: {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</h4>
        <div class="text-muted small">Class Record Details</div>
    </div>
    <div>
        <a href="{{ route('teacher.class-records.show', $assignment) }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i> Back to Class Record</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">Strand</div><div class="fw-semibold">{{ $details['strand'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Section</div><div class="fw-semibold">{{ $details['section'] ?? '—' }}</div></div>
            <div class="col-md-2"><div class="text-muted small">Grade</div><div class="fw-semibold">{{ $details['grade'] ?? '—' }}</div></div>
            <div class="col-md-4"><div class="text-muted small">Subject</div><div class="fw-semibold">{{ $details['subject'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Subject Teacher</div><div class="fw-semibold">{{ $details['subject_teacher'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Adviser</div><div class="fw-semibold">{{ $details['adviser'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">School Year</div><div class="fw-semibold">{{ $details['school_year'] ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Semester</div><div class="fw-semibold">{{ $details['semester'] ?? '—' }}</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Grades</h5>
    </div>
    <div class="card-body">
        @if(empty($quartersData) || count($quartersData) === 0)
            <p class="text-muted mb-0">No assessment data yet for this student.</p>
        @else
            @foreach($quartersData as $q)
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">{{ $q['label'] }}</h6>
                        <div class="text-muted small">Weights — WW: {{ rtrim(rtrim(number_format(($weights['ww'] ?? 0) * 100, 2), '0'), '.') }}% • PT: {{ rtrim(rtrim(number_format(($weights['pt'] ?? 0) * 100, 2), '0'), '.') }}% • QA: {{ rtrim(rtrim(number_format(($weights['qa'] ?? 0) * 100, 2), '0'), '.') }}%</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%">Category</th>
                                    <th>Assessments</th>
                                    <th class="text-center" style="width: 10%">Total</th>
                                    <th class="text-center" style="width: 10%">PS</th>
                                    <th class="text-center" style="width: 10%">WS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                    $renderTable = function($items) use($fmt){
                                        if (empty($items) || count($items) === 0) return '<span class="text-muted">—</span>';
                                        $html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0 align-middle">';
                                        $html .= '<thead><tr>'
                                            .'<th class="text-center" style="width:6%">#</th>'
                                            .'<th>Name</th>'
                                            .'<th class="text-center" style="width:12%">Date</th>'
                                            .'<th class="text-center" style="width:10%">Score</th>'
                                            .'<th class="text-center" style="width:10%">Over</th>'
                                            .'</tr></thead><tbody>';
                                        foreach ($items as $i => $it) {
                                            $idx = $i + 1;
                                            $name = htmlspecialchars($it['name'] ?? '');
                                            $date = !empty($it['date']) ? htmlspecialchars($it['date']) : '—';
                                            $score = $fmt($it['score'] ?? 0);
                                            $max = $fmt($it['max'] ?? 0);
                                            $desc = !empty($it['description']) ? '<div class="text-muted small">'.htmlspecialchars($it['description']).'</div>' : '';
                                            $html .= '<tr>'
                                                .'<td class="text-center">'.$idx.'</td>'
                                                .'<td>'.$name.$desc.'</td>'
                                                .'<td class="text-center">'.$date.'</td>'
                                                .'<td class="text-center">'.$score.'</td>'
                                                .'<td class="text-center">'.$max.'</td>'
                                                .'</tr>';
                                        }
                                        $html .= '</tbody></table></div>';
                                        return $html;
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold">Written Work</td>
                                    <td>{!! $renderTable($q['ww']['records']) !!}</td>
                                    <td class="text-center">{{ $fmt($q['ww']['raw_total']) }} / {{ $fmt($q['ww']['max_total']) }}</td>
                                    <td class="text-center">{{ isset($q['ww']['ps']) ? $fmt($q['ww']['ps']) : '—' }}</td>
                                    <td class="text-center">{{ isset($q['ww']['ws']) ? $fmt($q['ww']['ws']) : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Performance Task</td>
                                    <td>{!! $renderTable($q['pt']['records']) !!}</td>
                                    <td class="text-center">{{ $fmt($q['pt']['raw_total']) }} / {{ $fmt($q['pt']['max_total']) }}</td>
                                    <td class="text-center">{{ isset($q['pt']['ps']) ? $fmt($q['pt']['ps']) : '—' }}</td>
                                    <td class="text-center">{{ isset($q['pt']['ws']) ? $fmt($q['pt']['ws']) : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Quarterly Assessment</td>
                                    <td>{!! $renderTable($q['qa']['records']) !!}</td>
                                    <td class="text-center">{{ $fmt($q['qa']['raw_total']) }} / {{ $fmt($q['qa']['max_total']) }}</td>
                                    <td class="text-center">{{ isset($q['qa']['ps']) ? $fmt($q['qa']['ps']) : '—' }}</td>
                                    <td class="text-center">{{ isset($q['qa']['ws']) ? $fmt($q['qa']['ws']) : '—' }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-center fw-semibold">Initial Grade</td>
                                    @php $initial = $q['initial']['total'] ?? 0; @endphp
                                    <td colspan="2" class="fw-semibold">{{ $fmt($initial) }}</td>
                                    <td colspan="1" class="text-center fw-semibold">Description</td>
                                    <td class="text-center fw-semibold">{{ $q['initial']['description'] ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
