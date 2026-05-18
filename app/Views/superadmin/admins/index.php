<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1><i class='bx bxs-user-account text-gold me-2'></i> إدارة حسابات المدارس</h1>
        <p class="text-muted small ps-5 mb-0">إدارة حسابات المدراء التنفيذيين وصلاحيات الدخول لكل مؤسسة</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('superadmin/admins/create') ?>" class="btn-sela btn-sela-gold shadow-sm">
            <i class='bx bx-plus-circle me-1'></i> إنشاء حساب مدير جديد
        </a>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    <?php foreach ($admins as $admin): ?>
        <div class="col animate__animated animate__fadeIn">
            <div class="card border-0 shadow-sm admin-card h-100 position-relative overflow-hidden">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Top Row: Username & Status -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <code class="bg-light p-1.5 px-2 rounded border-0 small text-nile fw-bold">@<?= $admin['username'] ?></code>
                            <span class="badge bg-soft-success text-success rounded-pill px-2.5 py-1 fw-bold fs-7.5">
                                <i class='bx bxs-circle me-1' style="font-size: 0.5rem; vertical-align: middle;"></i> نشط
                            </span>
                        </div>
                        
                        <!-- Middle Row: Avatar, Name, Phone -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-admin me-2.5 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(25, 42, 86, 0.04) !important; color: var(--primary-color) !important;">
                                <i class='bx bxs-user-circle fs-3'></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-extrabold text-nile mb-0.5 text-truncate" title="<?= $admin['name'] ?>"><?= $admin['name'] ?></h6>
                                <small class="text-muted d-block" style="font-size: 0.72rem;"><i class='bx bx-phone me-1 text-muted'></i> <?= $admin['phone'] ?></small>
                            </div>
                        </div>

                        <!-- Box: Organization details (Minimal List) -->
                        <div class="card-details-box mb-3 border-top border-light pt-2.5">
                            <div class="d-flex align-items-center mb-1.5 text-muted" style="font-size: 0.78rem;">
                                <i class='bx bxs-school me-2 text-muted fs-6'></i>
                                <span>المؤسسة: <strong class="text-nile ms-1 text-truncate" style="max-width: 190px; display: inline-block; vertical-align: bottom;" title="<?= $admin['school_name'] ?? 'بدون مدرسة' ?>"><?= $admin['school_name'] ?? 'بدون مدرسة' ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center text-muted" style="font-size: 0.78rem;">
                                <i class='bx bx-envelope me-2 text-muted fs-6'></i>
                                <span>البريد: <strong class="text-nile ms-1 text-truncate" style="max-width: 190px; display: inline-block; vertical-align: bottom;" title="<?= $admin['email'] ?? 'لم يضبط' ?>"><?= $admin['email'] ?? 'لم يضبط' ?></strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Actions (Borderless Minimal) -->
                    <div class="pt-2 border-top border-light d-flex align-items-center justify-content-end gap-1">
                        <a href="<?= base_url('superadmin/admins/edit/'.$admin['admin_id']) ?>" 
                           class="btn-minimal-nile flex-fill justify-content-center">
                            <i class='bx bxs-edit-alt me-1'></i> تعديل الحساب
                        </a>
                        <button onclick="confirmDelete(<?= $admin['admin_id'] ?>)" 
                                class="btn-minimal-danger flex-fill justify-content-center">
                            <i class='bx bxs-trash me-1'></i> حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($admins)): ?>
        <div class="col-12 text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7221376.png" width="180" class="mb-3 opacity-25">
            <h6 class="text-muted">لم يتم إضافة أي حسابات مسؤولين بعد</h6>
        </div>
    <?php endif; ?>
</div>

<style>
    .btn-sela-outline {
        border: 1px solid #e2e8f0;
        background: transparent;
        color: #475569;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-sela-outline:hover {
        border-color: #C5A021;
        color: #C5A021;
        background-color: rgba(197, 160, 33, 0.03);
    }
    .btn-sela-danger-outline {
        border: 1px solid #fecaca;
        background: transparent;
        color: #ef4444;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-sela-danger-outline:hover {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الحساب نهائياً؟')) {
            window.location.href = '<?= base_url('superadmin/admins/delete') ?>/' + id;
        }
    }
</script>
<?= $this->endSection() ?>
