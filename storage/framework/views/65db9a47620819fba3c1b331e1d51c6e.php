

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('teacher.students.all-sections')); ?>">All Sections</a></li>
                            <li class="breadcrumb-item active">Section <?php echo e($sectionAssignment->section->name ?? 'N/A'); ?></li>
                        </ol>
                    </nav>
                    <h4 class="mb-1">
                        <?php echo e($sectionAssignment->strand->code ?? 'Strand'); ?> • 
                        <?php echo e($sectionAssignment->section->grade ?? 'Grade'); ?> 
                        Section <?php echo e($sectionAssignment->section->name ?? 'N/A'); ?>

                    </h4>
                    <p class="text-muted mb-0"><?php echo e($sectionAssignment->academicYear->display_name ?? $sectionAssignment->academicYear->name ?? 'Academic Year'); ?></p>
                </div>
                <a href="<?php echo e(route('teacher.students.all-sections')); ?>" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to All Sections
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Students</h6>
                            <h2 class="mb-0"><?php echo e($total); ?></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="ti ti-users" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Male Students</h6>
                            <h2 class="mb-0"><?php echo e($male); ?></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="ti ti-user" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Female Students</h6>
                            <h2 class="mb-0"><?php echo e($female); ?></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="ti ti-user" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="ti ti-list me-2"></i>Student List</h5>
        </div>
        <div class="card-body p-0">
            <?php if($students->isEmpty()): ?>
                <div class="text-center py-5">
                    <div class="display-4 text-muted mb-3"><i class="ti ti-users-off"></i></div>
                    <h5 class="mb-2">No Students Enrolled</h5>
                    <p class="text-muted mb-0">There are no students enrolled in this section yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Registration Number</th>
                                <th>Status</th>
                                <th class="text-end" style="width:240px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td class="font-monospace"><?php echo e($row['student']->student_number); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                <?php echo e(strtoupper(substr($row['student']->first_name, 0, 1))); ?>

                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <?php echo e($row['student']->last_name); ?>, <?php echo e($row['student']->first_name); ?> <?php echo e($row['student']->middle_name); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e(strtolower($row['student']->gender) === 'male' ? 'info' : 'success'); ?>-subtle text-<?php echo e(strtolower($row['student']->gender) === 'male' ? 'info' : 'success'); ?>">
                                            <?php echo e(ucfirst($row['student']->gender)); ?>

                                        </span>
                                    </td>
                                    <td class="font-monospace small"><?php echo e($row['registration_number']); ?></td>
                                    <td>
                                        <?php
                                            $statusColor = $row['status'] === 'enrolled' ? 'success' : ($row['status'] === 'completed' ? 'primary' : 'secondary');
                                        ?>
                                        <span class="badge bg-<?php echo e($statusColor); ?>"><?php echo e(ucfirst($row['status'])); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end">
                                            <?php $assignCount = isset($assignmentOptions) ? $assignmentOptions->count() : 0; ?>
                                            <?php if($assignCount === 1): ?>
                                                <?php $a = $assignmentOptions->first(); ?>
                                                <a href="<?php echo e(route('teacher.class-records.students.show', ['assignment' => $a->id, 'student' => $row['student']->student_number])); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-notes"></i>Record
                                                </a>
                                            <?php elseif($assignCount > 1): ?>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ti ti-notes"></i> Add Record
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <?php $__currentLoopData = $assignmentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li>
                                                                <a class="dropdown-item" href="<?php echo e(route('teacher.class-records.students.show', ['assignment' => $opt->id, 'student' => $row['student']->student_number])); ?>">
                                                                    <?php echo e($opt->subject->name ?? 'Subject'); ?>

                                                                </a>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">No subject</span>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2 js-view-student"
                                                    data-student-number="<?php echo e($row['student']->student_number); ?>"
                                                    data-name="<?php echo e($row['student']->last_name); ?>, <?php echo e($row['student']->first_name); ?> <?php echo e($row['student']->middle_name); ?>"
                                                    data-gender="<?php echo e(ucfirst($row['student']->gender)); ?>"
                                                    data-registration="<?php echo e($row['registration_number']); ?>"
                                                    data-status="<?php echo e(ucfirst($row['status'])); ?>">
                                                <i class="ti ti-user"></i> View Info
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Student Quick Info Modal -->
    <div class="modal fade" id="studentInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-id"></i> Student Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Student Number</dt>
                        <dd class="col-sm-7" id="si-student-number">—</dd>
                        <dt class="col-sm-5">Name</dt>
                        <dd class="col-sm-7" id="si-name">—</dd>
                        <dt class="col-sm-5">Gender</dt>
                        <dd class="col-sm-7" id="si-gender">—</dd>
                        <dt class="col-sm-5">Registration #</dt>
                        <dd class="col-sm-7" id="si-registration">—</dd>
                        <dt class="col-sm-5">Enrollment Status</dt>
                        <dd class="col-sm-7" id="si-status">—</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('studentInfoModal');
            const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
            document.querySelectorAll('.js-view-student').forEach(function(btn){
                btn.addEventListener('click', function(){
                    if(!modal) return;
                    document.getElementById('si-student-number').textContent = this.dataset.studentNumber || '—';
                    document.getElementById('si-name').textContent = this.dataset.name || '—';
                    document.getElementById('si-gender').textContent = this.dataset.gender || '—';
                    document.getElementById('si-registration').textContent = this.dataset.registration || '—';
                    document.getElementById('si-status').textContent = this.dataset.status || '—';
                    modal.show();
                });
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/students/section.blade.php ENDPATH**/ ?>