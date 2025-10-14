@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Update Strand Adviser</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $adviser->academic_year_id) }}">Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Adviser</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.academic-year-strand-advisers.update', $adviser) }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Strand</label>
                            <input type="text" class="form-control" value="{{ $adviser->strand?->code }} — {{ $adviser->strand?->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <input type="text" class="form-control" value="{{ $adviser->academicYear?->name }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Adviser Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select" required>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" @selected(old('teacher_id', $adviser->teacher_id)==$t->id)>{{ $t->employee_number }} — {{ $t->first_name }} {{ $t->last_name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
