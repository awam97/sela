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
    <h1><i class='bx bxs-edit-alt text-gold me-2'></i> تعديل بيانات الطالب</h1>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card-sela primary">
    <div class="card-header">
        <i class='bx bx-info-circle me-1'></i> تعديل الملوفات لـ: <span class="text-gold fw-bold"><?= $student['name'] ?></span>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/students/edit/' . $student['student_id']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-4">
                <!-- Name -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">اسم الطالب</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name', $student['name']) ?>" required>
                </div>

                <!-- Mother's Name -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">اسم الأم</label>
                    <input type="text" name="mother" class="form-control" value="<?= old('mother', $student['mother']) ?>" required>
                </div>

                <!-- Roll / National ID -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الرقم الوطني / رقم القيد</label>
                    <input type="text" name="roll" class="form-control" value="<?= old('roll', $student['nationalid']) ?>">
                </div>

                <!-- Class -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الصــــــــــف</label>
                    <select name="class_id" id="class_selector_edit" class="form-select" required>
                        <option value="">اختر الصف</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['class_id'] ?>" <?= $enroll['class_id'] == $class['class_id'] ? 'selected' : '' ?>>
                                <?= $class['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Section -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الفصــــــــــل</label>
                    <select name="section_id" id="section_selector_edit" class="form-select" required>
                        <option value="<?= $enroll['section_id'] ?>">لا تغيير (الفصل الحالي)</option>
                    </select>
                    <small class="text-muted">قم بتغيير الصف لتحديث الفصول</small>
                </div>

                <!-- Sex -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">الجنس</label>
                    <select name="sex" class="form-select">
                        <option value="male" <?= $student['sex'] == 'male' ? 'selected' : '' ?>>ذكر</option>
                        <option value="female" <?= $student['sex'] == 'female' ? 'selected' : '' ?>>أنثى</option>
                    </select>
                </div>

                <!-- Phone -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone_input" class="form-control" value="<?= old('phone', $student['phone']) ?>" required>
                    <input type="hidden" name="phone" id="phone_full">
                </div>

                <!-- Password -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">كلمة المرور (تغيير؟)</label>
                    <input type="password" name="password" class="form-control" value="<?= $student['password'] ?>" required>
                </div>

                <!-- Photo -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-nile">تحديث الصورة الشخصية</label>
                    <input type="file" name="userfile" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-end">
                    <hr class="mb-4 opacity-5">
                    <button type="submit" class="btn-sela btn-sela-gold btn-lg shadow-sm">
                        <i class='bx bx-save me-1'></i> حفظ التعديلات
                    </button>
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

    $(document).ready(function() {
        // Initial load for current class sections if needed
        function loadSections(classId, selectedSectionId = null) {
            $.ajax({
                url: '<?= base_url('admin/students/get_sections') ?>/' + classId,
                type: 'GET',
                success: function(response) {
                    $('#section_selector_edit').html(response);
                    if (selectedSectionId) {
                        $('#section_selector_edit').val(selectedSectionId);
                    }
                }
            });
        }

        $('#class_selector_edit').on('change', function() {
            var classId = $(this).val();
            if (classId) {
                loadSections(classId);
            }
        });
        
        // Populate initially
        loadSections(<?= $enroll['class_id'] ?>, <?= $enroll['section_id'] ?>);
    });
</script>
<?= $this->endSection() ?>
