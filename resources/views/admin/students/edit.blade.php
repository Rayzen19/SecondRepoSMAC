@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.students.index') }}">Student Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Student Edit</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @method('PUT')
                <div>
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <div class="mb-3">
                                <label class="form-label">Student Number</label>
                                <input type="text" class="form-control" value="{{ $student->student_number }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                                @error('first_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $student->middle_name) }}">
                                @error('middle_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                                @error('last_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Suffix</label>
                                <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $student->suffix) }}">
                                @error('suffix')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sex <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled {{ old('gender', $student->gender) ? '' : 'selected' }}>Select</option>
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate', $student->birthdate) }}" max="{{ date('Y-m-d') }}" required>
                                @error('birthdate')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" required>
                                @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number', $student->mobile_number) }}" required>
                                @error('mobile_number')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $student->address) }}" required>
                                @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}" required>
                                @error('guardian_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Contact <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact', $student->guardian_contact) }}" required>
                                @error('guardian_contact')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guardian Email <span class="text-danger">*</span></label>
                                <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email', $student->guardian_email) }}" required>
                                @error('guardian_email')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Program (Strand) <span class="text-danger">*</span></label>
                                <select name="program" class="form-select" required>
                                    <option value="" disabled {{ old('program', $student->program) ? '' : 'selected' }}>Select strand</option>
                                    @foreach(($activeStrands ?? []) as $strand)
                                        <option value="{{ $strand->code ?? $strand->name }}" {{ old('program', $student->program) == ($strand->code ?? $strand->name) ? 'selected' : '' }}>
                                            {{ $strand->name }} @if(!empty($strand->code)) ({{ $strand->code }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('program')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year_id" class="form-control" style="display: none;" value="{{ old('academic_year_id', $student->academic_year_id) }}" disabled="true" required>
                                <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $student->academic_year) }}" disabled="true">
                                @error('academic_year')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @php $status = old('status', $student->status); @endphp
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="graduated" {{ $status == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="dropped" {{ $status == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Generated Password (encrypted)</label>
                                @if(!empty($student->generated_password_encrypted))
                                    <textarea class="form-control" rows="2" disabled>{{ $student->generated_password_encrypted }}</textarea>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted">No generated password stored.</span>
                                        <form action="{{ route('admin.students.generate-password', $student) }}" method="POST" onsubmit="return confirm('Generate a new password for this student? This will update their login and email them.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Generate Now</button>
                                        </form>
                                    </div>
                                @endif
                                <div class="small text-muted mt-1">Displayed for reference only; stored encrypted and not editable here.</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
