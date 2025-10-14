@extends('teacher.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Class Records</h4>
        <div class="text-muted small">Academic Year: {{ $activeYear?->display_name ?? '—' }}</div>
    </div>
</div>

@if($rows->isEmpty())
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-notebook"></i></div>
            <h5 class="mb-2">No class records yet</h5>
            <p class="text-muted mb-0">Your assigned subjects will appear here once linked to a section in an academic year.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Academic Details</th>
                        <th>Subject</th>
                        <th>Strand & Section</th>
                        <th>Adviser</th>
                        <th>Students No.</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td>{{ $r['year'] ?? '—' }} ({{ $r['semester'] ?? '—' }} Semester)</td>
                            <td>
                                @if(!empty($r['subject_name']) || !empty($r['subject_code']))
                                    <div class="small text-muted text-uppercase">{{ $r['subject_code'] ?? '' }}</div>
                                    <div>{{ $r['subject_name'] ?? '—' }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $r['strand'] ?? '—' }} {{ $r['section'] ?? '—' }}</td>
                            <td>{{ $r['adviser'] ?? '—' }}</td>
                            <td>{{ $r['students_count'] ?? '0' }}</td>
                            <td>
                                @php $status = strtolower($r['ay_status'] ?? ''); @endphp
                                @switch($status)
                                    @case('pending')
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        @break
                                    @case('ongoing enrollment')
                                        <span class="badge bg-info-subtle text-info">Ongoing Enrollment</span>
                                        @break
                                    @case('ongoing school year')
                                        <span class="badge bg-success-subtle text-success">Ongoing School Year</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-secondary-subtle text-secondary">Completed</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-muted border">N/A</span>
                                @endswitch
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="{{ route('teacher.class-records.show', ['assignment' => $r['id']]) }}">
                                    <i class="ti ti-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
