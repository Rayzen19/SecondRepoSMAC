@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject Teachers</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subjects.index') }}">Subjects</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subjects.show', $subject) }}">{{ $subject->code }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Teachers</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Back to Subjects
            </a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex align-items-center">
                <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:18px;">
                    {{ strtoupper(substr($subject->name ?? '', 0, 1)) }}
                </div>
                <div>
                    <h5 class="mb-0">{{ $subject->name }}</h5>
                    <small class="text-muted">{{ $subject->code }} | {{ $subject->semester }} Semester</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($teachers->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Employee Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th>Academic Year(s)</th>
                                <th>Strand(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacherData)
                                @php
                                    $teacher = $teacherData['teacher'];
                                    $assignments = $teacherData['assignments'];
                                    $hasAssignment = $teacherData['has_assignment'] ?? false;
                                    $academicYears = $assignments->pluck('academic_year')->unique()->values();
                                    $strands = $assignments->pluck('strand')->unique()->values();
                                @endphp
                                <tr class="{{ !$hasAssignment ? 'table-light' : '' }}">
                                    <td class="font-monospace">{{ $teacher->employee_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar rounded-circle {{ $hasAssignment ? 'bg-info' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:14px;">
                                                {{ strtoupper(substr($teacher->first_name ?? '', 0, 1) . substr($teacher->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $teacher->last_name }}, {{ $teacher->first_name }} {{ $teacher->middle_name ? substr($teacher->middle_name, 0, 1) . '.' : '' }}</div>
                                                @if($teacher->suffix)
                                                    <small class="text-muted">{{ $teacher->suffix }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $teacher->email ?? 'N/A' }}</td>
                                    <td>{{ $teacher->department ?? 'N/A' }}</td>
                                    <td>{{ $teacher->specialization ?? 'N/A' }}</td>
                                    <td>
                                        @if($hasAssignment)
                                            <span class="badge bg-success">Assigned</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Qualified Only</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($academicYears->count() > 0)
                                            @foreach($academicYears as $year)
                                                <span class="badge bg-success me-1">{{ $year }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No assignment</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($strands->count() > 0)
                                            @foreach($strands as $strand)
                                                <span class="badge bg-primary me-1">{{ $strand }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No assignment</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-users" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No teachers assigned or qualified for this subject yet</p>
                    <p class="text-muted small">Teachers will appear here once they are assigned to teach this subject in an academic year or marked as qualified.</p>
                </div>
            @endif
        </div>
        @if($teachers->count() > 0)
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="ti ti-info-circle me-1"></i>
                        Total: <strong>{{ $teachers->count() }}</strong> {{ Str::plural('teacher', $teachers->count()) }}
                        @php
                            $assignedCount = $teachers->where('has_assignment', true)->count();
                            $qualifiedCount = $teachers->where('has_assignment', false)->count();
                        @endphp
                        @if($assignedCount > 0 && $qualifiedCount > 0)
                            ({{ $assignedCount }} assigned, {{ $qualifiedCount }} qualified only)
                        @endif
                    </div>
                    <div class="text-muted small">
                        <span class="badge bg-success me-2">Assigned</span> = Has active teaching assignment
                        <span class="badge bg-warning text-dark ms-2">Qualified Only</span> = Can teach but not yet assigned
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
