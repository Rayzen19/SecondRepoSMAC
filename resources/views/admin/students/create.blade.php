@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    Student
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.students.index') }}">Student Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Student Create</li>
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
            <form action="{{ route('admin.students.store') }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="mb-3">
                                <label class="form-label">Student Number <span class="text-danger">*</span></label>
                                <input type="text" name="student_number" class="form-control" value="Auto Generated" disabled>
                                @error('student_number')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                                @error('middle_name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Suffix</label>
                                <input type="text" name="suffix" class="form-control" value="{{ old('suffix') }}">
                                @error('suffix')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sex <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate') }}" max="{{ date('Y-m-d') }}" required>
                                @error('birthdate')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number') }}" required>
                                @error('mobile_number')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                                @error('address')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" required>
                                @error('guardian_name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Contact <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact') }}" required>
                                @error('guardian_contact')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Email <span class="text-danger">*</span></label>
                                <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email') }}" required>
                                @error('guardian_email')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Program (Strand) <span class="text-danger">*</span></label>
                                <select name="program" class="form-select" required>
                                    <option value="" disabled {{ old('program') ? '' : 'selected' }}>Select strand</option>
                                    @foreach(($activeStrands ?? []) as $strand)
                                        <option value="{{ $strand->code ?? $strand->name }}" {{ old('program') == ($strand->code ?? $strand->name) ? 'selected' : '' }}>
                                            {{ $strand->name }} @if(!empty($strand->code)) ({{ $strand->code }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('program')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                {{-- Hidden inputs so values are submitted (disabled fields are not sent) --}}
                                <input type="hidden" name="academic_year_id" value="{{ old('academic_year_id', optional($activeYear)->id) }}">
                                <input type="hidden" name="academic_year" value="{{ old('academic_year', optional($activeYear)->name) }}">
                                {{-- Visible disabled field for display only --}}
                                <input type="text" class="form-control" value="{{ optional($activeYear)->name }}" disabled>
                                @error('academic_year')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light border me-2">Back</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Add Client Success -->
@endsection