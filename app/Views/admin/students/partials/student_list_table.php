<!-- List View Snippet -->
<div id="students-list-view" class="view-container <?= ($view_pref == 'list' ? 'active' : '') ?> animate__animated animate__fadeIn">
    <div class="card-sela primary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sela align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">الطالب</th>
                            <th>رقم القيد</th>
                            <th>رقم الهاتف</th>
                            <th>حالة الحساب</th>
                            <th class="text-center pe-4">الإجراءات السريعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr id="student-row-<?= $student['student_id'] ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-container me-3 position-relative">
                                            <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>"
                                                class="rounded-circle border border-2 border-gold shadow-sm student-avatar-list"
                                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff'">
                                            <div class="status-indicator <?= ($student['activate'] == 0 ? 'bg-success' : 'bg-danger') ?>"></div>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-nile fs-6 mb-0">
                                                <?= $student['name'] ?>
                                            </div>
                                            <div class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class='bx bx-hash text-gold me-1'></i> Sela Student ID: <?= $student['student_id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-gold font-monospace">
                                        <?= $student['nationalid'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="tel:<?= $student['phone'] ?>" class="text-decoration-none text-nile">
                                        <i class='bx bx-phone text-muted me-1'></i> <?= $student['phone'] ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($student['activate'] == 0): ?>
                                        <span class="badge-sela-success">نشط بالمنصة</span>
                                    <?php else: ?>
                                        <span class="badge-sela-danger">موقوف مؤقتاً</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4" style="min-width: 200px;">
                                    <div class="btn-group-sela">
                                        <a href="<?= base_url('admin/students/profile/' . $student['student_id']) ?>"
                                            class="btn-action shadow-sm" title="الملف الكامل">
                                            <i class='bx bxs-user-detail me-1'></i> ملف
                                        </a>
                                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($student)) ?>)"
                                            class="btn-action shadow-sm" title="تعديل">
                                            <i class='bx bxs-edit-alt me-1'></i> تعديل
                                        </button>
                                        <button onclick="confirmDelete(<?= $student['student_id'] ?>)"
                                            class="btn-action-danger shadow-sm" title="حذف الطالب">
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

<!-- Grid View Snippet -->
<div id="students-grid-view" class="view-container student-grid row g-4 mt-1 animate__animated animate__fadeIn" style="display: <?= ($view_pref == 'grid' ? 'flex' : 'none') ?>;">
    <?php foreach ($students as $student): ?>
        <div class="col-xl-3 col-md-4 col-sm-6 col-12" id="student-grid-<?= $student['student_id'] ?>">
            <div class="student-card-premium shadow-sm">
                <div class="student-card-header">
                    <div class="student-avatar-wrapper-premium">
                        <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>"
                            class="rounded-circle border border-4 border-white shadow"
                            onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff'">
                        <div class="student-status-badge <?= ($student['activate'] == 0 ? 'active' : 'inactive') ?>"></div>
                    </div>
                </div>
                
                <div class="student-card-body">
                    <h6 class="student-name-premium text-nile mb-1">
                        <?= $student['name'] ?>
                    </h6>
                    <div class="student-info-tag mb-3">
                        <span class="badge bg-soft-gold text-gold p-1 px-2 rounded-pill small">
                            <i class='bx bx-fingerprint me-1'></i> <?= $student['nationalid'] ?>
                        </span>
                    </div>

                    <div class="student-contact-pill mb-4">
                        <i class='bx bx-phone me-1'></i> <?= $student['phone'] ?: 'بدون هاتف' ?>
                    </div>

                    <div class="student-card-footer d-flex justify-content-center gap-2">
                        <a href="<?= base_url('admin/students/profile/' . $student['student_id']) ?>" 
                           class="btn-action shadow-sm px-3" title="استعراض الملف">
                            <i class='bx bxs-user-circle me-1'></i> ملف
                        </a>
                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($student)) ?>)" 
                           class="btn-action shadow-sm px-3" title="تعديل البيانات">
                            <i class='bx bxs-edit-alt me-1'></i> تعديل
                        </button>
                        <button onclick="confirmDelete(<?= $student['student_id'] ?>)" 
                           class="btn-action-danger shadow-sm px-3" title="حذف">
                            <i class='bx bxs-trash me-1'></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($students)): ?>
    <div class="text-center py-5 mt-4 card-sela primary border-dashed">
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7221376.png" width="180"
            class="mb-3 opacity-25">
        <h6 class="text-muted">لا يوجد طلاب مسجلين حالياً</h6>
    </div>
<?php endif; ?>
