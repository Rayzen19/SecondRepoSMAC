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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Grades</h5>
        @if(!empty($quartersData))
            <button type="submit" form="formSaveScores" class="btn btn-primary btn-sm"><i class="ti ti-device-floppy me-1"></i> Save Scores</button>
        @endif
    </div>
    <div class="card-body">
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
        <form id="formSaveScores" action="{{ route('teacher.class-records.scores.store', $assignment) }}" method="post">
            @csrf
        @if(empty($quartersData) || count($quartersData) === 0)
            <p class="text-muted mb-0">No assessment data yet for this student.</p>
        @else
            @foreach($quartersData as $q)
                @php $qKey = $q['key'] ?? 'final'; @endphp
                <div class="mb-4" data-quarter="{{ $qKey }}" data-ww-max="{{ (float)($q['ww']['max_total'] ?? 0) }}" data-pt-max="{{ (float)($q['pt']['max_total'] ?? 0) }}" data-qa-max="{{ (float)($q['qa']['max_total'] ?? 0) }}" data-ww-weight="{{ (float)($weights['ww'] ?? 0) }}" data-pt-weight="{{ (float)($weights['pt'] ?? 0) }}" data-qa-weight="{{ (float)($weights['qa'] ?? 0) }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">{{ $q['label'] }}</h6>
                        @php
                            $termKey = ($q['key'] ?? null) === '1st' ? 'first-semester' : (($q['key'] ?? null) === '2nd' ? 'second-semester' : 'semester-final');
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-muted small me-2">Weights — WW: {{ rtrim(rtrim(number_format(($weights['ww'] ?? 0) * 100, 2), '0'), '.') }}% • PT: {{ rtrim(rtrim(number_format(($weights['pt'] ?? 0) * 100, 2), '0'), '.') }}% • QA: {{ rtrim(rtrim(number_format(($weights['qa'] ?? 0) * 100, 2), '0'), '.') }}%</div>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Add assessment">
                                <button type="button" class="btn btn-outline-primary js-open-add" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-term="{{ $termKey }}" data-category="written_work" data-title="Add Written Work">
                                    <i class="ti ti-plus"></i> WW
                                </button>
                                <button type="button" class="btn btn-outline-primary js-open-add" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-term="{{ $termKey }}" data-category="performance_task" data-title="Add Performance Task">
                                    <i class="ti ti-plus"></i> PT
                                </button>
                                <button type="button" class="btn btn-outline-primary js-open-add" data-bs-toggle="modal" data-bs-target="#modalAddAssessment" data-term="{{ $termKey }}" data-category="quarterly_assessment" data-title="Add Quarterly Assessment">
                                    <i class="ti ti-plus"></i> QA
                                </button>
                            </div>
                        </div>
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
                                    $renderTable = function($items, $cat, $quarterKey) use($fmt, $student){
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
                                            $scoreVal = isset($it['score']) ? (float)$it['score'] : 0;
                                            $maxVal = isset($it['max']) ? (float)$it['max'] : 0;
                                            $input = '<input type="number" name="scores['.($it['id'] ?? 0).']['.$student->id.']" class="form-control form-control-sm text-center js-score-input" value="'.$scoreVal.'" min="0" max="'.$maxVal.'" step="1" data-cat="'.$cat.'" data-quarter="'.$quarterKey.'">';
                                            $max = $fmt($maxVal);
                                            $desc = !empty($it['description']) ? '<div class="text-muted small">'.htmlspecialchars($it['description']).'</div>' : '';
                                            $html .= '<tr>'
                                                .'<td class="text-center">'.$idx.'</td>'
                                                .'<td>'.$name.$desc.'</td>'
                                                .'<td class="text-center">'.$date.'</td>'
                                                .'<td class="text-center">'.$input.'</td>'
                                                .'<td class="text-center">'.$max.'</td>'
                                                .'</tr>';
                                        }
                                        $html .= '</tbody></table></div>';
                                        return $html;
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold">Written Work</td>
                                    <td>{!! $renderTable($q['ww']['records'], 'ww', $qKey) !!}</td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="raw">{{ $fmt($q['ww']['raw_total']) }} / {{ $fmt($q['ww']['max_total']) }}</td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="ps">{{ isset($q['ww']['ps']) ? $fmt($q['ww']['ps']) : '—' }}</td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="ws">{{ isset($q['ww']['ws']) ? $fmt($q['ww']['ws']) : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Performance Task</td>
                                    <td>{!! $renderTable($q['pt']['records'], 'pt', $qKey) !!}</td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="raw">{{ $fmt($q['pt']['raw_total']) }} / {{ $fmt($q['pt']['max_total']) }}</td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="ps">{{ isset($q['pt']['ps']) ? $fmt($q['pt']['ps']) : '—' }}</td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="ws">{{ isset($q['pt']['ws']) ? $fmt($q['pt']['ws']) : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Quarterly Assessment</td>
                                    <td>{!! $renderTable($q['qa']['records'], 'qa', $qKey) !!}</td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="raw">{{ $fmt($q['qa']['raw_total']) }} / {{ $fmt($q['qa']['max_total']) }}</td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="ps">{{ isset($q['qa']['ps']) ? $fmt($q['qa']['ps']) : '—' }}</td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="ws">{{ isset($q['qa']['ws']) ? $fmt($q['qa']['ws']) : '—' }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-center fw-semibold">Initial Grade</td>
                                    @php $initial = $q['initial']['total'] ?? 0; @endphp
                                    <td colspan="2" class="fw-semibold js-initial">{{ $fmt($initial) }}</td>
                                    <td colspan="1" class="text-center fw-semibold">Description</td>
                                    <td class="text-center fw-semibold js-description">{{ $q['initial']['description'] ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Save Scores</button>
            </div>
        @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    function fmt(n){
        if(n===null || isNaN(n)) return '—';
        const s = Number(n).toFixed(2).replace(/\.00$/, '');
        return s.replace(/(\.[1-9])0$/, '$1');
    }

    function recompute(block){
        const wwMax = parseFloat(block.getAttribute('data-ww-max')||'0')||0;
        const ptMax = parseFloat(block.getAttribute('data-pt-max')||'0')||0;
        const qaMax = parseFloat(block.getAttribute('data-qa-max')||'0')||0;
        const wWW = parseFloat(block.getAttribute('data-ww-weight')||'0')||0;
        const wPT = parseFloat(block.getAttribute('data-pt-weight')||'0')||0;
        const wQA = parseFloat(block.getAttribute('data-qa-weight')||'0')||0;

        const cats = [
            {key:'ww', max: wwMax, weight: wWW},
            {key:'pt', max: ptMax, weight: wPT},
            {key:'qa', max: qaMax, weight: wQA},
        ];

        let initial = 0;
        cats.forEach(c => {
            let raw = 0;
            block.querySelectorAll(`input.js-score-input[data-cat="${c.key}"]`).forEach(inp => {
                const v = parseFloat(inp.value);
                if(!isNaN(v)) raw += v;
            });
            const ps = c.max > 0 ? (raw / c.max) * 100 : null;
            const ws = ps !== null ? ps * c.weight : null;
            if(ws !== null) initial += ws;
            const rawCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="raw"]`);
            const psCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="ps"]`);
            const wsCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="ws"]`);
            if(rawCell){ rawCell.textContent = `${raw>0?fmt(raw):'—'}${c.max>0?` / ${fmt(c.max)}`:''}`; }
            if(psCell){ psCell.textContent = ps!==null?fmt(ps):'—'; }
            if(wsCell){ wsCell.textContent = ws!==null?fmt(ws):'—'; }
        });

        const initCell = block.querySelector('.js-initial');
        if(initCell) initCell.textContent = initial>0?fmt(initial):'—';
        const descCell = block.querySelector('.js-description');
        if(descCell){
            let d = '0';
            if (initial > 89) d = 'Outstanding';
            else if (initial > 84) d = 'Very Satisfactory';
            else if (initial > 79) d = 'Satisfactory';
            else if (initial > 74) d = 'Fairly Satisfactory';
            else if (initial > 59) d = 'Did Not Meet Expectations';
            descCell.textContent = d;
        }
    }

    document.querySelectorAll('.mb-4[data-quarter]').forEach(block => {
        block.addEventListener('input', e => {
            const inp = e.target.closest('input.js-score-input');
            if(!inp) return;
            // clamp to min/max and digits only
            const min = parseInt(inp.getAttribute('min')||'0', 10);
            const max = parseInt(inp.getAttribute('max')||'0', 10);
            inp.value = (inp.value||'').replace(/[^0-9]/g, '');
            let v = inp.value === '' ? 0 : parseInt(inp.value, 10);
            if(v < min) v = min;
            if(max>0 && v>max) v = max;
            inp.value = String(v);
            recompute(block);
        });
        // initial compute on page load
        recompute(block);
    });
});
</script>
@endpush

@push('modals')
<div class="modal fade" id="modalAddAssessment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddAssessmentTitle">Add Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddAssessment" action="{{ route('teacher.class-records.assessments.store', $assignment) }}" method="post">
                @csrf
                <input type="hidden" name="term" id="modalAddTerm" value="first-semester">
                <input type="hidden" name="category" id="modalAddCategory" value="written_work">
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
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.js-open-add').forEach(btn => {
            btn.addEventListener('click', function(){
                document.getElementById('modalAddAssessmentTitle').textContent = this.dataset.title || 'Add Assessment';
                document.getElementById('modalAddTerm').value = this.dataset.term || 'first-semester';
                document.getElementById('modalAddCategory').value = this.dataset.category || 'written_work';
            });
        });
    });
    </script>
</div>
@endpush
