@extends('admin.components.template')

@section('content')
<style>
    .message-view-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .message-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .message-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
    }
    .message-header h4 {
        margin: 0 0 12px 0;
        font-weight: 600;
        font-size: 1.5rem;
    }
    .message-meta-header {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 0.95rem;
        opacity: 0.95;
    }
    .message-meta-header i {
        font-size: 1.1rem;
    }
    .message-body-section {
        padding: 32px;
        background: white;
    }
    .sender-info {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    .sender-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 24px;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .sender-details h6 {
        margin: 0 0 4px 0;
        font-weight: 600;
        color: #212529;
    }
    .sender-details .text-muted {
        font-size: 0.9rem;
    }
    .message-content {
        font-size: 1rem;
        line-height: 1.7;
        color: #495057;
        padding: 24px;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        white-space: pre-wrap;
    }
    .message-actions {
        padding: 20px 32px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 12px;
    }
    .btn-reply {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        transition: transform 0.2s;
    }
    .btn-reply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .btn-back {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: 2px solid #e9ecef;
    }
    .btn-back:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
    }
    .message-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e9ecef, transparent);
        margin: 24px 0;
    }
</style>

<div class="message-view-container">
    <div class="card message-card">
        <!-- Header -->
        <div class="message-header">
            <h4>{{ $recipient->message->subject ?? '(No subject)' }}</h4>
            <div class="message-meta-header">
                <span>
                    <i class="ti ti-calendar"></i>
                    {{ $recipient->created_at->format('F j, Y') }}
                </span>
                <span>
                    <i class="ti ti-clock"></i>
                    {{ $recipient->created_at->format('g:i A') }}
                </span>
                <span>
                    <i class="ti ti-clock-hour-3"></i>
                    {{ $recipient->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <!-- Body -->
        <div class="message-body-section">
            <!-- Sender Info -->
            <div class="sender-info">
                <div class="sender-avatar">
                    {{ strtoupper(substr(optional($recipient->message->sender)->name ?? 'S', 0, 1)) }}
                </div>
                <div class="sender-details">
                    <h6>{{ optional($recipient->message->sender)->name ?? 'System' }}</h6>
                    <div class="text-muted">
                        <i class="ti ti-mail me-1"></i>{{ optional($recipient->message->sender)->email ?? 'system@school.test' }}
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="message-content">
                {!! nl2br(e($recipient->message->body)) !!}
            </div>
        </div>

        <!-- Actions -->
        <div class="message-actions">
            <a href="{{ route('admin.messages.messenger') }}" class="btn btn-reply">
                <i class="ti ti-arrow-back-up me-2"></i>Reply
            </a>
            <a href="{{ route('admin.messages.inbox') }}" class="btn btn-outline-secondary btn-back">
                <i class="ti ti-arrow-left me-2"></i>Back to Inbox
            </a>
            <a href="{{ route('admin.messages.messenger') }}" class="btn btn-outline-secondary">
                <i class="ti ti-messages me-2"></i>All Messages
            </a>
        </div>
    </div>
</div>
@endsection
