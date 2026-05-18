<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-book-bookmark text-gold me-2'></i> جدول الامتحانات</h1>
    <a href="#" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إضافة امتحان جديد
    </a>
</div>

<div class="card-sela primary animate__animated animate__fadeIn">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sela align-middle mb-0" id="examsTable">
                <thead>
                    <tr>
                        <th class="ps-4">اسم الاختيار/الامتحان</th>
                        <th>تاريخ الانعقاد</th>
                        <th>الدرجة النهائية</th>
                        <th>حالة الاعتماد</th>
                        <th class="text-center pe-4">الخيارات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="exam-icon-box me-3">
                                        <i class='bx bxs-認定'></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-nile fs-6 mb-0"><?= $exam['name'] ?></div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Sela Exam ID: #<?= $exam['exam_id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="date-pill shadow-sm me-2">
                                        <i class='bx bx-calendar-event me-1'></i>
                                        <?= is_numeric($exam['date']) ? date('M d, Y', $exam['date']) : $exam['date'] ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-sela-nile"><?= $exam['marks'] ?> درجة</span>
                            </td>
                            <td>
                                <?php if ($exam['status'] == 1): ?>
                                    <span class="badge-sela-success"><i class='bx bxs-check-shield me-1'></i> معتمد نهائياً</span>
                                <?php else: ?>
                                    <span class="badge-sela-warning"><i class='bx bxs-hourglass-top me-1'></i> قيد المراجعة</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4" style="min-width: 220px;">
                                <div class="btn-group-sela">
                                    <a href="#" class="btn-action shadow-sm" title="عرض النتائج">
                                        <i class='bx bxs-bar-chart-alt-2 me-1'></i> نتائج
                                    </a>
                                    <a href="#" class="btn-action shadow-sm" title="تعديل">
                                        <i class='bx bxs-edit-alt me-1'></i> تعديل
                                    </a>
                                    <button class="btn-action-danger shadow-sm" title="حذف">
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

<style>
    .exam-icon-box { width: 40px; height: 40px; background: #f4f6fb; color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .date-pill { background: #fffcf2; color: #a18a4d; padding: 6px 15px; border-radius: 50px; border: 1px solid rgba(197, 160, 33, 0.1); font-size: 0.85rem; font-weight: 700; }
    
    .badge-sela-nile { background: #f1f5f9; color: var(--primary-color); padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(25, 42, 86, 0.1); }
    .badge-sela-gold { background: #fffcf2; color: var(--secondary-color); padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(197, 160, 33, 0.1); }
    .badge-sela-success { background: #e6f7ef; color: #10b981; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.1); }
    .badge-sela-warning { background: #fffbeb; color: #d97706; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(217, 119, 6, 0.1); }

    .btn-group-sela { display: flex; gap: 8px; justify-content: center; }
    .btn-action { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #eee; border-radius: 12px; color: var(--primary-color); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action:hover { background: var(--primary-color); color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(25, 42, 86, 0.1) !important; }
    .btn-action-danger { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #fee2e2; border-radius: 12px; color: #ef4444; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action-danger:hover { background: #ef4444; color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1) !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#examsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
            },
            responsive: true,
            order: [[1, 'desc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                $('.dataTables_paginate .page-link').addClass('border-0 shadow-none');
            }
        });
    });
</script>
<?= $this->endSection() ?>
