<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-face text-gold me-2'></i> صور الطلاب - الصف : <?= $students[0]['class_name'] ?? '...' ?></h1>
    <div>
        <button type="submit" form="picsForm" class="btn-sela btn-sela-primary shadow-sm">
            <i class='bx bx-save me-1'></i> حفظ الصور المرفوعة
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/students/pics/' . $class_id . '/' . $section_id) ?>" method="POST" id="picsForm"
    enctype="multipart/form-data" class="animate__animated animate__fadeIn">
    <?= csrf_field() ?>
    <div class="row g-4 overflow-hidden">
        <?php foreach ($students as $student): ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card-sela primary h-100 text-center p-0 border-0 shadow-sm overflow-hidden student-photo-card">
                    <div class="photo-card-header bg-light bg-opacity-50 py-4">
                        <div class="position-relative d-inline-block">
                            <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>"
                                class="rounded-circle border border-5 border-white shadow-lg profile-preview"
                                id="preview_<?= $student['student_id'] ?>"
                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff&size=100'">
                            <div class="upload-indicator"><i class='bx bx-camera'></i></div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <h6 class="fw-bold text-nile mb-1 text-truncate"><?= $student['name'] ?></h6>
                        <div class="text-muted small mb-3">#<?= $student['student_id'] ?> | <?= $student['section_name'] ?></div>

                        <div class="mt-auto pt-2">
                            <label class="btn-sela-upload w-100 shadow-sm">
                                <i class='bx bx-plus-circle me-1'></i> اختيار ملف جديد
                                <input type="file" name="file_<?= $student['student_id'] ?>_a" class="d-none"
                                    onchange="previewImage(this, '<?= $student['student_id'] ?>')">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($students)): ?>
            <div class="col-12">
                <div class="card-sela primary text-center py-5 border-dashed">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-box-4816210-4012154.png" width="150" class="mb-3 opacity-50">
                    <h6 class="text-muted fw-bold">لا يوجد طلاب مسجلون في هذا المسار حالياً</h6>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-5 mb-5 text-center">
        <button type="submit" class="btn-sela btn-sela-primary btn-lg px-5 py-3 shadow-lg hover-up">
            <i class='bx bxs-save me-2'></i> اعتماد وحفظ صور الطلاب المرفوعة
        </button>
        <p class="text-muted x-small mt-3">سيتم تحديث الملفات الشخصية فور الضغط على زر الحفظ</p>
    </div>
</form>

<style>
    .student-photo-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 24px !important; }
    .student-photo-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(25, 42, 86, 0.08) !important; border-color: var(--secondary-color) !important; }
    .photo-card-header { position: relative; background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%); }
    .profile-preview { width: 110px; height: 110px; object-fit: cover; transition: all 0.3s; }
    .upload-indicator { position: absolute; bottom: 5px; right: 5px; width: 32px; height: 32px; background: var(--secondary-color); color: #fff; border-radius: 50%; border: 3px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    
    .btn-sela-upload { background: #fdfaf0; color: var(--secondary-color); border: 1px solid rgba(197, 160, 33, 0.1); padding: 10px; border-radius: 12px; display: block; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; }
    .btn-sela-upload:hover { background: var(--secondary-color); color: #fff; border-color: var(--secondary-color); }
    .hover-up:hover { transform: translateY(-3px); }
    .x-small { font-size: 0.75rem; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function previewImage(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview_' + id).attr('src', e.target.result);
                // Highlight the card that has a pending upload
                $(input).closest('.card-sela').addClass('border-gold border-2');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<?= $this->endSection() ?>