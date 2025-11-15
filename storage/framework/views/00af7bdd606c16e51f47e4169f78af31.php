

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Performance Analytics</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('student.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Performance</li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('student.performance.index')); ?>" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select">
                    <option value="">-- Select Academic Year --</option>
                    <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year->id); ?>" <?php echo e($selectedYearId == $year->id ? 'selected' : ''); ?>>
                            <?php echo e($year->name); ?> - <?php echo e($year->semester); ?> Semester
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select">
                    <option value="midterm" <?php echo e($selectedTerm == 'midterm' ? 'selected' : ''); ?>>Midterm</option>
                    <option value="finals" <?php echo e($selectedTerm == 'finals' ? 'selected' : ''); ?>>Finals</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    <?php $__currentLoopData = $availableSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subject['id']); ?>" <?php echo e($selectedSubjectId == $subject['id'] ? 'selected' : ''); ?>>
                            <?php echo e($subject['code']); ?> - <?php echo e($subject['name']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Performance Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Total Assessments</h6>
                        <h3 class="text-white mb-0"><?php echo e($performanceSummary['total_assessments']); ?></h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-file-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Completed</h6>
                        <h3 class="text-white mb-0"><?php echo e($performanceSummary['completed_assessments']); ?></h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-circle-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Average Score</h6>
                        <h3 class="text-white mb-0"><?php echo e($performanceSummary['average_score']); ?>%</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-chart-bar fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white mb-2">Highest Score</h6>
                        <h3 class="text-white mb-0"><?php echo e($performanceSummary['highest_score']); ?>%</h3>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25">
                        <i class="ti ti-trending-up fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Assessments Doughnut Chart -->
<?php if($allAssessments->isNotEmpty()): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">All Assessments - Doughnut Chart</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-center mb-4">
            <canvas id="allAssessmentsChart" style="max-width: 600px; max-height: 600px;"></canvas>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">All Assessments - Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Assessment Name</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th class="text-center">Score</th>
                        <th class="text-center">Max Score</th>
                        <th class="text-center">Percentage</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $allAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($assessment['name']); ?></strong></td>
                        <td><?php echo e($assessment['subject']); ?></td>
                        <td><?php echo e(ucwords(str_replace('_', ' ', $assessment['type']))); ?></td>
                        <td class="text-center"><?php echo e(round($assessment['score'], 2)); ?></td>
                        <td class="text-center"><?php echo e(round($assessment['max_score'], 2)); ?></td>
                        <td class="text-center">
                            <?php
                                $badgeClass = 'bg-danger';
                                if($assessment['percentage'] >= 90) $badgeClass = 'bg-success';
                                elseif($assessment['percentage'] >= 80) $badgeClass = 'bg-primary';
                                elseif($assessment['percentage'] >= 75) $badgeClass = 'bg-warning';
                            ?>
                            <span class="badge <?php echo e($badgeClass); ?>">
                                <?php echo e($assessment['percentage']); ?>%
                            </span>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?php echo e($badgeClass); ?>"
                                    role="progressbar"
                                    style="width: <?php echo e($assessment['percentage']); ?>%;"
                                    aria-valuenow="<?php echo e($assessment['percentage']); ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <?php echo e($assessment['percentage']); ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Performance by Assessment Type -->
<?php if($performanceByType->isNotEmpty()): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Assessment Type - Bar Chart</h5>
    </div>
    <div class="card-body">
        <canvas id="assessmentTypeChart" style="max-height: 400px;"></canvas>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Assessment Type - Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Assessment Type</th>
                        <th class="text-center">Count</th>
                        <th class="text-center">Total Score</th>
                        <th class="text-center">Max Score</th>
                        <th class="text-center">Percentage</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong><?php echo e(ucwords(str_replace('_', ' ', $type['type']))); ?></strong>
                        </td>
                        <td class="text-center"><?php echo e($type['count']); ?></td>
                        <td class="text-center"><?php echo e(round($type['score'], 2)); ?></td>
                        <td class="text-center"><?php echo e(round($type['max_score'], 2)); ?></td>
                        <td class="text-center">
                            <?php
                                $typeBadgeClass = 'bg-danger';
                                if($type['percentage'] >= 90) $typeBadgeClass = 'bg-success';
                                elseif($type['percentage'] >= 80) $typeBadgeClass = 'bg-primary';
                                elseif($type['percentage'] >= 75) $typeBadgeClass = 'bg-warning';
                            ?>
                            <span class="badge <?php echo e($typeBadgeClass); ?>">
                                <?php echo e($type['percentage']); ?>%
                            </span>
                        </td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar <?php echo e($typeBadgeClass); ?>" 
                                role="progressbar" 
                                style="width: <?php echo e($type['percentage']); ?>%;" 
                                aria-valuenow="<?php echo e($type['percentage']); ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                    <?php echo e($type['percentage']); ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Performance by Subject -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Subject - Bar Chart</h5>
    </div>
    <div class="card-body">
        <?php if($performanceBySubject->isEmpty()): ?>
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No performance data available for the selected academic year and term.
            </div>
        <?php else: ?>
            <canvas id="subjectPerformanceChart" style="max-height: 400px;"></canvas>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Performance by Subject - Details</h5>
    </div>
    <div class="card-body">
        <?php if($performanceBySubject->isEmpty()): ?>
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No performance data available for the selected academic year and term.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">Assessments</th>
                            <th class="text-center">Completed</th>
                            <th class="text-center">Total Score</th>
                            <th class="text-center">Max Score</th>
                            <th class="text-center">Percentage</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($subject['subject_name']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo e($subject['subject_code']); ?></small>
                            </td>
                            <td class="text-center"><?php echo e($subject['total_assessments']); ?></td>
                            <td class="text-center"><?php echo e($subject['completed_assessments']); ?></td>
                            <td class="text-center"><?php echo e(round($subject['total_score'], 2)); ?></td>
                            <td class="text-center"><?php echo e(round($subject['total_max_score'], 2)); ?></td>
                            <td class="text-center">
                                <?php
                                    $subjectBadgeClass = 'bg-danger';
                                    if($subject['percentage'] >= 90) $subjectBadgeClass = 'bg-success';
                                    elseif($subject['percentage'] >= 80) $subjectBadgeClass = 'bg-primary';
                                    elseif($subject['percentage'] >= 75) $subjectBadgeClass = 'bg-warning';
                                ?>
                                <span class="badge <?php echo e($subjectBadgeClass); ?>">
                                    <?php echo e($subject['percentage']); ?>%
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar <?php echo e($subjectBadgeClass); ?>" 
                                    role="progressbar" 
                                    style="width: <?php echo e($subject['percentage']); ?>%;" 
                                    aria-valuenow="<?php echo e($subject['percentage']); ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                        <?php echo e($subject['percentage']); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Detailed breakdown by type -->
                        <?php if(!empty($subject['by_type'])): ?>
                        <tr class="table-light">
                            <td colspan="7">
                                <div class="ms-4">
                                    <small class="text-muted d-block mb-2">Assessment Breakdown:</small>
                                    <div class="row g-2">
                                        <?php $__currentLoopData = $subject['by_type']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeName => $typeData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $typePercentage = $typeData['max_score'] > 0 
                                                ? round(($typeData['score'] / $typeData['max_score']) * 100, 2) 
                                                : 0;
                                        ?>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">
                                                    <?php echo e(ucwords(str_replace('_', ' ', $typeName))); ?>

                                                </span>
                                                <?php
                                                    $typeBreakdownBadgeClass = 'bg-danger';
                                                    if($typePercentage >= 90) $typeBreakdownBadgeClass = 'bg-success';
                                                    elseif($typePercentage >= 80) $typeBreakdownBadgeClass = 'bg-primary';
                                                    elseif($typePercentage >= 75) $typeBreakdownBadgeClass = 'bg-warning';
                                                ?>
                                                <span class="badge <?php echo e($typeBreakdownBadgeClass); ?>">
                                                    <?php echo e($typePercentage); ?>%
                                                </span>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const yearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term');
    const subjectSelect = document.getElementById('subject_id');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            // Reset subject when year changes
            if (subjectSelect) {
                subjectSelect.value = '';
            }
            this.form.submit();
        });
    }
    
    if (termSelect) {
        termSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }

    // All Assessments Doughnut Chart
    <?php if($allAssessments->isNotEmpty()): ?>
    const allAssessmentsCtx = document.getElementById('allAssessmentsChart');
    if (allAssessmentsCtx) {
        // Generate a diverse color palette for each assessment
        const generateColors = (count) => {
            const colors = [];
            const hueStep = 360 / count;
            for (let i = 0; i < count; i++) {
                const hue = (i * hueStep) % 360;
                const saturation = 65 + (i % 3) * 10; // Vary saturation: 65%, 75%, 85%
                const lightness = 50 + (i % 2) * 10;  // Vary lightness: 50%, 60%
                colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, 0.8)`);
            }
            return colors;
        };

        const generateBorderColors = (count) => {
            const colors = [];
            const hueStep = 360 / count;
            for (let i = 0; i < count; i++) {
                const hue = (i * hueStep) % 360;
                const saturation = 65 + (i % 3) * 10;
                const lightness = 40 + (i % 2) * 10; // Darker for border
                colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, 1)`);
            }
            return colors;
        };

        const assessmentCount = <?php echo e($allAssessments->count()); ?>;
        const backgroundColors = generateColors(assessmentCount);
        const borderColors = generateBorderColors(assessmentCount);

        const allAssessmentsData = {
            labels: [
                <?php $__currentLoopData = $allAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e($assessment['name']); ?> (<?php echo e($assessment['subject']); ?>)',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    <?php $__currentLoopData = $allAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($assessment['percentage']); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        };

        new Chart(allAssessmentsCtx, {
            type: 'doughnut',
            data: allAssessmentsData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            },
                            boxWidth: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    // Shorten label for legend
                                    const shortLabel = label.length > 25 ? label.substring(0, 25) + '...' : label;
                                    return {
                                        text: shortLabel + ': ' + value.toFixed(2) + '%',
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: data.datasets[0].borderColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'All Assessments Performance',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const assessments = [
                                    <?php $__currentLoopData = $allAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        {
                                            name: '<?php echo e($assessment['name']); ?>',
                                            subject: '<?php echo e($assessment['subject']); ?>',
                                            type: '<?php echo e(ucwords(str_replace('_', ' ', $assessment['type']))); ?>',
                                            score: <?php echo e(round($assessment['score'], 2)); ?>,
                                            maxScore: <?php echo e(round($assessment['max_score'], 2)); ?>,
                                            percentage: <?php echo e($assessment['percentage']); ?>

                                        },
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const assessment = assessments[index];
                                return [
                                    assessment.name,
                                    'Subject: ' + assessment.subject,
                                    'Type: ' + assessment.type,
                                    'Percentage: ' + assessment.percentage.toFixed(2) + '%',
                                    'Score: ' + assessment.score + ' / ' + assessment.maxScore
                                ];
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // Assessment Type Doughnut Chart
    <?php if($performanceByType->isNotEmpty()): ?>
    const assessmentTypeCtx = document.getElementById('assessmentTypeChart');
    if (assessmentTypeCtx) {
        const assessmentTypeData = {
            labels: [
                <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e(ucwords(str_replace('_', ' ', $type['type']))); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($type['percentage']); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: [
                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($type['percentage'] >= 90): ?>
                            'rgba(40, 167, 69, 0.8)',
                        <?php elseif($type['percentage'] >= 80): ?>
                            'rgba(0, 123, 255, 0.8)',
                        <?php elseif($type['percentage'] >= 75): ?>
                            'rgba(255, 193, 7, 0.8)',
                        <?php else: ?>
                            'rgba(220, 53, 69, 0.8)',
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderColor: [
                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($type['percentage'] >= 90): ?>
                            'rgba(40, 167, 69, 1)',
                        <?php elseif($type['percentage'] >= 80): ?>
                            'rgba(0, 123, 255, 1)',
                        <?php elseif($type['percentage'] >= 75): ?>
                            'rgba(255, 193, 7, 1)',
                        <?php else: ?>
                            'rgba(220, 53, 69, 1)',
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderWidth: 2
            }]
        };

        new Chart(assessmentTypeCtx, {
            type: 'bar',
            data: assessmentTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Performance by Assessment Type',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const scores = [
                                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e(round($type['score'], 2)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const maxScores = [
                                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e(round($type['max_score'], 2)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const counts = [
                                    <?php $__currentLoopData = $performanceByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($type['count']); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                return [
                                    'Percentage: ' + context.parsed.y.toFixed(2) + '%',
                                    'Score: ' + scores[index] + ' / ' + maxScores[index],
                                    'Assessments: ' + counts[index]
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Assessment Type'
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // Subject Performance Doughnut Chart
    <?php if($performanceBySubject->isNotEmpty()): ?>
    const subjectPerformanceCtx = document.getElementById('subjectPerformanceChart');
    if (subjectPerformanceCtx) {
        const subjectPerformanceData = {
            labels: [
                <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e($subject['subject_code']); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                label: 'Percentage',
                data: [
                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($subject['percentage']); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: [
                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($subject['percentage'] >= 90): ?>
                            'rgba(40, 167, 69, 0.8)',
                        <?php elseif($subject['percentage'] >= 80): ?>
                            'rgba(0, 123, 255, 0.8)',
                        <?php elseif($subject['percentage'] >= 75): ?>
                            'rgba(255, 193, 7, 0.8)',
                        <?php else: ?>
                            'rgba(220, 53, 69, 0.8)',
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderColor: [
                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($subject['percentage'] >= 90): ?>
                            'rgba(40, 167, 69, 1)',
                        <?php elseif($subject['percentage'] >= 80): ?>
                            'rgba(0, 123, 255, 1)',
                        <?php elseif($subject['percentage'] >= 75): ?>
                            'rgba(255, 193, 7, 1)',
                        <?php else: ?>
                            'rgba(220, 53, 69, 1)',
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderWidth: 2
            }]
        };

        new Chart(subjectPerformanceCtx, {
            type: 'bar',
            data: subjectPerformanceData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Performance by Subject',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const subjectNames = [
                                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        '<?php echo e($subject['subject_name']); ?>',
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const scores = [
                                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e(round($subject['total_score'], 2)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const maxScores = [
                                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e(round($subject['total_max_score'], 2)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                const assessments = [
                                    <?php $__currentLoopData = $performanceBySubject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($subject['total_assessments']); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ];
                                return [
                                    subjectNames[index],
                                    'Percentage: ' + context.parsed.y.toFixed(2) + '%',
                                    'Score: ' + scores[index] + ' / ' + maxScores[index],
                                    'Assessments: ' + assessments[index]
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Subject Code'
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>

<style>
.avatar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.progress {
    border-radius: 5px;
}

.progress-bar {
    border-radius: 5px;
    font-weight: 600;
}

.table-light td {
    background-color: #f8f9fa !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/performance/index.blade.php ENDPATH**/ ?>