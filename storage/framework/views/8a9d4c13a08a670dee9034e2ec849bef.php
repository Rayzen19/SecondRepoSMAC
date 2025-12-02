

<?php $__env->startSection('breadcrumb'); ?>
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <h2 class="mb-1 h4">
                    <i class="ti ti-user-plus me-2"></i>
                    New Enrollment
                </h2>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="ti ti-smart-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.student-enrollments.index')); ?>">Enrollments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
        <div class="mb-2">
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary d-flex align-items-center"><i class="ti ti-arrow-back-up me-2"></i>Cancel</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form method="POST" action="<?php echo e(route('admin.student-enrollments.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Select Student --</option>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e(old('student_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->student_number); ?> — <?php echo e($s->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select <?php $__errorArgs = ['academic_year_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Select Academic Year --</option>
                            <?php $__currentLoopData = $academicYears->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ay->id); ?>" <?php echo e((old('academic_year_id', request('academic_year_id')) == $ay->id) ? 'selected' : ''); ?>><?php echo e($ay->display_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['academic_year_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Strand (optional)</label>
                        <select name="strand_id" class="form-select <?php $__errorArgs = ['strand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Select Strand --</option>
                            <?php $__currentLoopData = $strands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->id); ?>" <?php echo e(old('strand_id') == $st->id ? 'selected' : ''); ?>><?php echo e($st->code); ?> — <?php echo e($st->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['strand_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assigned Section</label>
                        <select name="academic_year_strand_section_id" class="form-select <?php $__errorArgs = ['academic_year_strand_section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Select AY-Strand-Section --</option>
                            <?php $__currentLoopData = $strandSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($assn->id); ?>" <?php echo e(old('academic_year_strand_section_id') == $assn->id ? 'selected' : ''); ?>>
                                <?php echo e($assn->academicYear->display_name); ?> — <?php echo e($assn->strand?->code); ?> — <?php echo e($assn->section?->grade); ?> <?php echo e($assn->section?->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['academic_year_strand_section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__currentLoopData = ['enrolled','dropped','completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>" <?php echo e(old('status','enrolled') == $st ? 'selected' : ''); ?>><?php echo e(ucwords($st)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn bg-info d-flex align-items-center"><i class="ti ti-device-floppy me-2"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function() {
        const aySelect = document.querySelector('select[name="academic_year_id"]');
        const strandSelect = document.querySelector('select[name="strand_id"]');
        const sectionSelect = document.querySelector('select[name="academic_year_strand_section_id"]');

        function setStrandOptions(options) {
            const current = strandSelect.value;
            strandSelect.innerHTML = '<option value="">-- Select Strand --</option>';
            options.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = o.text;
                if (String(o.id) === String(current)) opt.selected = true;
                strandSelect.appendChild(opt);
            });
        }

        function setSectionOptions(options) {
            const current = sectionSelect.value;
            sectionSelect.innerHTML = '<option value="">-- Select AY-Strand-Section --</option>';
            options.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = o.text;
                if (String(o.id) === String(current)) opt.selected = true;
                sectionSelect.appendChild(opt);
            });
        }

        async function refreshSections() {
            const ay = aySelect.value;
            if (!ay) {
                setSectionOptions([]);
                return;
            }
            const strand = strandSelect.value || '';
            try {
                const url = new URL("<?php echo e(route('admin.student-enrollments.sections.options')); ?>", window.location.origin);
                url.searchParams.set('academic_year_id', ay);
                if (strand) url.searchParams.set('strand_id', strand);
                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Failed to load sections');
                const json = await res.json();
                setSectionOptions(json.data || []);
            } catch (e) {
                console.error(e);
                setSectionOptions([]);
            }
        }

        async function refreshStrands() {
            const ay = aySelect.value;
            if (!ay) {
                setStrandOptions([]);
                return;
            }
            try {
                const url = new URL("<?php echo e(route('admin.student-enrollments.strands.options')); ?>", window.location.origin);
                url.searchParams.set('academic_year_id', ay);
                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Failed to load strands');
                const json = await res.json();
                setStrandOptions(json.data || []);
            } catch (e) {
                console.error(e);
                setStrandOptions([]);
            }
        }

        aySelect && aySelect.addEventListener('change', async () => {
            await refreshStrands();
            await refreshSections();
        });
        strandSelect && strandSelect.addEventListener('change', refreshSections);

        // initial population if AY preset
        if (aySelect && aySelect.value) {
            (async () => {
                await refreshStrands();
                await refreshSections();
            })();
        }
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/admin/student_enrollments/create.blade.php ENDPATH**/ ?>