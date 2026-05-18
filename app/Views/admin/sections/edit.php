<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
    <div>
        <h1 class="mb-1"><i class='bx bxs-door-open text-gold me-2'></i> <?= isset($section) ? 'تعديل فصل' : 'إضافة فصل للصف' . ' ' . ($class['name'] ?? '') ?></h1>
        <p class="text-muted small ps-5 mb-0">تحديد البيانات الأساسية للفصل الدراسي</p>
    </div>
    <a href="<?= base_url('admin/classes') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-chevron-right me-1'></i> العودة لقائمة الصفوف
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-sela primary shadow-lg border-0 animate__animated animate__fadeInUp">
            <div class="card-body p-5">
                <form action="<?= base_url(isset($section) ? 'admin/sections/edit/' . $section['section_id'] : 'admin/sections/create/' . $class['class_id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-5">
                        <label class="form-label fw-bold text-nile">اسم الفصل (بالعربي)</label>
                        <input type="text" name="name" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               value="<?= $section['name'] ?? '' ?>" placeholder="مثال: فصل أ / فصل ب" required>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn-sela btn-sela-primary px-5 py-3 shadow-lg w-100">
                            <i class='bx <?= isset($section) ? 'bx-save' : 'bx-plus-circle' ?> me-2'></i>
                            <?= isset($section) ? 'تحديث البيانات' : 'حفظ وإضافة الفصل' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
