@extends('student.components.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Student Scores</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Scores</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.scores.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} - {{ ucfirst($year->semester) }} Semester
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="term" class="form-label">Term</label>
                            <select name="term" id="term" class="form-select" onchange="this.form.submit()">
                                <option value="midterm" {{ $selectedTerm === 'midterm' ? 'selected' : '' }}>Midterm (1st Quarter)</option>
                                <option value="finals" {{ $selectedTerm === 'finals' ? 'selected' : '' }}>Finals (2nd Quarter)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="subject_id" class="form-label">Subject (Optional)</label>
                            <select name="subject_id" id="subject_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Subjects</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject['id'] }}" {{ $selectedSubjectId == $subject['id'] ? 'selected' : '' }}>
                                        {{ $subject['code'] }} - {{ $subject['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            @if($selectedSubjectId)
                                <a href="{{ route('student.scores.index', ['academic_year_id' => $selectedYearId, 'term' => $selectedTerm]) }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            @if($selectedYearId)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="ti ti-clipboard-list text-primary mb-2" style="font-size: 2rem;"></i>
                                <h3 class="mb-0">{{ $summary['total_assessments'] }}</h3>
                                <p class="text-muted mb-0">Total Assessments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="ti ti-circle-check text-success mb-2" style="font-size: 2rem;"></i>
                                <h3 class="mb-0">{{ $summary['completed_assessments'] }}</h3>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="ti ti-trophy text-warning mb-2" style="font-size: 2rem;"></i>
                                <h3 class="mb-0">{{ number_format($summary['total_points_earned'], 1) }}</h3>
                                <p class="text-muted mb-0">Points Earned</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="ti ti-percentage text-info mb-2" style="font-size: 2rem;"></i>
                                <h3 class="mb-0">{{ $summary['average_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Overall Average</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scores by Subject -->
                @if($scoresBySubject->isEmpty())
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No assessment scores found for the selected academic year and term.
                    </div>
                @else
                    @foreach($scoresBySubject as $subjectData)
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-0">
                                            <i class="ti ti-book me-2"></i>
                                            {{ $subjectData['subject_code'] }} - {{ $subjectData['subject_name'] }}
                                        </h5>
                                        <small>Teacher: {{ $subjectData['teacher_name'] }}</small>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <span class="badge bg-light text-dark fs-6">
                                            Average: {{ $subjectData['average_percentage'] }}%
                                        </span>
                                        <span class="badge bg-light text-dark fs-6 ms-2">
                                            {{ $subjectData['completed_assessments'] }}/{{ $subjectData['total_assessments'] }} Completed
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Assessment Name</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th class="text-center">Score</th>
                                                <th class="text-center">Max Score</th>
                                                <th class="text-center">Percentage</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subjectData['assessments'] as $assessment)
                                                <tr>
                                                    <td><strong>{{ $assessment['name'] }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $assessment['type'] }}</span>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($assessment['date'])->format('M d, Y') }}</td>
                                                    <td class="text-center">
                                                        @if($assessment['score'] !== null)
                                                            <strong>{{ number_format($assessment['score'], 2) }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ number_format($assessment['max_score'], 2) }}</td>
                                                    <td class="text-center">
                                                        @if($assessment['percentage'] !== null)
                                                            <span class="badge 
                                                                @if($assessment['percentage'] >= 90) bg-success
                                                                @elseif($assessment['percentage'] >= 80) bg-primary
                                                                @elseif($assessment['percentage'] >= 75) bg-warning
                                                                @else bg-danger
                                                                @endif
                                                            ">
                                                                {{ $assessment['percentage'] }}%
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($assessment['status'] === 'completed')
                                                            <span class="badge bg-success">
                                                                <i class="ti ti-check"></i> Completed
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="ti ti-clock"></i> Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Subject Total:</th>
                                                <th class="text-center">{{ number_format($subjectData['total_score'], 2) }}</th>
                                                <th class="text-center">{{ number_format($subjectData['total_max_score'], 2) }}</th>
                                                <th class="text-center">
                                                    <span class="badge 
                                                        @if($subjectData['average_percentage'] >= 90) bg-success
                                                        @elseif($subjectData['average_percentage'] >= 80) bg-primary
                                                        @elseif($subjectData['average_percentage'] >= 75) bg-warning
                                                        @else bg-danger
                                                        @endif
                                                        fs-6
                                                    ">
                                                        {{ $subjectData['average_percentage'] }}%
                                                    </span>
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Print Button -->
                    <div class="text-center mt-4 mb-4">
                        <button onclick="window.print()" class="btn btn-info btn-lg">
                            <i class="ti ti-printer me-2"></i> Print Scores
                        </button>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Please select an academic year to view your scores.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .portal-mobile-header, .footer, .page-header, .card-body form, button, .breadcrumb {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }
    .card-header {
        background-color: #0d6efd !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endsection
