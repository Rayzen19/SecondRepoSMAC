@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-eye me-2"></i>Class Record Entry</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-record-results.index') }}">Class Record Entries</a></li>
                <li class="breadcrumb-item active" aria-current="page">View</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subject-record-results.edit', $result) }}" class="btn btn-outline-primary d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
    </div>
    </div>
@endsection

@section('content')
<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @php($sr = $result->subjectRecord)
            @php($se = $sr?->subjectEnrollment)
            <div class="row g-3">
                <div class="col-md-4"><strong>Student</strong><div>{{ $result->student?->student_number }} — {{ $result->student?->name }}</div></div>
                <div class="col-md-4"><strong>Academic Year</strong><div>{{ $se?->studentEnrollment?->academicYear?->display_name }}</div></div>
                <div class="col-md-4"><strong>Subject</strong><div>{{ $se?->academicYearStrandSubject?->subject?->name }}</div></div>
                <div class="col-md-4"><strong>Class Record</strong><div>{{ $sr?->name }}</div></div>
                <div class="col-md-4"><strong>Date Submitted</strong><div>{{ optional($result->date_submitted)->format('Y-m-d') }}</div></div>
                <div class="col-md-4"><strong>Scores</strong><div>Raw {{ $result->raw_score }} / Base {{ $result->base_score }} → Final {{ $result->final_score }}</div></div>
                <div class="col-12"><strong>Remarks</strong><div>{{ $result->remarks }}</div></div>
                <div class="col-12"><strong>Description</strong><div>{{ $result->description }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
