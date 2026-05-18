<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="mb-1"><i class='bx bxs-user-plus text-gold me-2'></i> طلبات التسجيل الجديدة</h1>
        <p class="text-muted small ps-5 mb-0">مراجعة واعتماد طلبات الالتحاق الذاتي للطلاب</p>
    </div>
</div>

<div class="card-sela shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sela mb-0">
                <thead>
                    <tr>
                        <th>تاريخ الطلب</th>
                        <th>اسم الطالب</th>
                        <th>رقم الهاتف</th>
                        <th>الصف المطلوب</th>
                        <th>الجنس</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($requests)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">لا توجد طلبات تسجيل معلقة حالياً</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($requests as $r): ?>
                        <tr id="req-<?= $r['id'] ?>">
                            <td class="small text-muted"><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                            <td><h6 class="fw-bold mb-0 text-nile"><?= $r['name'] ?></h6></td>
                            <td dir="ltr" class="text-end small"><i class='bx bxl-whatsapp text-success me-1'></i> <?= $r['phone'] ?></td>
                            <td><span class="badge bg-gold-light text-gold"><?= $r['class_name'] ?></span></td>
                            <td><?= $r['sex'] == 'male' ? 'ذكر' : 'أنثى' ?></td>
                            <td class="text-center">
                                <button onclick="approveRequest(<?= $r['id'] ?>)" class="btn btn-sm btn-sela btn-sela-gold me-1 shadow-none">
                                    <i class='bx bx-check me-1'></i> قبول الطلب
                                </button>
                                <button onclick="rejectRequest(<?= $r['id'] ?>)" class="btn btn-sm btn-light text-danger rounded-pill shadow-none px-3">
                                    <i class='bx bx-x me-1'></i> رفض
                                </button>
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
<script>
    function approveRequest(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم إنشاء حساب للطالب وتفعيل انضمامه للمدرسة فوراً",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، قم بالقبول',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.post('<?= base_url('admin/registrations/approve') ?>/' + id, {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }, function(res) {
                    Swal.close();
                    if(res.status === 'success') {
                        $(`#req-${id}`).fadeOut();
                        showAlert('success', res.message);
                    } else {
                        showAlert('error', res.message);
                    }
                });
            }
        })
    }

    function rejectRequest(id) {
        Swal.fire({
            title: 'رفض الطلب؟',
            text: "لن يتمكن الطالب من التسجيل بهذا الرقم مجدداً إلا بعد مراجعة الإدارة",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'نعم، ارفض الطلب',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.post('<?= base_url('admin/registrations/reject') ?>/' + id, {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }, function(res) {
                    Swal.close();
                    if(res.status === 'success') {
                        $(`#req-${id}`).fadeOut();
                        showAlert('success', res.message);
                    } else {
                        showAlert('error', res.message);
                    }
                });
            }
        })
    }

    function showLoading() {
        Swal.fire({
            title: 'جاري المعالجة...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }
</script>
<?= $this->endSection() ?>
