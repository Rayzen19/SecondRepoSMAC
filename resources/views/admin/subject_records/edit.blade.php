@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-edit me-2"></i>Edit Class Record</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-records.index') }}">Class Records</a></li>
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
            <form method="POST" action="{{ route('admin.subject-records.update', $record) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Assignment</label>
                        <select name="academic_year_strand_subject_id" class="form-select">
                            @foreach($assignments as $a)
                                <option value="{{ $a->id }}" {{ old('academic_year_strand_subject_id', $record->academic_year_strand_subject_id)==$a->id?'selected':'' }}>
                                    {{ $a->academicYear?->display_name }} — {{ $a->subject?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_strand_subject_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $record->name) }}">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">—</option>
                            @foreach(['written work','performance task','quarterly assessment'] as $opt)
                                <option value="{{ $opt }}" {{ old('type', $record->type)===$opt?'selected':'' }}>{{ ucwords($opt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quarter</label>
                        <select name="quarter" class="form-select">
                            <option value="">—</option>
                            @foreach(['1st','2nd'] as $q)
                                <option value="{{ $q }}" {{ old('quarter', $record->quarter)===$q?'selected':'' }}>{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Max Score</label>
                        <input type="number" step="0.01" name="max_score" class="form-control" value="{{ old('max_score', $record->max_score) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date Given</label>
                        <input type="date" name="date_given" class="form-control" value="{{ old('date_given', optional($record->date_given)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $record->remarks) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $record->description) }}</textarea>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('admin.subject-records.show', $record) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
