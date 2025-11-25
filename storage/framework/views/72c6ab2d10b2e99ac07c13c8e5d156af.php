

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h3 class="page-title">Message Reports</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
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
                        <h3 class="mb-0"><?php echo e($stats['total']); ?></h3>
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
                        <h3 class="mb-0 text-warning"><?php echo e($stats['pending']); ?></h3>
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
                        <h3 class="mb-0 text-info"><?php echo e($stats['reviewed']); ?></h3>
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
                        <h3 class="mb-0 text-success"><?php echo e($stats['action_taken']); ?></h3>
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
                <a class="nav-link <?php echo e($status == 'all' ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index', ['status' => 'all'])); ?>">
                    All (<?php echo e($stats['total']); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e($status == 'pending' ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index', ['status' => 'pending'])); ?>">
                    Pending (<?php echo e($stats['pending']); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e($status == 'reviewed' ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index', ['status' => 'reviewed'])); ?>">
                    Reviewed (<?php echo e($stats['reviewed']); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e($status == 'dismissed' ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index', ['status' => 'dismissed'])); ?>">
                    Dismissed (<?php echo e($stats['dismissed']); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e($status == 'action_taken' ? 'active' : ''); ?>" href="<?php echo e(route('admin.message-reports.index', ['status' => 'action_taken'])); ?>">
                    Action Taken (<?php echo e($stats['action_taken']); ?>)
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php if($reports->isEmpty()): ?>
            <div class="text-center py-5">
                <i class="ti ti-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="text-muted mt-3">No reports found</p>
            </div>
        <?php else: ?>
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
                        <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#<?php echo e($report->id); ?></td>
                            <td>
                                <div>
                                    <strong><?php echo e($report->reporter->name); ?></strong>
                                    <br><small class="text-muted"><?php echo e($report->reporter->email); ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if($report->message && $report->message->sender): ?>
                                    <div>
                                        <strong><?php echo e($report->message->sender->name); ?></strong>
                                        <br><small class="text-muted"><?php echo e(Str::limit($report->message->body, 50)); ?></small>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Message deleted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($report->reason == 'spam' ? 'info' : 
                                    ($report->reason == 'harassment' ? 'danger' : 
                                    ($report->reason == 'inappropriate' ? 'warning' : 'secondary'))); ?>">
                                    <?php echo e(ucfirst($report->reason)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($report->status == 'pending' ? 'warning' : 
                                    ($report->status == 'reviewed' ? 'info' : 
                                    ($report->status == 'action_taken' ? 'success' : 'secondary'))); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $report->status))); ?>

                                </span>
                            </td>
                            <td><?php echo e($report->created_at->format('M d, Y h:i A')); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.message-reports.show', $report)); ?>" class="btn btn-sm bg-info">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                    <?php if($report->isPending()): ?>
                                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus(<?php echo e($report->id); ?>, 'reviewed')">
                                            <i class="ti ti-check"></i> Review
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="dismissReport(<?php echo e($report->id); ?>)">
                                            <i class="ti ti-x"></i> Dismiss
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($reports->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updateStatus(reportId, status) {
    if (!confirm('Are you sure you want to mark this report as ' + status + '?')) {
        return;
    }

    fetch(`/admin/message-reports/${reportId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/message-reports/index.blade.php ENDPATH**/ ?>