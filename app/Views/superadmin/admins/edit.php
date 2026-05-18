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
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1><i class='bx bxs-user-detail text-gold me-2'></i> تعديل حساب المدير</h1>
        <p class="text-muted small ps-5 mb-0">تحديث بيانات الدخول والصلاحيات للمدير التنفيذي</p>
    </div>
    <a href="<?= base_url('superadmin/admins') ?>" class="btn-sela bg-light text-nile shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للمسؤولين
    </a>
</div>

<div class="row justify-content-center mt-5">
    <div class="col-xl-6 col-lg-8">
        <form action="<?= base_url('superadmin/admins/edit/'.$admin['admin_id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="card-sela primary shadow-sm border-0">
                <div class="card-header bg-white py-4 px-4 border-bottom">
                    <h5 class="fw-bold text-nile mb-0"><i class='bx bx-info-circle text-gold me-2'></i> بيانات الحساب</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">اسم المسؤول بالكامل</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" name="name" value="<?= $admin['name'] ?>" class="form-control form-control-lg border-light bg-light" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">المدرسة المرتبطة</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bxs-school'></i></span>
                                <select name="school" class="form-select form-select-lg border-light bg-light" required>
                                    <?php foreach ($schools as $school): ?>
                                        <option value="<?= $school['ID'] ?>" <?= $admin['school'] == $school['ID'] ? 'selected' : '' ?>><?= $school['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">اسم المستخدم (للدخول)</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                <input type="text" name="username" value="<?= $admin['username'] ?>" class="form-control form-control-lg border-light bg-light" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">كلمة المرور الجديدة</label>
                            <div class="input-group-sela">
                                <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                                <input type="password" name="password" class="form-control form-control-lg border-light bg-light" placeholder="اترك فارغاً لعدم التغيير">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">بيانات التواصل والأمان</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="tel" id="phone" name="phone_input" value="<?= $admin['phone'] ?>" class="form-control" required>
                                    <input type="hidden" name="phone" id="phone_full">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">البريد الإلكتروني (لتحقق OTP)</label>
                                    <input type="email" name="email" value="<?= $admin['email'] ?? '' ?>" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn-sela btn-sela-gold shadow-lg py-3 w-100 fs-5 rounded-pill">
                    <i class='bx bx-save me-2'></i> حفظ التحديثات
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .input-group-sela { position: relative; display: flex; align-items: center; }
    .input-group-sela .input-group-text {
        position: absolute; right: 15px; z-index: 5; background: none; border: none; color: #192A56; opacity: 0.5;
    }
    .input-group-sela .form-control, .input-group-sela .form-select { padding-right: 45px !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>
<script>
    const phoneInput = document.querySelector("#phone");
    const phoneFull = document.querySelector("#phone_full");
    
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        geoIpLookup: callback => {
            fetch("https://ipapi.co/json")
                .then(res => res.json())
                .then(data => callback(data.country_code))
                .catch(() => callback("ly"));
        },
        separateDialCode: true,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
    });

    document.querySelector("form").onsubmit = function() {
        phoneFull.value = iti.getNumber();
    };
</script>
<?= $this->endSection() ?>
