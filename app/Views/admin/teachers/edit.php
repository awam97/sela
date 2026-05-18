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
    <h1><i class='bx bx-edit-alt text-gold me-2'></i> تعديل بيانات المعلم</h1>
    <a href="<?= base_url('admin/teachers') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للقائمة
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg animate__animated animate__fadeIn">
            <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-nile mb-1"><?= $teacher['name'] ?></h5>
                    <p class="text-muted small mb-0">يمكنك تحديث البيانات الشخصية أو تغيير كلمة المرور هنا</p>
                </div>
                <div class="avatar-lg">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($teacher['name']) ?>&background=192A56&color=fff&size=100" class="rounded-circle border border-3 border-gold" width="80">
                </div>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/teachers/edit/' . $teacher['teacher_id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control px-4 py-3 rounded-3" value="<?= $teacher['name'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile px-1 mb-1">رقم الهاتف</label>
                            <input type="tel" id="phone" name="phone_input" class="form-control px-4 py-3 rounded-3" value="<?= $teacher['phone'] ?>" required>
                            <input type="hidden" name="phone" id="phone_full">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile px-1 mb-1">البريد الإلكتروني (اختياري)</label>
                            <input type="email" name="email" class="form-control px-4 py-3 rounded-3" value="<?= $teacher['email'] ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">حالة الحساب</label>
                            <select name="status" class="form-select px-4 py-3 rounded-3">
                                <option value="1" <?= ($teacher['status'] == 1) ? 'selected' : '' ?>>نشط</option>
                                <option value="0" <?= ($teacher['status'] == 0) ? 'selected' : '' ?>>غير نشط</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4 px-3">
                            <div class="bg-light p-4 rounded-3 border-start border-4 border-gold">
                                <label class="form-label fw-bold text-nile mb-2"><i class='bx bxs-lock-open me-1'></i> تغيير كلمة المرور (اختياري)</label>
                                <input type="password" name="password" class="form-control" placeholder="اتركها فارغة لعدم التغيير">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg fs-5">
                            <i class='bx bxs-save me-1'></i> حفظ التعديلات
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
