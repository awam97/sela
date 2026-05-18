<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-wallet text-gold me-2'></i> السجلات المالية</h1>
    <a href="#" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-plus-circle me-1'></i> إنشاء فاتورة جديدة
    </a>
</div>

<div class="card-sela gold">
    <div class="card-header">
        <i class='bx bx-receipt me-1'></i> قائمة الفواتير والمستحقات
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sela align-middle" id="financeTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-nile fw-bold">رقم الفاتورة</th>
                        <th class="text-nile fw-bold">الطالب</th>
                        <th class="text-nile fw-bold">البيان</th>
                        <th class="text-nile fw-bold">المبلغ الكلي</th>
                        <th class="text-nile fw-bold">المدفوع</th>
                        <th class="text-nile fw-bold">المتبقي</th>
                        <th class="text-nile fw-bold">الحالة</th>
                        <th class="text-nile fw-bold text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td class="fw-bold">#<?= $invoice['invoice_id'] ?></td>
                            <td>
                                <div class="text-nile fw-bold">طالب ID: <?= $invoice['student_id'] ?></div>
                                <small class="text-muted">نظام: فوري</small>
                            </td>
                            <td><?= $invoice['title'] ?></td>
                            <td class="text-nile fw-bold"><?= number_format($invoice['amount'], 2) ?></td>
                            <td class="text-success"><?= number_format($invoice['amount_paid'], 2) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($invoice['due'], 2) ?></td>
                            <td>
                                <?php if ($invoice['due'] <= 0): ?>
                                    <span class="badge bg-success bg-opacity-75">مدفوعة</span>
                                <?php elseif ($invoice['amount_paid'] > 0): ?>
                                    <span class="badge bg-warning text-dark bg-opacity-75">جزئية</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-75">غير مدفوعة</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm rounded">
                                    <a href="#" class="btn btn-outline-nile btn-sm" title="طباعة الفاتورة">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-gold btn-sm" title="تسجيل دفعة">
                                        <i class='bx bx-dollar-circle'></i>
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
        $('#financeTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
            },
            responsive: true,
            order: [[0, 'desc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                $('.dataTables_paginate .page-link').addClass('border-0 shadow-none');
            }
        });
    });
</script>
<?= $this->endSection() ?>
