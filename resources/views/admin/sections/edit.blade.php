@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Edit Section</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Grade & Section</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.sections.update', $section) }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Grade</label>
                        <select name="grade" class="form-select" required>
                            @foreach($grades as $g)
                                <option value="{{ $g }}" @selected(old('grade', $section->grade)==$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('grade')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $section->name) }}" class="form-control" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i>Save</button>
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
