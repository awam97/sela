<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
    <style>
        .drag-handle { cursor: grab; color: #cbd5e1; padding: 0 10px; transition: color 0.2s; }
        .drag-handle:hover { color: var(--secondary-color); }
        .sortable-ghost { opacity: 0.4 !important; background: #fdfaf0 !important; }
        .sortable-chosen { background: #fffcf2 !important; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-1"><i class='bx bxs-book-bookmark text-gold me-2'></i> إدارة المواد الدراسية</h1>
        <p class="text-muted small ps-5 mb-0">قائمة المقررات والمواد التعليمية المعتمدة لهذا الصف</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn-sela btn-sela-gold shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class='bx bx-plus-circle me-1'></i> إضافة مادة جديدة
        </button>
    </div>
</div>

<div class="card-sela primary shadow-sm mb-4">
    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded-circle me-3 border border-gold d-none d-sm-block">
                <i class='bx bxs-graduation text-gold fs-4'></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-nile">التوزيع الأكاديمي الحالي</h6>
                <p class="text-muted small mb-0">إجمالي المواد: <span class="badge bg-gold"><?= count($subjects) ?></span></p>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-4">
            <!-- View Toggle -->
            <div class="view-toggle-wrapper d-flex bg-white p-1 rounded-3 border">
                <button class="view-toggle-btn active" data-view="list" title="عرض القائمة">
                    <i class='bx bx-list-ul fs-5'></i>
                </button>
                <button class="view-toggle-btn" data-view="grid" title="عرض الشبكة">
                    <i class='bx bx-grid-alt fs-5'></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- List View -->
<div id="subjects-list-view" class="view-container active animate__animated animate__fadeIn">
    <div class="card-sela primary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sela align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th class="ps-4">المادة الدراسية</th>
                            <th>المعلم المسؤول</th>
                            <th>الدرجة الكلية</th>
                            <th>حد النجاح</th>
                            <th class="text-center pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr data-id="<?= $subject['subject_id'] ?>">
                                <td class="text-center">
                                    <div class="drag-handle"><i class='bx bx-dots-vertical-rounded fs-5'></i></div>
                                </td>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="subject-icon-box me-3">
                                            <i class='bx bxs-book-open'></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-nile fs-6 mb-0"><?= $subject['name'] ?></div>
                                            <small class="text-muted" style="font-size: 0.75rem;">ID: #<?= $subject['subject_id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-teacher-mini me-2">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($subject['teacher_name'] ?? 'T') ?>&background=f4f6fb&color=192A56&size=30" class="rounded-circle">
                                        </div>
                                        <span class="text-nile fw-500"><?= $subject['teacher_name'] ?? 'لم يحدد بعد' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-sela-nile"><?= $subject['total_mark'] ?> درجة</span>
                                </td>
                                <td>
                                    <span class="badge-sela-gold"><?= $subject['pass_mark'] ?> درجة</span>
                                </td>
                                <td class="text-center pe-4" style="min-width: 150px;">
                                    <div class="btn-group-sela">
                                        <button onclick="editSubject(<?= $subject['subject_id'] ?>)" 
                                           class="btn-action shadow-sm" title="تعديل المادة">
                                            <i class='bx bxs-edit-alt me-1'></i> تعديل
                                        </button>
                                        <button onclick="confirmDelete(<?= $subject['subject_id'] ?>)" 
                                           class="btn-action-danger shadow-sm" title="حذف المادة">
                                            <i class='bx bxs-trash me-1'></i> حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Grid View -->
<div id="subjects-grid-view" class="view-container student-grid row g-4 mt-1 animate__animated animate__fadeIn">
    <?php foreach ($subjects as $subject): ?>
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="subject-card-premium shadow-sm">
                <div class="subject-card-header">
                    <div class="subject-icon-wrapper-premium">
                        <i class='bx bxs-book-bookmark'></i>
                    </div>
                </div>
                
                <div class="subject-card-body">
                    <h6 class="subject-name-premium text-nile mb-1">
                        <?= $subject['name'] ?>
                    </h6>
                    <div class="teacher-info-tag mb-3">
                        <i class='bx bx-user-voice me-1'></i> <?= $subject['teacher_name'] ?? 'معلم غير محدد' ?>
                    </div>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <div class="mark-pill">
                            <small class="d-block text-muted">الكلي</small>
                            <span class="fw-bold"><?= $subject['total_mark'] ?></span>
                        </div>
                        <div class="mark-pill border-gold-light">
                            <small class="d-block text-muted">النجاح</small>
                            <span class="fw-bold text-gold"><?= $subject['pass_mark'] ?></span>
                        </div>
                    </div>

                    <div class="subject-card-footer d-flex justify-content-center gap-2">
                        <button onclick="editSubject(<?= $subject['subject_id'] ?>)" 
                           class="btn-action shadow-sm" title="تعديل">
                            <i class='bx bxs-edit-alt me-1'></i> تعديل
                        </button>
                        <button onclick="confirmDelete(<?= $subject['subject_id'] ?>)" 
                           class="btn-action-danger shadow-sm" title="حذف">
                            <i class='bx bxs-trash me-1'></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .subject-icon-box { width: 40px; height: 40px; background: #fdfaf0; color: var(--secondary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .badge-sela-nile { background: #f4f6fb; color: var(--primary-color); padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; border: 1px solid rgba(25, 42, 86, 0.05); }
    .badge-sela-gold { background: #fffcf2; color: var(--secondary-color); padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; border: 1px solid rgba(197, 160, 33, 0.1); }
    
    .btn-group-sela { display: flex; gap: 8px; justify-content: center; }
    .btn-action { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #eee; border-radius: 12px; color: var(--primary-color); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action:hover { background: var(--primary-color); color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(25, 42, 86, 0.1) !important; }
    .btn-action-danger { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #fee2e2; border-radius: 12px; color: #ef4444; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action-danger:hover { background: #ef4444; color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1) !important; }

    /* Premium subject Cards */
    .subject-card-premium { background: #fff; border-radius: 20px; border: 1px solid #f0f0f0; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%; text-align: center; }
    .subject-card-premium:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(25, 42, 86, 0.08) !important; border-color: var(--secondary-color); }
    .subject-card-header { height: 70px; background: linear-gradient(45deg, var(--secondary-color), #d4af37); position: relative; margin-bottom: 40px; }
    .subject-icon-wrapper-premium { position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%); width: 60px; height: 60px; background: #fff; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--primary-color); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .subject-name-premium { font-weight: 800; font-size: 1.1rem; }
    .teacher-info-tag { font-size: 0.8rem; color: #666; background: #f8fafc; padding: 6px 15px; border-radius: 50px; display: inline-block; }
    .subject-card-body { padding: 0 20px 25px 20px; }
    .mark-pill { flex: 1; background: #f8f9fa; border-radius: 12px; padding: 8px; border: 1px solid #eee; }
    .border-gold-light { border-color: rgba(197, 160, 33, 0.1); background: #fffdf5; }
</style>

<?php if (empty($subjects)): ?>
    <div class="text-center py-5 mt-4 card-sela primary border-dashed">
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7221376.png" width="180" class="mb-3 opacity-25">
        <h6 class="text-muted">لا توجد مواد مضافة لهذا الصف حالياً</h6>
        <button type="button" class="btn btn-link text-gold text-decoration-none fw-bold small" data-bs-toggle="modal" data-bs-target="#addSubjectModal">أضف أول مادة دراسية الآن <i class='bx bx-left-arrow-alt'></i></button>
    </div>
<?php endif; ?>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-nile"><i class='bx bx-plus-circle text-gold me-2'></i> إضافة مادة دراسية جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/subjects/create/' . $class_id) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">اسم المادة</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="مثلاً: اللغة العربية، الرياضيات..." required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">المعلم المسند له (اختياري)</label>
                            <select name="teacher_id" class="form-select rounded-3">
                                <option value="" selected>اختر المعلم...</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">الدرجة الكلية</label>
                            <input type="number" name="total_mark" class="form-control rounded-3" value="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">درجة النجاح</label>
                            <input type="number" name="pass_mark" class="form-control rounded-3" value="50" required>
                        </div>
                        <div class="col-md-4 d-none">
                            <label class="form-label fw-bold text-nile">الترتيب</label>
                            <input type="number" name="sort" class="form-control rounded-3" value="1" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-nile mb-0"><i class='bx bx-list-check text-gold me-1'></i> تقسيم الدرجات لهذه المادة</h6>
                            <button type="button" class="btn btn-sm btn-soft-gold rounded-pill px-3" onclick="addDistributionRow('add')">
                                <i class='bx bx-plus-circle me-1'></i> إضافة عنصر
                            </button>
                        </div>
                        <div id="distributionContainerAdd">
                            <div class="distribution-row row g-1 mb-2">
                                <div class="col-7"><input type="text" name="dist_name[]" class="form-control form-control-sm" placeholder="الاسم (مثلاً: الفترة الأولى)"></div>
                                <div class="col-4"><input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" placeholder="الدرجة"></div>
                                <div class="col-1 text-end"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('.distribution-row').remove()"><i class='bx bx-trash'></i></button></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary rounded-pill px-5">حفظ المادة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-nile"><i class='bx bx-edit-alt text-gold me-2'></i> تعديل المادة الدراسية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSubjectForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">اسم المادة</label>
                            <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile">المعلم</label>
                            <select name="teacher_id" id="edit_teacher_id" class="form-select rounded-3">
                                <option value="">لم يحدد بعد</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">الدرجة الكلية</label>
                            <input type="number" name="total_mark" id="edit_total_mark" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-nile">درجة النجاح</label>
                            <input type="number" name="pass_mark" id="edit_pass_mark" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4 d-none">
                            <label class="form-label fw-bold text-nile">الترتيب</label>
                            <input type="number" name="sort" id="edit_sort" class="form-control rounded-3" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-nile mb-0"><i class='bx bx-list-check text-gold me-1'></i> تقسيم الدرجات</h6>
                            <button type="button" class="btn btn-sm btn-soft-gold rounded-pill px-3" onclick="addDistributionRow()">
                                <i class='bx bx-plus-circle me-1'></i> إضافة عنصر
                            </button>
                        </div>
                        <div id="distributionContainerModal">
                            <!-- Dynamic Rows -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary rounded-pill px-5">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-soft-gold { background: rgba(197, 160, 33, 0.1); color: #c5a021; border: none; }
    .btn-soft-gold:hover { background: #c5a021; color: #fff; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle Logic
        $('.view-toggle-btn').on('click', function() {
            const view = $(this).data('view');
            
            // Update Buttons
            $('.view-toggle-btn').removeClass('active');
            $(this).addClass('active');
            
            // Update Containers
            if (view === 'grid') {
                $('#subjects-list-view').hide().removeClass('active');
                $('#subjects-grid-view').css('display', 'flex').hide().fadeIn(300).addClass('active');
            } else {
                $('#subjects-grid-view').hide().removeClass('active');
                $('#subjects-list-view').fadeIn(300).addClass('active');
            }

            // Save Preference
            localStorage.setItem('subject_view_pref', view);
        });

        // Load Preference
        const pref = localStorage.getItem('subject_view_pref');
        if (pref === 'grid') {
            $('.view-toggle-btn[data-view="grid"]').click();
        }
    });

    // Initialize Sortable for the table
    const el = document.querySelector('.table-sela tbody');
    if (el) {
        Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function() {
                updateSubjectOrder();
            }
        });
    }

    function updateSubjectOrder() {
        const order = [];
        $('.table-sela tbody tr').each(function() {
            const id = $(this).attr('data-id');
            if (id) order.push(id);
        });

        if (order.length === 0) return;

        $.ajax({
            url: '<?= base_url('admin/subjects/update_order') ?>',
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                'order': order
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    console.log('Order updated');
                } else {
                    alert('فشل في تحديث الترتيب: ' + (res.message || 'خطأ غير معروف'));
                }
            },
            error: function(xhr) {
                console.error('Update failed: ', xhr.responseText);
                alert('فشل في الاتصال بالخادم لتحديث الترتيب');
            }
        });
    }

    function editSubject(id) {
        // Show loading or just open modal
        $('#distributionContainerModal').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-gold"></div></div>');
        $('#editSubjectModal').modal('show');
        
        // Update form action
        $('#editSubjectForm').attr('action', '<?= base_url('admin/subjects/edit') ?>/' + id);

        // Fetch details
        $.get('<?= base_url('admin/subjects/get_details') ?>/' + id, function(res) {
            if (res.status === 'success') {
                $('#edit_name').val(res.subject.name);
                $('#edit_teacher_id').val(res.subject.teacher_id);
                $('#edit_total_mark').val(res.subject.total_mark);
                $('#edit_pass_mark').val(res.subject.pass_mark);
                $('#edit_sort').val(res.subject.sort);

                // Populate distributions
                let html = '';
                if (res.distributions.length > 0) {
                    res.distributions.forEach(dist => {
                        html += `
                            <div class="distribution-row row g-1 mb-2">
                                <div class="col-7"><input type="text" name="dist_name[]" class="form-control form-control-sm" value="${dist.name}" placeholder="الاسم"></div>
                                <div class="col-4"><input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" value="${dist.max_mark}" placeholder="الدرجة"></div>
                                <div class="col-1 text-end"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('.distribution-row').remove()"><i class='bx bx-trash'></i></button></div>
                            </div>`;
                    });
                } else {
                    html = '<p class="text-muted small text-center py-2 no-dist-msg">لا يوجد تقسيم مخصص لهذه المادة حالياً</p>';
                }
                $('#distributionContainerModal').html(html);
            } else {
                alert('فشل في جلب البيانات');
                $('#editSubjectModal').modal('hide');
            }
        });
    }

    function addDistributionRow(type = 'edit') {
        const container = type === 'edit' ? '#distributionContainerModal' : '#distributionContainerAdd';
        $('.no-dist-msg').remove();
        const row = `
            <div class="distribution-row row g-1 mb-2 animate__animated animate__fadeIn">
                <div class="col-7"><input type="text" name="dist_name[]" class="form-control form-control-sm" placeholder="الاسم"></div>
                <div class="col-4"><input type="number" step="0.5" name="dist_mark[]" class="form-control form-control-sm" placeholder="الدرجة"></div>
                <div class="col-1 text-end"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('.distribution-row').remove()"><i class='bx bx-trash'></i></button></div>
            </div>`;
        $(container).append(row);
    }

    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذه المادة؟ سيؤدي ذلك لحذف كافة الدرجات المرتبطة بها.')) {
            window.location.href = '<?= base_url('admin/subjects/delete') ?>/' + id + '/<?= $class_id ?>';
        }
    }
</script>
<?= $this->endSection() ?>
