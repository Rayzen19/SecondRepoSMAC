

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Class Record: <?php echo e($assignment->subject?->name ?? '—'); ?></h4>
        <div class="text-muted small"><?php echo e($assignment->academicYear?->display_name ?? ($assignment->academicYear?->name)); ?> • Strand: <?php echo e($assignment->strand?->name ?? '—'); ?></div>
    </div>
    <div class="d-flex gap-2">
        <!-- Publish/Unpublish Grades Button -->
        <form action="<?php echo e(route('teacher.class-records.toggle-publication', $assignment->id)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <?php if($assignment->grades_published): ?>
                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to unpublish grades? Students will no longer be able to view their grades.')">
                    <i class="ti ti-eye-off me-1"></i> Unpublish Grades
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to publish grades? Students will be able to view their grades.')">
                    <i class="ti ti-eye me-1"></i> Publish Grades to Students
                </button>
            <?php endif; ?>
        </form>
        <a href="<?php echo e(route('teacher.class-records.index')); ?>" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i> Back</a>
    </div>
</div>

<!-- School Year Ended Status Badge -->
<?php if($assignment->school_year_ended): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ti ti-calendar-check me-2"></i>
        <strong>School Year Ended:</strong> This class has been finalized on <?php echo e($assignment->school_year_ended_at ? $assignment->school_year_ended_at->format('F d, Y') : 'N/A'); ?>. Pre-enrollment is now disabled for students.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Publication Status Badge -->
<?php if($assignment->grades_published): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-eye me-2"></i>
        <strong>Grades Published:</strong> Students can currently view their grades for this subject.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php else: ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="ti ti-eye-off me-2"></i>
        <strong>Grades Not Published:</strong> Students cannot view their grades yet. Click "Publish Grades to Students" when ready.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(isset($classDetails)): ?>
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">Strand</div><div class="fw-semibold"><?php echo e($classDetails['strand'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Section</div><div class="fw-semibold"><?php echo e($classDetails['section'] ?? '—'); ?></div></div>
            <div class="col-md-2"><div class="text-muted small">Grade</div><div class="fw-semibold"><?php echo e($classDetails['grade'] ?? '—'); ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Subject</div><div class="fw-semibold"><?php echo e($classDetails['subject'] ?? '—'); ?> <span class="text-muted"><?php echo e($classDetails['subject_code'] ? '(' . $classDetails['subject_code'] . ')' : ''); ?></span></div></div>
            <div class="col-md-3"><div class="text-muted small">Subject Teacher</div><div class="fw-semibold"><?php echo e($classDetails['subject_teacher'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Adviser</div><div class="fw-semibold"><?php echo e($classDetails['adviser'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">School Year</div><div class="fw-semibold"><?php echo e($classDetails['school_year'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Semester</div><div class="fw-semibold"><?php echo e($classDetails['semester'] ?? '—'); ?></div></div>
        </div>
    </div>
</div>
<?php endif; ?>

<h6 class="text-muted mb-2">Overview</h6>
<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mx-1 mx-md-0" role="alert">
    <i class="ti ti-check me-2"></i>
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-dark rounded-circle"><i class="ti ti-users"></i></span><div><div class="text-muted small">Total Students</div><div class="h4 mb-0"><?php echo e($counts['total']); ?></div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-success rounded-circle"><i class="ti ti-mood-boy"></i></span><div><div class="text-muted small">Boys</div><div class="h4 mb-0"><?php echo e($counts['male']); ?></div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-info rounded-circle"><i class="ti ti-mood-girl"></i></span><div><div class="text-muted small">Girls</div><div class="h4 mb-0"><?php echo e($counts['female']); ?></div></div></div></div></div>
    </div>
</div>

<h6 class="text-muted mb-2">By Status</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-primary rounded-circle"><i class="ti ti-badge"></i></span><div><div class="text-muted small">Active</div><div class="h4 mb-0"><?php echo e($counts['active']); ?></div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-primary-subtle rounded-circle"><i class="ti ti-certificate"></i></span><div><div class="text-muted small">Graduated</div><div class="h4 mb-0"><?php echo e($counts['graduated']); ?></div></div></div></div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body"><div class="d-flex align-items-center gap-2"><span class="avatar avatar-md bg-warning rounded-circle"><i class="ti ti-user-minus"></i></span><div><div class="text-muted small">Dropped</div><div class="h4 mb-0"><?php echo e($counts['dropped']); ?></div></div></div></div></div>
    </div>
</div>

<h6 class="text-muted mb-2">Students</h6>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $boys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($i + 1); ?></td>
                                    <td><?php echo e($s['student_number']); ?></td>
                                    <td><?php echo e($s['name']); ?></td>
                                    <td>
                                        <?php $st = $s['status']; ?>
                                        <?php switch($st):
                                            case ('active'): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                <?php break; ?>
                                            <?php case ('graduated'): ?>
                                                <span class="badge bg-primary-subtle text-primary">Graduated</span>
                                                <?php break; ?>
                                            <?php case ('dropped'): ?>
                                                <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="badge bg-light text-muted border">N/A</span>
                                        <?php endswitch; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><strong>Boys</strong></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $girls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($i + 1); ?></td>
                                    <td><?php echo e($s['student_number']); ?></td>
                                    <td><?php echo e($s['name']); ?></td>
                                    <td>
                                        <?php $st = $s['status']; ?>
                                        <?php switch($st):
                                            case ('active'): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                <?php break; ?>
                                            <?php case ('graduated'): ?>
                                                <span class="badge bg-primary-subtle text-primary">Graduated</span>
                                                <?php break; ?>
                                            <?php case ('dropped'): ?>
                                                <span class="badge bg-warning-subtle text-warning">Dropped</span>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="badge bg-light text-muted border">N/A</span>
                                        <?php endswitch; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><strong>Girls</strong></div>
        </div>
    </div>
</div>

<h6 class="text-muted mb-2">Class Record</h6>
<style>
    /* Scoped to class record tables only */
    .table-border-black {
        border: 2px solid #000 !important;
        border-collapse: collapse !important;
        background: #fff;
    }
    .table-border-black th,
    .table-border-black td {
        border: 1px solid #000 !important;
    }
    .table-border-black thead th {
        border-bottom: 2px solid #000 !important;
    }
    .table-border-black thead tr:first-child th {
        text-align: center;
    }
    .table-border-black .text-nowrap { white-space: nowrap; }
</style>
<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-first-semester" data-bs-toggle="tab" data-bs-target="#panel-first-semester" type="button" role="tab" aria-controls="panel-first-semester" aria-selected="true">First Semester</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-second-semester" data-bs-toggle="tab" data-bs-target="#panel-second-semester" type="button" role="tab" aria-controls="panel-second-semester" aria-selected="false">Second Semester</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-semester-final" data-bs-toggle="tab" data-bs-target="#panel-semester-final" type="button" role="tab" aria-controls="panel-semester-final" aria-selected="false">Semester Final Grade</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <?php
                $tabs = [
                    ['id' => 'first-semester', 'label' => 'First Semester', 'grade_label' => 'Quarterly Grade'],
                    ['id' => 'second-semester', 'label' => 'Second Semester', 'grade_label' => 'Quarterly Grade'],
                    ['id' => 'semester-final', 'label' => 'Semester Final Grade', 'grade_label' => 'Final Grade'],
                ];
            ?>
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ti => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="tab-pane fade <?php echo e($ti === 0 ? 'show active' : ''); ?>" id="panel-<?php echo e($t['id']); ?>" role="tabpanel" aria-labelledby="tab-<?php echo e($t['id']); ?>">

                    <div class="d-flex justify-content-end py-2 px-3">
                        <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('teacher.class-records.term.show', ['assignment' => $assignment->id, 'term' => $t['id']])); ?>">
                            <i class="ti ti-external-link me-1"></i> View <?php echo e($t['label']); ?>

                        </a>
                    </div>
                    
                    <div class="custom-datatable-filter table-responsive">
                        <?php $tt = $t['id']; $ts = $termSummaries[$tt] ?? null; ?>
                        
                        <?php if($tt === 'first-semester' || $tt === 'second-semester'): ?>
                        <div class="d-flex justify-content-end py-2 px-3">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSubmitFinalGrades">
                                <i class="ti ti-check me-1"></i> Submit Final Grades
                            </button>
                        </div>
                        <table class="table table-sm align-middle mb-0 table-bordered table-border-black">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">No.</th>
                                    <th colspan="5" class="text-center">Name of Student</th>
                                    <th colspan="3" class="text-center">Written Work (<?php echo e(rtrim(rtrim(number_format(($weights['ww'] ?? 0) * 100, 2), '0'), '.')); ?>%)</th>
                                    <th colspan="3" class="text-center">Performance Task (<?php echo e(rtrim(rtrim(number_format(($weights['pt'] ?? 0) * 100, 2), '0'), '.')); ?>%)</th>
                                    <th colspan="3" class="text-center">Quarterly Assessment (<?php echo e(rtrim(rtrim(number_format(($weights['qa'] ?? 0) * 100, 2), '0'), '.')); ?>%)</th>
                                    <th colspan="1" class="text-center">Initial Grade</th>
                                    <th rowspan="2" class="text-center"><?php echo e($t['grade_label']); ?></th>
                                    <th rowspan="2" class="text-center">Action</th>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Family Name</th>
                                    <th class="text-nowrap">,</th>
                                    <th class="text-nowrap">First Name</th>
                                    <th class="text-nowrap">Middle Name</th>
                                    <th class="text-nowrap">Sex</th>
                                    <th class="text-nowrap text-center">Total (<?php echo e(isset($ts['wwMaxTotal']) ? rtrim(rtrim(number_format($ts['wwMaxTotal'], 2), '0'), '.') : '—'); ?>)</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">Total (<?php echo e(isset($ts['ptMaxTotal']) ? rtrim(rtrim(number_format($ts['ptMaxTotal'], 2), '0'), '.') : '—'); ?>)</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">Total (<?php echo e(isset($ts['qaMaxTotal']) ? rtrim(rtrim(number_format($ts['qaMaxTotal'], 2), '0'), '.') : '—'); ?>)</th>
                                    <th class="text-nowrap text-center">PS</th>
                                    <th class="text-nowrap text-center">WS</th>
                                    <th class="text-nowrap text-center">100</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center"><?php echo e($i + 1); ?></td>
                                        <td><?php echo e($s['last_name']); ?></td>
                                        <td>,</td>
                                        <td><?php echo e($s['first_name']); ?></td>
                                        <td><?php echo e($s['middle_name']); ?></td>
                                        <td class="text-center text-uppercase"><?php echo e($s['gender'] === 'male' ? 'M' : 'F'); ?></td>
                                        <?php
                                            $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                            $sid = $s['id'];
                                            $sum = $termSummaries[$tt]['perStudent'][$sid] ?? null;
                                            $wwRaw = $sum['wwRaw'] ?? null; $wwPS = $sum['wwPS'] ?? null; $wwWS = $sum['wwWS'] ?? null;
                                            $ptRaw = $sum['ptRaw'] ?? null; $ptPS = $sum['ptPS'] ?? null; $ptWS = $sum['ptWS'] ?? null;
                                            $qaRaw = $sum['qaRaw'] ?? null; $qaPS = $sum['qaPS'] ?? null; $qaWS = $sum['qaWS'] ?? null;
                                            $initial = $sum['initialTotal'] ?? null;
                                        ?>
                                        <!-- WW -->
                                        <td class="text-center"><?php echo e($fmt($wwRaw)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($wwPS)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($wwWS)); ?></td>
                                        <!-- PT -->
                                        <td class="text-center"><?php echo e($fmt($ptRaw)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($ptPS)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($ptWS)); ?></td>
                                        <!-- QA -->
                                        <td class="text-center"><?php echo e($fmt($qaRaw)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($qaPS)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($qaWS)); ?></td>
                                        <!-- Initial Grade (Total/PS/100) -->
                                        <td class="text-center"><?php echo e($fmt($initial)); ?></td>
                                        <!-- Quarterly Grade equals Initial Grade here -->
                                        <td class="text-center"><?php echo e($fmt($initial)); ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm bg-info" href="<?php echo e(route('teacher.class-records.students.show', ['assignment' => $assignment->id, 'student' => $s['student_number']])); ?>">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        
                        <?php endif; ?>

                        <?php if($tt === 'semester-final'): ?>
                        <table class="table table-sm align-middle mb-0 table-bordered table-border-black">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center">Names</th>
                                    <th colspan="2" class="text-center">Grading Period</th>
                                    <th rowspan="2" class="text-center">Average</th>
                                    <th rowspan="2" class="text-center">Final Grade</th>
                                    <th rowspan="2" class="text-center">Remarks</th>
                                    <th rowspan="2" class="text-center">Description</th>
                                    <th rowspan="2" class="text-center"></th>
                                </tr>
                                <tr>
                                    <th class="text-nowrap"></th>
                                    <th class="text-nowrap">Family Name</th>
                                    <th class="text-nowrap">,</th>
                                    <th class="text-nowrap">First Name</th>
                                    <th class="text-nowrap">Middle Name</th>
                                    <th class="text-nowrap text-center">Sex</th>
                                    <th class="text-nowrap text-center">First Quarter</th>
                                    <th class="text-nowrap text-center">Second Quarter</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center"><?php echo e($i + 1); ?></td>
                                        <td><?php echo e($s['last_name']); ?></td>
                                        <td>,</td>
                                        <td><?php echo e($s['first_name']); ?></td>
                                        <td><?php echo e($s['middle_name']); ?></td>
                                        <td class="text-center text-uppercase"><?php echo e($s['gender'] === 'male' ? 'M' : 'F'); ?></td>
                                        <?php
                                            $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                            $sid = $s['id'];
                                            $firstInit = $termSummaries['first-semester']['perStudent'][$sid]['initialTotal'] ?? null;
                                            $secondInit = $termSummaries['second-semester']['perStudent'][$sid]['initialTotal'] ?? null;
                                            $avg = ($firstInit !== null && $secondInit !== null) ? (($firstInit + $secondInit) / 2) : null;
                                            $final = $avg;
                                            $remarks = null;
                                            if ($final !== null) { $remarks = $final >= 75 ? 'Passed' : 'Failed'; }
                                            $desc = null;
                                            if ($final !== null) {
                                                if ($final >= 90) { $desc = 'Outstanding'; }
                                                elseif ($final >= 85) { $desc = 'Very Satisfactory'; }
                                                elseif ($final >= 80) { $desc = 'Satisfactory'; }
                                                elseif ($final >= 75) { $desc = 'Fairly Satisfactory'; }
                                                elseif ($final >= 60) { $desc = 'Did Not Meet Expectations'; }
                                                else { $desc = '—'; }
                                            }
                                        ?>
                                        <td class="text-center"><?php echo e($fmt($firstInit)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($secondInit)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($avg)); ?></td>
                                        <td class="text-center"><?php echo e($fmt($final)); ?></td>
                                        <td class="text-center"><?php echo e($remarks ?? '—'); ?></td>
                                        <td class="text-center"><?php echo e($desc ?? '—'); ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm bg-info" href="<?php echo e(route('teacher.class-records.students.show', ['assignment' => $assignment->id, 'student' => $s['student_number']])); ?>">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<div class="modal fade" id="modalSubmitFinalGrades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-checks me-2 text-success"></i>Submit Final Grades</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo e(route('teacher.class-records.final-grades.submit', $assignment)); ?>" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Submitting...';">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
                        <i class="ti ti-alert-triangle mt-1"></i>
                        <div>
                            You are about to write final grades to Subject Enrollments for all listed students. You can re-submit later to update the values.
                        </div>
                    </div>
                    <div class="mb-2 small text-muted">Students affected: <strong><?php echo e(isset($students) ? $students->count() : 0); ?></strong></div>
                    <ul class="mb-0 small">
                        <li><strong>First Semester (FQ)</strong> = Initial Grade from First Semester</li>
                        <li><strong>Second Semester (SQ)</strong> = Initial Grade from Second Semester</li>
                        <li><strong>Average (A)</strong> = (FQ + SQ) / 2</li>
                        <li><strong>Final Grade (F)</strong> = Average</li>
                        <li><strong>Remarks</strong> = Passed if Final ≥ 75, else Failed</li>
                        <li><strong>Description</strong> =
                            <span class="d-inline-block">
                                ≥90 Outstanding; ≥85 Very Satisfactory; ≥80 Satisfactory; ≥75 Fairly Satisfactory; ≥60 Did Not Meet Expectations; else —
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> Yes, Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/class_records/show.blade.php ENDPATH**/ ?>