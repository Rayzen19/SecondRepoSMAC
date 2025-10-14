@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4"><i class="ti ti-clipboard-list me-2"></i>Class Record Entries</h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Class Record Entries</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subject-record-results.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>New Entry</a>
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
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>AY</th>
                        <th>Subject</th>
                        <th>Record</th>
                        <th>Scores</th>
                        <th>Date Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $row)
                        @php($sr = $row->subjectRecord)
                        @php($se = $sr?->subjectEnrollment)
                        <tr>
                            <td>{{ $row->student?->name }}</td>
                            <td>{{ $se?->studentEnrollment?->academicYear?->display_name }}</td>
                            <td>{{ $se?->academicYearStrandSubject?->subject?->name }}</td>
                            <td>{{ $sr?->name }}</td>
                            <td>
                                Raw {{ $row->raw_score }} / Base {{ $row->base_score }} → Final {{ $row->final_score }}
                            </td>
                            <td>{{ optional($row->date_submitted)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.subject-record-results.show', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('admin.subject-record-results.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection
