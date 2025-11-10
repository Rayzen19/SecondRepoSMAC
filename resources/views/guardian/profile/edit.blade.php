@extends('guardian.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Edit Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guardian.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guardian.profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ti ti-user-edit me-2"></i>Update Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('guardian.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h6 class="mb-3">Profile Picture</h6>
                            <div class="mb-3">
                                @if($guardian->profile_picture)
                                    <img src="{{ asset('storage/' . $guardian->profile_picture) }}" id="preview-image" alt="Profile" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
                                    <div id="preview-placeholder" class="d-none"></div>
                                @else
                                    <div id="preview-placeholder" class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 60px; border: 3px solid #ddd;">
                                        {{ strtoupper(substr($guardian->first_name, 0, 1)) }}{{ strtoupper(substr($guardian->last_name, 0, 1)) }}
                                    </div>
                                    <img src="" id="preview-image" alt="Profile" class="rounded-circle mb-3 d-none" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="btn btn-primary btn-sm mb-2">
                                    <i class="ti ti-upload me-1"></i> Choose File
                                </label>
                                <input type="file" class="d-none @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)">
                                <div id="file-name" class="text-muted small mb-2">No file chosen</div>
                                @error('profile_picture')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block">Accepted: JPG, JPEG, PNG, GIF (Max: 2MB)</small>
                            </div>
                            @if($guardian->profile_picture)
                            <form action="{{ route('guardian.profile.removePicture') }}" method="POST" class="d-inline" id="remove-picture-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmRemovePicture()">
                                    <i class="ti ti-trash me-1"></i> Remove Picture
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <hr class="mb-4">

                    <!-- Basic Information -->
                    <h6 class="mb-3"><i class="ti ti-user me-2"></i>Basic Information</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $guardian->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $guardian->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name', $guardian->middle_name) }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $guardian->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $guardian->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="mb-4">

                    <!-- Contact Information -->
                    <h6 class="mb-3"><i class="ti ti-address-book me-2"></i>Contact Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control bg-light" value="{{ $guardian->email }}" disabled>
                        <small class="text-muted">Email cannot be changed. Contact administrator if needed.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" name="mobile_number" value="{{ old('mobile_number', $guardian->mobile_number) }}" required pattern="[0-9]{11}" placeholder="09XXXXXXXXX">
                        @error('mobile_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: 09XXXXXXXXX (11 digits)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address', $guardian->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="mb-4">

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guardian.profile.show') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const fileNameDisplay = document.getElementById('file-name');
    
    if (file) {
        // Update file name display
        fileNameDisplay.textContent = file.name;
        fileNameDisplay.classList.remove('text-muted');
        fileNameDisplay.classList.add('text-success');
        
        // Preview the image
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview-image');
            const placeholder = document.getElementById('preview-placeholder');
            
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        }
        reader.readAsDataURL(file);
    } else {
        fileNameDisplay.textContent = 'No file chosen';
        fileNameDisplay.classList.add('text-muted');
        fileNameDisplay.classList.remove('text-success');
    }
}

function confirmRemovePicture() {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        document.getElementById('remove-picture-form').submit();
    }
}
</script>
@endsection
