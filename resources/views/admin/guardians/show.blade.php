@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Guardian Profile</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.guardians.index') }}">Guardians</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $guardian->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2 me-2">
            <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-warning d-flex align-items-center">
                <i class="ti ti-edit me-2"></i>Edit Profile
            </a>
        </div>
        <div class="mb-2">
            <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Guardian Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($guardian->profile_picture)
                            <img src="{{ asset('storage/' . $guardian->profile_picture) }}" alt="Guardian Photo" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
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
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $guardian->created_at->format('M d, Y') }}</strong>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted d-block">Last Updated</small>
                            <strong>{{ $guardian->updated_at->format('M d, Y h:i A') }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Students & Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Statistics Cards -->
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
                        <h5 class="mb-0"><i class="ti ti-school me-2"></i>Students Under Guardianship</h5>
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
                                        <th>Actions</th>
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
                                        <td>
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary" title="View Student">
                                                <i class="ti ti-eye"></i> View
                                            </a>
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
                            <p class="text-muted">This guardian is not currently linked to any students.</p>
                        </div>
                    @endif
                </div>
            </div>
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

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}
</style>
@endsection
