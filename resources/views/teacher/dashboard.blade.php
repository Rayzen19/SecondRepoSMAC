@extends('teacher.components.template')

@section('content')
<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        background: rgba(255,255,255,0.2);
    }
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .quick-action-btn {
        border-radius: 10px;
        padding: 15px;
        transition: all 0.3s ease;
        border: 2px solid #f0f0f0;
        text-decoration: none;
        display: block;
    }
    .quick-action-btn:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }
    .section-card {
        border-radius: 10px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .section-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    .announcement-item {
        border-left: 3px solid #38ef7d;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    .announcement-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .message-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .message-item:hover {
        background: #f8f9fa;
    }
    .badge-custom {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 12px;
    }
    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="ti ti-layout-dashboard"></i>
        </span>
        Dashboard
    </h3>
</div>

<!-- Welcome Banner -->
<div class="welcome-banner mb-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">Welcome back, {{ session('teacher.name', Auth::guard('teacher')->user()->name ?? 'Teacher') }}! 👋</h2>
            <p class="mb-0 opacity-75">
                <i class="ti ti-calendar me-2"></i>{{ now()->format('l, F j, Y') }}
            </p>
            <p class="mb-0 opacity-75">
                <i class="ti ti-clock me-2"></i>{{ now()->format('h:i A') }}
            </p>
        </div>
        <div class="col-md-4 text-end d-none d-md-block">
            <i class="ti ti-school" style="font-size: 80px; opacity: 0.2;"></i>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card gradient-primary text-white shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Sections</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['total_sections'] ?? 0 }}</h2>
                        <small class="text-white-50">Sections handled</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ti ti-school text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card gradient-success text-white shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Subjects Teaching</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['total_subjects'] ?? 0 }}</h2>
                        <small class="text-white-50">Active subjects</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ti ti-book text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card gradient-warning text-white shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Students</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['total_students'] ?? 0 }}</h2>
                        <small class="text-white-50">Students enrolled</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ti ti-users text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card gradient-info text-white shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Messages</h6>
                        <h2 class="mb-0 fw-bold">{{ $unreadMessagesCount ?? 0 }}</h2>
                        <small class="text-white-50">Unread messages</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ti ti-mail text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="ti ti-bolt me-2 text-primary"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('teacher.class-records.index') }}" class="quick-action-btn text-center">
                            <i class="ti ti-report-analytics text-primary" style="font-size: 32px;"></i>
                            <h6 class="mt-2 mb-0">Class Records</h6>
                            <small class="text-muted">View & manage records</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('teacher.students.all-sections') }}" class="quick-action-btn text-center">
                            <i class="ti ti-users text-success" style="font-size: 32px;"></i>
                            <h6 class="mt-2 mb-0">Students</h6>
                            <small class="text-muted">View students</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('teacher.messages.messenger') }}" class="quick-action-btn text-center">
                            <i class="ti ti-messages text-info" style="font-size: 32px;"></i>
                            <h6 class="mt-2 mb-0">Messages</h6>
                            <small class="text-muted">Send & receive</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('teacher.profile.show') }}" class="quick-action-btn text-center">
                            <i class="ti ti-user-circle text-warning" style="font-size: 32px;"></i>
                            <h6 class="mt-2 mb-0">My Profile</h6>
                            <small class="text-muted">View profile</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sections Handled -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-school me-2 text-primary"></i>My Sections
                </h5>
                <span class="badge bg-primary">{{ $sectionsHandled->count() }} Sections</span>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                @forelse($sectionsHandled as $section)
                <div class="section-card card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-2">
                                    {{ $section['strand_code'] }} - {{ $section['section_name'] }}
                                    @if($section['is_adviser'])
                                        <span class="badge badge-custom bg-success ms-2">
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
                    <i class="ti ti-folder-off text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">No sections assigned yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Recent Announcements -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="ti ti-speakerphone me-2 text-success"></i>Announcements
                </h5>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @forelse($recentAnnouncements ?? [] as $announcement)
                <div class="announcement-item">
                    <h6 class="mb-1">{{ $announcement->title }}</h6>
                    <p class="mb-1 small text-muted">{{ Str::limit($announcement->content, 100) }}</p>
                    <small class="text-muted">
                        <i class="ti ti-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                    </small>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="ti ti-bell-off text-muted" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0">No announcements</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-mail me-2 text-info"></i>Recent Messages
                </h5>
                @if($unreadMessagesCount > 0)
                <span class="badge bg-danger">{{ $unreadMessagesCount }}</span>
                @endif
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                @forelse($recentMessages ?? [] as $messageRecipient)
                <div class="message-item" onclick="window.location.href='{{ route('teacher.messages.messenger') }}'">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                {{ $messageRecipient->message->sender->name ?? 'Unknown' }}
                                @if(!$messageRecipient->read_at)
                                    <span class="badge bg-danger badge-sm">New</span>
                                @endif
                            </h6>
                            <p class="mb-1 small">{{ Str::limit($messageRecipient->message->body ?? 'No content', 60) }}</p>
                            <small class="text-muted">
                                <i class="ti ti-clock me-1"></i>{{ $messageRecipient->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="ti ti-inbox text-muted" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0">No messages</p>
                </div>
                @endforelse
            </div>
            @if(count($recentMessages ?? []) > 0)
            <div class="card-footer bg-white text-center">
                <a href="{{ route('teacher.messages.messenger') }}" class="text-primary text-decoration-none">
                    View all messages <i class="ti ti-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@if($stats['advised_sections'] > 0)
<!-- Adviser Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="ti ti-shield-check me-2"></i>Class Adviser Responsibilities
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="ti ti-users text-success" style="font-size: 48px;"></i>
                            <h6 class="mt-2">Monitor Students</h6>
                            <p class="text-muted small mb-0">Track attendance and performance</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="ti ti-report text-success" style="font-size: 48px;"></i>
                            <h6 class="mt-2">Generate Reports</h6>
                            <p class="text-muted small mb-0">Create class reports and summaries</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="ti ti-message-circle text-success" style="font-size: 48px;"></i>
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

@endsection