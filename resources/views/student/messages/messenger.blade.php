@extends('student.components.template')

@section('content')
<style>
    .conversation-item {
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .conversation-item:hover {
        background-color: #f8f9fa;
    }
    .conversation-item.active {
        background-color: #e7f3ff;
        border-left: 3px solid #007bff;
    }
    .conversation-item.has-unread {
        font-weight: 600;
        background-color: #fff8f0;
    }
    .unread-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .user-select-item {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    
    /* Typing Indicator Animation */
    .typing-dots {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .typing-dots span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #6c757d;
        animation: typing-bounce 1.4s infinite ease-in-out;
    }
    .typing-dots span:nth-child(1) {
        animation-delay: 0s;
    }
    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }
    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }
    @keyframes typing-bounce {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.7;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }
    #typing-indicator {
        padding-left: 15px;
        margin-top: 10px;
    }
    .user-select-item:hover {
        background-color: #f8f9fa;
    }
    .user-select-item.selected {
        background-color: #e7f3ff;
        border-left: 3px solid #007bff;
    }
    .user-avatar-small {
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
</style>

<div class="page-header">
    <h3 class="page-title">Messages</h3>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Conversations</h5>
                <button class="btn btn-sm btn-primary" id="new-conversation-btn">
                    <i class="ti ti-plus"></i> New
                </button>
            </div>
            <div class="card-body p-0">
                <ul id="conversation-list" class="list-group list-group-flush">
                    @forelse($partners as $p)
                    <li class="list-group-item conversation-item {{ $p->unread_count > 0 ? 'has-unread' : '' }}" data-user-id="{{ $p->id }}" data-user-name="{{ $p->name }}" data-user-email="{{ $p->email }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $p->name }}</strong>
                                <div class="small text-muted">{{ $p->email }}</div>
                            </div>
                            <div class="text-end">
                                @if($p->unread_count > 0)
                                <span class="badge bg-danger unread-badge" id="unread-{{ $p->id }}">{{ $p->unread_count }}</span>
                                @else
                                <span class="badge bg-danger d-none" id="unread-{{ $p->id }}">0</span>
                                @endif
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">
                        No conversations yet<br>
                        <small>Click "New" to start messaging</small>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <div id="thread-header">Select a conversation</div>
            </div>
            <div class="card-body overflow-auto" id="thread-body" style="height:60vh">
                <div id="thread-messages" class="d-flex flex-column gap-3"></div>
                <!-- Typing Indicator -->
                <div id="typing-indicator" class="d-flex align-items-center gap-2 p-2" style="display: none !important;">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <small class="text-muted" id="typing-text">Someone is typing...</small>
                </div>
            </div>
            <div class="card-footer">
                <form id="send-form">
                    @csrf
                    <input type="hidden" name="to" id="to-user-id">
                    <div class="input-group">
                        <input id="message-input" name="body" class="form-control" placeholder="Type a message..." autocomplete="off">
                        <button class="btn btn-primary" type="submit">Send</button>
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
                <!-- Selected User Display -->
                <div id="selected-user-display" class="alert alert-info d-none mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong>Selected:</strong>
                            <span id="selected-user-name" class="ms-2"></span>
                            <small id="selected-user-email" class="d-block text-muted"></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-selection">
                            <i class="ti ti-x"></i> Change
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Search for a user:</label>
                    <input type="text" id="user-search-input" class="form-control" placeholder="Type name or email...">
                </div>
                <div id="user-select-dropdown" style="max-height: 300px; overflow-y: auto;">
                    <!-- Users will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="start-chat-btn" disabled>
                    <i class="ti ti-message"></i> Start Chat
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function(){
        const list = document.getElementById('conversation-list');
        const threadHeader = document.getElementById('thread-header');
        const threadMessages = document.getElementById('thread-messages');
        const toInput = document.getElementById('to-user-id');
        const sendForm = document.getElementById('send-form');
        const messageInput = document.getElementById('message-input');

        let currentUserId = null;
        const ME_ID = "{{ Auth::id() ?? '' }}";
        let lastMessageId = 0;
        let autoRefreshInterval = null;

        function formatTime(dt) {
            const d = new Date(dt);
            return d.toLocaleString();
        }

        function loadConversation(userId, scrollToBottom = true) {
            fetch("{{ url('/student/messenger/conversation/') }}/" + userId)
                .then(r => r.json())
                .then(data => {
                    currentUserId = userId;
                    toInput.value = userId;
                    threadHeader.textContent = data.conversation_with.name || '';
                    threadMessages.innerHTML = '';
                    
                    // Hide unread badge for this conversation
                    const unreadBadge = document.getElementById('unread-' + userId);
                    if (unreadBadge) {
                        unreadBadge.classList.add('d-none');
                        unreadBadge.textContent = '0';
                    }
                    
                    // Remove has-unread class from conversation item
                    const conversationItem = document.querySelector('.conversation-item[data-user-id="' + userId + '"]');
                    if (conversationItem) {
                        conversationItem.classList.remove('has-unread');
                    }
                    
                    // Track the last message ID
                    lastMessageId = 0;
                    if (data.messages.length > 0) {
                        lastMessageId = data.messages[data.messages.length - 1].id;
                    }
                    
                    data.messages.forEach(m => {
                        const div = document.createElement('div');
                        div.className = 'p-2 rounded position-relative';
                        if (String(m.from) === ME_ID) {
                            div.classList.add('bg-primary', 'text-white', 'align-self-end');
                            div.style.maxWidth = '70%';
                        } else {
                            div.classList.add('bg-light', 'text-dark', 'align-self-start');
                            div.style.maxWidth = '70%';
                        }
                        
                        let messageHtml = '';
                        
                        // Add 3-dot menu for own messages (upper right)
                        if (String(m.from) === ME_ID) {
                            messageHtml += '<div class="dropdown position-absolute top-0 end-0" style="margin: 4px;">';
                            messageHtml += '<button class="btn btn-link btn-sm p-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">';
                            messageHtml += '<i class="ti ti-dots-vertical" style="font-size: 16px;"></i>';
                            messageHtml += '</button>';
                            messageHtml += '<ul class="dropdown-menu dropdown-menu-end">';
                            messageHtml += '<li><a class="dropdown-item text-danger unsend-btn" href="#" data-message-id="' + m.id + '"><i class="ti ti-trash me-2"></i>Delete</a></li>';
                            messageHtml += '</ul>';
                            messageHtml += '</div>';
                        }
                        
                        messageHtml += '<div style="padding-right: 20px;">';
                        messageHtml += (m.subject ? '<strong>' + escapeHtml(m.subject) + '</strong><br>' : '') + nl2br(escapeHtml(m.body));
                        messageHtml += '</div><div class="small mt-1" style="opacity: 0.8;">' + formatTime(m.created_at) + '</div>';
                        
                        div.innerHTML = messageHtml;
                        div.setAttribute('data-message-id', m.id);
                        threadMessages.appendChild(div);
                    });
                    
                    if (scrollToBottom) {
                        threadMessages.scrollTop = threadMessages.scrollHeight;
                    }
                    
                    // Start auto-refresh for this conversation
                    startAutoRefresh();
                });
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

        // Auto-refresh functions
        function startAutoRefresh() {
            // Clear existing interval if any
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            
            // Check for new messages every 3 seconds
            autoRefreshInterval = setInterval(checkForNewMessages, 3000);
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        function checkForNewMessages() {
            if (!currentUserId) return;
            
            fetch("{{ url('/student/messenger/conversation/') }}/" + currentUserId)
                .then(r => r.json())
                .then(data => {
                    // Check if there are new messages
                    if (data.messages.length > 0) {
                        const latestMessageId = data.messages[data.messages.length - 1].id;
                        
                        if (latestMessageId > lastMessageId) {
                            // There are new messages, append them
                            data.messages.forEach(m => {
                                if (m.id > lastMessageId) {
                                    const div = document.createElement('div');
                                    div.className = 'p-2 rounded position-relative';
                                    if (String(m.from) === ME_ID) {
                                        div.classList.add('bg-primary', 'text-white', 'align-self-end');
                                        div.style.maxWidth = '70%';
                                    } else {
                                        div.classList.add('bg-light', 'text-dark', 'align-self-start');
                                        div.style.maxWidth = '70%';
                                    }
                                    
                                    let msgHtml = '';
                                    
                                    // Add 3-dot menu for own messages (upper right)
                                    if (String(m.from) === ME_ID) {
                                        msgHtml += '<div class="dropdown position-absolute top-0 end-0" style="margin: 4px;">';
                                        msgHtml += '<button class="btn btn-link btn-sm p-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">';
                                        msgHtml += '<i class="ti ti-dots-vertical" style="font-size: 16px;"></i>';
                                        msgHtml += '</button>';
                                        msgHtml += '<ul class="dropdown-menu dropdown-menu-end">';
                                        msgHtml += '<li><a class="dropdown-item text-danger unsend-btn" href="#" data-message-id="' + m.id + '"><i class="ti ti-trash me-2"></i>Delete</a></li>';
                                        msgHtml += '</ul>';
                                        msgHtml += '</div>';
                                    }
                                    
                                    msgHtml += '<div style="padding-right: 20px;">';
                                    msgHtml += (m.subject ? '<strong>' + escapeHtml(m.subject) + '</strong><br>' : '') + nl2br(escapeHtml(m.body));
                                    msgHtml += '</div><div class="small mt-1" style="opacity: 0.8;">' + formatTime(m.created_at) + '</div>';
                                    
                                    div.innerHTML = msgHtml;
                                    div.setAttribute('data-message-id', m.id);
                                    threadMessages.appendChild(div);
                                }
                            });
                            
                            lastMessageId = latestMessageId;
                            threadMessages.scrollTop = threadMessages.scrollHeight;
                        }
                    }
                })
                .catch(err => {
                    console.error('Auto-refresh error:', err);
                });
        }

        list.addEventListener('click', function(e){
            const li = e.target.closest('.conversation-item');
            if (!li) return;
            const userId = li.dataset.userId;
            loadConversation(userId);
        });

        sendForm.addEventListener('submit', function(e){
            e.preventDefault();
            if (!currentUserId) return alert('Select a conversation first');
            const body = messageInput.value.trim();
            if (!body) return;
            
            fetch("{{ route('student.messages.sendConversation') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ to: currentUserId, body: body })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(res => {
                // Append message
                const m = res.message;
                const div = document.createElement('div');
                div.className = 'p-2 rounded bg-primary text-white align-self-end position-relative';
                div.style.maxWidth = '70%';
                
                let msgHtml = '';
                
                // Add 3-dot menu (upper right)
                msgHtml += '<div class="dropdown position-absolute top-0 end-0" style="margin: 4px;">';
                msgHtml += '<button class="btn btn-link btn-sm p-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">';
                msgHtml += '<i class="ti ti-dots-vertical" style="font-size: 16px;"></i>';
                msgHtml += '</button>';
                msgHtml += '<ul class="dropdown-menu dropdown-menu-end">';
                msgHtml += '<li><a class="dropdown-item text-danger unsend-btn" href="#" data-message-id="' + m.id + '"><i class="ti ti-trash me-2"></i>Delete</a></li>';
                msgHtml += '</ul>';
                msgHtml += '</div>';
                
                msgHtml += '<div style="padding-right: 20px;">';
                msgHtml += nl2br(escapeHtml(m.body));
                msgHtml += '</div><div class="small mt-1" style="opacity: 0.8;">' + new Date(m.created_at).toLocaleString() + '</div>';
                
                div.innerHTML = msgHtml;
                div.setAttribute('data-message-id', m.id);
                threadMessages.appendChild(div);
                messageInput.value = '';
                threadMessages.scrollTop = threadMessages.scrollHeight;
                
                // Update last message ID
                lastMessageId = m.id;
            })
            .catch(err => { 
                console.error('Send error:', err); 
                alert('Failed to send message. Please try again.'); 
            });
        });

        // ===== TYPING INDICATOR LOGIC =====
        const typingIndicator = document.getElementById('typing-indicator');
        const typingText = document.getElementById('typing-text');
        let typingTimeout = null;
        let isCurrentlyTyping = false;

        // Send typing status to recipient
        function sendTypingStatus(isTyping) {
            if (!currentUserId) return;
            
            fetch("{{ route('student.messages.typing') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    recipient_id: currentUserId,
                    is_typing: isTyping
                })
            }).catch(err => console.error('Typing broadcast error:', err));
        }

        // Listen for input in message field
        messageInput.addEventListener('input', function() {
            if (!currentUserId) return;
            
            // Start typing
            if (!isCurrentlyTyping) {
                isCurrentlyTyping = true;
                sendTypingStatus(true);
            }
            
            // Clear previous timeout
            clearTimeout(typingTimeout);
            
            // Stop typing after 2 seconds of no input
            typingTimeout = setTimeout(() => {
                isCurrentlyTyping = false;
                sendTypingStatus(false);
            }, 2000);
        });

        // Stop typing when form is submitted
        sendForm.addEventListener('submit', function() {
            clearTimeout(typingTimeout);
            if (isCurrentlyTyping) {
                isCurrentlyTyping = false;
                sendTypingStatus(false);
            }
        });

        // Show/hide typing indicator
        function showTypingIndicator(userName) {
            typingText.textContent = `${userName} is typing...`;
            typingIndicator.style.display = 'flex';
            // Auto-scroll to show typing indicator
            const threadBody = document.getElementById('thread-body');
            threadBody.scrollTop = threadBody.scrollHeight;
        }

        function hideTypingIndicator() {
            typingIndicator.style.display = 'none';
        }

        // Stop auto-refresh when leaving the page
        window.addEventListener('beforeunload', stopAutoRefresh);

        // Real-time unread count polling
        let unreadCountInterval = null;

        function updateUnreadCounts() {
            fetch("{{ route('student.api.unread-counts-by-partner') }}")
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.unread_counts) {
                        // Update each conversation item
                        document.querySelectorAll('.conversation-item').forEach(item => {
                            const userId = item.dataset.userId;
                            const unreadBadge = document.getElementById('unread-' + userId);
                            const unreadCount = data.unread_counts[userId] || 0;
                            
                            if (unreadBadge) {
                                if (unreadCount > 0 && userId !== String(currentUserId)) {
                                    unreadBadge.textContent = unreadCount;
                                    unreadBadge.classList.remove('d-none');
                                    item.classList.add('has-unread');
                                } else {
                                    unreadBadge.classList.add('d-none');
                                    item.classList.remove('has-unread');
                                }
                            }
                        });
                    }
                })
                .catch(err => console.error('Failed to fetch unread counts:', err));
        }

        // Start polling for unread counts every 5 seconds
        function startUnreadCountPolling() {
            if (unreadCountInterval) {
                clearInterval(unreadCountInterval);
            }
            updateUnreadCounts(); // Initial update
            unreadCountInterval = setInterval(updateUnreadCounts, 5000);
        }

        function stopUnreadCountPolling() {
            if (unreadCountInterval) {
                clearInterval(unreadCountInterval);
                unreadCountInterval = null;
            }
        }

        // Start polling when page loads
        startUnreadCountPolling();

        // Stop polling when leaving
        window.addEventListener('beforeunload', stopUnreadCountPolling);

        // Load all users for new conversation modal
        let allUsers = [];
        let selectedUser = null;
        const userSearchInput = document.getElementById('user-search-input');
        const userSelectDropdown = document.getElementById('user-select-dropdown');
        const newConvModal = document.getElementById('newConversationModal');
        const selectedUserDisplay = document.getElementById('selected-user-display');
        const selectedUserName = document.getElementById('selected-user-name');
        const selectedUserEmail = document.getElementById('selected-user-email');
        const startChatBtn = document.getElementById('start-chat-btn');
        const clearSelectionBtn = document.getElementById('clear-selection');

        function loadAllUsers() {
            // For students, we'll use the compose route to get available users
            // Or create a new API endpoint similar to teacher
            fetch("{{ url('/student/api/all-users') }}")
                .then(r => r.json())
                .then(users => {
                    allUsers = users;
                    renderUserList(allUsers);
                })
                .catch(err => {
                    console.error('Failed to load users:', err);
                    userSelectDropdown.innerHTML = '<div class="text-center text-danger py-3">Failed to load users</div>';
                });
        }

        function showSelectedUser(user) {
            selectedUser = user;
            selectedUserName.textContent = user.name;
            selectedUserEmail.textContent = user.email;
            selectedUserDisplay.classList.remove('d-none');
            startChatBtn.disabled = false;
            
            // Highlight the selected user in the list
            document.querySelectorAll('.user-select-item').forEach(item => {
                item.classList.remove('selected');
            });
            const selectedItem = document.querySelector(`[data-user-id="${user.id}"]`);
            if (selectedItem) {
                selectedItem.classList.add('selected');
            }
        }

        function clearSelection() {
            selectedUser = null;
            selectedUserDisplay.classList.add('d-none');
            startChatBtn.disabled = true;
            
            // Remove highlight from all items
            document.querySelectorAll('.user-select-item').forEach(item => {
                item.classList.remove('selected');
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
                item.dataset.userId = user.id;
                item.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="user-avatar-small">${user.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <strong>${escapeHtml(user.name)}</strong>
                            <div class="small text-muted">${escapeHtml(user.email)}</div>
                        </div>
                    </div>
                `;
                
                // If this user is already selected, highlight it
                if (selectedUser && selectedUser.id === user.id) {
                    item.classList.add('selected');
                }
                
                item.addEventListener('click', function() {
                    showSelectedUser(user);
                });
                
                userSelectDropdown.appendChild(item);
            });
        }

        // Clear selection button
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                clearSelection();
            });
        }

        // Start chat button
        if (startChatBtn) {
            startChatBtn.addEventListener('click', function() {
                if (!selectedUser) return;
                
                // Check if conversation already exists
                const existingConv = document.querySelector(`.conversation-item[data-user-id="${selectedUser.id}"]`);
                if (existingConv) {
                    existingConv.click();
                } else {
                    // Start new conversation
                    currentUserId = selectedUser.id;
                    toInput.value = selectedUser.id;
                    threadHeader.textContent = selectedUser.name;
                    threadMessages.innerHTML = '<div class="text-center text-muted py-3">Start the conversation!</div>';
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(newConvModal);
                if (modal) modal.hide();
                
                // Clear selection and search
                clearSelection();
                userSearchInput.value = '';
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
        if (newConvModal) {
            newConvModal.addEventListener('show.bs.modal', function() {
                console.log('Modal opening, loading users...');
                clearSelection();
                userSearchInput.value = '';
                loadAllUsers();
            });
        } else {
            console.error('Modal element not found!');
        }

        // Debug: Check if Bootstrap is available
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        console.log('Modal element found:', !!newConvModal);

        // Manual button click handler for New Conversation
        const newConvBtn = document.getElementById('new-conversation-btn');
        if (newConvBtn) {
            newConvBtn.addEventListener('click', function() {
                console.log('New button clicked');
                if (typeof bootstrap !== 'undefined' && newConvModal) {
                    const modal = new bootstrap.Modal(newConvModal);
                    modal.show();
                } else {
                    alert('Bootstrap modal not available. Please refresh the page.');
                }
            });
        } else {
            console.error('New conversation button not found!');
        }

        // Unsend message handler (event delegation)
        threadMessages.addEventListener('click', function(e) {
            const unsendBtn = e.target.closest('.unsend-btn');
            if (!unsendBtn) return;
            
            e.preventDefault(); // Prevent default link behavior
            
            const messageId = unsendBtn.getAttribute('data-message-id');
            
            if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
                fetch('/student/messages/' + messageId + '/unsend', {
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
                        
                        // Update lastMessageId if needed
                        const remainingMessages = threadMessages.querySelectorAll('[data-message-id]');
                        if (remainingMessages.length > 0) {
                            const ids = Array.from(remainingMessages).map(div => parseInt(div.getAttribute('data-message-id')));
                            lastMessageId = Math.max(...ids);
                        } else {
                            lastMessageId = 0;
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

        // ===== REAL-TIME LARAVEL ECHO INTEGRATION =====
        // Make sure Echo is loaded (add @@vite directive if not already present)
        if (typeof window.Echo !== 'undefined') {
            try {
                console.log('✓ Laravel Echo available, subscribing to channel...');
                
                // Subscribe to the current user's private channel using Laravel Echo
                const channel = window.Echo.private('user.{{ auth()->id() }}');

                console.log('✓ Subscribed to private channel: user.{{ auth()->id() }}');

                // Listen for new messages
                channel.listen('.message.sent', function(data) {
                    console.log('✓ Real-time message received:', data);

                    // Only process if we're viewing the conversation with the sender
                    if (currentUserId && parseInt(data.sender_id) === parseInt(currentUserId)) {
                        // Append the message to the conversation
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'd-flex justify-content-start mb-3';
                        messageDiv.setAttribute('data-message-id', data.id);
                        
                        let attachmentHtml = '';
                        if (data.attachment_path) {
                            const fileIcon = getFileIcon(data.attachment_type);
                            const fileSize = formatFileSize(data.attachment_size);
                            attachmentHtml = `
                                <div class="mt-2">
                                    <a href="/student/messages/${data.id}/download" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-${fileIcon} me-1"></i>
                                        ${escapeHtml(data.attachment_name)}
                                        <span class="badge bg-secondary ms-2">${fileSize}</span>
                                    </a>
                                </div>
                            `;
                        }

                        messageDiv.innerHTML = `
                            <div class="message-bubble bg-light p-3 rounded shadow-sm" style="max-width:70%">
                                <div class="mb-1"><strong>${escapeHtml(data.sender_name || 'User')}</strong></div>
                                <div>${escapeHtml(data.body)}</div>
                                ${attachmentHtml}
                                <div class="small text-muted mt-2">${formatDate(data.created_at)}</div>
                            </div>
                        `;

                        threadMessages.appendChild(messageDiv);
                        threadMessages.scrollTop = threadMessages.scrollHeight;

                        // Update lastMessageId
                        if (data.id > lastMessageId) {
                            lastMessageId = data.id;
                        }

                        // Show notification if not in focus
                        if (document.hidden) {
                            showNotification('New message from ' + (data.sender_name || 'User'));
                        }
                    }

                    // Update conversation list (add sender if not exists)
                    updateConversationList(data);
                });

                // Listen for typing events
                channel.listen('.user.typing', function(data) {
                    console.log('✓ Typing event received:', data);
                    
                    // Only show if typing from current conversation partner
                    if (currentUserId && parseInt(data.user_id) === parseInt(currentUserId)) {
                        if (data.is_typing) {
                            showTypingIndicator(data.user_name);
                        } else {
                            hideTypingIndicator();
                        }
                    }
                });

                // Handle connection state
                console.log('✓ Echo real-time messaging is active');

            } catch (error) {
                console.error('❌ Failed to initialize Laravel Echo:', error);
                console.log('⚠️ Falling back to polling...');
            }
        } else {
            console.warn('⚠️ Laravel Echo not available. Make sure @@vite directive is added and npm run dev is running.');
            console.log('⚠️ Messages will use polling instead of real-time updates.');
        }

        // Helper function to update conversation list
        function updateConversationList(messageData) {
            const senderId = messageData.sender_id;
            const existingConv = document.querySelector(`.conversation-item[data-user-id="${senderId}"]`);
            
            if (!existingConv && senderId !== {{ auth()->id() }}) {
                // Add new conversation to the list
                const conversationList = document.getElementById('conversation-list');
                const newConv = document.createElement('li');
                newConv.className = 'list-group-item conversation-item';
                newConv.setAttribute('data-user-id', senderId);
                newConv.setAttribute('data-user-name', messageData.sender_name);
                newConv.setAttribute('data-user-email', '');
                
                newConv.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${escapeHtml(messageData.sender_name)}</strong>
                            <div class="small text-muted">New message</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary" id="unread-${senderId}">1</span>
                        </div>
                    </div>
                `;
                
                // Add click handler
                newConv.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    
                    document.querySelectorAll('.conversation-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    loadConversation(userId, userName);
                });
                
                conversationList.insertBefore(newConv, conversationList.firstChild);
            }
        }

        // Show browser notification
        function showNotification(message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Message', {
                    body: message,
                    icon: '/favicon.ico'
                });
            }
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

    })();
</script>

<!-- Load Laravel Echo via Vite for real-time messaging -->
@vite(['resources/js/app.js'])

@endpush
