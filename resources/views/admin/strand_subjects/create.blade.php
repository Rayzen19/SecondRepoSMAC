@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Link Subject and Strand</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                @if(!empty($subject))
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.show', $subject) }}">Subject Details</a></li>
                @elseif(!empty($strand))
                    <li class="breadcrumb-item"><a href="{{ route('admin.strands.index') }}">Strands</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.strands.show', $strand) }}">Strand Details</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Create Link</li>
            </ol>
        </nav>
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

            @if(!empty($subject))
                <div class="mb-4">
                    <h5 class="mb-3">Subject Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Code:</strong> {{ $subject->code ?? '—' }}</div>
                            <div class="mb-2"><strong>Name:</strong> {{ $subject->name ?? '—' }}</div>
                            <div class="mb-2"><strong>Type:</strong> {{ $subject->type ?? $subject->subject_type ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Units:</strong> {{ $subject->units ?? '—' }}</div>
                            <div class="mb-2"><strong>Semester:</strong> {{ $subject->semester ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            @elseif(!empty($strand))
                <div class="mb-4">
                    <h5 class="mb-3">Strand Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Code:</strong> {{ $strand->code ?? '—' }}</div>
                            <div class="mb-2"><strong>Name:</strong> {{ $strand->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            @endif
            
            <hr>
            <h5 class="mb-3">Link Details</h5>

            <form action="{{ route('admin.strand-subjects.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @if(!empty($subject))
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <input type="hidden" name="return_to" value="subject">
                @endif
                @if(!empty($strand))
                    <input type="hidden" name="strand_id" value="{{ $strand->id }}">
                    <input type="hidden" name="return_to" value="strand">
                @endif

                <div class="row">
                    @if(empty($strand))
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Strand <span class="text-danger">*</span></label>
                                <select name="strand_id" class="form-select" @if(empty($subject)) required @endif>
                                    <option value="">Select Strand</option>
                                    @foreach($strands as $s)
                                        <option value="{{ $s->id }}" @selected(old('strand_id')==$s->id)>
                                            {{ $s->code }} — {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('strand_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endif
                    @if(empty($subject))
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" class="form-select" @if(empty($strand)) required @endif>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subj)
                                        <option value="{{ $subj->id }}" @selected(old('subject_id')==$subj->id)>
                                            {{ $subj->code }} — {{ $subj->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select">
                                <option value="">Select Grade Level</option>
                                <option value="11" @selected(old('grade_level') == '11')>Grade 11</option>
                                <option value="12" @selected(old('grade_level') == '12')>Grade 12</option>
                            </select>
                            @error('grade_level')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Semestral Period</label>
                            <select name="semestral_period" class="form-select">
                                <option value="">Select Semestral Period</option>
                                @php $defaultSem = old('semestral_period', $subject->semester ?? ''); @endphp
                                <option value="1st" @selected($defaultSem == '1st')>1st</option>
                                <option value="2nd" @selected($defaultSem == '2nd')>2nd</option>
                            </select>
                            @error('semestral_period')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Written Works % <span class="text-danger">*</span></label>
                            <input type="number" name="written_works_percentage" class="form-control" value="{{ old('written_works_percentage', 20) }}" min="0" max="100" step="0.01" required>
                            @error('written_works_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Performance Tasks % <span class="text-danger">*</span></label>
                            <input type="number" name="performance_tasks_percentage" class="form-control" value="{{ old('performance_tasks_percentage', 60) }}" min="0" max="100" step="0.01" required>
                            @error('performance_tasks_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Quarterly Assessment % <span class="text-danger">*</span></label>
                            <input type="number" name="quarterly_assessment_percentage" class="form-control" value="{{ old('quarterly_assessment_percentage', 20) }}" min="0" max="100" step="0.01" required>
                            @error('quarterly_assessment_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                @php
                    $cancelRoute = !empty($subject)
                        ? route('admin.subjects.show', $subject)
                        : (!empty($strand) ? route('admin.strands.show', $strand) : route('admin.dashboard'));
                @endphp

                <a href="{{ $cancelRoute }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>
    </div>
</div>
@endsection
