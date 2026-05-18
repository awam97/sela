<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1 fw-bold text-nile"><i class='bx bx-edit text-gold me-2'></i> رصد درجات الطلاب</h1>
        <p class="text-muted small ps-5 mb-0">إدخال نتائج الفترة الدراسية الحالية - <?= $current_year ?? '2025-2026' ?></p>
    </div>
</div>

<div class="card-sela primary shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="<?= base_url('admin/academic/marks') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-nile">الصف الدراسي</label>
                <select name="class_id" id="class_id" class="form-select rounded-3" required onchange="loadFilters(this.value)">
                    <option value="">اختر الصف...</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>" <?= ($selected['class_id'] == $c['class_id'] ? 'selected' : '') ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-nile">الفصل</label>
                <select name="section_id" id="section_id" class="form-select rounded-3" required>
                    <option value="">اختر الفصل...</option>
                    <?php if (isset($sections)): foreach ($sections as $s): ?>
                        <option value="<?= $s['section_id'] ?>" <?= ($selected['section_id'] == $s['section_id'] ? 'selected' : '') ?>><?= $s['name'] ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-nile">المادة الدراسية</label>
                <select name="subject_id" id="subject_id" class="form-select rounded-3" required>
                    <option value="">اختر المادة...</option>
                    <?php if (isset($subjects)): foreach ($subjects as $su): ?>
                        <option value="<?= $su['subject_id'] ?>" <?= ($selected['subject_id'] == $su['subject_id'] ? 'selected' : '') ?>><?= $su['name'] ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-sela btn-sela-primary w-100 py-2">
                    <i class='bx bx-refresh me-1'></i> تحميل
                </button>
            </div>
            <div class="col-md-2">
                <div class="search-wrapper position-relative">
                    <i class='bx bx-search position-absolute text-muted' style="top: 10px; left: 12px;"></i>
                    <input type="text" id="studentSearch" class="form-control rounded-3" placeholder="بحث بالاسم..." style="padding-left: 35px;">
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($selected['class_id']) && !empty($selected['subject_id']) && empty($distribution)): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-4 text-center py-5 mt-4">
        <i class='bx bx-error-circle fs-1 d-block mb-3 text-warning'></i>
        <h5 class="fw-bold">تنبيه: لم يتم تعريف توزيع الدرجات لهذه المادة!</h5>
        <p class="mb-4 text-muted">يرجى ضبط توزيع الدرجات أولاً لكي تتمكن من البدء في عملية الرصد.</p>
        <a href="<?= base_url('admin/subjects/index/' . $selected['class_id']) ?>" class="btn-sela btn-sela-gold px-4">
            <i class='bx bx-cog me-1'></i> إعدادات المادة
        </a>
    </div>
<?php elseif (!empty($students) && !empty($distribution)): ?>
    <div class="card-sela primary shadow-sm overflow-hidden animate__animated animate__fadeIn">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3" style="min-width: 250px;">
                <span class="small fw-bold text-muted">اكتمال الرصد:</span>
                <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px;">
                    <div id="grading-progress" class="progress-bar bg-gold" style="width: 0%"></div>
                </div>
                <span id="progress-percent" class="small fw-bold text-gold">0%</span>
            </div>
            <div class="text-muted small">
                <i class='bx bx-info-circle me-1'></i> استخدم الأسهم للتنقل و <kbd class="bg-light text-dark border p-1 rounded">Ctrl + S</kbd> للحفظ.
            </div>
        </div>

        <div class="card-body p-0">
            <form action="<?= base_url('admin/academic/marks/save') ?>" method="POST" id="marksSaveForm">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selected['class_id'] ?>">
                <input type="hidden" name="section_id" value="<?= $selected['section_id'] ?>">
                <input type="hidden" name="subject_id" value="<?= $selected['subject_id'] ?>">
                
                <?php 
                    $overallTotalPossible = 0;
                    foreach ($distribution as $dist) { $overallTotalPossible += (float)$dist['max_mark']; }
                    $passMark = $current_subject['pass_mark'] ?? ($overallTotalPossible / 2);
                ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="marksTable" style="border-collapse: collapse;">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 text-nile small fw-bold">اسم الطالب</th>
                                <?php foreach ($distribution as $dist): ?>
                                    <th class="text-center" style="width: 120px;">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="text-nile small fw-bold"><?= $dist['name'] ?></span>
                                            <span class="x-small text-gold fw-bold mt-1">من <?= $dist['max_mark'] ?></span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted x-small mt-1 opacity-50" 
                                                    title="تعبئة الدرجة الكاملة" onclick="bulkFillCols('dynamic-mark-<?= $dist['id'] ?>', <?= $dist['max_mark'] ?>)">
                                                تعبئة الكل
                                            </button>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                                <th class="text-center text-nile small fw-bold" style="width: 100px;">المجموع</th>
                                <th class="text-center text-nile small fw-bold" style="width: 80px;">الكاملة</th>
                                <th class="text-center text-nile small fw-bold" style="width: 100px;">الحالة</th>
                                <th class="pe-4 text-nile small fw-bold">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr class="student-row" data-name="<?= strtolower($s['name']) ?>">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold text-nile" style="font-size: 0.9rem;"><?= $s['name'] ?></div>
                                        </div>
                                        <input type="hidden" name="student_id[]" value="<?= $s['student_id'] ?>">
                                    </td>
                                    
                                    <?php 
                                        $savedMarks = json_decode($s['marks_json'] ?? '{}', true);
                                        $currTotal = 0;
                                    ?>
                                    <?php foreach ($distribution as $dist): ?>
                                        <?php 
                                            $val = $savedMarks[$dist['name']] ?? '';
                                            $currTotal += (float)$val;
                                        ?>
                                        <td class="text-center p-1" data-col="dist-<?= $dist['id'] ?>">
                                            <input type="number" step="0.5" name="dynamic_mark[<?= $dist['id'] ?>][]" 
                                                   class="form-control mark-input dynamic-mark-input dynamic-mark-<?= $dist['id'] ?> text-center fw-bold border-0 bg-light rounded-2 py-1 mx-auto" 
                                                   max="<?= $dist['max_mark'] ?>" style="width: 70px; font-size: 0.9rem;"
                                                   value="<?= $val ?>" oninput="calculateTotal(this)" onfocus="this.select()">
                                        </td>
                                    <?php endforeach; ?>

                                    <td class="text-center">
                                        <span class="total-display fw-bold text-nile" style="font-size: 1rem;"><?= $currTotal ?></span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="mark_total[]" class="form-control mark-total-input border-0 bg-transparent text-center text-muted small" 
                                               value="<?= $s['total_possible'] ?? $overallTotalPossible ?>" readonly style="width: 60px;">
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $isPass = $currTotal >= $passMark;
                                            $hasInput = ($currTotal > 0 || (isset($savedMarks) && count($savedMarks) > 0));
                                        ?>
                                        <span class="status-badge <?= !$hasInput ? 'bg-light text-muted' : ($isPass ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger') ?> rounded-pill px-2 py-1 small fw-bold">
                                            <?= !$hasInput ? 'انتظار' : ($isPass ? 'ناجح' : 'راسب') ?>
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <input type="text" name="comment[]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3" 
                                               value="<?= $s['comment'] ?? '' ?>" placeholder="..." style="font-size: 0.8rem;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-0 p-4 text-center">
                    <button type="submit" class="btn-sela btn-sela-primary px-5 py-3 shadow-sm hover-up rounded-pill">
                        <i class='bx bxs-save me-2'></i> حفظ كافة الدرجات المحدثة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .hover-up:hover { transform: translateY(-2px); transition: all 0.2s; }
        .x-small { font-size: 0.7rem; }
        .bg-soft-success { background: #e8f5e9; }
        .bg-soft-danger { background: #ffebee; }
        .mark-input:focus { background-color: #fff9e6 !important; border: 1px solid #d4af37 !important; outline: none; }
        .student-row:hover { background-color: rgba(25, 42, 86, 0.01); }
        .student-row.active-row { background-color: rgba(212, 175, 55, 0.05); }
    </style>
<?php endif; ?>

<script>
    const PASS_MARK = <?= $passMark ?? 50 ?>;

    function calculateTotal(input) {
        const row = input.closest('tr');
        let total = 0;
        let filledCount = 0;
        
        const dynamicInputs = row.querySelectorAll('.dynamic-mark-input');
        dynamicInputs.forEach(inp => {
            const val = parseFloat(inp.value || 0);
            const max = parseFloat(inp.getAttribute('max') || 100);
            if (inp.value !== '') filledCount++;
            
            if (val > max) {
                inp.classList.add('text-danger');
                inp.style.background = '#ffebee';
            } else {
                inp.classList.remove('text-danger');
                inp.style.background = '';
            }
            total += val;
        });

        row.querySelector('.total-display').innerText = total;

        const badge = row.querySelector('.status-badge');
        if (total === 0 && filledCount === 0) {
            badge.className = 'status-badge bg-light text-muted rounded-pill px-2 py-1 small fw-bold';
            badge.innerText = 'انتظار';
        } else if (total >= PASS_MARK) {
            badge.className = 'status-badge bg-soft-success text-success rounded-pill px-2 py-1 small fw-bold';
            badge.innerText = 'ناجح';
        } else {
            badge.className = 'status-badge bg-soft-danger text-danger rounded-pill px-2 py-1 small fw-bold';
            badge.innerText = 'راسب';
        }

        updateOverallProgress();
    }

    document.getElementById('studentSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.student-row').forEach(row => {
            row.style.display = row.getAttribute('data-name').includes(query) ? '' : 'none';
        });
    });

    function updateOverallProgress() {
        const inputs = document.querySelectorAll('.dynamic-mark-input');
        if (inputs.length === 0) return;
        let filled = 0;
        inputs.forEach(i => { if(i.value !== '') filled++; });
        const percent = Math.round((filled / inputs.length) * 100);
        const bar = document.getElementById('grading-progress');
        const txt = document.getElementById('progress-percent');
        if (bar && txt) {
            bar.style.width = percent + '%';
            txt.innerText = percent + '%';
        }
    }

    function bulkFillCols(className, value) {
        if (!confirm('هل أنت متأكد من تعبئة كافة الدرجات العظمى؟')) return;
        document.querySelectorAll('.' + className).forEach(inp => {
            inp.value = value;
            calculateTotal(inp);
        });
    }

    document.addEventListener('keydown', function(e) {
        const active = document.activeElement;
        if (!active || !active.classList.contains('form-control')) return;

        const row = active.closest('tr');
        const allRows = Array.from(document.querySelectorAll('.student-row:not([style*="display: none"])'));
        const rowIndex = allRows.indexOf(row);
        const rowInputs = Array.from(row.querySelectorAll('input:not([type="hidden"])'));
        const inputIndex = rowInputs.indexOf(active);

        let target = null;
        if (e.key === 'ArrowDown' || (e.key === 'Enter' && !active.classList.contains('comment-input'))) {
            e.preventDefault();
            if (rowIndex < allRows.length - 1) {
                target = allRows[rowIndex + 1].querySelectorAll('input:not([type="hidden"])')[inputIndex];
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (rowIndex > 0) {
                target = allRows[rowIndex - 1].querySelectorAll('input:not([type="hidden"])')[inputIndex];
            }
        } else if (e.key === 'ArrowRight' && active.selectionStart === active.value.length) {
            if (inputIndex < rowInputs.length - 1) target = rowInputs[inputIndex + 1];
        } else if (e.key === 'ArrowLeft' && active.selectionStart === 0) {
            if (inputIndex > 0) target = rowInputs[inputIndex - 1];
        } else if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.getElementById('marksSaveForm').submit();
        }

        if (target) {
            target.focus();
            if (target.select) target.select();
        }
    });

    document.addEventListener('focusin', function(e) {
        if (e.target.classList.contains('form-control')) {
            document.querySelectorAll('.student-row').forEach(r => r.classList.remove('active-row'));
            e.target.closest('tr')?.classList.add('active-row');
        }
    });

    updateOverallProgress();

    function loadFilters(classId) {
        if (!classId) return;
        const sSel = document.getElementById('section_id');
        const bSel = document.getElementById('subject_id');
        
        console.log('Loading filters for class:', classId);

        if (sSel) {
            sSel.innerHTML = '<option value="">جاري التحميل...</option>';
            fetch('<?= site_url('admin/students/get_sections') ?>/' + classId)
                .then(r => {
                    if (!r.ok) throw new Error('Network response was not ok');
                    return r.text();
                })
                .then(h => { 
                    sSel.innerHTML = h; 
                    console.log('Sections loaded');
                })
                .catch(err => {
                    console.error('Error loading sections:', err);
                    sSel.innerHTML = '<option value="">خطأ في التحميل</option>';
                });
        }
        if (bSel) {
            bSel.innerHTML = '<option value="">جاري التحميل...</option>';
            fetch('<?= site_url('admin/students/get_subjects') ?>/' + classId)
                .then(r => {
                    if (!r.ok) throw new Error('Network response was not ok');
                    return r.text();
                })
                .then(h => { 
                    bSel.innerHTML = h; 
                    console.log('Subjects loaded');
                })
                .catch(err => {
                    console.error('Error loading subjects:', err);
                    bSel.innerHTML = '<option value="">خطأ في التحميل</option>';
                });
        }
    }
</script>
<?= $this->endSection() ?>
