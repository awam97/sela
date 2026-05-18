<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-directions text-gold me-2'></i> توزيع الطلاب على الفصول</h1>
    <div>
        <button type="submit" form="sectionsForm" class="btn-sela btn-sela-primary shadow-sm">
            <i class='bx bx-save me-1'></i> حفظ التوزيع الجديد
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="card-sela primary shadow-sm mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-nile mb-2"><i class='bx bx-info-circle me-1'></i> التوزيع الجماعي للمراحل الدراسية</h6>
        <p class="text-muted small">يمكنك من خلال هذه الواجهة نقل الطلاب بين الصفوف والفصول بشكل جماعي وسريع. سيتم تحديث سجلات القيد (Enrollment) للعام الحالي فور الحفظ.</p>
    </div>
</div>

<form action="<?= base_url('admin/students/sections/' . $class_id . '/' . $section_id) ?>" method="POST" id="sectionsForm" class="animate__animated animate__fadeIn">
    <?= csrf_field() ?>
    <div class="row g-4 overflow-hidden">
        <?php foreach ($students as $student): ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card-sela primary h-100 p-0 border-0 shadow-sm overflow-hidden section-card">
                    <div class="card-header bg-soft-gold border-0 py-3 px-4 d-flex align-items-center">
                        <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>" 
                             class="rounded-circle border border-2 border-white me-3 shadow-sm" width="45" height="45" 
                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff&size=50'">
                        <div class="overflow-hidden">
                            <h6 class="fw-bold text-nile mb-0 text-truncate" style="font-size: 0.9rem;"><?= $student['name'] ?></h6>
                            <div class="text-gold fw-bold font-monospace mt-1" style="font-size: 0.7rem;">Sela ID: #<?= $student['student_id'] ?></div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label-sela"><i class='bx bx-layer me-1'></i> المرحلة الدراسية</label>
                            <select name="class_<?= $student['student_id'] ?>" class="form-select minimal-select shadow-sm" 
                                    onchange="loadSectionsForStudent(this, <?= $student['student_id'] ?>)">
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['class_id'] ?>" <?= $student['class_id'] == $class['class_id'] ? 'selected' : '' ?>>
                                        <?= $class['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label-sela"><i class='bx bx-grid-alt me-1'></i> الفصل</label>
                            <select name="section_<?= $student['student_id'] ?>" id="section_student_<?= $student['student_id'] ?>" 
                                    class="form-select minimal-select shadow-sm">
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?= $section['section_id'] ?>" <?= $student['current_section_id'] == $section['section_id'] ? 'selected' : '' ?>>
                                        <?= $section['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer bg-light bg-opacity-25 border-0 py-2 px-4">
                         <div class="text-muted" style="font-size: 0.65rem;"><i class='bx bx-info-circle me-1'></i> التعديل ساري للعام <?= date('Y') ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-5 mb-5 text-center">
        <button type="submit" class="btn-sela btn-sela-primary btn-lg px-5 py-3 shadow-lg hover-up">
            <i class='bx bxs-check-double me-2'></i> اعتماد توزيع الطلاب الموضح أعلاه
        </button>
        <p class="text-muted x-small mt-3">سيتم نقل الطلاب فوراً للمسارات الجديدة المحددة</p>
    </div>
</form>

<style>
    .section-card { border-radius: 20px !important; transition: all 0.3s; }
    .section-card:hover { transform: translateY(-8px); shadow: 0 15px 30px rgba(0,0,0,0.05) !important; border-right: 4px solid var(--secondary-color) !important; }
    
    .form-label-sela { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block; }
    .bg-soft-gold { background-color: #fdfaf0; }
    .hover-up:hover { transform: translateY(-3px); }
    .x-small { font-size: 0.75rem; }
</style>

<style>
    .border-top-gold {
        border-top: 3px solid var(--secondary-color) !important;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function loadSectionsForStudent(select, studentId) {
        var classId = $(select).val();
        if (classId) {
            $('#section_student_' + studentId).html('<option value="">...</option>');
            $.ajax({
                url: '<?= base_url('admin/students/get_sections') ?>/' + classId,
                type: 'GET',
                success: function(response) {
                    $('#section_student_' + studentId).html(response);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
