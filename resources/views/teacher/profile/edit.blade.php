@extends('teacher.components.template')

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
                        <a href="{{ route('teacher.profile.show') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.profile.update') }}" method="POST">
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
                                   value="{{ old('email', $teacher->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $teacher->phone) }}" 
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3">{{ old('address', $teacher->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information (Read-only) -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Personal Information <small class="text-muted">(Read-only)</small></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Employee Number</label>
                                <p class="mb-0">{{ $teacher->employee_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($teacher->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">First Name</label>
                                <p class="mb-0">{{ $teacher->first_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">Middle Name</label>
                                <p class="mb-0">{{ $teacher->middle_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">Last Name</label>
                                <p class="mb-0">{{ $teacher->last_name }}{{ $teacher->suffix ? ' ' . $teacher->suffix : '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Department</label>
                                <p class="mb-0">{{ $teacher->department }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Term</label>
                                <p class="mb-0">{{ $teacher->term }}</p>
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
                            <a href="{{ route('teacher.profile.show') }}" class="btn btn-secondary">
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
