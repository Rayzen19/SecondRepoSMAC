

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Class Records</h4>
        <div class="text-muted small">Academic Year: <?php echo e($activeYear?->display_name ?? '—'); ?></div>
    </div>
</div>

<?php if($rows->isEmpty()): ?>
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="ti ti-notebook"></i></div>
            <h5 class="mb-2">No class records yet</h5>
            <p class="text-muted mb-0">Your assigned subjects will appear here once linked to a section in an academic year.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Academic Details</th>
                        <th>Subject</th>
                        <th>Strand & Section</th>
                        <th>Adviser</th>
                        <th>Students No.</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($r['year'] ?? '—'); ?> (<?php echo e($r['semester'] ?? '—'); ?> Semester)</td>
                            <td>
                                <?php if(!empty($r['subject_name']) || !empty($r['subject_code'])): ?>
                                    <div class="small text-muted text-uppercase"><?php echo e($r['subject_code'] ?? ''); ?></div>
                                    <div><?php echo e($r['subject_name'] ?? '—'); ?></div>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($r['strand'] ?? '—'); ?> <?php echo e($r['section'] ?? '—'); ?></td>
                            <td><?php echo e($r['adviser'] ?? '—'); ?></td>
                            <td><?php echo e($r['students_count'] ?? '0'); ?></td>
                            <td>
                                <?php $status = strtolower($r['ay_status'] ?? ''); ?>
                                <?php switch($status):
                                    case ('pending'): ?>
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        <?php break; ?>
                                    <?php case ('ongoing enrollment'): ?>
                                        <span class="badge bg-info-subtle text-info">Ongoing Enrollment</span>
                                        <?php break; ?>
                                    <?php case ('ongoing school year'): ?>
                                        <span class="badge bg-success-subtle text-success">Ongoing School Year</span>
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Completed</span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge bg-light text-muted border">N/A</span>
                                <?php endswitch; ?>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm bg-info" href="<?php echo e(route('teacher.class-records.show', ['assignment' => $r['id']])); ?>">
                                    <i class="ti ti-eye me-1"></i> View
                                </a>
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

<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/class_records/index.blade.php ENDPATH**/ ?>