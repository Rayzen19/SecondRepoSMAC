@extends('admin.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Co-Admin Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.co-admins.index') }}">Co-Admins</a></li>
                    <li class="breadcrumb-item active">{{ $coAdmin->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card comman-shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="form-title">Co-Admin Information</h5>
                                </div>
                                <div class="col-auto text-end">
                                    <a href="{{ route('admin.co-admins.edit', $coAdmin) }}" class="btn btn-primary">
                                        <i class="feather-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.co-admins.destroy', $coAdmin) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this co-admin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="feather-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Name:</label>
                            <p>{{ $coAdmin->name }}</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Email:</label>
                            <p>{{ $coAdmin->email }}</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">User Type:</label>
                            <p><span class="badge bg-primary">Co-Admin</span></p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Email Verified:</label>
                            <p>
                                @if($coAdmin->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                    <small class="text-muted d-block">{{ $coAdmin->email_verified_at->format('M d, Y h:i A') }}</small>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Created At:</label>
                            <p>{{ $coAdmin->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Last Updated:</label>
                            <p>{{ $coAdmin->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This co-admin has full access to all admin features and can login through the admin login form.
                        </div>
                    </div>

                    <div class="col-12">
                        <a href="{{ route('admin.co-admins.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
