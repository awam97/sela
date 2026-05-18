<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bx-edit text-gold me-2'></i> رصد الحضور والغياب</h1>
        <p class="text-muted small ps-5 mb-0">
            <?= $current_class['name'] ?> - <?= $current_section['name'] ?> | 
            <span class="text-gold fw-bold"><i class='bx bx-calendar me-1'></i> <?= $selected['date'] ?></span>
        </p>
    </div>
    <a href="<?= base_url('admin/attendance') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> تغيير الصف/التاريخ
    </a>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert-info border-0 shadow-sm rounded-4 text-center py-5 animate__animated animate__fadeIn">
        <i class='bx bx-info-circle fs-1 d-block mb-3'></i>
        <h5>لا يوجد طلاب مقيدون في هذا الفصل حالياً</h5>
        <p class="mb-0">يرجى التأكد من اختيار الفصل الصحيح أو مراجعة قيود الطلاب.</p>
    </div>
<?php else: ?>
    <div class="card-sela gold shadow-lg animate__animated animate__fadeIn">
        <div class="card-body p-0">
            <form action="<?= base_url('admin/attendance/save') ?>" method="POST" id="attendanceForm">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selected['class_id'] ?>">
                <input type="hidden" name="section_id" value="<?= $selected['section_id'] ?>">
                <input type="hidden" name="date" value="<?= $selected['date'] ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sela-attendance">
                        <thead class="bg-soft-gold">
                            <tr>
                                <th class="ps-4 py-3 text-nile" style="width: 300px;">اسم الطالب</th>
                                <th class="text-center" style="width: 400px;">حالة الحضور</th>
                                <th class="pe-4">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <?php 
                                    $currentStatus = $attendanceMap[$s['student_id']] ?? 1; // Default to Present (1)
                                    $currentNote = $notesMap[$s['student_id']] ?? '';
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-small me-3">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($s['name']) ?>&background=192A56&color=fff&size=40" class="rounded-circle shadow-sm">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-nile mb-0"><?= $s['name'] ?></div>
                                                <small class="text-muted" style="font-size: 0.75rem;">ID: #<?= $s['student_id'] ?></small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="student_id[]" value="<?= $s['student_id'] ?>">
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="status-toggle-group d-flex justify-content-center gap-2">
                                            <input type="radio" name="status[<?= $s['student_id'] ?>]" id="p_<?= $s['student_id'] ?>" value="1" class="btn-check" <?= $currentStatus == 1 ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-success rounded-pill px-3 py-1 btn-status" for="p_<?= $s['student_id'] ?>">حاضر</label>

                                            <input type="radio" name="status[<?= $s['student_id'] ?>]" id="a_<?= $s['student_id'] ?>" value="2" class="btn-check" <?= $currentStatus == 2 ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-danger rounded-pill px-3 py-1 btn-status" for="a_<?= $s['student_id'] ?>">غائب</label>

                                            <input type="radio" name="status[<?= $s['student_id'] ?>]" id="l_<?= $s['student_id'] ?>" value="3" class="btn-check" <?= $currentStatus == 3 ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-warning rounded-pill px-3 py-1 btn-status" for="l_<?= $s['student_id'] ?>">متأخر</label>

                                            <input type="radio" name="status[<?= $s['student_id'] ?>]" id="e_<?= $s['student_id'] ?>" value="4" class="btn-check" <?= $currentStatus == 4 ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-info rounded-pill px-3 py-1 btn-status" for="e_<?= $s['student_id'] ?>">بعذر</label>
                                        </div>
                                    </td>

                                    <td class="pe-4">
                                        <div class="note-input-wrapper">
                                            <input type="text" name="notes[<?= $s['student_id'] ?>]" 
                                                   class="form-control note-input border-0 bg-transparent" 
                                                   value="<?= $currentNote ?>" placeholder="إضافة ملاحظة...">
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-top text-center bg-light bg-opacity-50 sticky-bottom-custom">
                    <button type="submit" class="btn-sela btn-sela-primary px-5 py-3 shadow-lg hover-up">
                        <i class='bx bxs-save me-2'></i> حفظ سجل الحضور
                    </button>
                    <div class="mt-2 text-muted x-small">
                        <i class='bx bx-info-circle me-1'></i> سيتم تحديث سجلات الحضور لهذا اليوم فور الضغط على الحفظ
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .table-sela-attendance thead th { border-bottom: 2px solid #f0f0f0; font-weight: 800; font-size: 0.85rem; background-color: #fdfaf0; }
        .table-sela-attendance tbody td { padding: 18px 10px; border-bottom: 1px solid #f9f9f9; }
        
        .btn-status { font-size: 0.8rem; font-weight: 600; border-width: 2px; }
        .btn-check:checked + .btn-outline-success { background-color: #28a745; color: #fff; }
        .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: #fff; }
        .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: #fff; }
        .btn-check:checked + .btn-outline-info { background-color: #17a2b8; color: #fff; }
        
        .note-input-wrapper { position: relative; }
        .note-input-wrapper::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 1px; background: #eee; transform: scaleX(0); transition: transform 0.3s; }
        .note-input-wrapper:focus-within::after { transform: scaleX(1); background: var(--secondary-color); }
        .note-input { font-size: 0.85rem; }
        .note-input:focus { border: none !important; box-shadow: none !important; }
        
        .sticky-bottom-custom { position: sticky; bottom: 0; z-index: 100; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; }
        .bg-soft-gold { background-color: #fdfaf0; }
        .hover-up:hover { transform: translateY(-3px); }
        .x-small { font-size: 0.7rem; }
    </style>
<?php endif; ?>
<?= $this->endSection() ?>
