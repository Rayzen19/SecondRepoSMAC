
<?php use Illuminate\Support\Str; ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Student Dashboard</h4>
                        <p class="text-muted mb-0">Welcome back, <?php echo e($student->first_name ?? $student->name ?? 'Student'); ?>.</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Academic Year: <?php echo e($student->academic_year ?? ($enrollment->academicYear->year_name ?? 'N/A')); ?></small>
                        <div><?php echo e(isset($enrollment->section) ? 'Section: ' . ($enrollment->section->section_name ?? 'N/A') : ''); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Announcements</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($announcements) && $announcements->count()): ?>
                        <ul class="list-unstyled">
                            <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="mb-3">
                                    <strong><?php echo e($a->title); ?></strong>
                                    <div class="text-muted small"><?php echo e(optional($a->published_at)->format('M d, Y') ?? $a->created_at->format('M d, Y')); ?></div>
                                    <div class="mt-1"><?php echo e(Str::limit(strip_tags($a->content ?? $a->description), 140)); ?></div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No announcements found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enrolled Subjects</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($subjects) && $subjects->count()): ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-end">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($s->subject_name ?? ($s->subject->name ?? 'N/A')); ?></td>
                                        <td class="text-end"><?php echo e(isset($s->f_grade) ? number_format($s->f_grade, 2) : '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted mb-0">No enrolled subjects found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/dashboard.blade.php ENDPATH**/ ?>