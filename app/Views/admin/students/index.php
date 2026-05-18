<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="mb-1"><i class='bx bxs-grid-alt text-gold me-2'></i> مركز الخدمات الأكاديمية</h1>
        <p class="text-muted small ps-5 mb-0">اختر نوع العملية المطلوبة من الشبكة أدناه للبدء</p>
    </div>
    <button onclick="openCreateModal()" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إضافة طالب جديد
    </button>
</div>

<div class="row g-4">
    <!-- Service Cards Grid -->
    <?php foreach($services as $s): ?>
    <div class="col-lg-3 col-md-4 col-6">
        <div class="service-card-modern" 
             onclick="openSelectionModal('<?= $s['service_id'] ?>', '<?= $s['title'] ?>')"
             data-aos="fade-up">
            <div class="service-icon-wrapper">
                <i class='bx <?= $s['icon'] ?>'></i>
            </div>
            <h6 class="service-title"><?= $s['title'] ?></h6>
            <p class="service-desc"><?= $s['description'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Selection Modal -->
<div class="modal fade modal-sela" id="selectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-nile" id="modalTitle">تحديد النطاق</h5>
                    <p class="text-muted small mb-0" id="modalSubTitle">يرجى اختيار الصف والفصل المطلوب</p>
                </div>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('admin/students/selector') ?>" method="POST" id="hubSelectorForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="service_id" id="modal_service_id">
                    <input type="hidden" name="class_id" id="hidden_class_id">
                    <input type="hidden" name="section_id" id="hidden_section_id">
                    <input type="hidden" name="subject_id" id="hidden_subject_id">
                    
                    <!-- Class Selection Step -->
                    <div id="step_class">
                        <label class="service-label mb-3">اختر الصف الدراسي</label>
                        <div class="selection-grid">
                            <?php foreach($classes as $row): ?>
                                <div class="selection-item-card" onclick="selectClass('<?= $row['class_id'] ?>', this)">
                                    <i class='bx bxs-school'></i>
                                    <div class="item-name"><?= $row['name'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Section Selection Step (Initially Hidden) -->
                    <div id="step_section" style="display: none;" class="mt-4 animate__animated animate__fadeIn">
                        <label class="service-label mb-3 text-nile fw-bold">اختر الفصل</label>
                        <div class="selection-grid" id="sections_grid">
                            <!-- Dynamic Sections Grid -->
                        </div>
                    </div>

                    <!-- Subject Selection Step (Initially Hidden) -->
                    <div id="step_subject" style="display: none;" class="mt-4 animate__animated animate__fadeIn">
                        <label class="service-label mb-3 text-nile fw-bold">اختر المادة الدراسية</label>
                        <div class="selection-grid" id="subjects_grid">
                            <!-- Dynamic Subjects Grid -->
                        </div>
                    </div>

                    <div class="text-center mt-4" id="back_button_container" style="display: none;">
                        <button type="button" class="btn btn-sm btn-link text-muted" onclick="backToClasses()">
                            <i class='bx bx-chevron-right'></i> العودة لاختيار الصف
                        </button>
                    </div>

                    <div class="mt-5" id="submit_container" style="display: none;">
                        <button type="submit" class="btn-service-go w-100 shadow-lg">
                            استمرار للخدمة <i class='bx bx-right-arrow-alt ms-2'></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const selectionModal = new bootstrap.Modal(document.getElementById('selectionModal'));
    let selectedServiceId = 0;

    function openSelectionModal(id, title) {
        // Services that don't need a class selection (Global services)
        if (id == '11') {
            window.location.href = '<?= base_url('admin/classes') ?>';
            return;
        }
        if (id == '8') {
            window.location.href = '<?= base_url('admin/academic/exams') ?>';
            return;
        }

        selectedServiceId = id;
        document.getElementById('modal_service_id').value = id;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalSubTitle').innerText = 'إدارة ' + title;

        // Reset steps
        backToClasses();
        selectionModal.show();
    }

    function selectClass(classId, element) {
        // Visual feedback
        document.querySelectorAll('#step_class .selection-item-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('hidden_class_id').value = classId;

        // Logic check: services that don't need sections (from original code)
        const skipSections = ['1', '3', '6', '9', '11'];
        
        if (skipSections.includes(selectedServiceId)) {
            document.getElementById('submit_container').style.display = 'block';
            document.getElementById('step_section').style.display = 'none';
            document.getElementById('step_subject').style.display = 'none';
        } else {
            // Load Sections
            $('#sections_grid').html('<div class="col-12 text-center py-4"><div class="spinner-border text-gold" role="status"></div></div>');
            $('#step_class').hide();
            $('#step_section').show();
            $('#back_button_container').show();
            
            $.ajax({
                url: '<?= base_url('admin/students/get_sections_grid') ?>/' + classId,
                type: 'GET',
                success: function(response) {
                    $('#sections_grid').html(response);
                }
            });

            // If Service is Marks Entry (10), also prepare subjects
            if (selectedServiceId == '10') {
                 $('#subjects_grid').html('<div class="col-12 text-center py-4"><div class="spinner-border text-gold" role="status"></div></div>');
                 $.ajax({
                    url: '<?= base_url('admin/students/get_subjects_grid') ?>/' + classId,
                    type: 'GET',
                    success: function(response) {
                        $('#subjects_grid').html(response);
                    }
                });
            }
        }
    }

    function selectSection(sectionId, element) {
        document.querySelectorAll('#step_section .selection-item-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('hidden_section_id').value = sectionId;

        if (selectedServiceId == '10') {
            // Move to subject selection
            $('#step_section').hide();
            $('#step_subject').show();
        } else {
            document.getElementById('submit_container').style.display = 'block';
        }
    }

    function selectSubject(subjectId, element) {
        document.querySelectorAll('#step_subject .selection-item-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('hidden_subject_id').value = subjectId;
        document.getElementById('submit_container').style.display = 'block';
    }

    function backToClasses() {
        $('#step_class').show();
        $('#step_section').hide();
        $('#step_subject').hide();
        $('#submit_container').hide();
        $('#back_button_container').hide();
        document.getElementById('hidden_class_id').value = '';
        document.getElementById('hidden_section_id').value = '';
        document.getElementById('hidden_subject_id').value = '';
        document.querySelectorAll('.selection-item-card').forEach(card => card.classList.remove('active'));
    }
</script>
<?= $this->endSection() ?>
