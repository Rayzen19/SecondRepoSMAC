

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3" style="color:#313131">
                <i class="ti ti-message-2 me-2"></i>SMS Management
            </h2>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-x me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- SMS Balance Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="ti ti-wallet me-2"></i>SMS Balance</h5>
                    <div class="text-center py-4">
                        <h2 class="mb-0" id="smsBalance"><i class="ti ti-loader"></i></h2>
                        <p class="text-muted">Credits Available</p>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="checkBalance()">
                        <i class="ti ti-refresh me-2"></i>Refresh Balance
                    </button>
                </div>
            </div>
        </div>

        <!-- Test SMS Card -->
        <div class="col-lg-8 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="ti ti-send me-2"></i>Test SMS</h5>
                    <form action="<?php echo e(route('admin.sms.test')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" 
                                   placeholder="09171234567" value="09762129986" required>
                            <small class="text-muted">Format: 09XXXXXXXXX</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="3" 
                                      maxlength="160" required placeholder="Enter test message here...">Test message from SMAC School Management System</textarea>
                            <small class="text-muted"><span id="charCount">0</span>/160 characters</small>
                        </div>
                        <button type="submit" class="btn bg-info">
                            <i class="ti ti-send me-2"></i>Send Test SMS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk SMS Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="ti ti-users me-2"></i>Send Bulk SMS</h5>
                    <form action="<?php echo e(route('admin.sms.send.bulk')); ?>" method="POST" onsubmit="return confirmBulkSend()">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recipient Type</label>
                                <select name="recipient_type" class="form-select" id="recipientType" required>
                                    <option value="">Select Recipients</option>
                                    <option value="teachers">All Teachers</option>
                                    <option value="students">All Students</option>
                                    <option value="guardians">All Guardians</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="4" 
                                          maxlength="160" id="bulkMessage" required 
                                          placeholder="Enter your message here..."></textarea>
                                <small class="text-muted"><span id="bulkCharCount">0</span>/160 characters</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-send me-2"></i>Send Bulk SMS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter for test SMS
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Character counter for bulk SMS
document.getElementById('bulkMessage').addEventListener('input', function() {
    document.getElementById('bulkCharCount').textContent = this.value.length;
});

// Check SMS balance
function checkBalance() {
    const balanceEl = document.getElementById('smsBalance');
    const statusEl = balanceEl.parentElement.querySelector('p');
    balanceEl.innerHTML = '<i class="ti ti-loader"></i>';
    statusEl.className = 'text-muted';
    statusEl.textContent = 'Loading...';
    
    fetch('<?php echo e(route('admin.sms.balance')); ?>')
        .then(response => {
            return response.json().then(data => ({
                status: response.status,
                data: data
            }));
        })
        .then(({status, data}) => {
            console.log('Balance response:', data);
            
            if (data.success && data.balance) {
                // Try different possible properties from Semaphore API
                const balance = data.balance.credit_balance || 
                               data.balance.credits || 
                               data.balance.balance || 
                               data.balance.account_credit ||
                               'N/A';
                balanceEl.innerHTML = `<span class="text-success">${balance}</span>`;
                statusEl.textContent = 'Credits Available';
                statusEl.className = 'text-muted';
            } else {
                // Handle different error types based on HTTP status or status_code
                const errorCode = data.status_code || status;
                
                if (errorCode === 429) {
                    balanceEl.innerHTML = '<small class="text-warning"><i class="ti ti-clock me-1"></i>Rate Limited</small>';
                    statusEl.textContent = 'Too many requests. Wait 30 seconds and click Refresh.';
                    statusEl.className = 'text-warning small';
                } else if (errorCode === 401 || errorCode === 403) {
                    balanceEl.innerHTML = '<small class="text-danger"><i class="ti ti-alert-circle me-1"></i>Invalid API Key</small>';
                    statusEl.textContent = 'Check SEMAPHORE_API_KEY in .env file';
                    statusEl.className = 'text-danger small';
                } else {
                    balanceEl.innerHTML = '<small class="text-danger"><i class="ti ti-x me-1"></i>Error</small>';
                    
                    // Parse error message if it's an object
                    let errorMsg = data.message || 'Failed to fetch balance';
                    if (typeof errorMsg === 'object') {
                        errorMsg = JSON.stringify(errorMsg);
                    }
                    
                    statusEl.textContent = errorMsg;
                    statusEl.className = 'text-danger small';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching balance:', error);
            balanceEl.innerHTML = '<small class="text-danger"><i class="ti ti-wifi-off me-1"></i>Error</small>';
            statusEl.textContent = 'Connection failed';
            statusEl.className = 'text-danger small';
        });
}

// Load balance on page load
checkBalance();

// Confirm bulk send
function confirmBulkSend() {
    const type = document.getElementById('recipientType').value;
    return confirm(`Are you sure you want to send SMS to all ${type}? This action cannot be undone.`);
}

// Use template
function useTemplate(text) {
    document.getElementById('bulkMessage').value = text;
    document.getElementById('bulkCharCount').textContent = text.length;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/sms/index.blade.php ENDPATH**/ ?>