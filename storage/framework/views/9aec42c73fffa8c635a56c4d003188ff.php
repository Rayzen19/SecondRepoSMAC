

<?php $__env->startSection('breadcrumb'); ?>
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Subject</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Subject</li>
                <li class="breadcrumb-item active" aria-current="page">Subject List</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="<?php echo e(route('admin.subjects.create')); ?>" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Subject</a>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form method="GET" action="<?php echo e(route('admin.subjects.index')); ?>" id="filterForm">
                        <select name="strand_id" class="form-select" id="strandFilter" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Strands</option>
                            <?php $__currentLoopData = $strands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $strand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($strand->id); ?>" <?php echo e(request('strand_id') == $strand->id ? 'selected' : ''); ?>>
                                    <?php echo e($strand->code); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                </div>
                <div class="col-md-8 text-end">
                    <?php if(request('strand_id')): ?>
                        <span class="text-muted me-2">
                            Showing subjects for: <strong><?php echo e($strands->firstWhere('id', request('strand_id'))->code ?? ''); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="custom-datatable-filter table-responsive">
                
                
                <?php if($coreSubjects->count() > 0): ?>
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ti ti-book me-2"></i>Senior High School Core Curriculum Subjects
                    </h5>
                    <small class="text-muted"><?php echo e($coreSubjects->count()); ?> subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $coreSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-monospace"><strong><?php echo e($subject->code); ?></strong></td>
                            <td><?php echo e($subject->name); ?></td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php $__currentLoopData = $subject->strandSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-primary me-1"><?php echo e($ss->strand->code ?? 'N/A'); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not linked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    ?>
                                    <?php if($gradeLevels->count() > 0): ?>
                                        <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-success me-1">Grade <?php echo e($level); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo e($subject->semester); ?></span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="<?php echo e(route('admin.subjects.teachers', $subject)); ?>" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.show', $subject)); ?>" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.edit', $subject)); ?>" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="<?php echo e(route('admin.subjects.destroy', $subject)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php endif; ?>

                
                <?php if($appliedSubjects->count() > 0): ?>
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-info">
                        <i class="ti ti-briefcase me-2"></i>Senior High School Applied Track Subjects
                    </h5>
                    <small class="text-muted"><?php echo e($appliedSubjects->count()); ?> subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $appliedSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-monospace"><strong><?php echo e($subject->code); ?></strong></td>
                            <td><?php echo e($subject->name); ?></td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php $__currentLoopData = $subject->strandSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-primary me-1"><?php echo e($ss->strand->code ?? 'N/A'); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not linked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    ?>
                                    <?php if($gradeLevels->count() > 0): ?>
                                        <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-success me-1">Grade <?php echo e($level); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo e($subject->semester); ?></span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="<?php echo e(route('admin.subjects.teachers', $subject)); ?>" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.show', $subject)); ?>" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.edit', $subject)); ?>" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="<?php echo e(route('admin.subjects.destroy', $subject)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php endif; ?>

                
                <?php if($specializedSubjects->count() > 0): ?>
                <div class="px-4 py-3 bg-light border-bottom">
                    <h5 class="mb-0 text-warning">
                        <i class="ti ti-star me-2"></i>Senior High School Specialized Subjects
                    </h5>
                    <small class="text-muted"><?php echo e($specializedSubjects->count()); ?> subjects</small>
                </div>
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Strand(s)</th>
                            <th>Grade Level</th>
                            <th>Semester</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $specializedSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-monospace"><strong><?php echo e($subject->code); ?></strong></td>
                            <td><?php echo e($subject->name); ?></td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php $__currentLoopData = $subject->strandSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-primary me-1"><?php echo e($ss->strand->code ?? 'N/A'); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not linked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($subject->strandSubjects && $subject->strandSubjects->count() > 0): ?>
                                    <?php
                                        $gradeLevels = $subject->strandSubjects->pluck('grade_level')->filter()->unique()->sort()->values();
                                    ?>
                                    <?php if($gradeLevels->count() > 0): ?>
                                        <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-success me-1">Grade <?php echo e($level); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo e($subject->semester); ?></span></td>
                            <td>
                                <div class="action-icon d-inline-flex">
                                    <a href="<?php echo e(route('admin.subjects.teachers', $subject)); ?>" class="me-2" title="View Teachers"><i class="ti ti-users"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.show', $subject)); ?>" class="me-2"><i class="ti ti-eye"></i></a>
                                    <a href="<?php echo e(route('admin.subjects.edit', $subject)); ?>" class="me-2"><i class="ti ti-edit"></i></a>
                                    <form action="<?php echo e(route('admin.subjects.destroy', $subject)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="vertical-align: baseline;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php endif; ?>

                
                <?php if($coreSubjects->count() == 0 && $appliedSubjects->count() == 0 && $specializedSubjects->count() == 0): ?>
                <div class="text-center py-5">
                    <i class="ti ti-books" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No subjects found</p>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/laravelapp/resources/views/admin/subjects/index.blade.php ENDPATH**/ ?>