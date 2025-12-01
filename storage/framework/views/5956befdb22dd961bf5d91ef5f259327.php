

<?php $__env->startSection('title', 'Pre-Enrollment'); ?>

<?php $__env->startPush('head'); ?>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-0">Pre-Enrollment for Next Academic Year</h1>
                    <p class="text-muted mb-0">Submit your enrollment preferences for the upcoming school year</p>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Current Enrollment Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ti ti-info-circle me-2"></i>Your Current Enrollment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Academic Year:</strong>
                            <p class="mb-0"><?php echo e($currentAcademicYear->display_name); ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Grade Level:</strong>
                            <p class="mb-0"><?php echo e($currentEnrollment->academicYearStrandSection?->section?->grade ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Strand:</strong>
                            <p class="mb-0"><?php echo e($currentEnrollment->strand?->code ?? 'N/A'); ?> - <?php echo e($currentEnrollment->strand?->name ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Section:</strong>
                            <p class="mb-0"><?php echo e($currentEnrollment->academicYearStrandSection?->section?->name ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($existingPreEnrollment): ?>
        <!-- Existing Pre-Enrollment Submission -->
        <div class="row">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="ti ti-check-circle me-2"></i>Your Pre-Enrollment Submission</h5>
                    </div>
                    <div class="card-body">
                        <?php if($existingPreEnrollment->status === 'pending'): ?>
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-clock" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>For Approval</strong></h4>
                                        <p class="mb-0">Your pre-enrollment application has been submitted successfully and is currently awaiting administrator review. You will be notified once your application has been processed.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($existingPreEnrollment->status === 'approved'): ?>
                            <div class="alert alert-success mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-check" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>You Are Already Enrolled</strong></h4>
                                        <p class="mb-0">Congratulations! Your pre-enrollment application has been approved. You are now enrolled for the next academic year.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($existingPreEnrollment->status === 'enrolled'): ?>
                            <div class="alert alert-success mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-check" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>You Are Already Enrolled</strong></h4>
                                        <p class="mb-0">Congratulations! You have been successfully enrolled for the next academic year. Your enrollment has been processed and finalized.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($existingPreEnrollment->status === 'rejected'): ?>
                            <div class="alert alert-danger mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-circle-x" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1"><strong>Application Rejected</strong></h4>
                                        <p class="mb-0">Unfortunately, your pre-enrollment application has been rejected. Please contact the administrator for more information.</p>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-4">
                                <i class="ti ti-info-circle me-2"></i>
                                You have already submitted your pre-enrollment. Below are the details:
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p class="mb-0">
                                    <?php if($existingPreEnrollment->status === 'pending'): ?>
                                        <span class="badge bg-warning">Pending Review</span>
                                    <?php elseif($existingPreEnrollment->status === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif($existingPreEnrollment->status === 'rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php elseif($existingPreEnrollment->status === 'enrolled'): ?>
                                        <span class="badge bg-primary">Enrolled</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Submitted On:</strong>
                                <p class="mb-0"><?php echo e($existingPreEnrollment->submitted_at?->format('M d, Y h:i A') ?? 'N/A'); ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Grade Level:</strong>
                                <p class="mb-0"><?php echo e($existingPreEnrollment->grade_level); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong>Strand:</strong>
                                <p class="mb-0"><?php echo e($existingPreEnrollment->strand?->code ?? 'N/A'); ?> - <?php echo e($existingPreEnrollment->strand?->name ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong>Preferred Section:</strong>
                                <p class="mb-0"><?php echo e($existingPreEnrollment->section?->name ?? 'No preference'); ?></p>
                            </div>
                        </div>

                        <?php if($existingPreEnrollment->remarks): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Remarks:</strong>
                                    <p class="mb-0"><?php echo e($existingPreEnrollment->remarks); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?php echo e(route('student.dashboard')); ?>" class="btn bg-info">
                                <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Pre-Enrollment Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-file-check me-2"></i>Pre-Enrollment Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('student.pre-enrollment.store')); ?>" method="POST" id="preEnrollmentForm">
                            <?php echo csrf_field(); ?>

                            <div class="alert alert-info mb-4">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Note:</strong> Please select your preferred grade level, strand, and section for the next academic year.
                                The section selection is optional and will be considered as a preference only. Each section has a maximum capacity of 30 students.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="grade_level" class="form-label">Grade Level <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['grade_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="grade_level" 
                                            name="grade_level" 
                                            required>
                                        <option value="">Select Grade Level</option>
                                        <option value="G-12" <?php echo e(old('grade_level') === 'G-12' ? 'selected' : ''); ?>>Grade 12</option>
                                    </select>
                                    <?php $__errorArgs = ['grade_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="strand_id" class="form-label">Strand <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['strand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="strand_id" 
                                            name="strand_id" 
                                            required>
                                        <option value="">Select Strand</option>
                                        <?php $__currentLoopData = $strands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $strand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($strand->id); ?>" <?php echo e(old('strand_id') == $strand->id ? 'selected' : ''); ?>>
                                                <?php echo e($strand->code); ?> - <?php echo e($strand->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['strand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="section_id" class="form-label">Preferred Section (Optional)</label>
                                    <select class="form-select <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="section_id" 
                                            name="section_id">
                                        <option value="">No preference</option>
                                    </select>
                                    <small class="text-muted">Sections will load based on your grade level and strand selection. Only sections with available spots (max 30 students) are shown.</small>
                                    <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-4">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Important:</strong> Please review your selections carefully before submitting. 
                                You can only submit your pre-enrollment once. If you need to make changes, you will need to cancel and resubmit.
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                                </a>
                                <button type="submit" class="btn bg-info">
                                    <i class="ti ti-check me-1"></i>Submit Pre-Enrollment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade_level');
    const strandSelect = document.getElementById('strand_id');
    const sectionSelect = document.getElementById('section_id');

    function loadSections() {
        const gradeLevel = gradeSelect.value;
        const strandId = strandSelect.value;

        if (!gradeLevel || !strandId) {
            sectionSelect.innerHTML = '<option value="">No preference</option>';
            return;
        }

        // Show loading
        sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
        sectionSelect.disabled = true;

        // Fetch sections
        fetch('<?php echo e(route("student.pre-enrollment.sections")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                grade_level: gradeLevel,
                strand_id: strandId
            })
        })
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">No preference</option>';
            
            if (data.sections && data.sections.length > 0) {
                data.sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.display;
                    sectionSelect.appendChild(option);
                });
            }
            
            sectionSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            sectionSelect.disabled = false;
        });
    }

    gradeSelect.addEventListener('change', loadSections);
    strandSelect.addEventListener('change', loadSections);

    // Load sections on page load if values are pre-selected
    if (gradeSelect.value && strandSelect.value) {
        loadSections();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/student/pre_enrollment/index.blade.php ENDPATH**/ ?>