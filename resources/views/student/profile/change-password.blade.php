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
                            <h4 class="mb-1">Change Password</h4>
                            <p class="text-muted mb-0">Update your account password</p>
                        </div>
                        <a href="{{ route('student.profile.show') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('student.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required
                                       autocomplete="current-password">
                                <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('current_password', this)" style="border: none; background: transparent;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="12"
                                       autocomplete="new-password">
                                <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('password', this)" style="border: none; background: transparent;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Password must be at least 12 characters long (14+ recommended).</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       minlength="12"
                                       autocomplete="new-password">
                                <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('password_confirmation', this)" style="border: none; background: transparent;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Password Policy:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>At least 12 characters long</strong> (but 14 or more is better)</li>
                                <li>A combination of <strong>uppercase letters, lowercase letters, numbers, and symbols</strong></li>
                                <li><strong>Not a word</strong> that can be found in a dictionary or the name of a person, character, product, or organization</li>
                                <li>Make sure your new password is <strong>different from your current password</strong></li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('student.profile.show') }}" class="btn btn-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn bg-info">
                                <i class="ti ti-check me-1"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
