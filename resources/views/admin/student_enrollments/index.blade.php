@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Student Enrollments</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Enrollment Management</li>
                <li class="breadcrumb-item active" aria-current="page">Student Enrollments</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.student-enrollments.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="ti ti-circle-plus me-2"></i>New Enrollment
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

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Enrollments -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-primary rounded-circle">
                                <i class="ti ti-school"></i>
                            </span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Total Enrollments</p>
                            <h4>{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Total Enrollments -->

        <!-- Enrolled Students -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-success rounded-circle">
                                <i class="ti ti-user-check"></i>
                            </span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Currently Enrolled</p>
                            <h4>{{ $stats['enrolled'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Enrolled Students -->

        <!-- Completed -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-info rounded-circle">
                                <i class="ti ti-certificate"></i>
                            </span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Completed</p>
                            <h4>{{ $stats['completed'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Completed -->

        <!-- Dropped -->
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center overflow-hidden">
                        <div>
                            <span class="avatar avatar-lg bg-danger rounded-circle">
                                <i class="ti ti-user-x"></i>
                            </span>
                        </div>
                        <div class="ms-2 overflow-hidden">
                            <p class="fs-12 fw-medium mb-1 text-truncate">Dropped</p>
                            <h4>{{ $stats['dropped'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Dropped -->
    </div>
    <!-- /Statistics Cards -->

    <!-- Enrollments Table Card -->
    <div class="card">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="ti ti-list me-2"></i>Enrollment Records</h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary">{{ $stats['total'] }} Total</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Filter Section -->
            <div class="p-3 border-bottom">
                <form method="GET" action="{{ route('admin.student-enrollments.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">All Academic Years</option>
                                @isset($academicYears)
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                            {{ $ay->display_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Strand</label>
                            <select name="strand_id" class="form-select">
                                <option value="">All Strands</option>
                                @isset($strands)
                                    @foreach($strands as $st)
                                        <option value="{{ $st->id }}" {{ request('strand_id') == $st->id ? 'selected' : '' }}>
                                            {{ $st->code }} — {{ $st->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach(['enrolled','dropped','completed'] as $st)
                                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                                        {{ ucwords($st) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit">
                                    <i class="ti ti-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.student-enrollments.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /Filter Section -->

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Registration #</th>
                            <th>Student Details</th>
                            <th>Academic Year</th>
                            <th>Strand & Section</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $row)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace">
                                        {{ $row->registration_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md bg-primary-transparent rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-dark mb-0 fw-medium">
                                                <a href="{{ route('admin.student-enrollments.show', $row) }}" class="text-decoration-none">
                                                    {{ $row->student?->name }}
                                                </a>
                                            </p>
                                            <span class="fs-12 text-muted">
                                                <i class="ti ti-id-badge me-1"></i>{{ $row->student?->student_number }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $row->academicYear?->display_name }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-info me-1">{{ $row->strand?->code }}</span>
                                        <span class="text-muted fs-12">
                                            {{ $row->academicYearStrandSection?->section?->grade }} 
                                            {{ $row->academicYearStrandSection?->section?->name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'enrolled' => ['badge' => 'success', 'icon' => 'ti-user-check'],
                                            'completed' => ['badge' => 'primary', 'icon' => 'ti-certificate'],
                                            'dropped' => ['badge' => 'danger', 'icon' => 'ti-user-x']
                                        ];
                                        $config = $statusConfig[$row->status] ?? ['badge' => 'secondary', 'icon' => 'ti-help'];
                                    @endphp
                                    <span class="badge badge-{{ $config['badge'] }} d-inline-flex align-items-center badge-xs">
                                        <i class="ti {{ $config['icon'] }} me-1"></i>{{ ucfirst($row->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-icon d-inline-flex justify-content-center w-100">
                                        <a href="{{ route('admin.student-enrollments.show', $row) }}" 
                                           class="me-2" 
                                           title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.student-enrollments.edit', $row) }}" 
                                           class="me-2" 
                                           title="Edit Enrollment">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.student-enrollments.destroy', $row) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this enrollment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-link p-0 m-0 align-baseline text-danger" 
                                                    title="Delete Enrollment">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ti ti-folder-x fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No enrollments found.</p>
                                        @if(request()->hasAny(['academic_year_id', 'strand_id', 'status']))
                                            <small>Try adjusting your filters or <a href="{{ route('admin.student-enrollments.index') }}">reset filters</a>.</small>
                                        @else
                                            <small>Get started by <a href="{{ route('admin.student-enrollments.create') }}">creating a new enrollment</a>.</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /Table -->

            <!-- Pagination -->
            @if($enrollments->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="text-muted small mb-2 mb-md-0">
                            Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() }} entries
                        </div>
                        <div>
                            {{ $enrollments->links() }}
                        </div>
                    </div>
                </div>
            @elseif($enrollments->count() > 0)
                <div class="p-3 border-top">
                    <div class="text-muted small text-center">
                        Showing {{ $enrollments->count() }} {{ $enrollments->count() == 1 ? 'entry' : 'entries' }}
                    </div>
                </div>
            @endif
            <!-- /Pagination -->
        </div>
    </div>
    <!-- /Enrollments Table Card -->
</div>
@endsection
