@extends('admin.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Attendance / List</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Success Alert --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table comman-shadow">
            <div class="card-body">
                {{-- Filter Form --}}
                <form action="{{ route('admin.attendance.index') }}" method="GET" id="filterForm">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-auto text-end float-end ms-auto download-grp">
                                <a href="{{ route('admin.attendance.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Attendance
                                </a>
                                <button type="submit" form="exportForm" class="btn btn-outline-primary">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                            <div class="input-block local-forms">
                                <label>Strand / Section</label>
                                <select class="form-control select" name="strand">
                                    <option value="">Select Strand</option>
                                    @foreach($strands as $strand)
                                        <option value="{{ $strand }}" {{ request('strand') == $strand ? 'selected' : '' }}>
                                            {{ $strand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Year Level</label>
                                <select class="form-control select" name="year_level">
                                    <option value="">Select Year</option>
                                    @foreach($yearLevels as $level)
                                        <option value="{{ $level }}" {{ request('year_level') == $level ? 'selected' : '' }}>
                                            Grade {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Semester</label>
                                <select class="form-control select" name="semester">
                                    <option value="">Select Semester</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>
                                            {{ $sem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                            <div class="input-block local-forms">
                                <label>Subject</label>
                                <input type="text" class="form-control" name="subject" value="{{ request('subject') }}" placeholder="Enter subject">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Assessment</label>
                                <input type="text" class="form-control" name="assessment" value="{{ request('assessment') }}" placeholder="Assessment type">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-sm-6 col-md-6 col-xl-4">
                            <div class="input-block local-forms">
                                <label>Search Student</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Student ID or Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Status</label>
                                <select class="form-control select" name="status">
                                    <option value="">All Status</option>
                                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Date From</label>
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2">
                            <div class="input-block local-forms">
                                <label>Date To</label>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-xl-2 d-flex align-items-end">
                            <div class="input-block local-forms w-100">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(request()->hasAny(['search', 'strand', 'year_level', 'semester', 'subject', 'assessment', 'date_from', 'date_to', 'status']))
                        <div class="row mb-3">
                            <div class="col-12">
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        </div>
                    @endif
                </form>

                {{-- Export Form (hidden) --}}
                <form action="{{ route('admin.attendance.export') }}" method="GET" id="exportForm" class="d-none">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="strand" value="{{ request('strand') }}">
                    <input type="hidden" name="year_level" value="{{ request('year_level') }}">
                    <input type="hidden" name="semester" value="{{ request('semester') }}">
                    <input type="hidden" name="subject" value="{{ request('subject') }}">
                    <input type="hidden" name="assessment" value="{{ request('assessment') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                </form>

                {{-- Results Info --}}
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label">
                                    Overall <input type="checkbox" class="form-check-input ms-2" checked>
                                </label>
                            </div>
                            <div class="text-muted">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 table-striped">
                        <thead class="student-thread">
                            <tr>
                                <th>Student ID</th>
                                <th>Strand</th>
                                <th>Year Level</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Profile</th>
                                <th class="text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->student_number }}</td>
                                    <td>{{ $log->strand }}</td>
                                    <td>{{ $log->year_level }}</td>
                                    <td>{{ $log->date->format('m-d-y') }}</td>
                                    <td>{{ $log->time->format('H:i') }}</td>
                                    <td>
                                        <div class="avatar avatar-sm">
                                            @if($log->student && $log->student->profile_picture)
                                                <img src="{{ asset('storage/' . $log->student->profile_picture) }}" class="rounded-circle" alt="Profile">
                                            @else
                                                <div class="avatar-content bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    {{ substr($log->student_name ?? 'N', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.attendance.show', $log) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No attendance logs found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="row mt-3">
                    <div class="col-12">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select an option',
        allowClear: true
    });
});
</script>
@endpush
