<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
    <div>
        <h1 class="mb-1"><i class='bx bxs-school text-gold me-2'></i> إدارة الصفوف والفصول</h1>
        <p class="text-muted small ps-5 mb-0">تحديد الهيكل الدراسي للمدرسة وتنظيم الفصول التعليمية</p>
    </div>
    <button type="button" class="btn-sela btn-sela-gold shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
        <i class='bx bx-plus-circle me-1'></i> إضافة صف دراسي جديد
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 animate__animated animate__fadeIn">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="row g-4 mt-2">
    <?php if (empty($classes)): ?>
        <div class="col-12 text-center py-5 animate__animated animate__fadeIn">
            <div class="bg-light p-5 rounded-5 border border-dashed text-muted">
                <i class='bx bx-grid-alt fs-1 d-block mb-3'></i>
                <h5>لم يتم إضافة أي صفوف دراسية حتى الآن</h5>
                <p>ابدأ بإضافة الصفوف التي ترغب في تنظيمها للمدرسة.</p>
                <a href="<?= base_url('admin/classes/create') ?>" class="btn-sela btn-sela-primary mt-3">إضافة أول صف</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($classes as $c): ?>
            <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp">
                <div class="card-sela gold shadow-sm border-0 h-100 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-nile mb-0"><?= $c['name'] ?></h5>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm p-2" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2">
                                <li>
                                    <button class="dropdown-item py-2" type="button" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editClassModal"
                                            data-classid="<?= $c['class_id'] ?>"
                                            data-classname="<?= $c['name'] ?>"
                                            data-balance="<?= $c['balance'] ?? 0 ?>"
                                            data-system="<?= $c['study_system'] ?? 'semester' ?>">
                                        <i class='bx bx-edit-alt me-2 text-primary'></i> تعديل الصف
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item py-2 text-danger" type="button"
                                            onclick="confirmDelete('<?= base_url('admin/classes/delete/' . $c['class_id']) ?>', 'هل أنت متأكد من حذف هذا الصف بالكامل مع جميع فصوله؟')">
                                        <i class='bx bx-trash me-2 text-danger'></i> حذف الصف
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <h6 class="m-0 fw-bold text-nile fs-xsmall"><i class='bx bxs-door-open text-gold me-1'></i> الفصول الدراسية</h6>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-sm btn-soft-primary rounded-pill px-3 fs-xsmall fw-bold"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#addSectionModal"
                                        data-classid="<?= $c['class_id'] ?>"
                                        data-classname="<?= $c['name'] ?>">
                                    <i class='bx bx-plus-circle me-1'></i> إضافة فصل
                                </button>
                            </div>
                        </div>

                        <div class="list-group list-group-flush rounded-3 overflow-hidden border">
                            <?php if (empty($c['sections'])): ?>
                                <div class="list-group-item py-3 text-center text-muted small">لا توجد فصول مضافة بعد</div>
                            <?php else: ?>
                                <?php foreach ($c['sections'] as $s): ?>
                                    <div class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center border-0 border-top">
                                        <div class="d-flex align-items-center">
                                            <div class="badge-section bg-nile-light text-nile me-3">
                                                <i class='bx bx-door-open'></i>
                                            </div>
                                            <span class="text-nile fw-bold small"><?= $s['name'] ?></span>
                                        </div>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-light p-1 px-2 rounded me-1"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSectionModal"
                                                    data-sectionid="<?= $s['section_id'] ?>"
                                                    data-sectionname="<?= $s['name'] ?>"
                                                    data-classname="<?= $c['name'] ?>">
                                                <i class='bx bx-edit text-muted'></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light p-1 px-2 rounded text-danger" 
                                                    onclick="confirmDelete('<?= base_url('admin/sections/delete/' . $s['section_id']) ?>', 'هل تريد حذف هذا الفصل؟')">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-nile">
                    <i class='bx bx-door-open text-gold me-2'></i> إضافة فصل جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="addSectionForm">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-2 text-muted small">الصف الدراسي: <span id="modalClassName" class="fw-bold text-nile"></span></div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold text-nile">اسم الفصل</label>
                        <input type="text" name="name" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="مثال: فصل أ / فصل ب" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary px-4 shadow-sm">
                        <i class='bx bx-check-circle me-1'></i> حفظ وإضافة الفصل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-nile transition-300">
                    <i class='bx bx-edit text-gold me-2 pulse-gold'></i> تعديل الفصل
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editSectionForm">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-2 text-muted small">الصف الدراسي: <span id="editModalClassName" class="fw-bold text-nile"></span></div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold text-nile">اسم الفصل</label>
                        <input type="text" name="name" id="editSectionName" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="مثال: فصل أ / فصل ب" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary px-4 shadow-sm">
                        <i class='bx bx-save me-1'></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-nile">
                    <i class='bx bx-plus-circle text-gold me-2'></i> إضافة صف جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/classes/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">اسم الصف (بالعربي)</label>
                        <input type="text" name="name" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="مثال: الصف الأول الابتدائي" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">سعر الاشتراك (بالدينار)</label>
                        <input type="number" step="0.01" name="balance" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="0.00" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">نظام الدراسة في هذا الصف</label>
                        <div class="d-flex gap-3 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="study_system" value="semester" id="systemSemester" checked>
                                <label class="form-check-label small fw-bold" for="systemSemester">نظام السمستر</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="study_system" value="period" id="systemPeriod">
                                <label class="form-check-label small fw-bold" for="systemPeriod">نظام الفترات</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary px-4 shadow-sm">
                        <i class='bx bx-plus-circle me-1'></i> حفظ وإضافة الصف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-nile">
                    <i class='bx bx-edit-alt text-gold me-2'></i> تعديل بيانات الصف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editClassForm">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">اسم الصف (بالعربي)</label>
                        <input type="text" name="name" id="editClassNameInput" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="مثال: الصف الأول الابتدائي" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">سعر الاشتراك (بالدينار)</label>
                        <input type="number" step="0.01" name="balance" id="editClassBalanceInput" class="form-control rounded-3 p-3 shadow-sm border-0 bg-light" 
                               placeholder="0.00" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-nile px-1">نظام الدراسة</label>
                        <div class="d-flex gap-3 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="study_system" value="semester" id="editSystemSemester">
                                <label class="form-check-label small fw-bold" for="editSystemSemester">نظام السمستر</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="study_system" value="period" id="editSystemPeriod">
                                <label class="form-check-label small fw-bold" for="editSystemPeriod">نظام الفترات</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-primary px-4 shadow-sm">
                        <i class='bx bx-save me-1'></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Global Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="delete-icon-wrapper mb-3">
                    <i class='bx bx-error-circle text-danger fs-1'></i>
                </div>
                <h5 class="fw-bold text-nile mb-2">تنبيه تأكيد الحذف</h5>
                <p id="deleteModalMessage" class="text-muted small">هل أنت متأكد من رغبتك في حذف هذا العنصر نهائياً؟</p>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light flex-fill rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <a href="" id="deleteModalConfirmButton" class="btn btn-danger flex-fill rounded-3">حذف الآن</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-xsmall { font-size: 0.75rem; }
    .bg-nile-light { background-color: rgba(25, 42, 86, 0.05); }
    .badge-section { width: 35px; height: 35px; min-width: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    .btn-soft-primary { background: rgba(25, 42, 86, 0.05); color: #192a56; border: none; }
    .btn-soft-primary:hover { background: #192a56; color: #fff; }
    .delete-icon-wrapper { width: 60px; height: 60px; background: rgba(220, 53, 69, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
</style>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(url, message) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        document.getElementById('deleteModalMessage').textContent = message;
        document.getElementById('deleteModalConfirmButton').setAttribute('href', url);
        deleteModal.show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Add Section Modal Handler
        const addSectionModal = document.getElementById('addSectionModal');
        if (addSectionModal) {
            addSectionModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const classId = button.getAttribute('data-classid');
                const className = button.getAttribute('data-classname');
                const form = document.getElementById('addSectionForm');
                const modalClassNameSpan = document.getElementById('modalClassName');
                form.action = `<?= base_url('admin/sections/create/') ?>${classId}`;
                modalClassNameSpan.textContent = className;
                form.querySelector('input[name="name"]').value = '';
            });
        }

        // Edit Section Modal Handler
        const editSectionModal = document.getElementById('editSectionModal');
        if (editSectionModal) {
            editSectionModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const sectionId = button.getAttribute('data-sectionid');
                const sectionName = button.getAttribute('data-sectionname');
                const className = button.getAttribute('data-classname');
                const form = document.getElementById('editSectionForm');
                const modalClassNameSpan = document.getElementById('editModalClassName');
                const nameInput = document.getElementById('editSectionName');
                form.action = `<?= base_url('admin/sections/edit/') ?>${sectionId}`;
                modalClassNameSpan.textContent = className;
                nameInput.value = sectionName;
            });
        }

        // Edit Class Modal Handler
        const editClassModal = document.getElementById('editClassModal');
        if (editClassModal) {
            editClassModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const classId = button.getAttribute('data-classid');
                const className = button.getAttribute('data-classname');
                const balance = button.getAttribute('data-balance');
                const system = button.getAttribute('data-system');
                const form = document.getElementById('editClassForm');
                const nameInput = document.getElementById('editClassNameInput');
                const balanceInput = document.getElementById('editClassBalanceInput');
                form.action = `<?= base_url('admin/classes/edit/') ?>${classId}`;
                nameInput.value = className;
                balanceInput.value = balance;
                
                if (system === 'period') {
                    document.getElementById('editSystemPeriod').checked = true;
                } else {
                    document.getElementById('editSystemSemester').checked = true;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
