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
    <h1><i class='bx bxs-user-plus text-gold me-2'></i> إضافة طالب جديد</h1>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card-sela primary">
    <div class="card-header">
        <i class='bx bx-info-circle me-1'></i> بيانات الطالب الأساسية
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/students/create') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-4">
                <!-- Name -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">اسم الطالب (الرسمي)</label>
                    <input type="text" name="name" class="form-control" placeholder="أدخل الاسم الرباعي" value="<?= old('name') ?>" required>
                </div>

                <!-- Mother's Name -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">اسم الأم</label>
                    <input type="text" name="mother" class="form-control" placeholder="أدخل اسم الأم" value="<?= old('mother') ?>" required>
                </div>

                <!-- Roll / National ID -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الرقم الوطني / رقم القيد</label>
                    <input type="text" name="roll" class="form-control" placeholder="أدخل الرقم" value="<?= old('roll') ?>">
                </div>

                <!-- Class -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الصف الدراسي</label>
                    <select name="class_id" id="class_selector" class="form-select" required>
                        <option value="">اختر الصف</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['class_id'] ?>"><?= $class['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Section -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الفصل</label>
                    <select name="section_id" id="section_selector" class="form-select" required>
                        <option value="">اختر الصف أولاً</option>
                    </select>
                </div>

                <!-- Sex -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الجنس</label>
                    <select name="sex" class="form-select">
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>

                <!-- Phone -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">رقم هاتف ولي الأمر</label>
                    <input type="tel" id="phone" name="phone_input" class="form-control" placeholder="09XXXXXXXX" value="<?= old('phone') ?>" required>
                    <input type="hidden" name="phone" id="phone_full">
                </div>

                <!-- Password -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">كلمة مرور البوابة</label>
                    <input type="password" name="password" class="form-control" placeholder="أدخل كلمة مرور قوية" required>
                </div>

                <!-- Photo -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الصورة الشخصية</label>
                    <input type="file" name="userfile" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-end">
                    <hr class="mb-4 opacity-5">
                    <button type="submit" class="btn-sela btn-sela-gold btn-lg me-2">
                        <i class='bx bx-check-circle me-1'></i> تأكيد وتسجيل الطالب
                    </button>
                    <button type="reset" class="btn btn-light btn-lg">إعادة تعيين</button>
                </div>
            </div>
        </form>
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

    $(document).ready(function() {
        $('#class_selector').on('change', function() {
            var classId = $(this).val();
            if (classId) {
                $('#section_selector').html('<option value="">جاري التحميل...</option>');
                $.ajax({
                    url: '<?= base_url('admin/students/get_sections') ?>/' + classId,
                    type: 'GET',
                    success: function(response) {
                        $('#section_selector').html(response);
                    },
                    error: function() {
                        $('#section_selector').html('<option value="">فشل التحميل</option>');
                    }
                });
            } else {
                $('#section_selector').html('<option value="">اختر الصف أولاً</option>');
            }
        });
    });
</script>
<?= $this->endSection() ?>
