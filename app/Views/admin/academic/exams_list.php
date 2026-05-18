<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bxs-calendar-event text-gold me-2'></i> إدارة الامتحانات</h1>
        <p class="text-muted small ps-5 mb-0">جدول المواعيد والاختبارات الدورية للمدرسة</p>
    </div>
    <a href="<?= base_url('admin/academic/exams/create') ?>" class="btn-sela btn-sela-primary shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إضافة امتحان جديد
    </a>
</div>

<div class="card-sela gold shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sela align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">اسم الامتحان</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th class="text-center pe-4">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($exams)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted italic">
                                <i class='bx bx-info-circle fs-2 d-block mb-2'></i>
                                لا توجد امتحانات مضافة حالياً لهذا العام الدراسي
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($exams as $e): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-nile"><?= $e['name'] ?></td>
                                <td>
                                    <span class="badge bg-light text-nile border px-2 py-2">
                                        <i class='bx bx-calendar me-1'></i> <?= $e['date'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($e['status'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">مؤرشف</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                        <a href="<?= base_url('admin/academic/exams/edit/' . $e['ID']) ?>" class="btn btn-white btn-sm px-2 text-nile" title="تعديل"><i class='bx bxs-edit-alt fs-5'></i></a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?= $e['ID'] ?>)" class="btn btn-white btn-sm px-2 text-danger" title="حذف"><i class='bx bxs-trash fs-5'></i></a>
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

<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الامتحان؟')) {
            window.location.href = '<?= base_url('admin/academic/exams/delete') ?>/' + id;
        }
    }
</script>
<?= $this->endSection() ?>
