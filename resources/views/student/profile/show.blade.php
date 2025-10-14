@extends('student.components.template')

@section('content')
<div class="container-fluid">
    <!-- Success Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Profile Header -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">My Profile</h4>
                            <p class="text-muted mb-0">View and manage your personal information</p>
                        </div>
                        <a href="{{ route('student.profile.edit') }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Profile Picture Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Profile Picture</h5>
                    @if($student->profile_picture)
                        <img src="{{ asset('storage/' . $student->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3" 
                             style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 200px; height: 200px;">
                            <i class="ti ti-user" style="font-size: 100px; color: white;"></i>
                        </div>
                    @endif
                    
                    <form action="{{ route('student.profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        @method('POST')
                        <div class="mb-2">
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" required>
                            @error('profile_picture')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="ti ti-upload me-1"></i>Upload New Picture
                        </button>
                    </form>

                    @if($student->profile_picture)
                        <form action="{{ route('student.profile.picture.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="ti ti-trash me-1"></i>Remove Picture
                            </button>
                        </form>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted">Accepted: JPG, JPEG, PNG, GIF (Max: 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Security</h5>
                    <a href="{{ route('student.profile.password.edit') }}" class="btn btn-outline-primary w-100">
                        <i class="ti ti-lock me-1"></i>Change Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Student Number</label>
                            <p class="mb-0">{{ $student->student_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">First Name</label>
                            <p class="mb-0">{{ $student->first_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Middle Name</label>
                            <p class="mb-0">{{ $student->middle_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Last Name</label>
                            <p class="mb-0">{{ $student->last_name }}{{ $student->suffix ? ' ' . $student->suffix : '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Gender</label>
                            <p class="mb-0">{{ ucfirst($student->gender) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Birthdate</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($student->birthdate)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Email</label>
                            <p class="mb-0">{{ $student->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Mobile Number</label>
                            <p class="mb-0">{{ $student->mobile_number }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small">Address</label>
                            <p class="mb-0">{{ $student->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Program</label>
                            <p class="mb-0">{{ $student->program }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Academic Year</label>
                            <p class="mb-0">{{ $student->academic_year }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Guardian Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Guardian Name</label>
                            <p class="mb-0">{{ $student->guardian_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Guardian Contact</label>
                            <p class="mb-0">{{ $student->guardian_contact }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Guardian Email</label>
                            <p class="mb-0">{{ $student->guardian_email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
