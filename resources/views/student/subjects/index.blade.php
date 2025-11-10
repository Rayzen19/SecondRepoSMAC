@extends('student.components.template')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Subjects</h4>
        <div class="text-muted small">Academic Year: {{ $activeYear?->display_name ?? '—' }}</div>
    </div>
    <div></div>
    
</div>

@if($subjects->isEmpty())
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-books"></i></div>
            <h5 class="mb-2">No subjects yet</h5>
            <p class="text-muted mb-0">Once you are enrolled in subjects for the active academic year, they will appear here.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 10%">Code</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $s)
                            <tr>
                                <td class="text-nowrap fw-semibold">{{ $s['subject_code'] ?? '—' }}</td>
                                <td>{{ $s['subject_name'] ?? '—' }}</td>
                                <td class="text-nowrap">{{ $s['teacher'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info mt-3 mb-0">
                <i class="ti ti-info-circle me-2"></i>
                Grades will be visible here once they are published by your teachers.
            </div>
            <div class="text-muted small mt-2">Semester: <span class="fw-semibold">{{ $activeYear?->semester ?? '—' }}</span></div>
        </div>
    </div>
@endif
@endsection
