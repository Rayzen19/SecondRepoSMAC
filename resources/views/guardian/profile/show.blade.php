@extends('guardian.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">My Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guardian.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <!-- Profile Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($guardian->profile_picture)
                        <img src="{{ asset('storage/' . $guardian->profile_picture) }}" alt="Profile" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 60px;">
                            {{ strtoupper(substr($guardian->first_name, 0, 1)) }}{{ strtoupper(substr($guardian->last_name, 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $guardian->name }}</h4>
                    <p class="text-muted mb-2">
                        <i class="ti ti-id me-1"></i>{{ $guardian->guardian_number }}
                    </p>
                    <span class="badge bg-{{ $guardian->status === 'active' ? 'success' : 'secondary' }} px-3 py-2">
                        {{ ucfirst($guardian->status) }}
                    </span>
                </div>

                <hr>

                <!-- Contact Information -->
                <h6 class="mb-3"><i class="ti ti-address-book me-2"></i>Contact Information</h6>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <small class="text-muted d-block">Email Address</small>
                        <strong><i class="ti ti-mail me-2"></i>{{ $guardian->email }}</strong>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Mobile Number</small>
                        <strong><i class="ti ti-phone me-2"></i>{{ $guardian->mobile_number }}</strong>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Address</small>
                        <strong><i class="ti ti-map-pin me-2"></i>{{ $guardian->address ?? 'Not provided' }}</strong>
                    </li>
                </ul>

                <hr>

                <!-- Account Information -->
                <h6 class="mb-3"><i class="ti ti-info-circle me-2"></i>Account Information</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <small class="text-muted d-block">Gender</small>
                        <strong>{{ ucfirst($guardian->gender) }}</strong>
                    </li>
                    <li class="mb-2">
                        <small class="text-muted d-block">Member Since</small>
                        <strong>{{ $guardian->created_at->format('M d, Y') }}</strong>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="{{ route('guardian.profile.edit') }}" class="btn btn-primary w-100 mb-2">
                        <i class="ti ti-edit me-2"></i>Edit Profile
                    </a>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="ti ti-lock me-2"></i>Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- My Students -->
    <div class="col-xl-8 col-lg-7">
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-primary text-white rounded">
                                    <i class="ti ti-users fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Total Students</h6>
                                <h3 class="mb-0">{{ $students->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-lg bg-success text-white rounded">
                                    <i class="ti ti-user-check fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">Active Students</h6>
                                <h3 class="mb-0">{{ $students->where('status', 'active')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ti ti-school me-2"></i>My Students</h5>
                    @if($students->count() > 0)
                        <span class="badge bg-primary">{{ $students->count() }} Student{{ $students->count() > 1 ? 's' : '' }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Number</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Grade Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td class="font-monospace">{{ $student->student_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->profile_picture)
                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;" alt="{{ $student->name }}">
                                            @else
                                                <div class="avatar avatar-sm bg-info text-white rounded-circle me-2">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->program }}</td>
                                    <td>{{ $student->academic_year }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'graduated' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti ti-users-off fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">No Students Found</h5>
                        <p class="text-muted">You are not currently linked to any students.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('guardian.profile.updatePassword') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" id="guardian_current_password" required>
                            <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('guardian_current_password', this)" style="border: none; background: transparent; z-index: 10;">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" id="guardian_new_password" required minlength="12">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('guardian_new_password', this)" style="border: none; background: transparent; z-index: 10;">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimum 12 characters (14+ recommended)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="new_password_confirmation" id="guardian_new_password_confirmation" required minlength="12">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('guardian_new_password_confirmation', this)" style="border: none; background: transparent; z-index: 10;">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Password Policy:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li><strong>At least 12 characters long</strong> (but 14 or more is better)</li>
                            <li>A combination of <strong>uppercase letters, lowercase letters, numbers, and symbols</strong></li>
                            <li><strong>Not a word</strong> that can be found in a dictionary or the name of a person, character, product, or organization</li>
                            <li>Make sure your new password is <strong>different from your current password</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.avatar-lg {
    width: 60px;
    height: 60px;
}

.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
}
</style>

@push('scripts')
<script>
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
