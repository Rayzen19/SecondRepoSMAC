@extends('student.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Performance Analytics</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Performance</li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.performance.index') }}" class="row g-3">
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
            <div class="col-md-3">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select">
                    <option value="midterm" {{ $selectedTerm == 'midterm' ? 'selected' : '' }}>Midterm</option>
                    <option value="finals" {{ $selectedTerm == 'finals' ? 'selected' : '' }}>Finals</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($availableSubjects as $subject)
                        <option value="{{ $subject['id'] }}" {{ $selectedSubjectId == $subject['id'] ? 'selected' : '' }}>
                            {{ $subject['code'] }} - {{ $subject['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn bg-info w-100">
                    <i class="ti ti-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Performance Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Total Assessments</h6>
                        <h3 class="text-white mb-0">{{ $performanceSummary['total_assessments'] }}</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-file-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Completed</h6>
                        <h3 class="text-white mb-0">{{ $performanceSummary['completed_assessments'] }}</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-circle-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Average Score</h6>
                        <h3 class="text-white mb-0">{{ $performanceSummary['average_score'] }}%</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-chart-bar fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Highest Score</h6>
                        <h3 class="text-white mb-0">{{ $performanceSummary['highest_score'] }}%</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-trending-up fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Assessments Doughnut Chart -->
@if($allAssessments->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">All Assessments - Doughnut Chart</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-center mb-4">
            <canvas id="allAssessmentsChart" style="max-width: 600px; max-height: 600px;"></canvas>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">All Assessments - Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Assessment Name</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th class="text-center">Score</th>
                        <th class="text-center">Max Score</th>
                        <th class="text-center">Percentage</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allAssessments as $assessment)
                    <tr>
                        <td><strong>{{ $assessment['name'] }}</strong></td>
                        <td>{{ $assessment['subject'] }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $assessment['type'])) }}</td>
                        <td class="text-center">{{ round($assessment['score'], 2) }}</td>
                        <td class="text-center">{{ round($assessment['max_score'], 2) }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = 'bg-danger';
                                if($assessment['percentage'] >= 90) $badgeClass = 'bg-success';
                                elseif($assessment['percentage'] >= 80) $badgeClass = 'bg-primary';
                                elseif($assessment['percentage'] >= 75) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $assessment['percentage'] }}%
                            </span>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $badgeClass }}"
                                    role="progressbar"
                                    style="width: {{ $assessment['percentage'] }}%;"
                                    aria-valuenow="{{ $assessment['percentage'] }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{ $assessment['percentage'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Performance by Assessment Type -->
@if($performanceByType->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Assessment Type - Bar Chart</h5>
    </div>
    <div class="card-body">
        <canvas id="assessmentTypeChart" style="max-height: 400px;"></canvas>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Assessment Type - Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Assessment Type</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Total Score</th>
                        <th class="text-center">Max Score</th>
                        <th class="text-center">Percentage</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performanceByType as $type)
                    <tr>
                        <td>
                            <strong>{{ ucwords(str_replace('_', ' ', $type['type'])) }}</strong>
                        </td>
                        <td class="text-center">{{ $type['count'] }}</td>
                        <td class="text-center">{{ round($type['score'], 2) }}</td>
                        <td class="text-center">{{ round($type['max_score'], 2) }}</td>
                        <td class="text-center">
                            @php
                                $typeBadgeClass = 'bg-danger';
                                if($type['percentage'] >= 90) $typeBadgeClass = 'bg-success';
                                elseif($type['percentage'] >= 80) $typeBadgeClass = 'bg-primary';
                                elseif($type['percentage'] >= 75) $typeBadgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $typeBadgeClass }}">
                                {{ $type['percentage'] }}%
                            </span>
                        </td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $typeBadgeClass }}" 
                                role="progressbar" 
                                style="width: {{ $type['percentage'] }}%;" 
                                aria-valuenow="{{ $type['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                    {{ $type['percentage'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Performance by Subject -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Subject - Bar Chart</h5>
    </div>
    <div class="card-body">
        @if($performanceBySubject->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No performance data available for the selected academic year and term.
            </div>
        @else
            <canvas id="subjectPerformanceChart" style="max-height: 400px;"></canvas>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Subject - Details</h5>
    </div>
    <div class="card-body">
        @if($performanceBySubject->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No performance data available for the selected academic year and term.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Completed</th>
                            <th class="text-center">Total Score</th>
                            <th class="text-center">Max Score</th>
                            <th class="text-center">Percentage</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performanceBySubject as $subject)
                        <tr>
                            <td>
                                <strong>{{ $subject['subject_name'] }}</strong>
                                <br>
                                <small class="text-muted">{{ $subject['subject_code'] }}</small>
                            </td>
                            <td class="text-center">{{ $subject['total_assessments'] }}</td>
                            <td class="text-center">{{ $subject['completed_assessments'] }}</td>
                            <td class="text-center">{{ round($subject['total_score'], 2) }}</td>
                            <td class="text-center">{{ round($subject['total_max_score'], 2) }}</td>
                            <td class="text-center">
                                @php
                                    $subjectBadgeClass = 'bg-danger';
                                    if($subject['percentage'] >= 90) $subjectBadgeClass = 'bg-success';
                                    elseif($subject['percentage'] >= 80) $subjectBadgeClass = 'bg-primary';
                                    elseif($subject['percentage'] >= 75) $subjectBadgeClass = 'bg-warning';
                                @endphp
                                <span class="badge {{ $subjectBadgeClass }}">
                                    {{ $subject['percentage'] }}%
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar {{ $subjectBadgeClass }}" 
                                    role="progressbar" 
                                    style="width: {{ $subject['percentage'] }}%;" 
                                    aria-valuenow="{{ $subject['percentage'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                        {{ $subject['percentage'] }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Detailed breakdown by type -->
                        @if(!empty($subject['by_type']))
                        <tr class="table-light">
                            <td colspan="7">
                                <div class="ms-4">
                                    <small class="text-muted d-block mb-2">Assessment Breakdown:</small>
                                    <div class="row g-2">
                                        @foreach($subject['by_type'] as $typeName => $typeData)
                                        @php
                                            $typePercentage = $typeData['max_score'] > 0 
                                                ? round(($typeData['score'] / $typeData['max_score']) * 100, 2) 
                                                : 0;
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">
                                                    {{ ucwords(str_replace('_', ' ', $typeName)) }}
                                                </span>
                                                @php
                                                    $typeBreakdownBadgeClass = 'bg-danger';
                                                    if($typePercentage >= 90) $typeBreakdownBadgeClass = 'bg-success';
                                                    elseif($typePercentage >= 80) $typeBreakdownBadgeClass = 'bg-primary';
                                                    elseif($typePercentage >= 75) $typeBreakdownBadgeClass = 'bg-warning';
                                                @endphp
                                                <span class="badge {{ $typeBreakdownBadgeClass }}">
                                                    {{ $typePercentage }}%
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const yearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term');
    const subjectSelect = document.getElementById('subject_id');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            // Reset subject when year changes
            if (subjectSelect) {
                subjectSelect.value = '';
            }
            this.form.submit();
        });
    }
    
    if (termSelect) {
        termSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }

    // All Assessments Doughnut Chart
    @if($allAssessments->isNotEmpty())
    const allAssessmentsCtx = document.getElementById('allAssessmentsChart');
    if (allAssessmentsCtx) {
        // Generate a diverse color palette for each assessment
        const generateColors = (count) => {
            const colors = [];
            const hueStep = 360 / count;
            for (let i = 0; i < count; i++) {
                const hue = (i * hueStep) % 360;
                const saturation = 65 + (i % 3) * 10; // Vary saturation: 65%, 75%, 85%
                const lightness = 50 + (i % 2) * 10;  // Vary lightness: 50%, 60%
                colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, 0.8)`);
            }
            return colors;
        };

        const generateBorderColors = (count) => {
            const colors = [];
            const hueStep = 360 / count;
            for (let i = 0; i < count; i++) {
                const hue = (i * hueStep) % 360;
                const saturation = 65 + (i % 3) * 10;
                const lightness = 40 + (i % 2) * 10; // Darker for border
                colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, 1)`);
            }
            return colors;
        };

        const assessmentCount = {{ $allAssessments->count() }};
        const backgroundColors = generateColors(assessmentCount);
        const borderColors = generateBorderColors(assessmentCount);

        const allAssessmentsData = {
            labels: [
                @foreach($allAssessments as $index => $assessment)
                    '{{ $assessment['name'] }} ({{ $assessment['subject'] }})',
                @endforeach
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    @foreach($allAssessments as $assessment)
                        {{ $assessment['percentage'] }},
                    @endforeach
                ],
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        };

        new Chart(allAssessmentsCtx, {
            type: 'doughnut',
            data: allAssessmentsData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            },
                            boxWidth: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    // Shorten label for legend
                                    const shortLabel = label.length > 25 ? label.substring(0, 25) + '...' : label;
                                    return {
                                        text: shortLabel + ': ' + value.toFixed(2) + '%',
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].borderColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'All Assessments Performance',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const assessments = [
                                    @foreach($allAssessments as $assessment)
                                        {
                                            name: '{{ $assessment['name'] }}',
                                            subject: '{{ $assessment['subject'] }}',
                                            type: '{{ ucwords(str_replace('_', ' ', $assessment['type'])) }}',
                                            score: {{ round($assessment['score'], 2) }},
                                            maxScore: {{ round($assessment['max_score'], 2) }},
                                            percentage: {{ $assessment['percentage'] }}
                                        },
                                    @endforeach
                                ];
                                const assessment = assessments[index];
                                return [
                                    assessment.name,
                                    'Subject: ' + assessment.subject,
                                    'Type: ' + assessment.type,
                                    'Percentage: ' + assessment.percentage.toFixed(2) + '%',
                                    'Score: ' + assessment.score + ' / ' + assessment.maxScore
                                ];
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Assessment Type Doughnut Chart
    @if($performanceByType->isNotEmpty())
    const assessmentTypeCtx = document.getElementById('assessmentTypeChart');
    if (assessmentTypeCtx) {
        const assessmentTypeData = {
            labels: [
                @foreach($performanceByType as $type)
                    '{{ ucwords(str_replace('_', ' ', $type['type'])) }}',
                @endforeach
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    @foreach($performanceByType as $type)
                        {{ $type['percentage'] }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($performanceByType as $type)
                        @if($type['percentage'] >= 90)
                            'rgba(40, 167, 69, 0.8)',
                        @elseif($type['percentage'] >= 80)
                            'rgba(0, 123, 255, 0.8)',
                        @elseif($type['percentage'] >= 75)
                            'rgba(255, 193, 7, 0.8)',
                        @else
                            'rgba(220, 53, 69, 0.8)',
                        @endif
                    @endforeach
                ],
                borderColor: [
                    @foreach($performanceByType as $type)
                        @if($type['percentage'] >= 90)
                            'rgba(40, 167, 69, 1)',
                        @elseif($type['percentage'] >= 80)
                            'rgba(0, 123, 255, 1)',
                        @elseif($type['percentage'] >= 75)
                            'rgba(255, 193, 7, 1)',
                        @else
                            'rgba(220, 53, 69, 1)',
                        @endif
                    @endforeach
                ],
                borderWidth: 2
            }]
        };

        new Chart(assessmentTypeCtx, {
            type: 'bar',
            data: assessmentTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Performance by Assessment Type',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const scores = [
                                    @foreach($performanceByType as $type)
                                        {{ round($type['score'], 2) }},
                                    @endforeach
                                ];
                                const maxScores = [
                                    @foreach($performanceByType as $type)
                                        {{ round($type['max_score'], 2) }},
                                    @endforeach
                                ];
                                const counts = [
                                    @foreach($performanceByType as $type)
                                        {{ $type['count'] }},
                                    @endforeach
                                ];
                                return [
                                    'Percentage: ' + context.parsed.y.toFixed(2) + '%',
                                    'Score: ' + scores[index] + ' / ' + maxScores[index],
                                    'Assessments: ' + counts[index]
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Assessment Type'
                        }
                    }
                }
            }
        });
    }
    @endif

    // Subject Performance Doughnut Chart
    @if($performanceBySubject->isNotEmpty())
    const subjectPerformanceCtx = document.getElementById('subjectPerformanceChart');
    if (subjectPerformanceCtx) {
        const subjectPerformanceData = {
            labels: [
                @foreach($performanceBySubject as $subject)
                    '{{ $subject['subject_code'] }}',
                @endforeach
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    @foreach($performanceBySubject as $subject)
                        {{ $subject['percentage'] }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($performanceBySubject as $subject)
                        @if($subject['percentage'] >= 90)
                            'rgba(40, 167, 69, 0.8)',
                        @elseif($subject['percentage'] >= 80)
                            'rgba(0, 123, 255, 0.8)',
                        @elseif($subject['percentage'] >= 75)
                            'rgba(255, 193, 7, 0.8)',
                        @else
                            'rgba(220, 53, 69, 0.8)',
                        @endif
                    @endforeach
                ],
                borderColor: [
                    @foreach($performanceBySubject as $subject)
                        @if($subject['percentage'] >= 90)
                            'rgba(40, 167, 69, 1)',
                        @elseif($subject['percentage'] >= 80)
                            'rgba(0, 123, 255, 1)',
                        @elseif($subject['percentage'] >= 75)
                            'rgba(255, 193, 7, 1)',
                        @else
                            'rgba(220, 53, 69, 1)',
                        @endif
                    @endforeach
                ],
                borderWidth: 2
            }]
        };

        new Chart(subjectPerformanceCtx, {
            type: 'bar',
            data: subjectPerformanceData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Performance by Subject',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const subjectNames = [
                                    @foreach($performanceBySubject as $subject)
                                        '{{ $subject['subject_name'] }}',
                                    @endforeach
                                ];
                                const scores = [
                                    @foreach($performanceBySubject as $subject)
                                        {{ round($subject['total_score'], 2) }},
                                    @endforeach
                                ];
                                const maxScores = [
                                    @foreach($performanceBySubject as $subject)
                                        {{ round($subject['total_max_score'], 2) }},
                                    @endforeach
                                ];
                                const assessments = [
                                    @foreach($performanceBySubject as $subject)
                                        {{ $subject['total_assessments'] }},
                                    @endforeach
                                ];
                                return [
                                    subjectNames[index],
                                    'Percentage: ' + context.parsed.y.toFixed(2) + '%',
                                    'Score: ' + scores[index] + ' / ' + maxScores[index],
                                    'Assessments: ' + assessments[index]
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Subject Code'
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>

<style>
.avatar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

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

.table-light td {
    background-color: #f8f9fa !important;
}
</style>
@endsection
