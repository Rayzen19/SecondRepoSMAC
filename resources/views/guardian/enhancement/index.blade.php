@extends('guardian.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Academic Enhancement & Performance Analysis</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('guardian.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Enhancement</li>
            </ul>
        </div>
    </div>
</div>

@if($students->isEmpty())
    <div class="alert alert-info">
        <i class="ti ti-info-circle me-2"></i>You don't have any students linked to your account.
    </div>
@else

<!-- Student & Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('guardian.enhancement.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><strong>Select Student</strong></label>
                <select name="student_id" class="form-select" onchange="this.form.submit()">
                    @foreach($students as $stud)
                        <option value="{{ $stud->id }}" {{ $selectedStudentId == $stud->id ? 'selected' : '' }}>
                            {{ $stud->name }} ({{ $stud->student_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select">
                    <option value="">-- Select Academic Year --</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} - {{ $year->semester }} Semester
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select">
                    <option value="midterm" {{ $selectedTerm == 'midterm' ? 'selected' : '' }}>Midterm</option>
                    <option value="finals" {{ $selectedTerm == 'finals' ? 'selected' : '' }}>Finals</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-info w-100">
                    <i class="ti ti-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Student Info Card -->
@if($student)
<div class="card mb-4 border-primary">
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
@endif

<!-- Decision Support System Section -->
@if($selectedYearId && !empty($dssRecommendations))
<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <div class="d-flex align-items-center">
            <i class="ti ti-chart-dots fs-3 me-2"></i>
            <div>
                <h5 class="card-title mb-0 text-white">Decision Support System</h5>
                <small class="text-white-50">Performance Analysis & Recommendations for Your Child</small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Overall Status -->
        <div class="alert alert-{{ $dssRecommendations['overall_status'] == 'excellent' ? 'success' : ($dssRecommendations['overall_status'] == 'good' ? 'info' : ($dssRecommendations['overall_status'] == 'satisfactory' ? 'warning' : 'danger')) }} mb-4">
            <div class="d-flex align-items-center">
                <i class="ti ti-{{ $dssRecommendations['overall_status'] == 'excellent' ? 'trophy' : ($dssRecommendations['overall_status'] == 'good' ? 'thumb-up' : ($dssRecommendations['overall_status'] == 'satisfactory' ? 'alert-circle' : 'alert-triangle')) }} fs-2 me-3"></i>
                <div>
                    <h6 class="mb-1">Overall Performance: <strong>{{ ucwords(str_replace('_', ' ', $dssRecommendations['overall_status'])) }}</strong></h6>
                    <p class="mb-0">{{ $dssRecommendations['overall_message'] }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Priority Actions -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="ti ti-flag me-2"></i>Priority Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(!empty($dssRecommendations['priority_actions']))
                            @foreach($dssRecommendations['priority_actions'] as $action)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $action['title'] }}</h6>
                                        <span class="badge bg-{{ $action['priority'] == 'high' ? 'danger' : ($action['priority'] == 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($action['priority']) }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $action['description'] }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">No priority actions at this time.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Strengths & Areas to Improve -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="ti ti-list-check me-2"></i>Performance Overview
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(!empty($dssRecommendations['strengths']))
                            <div class="mb-3">
                                <h6 class="text-success mb-2">
                                    <i class="ti ti-thumb-up me-1"></i>Strengths
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($dssRecommendations['strengths'] as $strength)
                                        <li class="mb-1">
                                            <i class="ti ti-check text-success me-1"></i>
                                            <small>{{ $strength }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($dssRecommendations['areas_to_improve']))
                            <div>
                                <h6 class="text-danger mb-2">
                                    <i class="ti ti-alert-triangle me-1"></i>Areas Needing Support
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($dssRecommendations['areas_to_improve'] as $area)
                                        <li class="mb-1">
                                            <i class="ti ti-alert-circle text-danger me-1"></i>
                                            <small>{{ $area['area'] }} ({{ $area['percentage'] }}%)</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(empty($dssRecommendations['strengths']) && empty($dssRecommendations['areas_to_improve']))
                            <p class="text-muted mb-0">Performance data is being analyzed...</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Type Analysis -->
        @if(!empty($dssRecommendations['assessment_type_analysis']))
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ti ti-clipboard-check me-2"></i>Assessment Type Performance
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($dssRecommendations['assessment_type_analysis'] as $assessment)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>{{ $assessment['type'] }}</strong>
                                    <span class="badge bg-{{ $assessment['status'] == 'excellent' ? 'success' : ($assessment['status'] == 'good' ? 'primary' : ($assessment['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                        {{ $assessment['percentage'] }}%
                                    </span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $assessment['status'] == 'excellent' ? 'success' : ($assessment['status'] == 'good' ? 'primary' : ($assessment['status'] == 'needs_attention' ? 'warning' : 'danger')) }}" 
                                         style="width: {{ $assessment['percentage'] }}%"></div>
                                </div>
                                <p class="text-muted small mb-0">{{ $assessment['recommendation'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Subject Analysis -->
        @if(!empty($dssRecommendations['subject_analysis']))
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="ti ti-book me-2"></i>Subject Performance Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th class="text-center">Performance</th>
                                <th class="text-center">Status</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dssRecommendations['subject_analysis'] as $subject)
                                <tr>
                                    <td>
                                        <strong>{{ $subject['subject'] }}</strong>
                                        <br><small class="text-muted">{{ $subject['code'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $subject['status'] == 'excellent' ? 'success' : ($subject['status'] == 'good' ? 'primary' : ($subject['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                            {{ $subject['percentage'] }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-sm bg-{{ $subject['status'] == 'excellent' ? 'success' : ($subject['status'] == 'good' ? 'primary' : ($subject['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                            {{ ucwords(str_replace('_', ' ', $subject['status'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $subject['recommendation'] }}</small>
                                        @if(!empty($subject['weak_types']))
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="ti ti-alert-circle"></i>
                                                    Weak in: 
                                                    @foreach($subject['weak_types'] as $weak)
                                                        {{ $weak['type'] }} ({{ $weak['percentage'] }}%){{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Performance Summary -->
@if($performanceSummary['total_assessments'] > 0)
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Assessments</h6>
                <h3 class="text-primary mb-0">{{ $performanceSummary['total_assessments'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Completed</h6>
                <h3 class="text-success mb-0">{{ $performanceSummary['completed_assessments'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Average Score</h6>
                <h3 class="text-info mb-0">{{ $performanceSummary['average_score'] }}%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Score Range</h6>
                <h3 class="text-warning mb-0">
                    {{ $performanceSummary['lowest_score'] }} - {{ $performanceSummary['highest_score'] }}%
                </h3>
            </div>
        </div>
    </div>
</div>
@endif

@if(!$selectedYearId)
<div class="alert alert-warning">
    <i class="ti ti-alert-circle me-2"></i>
    <strong>No performance data available.</strong>
    @if($student && $academicYears->isEmpty())
        <p class="mb-0 mt-2">Your child hasn't been enrolled in any academic year yet. Please contact the school administration office to complete the enrollment process. Once enrolled, performance analysis will be available here.</p>
    @else
        <p class="mb-0 mt-2">Please select a student, academic year, and term above to view performance analysis.</p>
    @endif
</div>
@endif

@endif

@endsection
