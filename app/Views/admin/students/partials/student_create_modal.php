<!-- Global Student Creation/Edition Modal -->
<div class="modal fade modal-sela" id="globalStudentModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-nile text-white py-4 px-5">
                <div>
                    <h5 class="modal-title fw-bold" id="globalStudentModalTitle">إضافة طالب جديد</h5>
                    <p class="text-white-50 small mb-0">يرجى تعبئة كافة البيانات المطلوبة للملف الشخصي</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <form id="globalStudentForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="gs_student_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">اسم الطالب</label>
                            <input type="text" name="name" id="gs_name" class="form-control" placeholder="الاسم الرباعي" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">اسم الأم</label>
                            <input type="text" name="mother" id="gs_mother" class="form-control" placeholder="اسم الأم الكامل" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">الرقم الوطني / القيد</label>
                            <input type="text" name="roll" id="gs_roll" class="form-control" placeholder="الرقم التعريفي">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-nile">الجنس</label>
                            <select name="sex" id="gs_sex" class="form-select">
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-nile">رقم الهاتف</label>
                            <input type="text" name="phone" id="gs_phone" class="form-control" placeholder="09XXXXXXXX" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">الصف الدراسي</label>
                            <select name="class_id" id="gs_class_selector" class="form-select" required>
                                <option value="">جاري تحميل الصفوف...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-nile">الفصل</label>
                            <select name="section_id" id="gs_section_selector" class="form-select" required>
                                <option value="">اختر الصف أولاً</option>
                            </select>
                        </div>
                        <div class="col-md-12" id="gs_password_container">
                            <div class="bg-light p-3 rounded-3 border">
                                <label class="form-label fw-bold text-nile">كلمة مرور البوابة</label>
                                <input type="password" name="password" id="gs_password" class="form-control bg-white" placeholder="أدخل كلمة مرور قوية">
                                <div class="form-text mt-2 text-muted" style="font-size: 0.7rem;">سيتم استخدام هذه الكلمة لدخول الطالب لمركز الخدمات</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-end">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 fs-5 rounded-3 shadow">
                            تأكيد وحفظ البيانات <i class='bx bx-check-double ms-2'></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const globalStudentModal = new bootstrap.Modal(document.getElementById('globalStudentModal'));
    let classesLoaded = false;

    // Global Trigger for Creation
    window.openCreateModal = function() {
        $('#globalStudentForm')[0].reset();
        $('#gs_student_id').val('');
        $('#globalStudentModalTitle').text('إضافة طالب جديد');
        $('#gs_password_container').show();
        $('#gs_password').attr('required', true);
        
        loadAllClasses();
        globalStudentModal.show();
    };

    // Global Trigger for Edition
    window.openEditModal = function(student) {
        $('#globalStudentForm')[0].reset();
        $('#gs_student_id').val(student.student_id);
        $('#gs_name').val(student.name);
        $('#gs_mother').val(student.mother);
        $('#gs_roll').val(student.nationalid);
        $('#gs_sex').val(student.sex);
        $('#gs_phone').val(student.phone);
        
        $('#globalStudentModalTitle').text('تعديل بيانات الطالب');
        $('#gs_password_container').hide();
        $('#gs_password').attr('required', false);
        
        loadAllClasses(student.class_id);
        loadSections(student.class_id, student.section_id);
        
        globalStudentModal.show();
    };

    $('#gs_class_selector').on('change', function() {
        loadSections($(this).val());
    });

    $('#globalStudentForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#gs_student_id').val();
        const url = id ? '<?= base_url('admin/students/edit') ?>/' + id : '<?= base_url('admin/students/create') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    globalStudentModal.hide();
                    showAlert('success', res.message);
                    // Refresh if list exists
                    if (typeof refreshList === "function") {
                        refreshList();
                    }
                } else {
                    showAlert('error', res.message);
                }
            }
        });
    });

    function loadAllClasses(selectId = null) {
        if (classesLoaded && !selectId) return;
        
        $.get('<?= base_url('admin/students/get_classes_all') ?>', function(res) {
            let options = '<option value="">اختر الصف الدراسي</option>';
            res.forEach(c => {
                options += `<option value="${c.class_id}">${c.name}</option>`;
            });
            $('#gs_class_selector').html(options);
            if (selectId) $('#gs_class_selector').val(selectId);
            classesLoaded = true;
        });
    }

    function loadSections(classId, callbackId = null) {
        if (!classId) return;
        $('#gs_section_selector').html('<option value="">جاري التحميل...</option>');
        $.get('<?= base_url('admin/students/get_sections') ?>/' + classId, function(res) {
            $('#gs_section_selector').html(res);
            if (callbackId) $('#gs_section_selector').val(callbackId);
        });
    }
});
</script>
