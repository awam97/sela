<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-1"><i class='bx bxs-edit-location text-gold me-2'></i> الواجبات المنزلية</h1>
        <p class="text-muted small ps-5 mb-0">قائمة المهام والواجبات اليومية المقررة للصف الحالي</p>
    </div>
    <a href="<?= base_url('admin/homework/create/' . $class_id) ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إضافة واجب جديد
    </a>
</div>

<div class="card-sela gold shadow-sm mb-4 border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-sela-homework">
                <thead class="bg-soft-gold">
                    <tr>
                        <th class="ps-4 py-3 text-nile" style="width: 150px;">تاريخ النشر</th>
                        <th style="width: 200px;">المادة الدراسية</th>
                        <th>تفاصيل الواجب المنزلي</th>
                        <th class="text-center pe-4" style="width: 150px;">الخيارات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($homeworks)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-box-4816210-4012154.png" width="120" class="mb-3 opacity-50">
                                    <h6 class="text-muted fw-bold">لا توجد واجبات مضافة حالياً</h6>
                                    <p class="small text-muted">يمكنك البدء بإضافة أول واجب لهذا الصف من الزر بالأعلى</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($homeworks as $h): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="date-badge">
                                        <div class="date-day"><?= date('d', $h['timestamp']) ?></div>
                                        <div class="date-month"><?= date('M', $h['timestamp']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="subject-tag-mini me-2"></div>
                                        <div class="fw-bold text-nile"><?= $h['subject_name'] ?? 'مادة عامة' ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="homework-content-box">
                                        <div class="fw-bold text-nile mb-1"><?= $h['title'] ?></div>
                                        <div class="text-muted small homework-desc-text">
                                            <?= strip_tags($h['description']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group-sela">
                                        <a href="<?= base_url('admin/homework/edit/' . $h['ID']) ?>" 
                                           class="btn-action shadow-sm" title="تعديل الواجب">
                                            <i class='bx bxs-edit-alt me-1'></i> تعديل
                                        </a>
                                        <button onclick="confirmDelete(<?= $h['ID'] ?>, <?= $class_id ?>)" 
                                           class="btn-action-danger shadow-sm" title="حذف الواجب">
                                            <i class='bx bxs-trash me-1'></i> حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-sela-homework thead th { font-weight: 800; font-size: 0.85rem; border-bottom: 2px solid #f8f9fa; }
    .table-sela-homework tbody td { padding: 20px 10px; border-bottom: 1px solid #f9f9f9; }
    
    .date-badge { width: 55px; height: 55px; background: #fdfaf0; border: 1px solid #fdf8e8; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .date-day { font-size: 1.2rem; font-weight: 900; color: var(--secondary-color); line-height: 1; }
    .date-month { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #a18a4d; }
    
    .subject-tag-mini { width: 4px; height: 20px; background: var(--secondary-color); border-radius: 2px; }
    .homework-desc-text { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; opacity: 0.8; line-height: 1.4; }
    
    .btn-group-sela { display: flex; gap: 8px; justify-content: center; }
    .btn-action { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #eee; border-radius: 12px; color: var(--primary-color); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action:hover { background: var(--primary-color); color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(25, 42, 86, 0.1) !important; }
    .btn-action-danger { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #fee2e2; border-radius: 12px; color: #ef4444; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action-danger:hover { background: #ef4444; color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1) !important; }
    
    .bg-soft-gold { background-color: #fdfaf0; }
</style>

<script>
    function confirmDelete(id, classId) {
        if (confirm('هل أنت متأكد من حذف هذا الواجب؟')) {
            window.location.href = '<?= base_url('admin/homework/delete') ?>/' + id + '/' + classId;
        }
    }
</script>
<?= $this->endSection() ?>
