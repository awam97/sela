<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bx-plus-circle text-gold me-2'></i> إضافة واجب جديد</h1>
        <p class="text-muted small ps-5 mb-0">تحديد مهمة دراسية جديدة لطلاب هذا الصف</p>
    </div>
    <a href="<?= base_url('admin/homework/index/' . $class_id) ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للمهام
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg animate__animated animate__fadeIn">
            <div class="card-body p-4">
                <form action="<?= base_url('admin/homework/create/' . $class_id) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">عنوان الواجب</label>
                            <input type="text" name="title" class="form-control px-4 py-3 rounded-3" placeholder="أدخل عنواناً للمهمة..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile px-1 mb-1">المادة الدراسية</label>
                            <select name="subject_id" class="form-select px-4 py-3 rounded-3" required>
                                <option value="">اختر المادة...</option>
                                <?php foreach ($subjects as $s): ?>
                                    <option value="<?= $s['subject_id'] ?>"><?= $s['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile px-1 mb-1">تاريخ التسليم / التنفيذ</label>
                            <input type="date" name="date" class="form-control px-4 py-3 rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">وصف الواجب والتفاصيل</label>
                            <textarea name="description" class="form-control px-4 py-3 rounded-3" rows="6" placeholder="اكتب تفاصيل الواجب هنا..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg fs-5">
                            <i class='bx bx-check-circle me-1'></i> تأكيد وإضافة الواجب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
