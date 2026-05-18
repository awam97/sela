<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-user-pin text-gold me-2'></i> ملف الطالب التعريفي</h1>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/students/edit/' . $student['student_id']) ?>" class="btn-sela btn-sela-gold shadow-sm">
            <i class='bx bx-edit-alt me-1'></i> تعديل البيانات
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar: Photo & Quick Status -->
    <div class="col-lg-4">
        <div class="card-sela primary text-center shadow-lg p-4">
            <div class="position-relative d-inline-block mb-3">
                <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>" class="rounded-circle border border-5 border-gold shadow-lg" width="180" height="180" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff&size=200'">
                <?php if ($student['activate'] == 0): ?>
                    <span class="position-absolute bottom-0 start-0 translate-middle p-3 bg-success border border-white rounded-circle shadow"></span>
                <?php endif; ?>
            </div>
            
            <h4 class="fw-bold text-nile mb-1"><?= $student['name'] ?></h4>
            <span class="badge bg-gold px-3 py-2 rounded-pill mb-3">رقم القيد: <?= $student['nationalid'] ?></span>

            <div class="row g-2 mt-3 pt-3 border-top">
                <div class="col-6">
                    <div class="bg-light p-2 rounded-3 border">
                        <small class="text-muted d-block">المرحلة</small>
                        <span class="fw-bold text-nile small"><?= $student['class_name'] ?></span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light p-2 rounded-3 border">
                        <small class="text-muted d-block">الفصل</small>
                        <span class="fw-bold text-nile small"><?= $student['section_name'] ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-grid gap-2">
                <button class="btn btn-outline-nile btn-sm"><i class='bx bx-id-card me-1'></i> استخراج بطاقة هوية</button>
                <button class="btn btn-outline-nile btn-sm"><i class='bx bx-printer me-1'></i> طباعة كشف الدرجات</button>
            </div>
        </div>
    </div>

    <!-- Main Info: Details & History -->
    <div class="col-lg-8">
        <div class="card-sela primary h-100 shadow-sm overflow-hidden">
            <div class="card-header bg-nile text-white border-0 py-3">
                <h5 class="m-0"><i class='bx bx-list-check me-2'></i> السجل الكامل لبيانات الطالب</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-0">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-4 bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <i class='bx bx-user-circle fs-3 text-gold me-3'></i>
                            <span class="fw-bold text-nile fs-6">اسم الأم</span>
                        </div>
                        <span class="text-muted fs-6"><?= $student['mother'] ?></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-4 bg-light border-bottom">
                        <div class="d-flex align-items-center">
                            <i class='bx bx-phone-call fs-3 text-gold me-3'></i>
                            <span class="fw-bold text-nile fs-6">رقم هاتف ولي الأمر (للمراسلات)</span>
                        </div>
                        <span class="text-muted fs-6" style="direction: ltr; display: inline-block;"><?= $student['phone'] ?></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-4 bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <i class='bx bx-id-card fs-3 text-gold me-3'></i>
                            <span class="fw-bold text-nile fs-6">الجنس</span>
                        </div>
                        <span class="text-muted fs-6"><?= $student['sex'] == 'male' ? 'ذكر' : 'أنثى' ?></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-4 bg-light border-bottom">
                        <div class="d-flex align-items-center">
                            <i class='bx bx-key fs-3 text-gold me-3'></i>
                            <span class="fw-bold text-nile fs-6">كلمة مرور نظام البوابة</span>
                        </div>
                        <code class="bg-white px-3 py-1 border rounded text-nile"><?= $student['password'] ?></code>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center py-4 px-4 bg-white">
                        <div class="d-flex align-items-center">
                            <i class='bx bx-calendar-event fs-3 text-gold me-3'></i>
                            <span class="fw-bold text-nile fs-6">الحالة الأكاديمية</span>
                        </div>
                        <?php if ($student['activate'] == 0): ?>
                            <span class="badge bg-success bg-opacity-75">طالب نشط ومسجل</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-75">موقوف إدارياً</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light p-4 text-center">
                <p class="text-muted small mb-0"><i class='bx bxs-lock-alt me-1'></i> جميع البيانات مشفرة ومؤمنة طبقاً لسياسة الخصوصية بالمنصة</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
