@extends('admin.components.template')

@section('breadcrumb')
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-notes me-2"></i>
                    Class Records
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Class Records</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Reg #</th>
                        <th>Academic Year</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Record</th>
                        <th>Score</th>
                        <th>Max</th>
                        <th>Type</th>
                        <th>Quarter</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        @php($en = $row->subjectEnrollment)
                        <tr>
                            <td>{{ $en?->studentEnrollment?->student?->name }}</td>
                            <td>{{ $en?->studentEnrollment?->registration_number }}</td>
                            <td>{{ $en?->studentEnrollment?->academicYear?->display_name }}</td>
                            <td>{{ $en?->academicYearStrandSubject?->subject?->name }}</td>
                            <td>{{ $en?->academicYearStrandSubject?->teacher?->name }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->score }}</td>
                            <td>{{ $row->max_score }}</td>
                            <td>{{ ucfirst($row->type ?? '') }}</td>
                            <td>{{ $row->quarter }}</td>
                            <td>{{ optional($row->date_given)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.subject-record-results.index', ['subject_record_id' => $row->id]) }}" class="btn btn-sm btn-outline-primary">Entries</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No class records found.</td>
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
