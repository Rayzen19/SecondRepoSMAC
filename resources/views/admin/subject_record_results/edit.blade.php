@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-edit me-2"></i>Edit Class Record Entry</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-record-results.index') }}">Class Record Entries</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subject-record-results.update', $result) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Subject Record</label>
                        <input type="number" name="subject_record_id" class="form-control" value="{{ old('subject_record_id', $subjectRecord?->id) }}">
                        @error('subject_record_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" {{ old('student_id', $result->student_id)==$s->id?'selected':'' }}>{{ $s->student_number }} — {{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date Submitted</label>
                        <input type="date" name="date_submitted" class="form-control" value="{{ old('date_submitted', optional($result->date_submitted)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $result->remarks) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Raw Score</label>
                        <input type="number" step="0.01" name="raw_score" class="form-control" value="{{ old('raw_score', $result->raw_score) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Base Score</label>
                        <input type="number" step="0.01" name="base_score" class="form-control" value="{{ old('base_score', $result->base_score) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Final Score</label>
                        <input type="number" step="0.01" name="final_score" class="form-control" value="{{ old('final_score', $result->final_score) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $result->description) }}</textarea>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('admin.subject-record-results.show', $result) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
