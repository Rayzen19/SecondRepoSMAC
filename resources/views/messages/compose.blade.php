@extends('admin.components.template')

@section('content')
<style>
    .compose-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .compose-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    .compose-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
    }
    .compose-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .compose-body {
        padding: 24px;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .recipient-selector {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        min-height: 120px;
        max-height: 300px;
        overflow-y: auto;
        background: #f8f9fa;
    }
    .recipient-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        margin-bottom: 8px;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .recipient-item:hover {
        border-color: #667eea;
        transform: translateX(4px);
    }
    .recipient-item.selected {
        border-color: #667eea;
        background: #e7f3ff;
    }
    .recipient-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-right: 12px;
    }
    .recipient-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 12px;
        font-size: 16px;
    }
    .recipient-info {
        flex: 1;
    }
    .recipient-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }
    .recipient-email {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .search-box {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        padding-bottom: 12px;
    }
    .search-box input {
        border-radius: 8px;
        padding-left: 40px;
    }
    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .selected-count {
        display: inline-block;
        padding: 4px 12px;
        background: #667eea;
        color: white;
        border-radius: 20px;
        font-size: 0.875rem;
        margin-left: 8px;
    }
    .btn-send {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 36px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.2s;
    }
    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .btn-cancel {
        padding: 12px 36px;
        border-radius: 8px;
        font-weight: 600;
    }
    .character-count {
        text-align: right;
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 4px;
    }
    .select-all-btn {
        font-size: 0.875rem;
        padding: 6px 12px;
        border-radius: 6px;
    }
</style>

<div class="compose-container">
    <div class="card compose-card">
        <div class="compose-header">
            <h5><i class="ti ti-mail me-2"></i>Compose Message</h5>
        </div>
        <div class="compose-body">
            <form action="{{ route('admin.messages.send') }}" method="POST" id="compose-form">
                @csrf
                
                <!-- Recipients Section -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">
                            To <span class="selected-count" id="selected-count">0 selected</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-primary select-all-btn" id="select-all-btn">
                            <i class="ti ti-checkbox me-1"></i>Select All
                        </button>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="search-box position-relative mb-3">
                        <i class="ti ti-search search-icon"></i>
                        <input type="text" id="recipient-search" class="form-control" placeholder="Search users by name or email...">
                    </div>
                    
                    <!-- Recipients List -->
                    <div class="recipient-selector" id="recipient-list">
                        @foreach($users as $u)
                        <label class="recipient-item" data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}">
                            <input type="checkbox" name="recipients[]" value="{{ $u->id }}" class="recipient-checkbox">
                            <div class="recipient-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                            <div class="recipient-info">
                                <div class="recipient-name">{{ $u->name }}</div>
                                <div class="recipient-email">{{ $u->email }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <small class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>You can select multiple recipients
                    </small>
                </div>

                <!-- Subject -->
                <div class="mb-4">
                    <label class="form-label">Subject <span class="text-muted">(Optional)</span></label>
                    <input type="text" name="subject" class="form-control" placeholder="Enter message subject..." id="subject-input">
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <label class="form-label">Message</label>
                    <textarea name="body" class="form-control" rows="8" required placeholder="Type your message here..." id="message-body"></textarea>
                    <div class="character-count">
                        <span id="char-count">0</span> characters
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-send">
                        <i class="ti ti-send me-2"></i>Send Message
                    </button>
                    <a href="{{ route('admin.messages.messenger') }}" class="btn btn-outline-secondary btn-cancel">
                        <i class="ti ti-x me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientCheckboxes = document.querySelectorAll('.recipient-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const recipientSearch = document.getElementById('recipient-search');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const selectAllBtn = document.getElementById('select-all-btn');
    const messageBody = document.getElementById('message-body');
    const charCount = document.getElementById('char-count');
    const composeForm = document.getElementById('compose-form');

    // Update selected count
    function updateSelectedCount() {
        const count = document.querySelectorAll('.recipient-checkbox:checked').length;
        selectedCount.textContent = count + ' selected';
        selectedCount.style.display = count > 0 ? 'inline-block' : 'none';
    }

    // Handle checkbox change
    recipientCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('.recipient-item');
            if (this.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
            updateSelectedCount();
        });
    });

    // Handle label click (toggle checkbox)
    recipientItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('.recipient-checkbox');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // Search functionality
    recipientSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        recipientItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const email = item.getAttribute('data-email');
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Select All / Deselect All
    selectAllBtn.addEventListener('click', function() {
        const visibleCheckboxes = Array.from(recipientCheckboxes).filter(cb => {
            return cb.closest('.recipient-item').style.display !== 'none';
        });
        
        const allChecked = visibleCheckboxes.every(cb => cb.checked);
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
            checkbox.dispatchEvent(new Event('change'));
        });
        
        this.innerHTML = allChecked 
            ? '<i class="ti ti-checkbox me-1"></i>Select All'
            : '<i class="ti ti-square-check me-1"></i>Deselect All';
    });

    // Character count
    messageBody.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Form validation
    composeForm.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.recipient-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one recipient');
            return false;
        }
        
        const message = messageBody.value.trim();
        if (message.length === 0) {
            e.preventDefault();
            alert('Please enter a message');
            messageBody.focus();
            return false;
        }
    });

    // Initialize
    updateSelectedCount();
    selectedCount.style.display = 'none';
});
</script>
@endsection
