@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Edit Strand Link</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subjects.show', $pivot->subject_id) }}">Subject Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Strand Link</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subjects.show', $pivot->subject_id) }}" class="btn btn-outline-light border d-flex align-items-center"><i class="ti ti-arrow-left me-2"></i>Back</a>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script>
    (function() {
        function attachSumValidator(scope) {
            const w = scope.querySelector('input[name="written_works_percentage"]');
            const p = scope.querySelector('input[name="performance_tasks_percentage"]');
            const q = scope.querySelector('input[name="quarterly_assessment_percentage"]');
            if (!w || !p || !q) return;
            const submitBtn = scope.querySelector('button[type="submit"]');
            const warn = document.createElement('div');
            warn.className = 'text-warning small mt-1';
            q.parentElement.appendChild(warn);
            const update = () => {
                const sum = (parseFloat(w.value || '0') + parseFloat(p.value || '0') + parseFloat(q.value || '0'));
                const ok = Math.abs(sum - 100) < 0.001;
                warn.textContent = ok ? '' : `The percentages currently sum to ${sum.toFixed(2)}%. They must total 100%.`;
                if (submitBtn) submitBtn.disabled = !ok;
            };
            ['input','change'].forEach(evt => {
                w.addEventListener(evt, update);
                p.addEventListener(evt, update);
                q.addEventListener(evt, update);
            });
            update();
        }
        document.addEventListener('DOMContentLoaded', function() {
            attachSumValidator(document);
        });
    })();
</script>
@endpush

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <div class="mb-4">
                <h5 class="mb-3">Subject & Strand</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Subject:</strong> {{ $pivot->subject?->code }} — {{ $pivot->subject?->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Strand:</strong> {{ $pivot->strand?->code }} — {{ $pivot->strand?->name }}</div>
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Grading & Status</h5>

            <form action="{{ route('admin.strand-subjects.update', $pivot) }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_to" value="{{ request('return_to') }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Semestral Period</label>
                            <select name="semestral_period" class="form-select">
                                <option value="">Select Semestral Period</option>
                                <option value="1st" @selected(old('semestral_period', $pivot->semestral_period) == '1st')>1st</option>
                                <option value="2nd" @selected(old('semestral_period', $pivot->semestral_period) == '2nd')>2nd</option>
                            </select>
                            @error('semestral_period')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Written Works % <span class="text-danger">*</span></label>
                            <input type="number" name="written_works_percentage" class="form-control" value="{{ old('written_works_percentage', $pivot->written_works_percentage) }}" min="0" max="100" step="0.01" required>
                            @error('written_works_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Performance Tasks % <span class="text-danger">*</span></label>
                            <input type="number" name="performance_tasks_percentage" class="form-control" value="{{ old('performance_tasks_percentage', $pivot->performance_tasks_percentage) }}" min="0" max="100" step="0.01" required>
                            @error('performance_tasks_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Quarterly Assessment % <span class="text-danger">*</span></label>
                            <input type="number" name="quarterly_assessment_percentage" class="form-control" value="{{ old('quarterly_assessment_percentage', $pivot->quarterly_assessment_percentage) }}" min="0" max="100" step="0.01" required>
                            @error('quarterly_assessment_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $pivot->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.subjects.show', $pivot->subject_id) }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
