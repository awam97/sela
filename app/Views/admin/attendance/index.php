<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bx-calendar-check text-gold me-2'></i> رصد الحضور والغياب</h1>
        <p class="text-muted small ps-5 mb-0">اختيار الصف والقسم لتسجيل حضور الطلاب اليومي</p>
    </div>
    <div class="badge bg-white shadow-sm border p-2 px-3 rounded-3 animate__animated animate__fadeInRight">
        <span class="text-muted small d-block">السنة الدراسية الحالية</span>
        <b class="sela-gold fs-5"><?= $current_year ?></b>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-sela primary shadow-sm mb-4 animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <form action="<?= base_url('admin/attendance/take') ?>" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-nile">الصف الدراسي</label>
                        <select name="class_id" id="class_id" class="form-select rounded-3" required onchange="fetchSections(this.value)">
                            <option value="">اختر الصف...</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['class_id'] ?>"><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-nile">الفصل</label>
                        <select name="section_id" id="section_id" class="form-select rounded-3" required disabled>
                            <option value="">اختر الفصل...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-nile">التاريخ</label>
                        <input type="date" name="date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-12 text-center mt-4">
                        <button type="submit" class="btn-sela btn-sela-primary px-5 shadow-sm">
                            <i class='bx bx-search-alt me-1'></i> الانتقال لرصد الحضور
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-5 opacity-50">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/attendance-management-4487920-3723145.png" width="250" class="mb-3">
            <p class="text-muted">اختر المعطيات أعلاه للبدء في عملية الرصد</p>
        </div>
    </div>
</div>

<script>
    function fetchSections(classId) {
        const sectionSelect = document.getElementById('section_id');
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">اختر الفصل...</option>';
            sectionSelect.disabled = true;
            return;
        }

        sectionSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        sectionSelect.disabled = true;

        fetch('<?= base_url('admin/students/get_sections') ?>/' + classId)
            .then(response => response.text())
            .then(html => {
                sectionSelect.innerHTML = html;
                sectionSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching sections:', error);
                sectionSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            });
    }
</script>
<?= $this->endSection() ?>
