

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Student Scores Overview</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('teacher.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Scores</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('teacher.scores.index')); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Select Year</option>
                                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year->id); ?>" <?php echo e($selectedYearId == $year->id ? 'selected' : ''); ?>>
                                        <?php echo e($year->name); ?> - <?php echo e(ucfirst($year->semester)); ?> Semester
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select" onchange="this.form.submit()" required>
                                <option value="">Select Subject</option>
                                <?php $__currentLoopData = $availableSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subject['id']); ?>" <?php echo e($selectedSubjectId == $subject['id'] ? 'selected' : ''); ?>>
                                        <?php echo e($subject['code']); ?> - <?php echo e($subject['name']); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="section_id" class="form-label">Section</label>
                            <select name="section_id" id="section_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Sections</option>
                                <?php $__currentLoopData = $availableSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($section['id']); ?>" <?php echo e($selectedSectionId == $section['id'] ? 'selected' : ''); ?>>
                                        <?php echo e($section['name']); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="term" class="form-label">Term/Quarter</label>
                            <select name="term" id="term" class="form-select" onchange="this.form.submit()">
                                <option value="midterm" <?php echo e($selectedTerm === 'midterm' ? 'selected' : ''); ?>>1st Quarter (Midterm)</option>
                                <option value="finals" <?php echo e($selectedTerm === 'finals' ? 'selected' : ''); ?>>2nd Quarter (Finals)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="assessment_id" class="form-label">Assessment</label>
                            <select name="assessment_id" id="assessment_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Assessments</option>
                                <?php $__currentLoopData = $assessmentsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($assessment['id']); ?>" <?php echo e(request('assessment_id') == $assessment['id'] ? 'selected' : ''); ?>>
                                        <?php echo e($assessment['name']); ?> (<?php echo e($assessment['type']); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="student_id" class="form-label">Student</label>
                            <select name="student_id" id="student_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Students</option>
                                <?php $__currentLoopData = $availableStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($student['id']); ?>" <?php echo e($selectedStudentId == $student['id'] ? 'selected' : ''); ?>>
                                        <?php echo e($student['student_number']); ?> - <?php echo e($student['name']); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($selectedSubjectId): ?>
                <?php if($assessmentsList->isEmpty()): ?>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No assessments found for the selected subject and term.
                    </div>
                <?php elseif($studentScores->isEmpty()): ?>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No students enrolled in this subject.
                    </div>
                <?php else: ?>
                    <!-- Student Scores List -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="ti ti-list-check me-2"></i>
                                Student Scores
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student Number</th>
                                            <?php $__currentLoopData = $assessmentsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="text-center">
                                                    <?php echo e($assessment['name']); ?><br>
                                                    <small class="text-muted"><?php echo e($assessment['type']); ?></small>
                                                </th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <th class="text-center">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $studentScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><strong><?php echo e($studentData['student_name']); ?></strong></td>
                                                <td><?php echo e($studentData['student_number']); ?></td>
                                                <?php $__currentLoopData = $studentData['scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scoreData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <input 
                                                                type="number" 
                                                                class="form-control form-control-sm score-input text-center" 
                                                                style="width: 70px;"
                                                                step="0.01"
                                                                min="0"
                                                                max="<?php echo e($scoreData['max_score']); ?>"
                                                                value="<?php echo e($scoreData['score'] !== null ? $scoreData['score'] : ''); ?>"
                                                                placeholder="0"
                                                                data-student-id="<?php echo e($studentData['student_id']); ?>"
                                                                data-assessment-id="<?php echo e($scoreData['assessment_id']); ?>"
                                                                data-max-score="<?php echo e($scoreData['max_score']); ?>"
                                                            >
                                                            <span class="text-muted">/<?php echo e(number_format($scoreData['max_score'], 0)); ?></span>
                                                        </div>
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <td class="text-center">
                                                    <span class="badge 
                                                        <?php if($studentData['average_percentage'] >= 90): ?> bg-success
                                                        <?php elseif($studentData['average_percentage'] >= 80): ?> bg-primary
                                                        <?php elseif($studentData['average_percentage'] >= 75): ?> bg-warning
                                                        <?php else: ?> bg-danger
                                                        <?php endif; ?>
                                                        fs-6
                                                    ">
                                                        <?php echo e($studentData['average_percentage']); ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-success" id="saveScoresBtn">
                                    <i class="ti ti-device-floppy me-1"></i> Save Scores
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Please select a subject to view student scores.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .portal-mobile-header, .footer, .page-header, .card-body form, button, .breadcrumb {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        page-break-inside: avoid;
    }
    .card-header {
        background-color: #0d6efd !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    table {
        font-size: 10px;
    }
    .badge {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('saveScoresBtn');
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const inputs = document.querySelectorAll('.score-input');
            const scores = [];
            
            inputs.forEach(input => {
                const value = input.value;
                if (value !== '') {
                    scores.push({
                        student_id: input.dataset.studentId,
                        assessment_id: input.dataset.assessmentId,
                        raw_score: parseFloat(value),
                        max_score: parseFloat(input.dataset.maxScore)
                    });
                }
            });
            
            if (scores.length === 0) {
                alert('No scores to save.');
                return;
            }
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            
            fetch('<?php echo e(route("teacher.scores.store")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ scores: scores })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Scores saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save scores'));
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Save Scores';
                }
            })
            .catch(error => {
                alert('Error saving scores: ' + error.message);
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Save Scores';
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/scores/index.blade.php ENDPATH**/ ?>