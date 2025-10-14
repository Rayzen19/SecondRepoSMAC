@extends('student.components.template')

@section('content')
<div class="container-fluid">
    <!-- Validation Errors -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oops! There were some errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Page Header -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Edit Profile</h4>
                            <p class="text-muted mb-0">Update your personal information</p>
                        </div>
                        <a href="{{ route('student.profile.show') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('student.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-3">
            <!-- Contact Information -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $student->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('mobile_number') is-invalid @enderror" 
                                   id="mobile_number" 
                                   name="mobile_number" 
                                   value="{{ old('mobile_number', $student->mobile_number) }}" 
                                   required>
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3">{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Guardian Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="guardian_name" class="form-label">Guardian Name</label>
                            <input type="text" 
                                   class="form-control @error('guardian_name') is-invalid @enderror" 
                                   id="guardian_name" 
                                   name="guardian_name" 
                                   value="{{ old('guardian_name', $student->guardian_name) }}">
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guardian_contact" class="form-label">Guardian Contact <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('guardian_contact') is-invalid @enderror" 
                                   id="guardian_contact" 
                                   name="guardian_contact" 
                                   value="{{ old('guardian_contact', $student->guardian_contact) }}" 
                                   required>
                            @error('guardian_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="guardian_email" class="form-label">Guardian Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('guardian_email') is-invalid @enderror" 
                                   id="guardian_email" 
                                   name="guardian_email" 
                                   value="{{ old('guardian_email', $student->guardian_email) }}" 
                                   required>
                            @error('guardian_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information (Read-only) -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Personal Information <small class="text-muted">(Read-only)</small></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Student Number</label>
                                <p class="mb-0">{{ $student->student_number }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">First Name</label>
                                <p class="mb-0">{{ $student->first_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Middle Name</label>
                                <p class="mb-0">{{ $student->middle_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Last Name</label>
                                <p class="mb-0">{{ $student->last_name }}{{ $student->suffix ? ' ' . $student->suffix : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('student.profile.show') }}" class="btn btn-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
