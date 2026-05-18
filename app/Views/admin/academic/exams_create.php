<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bx-plus-circle text-gold me-2'></i> إضافة امتحان جديد</h1>
        <p class="text-muted small ps-5 mb-0">تحديد موعد مسمى اختبار دوري أو نهائي جديد</p>
    </div>
    <a href="<?= base_url('admin/academic/exams') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للجداول
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg animate__animated animate__fadeIn">
            <div class="card-body p-5">
                <form action="<?= base_url('admin/academic/exams/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">اسم الامتحان</label>
                            <input type="text" name="name" class="form-control px-4 py-3 rounded-3" placeholder="مثال: امتحانات الفصل الدراسي الأول / اختبار شهر مارس" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">تاريخ البدء المتوقع</label>
                            <input type="date" name="date" class="form-control px-4 py-3 rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">تصنيف الامتحان / الفترة</label>
                            <select name="category" class="form-select px-4 py-3 rounded-3" required>
                                <optgroup label="نظام السمستر">
                                    <option value="s1">الفصل الدراسي الأول</option>
                                    <option value="s2">الفصل الدراسي الثاني</option>
                                </optgroup>
                                <optgroup label="نظام الفترات">
                                    <option value="p1">الفترة الأولى</option>
                                    <option value="p2">الفترة الثانية</option>
                                    <option value="p3">الفترة الثالثة - نهائي</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4 px-3">
                            <div class="bg-light p-4 rounded-3 border-start border-4 border-info">
                                <p class="small text-muted mb-0">
                                    <i class='bx bx-info-circle me-1 text-info'></i>
                                    سيتم استخدام هذا المسمى لاحقاً في كشوف رصد الدرجات واستخراج التقارير الفصلية المجمعة.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg fs-5">
                            <i class='bx bx-check-circle me-1'></i> تأكيد وإضافة الامتحان
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
