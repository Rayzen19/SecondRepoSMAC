

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Student Record: <?php echo e($student->last_name); ?>, <?php echo e($student->first_name); ?> <?php echo e($student->middle_name); ?></h4>
        <div class="text-muted small">Class Record Details</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Publish/Unpublish Grades Button -->
        <form action="<?php echo e(route('teacher.class-records.toggle-publication', $assignment->id)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <?php if($assignment->grades_published): ?>
                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to unpublish grades? Students will no longer be able to view their grades.')">
                    <i class="ti ti-eye-off me-1"></i> Unpublish Grades
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to publish grades? Students will be able to view their grades.')">
                    <i class="ti ti-eye me-1"></i> Publish Grades
                </button>
            <?php endif; ?>
        </form>
        <div>
            <a href="<?php echo e(route('teacher.class-records.show', $assignment)); ?>" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i> Back to Class Record</a>
        </div>
        <div class="d-flex align-items-center gap-2">
            
            <div>
                <?php
                    $defaultLevels = ['11','12'];
                    $levels = $gradeLevels ?? $defaultLevels;
                    $selectedGrade = request()->query('grade_level');
                    $selectedTerm = request()->query('term');
                ?>
                <select id="filterGradeLevelStudent" class="form-select form-select-sm">
                    <option value="">All Grades</option>
                    <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lvl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($lvl); ?>" <?php echo e($selectedGrade == $lvl ? 'selected' : ''); ?>><?php echo e($lvl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div>
                <select id="filterTermStudent" class="form-select form-select-sm">
                    <option value="">All Terms</option>
                    <option value="midterm" <?php echo e($selectedTerm == 'midterm' ? 'selected' : ''); ?>>Midterm</option>
                    <option value="finals" <?php echo e($selectedTerm == 'finals' ? 'selected' : ''); ?>>Finals</option>
                </select>
            </div>
        </div>
    </div>
</div>

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
        <strong>Grades Not Published:</strong> Students cannot view their grades yet. Click "Publish Grades" when ready.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">Strand</div><div class="fw-semibold"><?php echo e($details['strand'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Section</div><div class="fw-semibold"><?php echo e($details['section'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Subject</div><div class="fw-semibold"><?php echo e($details['subject'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Subject Teacher</div><div class="fw-semibold"><?php echo e($details['subject_teacher'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">Adviser</div><div class="fw-semibold"><?php echo e($details['adviser'] ?? '—'); ?></div></div>
            <div class="col-md-3"><div class="text-muted small">School Year</div><div class="fw-semibold"><?php echo e($details['school_year'] ?? '—'); ?></div></div>
        </div>
    </div>
</div>

<?php if($selectedTerm): ?>
<div class="mb-3">
    <h3 class="mb-0 text-primary">
        <?php if($selectedTerm == 'midterm'): ?>
            <i class="ti ti-calendar-event me-2"></i>Midterm
        <?php elseif($selectedTerm == 'finals'): ?>
            <i class="ti ti-calendar-check me-2"></i>Finals
        <?php endif; ?>
    </h3>
    <div class="text-muted small">Viewing <?php echo e(ucfirst($selectedTerm)); ?> grades<?php echo e($selectedGrade ? ' for Grade ' . $selectedGrade : ''); ?></div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Grades</h5>
        <div class="d-flex align-items-center gap-2">
            <?php if(!empty($quartersData)): ?>
                <button type="submit" form="formSaveScores" class="btn bg-info btn-sm"><i class="ti ti-device-floppy me-1"></i> Save Scores</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">There were problems with your submission:</div>
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <form id="formSaveScores" action="<?php echo e(route('teacher.class-records.scores.store', $assignment)); ?>" method="post">
            <?php echo csrf_field(); ?>
        <?php if(empty($quartersData) || count($quartersData) === 0): ?>
            <p class="text-muted mb-0">No assessment data yet for this student.</p>
        <?php else: ?>
            <?php $__currentLoopData = $quartersData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $qKey = $q['key'] ?? 'final';
                    $termKey = ($q['key'] ?? null) === '1st' ? 'first-semester' : ((($q['key'] ?? null) === '2nd') ? 'second-semester' : 'semester-final');
                ?>
                <div class="mb-4" data-quarter="<?php echo e($qKey); ?>" data-term="<?php echo e($termKey); ?>" data-grade="<?php echo e($details['grade'] ?? ''); ?>" data-ww-max="<?php echo e((float)($q['ww']['max_total'] ?? 0)); ?>" data-pt-max="<?php echo e((float)($q['pt']['max_total'] ?? 0)); ?>" data-qa-max="<?php echo e((float)($q['qa']['max_total'] ?? 0)); ?>" data-ww-weight="<?php echo e((float)($weights['ww'] ?? 0)); ?>" data-pt-weight="<?php echo e((float)($weights['pt'] ?? 0)); ?>" data-qa-weight="<?php echo e((float)($weights['qa'] ?? 0)); ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0"><?php echo e($q['label']); ?></h6>
                        <?php
                            // termKey already computed above for use in data attributes
                        ?>
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-muted small me-2">Weights — WW: <?php echo e(rtrim(rtrim(number_format(($weights['ww'] ?? 0) * 100, 2), '0'), '.')); ?>% • PT: <?php echo e(rtrim(rtrim(number_format(($weights['pt'] ?? 0) * 100, 2), '0'), '.')); ?>% • QA: <?php echo e(rtrim(rtrim(number_format(($weights['qa'] ?? 0) * 100, 2), '0'), '.')); ?>%</div>
                            
                            <div class="btn-group btn-group-sm" role="group" aria-label="Add assessment buttons">
                                <button type="button" class="btn btn-outline-primary js-add-assessment" data-cat="written_work">Add WW</button>
                                <button type="button" class="btn btn-outline-primary js-add-assessment" data-cat="performance_task">Add PT</button>
                                <button type="button" class="btn btn-outline-primary js-add-assessment" data-cat="quarterly_assessment">Add QA</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%">Category</th>
                                    <th>Assessments</th>
                                    <th class="text-center" style="width: 10%">Total</th>
                                    <th class="text-center" style="width: 10%">PS</th>
                                    <th class="text-center" style="width: 10%">WS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                    $renderTable = function($items, $cat, $quarterKey) use($fmt, $student){
                                        if (empty($items) || count($items) === 0) return '<span class="text-muted">—</span>';
                                        $html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0 align-middle">';
                                        $html .= '<thead><tr>'
                                            .'<th class="text-center" style="width:6%">#</th>'
                                            .'<th>Name</th>'
                                            .'<th class="text-center" style="width:12%">Date</th>'
                                            .'<th class="text-center" style="width:10%">Score</th>'
                                            .'<th class="text-center" style="width:10%">Over</th>'
                                            .'</tr></thead><tbody>';
                                        foreach ($items as $i => $it) {
                                            $idx = $i + 1;
                                            $name = htmlspecialchars($it['name'] ?? '');
                                            $date = !empty($it['date']) ? htmlspecialchars($it['date']) : '—';
                                            $scoreVal = isset($it['score']) ? (float)$it['score'] : 0;
                                            $maxVal = isset($it['max']) ? (float)$it['max'] : 0;
                                            $input = '<input type="number" name="scores['.($it['id'] ?? 0).']['.$student->id.']" class="form-control form-control-sm text-center js-score-input" value="'.$scoreVal.'" min="0" max="'.$maxVal.'" step="1" data-cat="'.$cat.'" data-quarter="'.$quarterKey.'">';
                                            $max = $fmt($maxVal);
                                            $desc = !empty($it['description']) ? '<div class="text-muted small">'.htmlspecialchars($it['description']).'</div>' : '';
                                            // edit and delete buttons
                                            $editBtn = '';
                                            $deleteBtn = '';
                                            if (!empty($it['id'])) {
                                                $editUrl = route('teacher.class-records.assessments.update', ['assignment' => request()->route('assignment')->id ?? 0, 'subjectRecord' => $it['id']]);
                                                $editBtn = '<button type="button" class="btn btn-sm btn-outline-secondary js-edit-assessment" data-url="'.$editUrl.'" data-id="'.$it['id'].'" data-name="'.htmlspecialchars($it['name'] ?? '').'" data-desc="'.htmlspecialchars($it['description'] ?? '').'" data-date="'.($it['date'] ?? '').'" data-max="'.($it['max'] ?? '').'" style="margin-left:8px;">Edit</button>';
                                                $deleteUrl = route('teacher.class-records.assessments.destroy', ['assignment' => request()->route('assignment')->id ?? 0, 'subjectRecord' => $it['id']]);
                                                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger js-delete-assessment" data-url="'.$deleteUrl.'" data-id="'.$it['id'].'" data-name="'.htmlspecialchars($it['name'] ?? '').'" style="margin-left:8px;">Delete</button>';
                                            }

                                            $html .= '<tr>'
                                                .'<td class="text-center">'.$idx.'</td>'
                                                .'<td>'.$name.$desc.$editBtn.$deleteBtn.'</td>'
                                                .'<td class="text-center">'.$date.'</td>'
                                                .'<td class="text-center">'.$input.'</td>'
                                                .'<td class="text-center">'.$max.'</td>'
                                                .'</tr>';
                                        }
                                        $html .= '</tbody></table></div>';
                                        return $html;
                                    };
                                ?>
                                <tr>
                                    <td class="text-center fw-semibold">Written Work</td>
                                    <td><?php echo $renderTable($q['ww']['records'], 'ww', $qKey); ?></td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="raw"><?php echo e($fmt($q['ww']['raw_total'])); ?> / <?php echo e($fmt($q['ww']['max_total'])); ?></td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="ps"><?php echo e(isset($q['ww']['ps']) ? $fmt($q['ww']['ps']) : '—'); ?></td>
                                    <td class="text-center js-total" data-cat="ww" data-kind="ws"><?php echo e(isset($q['ww']['ws']) ? $fmt($q['ww']['ws']) : '—'); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Performance Task</td>
                                    <td><?php echo $renderTable($q['pt']['records'], 'pt', $qKey); ?></td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="raw"><?php echo e($fmt($q['pt']['raw_total'])); ?> / <?php echo e($fmt($q['pt']['max_total'])); ?></td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="ps"><?php echo e(isset($q['pt']['ps']) ? $fmt($q['pt']['ps']) : '—'); ?></td>
                                    <td class="text-center js-total" data-cat="pt" data-kind="ws"><?php echo e(isset($q['pt']['ws']) ? $fmt($q['pt']['ws']) : '—'); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Quarterly Assessment</td>
                                    <td><?php echo $renderTable($q['qa']['records'], 'qa', $qKey); ?></td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="raw"><?php echo e($fmt($q['qa']['raw_total'])); ?> / <?php echo e($fmt($q['qa']['max_total'])); ?></td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="ps"><?php echo e(isset($q['qa']['ps']) ? $fmt($q['qa']['ps']) : '—'); ?></td>
                                    <td class="text-center js-total" data-cat="qa" data-kind="ws"><?php echo e(isset($q['qa']['ws']) ? $fmt($q['qa']['ws']) : '—'); ?></td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-center fw-semibold">Initial Grade</td>
                                    <?php $initial = $q['initial']['total'] ?? 0; ?>
                                    <td colspan="2" class="fw-semibold js-initial"><?php echo e($fmt($initial)); ?></td>
                                    <td colspan="1" class="text-center fw-semibold">Description</td>
                                    <td class="text-center fw-semibold js-description"><?php echo e($q['initial']['description'] ?? '—'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn bg-info"><i class="ti ti-device-floppy me-1"></i> Save Scores</button>
            </div>
        <?php endif; ?>
        </form>

        
        <?php if(!empty($quartersData) && count($quartersData) >= 2): ?>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Final Grade Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" style="font-size: 19px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 25%">Semester</th>
                                    <th class="text-center" style="width: 25%">Grade</th>
                                    <th class="text-center" style="width: 25%">Description</th>
                                    <th class="text-center" style="width: 25%">Computation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $fmt = function($n){ return isset($n) && $n !== null ? rtrim(rtrim(number_format($n, 2), '0'), '.') : '—'; };
                                    $firstSemGrade = $semesterGrades['1st'] ?? null;
                                    $secondSemGrade = $semesterGrades['2nd'] ?? null;
                                    
                                    $getDesc = function($grade) {
                                        if ($grade === null) return '—';
                                        if ($grade > 89) return 'Outstanding';
                                        if ($grade > 84) return 'Very Satisfactory';
                                        if ($grade > 79) return 'Satisfactory';
                                        if ($grade > 74) return 'Fairly Satisfactory';
                                        if ($grade > 59) return 'Did Not Meet Expectations';
                                        return '0';
                                    };
                                ?>
                                <tr>
                                    <td class="text-center fw-semibold">First Semester</td>
                                    <td class="text-center fw-semibold js-first-sem-grade-summary"><?php echo e($fmt($firstSemGrade)); ?></td>
                                    <td class="text-center js-first-sem-desc-summary"><?php echo e($getDesc($firstSemGrade)); ?></td>
                                    <td class="text-center text-muted">From First Semester Initial Grade</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold">Second Semester</td>
                                    <td class="text-center fw-semibold js-second-sem-grade-summary"><?php echo e($fmt($secondSemGrade)); ?></td>
                                    <td class="text-center js-second-sem-desc-summary"><?php echo e($getDesc($secondSemGrade)); ?></td>
                                    <td class="text-center text-muted">From Second Semester Initial Grade</td>
                                </tr>
                                <tr class="table-primary">
                                    <td class="text-center fw-bold">Final Grade</td>
                                    <td class="text-center fw-bold js-final-grade-summary"><?php echo e($fmt($finalGrade)); ?></td>
                                    <td class="text-center fw-bold js-final-desc-summary"><?php echo e($finalDescription ?? '—'); ?></td>
                                    <td class="text-center js-final-computation-summary">
                                        <?php if($firstSemGrade !== null && $secondSemGrade !== null): ?>
                                            (<?php echo e($fmt($firstSemGrade)); ?> + <?php echo e($fmt($secondSemGrade)); ?>) ÷ 2 = <?php echo e($fmt($finalGrade)); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    function fmt(n){
        if(n===null || isNaN(n)) return '—';
        const s = Number(n).toFixed(2).replace(/\.00$/, '');
        return s.replace(/(\.[1-9])0$/, '$1');
    }

    function getDescription(grade) {
        if (grade === null || isNaN(grade)) return '—';
        if (grade > 89) return 'Outstanding';
        if (grade > 84) return 'Very Satisfactory';
        if (grade > 79) return 'Satisfactory';
        if (grade > 74) return 'Fairly Satisfactory';
        if (grade > 59) return 'Did Not Meet Expectations';
        return '0';
    }

    const semesterGrades = {
        '1st': null,
        '2nd': null
    };

    function updateFinalGrade() {
        const firstSem = semesterGrades['1st'];
        const secondSem = semesterGrades['2nd'];

        const firstSemCell = document.querySelector('.js-first-sem-grade-summary');
        const secondSemCell = document.querySelector('.js-second-sem-grade-summary');
        const firstDescCell = document.querySelector('.js-first-sem-desc-summary');
        const secondDescCell = document.querySelector('.js-second-sem-desc-summary');
        const finalGradeCell = document.querySelector('.js-final-grade-summary');
        const finalDescCell = document.querySelector('.js-final-desc-summary');
        const finalCompCell = document.querySelector('.js-final-computation-summary');

        if (firstSemCell && firstSem !== null) {
            firstSemCell.textContent = fmt(firstSem);
            if (firstDescCell) firstDescCell.textContent = getDescription(firstSem);
        }

        if (secondSemCell && secondSem !== null) {
            secondSemCell.textContent = fmt(secondSem);
            if (secondDescCell) secondDescCell.textContent = getDescription(secondSem);
        }

        if (finalGradeCell && firstSem !== null && secondSem !== null) {
            const finalGrade = (firstSem + secondSem) / 2;
            finalGradeCell.textContent = fmt(finalGrade);
            if (finalDescCell) finalDescCell.textContent = getDescription(finalGrade);
            if (finalCompCell) finalCompCell.textContent = `(${fmt(firstSem)} + ${fmt(secondSem)}) ÷ 2 = ${fmt(finalGrade)}`;
        }
    }

    function recompute(block){
        const quarter = block.getAttribute('data-quarter');
        const wwMax = parseFloat(block.getAttribute('data-ww-max')||'0')||0;
        const ptMax = parseFloat(block.getAttribute('data-pt-max')||'0')||0;
        const qaMax = parseFloat(block.getAttribute('data-qa-max')||'0')||0;
        const wWW = parseFloat(block.getAttribute('data-ww-weight')||'0')||0;
        const wPT = parseFloat(block.getAttribute('data-pt-weight')||'0')||0;
        const wQA = parseFloat(block.getAttribute('data-qa-weight')||'0')||0;

        const cats = [
            {key:'ww', max: wwMax, weight: wWW},
            {key:'pt', max: ptMax, weight: wPT},
            {key:'qa', max: qaMax, weight: wQA},
        ];

        let initial = 0;
        cats.forEach(c => {
            let raw = 0;
            block.querySelectorAll(`input.js-score-input[data-cat="${c.key}"]`).forEach(inp => {
                const v = parseFloat(inp.value);
                if(!isNaN(v)) raw += v;
            });
            const ps = c.max > 0 ? (raw / c.max) * 100 : null;
            const ws = ps !== null ? ps * c.weight : null;
            if(ws !== null) initial += ws;
            const rawCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="raw"]`);
            const psCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="ps"]`);
            const wsCell = block.querySelector(`.js-total[data-cat="${c.key}"][data-kind="ws"]`);
            if(rawCell){ rawCell.textContent = `${raw>0?fmt(raw):'—'}${c.max>0?` / ${fmt(c.max)}`:''}`; }
            if(psCell){ psCell.textContent = ps!==null?fmt(ps):'—'; }
            if(wsCell){ wsCell.textContent = ws!==null?fmt(ws):'—'; }
        });

        const initCell = block.querySelector('.js-initial');
        if(initCell) initCell.textContent = initial>0?fmt(initial):'—';
        const descCell = block.querySelector('.js-description');
        if(descCell){
            descCell.textContent = getDescription(initial);
        }

        // Store semester grade
        if (quarter === '1st' || quarter === '2nd') {
            semesterGrades[quarter] = initial;
        }

        // Update final grade if both semesters have grades
        updateFinalGrade();
    }

    document.querySelectorAll('.mb-4[data-quarter]').forEach(block => {
        block.addEventListener('input', e => {
            const inp = e.target.closest('input.js-score-input');
            if(!inp) return;
            // clamp to min/max and digits only
            const min = parseInt(inp.getAttribute('min')||'0', 10);
            const max = parseInt(inp.getAttribute('max')||'0', 10);
            inp.value = (inp.value||'').replace(/[^0-9]/g, '');
            let v = inp.value === '' ? 0 : parseInt(inp.value, 10);
            if(v < min) v = min;
            if(max>0 && v>max) v = max;
            inp.value = String(v);
            recompute(block);
        });
        // initial compute on page load
        recompute(block);
    });

    // Initialize final grade on page load
    updateFinalGrade();

    // Add-record UI removed for student view
    // Wire up add-assessment buttons to open the modal with proper category/term/grade
    document.querySelectorAll('.js-add-assessment').forEach(btn => {
        btn.addEventListener('click', function(){
            const cat = this.getAttribute('data-cat') || 'written_work';
            const modalTitle = document.getElementById('modalAddRecordHeaderTitle');
            const modalCatInput = document.getElementById('modalAddRecordCategory');

            // find nearest quarter block for context
            const quarterBlock = this.closest('.mb-4[data-quarter]');
            let grade = '';
            let term = '';
            let termType = '';
            if(quarterBlock){
                grade = quarterBlock.getAttribute('data-grade') || '';
                term = quarterBlock.getAttribute('data-term') || quarterBlock.getAttribute('data-quarter') || '';
            }

            // Get current selected term from filter
            const termSelect = document.getElementById('filterTermStudent');
            if (termSelect && termSelect.value) {
                termType = termSelect.value; // 'midterm' or 'finals'
            }

            // Get current selected grade from filter
            const gradeSelect = document.getElementById('filterGradeLevelStudent');
            let selectedGrade = '';
            if (gradeSelect && gradeSelect.value) {
                selectedGrade = gradeSelect.value;
            }

            if(modalTitle) modalTitle.textContent = 'Add ' + (cat === 'written_work' ? 'Written Work' : (cat === 'performance_task' ? 'Performance Task' : 'Quarterly Assessment'));
            if(modalCatInput) modalCatInput.value = cat;

            // populate hidden inputs
            const modalTermHidden = document.getElementById('modalAddRecordTerm');
            const modalTermTypeHidden = document.getElementById('modalAddRecordTermType');
            const modalGradeLevelHidden = document.getElementById('modalAddRecordGradeLevel');
            
            if(modalTermHidden) modalTermHidden.value = term || '';
            if(modalTermTypeHidden) modalTermTypeHidden.value = termType || '';
            if(modalGradeLevelHidden) modalGradeLevelHidden.value = selectedGrade || '';

            // Pre-select the term in the visible select dropdown
            const modalTermTypeSelect = document.querySelector('#modalAddRecordHeader select[name="term_type"]');
            if (modalTermTypeSelect && termType) {
                modalTermTypeSelect.value = termType;
            }

            // Pre-select the grade level in the visible select dropdown
            const modalGradeLevelSelect = document.querySelector('#modalAddRecordHeader select[name="grade_level"]');
            if (modalGradeLevelSelect && selectedGrade) {
                modalGradeLevelSelect.value = selectedGrade;
            }

            const modalEl = document.getElementById('modalAddRecordHeader');
            if(modalEl){
                const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                bsModal.show();
            }
        });
    });

    // Edit assessment modal wiring
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.js-edit-assessment');
        if(!btn) return;
        const url = btn.getAttribute('data-url');
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || '';
        const desc = btn.getAttribute('data-desc') || '';
        const date = btn.getAttribute('data-date') || '';
        const max = btn.getAttribute('data-max') || '';

        const form = document.getElementById('formEditAssessment');
        if(form){
            form.action = url;
            // set CSRF token input if missing (Blade <?php echo csrf_field(); ?> renders one)
            const nameInp = document.getElementById('editAssessmentName');
            const descInp = document.getElementById('editAssessmentDescription');
            const dateInp = document.getElementById('editAssessmentDate');
            const maxInp = document.getElementById('editAssessmentMax');
            if(nameInp) nameInp.value = name;
            if(descInp) descInp.value = desc;
            if(dateInp) dateInp.value = date;
            if(maxInp) maxInp.value = max;
        }

        const modalEl = document.getElementById('modalEditAssessment');
        if(modalEl){
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }
    });

    // Delete assessment wiring with AJAX
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.js-delete-assessment');
        if(!btn) return;
        
        const url = btn.getAttribute('data-url');
        const name = btn.getAttribute('data-name') || 'this assessment';
        
        if(!confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
            return;
        }
        
        // Get CSRF token
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            const existingToken = document.querySelector('input[name="_token"]');
            if (existingToken) {
                csrfToken = existingToken.value;
            }
        }
        
        // Disable button to prevent double-clicks
        btn.disabled = true;
        btn.textContent = 'Deleting...';
        
        // Use fetch API for AJAX request
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.status === 419) {
                alert('Your session has expired. Please refresh the page and try again.');
                window.location.reload();
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                // Show success message and reload
                alert(data.message || 'Assessment deleted successfully.');
                window.location.reload();
            } else {
                alert('Failed to delete assessment. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Delete';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please refresh the page and try again.');
            btn.disabled = false;
            btn.textContent = 'Delete';
        });
    });

    // Filter selects (student view)
    (function(){
        const gradeSel = document.getElementById('filterGradeLevelStudent');
        const termSel = document.getElementById('filterTermStudent');
        function applyFilters(){
            if(!gradeSel && !termSel) return;
            const params = new URLSearchParams(window.location.search);
            if(gradeSel){
                if(gradeSel.value) params.set('grade_level', gradeSel.value); else params.delete('grade_level');
            }
            if(termSel){
                if(termSel.value) params.set('term', termSel.value); else params.delete('term');
            }
            // Preserve other query params
            const target = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
            window.location.href = target;
        }
        if(gradeSel) gradeSel.addEventListener('change', applyFilters);
        if(termSel) termSel.addEventListener('change', applyFilters);
    })();
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('modals'); ?>
<div class="modal fade" id="modalAddRecordHeader" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddRecordHeaderTitle">Add Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('teacher.class-records.assessments.store', $assignment)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
                <input type="hidden" name="category" value="written_work" id="modalAddRecordCategory">
                <input type="hidden" name="term" value="" id="modalAddRecordTerm">
                <input type="hidden" name="term_type" value="" id="modalAddRecordTermType">
                <input type="hidden" name="grade_level" value="" id="modalAddRecordGradeLevel">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Assessment name (e.g. WW1)">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional description"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="date_given" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Over (Max Score)</label>
                            <input type="number" name="max_score" class="form-control" min="1" step="1" value="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select" required>
                                <option value="">Select Grade</option>
                                <option value="11">Grade 11</option>
                                <option value="12">Grade 12</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Term</label>
                            <select name="term_type" class="form-select" required>
                                <option value="">Select Term</option>
                                <option value="midterm">Midterm</option>
                                <option value="finals">Finals</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-info">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('modals'); ?>
<div class="modal fade" id="modalEditAssessment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAssessment" action="#" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="editAssessmentName" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editAssessmentDescription" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="date_given" id="editAssessmentDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Over (Max Score)</label>
                            <input type="number" name="max_score" id="editAssessmentMax" class="form-control" min="1" step="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-info">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function(){
        const form = document.getElementById('formNewEntryStudent');
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();

            const grade = document.getElementById('newEntryGradeLevelStudent')?.value || '';
            const term = document.getElementById('newEntryTermStudent')?.value || '';

            // Hide modal first
            const modalEl = document.getElementById('modalNewEntry');
            if (modalEl) {
                const mb = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                mb.hide();
            }

            // Prepare POST to create placeholders for this student+term
            const url = "<?php echo e(route('teacher.class-records.students.add-term', ['assignment' => $assignment->id, 'student' => $student->id])); ?>";
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>';

            try {
                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ grade_level: grade, term: term })
                });

                const data = await resp.json().catch(() => ({}));
                if (resp.ok && data.success) {
                    // reload to show newly-created records
                    window.location.reload();
                } else {
                    alert((data && data.message) ? data.message : 'Could not create placeholder assessments.');
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                alert('Network error while creating placeholder assessments.');
            }
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('modals'); ?>
<!-- New Entry modal for student view -->
<div class="modal fade" id="modalNewEntry" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNewEntryStudent">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level" id="newEntryGradeLevelStudent" class="form-select" required>
                            <option value="">Select grade level</option>
                            <?php
                                $defaultLevels = ['7','8','9','10','11','12'];
                                $levels = $gradeLevels ?? $defaultLevels;
                            ?>
                            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lvl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($lvl); ?>"><?php echo e($lvl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Term</label>
                        <select name="term_type" id="newEntryTermStudent" class="form-select" required>
                            <option value="midterm">Midterm</option>
                            <option value="finals">Finals</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-info">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('teacher.components.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/teacher/class_records/student_show.blade.php ENDPATH**/ ?>