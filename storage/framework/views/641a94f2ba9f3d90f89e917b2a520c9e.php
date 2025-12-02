

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
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year->id); ?>" <?php echo e($selectedYearId == $year->id ? 'selected' : ''); ?>>
                                    <?php echo e($year->name); ?> (<?php echo e($year->semester); ?> Semester)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select" onchange="this.form.submit()">
                            <option value="midterm" <?php echo e(($selectedTerm ?? '') === 'midterm' ? 'selected' : ''); ?>>Midterm</option>
                            <option value="finals" <?php echo e(($selectedTerm ?? '') === 'finals' ? 'selected' : ''); ?>>Finals</option>
                            <option value="final" <?php echo e(($selectedTerm ?? '') === 'final' ? 'selected' : ''); ?>>Final Grade</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $gradeLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($selectedGradeLevel ?? 'all') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" onchange="this.form.submit()">
                            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($section['id']); ?>" <?php echo e(($selectedSectionId ?? 'all') == $section['id'] ? 'selected' : ''); ?>>
                                    <?php echo e($section['name']); ?>

                                </option>
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
        <?php if($grades->count() > 0): ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="alert alert-info m-3 mb-2" role="alert">
                        <i class="fas fa-info-circle"></i>
                        INC or 4.00 grade can only be edited within one (1) academic year; otherwise, grade will be marked 5.00
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <tr>
                                    <th style="padding: 12px;">Subject Code</th>
                                    <th>Subject Title</th>
                                    <th class="text-center">Section</th>
                                    <th class="text-center">Schedule Code</th>
                                    <th class="text-center">Lec Unit</th>
                                    <th class="text-center">Lab Unit</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalLec = 0;
                                    $totalLab = 0;
                                    $totalUnits = 0;
                                ?>
                                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $lecUnit = $grade['lec_unit'] ?? 0;
                                        $labUnit = $grade['lab_unit'] ?? 0;
                                        $total = $lecUnit + $labUnit;
                                        $totalLec += $lecUnit;
                                        $totalLab += $labUnit;
                                        $totalUnits += $total;
                                        
                                        // Determine grade display based on selected term
                                        $displayGrade = null;
                                        if($selectedTerm === 'midterm' || $selectedTerm === 'finals') {
                                            $displayGrade = $selectedTerm === 'midterm' ? $grade['fq_grade'] : $grade['sq_grade'];
                                        } else {
                                            $displayGrade = $grade['f_grade'] ?? $grade['a_grade'];
                                        }
                                    ?>
                                    <tr>
                                        <td style="padding: 10px;"><?php echo e($grade['subject_code']); ?></td>
                                        <td><?php echo e($grade['subject_name']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white"><?php echo e($grade['section'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td class="text-center"><?php echo e($grade['schedule_code'] ?? 'N/A'); ?></td>
                                        <td class="text-center"><?php echo e($lecUnit); ?></td>
                                        <td class="text-center"><?php echo e($labUnit); ?></td>
                                        <td class="text-center"><strong><?php echo e($total); ?></strong></td>
                                        <td class="text-center">
                                            <?php if($displayGrade): ?>
                                                <span class="badge <?php echo e($displayGrade >= 90 ? 'bg-success' : ($displayGrade >= 80 ? 'bg-primary' : ($displayGrade >= 75 ? 'bg-warning text-dark' : 'bg-danger'))); ?>" style="font-size: 14px; padding: 6px 12px;">
                                                    <?php echo e(number_format($displayGrade, 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">—</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                                    <td colspan="4" class="text-end" style="padding: 12px;">Total:</td>
                                    <td class="text-center"><?php echo e($totalLec); ?></td>
                                    <td class="text-center"><?php echo e($totalLab); ?></td>
                                    <td class="text-center"><?php echo e($totalUnits); ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if($average): ?>
                        <div class="p-3 text-end" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                            <span class="badge bg-warning text-dark" style="font-size: 16px; padding: 8px 20px;">
                                Average: <?php echo e(number_format($average, 2)); ?>

                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No grades available for this selection.
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/grades/index.blade.php ENDPATH**/ ?>