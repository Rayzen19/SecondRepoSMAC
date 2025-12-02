

<?php $__env->startSection('content'); ?>
<style>
    .card-hover { transition: transform .15s ease, box-shadow .15s ease; cursor: pointer; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
    .card-hover .stretched-link { z-index: 1; }
    .card-hover .card-body { position: relative; }
</style>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">My Classes</h4>
        <div class="text-muted small">Academic Year: <?php echo e($activeYear?->display_name ?? 'No active year'); ?></div>
    </div>
</div>

<?php if($subjects->isEmpty()): ?>
    <div class="card shadow-none border-0 bg-transparent">
        <div class="card-body text-center py-5">
            <h5 class="mb-2">No subjects assigned yet</h5>
            <p class="text-muted mb-0">When an administrator assigns you to subjects for the active academic year, they will appear here.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 card-hover position-relative">
                    <div class="card-body">
                        <a href="<?php echo e(route('teacher.class-records.show', $s['id'])); ?>" class="stretched-link" aria-label="Open <?php echo e($s['subject_name']); ?>"></a>
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="fw-semibold text-uppercase text-muted small"><?php echo e($s['subject_code'] ?? '—'); ?></div>
                                <h5 class="mb-2"><?php echo e($s['subject_name']); ?></h5>
                            </div>
                            <span class="badge bg-primary-subtle text-primary"><?php echo e($activeYear?->name); ?></span>
                        </div>
                        <div class="mt-3 d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-muted border">Strand</span>
                                <?php if(!empty($s['strand_name'])): ?>
                                    <span class="badge rounded-pill bg-info-subtle text-info"><?php echo e($s['strand_name']); ?></span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary">N/A</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-muted border">Section</span>
                                <?php if(!empty($s['section_name'])): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success"><?php echo e($s['section_name']); ?></span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary">N/A</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-muted border">Adviser</span>
                                <?php if(!empty($s['adviser_name'])): ?>
                                    <span class="badge rounded-pill bg-primary-subtle text-primary"><?php echo e($s['adviser_name']); ?></span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary">N/A</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-muted border">Students</span>
                                <span class="badge rounded-pill bg-dark-subtle text-dark">Total: <?php echo e($s['counts']['total'] ?? 0); ?></span>
                                <span class="badge rounded-pill bg-success-subtle text-success">Male: <?php echo e($s['counts']['male'] ?? 0); ?></span>
                                <span class="badge rounded-pill bg-info-subtle text-info">Female: <?php echo e($s['counts']['female'] ?? 0); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-muted border">Status</span>
                                <span class="badge rounded-pill bg-primary-subtle text-primary">Active: <?php echo e($s['counts']['active'] ?? 0); ?></span>
                                <span class="badge rounded-pill bg-primary-subtle text-primary">Graduated: <?php echo e($s['counts']['graduated'] ?? 0); ?></span>
                                <span class="badge rounded-pill bg-warning-subtle text-warning">Dropped: <?php echo e($s['counts']['dropped'] ?? 0); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/subjects/index.blade.php ENDPATH**/ ?>