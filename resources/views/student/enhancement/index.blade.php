@extends('student.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Academic Enhancement & Decision Support</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Enhancement</li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.enhancement.index') }}" class="row g-3">
            <div class="col-md-5">
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
            <div class="col-md-4">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select">
                    <option value="midterm" {{ $selectedTerm == 'midterm' ? 'selected' : '' }}>Midterm</option>
                    <option value="finals" {{ $selectedTerm == 'finals' ? 'selected' : '' }}>Finals</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn bg-info w-100">
                    <i class="ti ti-filter me-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Decision Support System Section -->
@if($selectedYearId && !empty($dssRecommendations))
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <div class="d-flex align-items-center">
            <i class="ti ti-brain fs-3 me-2"></i>
            <div>
                <h5 class="card-title mb-0 text-white">Decision Support System</h5>
                <small class="text-white-50">AI-Powered Performance Analysis & Recommendations</small>
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
                <div class="card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="ti ti-flag me-2"></i>Priority Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(!empty($dssRecommendations['priority_actions']))
                            <div class="list-group list-group-flush">
                                @foreach($dssRecommendations['priority_actions'] as $index => $action)
                                    <div class="list-group-item border-0 px-0 {{ $index > 0 ? 'border-top' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">
                                                <span class="badge bg-{{ $action['priority'] == 'high' ? 'danger' : ($action['priority'] == 'medium' ? 'warning' : 'info') }} me-2">
                                                    {{ ucfirst($action['priority']) }}
                                                </span>
                                                {{ $action['title'] }}
                                            </h6>
                                            @if($action['percentage'] < 100)
                                                <span class="badge bg-light text-dark">{{ $action['percentage'] }}%</span>
                                            @endif
                                        </div>
                                        <p class="mb-0 text-muted small">{{ $action['description'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No priority actions needed. Keep up the good work!</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Strengths & Areas to Improve -->
            <div class="col-lg-6 mb-4">
                <div class="row h-100">
                    <!-- Strengths -->
                    <div class="col-12 mb-3">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="ti ti-award me-2"></i>Your Strengths
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(!empty($dssRecommendations['strengths']))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($dssRecommendations['strengths'] as $strength)
                                            <span class="badge bg-info-subtle text-info border border-info">
                                                <i class="ti ti-check me-1"></i>{{ $strength }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0 small">Keep working to build your strengths!</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Areas to Improve -->
                    <div class="col-12">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="ti ti-target me-2"></i>Areas to Improve
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(!empty($dssRecommendations['areas_to_improve']))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($dssRecommendations['areas_to_improve'] as $area)
                                            <span class="badge bg-{{ $area['priority'] == 'high' ? 'danger' : 'warning' }}-subtle text-{{ $area['priority'] == 'high' ? 'danger' : 'warning' }} border border-{{ $area['priority'] == 'high' ? 'danger' : 'warning' }}">
                                                <i class="ti ti-arrow-up me-1"></i>{{ $area['area'] }} ({{ $area['percentage'] }}%)
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0 small">Great! No critical areas to improve.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Type Analysis -->
        @if(!empty($dssRecommendations['assessment_type_analysis']))
        <div class="card mb-3 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="ti ti-clipboard-list me-2"></i>Assessment Type Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($dssRecommendations['assessment_type_analysis'] as $analysis)
                        <div class="col-md-6">
                            <div class="card border-{{ $analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $analysis['type'] }}</h6>
                                        <span class="badge bg-{{ $analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                            {{ $analysis['percentage'] }}%
                                        </span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger')) }}"
                                            style="width: {{ $analysis['percentage'] }}%"></div>
                                    </div>
                                    <p class="mb-0 small text-muted">{{ $analysis['recommendation'] }}</p>
                                    <small class="text-muted">{{ $analysis['count'] }} assessment(s)</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Subject Analysis -->
        @if(!empty($dssRecommendations['subject_analysis']))
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="ti ti-books me-2"></i>Subject-wise Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="subjectAnalysisAccordion">
                    @foreach($dssRecommendations['subject_analysis'] as $index => $analysis)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#subject{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <span>
                                            <strong>{{ $analysis['subject'] }}</strong> 
                                            <small class="text-muted">({{ $analysis['code'] }})</small>
                                        </span>
                                        <span class="badge bg-{{ $analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                            {{ $analysis['percentage'] }}%
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="subject{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#subjectAnalysisAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <strong>Status:</strong> 
                                        <span class="badge bg-{{ $analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger')) }}">
                                            {{ ucwords(str_replace('_', ' ', $analysis['status'])) }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Recommendation:</strong>
                                        <p class="mb-0">{{ $analysis['recommendation'] }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Total Assessments:</strong> {{ $analysis['assessments'] }}
                                    </div>
                                    @if(!empty($analysis['weak_types']))
                                        <div class="alert alert-warning mt-3">
                                            <strong><i class="ti ti-alert-triangle me-1"></i>Weak Assessment Types in this Subject:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach($analysis['weak_types'] as $weakType)
                                                    <li>{{ $weakType['type'] }}: {{ $weakType['percentage'] }}%</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Study Tips -->
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="ti ti-bulb me-2 text-warning"></i>General Study Tips
                </h6>
                <ul class="mb-0">
                    <li class="mb-2">Create a study schedule and stick to it consistently</li>
                    <li class="mb-2">Focus on understanding concepts rather than memorizing</li>
                    <li class="mb-2">Practice regularly with different types of assessments</li>
                    <li class="mb-2">Seek help from teachers when you're struggling with a topic</li>
                    <li class="mb-2">Form study groups with classmates to learn collaboratively</li>
                    <li class="mb-0">Take breaks and maintain a healthy study-life balance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="ti ti-chart-line fs-1 text-muted mb-3"></i>
        <h5>Select Academic Year and Term</h5>
        <p class="text-muted">Please select an academic year and term above to view your personalized enhancement recommendations.</p>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const yearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (termSelect) {
        termSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.progress {
    border-radius: 5px;
}

.progress-bar {
    border-radius: 5px;
    font-weight: 600;
}

.badge {
    font-weight: 500;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}
</style>
@endsection
