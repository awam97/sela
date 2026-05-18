<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-group text-gold me-2'></i> الطلاب - الصف :
        <?= $students[0]['class_name'] ?? '...' ?>
    </h1>
    <div class="d-flex gap-2">
        <button onclick="openCreateModal()" class="btn-sela btn-sela-gold shadow-sm">
            <i class='bx bx-plus-circle me-1'></i> إضافة طالب جديد
        </button>
    </div>
</div>

<div class="card-sela primary shadow-sm mb-4">
    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded-circle me-3 border border-gold d-none d-sm-block">
                <i class='bx bxs-school text-gold fs-4'></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-nile sub-title-sela">تصفية العرض الحالية</h6>
                <p class="text-muted small mb-0">
                    عدد الطلاب: <span class="badge bg-gold">
                        <?= count($students) ?>
                    </span> |
                    الصف: <span class="badge bg-nile">
                        <?= $students[0]['class_name'] ?? 'غير محدد' ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <!-- View Toggle -->
            <div class="view-toggle-wrapper d-flex bg-light p-1 rounded-3 border">
                <button class="view-toggle-btn active" data-view="list" title="عرض القائمة">
                    <i class='bx bx-list-ul fs-5'></i>
                </button>
                <button class="view-toggle-btn" data-view="grid" title="عرض الشبكة">
                    <i class='bx bx-grid-alt fs-5'></i>
                </button>
            </div>

            <!-- Section Filter -->
            <div class="filter-toggle-group d-flex bg-white p-1 rounded-3 border ms-lg-2">
                <a href="<?= base_url("admin/students/list/{$class_id}/all") ?>" class="btn btn-sm px-3 py-2
                    <?= ($section_id == 'all') ? 'btn-sela-gold active shadow-sm' : 'text-muted border-0' ?>">
                    الكل
                </a>
                <?php foreach ($sections as $sec): ?>
                    <a href="<?= base_url("admin/students/list/{$class_id}/{$sec['section_id']}") ?>"
                        class="btn btn-sm px-3 py-2
                    <?= ($section_id == $sec['section_id']) ? 'btn-sela-gold active shadow-sm' : 'text-muted border-0' ?>">
                        <?= $sec['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="btn-group shadow-sm ms-auto">
                <button class="btn btn-white btn-sm border"><i class='bx bx-printer me-1'></i> طباعة</button>
                <button class="btn btn-white btn-sm border"><i class='bx bx-export me-1'></i> تصدير</button>
            </div>
        </div>
    </div>
</div>

<div id="dynamic-student-content" class="animate__animated animate__fadeIn">
    <?= view('admin/students/partials/student_list_table', ['students' => $students, 'view_pref' => 'list']) ?>
</div>

<style>
    .student-avatar-list { width: 48px; height: 48px; object-fit: cover; }
    .status-indicator { position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; border: 2px solid #fff; border-radius: 50%; }
    
    .badge-sela-success { background: #e6f7ef; color: #10b981; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.1); }
    .badge-sela-danger { background: #fee2e2; color: #ef4444; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.1); }
    
    .btn-group-sela { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
    .btn-action { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #eee; border-radius: 12px; color: var(--primary-color); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action:hover { background: var(--primary-color); color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(25, 42, 86, 0.1) !important; }
    .btn-action-danger { padding: 8px 16px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #fee2e2; border-radius: 12px; color: #ef4444; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; font-size: 0.8rem; font-weight: 700; white-space: nowrap; }
    .btn-action-danger:hover { background: #ef4444; color: #fff; transform: translateY(-5px) scale(1.05); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1) !important; }

    /* Premium Grid Cards */
    .student-card-premium { background: #fff; border-radius: 20px; border: 1px solid #f0f0f0; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%; text-align: center; }
    .student-card-premium:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(25, 42, 86, 0.08) !important; border-color: var(--secondary-color); }
    .student-card-header { height: 80px; background: linear-gradient(45deg, var(--primary-color), #2d4073); position: relative; margin-bottom: 50px; }
    .student-avatar-wrapper-premium { position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); }
    .student-avatar-wrapper-premium img { width: 90px; height: 90px; object-fit: cover; }
    .student-status-badge { position: absolute; top: 5px; right: 8px; width: 14px; height: 14px; border: 3px solid #fff; border-radius: 50%; }
    .student-status-badge.active { background: #10b981; }
    .student-status-badge.inactive { background: #ef4444; }
    .student-name-premium { font-weight: 800; font-size: 1.1rem; }
    .student-contact-pill { font-size: 0.8rem; color: #666; background: #f8fafc; padding: 6px 15px; border-radius: 50px; display: inline-block; }
    .student-card-body { padding: 0 20px 25px 20px; }
    .btn-icon-round { width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 1.2rem; transition: all 0.2s; text-decoration: none; }
    .btn-icon-round:hover { background: var(--secondary-color); color: #fff; transform: rotate(10deg); }
    .bg-soft-gold { background: #fffcf2; }
</style>

<style>
    /* Premium Styling for Student Management */
    .view-toggle-btn.active { background: var(--secondary-color); color: #fff; border-color: var(--secondary-color); }
    #dynamic-student-content { min-height: 400px; }
    .btn-action, .btn-action-danger { cursor: pointer; border: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Toggle Logic
        $(document).on('click', '.view-toggle-btn', function () {
            const view = $(this).data('view');
            $('.view-toggle-btn').removeClass('active');
            $(this).addClass('active');

            if (view === 'grid') {
                $('#students-list-view').hide();
                $('#students-grid-view').css('display', 'flex').hide().fadeIn(300);
            } else {
                $('#students-grid-view').hide();
                $('#students-list-view').fadeIn(300);
            }
            localStorage.setItem('student_view_pref', view);
        });

        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لا يمكن التراجع عن حذف الطالب وسجلاته!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، احذف الطالب',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/students/delete') ?>/' + id,
                        type: 'POST',
                        success: function(res) {
                            if (res.status === 'success') {
                                showAlert('success', res.message);
                                refreshList();
                            } else {
                                showAlert('error', res.message);
                            }
                        }
                    });
                }
            });
        };

        window.refreshList = function() {
            const view = localStorage.getItem('student_view_pref') || 'list';
            $('#dynamic-student-content').css('opacity', '0.5');
            $.get('<?= base_url("admin/students/fetch_list/{$class_id}/{$section_id}") ?>?view=' + view, function(res) {
                $('#dynamic-student-content').html(res).css('opacity', '1');
            });
        }
    });
</script>
<?= $this->endSection() ?>