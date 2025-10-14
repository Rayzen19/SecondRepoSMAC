@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Assign Strand Adviser</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $academicYear) }}">Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assign Adviser</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.academic-year-strand-advisers.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Strand <span class="text-danger">*</span></label>
                            <select name="strand_id" class="form-select" required>
                                <option value="">Select Strand</option>
                                @foreach($strands as $strand)
                                    <option value="{{ $strand->id }}" @selected(old('strand_id')==$strand->id)>{{ $strand->code }} — {{ $strand->name }}</option>
                                @endforeach
                            </select>
                            @error('strand_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" @selected(old('teacher_id')==$t->id)>{{ $t->employee_number }} — {{ $t->first_name }} {{ $t->last_name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Assign</button>
            </form>
        </div>
    </div>
</div>
@endsection
