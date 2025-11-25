

<?php $__env->startSection('content'); ?>
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
                <button class="btn btn-sm bg-info" id="new-conversation-btn">
                    <i class="ti ti-plus"></i> New
                </button>
            </div>
            <div class="card-body p-0">
                <ul id="conversation-list" class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item conversation-item <?php echo e($p->unread_count > 0 ? 'has-unread' : ''); ?>" data-user-id="<?php echo e($p->id); ?>" data-user-name="<?php echo e($p->name); ?>" data-user-email="<?php echo e($p->email); ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1" style="cursor: pointer;" onclick="document.querySelector('[data-user-id=&quot;<?php echo e($p->id); ?>&quot;]').click()">
                                <strong><?php echo e($p->name); ?></strong>
                                <div class="small text-muted"><?php echo e($p->email); ?></div>
                            </div>
                            <div class="text-end d-flex align-items-center gap-2">
                                <?php if($p->unread_count > 0): ?>
                                <span class="badge bg-danger unread-badge" id="unread-<?php echo e($p->id); ?>"><?php echo e($p->unread_count); ?></span>
                                <?php else: ?>
                                <span class="badge bg-danger d-none" id="unread-<?php echo e($p->id); ?>">0</span>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-danger delete-conversation-btn" data-user-id="<?php echo e($p->id); ?>" data-user-name="<?php echo e($p->name); ?>" title="Delete conversation" onclick="event.stopPropagation();">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-center text-muted">
                        No conversations yet<br>
                        <small>Click "New" to start messaging</small>
                    </li>
                    <?php endif; ?>
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
            </div>
            <div class="card-footer">
                <form id="send-form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
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
                    <div class="input-group">
                        <input type="file" id="file-input" name="attachment" class="d-none" accept="*/*">
                        <button type="button" class="btn btn-outline-secondary" id="attach-btn" title="Attach file (Max 10MB)">
                            <i class="ti ti-paperclip"></i>
                        </button>
                        <input id="message-input" name="body" class="form-control" placeholder="Type a message..." autocomplete="off">
                        <button class="btn bg-info" type="submit">Send</button>
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
                <button type="button" class="btn bg-info" id="start-chat-btn" disabled>
                    <i class="ti ti-message"></i> Start Chat
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function(){
        const list = document.getElementById('conversation-list');
        const threadHeader = document.getElementById('thread-header');
        const threadMessages = document.getElementById('thread-messages');
        const toInput = document.getElementById('to-user-id');
        const sendForm = document.getElementById('send-form');
        const messageInput = document.getElementById('message-input');
        const fileInput = document.getElementById('file-input');
        const attachBtn = document.getElementById('attach-btn');
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentName = document.getElementById('attachment-name');
        const attachmentSize = document.getElementById('attachment-size');
        const removeAttachmentBtn = document.getElementById('remove-attachment');

        let currentUserId = null;
        const ME_ID = "<?php echo e(Auth::id() ?? ''); ?>";
        let lastMessageId = 0;
        let autoRefreshInterval = null;

        function formatBytes(bytes) {
            if (!bytes) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function formatTime(dt) {
            const d = new Date(dt);
            return d.toLocaleString();
        }

        function loadConversation(userId, scrollToBottom = true) {
            fetch("<?php echo e(url('/admin/messenger/conversation/')); ?>/" + userId)
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
                        else {
                            // For received messages add a report option
                            messageHtml += '<div class="dropdown position-absolute top-0 end-0" style="margin: 4px;">';
                            messageHtml += '<button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">';
                            messageHtml += '<i class="ti ti-dots-vertical" style="font-size: 16px;"></i>';
                            messageHtml += '</button>';
                            messageHtml += '<ul class="dropdown-menu dropdown-menu-end">';
                            messageHtml += '<li><a class="dropdown-item text-warning report-btn" href="#" data-message-id="' + m.id + '"><i class="ti ti-flag me-2"></i>Report</a></li>';
                            messageHtml += '</ul>';
                            messageHtml += '</div>';
                        }
                        
                        messageHtml += '<div style="padding-right: 20px;">';
                        
                        // Only show body if it's not the default "(File attachment)" text
                        if (m.body && m.body !== '(File attachment)') {
                            messageHtml += (m.subject ? '<strong>' + escapeHtml(m.subject) + '</strong><br>' : '') + nl2br(escapeHtml(m.body));
                        }
                        
                        // Add attachment if exists
                        if (m.attachment_path && m.attachment_name) {
                            const btnClass = String(m.from) === ME_ID ? 'btn-light' : 'btn-outline-primary';
                            messageHtml += '<div class="mt-2"><a href="/admin/messages/' + m.id + '/download" class="btn btn-sm ' + btnClass + '" target="_blank"><i class="ti ti-download"></i> ' + escapeHtml(m.attachment_name);
                            if (m.attachment_size) {
                                messageHtml += ' <small>(' + formatBytes(m.attachment_size) + ')</small>';
                            }
                            messageHtml += '</a></div>';
                        }
                        
                        messageHtml += '</div><div class="small mt-1" style="opacity: 0.8;">';
                        messageHtml += '<span>' + formatTime(m.created_at) + '</span>';
                        messageHtml += '</div>';
                        
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
            
            fetch("<?php echo e(url('/admin/messenger/conversation/')); ?>/" + currentUserId)
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
                                    else {
                                        msgHtml += '<div class="dropdown position-absolute top-0 end-0" style="margin: 4px;">';
                                        msgHtml += '<button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="line-height: 1; text-decoration: none;">';
                                        msgHtml += '<i class="ti ti-dots-vertical" style="font-size: 16px;"></i>';
                                        msgHtml += '</button>';
                                        msgHtml += '<ul class="dropdown-menu dropdown-menu-end">';
                                        msgHtml += '<li><a class="dropdown-item text-warning report-btn" href="#" data-message-id="' + m.id + '"><i class="ti ti-flag me-2"></i>Report</a></li>';
                                        msgHtml += '</ul>';
                                        msgHtml += '</div>';
                                    }
                                    
                                    msgHtml += '<div style="padding-right: 20px;">';
                                    
                                    // Only show body if it's not the default "(File attachment)" text
                                    if (m.body && m.body !== '(File attachment)') {
                                        msgHtml += (m.subject ? '<strong>' + escapeHtml(m.subject) + '</strong><br>' : '') + nl2br(escapeHtml(m.body));
                                    }
                                    
                                    // Add attachment if exists
                                    if (m.attachment_path && m.attachment_name) {
                                        const btnClass = String(m.from) === ME_ID ? 'btn-light' : 'btn-outline-primary';
                                        msgHtml += '<div class="mt-2"><a href="/admin/messages/' + m.id + '/download" class="btn btn-sm ' + btnClass + '" target="_blank"><i class="ti ti-download"></i> ' + escapeHtml(m.attachment_name);
                                        if (m.attachment_size) {
                                            msgHtml += ' <small>(' + formatBytes(m.attachment_size) + ')</small>';
                                        }
                                        msgHtml += '</a></div>';
                                    }
                                    
                                    msgHtml += '</div><div class="small mt-1" style="opacity: 0.8;">';
                                    msgHtml += '<span>' + formatTime(m.created_at) + '</span>';
                                    msgHtml += '</div>';
                                    
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
            
            fetch("<?php echo e(route('admin.messages.sendConversation')); ?>", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(res => {
                if (res.success && res.message) {
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
                    
                    // Only show body if it's not the default "(File attachment)" text
                    if (m.body && m.body !== '(File attachment)') {
                        msgHtml += nl2br(escapeHtml(m.body));
                    }
                    
                    // Add attachment if exists
                    if (m.attachment_path && m.attachment_name) {
                        msgHtml += '<div class="mt-2"><a href="/admin/messages/' + m.id + '/download" class="btn btn-sm btn-light" target="_blank"><i class="ti ti-download"></i> ' + escapeHtml(m.attachment_name);
                        if (m.attachment_size) {
                            msgHtml += ' <small>(' + formatBytes(m.attachment_size) + ')</small>';
                        }
                        msgHtml += '</a></div>';
                    }
                    
                    msgHtml += '</div><div class="small mt-1" style="opacity: 0.8;">';
                    msgHtml += '<span>' + new Date(m.created_at).toLocaleString() + '</span>';
                    msgHtml += '</div>';
                    
                    div.innerHTML = msgHtml;
                    div.setAttribute('data-message-id', m.id);
                    threadMessages.appendChild(div);
                    messageInput.value = '';
                    fileInput.value = '';
                    attachmentPreview.style.display = 'none';
                    threadMessages.scrollTop = threadMessages.scrollHeight;
                    
                    // Update last message ID
                    lastMessageId = m.id;
                } else {
                    throw new Error('Invalid response format');
                }
            })
            .catch(err => { 
                console.error('Send error:', err); 
                alert('Failed to send message. Please try again.'); 
            });
        });

        // Stop auto-refresh when leaving the page
        window.addEventListener('beforeunload', stopAutoRefresh);

        // Real-time unread count polling
        let unreadCountInterval = null;

        function updateUnreadCounts() {
            fetch("<?php echo e(route('admin.api.unread-counts-by-partner')); ?>")
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
            fetch("<?php echo e(url('/admin/api/all-users')); ?>")
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

        // Report message handler (event delegation)
        threadMessages.addEventListener('click', function(e) {
            const reportBtn = e.target.closest('.report-btn');
            if (!reportBtn) return;

            e.preventDefault();

            const messageId = reportBtn.getAttribute('data-message-id');
            // Simple reporting flow: ask for reason and optional details
            let reason = prompt('Reason for reporting (spam, harassment, inappropriate, offensive, other):');
            if (!reason) return alert('Report cancelled');
            reason = reason.trim().toLowerCase();
            const allowed = ['spam','harassment','inappropriate','offensive','other'];
            if (!allowed.includes(reason)) return alert('Invalid reason. Allowed: ' + allowed.join(', '));

            const details = prompt('Optional details (max 500 chars):') || '';

            const fd = new FormData();
            fd.append('_token', document.querySelector('input[name="_token"]').value);
            fd.append('reason', reason);
            fd.append('details', details);

            fetch('/admin/messages/' + messageId + '/report', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(res.message || 'Reported. Thank you.');
                    // disable the report button to prevent duplicate reports
                    reportBtn.classList.add('disabled');
                    reportBtn.textContent = 'Reported';
                } else {
                    alert(res.error || 'Failed to report message');
                }
            })
            .catch(err => {
                console.error('Report error:', err);
                alert('Failed to report message. Please try again.');
            });
        });

        // ===== REAL-TIME LARAVEL ECHO INTEGRATION =====
        if (typeof window.Echo !== 'undefined') {
            try {
                console.log('✓ Laravel Echo available, subscribing to channel...');
                
                // Subscribe to the current user's private channel
                const channel = window.Echo.private('user.<?php echo e(auth()->id()); ?>');

                console.log('✓ Subscribed to private channel: user.<?php echo e(auth()->id()); ?>');

                // Listen for new messages
                channel.listen('.message.sent', function(data) {
                    console.log('✓ Real-time message received:', data);

                    const senderId = parseInt(data.sender_id);
                    const isCurrentConversation = currentUserId && senderId === parseInt(currentUserId);

                    // Only process if we're viewing the conversation with the sender
                    if (isCurrentConversation) {
                        // Append the message to the conversation
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'd-flex justify-content-start mb-3';
                        messageDiv.setAttribute('data-message-id', data.id);
                        
                        let attachmentHtml = '';
                        if (data.attachment_path && data.attachment_name) {
                            const fileIcon = getFileIcon(data.attachment_type);
                            const fileSize = formatFileSize(data.attachment_size);
                            attachmentHtml = `
                                <div class="mt-2">
                                    <a href="/admin/messages/${data.id}/download" class="btn btn-sm btn-outline-primary">
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
                    } else if (senderId !== <?php echo e(auth()->id()); ?>) {
                        // Message from someone we're not currently chatting with - show notification
                        showNotification('New message from ' + (data.sender_name || 'User'));
                    }

                    // Update conversation list (add sender if not exists)
                    updateConversationList(data);
                });

                console.log('✓ Echo real-time messaging is active');

            } catch (error) {
                console.error('❌ Failed to initialize Laravel Echo:', error);
                console.log('⚠️ Falling back to polling...');
            }
        } else {
                console.warn('⚠️ Laravel Echo not available. Make sure @vite directive is added and assets are compiled.');
            console.log('⚠️ Messages will use polling instead of real-time updates.');
        }

        // Helper function to update conversation list
        function updateConversationList(messageData) {
            const senderId = messageData.sender_id;
            const existingConv = document.querySelector(`.conversation-item[data-user-id="${senderId}"]`);
            
            if (!existingConv && senderId !== <?php echo e(auth()->id()); ?>) {
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

<?php if(file_exists(public_path('build/manifest.json'))): ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
<?php elseif(file_exists(public_path('js/app.js'))): ?>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php else: ?>
    <script>console.warn('Vite manifest not found and no fallback JS located. Real-time features may not work.');</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/messages/messenger.blade.php ENDPATH**/ ?>