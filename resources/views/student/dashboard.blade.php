@extends('student.components.template')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Student Dashboard</h4>
                        <p class="text-muted mb-0">Welcome back, {{ $student->first_name ?? $student->name ?? 'Student' }}.</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Academic Year: {{ $student->academic_year ?? ($enrollment->academicYear->year_name ?? 'N/A') }}</small>
                        <div>{{ isset($enrollment->section) ? 'Section: ' . ($enrollment->section->section_name ?? 'N/A') : '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Announcements</h5>
                </div>
                <div class="card-body">
                    @if(isset($announcements) && $announcements->count())
                        <ul class="list-unstyled">
                            @foreach($announcements as $a)
                                <li class="mb-3">
                                    <strong>{{ $a->title }}</strong>
                                    <div class="text-muted small">{{ optional($a->published_at)->format('M d, Y') ?? $a->created_at->format('M d, Y') }}</div>
                                    <div class="mt-1">{{ Str::limit(strip_tags($a->content ?? $a->description), 140) }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No announcements found.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enrolled Subjects</h5>
                </div>
                <div class="card-body">
                    @if(isset($subjects) && $subjects->count())
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-end">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $s)
                                    <tr>
                                        <td>{{ $s->subject_name ?? ($s->subject->name ?? 'N/A') }}</td>
                                        <td class="text-end">{{ isset($s->f_grade) ? number_format($s->f_grade, 2) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No enrolled subjects found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection