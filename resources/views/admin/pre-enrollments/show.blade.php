@extends('admin.components.template')

@section('title', 'Pre-Enrollment Details')

@section('breadcrumb')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Pre-Enrollment Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pre-enrollments.index') }}">Pre-Enrollment Submissions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pre-Enrollment Information</h5>
                <div>
                    @if($preEnrollment->status == 'pending')
                        <span class="badge bg-warning">Pending Review</span>
                    @elseif($preEnrollment->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($preEnrollment->status == 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @elseif($preEnrollment->status == 'enrolled')
                        <span class="badge bg-info">Enrolled</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Student Information -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Student Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Student Number:</label>
                            <p>{{ $preEnrollment->student->student_number }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name:</label>
                            <p>{{ $preEnrollment->student->first_name }} {{ $preEnrollment->student->middle_name ?? '' }} {{ $preEnrollment->student->last_name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p>{{ $preEnrollment->student->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Grade Level:</label>
                            <p>Grade {{ $preEnrollment->student->grade_level ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Pre-Enrollment Details -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Pre-Enrollment Details</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Grade Level:</label>
                            <p>Grade {{ $preEnrollment->grade_level }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Strand:</label>
                            <p>{{ $preEnrollment->strand->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preferred Section:</label>
                            <p>{{ $preEnrollment->section->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Submitted At:</label>
                            <p>{{ $preEnrollment->submitted_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Academic Year Information -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Current Academic Year</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Year:</label>
                            <p>{{ $preEnrollment->currentAcademicYear->year }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Target Academic Year</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Year:</label>
                            <p>{{ $preEnrollment->targetAcademicYear->year ?? 'Not Set' }}</p>
                        </div>
                    </div>
                </div>

                @if($preEnrollment->status != 'pending')
                    <hr class="my-4">
                    
                    <!-- Processing Information -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-3 text-primary">Processing Information</h6>
                        </div>
                        @if($preEnrollment->processed_at)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Processed At:</label>
                                    <p>{{ $preEnrollment->processed_at->format('F d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($preEnrollment->processedBy)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Processed By:</label>
                                    <p>{{ $preEnrollment->processedBy->name ?? 'System' }}</p>
                                </div>
                            </div>
                        @endif
                        @if($preEnrollment->remarks)
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Remarks:</label>
                                    <p class="text-muted">{{ $preEnrollment->remarks }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="{{ route('admin.pre-enrollments.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>

                    @if($preEnrollment->status == 'pending')
                        <form action="{{ route('admin.pre-enrollments.approve', $preEnrollment) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this pre-enrollment?')">
                                <i class="ti ti-check me-1"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ti ti-x me-1"></i> Reject
                        </button>
                    @endif

                    @if($preEnrollment->status == 'approved')
                        <form action="{{ route('admin.pre-enrollments.process', $preEnrollment) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('This will create an actual enrollment for the student. Continue?')">
                                <i class="ti ti-circle-check me-1"></i> Process Enrollment
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Pre-Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.pre-enrollments.reject', $preEnrollment) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="4" required placeholder="Enter reason for rejecting this pre-enrollment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Pre-Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
