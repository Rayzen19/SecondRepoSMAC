

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Academic Years</h4>
        <div class="text-muted small">Your enrollment history and current status</div>
    </div>
    <div></div>
</div>

<?php if($rows->isEmpty()): ?>
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-school"></i></div>
            <h5 class="mb-2">No enrollment records</h5>
            <p class="text-muted mb-0">When you are enrolled in an academic year, it will appear here.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Strand</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($r['display_name'] ?? $r['year'] ?? '—'); ?></td>
                                <td><?php echo e($r['semester'] ?? '—'); ?></td>
                                <td><?php echo e($r['strand'] ?? '—'); ?></td>
                                <td><?php echo e($r['section'] ?? '—'); ?></td>
                                <td>
                                    <?php $st = strtolower($r['status'] ?? ''); ?>
                                    <?php switch($st):
                                        case ('active'): ?>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php break; ?>
                                        <?php case ('completed'): ?>
                                            <span class="badge bg-secondary-subtle text-secondary">Completed</span>
                                            <?php break; ?>
                                        <?php case ('dropped'): ?>
                                            <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-light text-muted border">N/A</span>
                                    <?php endswitch; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/academic_years/index.blade.php ENDPATH**/ ?>