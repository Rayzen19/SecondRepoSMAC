

<?php $__env->startSection('title', 'Pre-Enrollment Submissions'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Pre-Enrollment Submissions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pre-Enrollment Submissions</li>
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="dash-widget-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2"><?php echo e($stats['total']); ?></h4>
                                <p class="mb-0">Total Submissions</p>
                            </div>
                            <div class="avatar avatar-md">
                                <div class="avatar-title bg-primary-light rounded-circle">
                                    <i class="ti ti-users text-primary fs-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="dash-widget-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2"><?php echo e($stats['pending']); ?></h4>
                                <p class="mb-0">Pending Review</p>
                            </div>
                            <div class="avatar avatar-md">
                                <div class="avatar-title bg-warning-light rounded-circle">
                                    <i class="ti ti-clock text-warning fs-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="dash-widget-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2"><?php echo e($stats['approved']); ?></h4>
                                <p class="mb-0">Approved</p>
                            </div>
                            <div class="avatar avatar-md">
                                <div class="avatar-title bg-success-light rounded-circle">
                                    <i class="ti ti-check-circle text-success fs-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="dash-widget-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2"><?php echo e($stats['enrolled']); ?></h4>
                                <p class="mb-0">Enrolled</p>
                            </div>
                            <div class="avatar avatar-md">
                                <div class="avatar-title bg-info-light rounded-circle">
                                    <i class="ti ti-circle-check text-info fs-24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Table Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pre-Enrollment Submissions</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="<?php echo e(route('admin.pre-enrollments.index')); ?>" id="filterForm" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Student name or number..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                <option value="enrolled" <?php echo e(request('status') == 'enrolled' ? 'selected' : ''); ?>>Enrolled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select">
                                <option value="">All Grades</option>
                                <option value="11" <?php echo e(request('grade_level') == '11' ? 'selected' : ''); ?>>Grade 11</option>
                                <option value="12" <?php echo e(request('grade_level') == '12' ? 'selected' : ''); ?>>Grade 12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Strand</label>
                            <select name="strand_id" class="form-select">
                                <option value="">All Strands</option>
                                <?php $__currentLoopData = $strands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $strand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($strand->id); ?>" <?php echo e(request('strand_id') == $strand->id ? 'selected' : ''); ?>>
                                        <?php echo e($strand->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                            <a href="<?php echo e(route('admin.pre-enrollments.index')); ?>" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Current Grade</th>
                                <th>Target Grade</th>
                                <th>Strand</th>
                                <th>Section</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $preEnrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($enrollment->student->student_number); ?></td>
                                    <td><?php echo e($enrollment->student->first_name); ?> <?php echo e($enrollment->student->last_name); ?></td>
                                    <td>Grade <?php echo e($enrollment->student->grade_level ?? 'N/A'); ?></td>
                                    <td>Grade <?php echo e($enrollment->grade_level); ?></td>
                                    <td><?php echo e($enrollment->strand->name); ?></td>
                                    <td><?php echo e($enrollment->section->name); ?></td>
                                    <td><?php echo e($enrollment->submitted_at->format('M d, Y h:i A')); ?></td>
                                    <td>
                                        <?php if($enrollment->status == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif($enrollment->status == 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif($enrollment->status == 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php elseif($enrollment->status == 'enrolled'): ?>
                                            <span class="badge bg-info">Enrolled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo e(route('admin.pre-enrollments.show', $enrollment)); ?>">
                                                        <i class="ti ti-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                <?php if($enrollment->status == 'pending'): ?>
                                                    <li>
                                                        <form action="<?php echo e(route('admin.pre-enrollments.approve', $enrollment)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to approve this pre-enrollment?')">
                                                                <i class="ti ti-check me-2"></i>Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($enrollment->id); ?>">
                                                            <i class="ti ti-x me-2"></i>Reject
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if($enrollment->status == 'approved'): ?>
                                                    <li>
                                                        <form action="<?php echo e(route('admin.pre-enrollments.process', $enrollment)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item text-info" onclick="return confirm('This will create an actual enrollment for the student. Continue?')">
                                                                <i class="ti ti-circle-check me-2"></i>Process Enrollment
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?php echo e($enrollment->id); ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Pre-Enrollment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="<?php echo e(route('admin.pre-enrollments.reject', $enrollment)); ?>" method="POST">
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
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="ti ti-inbox fs-48 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">No pre-enrollment submissions found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($preEnrollments->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/pre-enrollments/index.blade.php ENDPATH**/ ?>