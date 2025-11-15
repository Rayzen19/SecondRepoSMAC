

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Co-Admins</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Co-Admin List</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex justify-content-end">
                <a href="<?php echo e(route('admin.co-admins.create')); ?>" class="btn btn-primary" style="background-color: #ff6b35; border-color: #ff6b35;">
                    <i class="fas fa-plus-circle me-1"></i> Add Co-Admin
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table comman-shadow">
            <div class="card-body">
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Card-based layout -->
                <?php $__empty_1 = true; $__currentLoopData = $coAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coAdmin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card mb-3 shadow-sm border">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <!-- Avatar and Name Column -->
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo e(asset('assets/img/profiles/avatar-02.jpg')); ?>" 
                                             alt="<?php echo e($coAdmin->name); ?>" 
                                             class="rounded-circle me-3" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0"><?php echo e($coAdmin->name); ?></h6>
                                            <small class="text-muted"><?php echo e($coAdmin->email); ?></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Column -->
                                <div class="col-md-3">
                                    <small class="text-muted d-block">User ID: #<?php echo e($coAdmin->id); ?></small>
                                    <small class="text-muted">Created: <?php echo e($coAdmin->created_at->format('M d, Y')); ?></small>
                                </div>

                                <!-- Status Column -->
                                <div class="col-md-2">
                                    <span class="badge bg-success">Active</span>
                                </div>

                                <!-- Action Buttons Column -->
                                <div class="col-md-3 text-end">
                                    <a href="<?php echo e(route('admin.co-admins.show', $coAdmin)); ?>" 
                                       class="btn btn-sm me-1"
                                       style="background-color: #28a745; color: white; border-radius: 20px; padding: 5px 15px;"
                                       title="View Details">
                                        <i class="feather-eye me-1"></i> View
                                    </a>
                                    <a href="<?php echo e(route('admin.co-admins.edit', $coAdmin)); ?>" 
                                       class="btn btn-sm me-1"
                                       style="background-color: #ffc107; color: white; border-radius: 20px; padding: 5px 15px;"
                                       title="Edit">
                                        <i class="feather-edit me-1"></i> Edit
                                    </a>
                                    <form action="<?php echo e(route('admin.co-admins.destroy', $coAdmin)); ?>" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this co-admin?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="btn btn-sm"
                                                style="background-color: #dc3545; color: white; border-radius: 20px; padding: 5px 15px; border: none;"
                                                title="Delete">
                                            <i class="feather-trash-2 me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No co-admins found. Click the + button to create one.
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if($coAdmins->hasPages()): ?>
                    <div class="mt-4">
                        <?php echo e($coAdmins->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/co-admins/index.blade.php ENDPATH**/ ?>