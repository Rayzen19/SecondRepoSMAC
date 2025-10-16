@extends('student.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Grades</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Grades</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('student.grades.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->semester }} Semester)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" onchange="this.form.submit()">
                            <option value="1st" {{ $selectedSemester == '1st' ? 'selected' : '' }}>First Semester</option>
                            <option value="2nd" {{ $selectedSemester == '2nd' ? 'selected' : '' }}>Second Semester</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Grades Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #ddddf6ff;">
                <h5 class="mb-0">Grades</h5>
            </div>
            <div class="card-body">
                @if($grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $grade)
                                    <tr>
                                        <td>{{ $grade['subject_code'] }}</td>
                                        <td>{{ $grade['subject_name'] }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $grade['grade'] >= 90 ? 'bg-success' : ($grade['grade'] >= 80 ? 'bg-primary' : 'bg-warning') }} fs-6">
                                                {{ $grade['grade'] }}
                                            </span>
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
                    <div class="alert alert-info">
                        No grades available for this semester.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Performance Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #ddddf6ff;">
                <h5 class="mb-0">Performance</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Performance Analytics Chart -->
                        <div style="height: 400px;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <!-- Performance Statistics Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Assessment Type</th>
                                        <th class="text-center">Score (%)</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach([
                                        ['label' => 'Activities', 'value' => $performanceData['activities'], 'color' => '#ffc107'],
                                        ['label' => 'Quizzes', 'value' => $performanceData['quizzes'], 'color' => '#0d6efd'],
                                        ['label' => 'Assignment', 'value' => $performanceData['assignment'], 'color' => '#0dcaf0'],
                                        ['label' => 'Major Quiz', 'value' => $performanceData['major_quiz'], 'color' => '#6c757d'],
                                        ['label' => 'Exam', 'value' => $performanceData['exam'], 'color' => '#198754'],
                                        ['label' => 'Recitation', 'value' => $performanceData['recitation'], 'color' => '#dc3545'],
                                        ['label' => 'Project', 'value' => $performanceData['project'], 'color' => '#20c997'],
                                    ] as $item)
                                        <tr>
                                            <td>
                                                <span class="badge me-2" style="background-color: {{ $item['color'] }}; width: 12px; height: 12px;"></span>
                                                <strong>{{ $item['label'] }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $item['value'] }}%</span>
                                            </td>
                                            <td class="text-center">
                                                @if($item['value'] >= 80)
                                                    <span class="badge bg-success">Excellent</span>
                                                @elseif($item['value'] >= 60)
                                                    <span class="badge bg-warning">Good</span>
                                                @else
                                                    <span class="badge bg-danger">Needs Improvement</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="ti ti-file-export me-1"></i> Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decision Support System Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-white" style="background-color: #ddddf6ff;">
                <h5 class="mb-0"><i class="ti ti-bulb me-2"></i>Decision Support System</h5>
            </div>
            <div class="card-body">
                <!-- Overall Performance Summary -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white" style="background-color: #FF6B35;">
                            <div class="card-body text-center">
                                <h2 class="display-4 fw-bold">{{ $overallAverage }}</h2>
                                <p class="mb-0">Overall Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white" style="background-color: #00A676;">
                            <div class="card-body text-center">
                                <h2 class="display-4 fw-bold">{{ $strengths->count() }}</h2>
                                <p class="mb-0">Strong Subjects</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white" style="background-color: #F7B32B;">
                            <div class="card-body text-center">
                                <h2 class="display-4 fw-bold">{{ $weaknesses->count() }}</h2>
                                <p class="mb-0">Subjects Needing Improvement</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Strengths & Weaknesses -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header text-white" style="background-color: #ddddf6ff;">
                                <h6 class="mb-0"><i class="ti ti-trophy me-2"></i>Academic Strengths</h6>
                            </div>
                            <div class="card-body">
                                @if($strengths->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($strengths as $strength)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ $strength['subject'] }}</span>
                                                <span class="badge rounded-pill text-white" style="background-color: #00A676;">{{ $strength['grade'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert mb-0" style="background-color: #E8F5F1; color: #003510; border-color: #ddddf6ff;">
                                        <i class="ti ti-info-circle me-2"></i>Complete more subjects to identify your strengths.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header text-white" style="background-color: #ddddf6ff;">
                                <h6 class="mb-0"><i class="ti ti-target me-2"></i>Areas for Improvement</h6>
                            </div>
                            <div class="card-body">
                                @if($weaknesses->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($weaknesses as $weakness)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ $weakness['subject'] }}</span>
                                                <span class="badge rounded-pill text-white" style="background-color: #F7B32B;">{{ $weakness['grade'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert mb-0" style="background-color: #E8F5F1; color: #003510; border-color: #00A676;">
                                        <i class="ti ti-check-circle me-2"></i>Great job! No subjects currently need improvement.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-white" style="background-color: #ddddf6ff;">
                                <h6 class="mb-0"><i class="ti ti-bulb me-2"></i>Personalized Recommendations</h6>
                            </div>
                            <div class="card-body">
                                @foreach($recommendations as $index => $recommendation)
                                    <div class="alert d-flex align-items-start mb-2" style="background-color: #E8F5F1; color: #003510; border-left: 4px solid #003510;">
                                        <div class="me-3">
                                            <span class="badge rounded-circle text-white" style="background-color: #003510; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0">{{ $recommendation }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Study Tips -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-white" style="background-color: #ddddf6ff;">
                                <h6 class="mb-0"><i class="ti ti-book me-2"></i>Study Tips & Resources</h6>
                            </div>
                            <div class="card-body" style="background-color: #F5FBF8;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3" style="color: #003510;"><i class="ti ti-clock me-2"></i>Time Management</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Create a weekly study schedule</li>
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Use the Pomodoro Technique (25 min study, 5 min break)</li>
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Prioritize difficult subjects during peak focus hours</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3" style="color: #003510;"><i class="ti ti-bulb me-2"></i>Effective Study Strategies</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Take organized, detailed notes during class</li>
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Practice active recall and self-testing</li>
                                            <li class="mb-2"><i class="ti ti-circle-check me-2" style="color: #00A676;"></i>Form study groups with classmates</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .performance-bars .bg-dark {
        background-color: #2c3e50 !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('performanceChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Activities', 'Quizzes', 'Assignment', 'Major Quiz', 'Exam', 'Recitation', 'Project'],
                    datasets: [{
                        label: 'Performance Score (%)',
                        data: [
                            {{ $performanceData['activities'] }},
                            {{ $performanceData['quizzes'] }},
                            {{ $performanceData['assignment'] }},
                            {{ $performanceData['major_quiz'] }},
                            {{ $performanceData['exam'] }},
                            {{ $performanceData['recitation'] }},
                            {{ $performanceData['project'] }}
                        ],
                        backgroundColor: [
                            '#ffc107', // Activities
                            '#0d6efd', // Quizzes
                            '#0dcaf0', // Assignment
                            '#6c757d', // Major Quiz
                            '#198754', // Exam
                            '#dc3545', // Recitation
                            '#20c997'  // Project
                        ],
                        borderColor: [
                            '#ffc107',
                            '#0d6efd',
                            '#0dcaf0',
                            '#6c757d',
                            '#198754',
                            '#dc3545',
                            '#20c997'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Performance Analysis by Assessment Type',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.parsed.x + '%';
                                    let status = '';
                                    if (context.parsed.x >= 80) {
                                        status = ' - Excellent';
                                    } else if (context.parsed.x >= 60) {
                                        status = ' - Good';
                                    } else {
                                        status = ' - Needs Improvement';
                                    }
                                    return 'Score: ' + label + status;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                font: {
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'Performance Score (%)',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    });
</script>
@endsection
