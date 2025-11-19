@extends('student.components.template')

@section('content')
<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">My Subjects</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('student.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Subjects</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">Academic Year: {{ $activeYear?->display_name ?? '—' }}</h5>
                <div class="text-muted small">Semester: <span class="badge bg-secondary">{{ $activeYear?->semester ?? '—' }}</span></div>
            </div>
            <div class="text-end">
                <div class="text-muted small">Total Subjects</div>
                <h3 class="mb-0">{{ (count($coreSubjects) + count($appliedSubjects) + count($specializedSubjects)) }}</h3>
            </div>
        </div>
    </div>
</div>

@if(count($coreSubjects) == 0 && count($appliedSubjects) == 0 && count($specializedSubjects) == 0)
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-books"></i></div>
            <h5 class="mb-2">No subjects yet</h5>
            <p class="text-muted mb-0">Once you are enrolled in subjects for the active academic year, they will appear here.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                
                {{-- Core Curriculum Subjects --}}
                @if(count($coreSubjects) > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-info">
                        <i class="ti ti-book me-2"></i>Senior High School Core Curriculum Subjects
                    </h5>
                    <small class="text-muted">{{ count($coreSubjects) }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Strand</th>
                            <th>Units</th>
                            <th>Semester</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coreSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject['subject_code'] ?? '—' }}</strong></td>
                            <td>{{ $subject['subject_name'] ?? '—' }}</td>
                            <td><span class="badge bg-primary">{{ $subject['strand'] ?? '—' }}</span></td>
                            <td>{{ $subject['units'] ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $subject['semester'] ?? '—' }}</span></td>
                            <td class="text-nowrap">{{ $subject['teacher'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Applied Track Subjects --}}
                @if(count($appliedSubjects) > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-info">
                        <i class="ti ti-briefcase me-2"></i>Senior High School Applied Track Subjects
                    </h5>
                    <small class="text-muted">{{ count($appliedSubjects) }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Strand</th>
                            <th>Units</th>
                            <th>Semester</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appliedSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject['subject_code'] ?? '—' }}</strong></td>
                            <td>{{ $subject['subject_name'] ?? '—' }}</td>
                            <td><span class="badge bg-primary">{{ $subject['strand'] ?? '—' }}</span></td>
                            <td>{{ $subject['units'] ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $subject['semester'] ?? '—' }}</span></td>
                            <td class="text-nowrap">{{ $subject['teacher'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Specialized Subjects --}}
                @if(count($specializedSubjects) > 0)
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-warning">
                        <i class="ti ti-star me-2"></i>Senior High School Specialized Subjects
                    </h5>
                    <small class="text-muted">{{ count($specializedSubjects) }} subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Strand</th>
                            <th>Units</th>
                            <th>Semester</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specializedSubjects as $subject)
                        <tr>
                            <td class="font-monospace"><strong>{{ $subject['subject_code'] ?? '—' }}</strong></td>
                            <td>{{ $subject['subject_name'] ?? '—' }}</td>
                            <td><span class="badge bg-primary">{{ $subject['strand'] ?? '—' }}</span></td>
                            <td>{{ $subject['units'] ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $subject['semester'] ?? '—' }}</span></td>
                            <td class="text-nowrap">{{ $subject['teacher'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                
            </div>
        </div>
        <div class="card-footer">
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                View your grades in the <strong>Grades</strong> section once they are published by your teachers.
            </div>
        </div>
    </div>
@endif
@endsection
