@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Assign Subject to Strand</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $academicYear->id) }}">Academic Year Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assign Subject</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.academic-year-strand-subjects.store') }}" onsubmit="return validateTotals();">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">

                <div class="row g-4">
                    <div class="col-12">
                        <h5 class="mb-3">General Details</h5>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Strand</label>
                        @php
                        $selectedStrandId = old('strand_id', isset($strand) ? $strand->id : ($strands->first()->id ?? ''));
                        $selectedStrand = isset($strand)
                        ? $strand
                        : ($strands->firstWhere('id', $selectedStrandId) ?? null);
                        @endphp

                        <input type="hidden" name="strand_id" value="{{ $selectedStrandId }}">
                        <input type="hidden" name="academic_year_strand_adviser_id" value="{{ $academicYearStrandAdviser->id }}">

                        <select class="form-select" disabled>
                            @if($selectedStrand)
                            <option value="{{ $selectedStrand->id }}">{{ $selectedStrand->code }} — {{ $selectedStrand->name }}</option>
                            @else
                            <option value="">No Strand Selected</option>
                            @endif
                        </select>

                        @error('strand_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <input type="text" class="form-control" value="{{ $academicYear->name }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Semestral Period</label>
                        <input type="text" class="form-control" value="{{ $academicYear->semester }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Subject</label>
                        <select id="subjectSelect" name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->subject_id }}"
                                data-semestral_period="{{ $subject->semestral_period }}"
                                data-written_works_percentage="{{ $subject->written_works_percentage }}"
                                data-performance_tasks_percentage="{{ $subject->performance_tasks_percentage }}"
                                data-quarterly_assessment_percentage="{{ $subject->quarterly_assessment_percentage }}"
                                @selected(old('subject_id')==$subject->id)
                                >{{ $subject->subject->code }} — {{ $subject->subject->name }}</option>
                            @endforeach
                        </select>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const subjectSelect = document.getElementById('subjectSelect');
                                const wwInput = document.querySelector('input[name="written_works_percentage"]');
                                const ptInput = document.querySelector('input[name="performance_tasks_percentage"]');
                                const qaInput = document.querySelector('input[name="quarterly_assessment_percentage"]');

                                function updateWrittenWorks() {
                                    if (!subjectSelect || !wwInput) return;
                                    const opt = subjectSelect.selectedOptions[0];
                                    const dataVal = opt?.dataset?.written_works_percentage;
                                    if (dataVal !== undefined && dataVal !== '') {
                                        wwInput.value = dataVal;
                                    }
                                }

                                function updatePerformanceTasks() {
                                    if (!subjectSelect || !ptInput) return;
                                    const opt = subjectSelect.selectedOptions[0];
                                    const dataVal = opt?.dataset?.performance_tasks_percentage;
                                    if (dataVal !== undefined && dataVal !== '') {
                                        ptInput.value = dataVal;
                                    }
                                }

                                function updateQuarterlyAssessment() {
                                    if (!subjectSelect || !qaInput) return;
                                    const opt = subjectSelect.selectedOptions[0];
                                    const dataVal = opt?.dataset?.quarterly_assessment_percentage;
                                    if (dataVal !== undefined && dataVal !== '') {
                                        qaInput.value = dataVal;
                                    }
                                }

                                if (subjectSelect && wwInput) {
                                    subjectSelect.addEventListener('change', updateWrittenWorks);
                                    updateWrittenWorks(); // initialize if a subject is preselected
                                }

                                if (subjectSelect && ptInput) {
                                    subjectSelect.addEventListener('change', updatePerformanceTasks);
                                    updatePerformanceTasks(); // initialize if a subject is preselected
                                }

                                if (subjectSelect && qaInput) {
                                    subjectSelect.addEventListener('change', updateQuarterlyAssessment);
                                    updateQuarterlyAssessment(); // initialize if a subject is preselected
                                }
                            });
                        </script>

                        @error('subject_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Subject Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Select Subject Teacher</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('teacher_id')==$teacher->id)>{{ $teacher->employee_number }} — {{ $teacher->last_name }}, {{ $teacher->first_name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <h5 class="mt-3">Grading Percentages</h5>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Written Works %</label>
                        <input type="number" step="0.01" min="0" max="100" name="written_works_percentage" value="{{ old('written_works_percentage', 20) }}" class="form-control" required>
                        @error('written_works_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Performance Tasks %</label>
                        <input type="number" step="0.01" min="0" max="100" name="performance_tasks_percentage" value="{{ old('performance_tasks_percentage', 60) }}" class="form-control" required>
                        @error('performance_tasks_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quarterly Assessment %</label>
                        <input type="number" step="0.01" min="0" max="100" name="quarterly_assessment_percentage" value="{{ old('quarterly_assessment_percentage', 20) }}" class="form-control" required>
                        @error('quarterly_assessment_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <h6 class="mt-3">Based Grade Percentages (optional)</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Written Works Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="written_works_based_grade_percentage" value="{{ old('written_works_based_grade_percentage', 0) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Performance Tasks Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="performance_tasks_based_grade_percentage" value="{{ old('performance_tasks_based_grade_percentage', 70) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quarterly Assessment Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="quarterly_assessment_based_grade_percentage" value="{{ old('quarterly_assessment_based_grade_percentage', 70) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Overall Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="over_all_based_grade_percentage" value="{{ old('over_all_based_grade_percentage', 0) }}" class="form-control">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-2"></i>Assign</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function validateTotals() {
        const ww = parseFloat(document.querySelector('[name=written_works_percentage]').value || 0);
        const pt = parseFloat(document.querySelector('[name=performance_tasks_percentage]').value || 0);
        const qa = parseFloat(document.querySelector('[name=quarterly_assessment_percentage]').value || 0);
        const total = ww + pt + qa;
        if (Math.abs(total - 100) > 0.0001) {
            alert('Written Works + Performance Tasks + Quarterly Assessment must total 100%.');
            return false;
        }
        return true;
    }
</script>
@endsection