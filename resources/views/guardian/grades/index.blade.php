@extends('guardian.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Student Grades</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guardian.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Grades</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if($students->isEmpty())
    <div class="alert alert-info">
        <i class="ti ti-info-circle me-2"></i>You don't have any students linked to your account.
    </div>
@else
<!-- Student & Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('guardian.grades.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label"><strong>Select Student</strong></label>
                        <select name="student_id" class="form-select" onchange="this.form.submit()">
                            @foreach($students as $stud)
                                <option value="{{ $stud->id }}" {{ $selectedStudentId == $stud->id ? 'selected' : '' }}>
                                    {{ $stud->name }} ({{ $stud->student_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->semester }} Sem)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" onchange="this.form.submit()">
                            <option value="midterm" {{ ($selectedTerm ?? '') === 'midterm' ? 'selected' : '' }}>Midterm</option>
                            <option value="finals" {{ ($selectedTerm ?? '') === 'finals' ? 'selected' : '' }}>Finals</option>
                            <option value="final" {{ ($selectedTerm ?? '') === 'final' ? 'selected' : '' }}>Final Grade</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="this.form.submit()">
                            @foreach($gradeLevels as $value => $label)
                                <option value="{{ $value }}" {{ ($selectedGradeLevel ?? 'all') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Student Info Card -->
@if($student)
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($student->profile_picture)
                        <img src="{{ asset('storage/' . $student->profile_picture) }}" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $student->name }}">
                    @else
                        <div class="avatar avatar-lg bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-1">{{ $student->name }}</h5>
                        <p class="mb-0 text-muted">
                            <i class="ti ti-id me-1"></i>{{ $student->student_number }} | 
                            <i class="ti ti-school me-1"></i>{{ $student->program }} | 
                            <i class="ti ti-calendar me-1"></i>{{ $student->academic_year }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Privacy Notice if Student Disabled Access -->
@if(isset($accessDenied) && $accessDenied)
<div class="row">
    <div class="col-md-12">
        <div class="card border-warning">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-lock" style="font-size: 4rem; color: #ffc107;"></i>
                </div>
                <h4 class="mb-3">Privacy Settings Enabled</h4>
                <p class="text-muted mb-4">
                    {{ $student->first_name }} has chosen to keep their grades and academic information private.<br>
                    This information is not currently available for viewing.
                </p>
                <div class="alert alert-info d-inline-block">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Note:</strong> Students have the right to control who can access their academic information. 
                    Please discuss this directly with your child if you have questions.
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Grades Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ti ti-report-analytics me-2"></i>Grades</h5>
            </div>
            <div class="card-body">
                @if($grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">1st Sem</th>
                                    <th class="text-center">2nd Sem</th>
                                    <th class="text-center">Average</th>
                                    <th class="text-center">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $grade)
                                    <tr>
                                        <td>{{ $grade['subject_code'] }}</td>
                                        <td>{{ $grade['subject_name'] }}</td>
                                        <td class="text-center">
                                            @if($grade['fq_grade'])
                                                <span class="badge grade-badge {{ $grade['fq_grade'] >= 90 ? 'bg-success' : ($grade['fq_grade'] >= 80 ? 'bg-primary' : ($grade['fq_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['fq_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['sq_grade'])
                                                <span class="badge grade-badge {{ $grade['sq_grade'] >= 90 ? 'bg-success' : ($grade['sq_grade'] >= 80 ? 'bg-primary' : ($grade['sq_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['sq_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['a_grade'])
                                                <span class="badge grade-badge {{ $grade['a_grade'] >= 90 ? 'bg-success' : ($grade['a_grade'] >= 80 ? 'bg-primary' : ($grade['a_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['a_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($grade['f_grade'])
                                                <span class="badge grade-badge {{ $grade['f_grade'] >= 90 ? 'bg-success' : ($grade['f_grade'] >= 80 ? 'bg-primary' : ($grade['f_grade'] >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($grade['f_grade'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($average)
                        <div class="text-end mt-3">
                            <div class="d-inline-block bg-primary text-white px-4 py-2 rounded">
                                <strong>Average: {{ $average }}</strong>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-circle me-2"></i>
                        <strong>No grades available.</strong>
                        @if($student && $academicYears->isEmpty())
                            <p class="mb-0 mt-2">Your child hasn't been enrolled in any academic year yet. Please contact the school administration office to complete the enrollment process.</p>
                        @else
                            <p class="mb-0 mt-2">No grades have been recorded for this selection. If your child is enrolled, grades will appear here once teachers submit them.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Decision Support System Section -->
@if($overallAverage > 0)
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="ti ti-chart-dots me-2"></i>Performance Analysis</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Overall Average</h6>
                            <h2 class="mb-0 {{ $overallAverage >= 90 ? 'text-success' : ($overallAverage >= 80 ? 'text-primary' : ($overallAverage >= 75 ? 'text-warning' : 'text-danger')) }}">
                                {{ $overallAverage }}
                            </h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Total Subjects</h6>
                            <h2 class="mb-0 text-primary">{{ $totalSubjects }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Status</h6>
                            <h5 class="mb-0">
                                <span class="badge {{ $overallAverage >= 90 ? 'bg-success' : ($overallAverage >= 80 ? 'bg-primary' : ($overallAverage >= 75 ? 'bg-warning' : 'bg-danger')) }}">
                                    {{ $overallAverage >= 90 ? 'Excellent' : ($overallAverage >= 80 ? 'Good' : ($overallAverage >= 75 ? 'Satisfactory' : 'Needs Improvement')) }}
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>

                @if($strengths->count() > 0)
                <div class="mb-3">
                    <h6 class="text-success"><i class="ti ti-thumb-up me-2"></i>Strengths</h6>
                    <ul class="list-unstyled">
                        @foreach($strengths as $strength)
                            <li class="mb-1">
                                <i class="ti ti-check text-success me-2"></i>
                                <strong>{{ $strength['subject'] }}</strong>: {{ $strength['grade'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($weaknesses->count() > 0)
                <div class="mb-3">
                    <h6 class="text-danger"><i class="ti ti-alert-triangle me-2"></i>Areas Needing Attention</h6>
                    <ul class="list-unstyled">
                        @foreach($weaknesses as $weakness)
                            <li class="mb-1">
                                <i class="ti ti-alert-circle text-danger me-2"></i>
                                <strong>{{ $weakness['subject'] }}</strong>: {{ $weakness['grade'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(count($recommendations) > 0)
                <div>
                    <h6 class="text-primary"><i class="ti ti-bulb me-2"></i>Recommendations</h6>
                    <ul class="list-unstyled">
                        @foreach($recommendations as $recommendation)
                            <li class="mb-2">
                                <i class="ti ti-arrow-right text-primary me-2"></i>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endif

<style>
    .grade-badge {
        font-size: 16px !important;
        padding: 0.5em 0.75em;
    }
    
    .avatar-lg {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
</style>
@endsection
