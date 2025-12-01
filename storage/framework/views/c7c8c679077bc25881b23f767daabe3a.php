

<?php $__env->startSection('breadcrumb'); ?>
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Guardian</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Guardian</li>
                <li class="breadcrumb-item active" aria-current="page">Guardian List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="<?php echo e(route('admin.guardians.create')); ?>" class="btn bg-info d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Guardian</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-circle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Guardian Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $guardians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guardian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-monospace"><?php echo e($guardian->guardian_number); ?></td>
                            <td><?php echo e($guardian->last_name); ?>, <?php echo e($guardian->first_name); ?> <?php echo e($guardian->middle_name); ?></td>
                            <td><?php echo e($guardian->email); ?></td>
                            <td><?php echo e($guardian->mobile_number); ?></td>
                            <td><span class="badge bg-<?php echo e($guardian->status === 'active' ? 'success' : 'secondary'); ?>"><?php echo e(ucfirst($guardian->status)); ?></span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="<?php echo e(route('admin.guardians.show', $guardian)); ?>" class="me-2" title="View"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.guardians.edit', $guardian)); ?>" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                    <form action="<?php echo e(route('admin.guardians.destroy', $guardian)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this guardian? This action cannot be undone.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-link p-0 text-danger" title="Delete" style="border: none; background: none;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/guardians/index.blade.php ENDPATH**/ ?>