

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Grades</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('student.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Grades</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('student.grades.index')); ?>" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year->id); ?>" <?php echo e($selectedYearId == $year->id ? 'selected' : ''); ?>>
                                    <?php echo e($year->name); ?> (<?php echo e($year->semester); ?> Semester)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" onchange="this.form.submit()">
                            <option value="midterm" <?php echo e(($selectedTerm ?? '') === 'midterm' ? 'selected' : ''); ?>>Midterm</option>
                            <option value="finals" <?php echo e(($selectedTerm ?? '') === 'finals' ? 'selected' : ''); ?>>Finals</option>
                            <option value="final" <?php echo e(($selectedTerm ?? '') === 'final' ? 'selected' : ''); ?>>Final Grade</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($selectedGradeLevel ?? 'all') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Grades Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #ddddf6ff;">
                <h5 class="mb-0">Grades</h5>
            </div>
            <div class="card-body">
                <?php if($grades->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">1st Sem</th>
                                    <th class="text-center">2nd Sem</th>
                                    <th class="text-center">Average</th>
                                    <th class="text-center">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($grade['subject_code']); ?></td>
                                        <td><?php echo e($grade['subject_name']); ?></td>
                                        <td class="text-center">
                                            <?php if($grade['fq_grade']): ?>
                                                <span class="badge grade-badge <?php echo e($grade['fq_grade'] >= 90 ? 'bg-success' : ($grade['fq_grade'] >= 80 ? 'bg-primary' : ($grade['fq_grade'] >= 75 ? 'bg-warning' : 'bg-danger'))); ?>">
                                                    <?php echo e(number_format($grade['fq_grade'], 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($grade['sq_grade']): ?>
                                                <span class="badge grade-badge <?php echo e($grade['sq_grade'] >= 90 ? 'bg-success' : ($grade['sq_grade'] >= 80 ? 'bg-primary' : ($grade['sq_grade'] >= 75 ? 'bg-warning' : 'bg-danger'))); ?>">
                                                    <?php echo e(number_format($grade['sq_grade'], 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($grade['a_grade']): ?>
                                                <span class="badge grade-badge <?php echo e($grade['a_grade'] >= 90 ? 'bg-success' : ($grade['a_grade'] >= 80 ? 'bg-primary' : ($grade['a_grade'] >= 75 ? 'bg-warning' : 'bg-danger'))); ?>">
                                                    <?php echo e(number_format($grade['a_grade'], 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($grade['f_grade']): ?>
                                                <span class="badge grade-badge <?php echo e($grade['f_grade'] >= 90 ? 'bg-success' : ($grade['f_grade'] >= 80 ? 'bg-primary' : ($grade['f_grade'] >= 75 ? 'bg-warning' : 'bg-danger'))); ?>">
                                                    <?php echo e(number_format($grade['f_grade'], 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($average): ?>
                        <div class="text-end mt-3">
                            <div class="d-inline-block bg-primary text-white px-4 py-2 rounded">
                                <strong>Average: <?php echo e($average); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        No grades available for this selection.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Larger font for grade badges */
    .grade-badge {
        font-size: 19px !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/grades/index.blade.php ENDPATH**/ ?>