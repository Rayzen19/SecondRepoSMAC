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
        <!-- Profile Information Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; font-size: 48px;">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                    </div>
                    
                    <h4 class="fw-bold mb-1">{{ $admin->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="ti ti-shield-check me-1"></i>
                        @if($admin->type === 'co-admin')
                            Co-Administrator
                        @else
                            Administrator
                        @endif
                    </p>
                    <p class="mb-4">
                        <i class="ti ti-mail me-1"></i>{{ $admin->email }}
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.profile.edit') }}" class="btn bg-info">
                            <i class="ti ti-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('admin.profile.password.edit') }}" class="btn btn-outline-danger">
                            <i class="ti ti-lock me-2"></i>Change Password
                        </a>
                    </div>
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
@endsection
