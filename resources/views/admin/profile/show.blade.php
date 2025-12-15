@extends('admin.components.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3" style="color:#313131">
                <i class="ti ti-user-circle me-2"></i>My Profile
            </h2>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Left Column: Profile Picture & Security -->
        <div class="col-lg-4">
            <!-- Profile Picture Card -->
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Profile Picture</h5>
                    @if($admin->profile_picture && Storage::disk('public')->exists($admin->profile_picture))
                        <img src="{{ asset('storage/' . $admin->profile_picture) }}" 
                             alt="Profile Picture" 
                             id="preview-image-admin"
                             class="rounded-circle mb-3" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #ddd;">
                        <div id="preview-placeholder-admin" class="d-none"></div>
                    @else
                        <div id="preview-placeholder-admin" class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 200px; height: 200px; border: 3px solid #ddd;">
                            <span style="font-size: 80px; color: white; font-weight: bold;">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                        </div>
                        <img src="" id="preview-image-admin" alt="Profile Picture" class="rounded-circle mb-3 d-none" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #ddd;">
                    @endif
                    
                    <form action="{{ route('admin.profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <label for="profile_picture" class="btn bg-info btn-sm w-100 mb-2">
                                <i class="ti ti-photo me-1"></i> Choose File
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewImageAdmin(event)" required>
                            <div id="file-name-admin" class="text-muted small mb-2">No file chosen</div>
                            @error('profile_picture')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-2" id="upload-btn-admin" style="display: none;">
                            <i class="ti ti-upload me-1"></i>Upload New Picture
                        </button>
                    </form>

                    @if($admin->profile_picture)
                        <form action="{{ route('admin.profile.picture.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
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

            <!-- Security Settings Card -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ti ti-shield-lock me-2"></i>Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.profile.password.edit') }}" class="btn btn-outline-danger w-100">
                        <i class="ti ti-lock me-1"></i>Change Password
                    </a>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <small><strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Account Information -->
        <div class="col-lg-8">
            <!-- Profile Header Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $admin->name }}</h4>
                            <p class="text-muted mb-0">
                                <i class="ti ti-shield-check me-1"></i>
                                @if($admin->type === 'co-admin')
                                    <span class="badge bg-info">Co-Administrator</span>
                                @else
                                    <span class="badge bg-success">Administrator</span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('admin.profile.edit') }}" class="btn bg-info">
                            <i class="ti ti-edit me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-info-circle me-2"></i>Account Information
                    </h5>
                    <a href="{{ route('admin.profile.edit') }}" class="btn bg-info btn-sm">
                        <i class="ti ti-edit me-1"></i>Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td width="30%" class="fw-bold">Full Name:</td>
                                    <td>{{ $admin->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email Address:</td>
                                    <td>{{ $admin->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Account Type:</td>
                                    <td>
                                        @if($admin->type === 'co-admin')
                                            <span class="badge bg-info">Co-Administrator</span>
                                        @else
                                            <span class="badge bg-success">Administrator</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Member Since:</td>
                                    <td>{{ $admin->created_at ? $admin->created_at->format('F d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $admin->updated_at ? $admin->updated_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Security Settings Card -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ti ti-shield-lock me-2"></i>Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
                        <div>
                            <h6 class="mb-1"><i class="ti ti-key me-2"></i>Password</h6>
                            <p class="text-muted mb-0 small">Keep your account secure with a strong password</p>
                        </div>
                        <a href="{{ route('admin.profile.password.edit') }}" class="btn btn-danger">
                            <i class="ti ti-lock me-1"></i>Change Password
                        </a>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImageAdmin(event) {
    const file = event.target.files[0];
    const fileNameDisplay = document.getElementById('file-name-admin');
    const uploadBtn = document.getElementById('upload-btn-admin');
    const previewImage = document.getElementById('preview-image-admin');
    const previewPlaceholder = document.getElementById('preview-placeholder-admin');
    
    if (file) {
        fileNameDisplay.textContent = file.name;
        uploadBtn.style.display = 'block';
        
        // Show image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.classList.remove('d-none');
            previewPlaceholder.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        fileNameDisplay.textContent = 'No file chosen';
        uploadBtn.style.display = 'none';
    }
}
</script>
@endsection
