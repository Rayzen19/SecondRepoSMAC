@extends('guardian.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <h3 class="page-title">Dashboard</h3>
            <p class="text-muted">Welcome back, {{ Auth::guard('guardian')->user()->name }}!</p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Students</p>
                        <h3 class="mb-0">{{ $totalStudents }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-primary-light rounded-circle">
                        <i class="ti ti-users" style="font-size: 24px; color: #007bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Overall Average</p>
                        <h3 class="mb-0">
                            @if($overallAverageGrade > 0)
                                {{ $overallAverageGrade }}
                                @if($overallAverageGrade >= 90)
                                    <span class="badge bg-success ms-2">Excellent</span>
                                @elseif($overallAverageGrade >= 85)
                                    <span class="badge bg-info ms-2">Very Good</span>
                                @elseif($overallAverageGrade >= 75)
                                    <span class="badge bg-primary ms-2">Passing</span>
                                @else
                                    <span class="badge bg-danger ms-2">Needs Help</span>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </h3>
                    </div>
                    <div class="avatar avatar-md bg-success-light rounded-circle">
                        <i class="ti ti-chart-bar" style="font-size: 24px; color: #28a745;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Subjects</p>
                        <h3 class="mb-0">{{ $totalSubjects }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-warning-light rounded-circle">
                        <i class="ti ti-book" style="font-size: 24px; color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Unread Messages</p>
                        <h3 class="mb-0">{{ $recentMessages->where('read_at', null)->count() }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-info-light rounded-circle">
                        <i class="ti ti-mail" style="font-size: 24px; color: #17a2b8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students Performance -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-users me-2"></i>My Students Performance</h5>
                <a href="{{ route('guardian.grades.index') }}" class="btn btn-sm btn-info">
                    <i class="ti ti-eye me-1"></i>View All Grades
                </a>
            </div>
            <div class="card-body">
                @if($studentPerformance && count($studentPerformance) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student Number</th>
                                    <th>Section</th>
                                    <th>Subjects</th>
                                    <th>Average</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentPerformance as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-title rounded-circle bg-primary text-white">
                                                    {{ strtoupper(substr($student['name'], 0, 1)) }}
                                                </span>
                                            </div>
                                            <strong>{{ $student['name'] }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $student['student_number'] }}</td>
                                    <td>{{ $student['section'] ?? 'N/A' }}</td>
                                    <td>
                                        @if($student['subjects_count'] > 0)
                                            <span class="badge bg-light text-dark">{{ $student['subjects_count'] }} subjects</span>
                                            @if($student['passing_subjects'] > 0)
                                                <span class="badge bg-success">{{ $student['passing_subjects'] }} Passing</span>
                                            @endif
                                            @if($student['failing_subjects'] > 0)
                                                <span class="badge bg-danger">{{ $student['failing_subjects'] }} Failing</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No grades yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student['average'] > 0)
                                            <strong class="
                                                @if($student['average'] >= 90) text-success
                                                @elseif($student['average'] >= 85) text-info
                                                @elseif($student['average'] >= 75) text-info
                                                @else text-danger
                                                @endif
                                            ">{{ $student['average'] }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student['status'] == 'Passing')
                                            <span class="badge bg-success">
                                                <i class="ti ti-check me-1"></i>Passing
                                            </span>
                                        @elseif($student['status'] == 'Needs Attention')
                                            <span class="badge bg-danger">
                                                <i class="ti ti-alert-triangle me-1"></i>Needs Attention
                                            </span>
                                        @elseif($student['status'] == 'Enrolled')
                                            <span class="badge bg-info">
                                                <i class="ti ti-hourglass me-1"></i>Enrolled
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ti ti-x me-1"></i>No Enrollment
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('guardian.grades.index', ['student_id' => $student['id']]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-users" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-2">No students assigned to your account yet.</p>
                        <p class="text-muted">Please contact the school administrator.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Messages and Quick Actions -->
<div class="row">
    <!-- Recent Messages -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-mail me-2"></i>Recent Messages</h5>
                <a href="{{ route('guardian.messages.messenger') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-message me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                @if($recentMessages && $recentMessages->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentMessages as $message)
                        <div class="list-group-item px-0 {{ $message->recipients->first() && $message->recipients->first()->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-user-circle me-2" style="font-size: 20px;"></i>
                                        <strong>{{ $message->sender ? $message->sender->name : 'Unknown' }}</strong>
                                        @if($message->recipients->first() && !$message->recipients->first()->read_at)
                                            <span class="badge bg-primary ms-2">New</span>
                                        @endif
                                    </div>
                                    <p class="mb-1">
                                        @if($message->subject)
                                            <strong>{{ Str::limit($message->subject, 50) }}</strong><br>
                                        @endif
                                        {{ Str::limit($message->body, 100) }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ $message->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <a href="{{ route('guardian.messages.messenger') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-mail" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-2">No messages yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="ti ti-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('guardian.grades.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-report-analytics me-2"></i>View Grades
                    </a>
                    <a href="{{ route('guardian.enhancement.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-trending-up me-2"></i>Check Enhancement
                    </a>
                    <a href="{{ route('guardian.messages.messenger') }}" class="btn btn-outline-info">
                        <i class="ti ti-message me-2"></i>Send Message
                    </a>
                    <a href="{{ route('guardian.profile.show') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-user me-2"></i>My Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        @if($overallAverageGrade > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="ti ti-chart-pie me-2"></i>Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Overall Performance</span>
                        <strong>{{ $overallAverageGrade }}%</strong>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar 
                            @if($overallAverageGrade >= 90) bg-success
                            @elseif($overallAverageGrade >= 85) bg-info
                            @elseif($overallAverageGrade >= 75) bg-primary
                            @else bg-danger
                            @endif
                        " role="progressbar" style="width: {{ min($overallAverageGrade, 100) }}%;">
                            {{ $overallAverageGrade }}
                        </div>
                    </div>
                </div>
                
                @php
                    $totalPassing = 0;
                    $totalFailing = 0;
                    foreach($studentPerformance as $student) {
                        $totalPassing += $student['passing_subjects'];
                        $totalFailing += $student['failing_subjects'];
                    }
                @endphp
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-2 bg-success-light rounded">
                            <h4 class="mb-0 text-success">{{ $totalPassing }}</h4>
                            <small class="text-muted">Passing</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-danger-light rounded">
                            <h4 class="mb-0 text-danger">{{ $totalFailing }}</h4>
                            <small class="text-muted">Failing</small>
                        </div>
                    </div>
                </div>

                @if($totalFailing > 0)
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <small><strong>Attention:</strong> {{ $totalFailing }} subject(s) need improvement.</small>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.bg-primary-light {
    background-color: rgba(0, 123, 255, 0.1);
}

.bg-success-light {
    background-color: rgba(40, 167, 69, 0.1);
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-info-light {
    background-color: rgba(23, 162, 184, 0.1);
}

.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1);
}
</style>
@endsection
