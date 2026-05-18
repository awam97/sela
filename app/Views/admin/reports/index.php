<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela animate__animated animate__fadeIn">
    <h1><i class='bx bxs-report text-gold me-2'></i> مركز التقارير والإحصائيات</h1>
    <p class="text-muted small ps-5">اختر نوع التقرير المطلوب لاستخراجه أو طباعته</p>
</div>

<div class="row g-4 mt-2">
    <!-- Student Access Cards -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="selection-item-card h-100 p-4 d-flex flex-column align-items-center justify-content-center" 
             onclick="openSelector(5, 'بطاقات دخول المنصة')">
            <div class="bg-light p-3 rounded-circle mb-3 border border-nile">
                <i class='bx bxs-id-card fs-1 text-nile'></i>
            </div>
            <div class="item-name fs-5">بطاقات دخول الطلاب</div>
            <p class="text-muted small text-center mt-2">طباعة بيانات الدخول للمنصة الذكية (10 في الصفحة)</p>
        </div>
    </div>

    <!-- Academic Marksheets -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="selection-item-card h-100 p-4 d-flex flex-column align-items-center justify-content-center"
             onclick="openSelector(8, 'كشوف الدرجات والنتائج')">
            <div class="bg-light p-3 rounded-circle mb-3 border border-gold">
                <i class='bx bxs-graduation fs-1 text-gold'></i>
            </div>
            <div class="item-name fs-5">كشوف الدرجات والنتائج</div>
            <p class="text-muted small text-center mt-2">استخراج نتائج الطلاب لكل صف دراسي بشكل مجمع</p>
        </div>
    </div>

    <!-- Attendance Reports -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="selection-item-card h-100 p-4 d-flex flex-column align-items-center justify-content-center"
             onclick="openSelector(10, 'تقارير الحضور والغياب')">
            <div class="bg-light p-3 rounded-circle mb-3 border border-info">
                <i class='bx bxs-calendar-check fs-1 text-info'></i>
            </div>
            <div class="item-name fs-5">الحضور والغياب</div>
            <p class="text-muted small text-center mt-2">إحصائيات الغياب والحضور الشهري والسنوي</p>
        </div>
    </div>

    <!-- Subject Distribution -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
        <div class="selection-item-card h-100 p-4 d-flex flex-column align-items-center justify-content-center"
             onclick="openSelector(9, 'توزيع المواد الدراسية')">
            <div class="bg-light p-3 rounded-circle mb-3 border border-success">
                <i class='bx bxs-book-open fs-1 text-success'></i>
            </div>
            <div class="item-name fs-5">توزيع المواد والمدرسين</div>
            <p class="text-muted small text-center mt-2">تقارير المواد الدراسية المعينة لكل قسم</p>
        </div>
    </div>
</div>

<!-- Selection Modal (Reusable from Students Index) -->
<div class="modal fade" id="reportSelectorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-nile text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="modalTitle">تحديد الصف والتقرير</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form action="<?= base_url('admin/students/selector') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="service_id" id="service_id">
                    
                    <h5 class="text-nile fw-bold mb-4">1. اختر الصف الدراسي</h5>
                    <div class="selection-grid mb-5">
                        <?php 
                        $db = \Config\Database::connect();
                        $classes = $db->table('class')->get()->getResultArray();
                        foreach ($classes as $c): ?>
                            <div class="selection-item-card class-card" data-id="<?= $c['class_id'] ?>" onclick="selectClass(<?= $c['class_id'] ?>)">
                                <i class='bx bxs-school'></i>
                                <div class="item-name"><?= $c['name'] ?></div>
                            </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="class_id" id="selected_class_id" required>
                    </div>

                    <div id="section-area" style="display: none;">
                        <h5 class="text-nile fw-bold mb-4">2. اختر الفصل</h5>
                        <div id="sections-grid" class="selection-grid mb-4">
                            <!-- Loaded via AJAX -->
                        </div>
                        <input type="hidden" name="section_id" id="selected_section_id">
                    </div>

                    <div class="mt-5 text-center">
                        <button type="submit" class="btn-sela btn-sela-primary px-5 py-3 fs-5 shadow-lg">
                            <i class='bx bx-check-double me-2'></i> معاينة التقرير النهائي
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openSelector(id, title) {
        document.getElementById('service_id').value = id;
        document.getElementById('modalTitle').innerText = title;
        new bootstrap.Modal(document.getElementById('reportSelectorModal')).show();
    }

    function selectClass(id) {
        $('.class-card').removeClass('active');
        $(`.class-card[data-id="${id}"]`).addClass('active');
        document.getElementById('selected_class_id').value = id;
        
        // Show sections area and load data
        $('#section-area').fadeIn();
        loadSections(id);
    }

    function loadSections(classId) {
        $.get('<?= base_url('admin/students/get_sections_grid') ?>/' + classId, function(data) {
            $('#sections-grid').html(data);
        });
    }

    function selectSection(id) {
        $('.section-card').removeClass('active');
        $(`.section-card[data-id="${id}"]`).addClass('active');
        document.getElementById('selected_section_id').value = id;
    }
</script>
<?= $this->endSection() ?>
