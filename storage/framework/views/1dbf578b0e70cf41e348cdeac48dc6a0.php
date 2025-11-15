

<?php $__env->startSection('breadcrumb'); ?>
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-user-check me-2"></i>
                    Enrollment Details
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.student-enrollments.index')); ?>">Enrollments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="<?php echo e(route('admin.student-enrollments.edit', $studentEnrollment)); ?>" class="btn btn-warning d-flex align-items-center"><i class="ti ti-edit me-2"></i>Edit</a>
        </div>
        <div class="mb-2 ms-2">
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary d-flex align-items-center"><i class="ti ti-arrow-back-up me-2"></i>Back</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-5">
            <dl class="row mb-0">
                <dt class="col-sm-3">Registration #</dt>
                <dd class="col-sm-9"><?php echo e($studentEnrollment->registration_number); ?></dd>

                <dt class="col-sm-3">Student</dt>
                <dd class="col-sm-9"><?php echo e($studentEnrollment->student?->student_number); ?> — <?php echo e($studentEnrollment->student?->name); ?></dd>

                <dt class="col-sm-3">Academic Year</dt>
                <dd class="col-sm-9"><?php echo e($studentEnrollment->academicYear?->display_name); ?></dd>

                <dt class="col-sm-3">Strand</dt>
                <dd class="col-sm-9"><?php echo e($studentEnrollment->strand?->code); ?> — <?php echo e($studentEnrollment->strand?->name); ?></dd>

                <dt class="col-sm-3">Section</dt>
                <dd class="col-sm-9"><?php echo e($studentEnrollment->academicYearStrandSection?->section?->grade); ?> — <?php echo e($studentEnrollment->academicYearStrandSection?->section?->name); ?></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><?php echo e(ucfirst($studentEnrollment->status)); ?></dd>
            </dl>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body p-5">
            <h5 class="mb-3">Subjects</h5>
            <?php $subs = $studentEnrollment->subjectEnrollments ?? collect(); ?>
            <?php if($subs->isNotEmpty()): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>1st Qtr</th>
                                <th>2nd Qtr</th>
                                <th>Average</th>
                                <th>Final</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $se): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $ays = $se->academicYearStrandSubject; ?>
                                <tr>
                                    <td><?php echo e($ays?->subject?->code); ?> — <?php echo e($ays?->subject?->name); ?></td>
                                    <td><?php echo e($ays?->teacher?->last_name); ?>, <?php echo e($ays?->teacher?->first_name); ?></td>
                                    <td><?php echo e($se->fq_grade); ?></td>
                                    <td><?php echo e($se->sq_grade); ?></td>
                                    <td><?php echo e($se->a_grade); ?></td>
                                    <td><?php echo e($se->f_grade); ?></td>
                                    <td><?php echo e($se->remarks); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="mb-0">No subject enrollments yet. Use the Sync action from the Academic Year page to create them.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/student_enrollments/show.blade.php ENDPATH**/ ?>