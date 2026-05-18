<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bx-edit text-gold me-2'></i> تعديل المادة الدراسية</h1>
    <a href="<?= base_url('admin/subjects/index/' . $class_id) ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للمواد
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="fw-bold text-nile mb-1"><?= $subject['name'] ?></h5>
                <p class="text-muted small mb-0">تحديث تفاصيل المادة أو تغيير المعلم المسؤول</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/subjects/edit/' . $subject['subject_id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">اسم المادة</label>
                            <input type="text" name="name" class="form-control" value="<?= $subject['name'] ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">المعلم</label>
                            <select name="teacher_id" class="form-select" required>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['teacher_id'] ?>" <?= ($teacher['teacher_id'] == $subject['teacher_id']) ? 'selected' : '' ?>>
                                        <?= $teacher['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">الدرجة الكلية</label>
                            <input type="number" name="total_mark" class="form-control" value="<?= $subject['total_mark'] ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">درجة النجاح</label>
                            <input type="number" name="pass_mark" class="form-control" value="<?= $subject['pass_mark'] ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">ترتيب العرض (Order)</label>
                            <input type="number" name="order" class="form-control" value="<?= $subject['order'] ?>" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-nile mb-0"><i class='bx bx-list-check text-gold me-1'></i> توزيع الدرجات (تقسيمة المواد)</h6>
                            <button type="button" class="btn btn-sm btn-soft-primary rounded-pill px-3" onclick="addDistributionRow()">
                                <i class='bx bx-plus-circle me-1'></i> إضافة عنصر توزيع
                            </button>
                        </div>
                        
                        <div id="distributionContainer">
                            <?php if (empty($distributions)): ?>
                                <div class="distribution-row row g-2 mb-2">
                                    <div class="col-7">
                                        <input type="text" name="dist_name[]" class="form-control form-control-sm" placeholder="اسم العنصر (مثال: أعمال سنة)">
                                    </div>
                                    <div class="col-4">
                                        <input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" placeholder="الدرجة">
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class='bx bx-trash'></i></button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($distributions as $dist): ?>
                                    <div class="distribution-row row g-2 mb-2">
                                        <div class="col-7">
                                            <input type="text" name="dist_name[]" class="form-control form-control-sm" value="<?= $dist['name'] ?>" placeholder="اسم العنصر">
                                        </div>
                                        <div class="col-4">
                                            <input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" value="<?= $dist['max_mark'] ?>" placeholder="الدرجة">
                                        </div>
                                        <div class="col-1 text-end">
                                            <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class='bx bx-trash'></i></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><i class='bx bx-info-circle me-1'></i> ملاحظة: اترك الاسم فارغاً لحذف العنصر عند الحفظ.</small>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg">
                            <i class='bx bxs-save me-1'></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-soft-primary { background: rgba(25, 42, 86, 0.05); color: #192a56; border: none; }
    .btn-soft-primary:hover { background: #192a56; color: #fff; }
</style>

<script>
    function addDistributionRow() {
        const container = document.getElementById('distributionContainer');
        const row = document.createElement('div');
        row.className = 'distribution-row row g-2 mb-2 animate__animated animate__fadeIn';
        row.innerHTML = `
            <div class="col-7">
                <input type="text" name="dist_name[]" class="form-control form-control-sm" placeholder="اسم العنصر">
            </div>
            <div class="col-4">
                <input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" placeholder="الدرجة">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class='bx bx-trash'></i></button>
            </div>
        `;
        container.appendChild(row);
    }

    function removeRow(button) {
        button.closest('.distribution-row').remove();
    }
</script>
<?= $this->endSection() ?>
