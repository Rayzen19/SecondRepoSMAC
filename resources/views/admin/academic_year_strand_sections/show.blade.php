@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Section Assignment Details</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $assignment->academic_year_id) }}">Academic Year Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assignment</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <input type="text" class="form-control" value="{{ $assignment->academicYear?->name }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Strand</label>
                    <input type="text" class="form-control" value="{{ $assignment->strand?->code }} — {{ $assignment->strand?->name }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Section</label>
                    <input type="text" class="form-control" value="{{ $assignment->section?->grade }} — {{ $assignment->section?->name }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Adviser Teacher</label>
                    <input type="text" class="form-control" value="{{ $assignment->adviserTeacher ? ($assignment->adviserTeacher?->employee_number . ' — ' . $assignment->adviserTeacher?->last_name . ', ' . $assignment->adviserTeacher?->first_name) : '—' }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div>
                        @if($assignment->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.academic-year-strand-sections.edit', $assignment) }}" class="btn btn-primary"><i class="ti ti-edit me-2"></i>Edit</a>
                <a href="{{ route('admin.academic-years.show', $assignment->academic_year_id) }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
