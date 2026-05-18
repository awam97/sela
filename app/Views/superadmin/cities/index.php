<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center">
    <div>
        <h1><i class='bx bxs-map-alt text-gold me-2'></i> إدارة المدن والمناطق</h1>
        <p class="text-muted small ps-5 mb-0">تحديد النطاقات الجغرافية لتوزيع المدارس في النظام</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn-sela btn-sela-gold shadow-sm" data-bs-toggle="modal" data-bs-target="#addCityModal">
            <i class='bx bx-plus-circle me-1'></i> إضافة مدينة جديدة
        </button>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
            <?php foreach ($cities as $city): ?>
                <div class="col animate__animated animate__fadeIn">
                    <div class="card border-0 shadow-sm position-relative overflow-hidden city-card h-100">
                        <div class="city-card-glow"></div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-nile rounded-pill px-3 py-2 fw-bold fs-7 shadow-sm border border-light">#<?= $city['ID'] ?></span>
                                    <span class="badge bg-gold-light text-nile rounded-pill px-3 py-2 fw-bold fs-7 shadow-sm">
                                        <i class='bx bxs-school me-1 text-gold'></i> <?= $city['schools_count'] ?? 0 ?> مدرسة
                                    </span>
                                </div>
                                <h4 class="fw-extrabold text-nile mb-2 text-truncate"><?= $city['name'] ?></h4>
                                <p class="text-muted small mb-0"><i class='bx bx-map-pin text-gold me-1'></i> نطاق جغرافي نشط</p>
                            </div>
                            <div class="mt-4 pt-3 border-top border-light d-flex gap-2">
                                <button type="button" 
                                        class="btn-sela-outline btn-sela-sm flex-fill d-flex align-items-center justify-content-center" 
                                        onclick="editCity(<?= $city['ID'] ?>, '<?= $city['name'] ?>')"
                                        title="تعديل الاسم">
                                    <i class='bx bxs-edit-alt me-1'></i> تعديل
                                </button>
                                <button type="button" 
                                        class="btn-sela-danger-outline btn-sela-sm flex-fill d-flex align-items-center justify-content-center" 
                                        onclick="confirmDelete(<?= $city['ID'] ?>)" 
                                        title="حذف المدينة">
                                    <i class='bx bxs-trash me-1'></i> حذف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($cities)): ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7221376.png" width="150" class="mb-3 opacity-25">
                    <h6 class="text-muted">لم يتم إضافة أي مدن بعد</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card-sela gold shadow-sm border-0">
            <div class="card-header bg-white py-4 px-4 border-bottom">
                <h5 class="fw-bold text-nile mb-0 small-title-sela">إحصائيات المدن</h5>
            </div>
            <div class="card-body p-4 text-center">
                <div class="display-1 fw-bold text-gold"><?= count($cities) ?></div>
                <p class="text-muted">مدينة مسجلة في النظام حالياً</p>
                <hr class="opacity-10">
                <div class="text-start small text-muted">
                    <i class='bx bx-info-circle me-1'></i> يتم ربط كل مدرسة تضاف للنظام بإحدى هذه المدن لتسهيل عمليات الفلترة والتقارير الجغرافية.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add City Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-nile"><i class='bx bx-plus-circle text-gold me-2'></i> إضافة مدينة جديدة</h5>
                <button type="button" class="btn-close" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('superadmin/cities/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">اسم المدينة / المنطقة</label>
                        <input type="text" name="name" class="form-control form-control-lg border-light bg-light" placeholder="مثال: بنغازي، طرابلس..." required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-gold shadow-sm px-4">حفظ المدينة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-nile"><i class='bx bxs-edit-alt text-gold me-2'></i> تعديل بيانات المدينة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCityForm" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">اسم المدينة الحالي</label>
                        <input type="text" name="name" id="edit_city_name" class="form-control form-control-lg border-light bg-light" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-sela btn-sela-gold shadow-sm px-4">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .city-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 20px !important;
        background: #ffffff !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }
    .city-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(25, 42, 86, 0.08) !important;
        border-color: rgba(197, 160, 33, 0.2) !important;
    }
    .city-card-glow {
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(197, 160, 33, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
        transition: all 0.5s ease;
    }
    .city-card:hover .city-card-glow {
        transform: scale(1.5);
    }
    .bg-gold-light {
        background-color: rgba(197, 160, 33, 0.08) !important;
    }
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
    .fw-extrabold {
        font-weight: 800 !important;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function editCity(id, name) {
        $('#edit_city_name').val(name);
        $('#editCityForm').attr('action', '<?= base_url('superadmin/cities/update') ?>/' + id);
        var editModal = new bootstrap.Modal(document.getElementById('editCityModal'));
        editModal.show();
    }

    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذه المدينة؟ سيؤدي ذلك لمشاكل في المدارس المرتبطة بها في حال وجودها.')) {
            window.location.href = '<?= base_url('superadmin/cities/delete') ?>/' + id;
        }
    }
</script>
<?= $this->endSection() ?>
