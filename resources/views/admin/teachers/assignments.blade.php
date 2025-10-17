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
            <form action="{{ route('admin.teachers.assignments.store', $teacher) }}" method="POST">
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
                            <label class="form-label">Strand <span class="text-danger">*</span></label>
                            <select name="strand_id" class="form-select" required>
                                <option value="">Select Strand</option>
                                @foreach($strands as $strand)
                                    <option value="{{ $strand->id }}" @selected(old('strand_id')==$strand->id)>
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
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected(old('subject_id')==$subject->id)>
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
                            <button type="submit" class="btn btn-primary d-block w-100">
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
