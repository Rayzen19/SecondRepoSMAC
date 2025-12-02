

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Academic Enhancement & Decision Support</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('student.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Enhancement</li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('student.enhancement.index')); ?>" class="row g-3">
            <div class="col-md-5">
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
            <div class="col-md-4">
                <label for="term" class="form-label">Term</label>
                <select name="term" id="term" class="form-select">
                    <option value="midterm" <?php echo e($selectedTerm == 'midterm' ? 'selected' : ''); ?>>Midterm</option>
                    <option value="finals" <?php echo e($selectedTerm == 'finals' ? 'selected' : ''); ?>>Finals</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn bg-info w-100">
                    <i class="ti ti-filter me-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Decision Support System Section -->
<?php if($selectedYearId && !empty($dssRecommendations)): ?>
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <div class="d-flex align-items-center">
            <i class="ti ti-brain fs-3 me-2"></i>
            <div>
                <h5 class="card-title mb-0 text-white">Decision Support System</h5>
                <small class="text-white-50">AI-Powered Performance Analysis & Recommendations</small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Overall Status -->
        <div class="alert alert-<?php echo e($dssRecommendations['overall_status'] == 'excellent' ? 'success' : ($dssRecommendations['overall_status'] == 'good' ? 'info' : ($dssRecommendations['overall_status'] == 'satisfactory' ? 'warning' : 'danger'))); ?> mb-4">
            <div class="d-flex align-items-center">
                <i class="ti ti-<?php echo e($dssRecommendations['overall_status'] == 'excellent' ? 'trophy' : ($dssRecommendations['overall_status'] == 'good' ? 'thumb-up' : ($dssRecommendations['overall_status'] == 'satisfactory' ? 'alert-circle' : 'alert-triangle'))); ?> fs-2 me-3"></i>
                <div>
                    <h6 class="mb-1">Overall Performance: <strong><?php echo e(ucwords(str_replace('_', ' ', $dssRecommendations['overall_status']))); ?></strong></h6>
                    <p class="mb-0"><?php echo e($dssRecommendations['overall_message']); ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Priority Actions -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="ti ti-flag me-2"></i>Priority Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($dssRecommendations['priority_actions'])): ?>
                            <div class="list-group list-group-flush">
                                <?php $__currentLoopData = $dssRecommendations['priority_actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-group-item border-0 px-0 <?php echo e($index > 0 ? 'border-top' : ''); ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">
                                                <span class="badge bg-<?php echo e($action['priority'] == 'high' ? 'danger' : ($action['priority'] == 'medium' ? 'warning' : 'info')); ?> me-2">
                                                    <?php echo e(ucfirst($action['priority'])); ?>

                                                </span>
                                                <?php echo e($action['title']); ?>

                                            </h6>
                                            <?php if($action['percentage'] < 100): ?>
                                                <span class="badge bg-light text-dark"><?php echo e($action['percentage']); ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-0 text-muted small"><?php echo e($action['description']); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No priority actions needed. Keep up the good work!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Strengths & Areas to Improve -->
            <div class="col-lg-6 mb-4">
                <div class="row h-100">
                    <!-- Strengths -->
                    <div class="col-12 mb-3">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="ti ti-award me-2"></i>Your Strengths
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($dssRecommendations['strengths'])): ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $dssRecommendations['strengths']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $strength): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                <i class="ti ti-check me-1"></i><?php echo e($strength); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0 small">Keep working to build your strengths!</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Areas to Improve -->
                    <div class="col-12">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">
                                    <i class="ti ti-target me-2"></i>Areas to Improve
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($dssRecommendations['areas_to_improve'])): ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $dssRecommendations['areas_to_improve']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-<?php echo e($area['priority'] == 'high' ? 'danger' : 'warning'); ?>-subtle text-<?php echo e($area['priority'] == 'high' ? 'danger' : 'warning'); ?> border border-<?php echo e($area['priority'] == 'high' ? 'danger' : 'warning'); ?>">
                                                <i class="ti ti-arrow-up me-1"></i><?php echo e($area['area']); ?> (<?php echo e($area['percentage']); ?>%)
                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0 small">Great! No critical areas to improve.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Type Analysis -->
        <?php if(!empty($dssRecommendations['assessment_type_analysis'])): ?>
        <div class="card mb-3 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="ti ti-clipboard-list me-2"></i>Assessment Type Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php $__currentLoopData = $dssRecommendations['assessment_type_analysis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analysis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="card border-<?php echo e($analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger'))); ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0"><?php echo e($analysis['type']); ?></h6>
                                        <span class="badge bg-<?php echo e($analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger'))); ?>">
                                            <?php echo e($analysis['percentage']); ?>%
                                        </span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-<?php echo e($analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger'))); ?>"
                                            style="width: <?php echo e($analysis['percentage']); ?>%"></div>
                                    </div>
                                    <p class="mb-0 small text-muted"><?php echo e($analysis['recommendation']); ?></p>
                                    <small class="text-muted"><?php echo e($analysis['count']); ?> assessment(s)</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Subject Analysis -->
        <?php if(!empty($dssRecommendations['subject_analysis'])): ?>
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="ti ti-books me-2"></i>Subject-wise Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="subjectAnalysisAccordion">
                    <?php $__currentLoopData = $dssRecommendations['subject_analysis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $analysis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?php echo e($index > 0 ? 'collapsed' : ''); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#subject<?php echo e($index); ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <span>
                                            <strong><?php echo e($analysis['subject']); ?></strong> 
                                            <small class="text-muted">(<?php echo e($analysis['code']); ?>)</small>
                                        </span>
                                        <span class="badge bg-<?php echo e($analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger'))); ?>">
                                            <?php echo e($analysis['percentage']); ?>%
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="subject<?php echo e($index); ?>" class="accordion-collapse collapse <?php echo e($index == 0 ? 'show' : ''); ?>" data-bs-parent="#subjectAnalysisAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <strong>Status:</strong> 
                                        <span class="badge bg-<?php echo e($analysis['status'] == 'excellent' ? 'success' : ($analysis['status'] == 'good' ? 'primary' : ($analysis['status'] == 'needs_attention' ? 'warning' : 'danger'))); ?>">
                                            <?php echo e(ucwords(str_replace('_', ' ', $analysis['status']))); ?>

                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Recommendation:</strong>
                                        <p class="mb-0"><?php echo e($analysis['recommendation']); ?></p>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Total Assessments:</strong> <?php echo e($analysis['assessments']); ?>

                                    </div>
                                    <?php if(!empty($analysis['weak_types'])): ?>
                                        <div class="alert alert-warning mt-3">
                                            <strong><i class="ti ti-alert-triangle me-1"></i>Weak Assessment Types in this Subject:</strong>
                                            <ul class="mb-0 mt-2">
                                                <?php $__currentLoopData = $analysis['weak_types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weakType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($weakType['type']); ?>: <?php echo e($weakType['percentage']); ?>%</li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Study Tips -->
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="ti ti-bulb me-2 text-warning"></i>General Study Tips
                </h6>
                <ul class="mb-0">
                    <li class="mb-2">Create a study schedule and stick to it consistently</li>
                    <li class="mb-2">Focus on understanding concepts rather than memorizing</li>
                    <li class="mb-2">Practice regularly with different types of assessments</li>
                    <li class="mb-2">Seek help from teachers when you're struggling with a topic</li>
                    <li class="mb-2">Form study groups with classmates to learn collaboratively</li>
                    <li class="mb-0">Take breaks and maintain a healthy study-life balance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="ti ti-chart-line fs-1 text-muted mb-3"></i>
        <h5>Select Academic Year and Term</h5>
        <p class="text-muted">Please select an academic year and term above to view your personalized enhancement recommendations.</p>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const yearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (termSelect) {
        termSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>

<style>
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

.badge {
    font-weight: 500;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/enhancement/index.blade.php ENDPATH**/ ?>