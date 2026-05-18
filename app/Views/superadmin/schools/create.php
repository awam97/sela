<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1><i class='bx bxs-school text-gold me-2'></i> تسجيل مؤسسة تعليمية</h1>
        <p class="text-muted small ps-5 mb-0">إضافة مدرسة جديدة إلى قاعدة بيانات نظام السيلا المركزي</p>
    </div>
    <a href="<?= base_url('superadmin/schools') ?>" class="btn-sela bg-light text-nile shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للمدارس
    </a>
</div>

<div class="row justify-content-center mt-5">
    <div class="col-xl-8">
        <form action="<?= base_url('superadmin/schools/create') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="card-sela primary shadow-sm border-0">
                <div class="card-header bg-white py-4 px-4 border-bottom">
                    <h5 class="fw-bold text-nile mb-0"><i class='bx bx-info-circle text-gold me-2'></i> البيانات الأساسية</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small fw-bold">اسم المؤسسة التعليمية</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bxs-school'></i></span>
                                <input type="text" name="name" class="form-control form-control-lg border-light bg-light" required placeholder="مثال: مدرسة الأمل الخاصة">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">المدينة / المنطقة</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bxs-map'></i></span>
                                <select name="city" class="form-select form-select-lg border-light bg-light" required>
                                    <option value="">اختر المدينة</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= $city['ID'] ?>"><?= $city['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">البريد الإلكتروني الرسمي</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                <input type="email" name="email" class="form-control form-control-lg border-light bg-light" required placeholder="school@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">العام الدراسي الافتراضي</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                <input type="text" name="year" class="form-control form-control-lg border-light bg-light" required placeholder="مثال: 2025-2026">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">العنوان التفصيلي</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-map-pin'></i></span>
                                <input type="text" name="address" class="form-control form-control-lg border-light bg-light" required placeholder="الحي، الشارع، المعلم القريب...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-sela primary shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-4 px-4 border-bottom">
                    <h5 class="fw-bold text-nile mb-0"><i class='bx bx-user-circle text-gold me-2'></i> إدارة المدرسة</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">اسم المدير العام</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" name="manager" class="form-control form-control-lg border-light bg-light" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">مسؤول الامتحانات</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-spreadsheet'></i></span>
                                <input type="text" name="exams_manager" class="form-control form-control-lg border-light bg-light" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center">
                <button type="submit" class="btn-sela btn-sela-gold shadow-lg py-3 px-5 rounded-pill w-100 fs-5">
                    <i class='bx bx-check-double me-2'></i> إتمام عملية التسجيل
                </button>
                <p class="text-muted small mt-3">بمجرد التسجيل، سيتمكن المدير من تسجيل الدخول وإدارة كافة العمليات الأكاديمية لهذه المدرسة.</p>
            </div>
        </form>
    </div>
</div>

<style>
    .input-group-sela { position: relative; display: flex; align-items: center; }
    .input-group-sela .input-group-text {
        position: absolute; right: 10px; z-index: 5; background: none; border: none; color: #192A56; opacity: 0.5;
    }
    .input-group-sela .form-control, .input-group-sela .form-select { padding-right: 45px !important; }
</style>
<?= $this->endSection() ?>
