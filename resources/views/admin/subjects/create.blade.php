@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Subject</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subjects.index') }}">Subject Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Subject Create</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
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
            <form action="{{ route('admin.subjects.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Strand <span class="text-danger">*</span></label>
                                <select name="strand_id" class="form-select" required>
                                    <option value="">Select Strand</option>
                                    @foreach($strands as $strand)
                                        <option value="{{ $strand->id }}" @selected(old('strand_id') == $strand->id)>
                                            {{ $strand->code }} — {{ $strand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('strand_id')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                                <small class="text-muted">Subject will be linked to this strand</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g., ICT201" required>
                                @error('code')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Computer Programming (JAVA)" required>
                                @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Brief description of the subject">{{ old('description') }}</textarea>
                                @error('description')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" name="units" class="form-control" value="{{ old('units', 3) }}" min="0" required>
                                @error('units')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="core" {{ old('type')=='core'?'selected':'' }}>Core</option>
                                    <option value="applied" {{ old('type')=='applied'?'selected':'' }}>Applied</option>
                                    <option value="specialized" {{ old('type')=='specialized'?'selected':'' }}>Specialized</option>
                                </select>
                                @error('type')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    <option value="1st" {{ old('semester', '1st')=='1st'?'selected':'' }}>1st Semester</option>
                                    <option value="2nd" {{ old('semester')=='2nd'?'selected':'' }}>2nd Semester</option>
                                </select>
                                @error('semester')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <!-- Strand-Subject Linking Configuration -->
                        <div class="col-md-12">
                            <hr class="my-4">
                            <h6 class="mb-3">Grading Configuration for Strand</h6>
                            <small class="text-muted d-block mb-3">Configure how this subject will be graded for the selected strand.</small>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Written Works % <span class="text-danger">*</span></label>
                                <input type="number" name="written_works_percentage" class="form-control" value="{{ old('written_works_percentage', 20) }}" min="0" max="100" step="0.01" required>
                                @error('written_works_percentage')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Performance Tasks % <span class="text-danger">*</span></label>
                                <input type="number" name="performance_tasks_percentage" class="form-control" value="{{ old('performance_tasks_percentage', 60) }}" min="0" max="100" step="0.01" required>
                                @error('performance_tasks_percentage')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Quarterly Assessment % <span class="text-danger">*</span></label>
                                <input type="number" name="quarterly_assessment_percentage" class="form-control" value="{{ old('quarterly_assessment_percentage', 20) }}" min="0" max="100" step="0.01" required>
                                @error('quarterly_assessment_percentage')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Note:</strong> The three percentages must total 100%. The subject will be automatically linked to the selected strand with these grading weights.
                            </div>
                        </div>
                    </div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
