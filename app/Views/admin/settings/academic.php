<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="mb-1"><i class='bx bxs-cog text-gold me-2'></i> الإعدادات الأكاديمية</h1>
        <p class="text-muted small ps-5 mb-0">تخصيص أيام العطلات الأسبوعية ونظام عمل الجداول الدراسية</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="card-sela primary shadow-sm border-0">
            <div class="card-header bg-white py-4 px-4 border-bottom">
                <h5 class="fw-bold text-nile mb-0"><i class='bx bx-calendar-x text-gold me-2'></i> أيام العطلة الأسبوعية (الويكند)</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">الأيام المحددة أدناه سيتم إخفاؤها تلقائياً من جداول الحصص الدراسية في كافة الصفوف.</p>
                
                <form action="<?= base_url('admin/settings/update_academic') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <?php 
                    $days = [
                        'saturday'  => 'السبت',
                        'sunday'    => 'الأحد',
                        'monday'    => 'الاثنين',
                        'tuesday'   => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday'  => 'الخميس',
                        'friday'    => 'الجمعة'
                    ];
                    $off_days_array = explode(',', $off_days);
                    ?>

                    <div class="list-group list-group-flush border rounded-3 overflow-hidden mb-4">
                        <?php foreach ($days as $key => $name): ?>
                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-light rounded-circle me-3">
                                        <i class="bx <?= in_array($key, $off_days_array) ? 'bx-calendar-x text-danger' : 'bx-calendar-check text-success' ?> fs-5"></i>
                                    </div>
                                    <span class="fw-bold text-nile"><?= $name ?></span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="off_days[]" value="<?= $key ?>" 
                                           <?= in_array($key, $off_days_array) ? 'checked' : '' ?>>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 small">
                        <i class='bx bx-info-circle me-2'></i>
                        تنبيه: يمكنك دائماً إضافة حصص في هذه الأيام حتى لو كانت مخفية من العرض الرئيسي للجدول.
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-service-go w-100 shadow">
                            <i class='bx bx-save me-2'></i> حفظ إعدادات الجدول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
