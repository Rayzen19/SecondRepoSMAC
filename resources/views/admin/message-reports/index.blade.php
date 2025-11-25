@extends('admin.components.template')

@section('content')
<div class="page-header">
    <h3 class="page-title">Message Reports</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Message Reports</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Reports</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="icon-lg bg-primary text-white rounded">
                        <i class="ti ti-flag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Pending</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    </div>
                    <div class="icon-lg bg-warning text-white rounded">
                        <i class="ti ti-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Reviewed</h6>
                        <h3 class="mb-0 text-info">{{ $stats['reviewed'] }}</h3>
                    </div>
                    <div class="icon-lg bg-info text-white rounded">
                        <i class="ti ti-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Action Taken</h6>
                        <h3 class="mb-0 text-success">{{ $stats['action_taken'] }}</h3>
                    </div>
                    <div class="icon-lg bg-success text-white rounded">
                        <i class="ti ti-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('admin.message-reports.index', ['status' => 'all']) }}">
                    All ({{ $stats['total'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('admin.message-reports.index', ['status' => 'pending']) }}">
                    Pending ({{ $stats['pending'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'reviewed' ? 'active' : '' }}" href="{{ route('admin.message-reports.index', ['status' => 'reviewed']) }}">
                    Reviewed ({{ $stats['reviewed'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'dismissed' ? 'active' : '' }}" href="{{ route('admin.message-reports.index', ['status' => 'dismissed']) }}">
                    Dismissed ({{ $stats['dismissed'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'action_taken' ? 'active' : '' }}" href="{{ route('admin.message-reports.index', ['status' => 'action_taken']) }}">
                    Action Taken ({{ $stats['action_taken'] }})
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        @if($reports->isEmpty())
            <div class="text-center py-5">
                <i class="ti ti-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="text-muted mt-3">No reports found</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Reported By</th>
                            <th>Message From</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Reported At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td>
                                <div>
                                    <strong>{{ $report->reporter->name }}</strong>
                                    <br><small class="text-muted">{{ $report->reporter->email }}</small>
                                </div>
                            </td>
                            <td>
                                @if($report->message && $report->message->sender)
                                    <div>
                                        <strong>{{ $report->message->sender->name }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($report->message->body, 50) }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Message deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $report->reason == 'spam' ? 'info' : 
                                    ($report->reason == 'harassment' ? 'danger' : 
                                    ($report->reason == 'inappropriate' ? 'warning' : 'secondary'))
                                }}">
                                    {{ ucfirst($report->reason) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $report->status == 'pending' ? 'warning' : 
                                    ($report->status == 'reviewed' ? 'info' : 
                                    ($report->status == 'action_taken' ? 'success' : 'secondary'))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                </span>
                            </td>
                            <td>{{ $report->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.message-reports.show', $report) }}" class="btn btn-sm bg-info">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                    @if($report->isPending())
                                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $report->id }}, 'reviewed')">
                                            <i class="ti ti-check"></i> Review
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="dismissReport({{ $report->id }})">
                                            <i class="ti ti-x"></i> Dismiss
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateStatus(reportId, status) {
    if (!confirm('Are you sure you want to mark this report as ' + status + '?')) {
        return;
    }

    fetch(`/admin/message-reports/${reportId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to update report status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function dismissReport(reportId) {
    const reason = prompt('Enter reason for dismissal (optional):');
    
    fetch(`/admin/message-reports/${reportId}/dismiss`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ admin_notes: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to dismiss report');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endpush
