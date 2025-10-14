@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-notes me-2"></i>Class Records</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Class Records</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subject-records.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>New Record</a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <div class="card">
        <div class="p-3 border-bottom bg-light">
            <form method="GET" action="{{ route('admin.subject-records.index') }}" class="row g-2">
                <div class="col-md-4">
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit"><i class="ti ti-filter me-1"></i>Filter</button>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin.subject-records.index') }}" class="btn btn-outline-secondary"><i class="ti ti-refresh me-1"></i>Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>AY</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Record</th>
                        <th>Max</th>
                        <th>Type</th>
                        <th>Quarter</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        @php($ass = $row->assignment)
                        <tr>
                            <td>—</td>
                            <td>{{ $ass?->academicYear?->display_name }}</td>
                            <td>{{ $ass?->subject?->name }}</td>
                            <td>{{ $ass?->teacher?->name }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->max_score }}</td>
                            <td>{{ ucfirst($row->type ?? '') }}</td>
                            <td>{{ $row->quarter }}</td>
                            <td>{{ optional($row->date_given)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.subject-records.show', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('admin.subject-records.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No class records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
