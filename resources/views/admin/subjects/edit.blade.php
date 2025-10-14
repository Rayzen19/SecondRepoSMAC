@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Subject</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subjects.index') }}">Subject Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Subject Edit</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @method('PUT')
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code) }}" required>
                                @error('code')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
                                @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $subject->description) }}</textarea>
                                @error('description')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" name="units" class="form-control" value="{{ old('units', $subject->units) }}" min="0" required>
                                @error('units')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="core" {{ old('type', $subject->type)=='core'?'selected':'' }}>Core</option>
                                    <option value="applied" {{ old('type', $subject->type)=='applied'?'selected':'' }}>Applied</option>
                                    <option value="specialized" {{ old('type', $subject->type)=='specialized'?'selected':'' }}>Specialized</option>
                                </select>
                                @error('type')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" class="form-select" required>
                                    <option value="1st" {{ old('semester', $subject->semester)=='1st'?'selected':'' }}>1st</option>
                                    <option value="2nd" {{ old('semester', $subject->semester)=='2nd'?'selected':'' }}>2nd</option>
                                </select>
                                @error('semester')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
