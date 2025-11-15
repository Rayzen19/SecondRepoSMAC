@extends('admin.components.template')

@section('title', 'View Report Details')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.message-reports.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Report Details Card -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Report Details #{{ $report->id }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Report Information -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Report Information</h5>
                    
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ 
                            $report->status == 'pending' ? 'warning' : 
                            ($report->status == 'reviewed' ? 'info' : 
                            ($report->status == 'action_taken' ? 'success' : 'secondary'))
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Reason:</strong>
                        <span class="badge bg-{{ 
                            $report->reason == 'spam' ? 'info' : 
                            ($report->reason == 'harassment' ? 'danger' : 
                            ($report->reason == 'inappropriate' ? 'warning' : 'secondary'))
                        }}">
                            {{ ucfirst($report->reason) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Reported By:</strong>
                        <div class="mt-1">
                            {{ $report->reporter->name }}<br>
                            <small class="text-muted">{{ $report->reporter->email }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Reported At:</strong>
                        <div>{{ $report->created_at->format('F d, Y h:i A') }}</div>
                    </div>

                    <div class="mb-3">
                        <strong>Details:</strong>
                        <div class="mt-1 p-2 bg-light rounded">
                            {{ $report->details ?: 'No additional details provided' }}
                        </div>
                    </div>

                    @if($report->screenshot_path)
                    <div class="mb-3">
                        <strong>Screenshot Evidence:</strong>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $report->screenshot_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $report->screenshot_path) }}" 
                                     class="img-fluid rounded border" 
                                     style="max-width: 100%; max-height: 300px; cursor: pointer;"
                                     alt="Report Screenshot"
                                     onclick="openImageModal(this.src)">
                            </a>
                            <br>
                            <small class="text-muted">Click to view full size</small>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Reported Message -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Reported Message</h5>
                    
                    @if($report->message)
                        <div class="mb-3">
                            <strong>From:</strong>
                            <div class="mt-1">
                                {{ $report->message->sender->name ?? 'Unknown' }}<br>
                                <small class="text-muted">{{ $report->message->sender->email ?? '' }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Sent At:</strong>
                            <div>{{ $report->message->created_at->format('F d, Y h:i A') }}</div>
                        </div>

                        <div class="mb-3">
                            <strong>Message Content:</strong>
                            <div class="mt-1 p-3 bg-light rounded border">
                                {{ $report->message->body }}
                            </div>
                        </div>

                        @if($report->message->attachment_path)
                        <div class="mb-3">
                            <strong>Message Attachment:</strong>
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $report->message->attachment_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-download"></i> Download Attachment
                                </a>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-circle"></i> The reported message has been deleted.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Review Section -->
            @if($report->reviewed_by)
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 mb-3">Admin Review</h5>
                    
                    <div class="mb-3">
                        <strong>Reviewed By:</strong>
                        <div class="mt-1">
                            {{ $report->reviewer->name ?? 'Unknown' }}<br>
                            <small class="text-muted">{{ $report->reviewer->email ?? '' }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Reviewed At:</strong>
                        <div>{{ $report->reviewed_at ? $report->reviewed_at->format('F d, Y h:i A') : 'N/A' }}</div>
                    </div>

                    <div class="mb-3">
                        <strong>Admin Notes:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {{ $report->admin_notes ?: 'No notes provided' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="border-top pt-3">
                        @if($report->isPending())
                            <button type="button" class="btn btn-success me-2" onclick="reviewReport()">
                                <i class="ti ti-check"></i> Mark as Reviewed
                            </button>
                            <button type="button" class="btn btn-primary me-2" onclick="takeAction()">
                                <i class="ti ti-shield-check"></i> Take Action
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="dismissReport()">
                                <i class="ti ti-x"></i> Dismiss
                            </button>
                        @else
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> This report has been {{ str_replace('_', ' ', $report->status) }}.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal for Full View -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Screenshot Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Screenshot">
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reviewForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Review Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="4" placeholder="Enter your review notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    function reviewReport() {
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }

    function takeAction() {
        if (confirm('Mark this report as "Action Taken"?')) {
            updateStatus('action_taken');
        }
    }

    function dismissReport() {
        if (confirm('Are you sure you want to dismiss this report?')) {
            updateStatus('dismissed');
        }
    }

    function updateStatus(status, notes = '') {
        fetch('{{ route('admin.message-reports.update-status', $report->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                admin_notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'Failed to update status');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to update status');
        });
    }

    // Review form submission
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        updateStatus('reviewed', formData.get('admin_notes'));
        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
    });
</script>
@endpush
