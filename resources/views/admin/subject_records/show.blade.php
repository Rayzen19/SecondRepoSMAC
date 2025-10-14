@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-eye me-2"></i>Class Record</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subject-records.index') }}">Class Records</a></li>
                <li class="breadcrumb-item active" aria-current="page">View</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subject-records.edit', $record) }}" class="btn btn-outline-primary d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
        <div class="mb-2 ms-2">
            <a href="{{ route('admin.subject-record-results.index', ['subject_record_id' => $record->id]) }}" class="btn btn-outline-secondary d-flex align-items-center"><i class="ti ti-list-details me-2"></i>Entries</a>
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

    <div class="card mb-3">
        <div class="card-body">
            @php($ass = $record->assignment)
            <div class="row g-3">
                <div class="col-md-3"><strong>Student</strong><div>—</div></div>
                <div class="col-md-3"><strong>Academic Year</strong><div>{{ $ass?->academicYear?->display_name }}</div></div>
                <div class="col-md-3"><strong>Subject</strong><div>{{ $ass?->subject?->name }}</div></div>
                <div class="col-md-3"><strong>Teacher</strong><div>{{ $ass?->teacher?->name }}</div></div>
                <div class="col-md-4"><strong>Name</strong><div>{{ $record->name }}</div></div>
                <div class="col-md-2"><strong>Quarter</strong><div>{{ $record->quarter }}</div></div>
                <div class="col-md-2"><strong>Type</strong><div>{{ ucfirst($record->type ?? '') }}</div></div>
                <div class="col-md-2"><strong>Max</strong><div>{{ $record->max_score }}</div></div>
                <div class="col-md-2"><strong>Date</strong><div>{{ optional($record->date_given)->format('Y-m-d') }}</div></div>
                <div class="col-12"><strong>Remarks</strong><div>{{ $record->remarks }}</div></div>
                <div class="col-12"><strong>Description</strong><div>{{ $record->description }}</div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h6 class="mb-0">Records</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subject-records.export', $record) }}" class="btn btn-sm btn-outline-success"><i class="ti ti-download me-1"></i>Download CSV</a>
                <a href="{{ route('admin.subject-record-results.create', ['subject_record_id' => $record->id]) }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-1"></i>Add Entry</a>
            </div>
        </div>
        <div class="card-body">
            @php($boys = $record->results->filter(fn($r) => strtolower($r->student?->gender ?? '') === 'male'))
            @php($girls = $record->results->filter(fn($r) => strtolower($r->student?->gender ?? '') === 'female'))

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <h6 class="mb-2">Boys ({{ $boys->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Student</th>
                                    <th>Scores</th>
                                    <th>Submitted</th>
                                    <th style="width:160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($boys->values() as $i => $res)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $res->student?->name }}</td>
                                        <td>Raw {{ $res->raw_score }} / Base {{ $res->base_score }} → Final {{ $res->final_score }}</td>
                                        <td>{{ optional($res->date_submitted)->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.subject-record-results.show', $res) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ route('admin.subject-record-results.edit', $res) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No entries.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <h6 class="mb-2">Girls ({{ $girls->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Student</th>
                                    <th>Scores</th>
                                    <th>Submitted</th>
                                    <th style="width:160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($girls->values() as $i => $res)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $res->student?->name }}</td>
                                        <td>Raw {{ $res->raw_score }} / Base {{ $res->base_score }} → Final {{ $res->final_score }}</td>
                                        <td>{{ optional($res->date_submitted)->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.subject-record-results.show', $res) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ route('admin.subject-record-results.edit', $res) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No entries.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
