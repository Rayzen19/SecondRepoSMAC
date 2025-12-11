@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Teacher Assignments</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teacher</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.teachers.index') }}">Teacher Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Assign</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <!-- Teacher Info Card -->
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="mb-2">{{ $teacher->last_name }}, {{ $teacher->first_name }} {{ $teacher->middle_name }}</h4>
            <p class="text-muted mb-0">{{ $teacher->employee_number }} | {{ $teacher->department }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Assignment Form Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Assigning Teacher to Subject</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="ti ti-info-circle me-2"></i>
                <strong>Limit:</strong> Each teacher can be assigned to a maximum of <strong>3 subjects</strong> per academic year.
            </div>
            <form action="{{ route('admin.teachers.assignments.store', $teacher) }}" method="POST" id="assignmentForm">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" @selected(old('academic_year_id')==$year->id)>
                                        {{ $year->name }} @if($year->semester) - {{ $year->semester }} @endif
                                        @if($year->is_active) <span class="badge bg-success">Active</span> @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Specialization <span class="text-danger">*</span></label>
                            <!-- Text input for filtering subjects -->
                            <input type="text" name="specialization" id="specializationInput" class="form-control" placeholder="Type to filter subjects..." value="{{ old('specialization', $teacher->specialization) }}">

                            <!-- Hidden Strand field preserved for backend compatibility -->
                            <select name="strand_id" class="form-select d-none" required>
                                @php($defaultStrandId = old('strand_id') ?? ($strands->first()->id ?? ''))
                                <option value="">Select Strand</option>
                                @foreach($strands as $strand)
                                    <option value="{{ $strand->id }}" {{ (string)$defaultStrandId === (string)$strand->id ? 'selected' : '' }}>
                                        {{ $strand->code }} - {{ $strand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('strand_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subjectSelect" class="form-select" required>
                                <option value="">Select Subject</option>
                                @php($qualifiedIds = $teacher->subjects->pluck('id')->toArray())
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" data-name="{{ strtolower($subject->name) }}" data-code="{{ strtolower($subject->code) }}" data-qualified="{{ in_array($subject->id, $qualifiedIds) ? '1' : '0' }}" @selected(old('subject_id')==$subject->id)>
                                        {{ $subject->code }} - {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn bg-info d-block w-100">
                                <i class="ti ti-plus me-1"></i>Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('assignmentForm');
        const academicYearSelect = document.querySelector('select[name="academic_year_id"]');
        const submitBtn = form?.querySelector('button[type="submit"]');
        const assignmentCounts = @json($assignmentCounts ?? []);

        // Check assignment limit on academic year change
        function checkAssignmentLimit() {
            if (!academicYearSelect || !submitBtn) return;
            const selectedYearId = academicYearSelect.value;
            const count = assignmentCounts[selectedYearId] || 0;
            
            if (count >= 3) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ti ti-lock me-1"></i>Limit Reached (3/3)';
                submitBtn.classList.remove('bg-info');
                submitBtn.classList.add('btn-secondary');
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-plus me-1"></i>Submit (' + count + '/3)';
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('bg-info');
            }
        }

        if (academicYearSelect) {
            academicYearSelect.addEventListener('change', checkAssignmentLimit);
            checkAssignmentLimit(); // Check on page load
        }

        const specialization = document.getElementById('specializationInput');
        const subjectSelect = document.getElementById('subjectSelect');
        if (!specialization || !subjectSelect) return;

        // Preserve a copy of all options for reset/filtering
        const allOptions = Array.from(subjectSelect.options).map(o => o.cloneNode(true));

        function filterSubjects() {
            const spec = (specialization.value || '').toLowerCase().trim();
            const currentValue = subjectSelect.value;

            // Reset to placeholder + filter
            subjectSelect.innerHTML = '';
            // Always keep the first placeholder
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select Subject';
            subjectSelect.appendChild(placeholder);

            let added = 0;
            allOptions.forEach(opt => {
                if (!opt.value) return; // skip placeholder copies
                const name = opt.getAttribute('data-name') || '';
                const code = opt.getAttribute('data-code') || '';
                const qualified = opt.getAttribute('data-qualified') === '1';
                const matchesKeyword = spec && (name.includes(spec) || code.includes(spec));
                // Show if no specialization chosen; otherwise filter by specialization match
                const show = !spec ? true : matchesKeyword;
                if (show) {
                    subjectSelect.appendChild(opt.cloneNode(true));
                    added++;
                }
            });

            // Try to keep previous selection when possible
            if (currentValue) {
                subjectSelect.value = currentValue;
            }

            // If nothing matched (other than placeholder), fallback to all
            if (subjectSelect.options.length <= 1) {
                allOptions.forEach(opt => { if (opt.value) subjectSelect.appendChild(opt.cloneNode(true)); });
            }
        }

        specialization.addEventListener('input', filterSubjects);
        // Auto-filter on load if a specialization is preselected
        if (specialization.value) filterSubjects();
    })();
</script>
@endpush
