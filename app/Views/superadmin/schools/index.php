<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1><i class='bx bxs-school text-gold me-2'></i> <?= $system_settings['sa_schools_title'] ?></h1>
        <p class="text-muted small ps-5 mb-0"><?= $system_settings['sa_schools_subtitle'] ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('superadmin/schools/create') ?>" class="btn-sela btn-sela-gold shadow-sm">
            <i class='bx bx-plus-circle me-1'></i> <?= $system_settings['sa_schools_add_btn'] ?>
        </a>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    <?php foreach ($schools as $school): ?>
        <div class="col animate__animated animate__fadeIn">
            <div class="card border-0 shadow-sm school-card h-100 position-relative overflow-hidden">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Top Row: City & Academic Year -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-light text-nile rounded-pill px-2.5 py-1.5 fw-bold fs-7.5">
                                <i class='bx bxs-map me-1 text-muted'></i> <?= $school['city_name'] ?? 'غير محدد' ?>
                            </span>
                            <span class="text-muted fw-bold" style="font-size: 0.75rem;">
                                <i class='bx bx-calendar me-1'></i> <?= $school['year'] ?>
                            </span>
                        </div>
                        
                        <!-- Middle Row: Logo Avatar, Title, Address -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-school me-2.5 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(25, 42, 86, 0.04) !important; color: var(--primary-color) !important;">
                                <i class='bx bxs-school fs-4'></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-extrabold text-nile mb-0.5 text-truncate" title="<?= $school['name'] ?>"><?= $school['name'] ?></h6>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.72rem;"><i class='bx bx-map-pin me-1 text-muted'></i> <?= $school['address'] ?></small>
                            </div>
                        </div>

                        <!-- Box: Managers info (Minimal List) -->
                        <div class="card-details-box mb-3 border-top border-light pt-2.5">
                            <div class="d-flex align-items-center mb-1.5 text-muted" style="font-size: 0.78rem;">
                                <i class='bx bx-user me-2 text-muted fs-6'></i>
                                <span>المدير العام: <strong class="text-nile ms-1"><?= $school['manager'] ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center text-muted" style="font-size: 0.78rem;">
                                <i class='bx bx-spreadsheet me-2 text-muted fs-6'></i>
                                <span>مسؤول الامتحانات: <strong class="text-nile ms-1"><?= $school['exams_manager'] ?></strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Contact & Actions (Borderless Minimal) -->
                    <div class="pt-2 border-top border-light d-flex align-items-center justify-content-between gap-1">
                        <a href="mailto:<?= $school['email'] ?>" class="btn-minimal-nile p-1.5" style="border-radius: 8px;" title="راسل المدرسة: <?= $school['email'] ?>">
                            <i class='bx bx-envelope fs-5'></i>
                        </a>
                        <div class="d-flex gap-1 flex-fill justify-content-end">
                            <a href="<?= base_url('superadmin/schools/edit/'.$school['ID']) ?>" 
                               class="btn-minimal-nile">
                                <i class='bx bxs-edit-alt me-1'></i> تعديل
                            </a>
                            <button onclick="confirmDelete(<?= $school['ID'] ?>)" 
                                    class="btn-minimal-danger">
                                <i class='bx bxs-trash me-1'></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($schools)): ?>
        <div class="col-12 text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7221376.png" width="180" class="mb-3 opacity-25">
            <h6 class="text-muted">لم يتم إضافة أي مدارس بعد</h6>
            <a href="<?= base_url('superadmin/schools/create') ?>" class="btn-sela btn-sela-gold mt-3 rounded-pill px-4">ابدأ بإضافة أول مدرسة</a>
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
        if (confirm('هل أنت متأكد من حذف هذه المدرسة نهائياً؟ سيتم حذف كافة البيانات المرتبطة بها.')) {
            window.location.href = '<?= base_url('superadmin/schools/delete') ?>/' + id;
        }
    }
</script>
<?= $this->endSection() ?>
