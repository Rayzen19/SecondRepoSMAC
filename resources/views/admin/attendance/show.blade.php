@extends('admin.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Attendance Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Details</li>
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="form-title">
                                <span>Attendance Log Information</span>
                            </h5>
                            <div>
                                <a href="{{ route('admin.attendance.edit', $log) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back
                                </a>
                            </div>
                        </div>
                        
                        <div class="row">
                            {{-- Student Information --}}
                            <div class="col-12 col-sm-4">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Student Information</h6>
                                        <div class="avatar avatar-xl mb-3 mx-auto">
                                            @if($log->student && $log->student->profile_picture)
                                                <img src="{{ asset('storage/' . $log->student->profile_picture) }}" class="rounded-circle" alt="Profile">
                                            @else
                                                <div class="avatar-content bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 48px;">
                                                    {{ substr($log->student_name ?? 'N', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <h5>{{ $log->student_name }}</h5>
                                        <p class="text-muted mb-2">{{ $log->student_number }}</p>
                                        <span class="badge bg-{{ $log->status_badge }} mb-2">{{ ucfirst($log->status) }}</span>
                                        @if($log->student)
                                            <div class="mt-3">
                                                <a href="{{ route('admin.students.show', $log->student) }}" class="btn btn-sm btn-outline-primary">
                                                    View Full Profile
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Attendance Details --}}
                            <div class="col-12 col-sm-8">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Attendance Details</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Date</label>
                                                <p class="mb-0">{{ $log->date->format('F d, Y') }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Time</label>
                                                <p class="mb-0">{{ $log->time->format('h:i A') }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Status</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-{{ $log->status_badge }}">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Recorded By</label>
                                                <p class="mb-0">{{ $log->recorded_by ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Academic Information --}}
                                <div class="card shadow-sm mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Academic Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Strand</label>
                                                <p class="mb-0">{{ $log->strand ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Year Level</label>
                                                <p class="mb-0">{{ $log->year_level ? 'Grade ' . $log->year_level : '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Section</label>
                                                <p class="mb-0">{{ $log->section ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Semester</label>
                                                <p class="mb-0">{{ $log->semester ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Subject</label>
                                                <p class="mb-0">{{ $log->subject ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold text-muted small">Assessment Type</label>
                                                <p class="mb-0">{{ $log->assessment_type ?? '-' }}</p>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold text-muted small">Remarks</label>
                                                <p class="mb-0">{{ $log->remarks ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="card shadow-sm mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Actions</h6>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.attendance.edit', $log) }}" class="btn btn-primary">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.attendance.destroy', $log) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this attendance log?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
