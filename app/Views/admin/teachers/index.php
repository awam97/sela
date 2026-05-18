<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-user-voice text-gold me-2'></i> هيئة التدريس</h1>
    <a href="<?= base_url('admin/teachers/create') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إضافة معلم جديد
    </a>
</div>

<div class="card-sela gold">
    <div class="card-header">
        <i class='bx bx-list-ul me-1'></i> قائمة المعلمين والمدربين
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sela align-middle" id="teachersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-nile fw-bold">الاسم الرسمي</th>
                        <th class="text-nile fw-bold">رقم الهاتف</th>
                        <th class="text-nile fw-bold">اسم المستخدم</th>
                        <th class="text-nile fw-bold">الحالة</th>
                        <th class="text-nile fw-bold text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($teacher['name']) ?>&background=192A56&color=fff" class="rounded-circle border border-2 border-gold" width="40">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-nile"><?= $teacher['name'] ?></div>
                                        <small class="text-muted">Code: T-<?= $teacher['teacher_id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= $teacher['phone'] ?></td>
                            <td><code class="text-gold"><?= $teacher['username'] ?></code></td>
                            <td>
                                <?php if ($teacher['status'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-75">نشط</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-75">غير نشط</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm rounded">
                                    <a href="<?= base_url('admin/teachers/edit/' . $teacher['teacher_id']) ?>" class="btn btn-outline-nile btn-sm">
                                        <i class='bx bxs-edit-alt'></i>
                                    </a>
                                    <a href="<?= base_url('admin/teachers/delete/' . $teacher['teacher_id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">
                                        <i class='bx bxs-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#teachersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
            },
            responsive: true,
            order: [[0, 'asc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                $('.dataTables_paginate .page-link').addClass('border-0 shadow-none');
            }
        });
    });
</script>
<?= $this->endSection() ?>
