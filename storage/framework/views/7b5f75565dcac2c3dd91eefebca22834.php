

<?php $__env->startSection('title', 'Pre-Enrollment Details'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Pre-Enrollment Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.pre-enrollments.index')); ?>">Pre-Enrollment Submissions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-sm-12">
        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pre-Enrollment Information</h5>
                <div>
                    <?php if($preEnrollment->status == 'pending'): ?>
                        <span class="badge bg-warning">Pending Review</span>
                    <?php elseif($preEnrollment->status == 'approved'): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php elseif($preEnrollment->status == 'rejected'): ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php elseif($preEnrollment->status == 'enrolled'): ?>
                        <span class="badge bg-info">Enrolled</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Student Information -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Student Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Student Number:</label>
                            <p><?php echo e($preEnrollment->student->student_number); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name:</label>
                            <p><?php echo e($preEnrollment->student->first_name); ?> <?php echo e($preEnrollment->student->middle_name ?? ''); ?> <?php echo e($preEnrollment->student->last_name); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p><?php echo e($preEnrollment->student->email); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Grade Level:</label>
                            <p>Grade <?php echo e($preEnrollment->student->grade_level ?? 'N/A'); ?></p>
                        </div>
                    </div>

                    <!-- Pre-Enrollment Details -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Pre-Enrollment Details</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Grade Level:</label>
                            <p>Grade <?php echo e($preEnrollment->grade_level); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Strand:</label>
                            <p><?php echo e($preEnrollment->strand->name); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preferred Section:</label>
                            <p><?php echo e($preEnrollment->section->name); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Submitted At:</label>
                            <p><?php echo e($preEnrollment->submitted_at->format('F d, Y h:i A')); ?></p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Academic Year Information -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Current Academic Year</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Year:</label>
                            <p><?php echo e($preEnrollment->currentAcademicYear->year); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Target Academic Year</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Year:</label>
                            <p><?php echo e($preEnrollment->targetAcademicYear->year ?? 'Not Set'); ?></p>
                        </div>
                    </div>
                </div>

                <?php if($preEnrollment->status != 'pending'): ?>
                    <hr class="my-4">
                    
                    <!-- Processing Information -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-3 text-primary">Processing Information</h6>
                        </div>
                        <?php if($preEnrollment->processed_at): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Processed At:</label>
                                    <p><?php echo e($preEnrollment->processed_at->format('F d, Y h:i A')); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($preEnrollment->processedBy): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Processed By:</label>
                                    <p><?php echo e($preEnrollment->processedBy->name ?? 'System'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($preEnrollment->remarks): ?>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Remarks:</label>
                                    <p class="text-muted"><?php echo e($preEnrollment->remarks); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="<?php echo e(route('admin.pre-enrollments.index')); ?>" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>

                    <?php if($preEnrollment->status == 'pending'): ?>
                        <form action="<?php echo e(route('admin.pre-enrollments.approve', $preEnrollment)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this pre-enrollment?')">
                                <i class="ti ti-check me-1"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ti ti-x me-1"></i> Reject
                        </button>
                    <?php endif; ?>

                    <?php if($preEnrollment->status == 'approved'): ?>
                        <form action="<?php echo e(route('admin.pre-enrollments.process', $preEnrollment)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-info" onclick="return confirm('This will create an actual enrollment for the student. Continue?')">
                                <i class="ti ti-circle-check me-1"></i> Process Enrollment
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Pre-Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.pre-enrollments.reject', $preEnrollment)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="4" required placeholder="Enter reason for rejecting this pre-enrollment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Pre-Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/pre-enrollments/show.blade.php ENDPATH**/ ?>