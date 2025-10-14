@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Edit Subject Assignment</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $assignment->academic_year_id) }}">Academic Year Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Assignment</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.academic-year-strand-subjects.update', $assignment) }}" onsubmit="return validateTotals();">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <input type="text" class="form-control" value="{{ $assignment->academicYear?->name }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Strand</label>
                        <input type="text" class="form-control" value="{{ $assignment->strand?->code }} — {{ $assignment->strand?->name }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" value="{{ $assignment->subject?->code }} — {{ $assignment->subject?->name }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id', $assignment->teacher_id)==$teacher->id)>{{ $teacher->employee_number }} — {{ $teacher->last_name }}, {{ $teacher->first_name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <h5 class="mt-3">Grading Percentages</h5>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Written Works %</label>
                        <input type="number" step="0.01" min="0" max="100" name="written_works_percentage" value="{{ old('written_works_percentage', $assignment->written_works_percentage) }}" class="form-control" required>
                        @error('written_works_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Performance Tasks %</label>
                        <input type="number" step="0.01" min="0" max="100" name="performance_tasks_percentage" value="{{ old('performance_tasks_percentage', $assignment->performance_tasks_percentage) }}" class="form-control" required>
                        @error('performance_tasks_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quarterly Assessment %</label>
                        <input type="number" step="0.01" min="0" max="100" name="quarterly_assessment_percentage" value="{{ old('quarterly_assessment_percentage', $assignment->quarterly_assessment_percentage) }}" class="form-control" required>
                        @error('quarterly_assessment_percentage')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <h6 class="mt-3">Based Grade Percentages (optional)</h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Written Works Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="written_works_based_grade_percentage" value="{{ old('written_works_based_grade_percentage', $assignment->written_works_based_grade_percentage) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Performance Tasks Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="performance_tasks_based_grade_percentage" value="{{ old('performance_tasks_based_grade_percentage', $assignment->performance_tasks_based_grade_percentage) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quarterly Assessment Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="quarterly_assessment_based_grade_percentage" value="{{ old('quarterly_assessment_based_grade_percentage', $assignment->quarterly_assessment_based_grade_percentage) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Overall Based Grade %</label>
                        <input type="number" step="0.01" min="0" max="100" name="over_all_based_grade_percentage" value="{{ old('over_all_based_grade_percentage', $assignment->over_all_based_grade_percentage) }}" class="form-control">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i>Save</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validateTotals(){
  const ww = parseFloat(document.querySelector('[name=written_works_percentage]').value||0);
  const pt = parseFloat(document.querySelector('[name=performance_tasks_percentage]').value||0);
  const qa = parseFloat(document.querySelector('[name=quarterly_assessment_percentage]').value||0);
  const total = ww+pt+qa;
  if (Math.abs(total-100) > 0.0001){
    alert('Written Works + Performance Tasks + Quarterly Assessment must total 100%.');
    return false;
  }
  return true;
}
</script>
@endsection
