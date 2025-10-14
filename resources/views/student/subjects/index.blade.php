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
    <div class="row g-3">
        @foreach($subjects as $s)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="fw-semibold text-uppercase text-muted small">{{ $s['subject_code'] ?? '—' }}</div>
                                <h5 class="mb-1">{{ $s['subject_name'] ?? '—' }}</h5>
                                <div class="text-muted small">{{ $s['teacher'] ? 'Teacher: ' . $s['teacher'] : '—' }}</div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">{{ $activeYear?->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
