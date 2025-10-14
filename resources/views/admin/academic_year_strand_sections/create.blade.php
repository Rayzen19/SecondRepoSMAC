@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Assign Section to Strand</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.show', $academicYear->id) }}">Academic Year Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assign Section</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.academic-year-strand-sections.store') }}">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Strand</label>
                        <select name="strand_id" class="form-select" required>
                            <option value="">Select Strand</option>
                            @foreach($strands as $strand)
                                <option value="{{ $strand->id }}" @selected(old('strand_id')==$strand->id)>{{ $strand->code }} — {{ $strand->name }}</option>
                            @endforeach
                        </select>
                        @error('strand_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section</label>
                        <select name="section_id[]" class="form-select" multiple required>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" @if(collect(old('section_id'))->contains($section->id)) selected @endif>{{ $section->grade }} — {{ $section->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Hold Ctrl (Windows) or Command (Mac) to select multiple sections.</div>
                        @error('section_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adviser Teacher (optional)</label>
                        <select name="adviser_teacher_id" class="form-select">
                            <option value="">— No Adviser —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('adviser_teacher_id')==$teacher->id)>{{ $teacher->employee_number }} — {{ $teacher->last_name }}, {{ $teacher->first_name }}</option>
                            @endforeach
                        </select>
                        @error('adviser_teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
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
@endsection
