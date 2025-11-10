@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Create Student Record</h4>
        <div class="text-muted small">Create a minimal student record for this class. You can update details later.</div>
    </div>
    <div>
        <a href="{{ route('teacher.class-records.show', $assignment) }}" class="btn btn-outline-secondary">&larr; Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('teacher.class-records.students.store', $assignment) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Last name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">First name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle name</label>
                    <input type="text" name="middle_name" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grade level (optional)</label>
                    <input type="text" name="grade_level" class="form-control" value="{{ $grade_level ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Term (optional)</label>
                    <input type="text" name="term" class="form-control" value="{{ $term ?? '' }}">
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('teacher.class-records.show', $assignment) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Student</button>
            </div>
        </form>
    </div>
</div>
@endsection
