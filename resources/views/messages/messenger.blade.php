@extends('admin.components.template')

@section('content')
<style>
    .conversation-item {
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .conversation-item:hover {
        background-color: #f8f9fa;
        border-left-color: #007bff;
    }
    .conversation-item.active {
        background-color: #e7f3ff;
        border-left-color: #007bff;
    }
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        margin-bottom: 8px;
        word-wrap: break-word;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .message-sent {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    .message-received {
        background-color: #f1f3f5;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }
    .user-search {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    .new-conversation-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: transform 0.2s;
    }
    .new-conversation-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    #user-select-dropdown {
        max-height: 300px;
        overflow-y: auto;
    }
    .user-select-item {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    .user-select-item:hover {
        background-color: #f8f9fa;
    }
    .user-select-item .user-avatar-small {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
        margin-right: 10px;
    }
    #attach-btn {
        transition: all 0.3s ease;
    }
    #attach-btn:hover {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
        transform: scale(1.05);
    }
    #attachment-preview {
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .attachment-link {
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .attachment-link:hover {
        transform: translateX(5px);
    }
    .message-time {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 4px;
    }
    #message-input {
        border-radius: 25px;
        padding: 12px 20px;
        border: 2px solid #e9ecef;
    }
    #message-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .send-btn {
        border-radius: 25px;
        padding: 12px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: transform 0.2s;
    }
    .send-btn:hover {
        transform: scale(1.05);
    }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #adb5bd;
    }
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
    }
</style>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="ti ti-messages me-2"></i>Messages</h5>
                <button class="btn btn-sm btn-info new-conversation-btn" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                    <i class="ti ti-plus"></i> New
                </button>
            </div>
            
            <!-- Search Box -->
            <div class="user-search">
                <input type="text" id="conversation-search" class="form-control" placeholder="🔍 Search conversations...">
            </div>
            
            <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                <ul id="conversation-list" class="list-group list-group-flush">
                    @forelse($partners as $p)
                    <li class="list-group-item conversation-item" data-user-id="{{ $p->id }}" data-user-name="{{ strtolower($p->name) }}">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar">
                                {{ strtoupper(substr($p->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="d-block">{{ $p->name }}</strong>
                                        <small class="text-muted">{{ $p->email }}</small>
                                    </div>
                                    <span class="badge bg-primary d-none" id="unread-{{ $p->id }}">0</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-5">
                        <i class="ti ti-inbox" style="font-size: 48px;"></i>
                        <p class="mt-2">No conversations yet</p>
                        <small>Click "New" to start messaging</small>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center py-3">
                <div class="user-avatar me-3" id="thread-avatar" style="display: none;">A</div>
                <div class="flex-grow-1">
                    <h5 class="mb-0" id="thread-header">Select a conversation</h5>
                    <small class="text-muted" id="thread-subtitle"></small>
                </div>
            </div>
            
            <div class="card-body overflow-auto" id="thread-body" style="height:60vh; background: #f8f9fa;">
                <div id="thread-messages" class="d-flex flex-column gap-2">
                    <div class="empty-state">
                        <i class="ti ti-message-circle"></i>
                        <p>Select a conversation to start messaging</p>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-white py-3">
                <form id="send-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="to" id="to-user-id">
                    <div id="attachment-preview" class="mb-2" style="display: none;">
                        <div class="alert alert-info py-2 px-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-paperclip me-2"></i>
                                <span id="attachment-name">File.pdf</span>
                                <small class="text-muted ms-2" id="attachment-size"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" id="remove-attachment">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="file" id="file-input" name="attachment" class="d-none" accept="*/*">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center" id="attach-btn" title="Attach file (Max 10MB)">
                            <i class="ti ti-paperclip fs-5"></i>
                        </button>
                        <input id="message-input" name="body" class="form-control" placeholder="Type your message..." autocomplete="off">
                        <button class="btn btn-info send-btn d-flex align-items-center" type="submit">
                            <i class="ti ti-send me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-message-plus me-2"></i>New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Search for a user:</label>
                    <input type="text" id="user-search-input" class="form-control" placeholder="Type name or email...">
                </div>
                <div id="user-select-dropdown" class="border rounded">
                    <!-- Users will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pusher Scripts -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>

@push('scripts')
<script>
    (function(){
        const list = document.getElementById('conversation-list');
        const threadHeader = document.getElementById('thread-header');
        const threadSubtitle = document.getElementById('thread-subtitle');
        const threadAvatar = document.getElementById('thread-avatar');
        const threadMessages = document.getElementById('thread-messages');
        const toInput = document.getElementById('to-user-id');
        const sendForm = document.getElementById('send-form');
        const messageInput = document.getElementById('message-input');
        const conversationSearch = document.getElementById('conversation-search');
        const userSearchInput = document.getElementById('user-search-input');
        const userSelectDropdown = document.getElementById('user-select-dropdown');
        const fileInput = document.getElementById('file-input');
        const attachBtn = document.getElementById('attach-btn');
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentName = document.getElementById('attachment-name');
        const attachmentSize = document.getElementById('attachment-size');
        const removeAttachmentBtn = document.getElementById('remove-attachment');

        let currentUserId = null;
        let allUsers = [];
        const ME_ID = "{{ Auth::id() ?? '' }}";

        // Initialize Pusher and Laravel Echo
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            }
        });

        // Listen for incoming messages
        Echo.private('user.' + ME_ID)
            .listen('.message.sent', (e) => {
                console.log('New message received:', e);
                
                if (currentUserId && String(e.from) === String(currentUserId)) {
                    appendMessage(e, false);
                }
                
                const badge = document.getElementById('unread-' + e.from);
                if (badge && String(e.from) !== String(currentUserId)) {
                    badge.classList.remove('d-none');
                    const count = parseInt(badge.textContent) || 0;
                    badge.textContent = count + 1;
                }
            });

        // Conversation search functionality
        if (conversationSearch) {
            conversationSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const conversations = document.querySelectorAll('.conversation-item');
                
                conversations.forEach(conv => {
                    const userName = conv.getAttribute('data-user-name');
                    if (userName && userName.includes(searchTerm)) {
                        conv.style.display = '';
                    } else {
                        conv.style.display = 'none';
                    }
                });
            });
        }

        // Conversation click handler with active state
        list.addEventListener('click', function(e){
            const li = e.target.closest('.conversation-item');
            if (!li) return;
            
            // Update active state
            document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
            li.classList.add('active');
            
            const userId = li.dataset.userId;
            const userName = li.querySelector('strong')?.textContent || 'User';
            const userEmail = li.querySelector('.text-muted')?.textContent || '';
            
            loadConversation(userId, userName, userEmail);
        });

        function formatTime(dt) {
            const d = new Date(dt);
            return d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        function loadConversation(userId, userName, userEmail) {
            fetch("{{ url('/admin/messenger/conversation/') }}/" + userId)
                .then(r => r.json())
                .then(data => {
                    currentUserId = userId;
                    toInput.value = userId;
                    
                    // Update header with avatar
                    threadHeader.textContent = userName || data.conversation_with.name || '';
                    threadSubtitle.textContent = userEmail || data.conversation_with.email || '';
                    threadAvatar.textContent = userName ? userName.charAt(0).toUpperCase() : 'U';
                    threadAvatar.style.display = 'flex';
                    
                    threadMessages.innerHTML = '';
                    
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(m => {
                            appendMessage(m, String(m.from) === ME_ID);
                        });
                    } else {
                        threadMessages.innerHTML = '<div class="text-center text-muted py-5"><p>No messages yet. Start the conversation!</p></div>';
                    }
                    
                    threadMessages.scrollTop = threadMessages.scrollHeight;
                    
                    const badge = document.getElementById('unread-' + userId);
                    if (badge) {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                    }
                });
        }

        function appendMessage(msg, isSent) {
            // Remove empty state if it exists
            const emptyState = threadMessages.querySelector('.text-center.text-muted');
            if (emptyState) emptyState.remove();
            
            const msgDiv = document.createElement('div');
            msgDiv.className = 'd-flex flex-column ' + (isSent ? 'align-items-end' : 'align-items-start');
            msgDiv.setAttribute('data-message-id', msg.id);
            
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble position-relative ' + (isSent ? 'message-sent' : 'message-received');
            
            // Add 3-dot menu for sent messages (upper right, white color)
            if (isSent) {
                const dropdown = document.createElement('div');
                dropdown.className = 'dropdown position-absolute top-0 end-0';
                dropdown.style.margin = '4px';
                dropdown.innerHTML = `
                    <button class="btn btn-link btn-sm p-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">
                        <i class="ti ti-dots-vertical" style="font-size: 16px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger unsend-btn" href="#" data-message-id="${msg.id}"><i class="ti ti-trash me-2"></i>Delete</a></li>
                    </ul>
                `;
                bubble.appendChild(dropdown);
            }
            
            // Add message content with padding to avoid overlap with menu
            const contentDiv = document.createElement('div');
            contentDiv.style.paddingRight = isSent ? '20px' : '0';
            
            // Only show body if it's not the default "(File attachment)" text
            if (msg.body && msg.body !== '(File attachment)') {
                contentDiv.textContent = msg.body;
            }
            
            bubble.appendChild(contentDiv);
            
            // Add attachment if exists
            if (msg.attachment_path && msg.attachment_name) {
                const attachDiv = document.createElement('div');
                attachDiv.className = 'mt-2';
                attachDiv.style.paddingRight = isSent ? '20px' : '0';
                attachDiv.innerHTML = `
                    <a href="/admin/messages/${msg.id}/download" class="btn btn-sm ${isSent ? 'btn-light' : 'btn-outline-primary'}" download>
                        <i class="ti ti-download"></i> ${escapeHtml(msg.attachment_name)}
                        <small class="text-muted">(${formatBytes(msg.attachment_size)})</small>
                    </a>
                `;
                bubble.appendChild(attachDiv);
            }
            
            const time = document.createElement('div');
            time.className = 'message-time ' + (isSent ? 'text-end' : 'text-start');
            time.textContent = formatTime(msg.created_at);
            
            msgDiv.appendChild(bubble);
            msgDiv.appendChild(time);
            threadMessages.appendChild(msgDiv);
            
            threadMessages.scrollTop = threadMessages.scrollHeight;
        }

        function formatBytes(bytes) {
            if (!bytes) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function nl2br (str) {
            return str.replace(/\n/g, '<br>');
        }

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/\"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        // File upload handlers
        attachBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    fileInput.value = '';
                    return;
                }
                
                attachmentName.textContent = file.name;
                attachmentSize.textContent = formatBytes(file.size);
                attachmentPreview.style.display = 'block';
            }
        });

        removeAttachmentBtn.addEventListener('click', function() {
            fileInput.value = '';
            attachmentPreview.style.display = 'none';
        });

        sendForm.addEventListener('submit', function(e){
            e.preventDefault();
            if (!currentUserId) return alert('Select a conversation first');
            
            const body = messageInput.value.trim();
            const hasFile = fileInput.files.length > 0;
            
            // Require either message text OR file attachment
            if (!body && !hasFile) {
                return alert('Please enter a message or attach a file');
            }
            
            const formData = new FormData(sendForm);
            
            fetch("{{ route('admin.messages.sendConversation') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                // Check if response is OK
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(res => {
                if (res.success && res.message) {
                    appendMessage(res.message, true);
                    messageInput.value = '';
                    fileInput.value = '';
                    attachmentPreview.style.display = 'none';
                } else {
                    throw new Error('Invalid response format');
                }
            })
            .catch(err => { 
                console.error('Send error:', err); 
                alert('Failed to send message. Please try again.'); 
            });
        });

        // Load all users for new conversation modal
        function loadAllUsers() {
            // Fetch all users from backend
            fetch("{{ url('/admin/api/all-users') }}")
                .then(r => r.json())
                .then(users => {
                    allUsers = users.filter(u => String(u.id) !== ME_ID);
                    renderUserList(allUsers);
                })
                .catch(err => {
                    console.error('Failed to load users:', err);
                    userSelectDropdown.innerHTML = '<div class="text-center text-danger py-3">Failed to load users</div>';
                });
        }

        function renderUserList(users) {
            userSelectDropdown.innerHTML = '';
            
            if (users.length === 0) {
                userSelectDropdown.innerHTML = '<div class="text-center text-muted py-3">No users found</div>';
                return;
            }
            
            users.forEach(user => {
                const item = document.createElement('div');
                item.className = 'user-select-item';
                item.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="user-avatar-small">${user.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <strong>${escapeHtml(user.name)}</strong>
                            <div class="small text-muted">${escapeHtml(user.email)}</div>
                        </div>
                    </div>
                `;
                
                item.addEventListener('click', function() {
                    const existingConv = document.querySelector(`[data-user-id="${user.id}"]`);
                    if (existingConv) {
                        existingConv.click();
                    } else {
                        loadConversation(user.id, user.name, user.email);
                    }
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
                    if (modal) modal.hide();
                    
                    userSearchInput.value = '';
                    renderUserList(allUsers);
                });
                
                userSelectDropdown.appendChild(item);
            });
        }

        // User search in modal
        if (userSearchInput) {
            userSearchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const filtered = allUsers.filter(u => 
                    u.name.toLowerCase().includes(searchTerm) || 
                    u.email.toLowerCase().includes(searchTerm)
                );
                renderUserList(filtered);
            });
        }

        // Load users when modal opens
        const newConvModal = document.getElementById('newConversationModal');
        if (newConvModal) {
            newConvModal.addEventListener('show.bs.modal', function() {
                loadAllUsers();
            });
        }

        // Unsend message handler (event delegation)
        threadMessages.addEventListener('click', function(e) {
            const unsendBtn = e.target.closest('.unsend-btn');
            if (!unsendBtn) return;
            
            e.preventDefault(); // Prevent default link behavior
            
            const messageId = unsendBtn.getAttribute('data-message-id');
            
            if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
                fetch('/admin/messages/' + messageId + '/unsend', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        // Remove the message from UI
                        const messageDiv = threadMessages.querySelector('[data-message-id="' + messageId + '"]');
                        if (messageDiv) {
                            messageDiv.remove();
                        }
                    } else {
                        alert(res.error || 'Failed to delete message');
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('Failed to delete message. Please try again.');
                });
            }
        });

    })();
</script>
@endpush

@endsection
