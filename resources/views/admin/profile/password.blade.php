@extends('admin.components.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3" style="color:#313131">
                <i class="ti ti-lock me-2"></i>Change Password
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ti ti-key me-2"></i>Update Your Password
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>At least 8 characters long</li>
                            <li>Contains both uppercase and lowercase letters</li>
                            <li>Contains at least one number</li>
                            <li>Contains at least one special character (e.g., !@#$%^&*)</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.profile.password.update') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group @error('current_password') has-validation @enderror">
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                <button class="btn btn-outline-secondary @error('current_password') border-danger @enderror" type="button" id="toggleCurrentPassword">
                                    <i class="ti ti-eye" id="currentPasswordIcon"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group @error('password') has-validation @enderror">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button class="btn btn-outline-secondary @error('password') border-danger @enderror" type="button" id="togglePassword">
                                    <i class="ti ti-eye" id="passwordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Password strength: </small>
                                <small id="passwordStrength" class="fw-bold"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="ti ti-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <small id="passwordMatch" class="fw-bold"></small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-check me-2"></i>Change Password
                            </button>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                <i class="ti ti-x me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
        const input = document.getElementById('current_password');
        const icon = document.getElementById('currentPasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const input = document.getElementById('password_confirmation');
        const icon = document.getElementById('confirmPasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthElement = document.getElementById('passwordStrength');
        
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (strength === 0) {
            strengthElement.textContent = '';
            strengthElement.className = '';
        } else if (strength <= 2) {
            strengthElement.textContent = 'Weak';
            strengthElement.className = 'text-danger fw-bold';
        } else if (strength === 3) {
            strengthElement.textContent = 'Fair';
            strengthElement.className = 'text-warning fw-bold';
        } else if (strength === 4) {
            strengthElement.textContent = 'Good';
            strengthElement.className = 'text-info fw-bold';
        } else {
            strengthElement.textContent = 'Strong';
            strengthElement.className = 'text-success fw-bold';
        }
        
        checkPasswordMatch();
    });

    // Password match checker
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchElement = document.getElementById('passwordMatch');
        
        if (confirmPassword === '') {
            matchElement.textContent = '';
            matchElement.className = '';
        } else if (password === confirmPassword) {
            matchElement.textContent = '✓ Passwords match';
            matchElement.className = 'text-success fw-bold';
        } else {
            matchElement.textContent = '✗ Passwords do not match';
            matchElement.className = 'text-danger fw-bold';
        }
    }

    document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);
});
</script>
@endsection
