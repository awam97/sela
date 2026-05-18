<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bx-book-add text-gold me-2'></i> إضافة مادة دراسية جديدة</h1>
    <a href="<?= base_url('admin/subjects/index/' . $class_id) ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للمواد
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg animate__animated animate__fadeInUp">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="fw-bold text-nile mb-0">بيانات المادة</h5>
                <p class="text-muted small mb-0">يرجى تعبئة كافة الحقول المطلوبة بدقة</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/subjects/create/' . $class_id) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">اسم المادة</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class='bx bx-book'></i></span>
                                <input type="text" name="name" class="form-control border-start-0" placeholder="مثلاً: اللغة العربية، الرياضيات..." required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">المعلم</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class='bx bxs-user-voice'></i></span>
                                <select name="teacher_id" class="form-select border-start-0" required>
                                    <option value="" disabled selected>اختر المعلم...</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">الدرجة الكلية</label>
                            <input type="number" name="total_mark" class="form-control" value="100" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">درجة النجاح</label>
                            <input type="number" name="pass_mark" class="form-control" value="50" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">ترتيب العرض (Order)</label>
                            <input type="number" name="order" class="form-control" value="1" required>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <button type="submit" class="btn-sela btn-sela-primary px-5 py-3 shadow-lg w-100">
                            <i class='bx bx-check-circle me-1'></i> حفظ المادة الجديدة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
