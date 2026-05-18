<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="mb-1"><i class='bx bxs-sort-alt text-gold me-2'></i> تنظيم مركز الخدمات</h1>
        <p class="text-muted small ps-5 mb-0">قم بسحب وإفلات العناصر لتغيير ترتيب ظهورها في مركز الطلاب لجميع المدارس</p>
    </div>
    <div class="d-flex gap-2">
        <button id="saveOrderBtn" class="btn-sela btn-sela-gold shadow-sm px-4">
            <i class='bx bx-save me-1'></i> حفظ الترتيب الجديد
        </button>
        <a href="<?= base_url('superadmin/dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-4">عودة</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela shadow-sm border-0 bg-white overflow-hidden">
            <div class="card-body p-0">
                <div id="sortable-list" class="list-group list-group-flush">
                    <?php foreach ($services as $s): ?>
                        <div class="list-group-item p-4 d-flex align-items-center cursor-move" data-id="<?= $s['id'] ?>">
                            <div class="me-4 text-muted fs-3 handle">
                                <i class='bx bx-grid-vertical'></i>
                            </div>
                            <div class="service-icon-wrapper-sm bg-nile-light text-nile me-4">
                                <i class='bx <?= $s['icon'] ?>'></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-nile"><?= $s['title'] ?></h6>
                                <p class="text-muted small mb-0"><?= $s['description'] ?></p>
                            </div>
                            <div class="badge bg-gold-light text-gold p-2 px-3 rounded-pill">
                                معرف: <?= $s['service_id'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-move { cursor: move; }
    .service-icon-wrapper-sm {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .list-group-item { transition: all 0.2s; border-left: 4px solid transparent; }
    .list-group-item.sortable-ghost { opacity: 0.4; background: #f8fafc; border-left-color: var(--secondary-color); }
    .list-group-item:hover { background-color: #fbfbfc; }
    .handle { cursor: grab; }
    .handle:active { cursor: grabbing; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const el = document.getElementById('sortable-list');
    const sortable = new Sortable(el, {
        animation: 150,
        handle: '.handle',
        ghostClass: 'sortable-ghost'
    });

    $('#saveOrderBtn').on('click', function() {
        const order = sortable.toArray();
        const btn = $(this);
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...');

        $.post('<?= base_url('superadmin/student-center/save-order') ?>', {
            order: order,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            btn.prop('disabled', false).html("<i class='bx bx-save me-1'></i> حفظ الترتيب الجديد");
            
            if (res.status === 'success') {
                showAlert('success', res.message);
            } else {
                showAlert('error', res.message);
            }
        }).fail(function() {
            btn.prop('disabled', false).html("<i class='bx bx-save me-1'></i> حفظ الترتيب الجديد");
            showAlert('error', 'حدث خطأ غير متوقع');
        });
    });
</script>
<?= $this->endSection() ?>
