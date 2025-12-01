

<?php $__env->startSection('breadcrumb'); ?>
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Teacher</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teacher</li>
                <li class="breadcrumb-item active" aria-current="page">Teacher List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="<?php echo e(route('admin.teachers.create')); ?>" class="btn bg-info d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Teacher</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Emp #</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subjects Handled</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-monospace"><?php echo e($teacher->employee_number); ?></td>
                            <td><?php echo e($teacher->last_name); ?>, <?php echo e($teacher->first_name); ?> <?php echo e($teacher->middle_name); ?></td>
                            <td><?php echo e($teacher->email); ?></td>
                            <td>
                                <?php if($teacher->subjects->isNotEmpty()): ?>
                                    <?php $__currentLoopData = $teacher->subjects->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-info text-dark me-1"><?php echo e($subject->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($teacher->subjects->count() > 3): ?>
                                        <span class="badge bg-secondary">+<?php echo e($teacher->subjects->count() - 3); ?> more</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small">No subjects</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-<?php echo e($teacher->status === 'active' ? 'success' : 'secondary'); ?>"><?php echo e(ucfirst($teacher->status)); ?></span></td>
                            <td>
                                <div class="action-icon d-inline-flex align-items-center">
                                    <a href="<?php echo e(route('admin.teachers.show', $teacher)); ?>" class="me-2" title="View"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.teachers.edit', $teacher)); ?>" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                    <a href="<?php echo e(route('admin.teachers.assignments', $teacher)); ?>" class="me-2" title="Assignment"><i class="ti ti-clipboard-text"></i></a>
                                    <a href="javascript:void(0);" class="text-danger" title="Delete" onclick="openDeleteModal('<?php echo e(route('admin.teachers.destroy', $teacher)); ?>', '<?php echo e($teacher->first_name); ?> <?php echo e($teacher->last_name); ?>')"><i class="ti ti-trash"></i></a>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-4">
                <h5 class="mb-3" id="deleteModalLabel">Are you sure you want to delete this teacher? This will also remove their login account.</h5>
                <form id="deleteForm" method="POST" action="">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-warning px-4">OK</button>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openDeleteModal(deleteUrl, teacherName) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = deleteUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/teachers/index.blade.php ENDPATH**/ ?>