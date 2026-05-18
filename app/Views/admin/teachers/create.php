<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.css">
<style>
    .iti, .iti__country-list { text-align: left; direction: ltr !important; }
    .iti--allow-dropdown, .iti--show-flags, .iti--inline-dropdown { direction: ltr !important; }
    .iti input { direction: ltr !important; text-align: left !important; padding-left: 90px !important; padding-right: 15px !important; }
    .iti__flag-container, .iti__country-container { left: 0 !important; right: auto !important; }
    .iti--separate-dial-code .iti__selected-dial-code { margin-left: 6px !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bx-user-plus text-gold me-2'></i> إضافة معلم جديد</h1>
    <a href="<?= base_url('admin/teachers') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للقائمة
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela primary shadow-lg">
            <div class="card-header border-0 bg-transparent pt-4 px-4">
                <h5 class="fw-bold text-nile mb-0">بيانات المعلم الأساسية</h5>
                <p class="text-muted small mb-0">يرجى ملء كافة البيانات المطلوبة لإنشاء حساب المعلم</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/teachers/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control px-4 py-3 rounded-3" placeholder="أدخل اسم المعلم الثلاثي..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">رقم الهاتف</label>
                            <input type="tel" id="phone" name="phone_input" class="form-control px-4 py-3 rounded-3" placeholder="09xxxxxxx" required>
                            <input type="hidden" name="phone" id="phone_full">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">البريد الإلكتروني (اختياري)</label>
                            <input type="email" name="email" class="form-control px-4 py-3 rounded-3" placeholder="teacher@sela.ly">
                        </div>

                        <div class="col-md-12">
                            <div class="alert bg-light border-start border-4 border-gold py-3 px-4 rounded-3">
                                <div class="d-flex align-items-center">
                                    <i class='bx bxs-lock text-gold fs-4 me-3'></i>
                                    <div>
                                        <label class="form-label fw-bold text-nile mb-1">كلمة المرور المسماة</label>
                                        <input type="text" name="password" class="form-control border-0 shadow-none bg-transparent p-0" value="123456" required>
                                        <small class="text-muted">سيتمكن المعلم من تغييرها لاحقاً</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg fs-5">
                            <i class='bx bx-check-circle me-1'></i> تأكيد وإضافة المعلم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>
<script>
    const phoneInput = document.querySelector("#phone");
    const phoneFull = document.querySelector("#phone_full");
    
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "ly",
        separateDialCode: true,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
    });

    document.querySelector("form").onsubmit = function() {
        phoneFull.value = iti.getNumber();
    };
</script>
<?= $this->endSection() ?>
