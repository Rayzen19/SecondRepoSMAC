

<?php $__env->startSection('breadcrumb'); ?>
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Announcements</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Announcements</li>
                <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
        <div class="mb-2">
            <a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn bg-info d-flex align-items-center">
                <i class="ti ti-circle-plus me-2"></i>Add Announcement
            </a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Announcements</h5>
        </div>
        <div class="card-body p-0">
            <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="custom-datatable-filter table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published Date</th>
                            <th>Expires Date</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($announcement->title); ?></strong>
                                <?php if($announcement->hasImage()): ?>
                                <i class="ti ti-photo text-info ms-1" title="Has image"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($announcement->is_active): ?>
                                    <?php if($announcement->isExpired()): ?>
                                        <span class="badge bg-secondary">Expired</span>
                                    <?php elseif($announcement->isPublished()): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Scheduled</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($announcement->published_at): ?>
                                    <?php echo e($announcement->published_at->format('M d, Y h:i A')); ?>

                                <?php else: ?>
                                    <span class="text-muted">Immediately</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($announcement->expires_at): ?>
                                    <?php echo e($announcement->expires_at->format('M d, Y h:i A')); ?>

                                <?php else: ?>
                                    <span class="text-muted">No expiration</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($announcement->creator->name ?? 'N/A'); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('admin.announcements.edit', $announcement)); ?>" class="btn btn-sm bg-info">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="<?php echo e(route('admin.announcements.destroy', $announcement)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="ti ti-speakerphone text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0">No announcements found.</p>
                                <a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn bg-info btn-sm mt-2">
                                    <i class="ti ti-plus me-1"></i>Create your first announcement
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($announcements->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <?php echo e($announcements->links()); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/announcements/index.blade.php ENDPATH**/ ?>