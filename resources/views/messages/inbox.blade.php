@extends('admin.components.template')

@section('content')
<style>
    .inbox-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .inbox-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    .inbox-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .inbox-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .inbox-body {
        padding: 0;
    }
    .compose-btn {
        background: white;
        color: #667eea;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: transform 0.2s;
    }
    .compose-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        color: #667eea;
    }
    .message-item {
        border: none;
        border-bottom: 1px solid #e9ecef;
        padding: 20px 24px;
        transition: all 0.2s ease;
        cursor: pointer;
        background: white;
    }
    .message-item:hover {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding-left: 20px;
    }
    .message-item:last-child {
        border-bottom: none;
        border-radius: 0 0 12px 12px;
    }
    .message-item.unread {
        background: #f0f7ff;
        border-left: 4px solid #667eea;
        padding-left: 20px;
    }
    .message-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .message-content {
        flex: 1;
    }
    .message-subject {
        font-weight: 600;
        font-size: 1rem;
        color: #212529;
        margin-bottom: 4px;
    }
    .message-preview {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 6px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .message-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .message-meta i {
        font-size: 1rem;
    }
    .message-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .btn-open {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        transition: transform 0.2s;
    }
    .btn-open:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #adb5bd;
    }
    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .empty-state h5 {
        color: #6c757d;
        margin-bottom: 12px;
    }
    .empty-state p {
        color: #adb5bd;
        margin-bottom: 24px;
    }
    .unread-badge {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: #667eea;
        border-radius: 50%;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .search-bar {
        padding: 16px 24px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }
    .search-bar input {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 10px 16px;
        padding-left: 40px;
    }
    .search-bar .search-icon {
        position: absolute;
        left: 38px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
</style>

<div class="inbox-container">
    <div class="card inbox-card">
        <div class="inbox-header">
            <h5><i class="ti ti-inbox me-2"></i>Inbox</h5>
            <a href="{{ route('admin.messages.compose') }}" class="btn compose-btn">
                <i class="ti ti-plus me-2"></i>Compose Message
            </a>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <div class="position-relative">
                <i class="ti ti-search search-icon"></i>
                <input type="text" id="message-search" class="form-control" placeholder="Search messages...">
            </div>
        </div>

        <div class="inbox-body">
            @if($recipients->isEmpty())
                <div class="empty-state">
                    <i class="ti ti-inbox-off"></i>
                    <h5>Your inbox is empty</h5>
                    <p>No messages yet. When you receive messages, they'll appear here.</p>
                    <a href="{{ route('admin.messages.compose') }}" class="btn btn-primary">
                        <i class="ti ti-mail me-2"></i>Send Your First Message
                    </a>
                </div>
            @else
                <div class="message-list">
                    @foreach($recipients as $r)
                    <div class="message-item {{ $r->read_at ? '' : 'unread' }}" 
                         data-subject="{{ strtolower($r->message->subject ?? '(no subject)') }}"
                         data-sender="{{ strtolower(optional($r->message->sender)->name ?? 'System') }}">
                        <div class="d-flex align-items-start">
                            <div class="message-avatar">
                                {{ strtoupper(substr(optional($r->message->sender)->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="message-content">
                                <div class="message-subject">
                                    @if(!$r->read_at)
                                        <span class="unread-badge"></span>
                                    @endif
                                    {{ $r->message->subject ?? '(No subject)' }}
                                </div>
                                <div class="message-preview">
                                    {{ Str::limit($r->message->body, 100) }}
                                </div>
                                <div class="message-meta">
                                    <span>
                                        <i class="ti ti-user"></i>
                                        {{ optional($r->message->sender)->name ?? 'System' }}
                                    </span>
                                    <span>
                                        <i class="ti ti-clock"></i>
                                        {{ $r->created_at->diffForHumans() }}
                                    </span>
                                    @if(!$r->read_at)
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                            </div>
                            <div class="message-actions">
                                <a href="{{ route('admin.messages.show', $r->id) }}" class="btn btn-sm btn-open">
                                    <i class="ti ti-eye me-1"></i>Open
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('message-search');
    const messageItems = document.querySelectorAll('.message-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            messageItems.forEach(item => {
                const subject = item.getAttribute('data-subject');
                const sender = item.getAttribute('data-sender');
                
                if (subject.includes(searchTerm) || sender.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Make message items clickable
    messageItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.closest('.btn')) {
                const openBtn = this.querySelector('.btn-open');
                if (openBtn) {
                    window.location.href = openBtn.getAttribute('href');
                }
            }
        });
    });
});
</script>
@endsection
