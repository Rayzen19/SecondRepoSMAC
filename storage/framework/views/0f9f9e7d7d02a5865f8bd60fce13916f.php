

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Archive</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Archive</li>
                </ul>
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

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php echo e($activeTab === 'teachers' ? 'active' : ''); ?>" 
                           href="<?php echo e(route('admin.archive.index', ['tab' => 'teachers'])); ?>">
                            <i class="ti ti-users me-1"></i> Inactive Teachers (<?php echo e($inactiveTeachers->total()); ?>)
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php echo e($activeTab === 'students' ? 'active' : ''); ?>" 
                           href="<?php echo e(route('admin.archive.index', ['tab' => 'students'])); ?>">
                            <i class="ti ti-school me-1"></i> Inactive Students (<?php echo e($inactiveStudents->total()); ?>)
                        </a>
                    </li>
                </ul>

                <!-- Teachers Tab Content -->
                <?php if($activeTab === 'teachers'): ?>
                    <div class="tab-pane fade show active">
                        <h5 class="mb-3">Inactive Teachers</h5>
                        <?php $__empty_1 = true; $__currentLoopData = $inactiveTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="card mb-3 shadow-sm border">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo e($teacher->profile_picture ?? asset('assets/img/profiles/avatar-02.jpg')); ?>" 
                                                     alt="<?php echo e($teacher->name); ?>" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($teacher->name); ?></h6>
                                                    <small class="text-muted"><?php echo e($teacher->email); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Employee: <?php echo e($teacher->employee_number); ?></small>
                                            <small class="text-muted">Dept: <?php echo e($teacher->department ?? 'N/A'); ?></small>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-secondary">Inactive</span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <form action="<?php echo e(route('admin.archive.teachers.restore', $teacher)); ?>" method="POST" class="d-inline restore-teacher-form" data-teacher-id="<?php echo e($teacher->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        class="btn btn-sm me-1 restore-teacher-btn"
                                                        style="background-color: #28a745; color: white; border-radius: 20px; padding: 5px 15px; border: none;">
                                                    <i class="feather-refresh-cw me-1"></i> Restore
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.archive.teachers.delete', $teacher)); ?>" method="POST" class="d-inline delete-teacher-form" data-teacher-id="<?php echo e($teacher->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="btn btn-sm delete-teacher-btn"
                                                        style="background-color: #dc3545; color: white; border-radius: 20px; padding: 5px 15px; border: none;">
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
                                No inactive teachers found.
                            </div>
                        <?php endif; ?>

                        <?php if($inactiveTeachers->hasPages()): ?>
                            <div class="mt-4">
                                <?php echo e($inactiveTeachers->appends(['tab' => 'teachers'])->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Students Tab Content -->
                <?php if($activeTab === 'students'): ?>
                    <div class="tab-pane fade show active">
                        <h5 class="mb-3">Inactive Students</h5>
                        <?php $__empty_1 = true; $__currentLoopData = $inactiveStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="card mb-3 shadow-sm border">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo e($student->profile_picture ?? asset('assets/img/profiles/avatar-02.jpg')); ?>" 
                                                     alt="<?php echo e($student->name); ?>" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($student->name); ?></h6>
                                                    <small class="text-muted"><?php echo e($student->email); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Student ID: <?php echo e($student->student_number); ?></small>
                                            <small class="text-muted">Program: <?php echo e($student->program ?? 'N/A'); ?></small>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-secondary">Inactive</span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <form action="<?php echo e(route('admin.archive.students.restore', $student)); ?>" method="POST" class="d-inline restore-student-form" data-student-id="<?php echo e($student->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        class="btn btn-sm me-1 restore-student-btn"
                                                        style="background-color: #28a745; color: white; border-radius: 20px; padding: 5px 15px; border: none;">
                                                    <i class="feather-refresh-cw me-1"></i> Restore
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.archive.students.delete', $student)); ?>" method="POST" class="d-inline delete-student-form" data-student-id="<?php echo e($student->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="btn btn-sm delete-student-btn"
                                                        style="background-color: #dc3545; color: white; border-radius: 20px; padding: 5px 15px; border: none;">
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
                                No inactive students found.
                            </div>
                        <?php endif; ?>

                        <?php if($inactiveStudents->hasPages()): ?>
                            <div class="mt-4">
                                <?php echo e($inactiveStudents->appends(['tab' => 'students'])->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Handle restore teacher form submission
    $('.restore-teacher-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Restore this teacher?')) {
            return false;
        }
        
        const form = $(this);
        const button = form.find('.restore-teacher-btn');
        const originalHtml = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="feather-loader"></i> Restoring...');
        
        // Get fresh CSRF token
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Reload the page to show updated list
                window.location.reload();
            },
            error: function(xhr) {
                button.prop('disabled', false).html(originalHtml);
                
                if (xhr.status === 419) {
                    alert('Your session has expired. Please refresh the page and try again.');
                    window.location.reload();
                } else {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to restore teacher'));
                }
            }
        });
    });
    
    // Handle delete teacher form submission
    $('.delete-teacher-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Permanently delete this teacher? This action cannot be undone!')) {
            return false;
        }
        
        const form = $(this);
        const button = form.find('.delete-teacher-btn');
        const originalHtml = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="feather-loader"></i> Deleting...');
        
        // Get fresh CSRF token
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function(response) {
                // Reload the page to show updated list
                window.location.reload();
            },
            error: function(xhr) {
                button.prop('disabled', false).html(originalHtml);
                
                if (xhr.status === 419) {
                    alert('Your session has expired. Please refresh the page and try again.');
                    window.location.reload();
                } else {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete teacher'));
                }
            }
        });
    });
    
    // Handle restore student form submission
    $('.restore-student-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Restore this student?')) {
            return false;
        }
        
        const form = $(this);
        const button = form.find('.restore-student-btn');
        const originalHtml = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="feather-loader"></i> Restoring...');
        
        // Get fresh CSRF token
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Reload the page to show updated list
                window.location.reload();
            },
            error: function(xhr) {
                button.prop('disabled', false).html(originalHtml);
                
                if (xhr.status === 419) {
                    alert('Your session has expired. Please refresh the page and try again.');
                    window.location.reload();
                } else {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to restore student'));
                }
            }
        });
    });
    
    // Handle delete student form submission
    $('.delete-student-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Permanently delete this student? This action cannot be undone!')) {
            return false;
        }
        
        const form = $(this);
        const button = form.find('.delete-student-btn');
        const originalHtml = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="feather-loader"></i> Deleting...');
        
        // Get fresh CSRF token
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function(response) {
                // Reload the page to show updated list
                window.location.reload();
            },
            error: function(xhr) {
                button.prop('disabled', false).html(originalHtml);
                
                if (xhr.status === 419) {
                    alert('Your session has expired. Please refresh the page and try again.');
                    window.location.reload();
                } else {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete student'));
                }
            }
        });
    });
    
    // Refresh CSRF token periodically (every 5 minutes)
    setInterval(function() {
        $.get('/csrf-token', function(data) {
            $('meta[name="csrf-token"]').attr('content', data.token);
        });
    }, 300000); // 5 minutes
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/archive/index.blade.php ENDPATH**/ ?>