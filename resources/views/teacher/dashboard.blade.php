@extends('teacher.components.template')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <h3 class="page-title">Dashboard</h3>
            <p class="text-muted">Welcome back, {{ session('teacher.name', Auth::guard('teacher')->user()->name ?? 'Teacher') }}!</p>
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
                        <p class="text-muted mb-1">Total Sections</p>
                        <h3 class="mb-0">{{ $stats['total_sections'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-primary-light rounded-circle">
                        <i class="ti ti-school" style="font-size: 24px; color: #007bff;"></i>
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
                        <p class="text-muted mb-1">Subjects Teaching</p>
                        <h3 class="mb-0">{{ $stats['total_subjects'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-success-light rounded-circle">
                        <i class="ti ti-book" style="font-size: 24px; color: #28a745;"></i>
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
                        <p class="text-muted mb-1">Total Students</p>
                        <h3 class="mb-0">{{ $stats['total_students'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-warning-light rounded-circle">
                        <i class="ti ti-users" style="font-size: 24px; color: #ffc107;"></i>
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
                        <h3 class="mb-0">{{ $unreadMessagesCount ?? 0 }}</h3>
                    </div>
                    <div class="avatar avatar-md bg-info-light rounded-circle">
                        <i class="ti ti-mail" style="font-size: 24px; color: #17a2b8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- My Sections and Quick Actions -->
<div class="row mb-4">
    <!-- Sections Handled -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-school me-2"></i>My Sections</h5>
                <span class="badge bg-primary">{{ $sectionsHandled->count() }} Sections</span>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                @forelse($sectionsHandled as $section)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">
                                    {{ $section['strand_code'] }} - {{ $section['section_name'] }}
                                    @if($section['is_adviser'])
                                        <span class="badge bg-success ms-2">
                                            <i class="ti ti-star"></i> Adviser
                                        </span>
                                    @endif
                                </h5>
                                <p class="text-muted mb-2">
                                    <i class="ti ti-school me-1"></i>Grade {{ $section['grade_level'] }}
                                </p>
                                <div class="mb-0">
                                    <strong class="text-primary">Subjects ({{ $section['subject_count'] }}):</strong>
                                    <div class="mt-2">
                                        @foreach($section['subjects'] as $subject)
                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                <i class="ti ti-book me-1"></i>{{ $subject }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('teacher.students.all-sections') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="ti ti-folder-off" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-3">No sections assigned yet</p>
                </div>
                @endforelse
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
                    <a href="{{ route('teacher.class-records.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-report-analytics me-2"></i>Class Records
                    </a>
                    <a href="{{ route('teacher.students.all-sections') }}" class="btn btn-outline-success">
                        <i class="ti ti-users me-2"></i>View Students
                    </a>
                    <a href="{{ route('teacher.messages.messenger') }}" class="btn btn-outline-info">
                        <i class="ti ti-message me-2"></i>Messages
                    </a>
                    <a href="{{ route('teacher.profile.show') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-user me-2"></i>My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements and Messages -->
<div class="row">
    <!-- Recent Announcements -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="ti ti-speakerphone me-2"></i>Recent Announcements</h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @forelse($recentAnnouncements ?? [] as $announcement)
                <div class="card mb-2 shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-1">{{ $announcement->title }}</h6>
                        <p class="mb-1 small text-muted">{{ Str::limit($announcement->content, 100) }}</p>
                        <small class="text-muted">
                            <i class="ti ti-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="ti ti-bell-off" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No announcements</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-mail me-2"></i>Recent Messages</h5>
                @if($unreadMessagesCount > 0)
                <span class="badge bg-danger">{{ $unreadMessagesCount }} New</span>
                @endif
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @forelse($recentMessages ?? [] as $messageRecipient)
                <div class="card mb-2 shadow-sm {{ $messageRecipient->read_at ? '' : 'bg-light' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti ti-user-circle me-2" style="font-size: 20px;"></i>
                                    <strong>{{ $messageRecipient->message->sender->name ?? 'Unknown' }}</strong>
                                    @if(!$messageRecipient->read_at)
                                        <span class="badge bg-primary ms-2">New</span>
                                    @endif
                                </div>
                                <p class="mb-1 small">{{ Str::limit($messageRecipient->message->body ?? 'No content', 100) }}</p>
                                <small class="text-muted">
                                    <i class="ti ti-clock me-1"></i>{{ $messageRecipient->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <a href="{{ route('teacher.messages.messenger') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="ti ti-mail" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No messages yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($stats['advised_sections'] > 0)
<!-- Adviser Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="ti ti-shield-check me-2"></i>Class Adviser Responsibilities
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="avatar avatar-xl bg-success-light rounded-circle mx-auto mb-3">
                                <i class="ti ti-users" style="font-size: 48px; color: #28a745;"></i>
                            </div>
                            <h6 class="mt-2">Monitor Students</h6>
                            <p class="text-muted small mb-0">Track attendance and performance</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="avatar avatar-xl bg-info-light rounded-circle mx-auto mb-3">
                                <i class="ti ti-report" style="font-size: 48px; color: #17a2b8;"></i>
                            </div>
                            <h6 class="mt-2">Generate Reports</h6>
                            <p class="text-muted small mb-0">Create class reports and summaries</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="avatar avatar-xl bg-primary-light rounded-circle mx-auto mb-3">
                                <i class="ti ti-message-circle" style="font-size: 48px; color: #007bff;"></i>
                            </div>
                            <h6 class="mt-2">Communicate</h6>
                            <p class="text-muted small mb-0">Connect with parents and students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.avatar-xl {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
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