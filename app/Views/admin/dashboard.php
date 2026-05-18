<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-dashboard text-gold me-2'></i> لوحة التحكم</h1>
    <div>
        <span class="badge bg-nile p-2 rounded-pill shadow-sm">
            <?= $system_name ?> - العام الدراسي <?= date('Y') ?>
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Quick Actions Panel -->
    <div class="col-12">
        <div class="card-sela gold shadow-sm overflow-hidden border-0">
            <div class="card-body p-4 bg-glass-gold">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
                    <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bxs-bolt-circle me-2 text-gold'></i> الإجراءات السريعة</h6>
                    <span class="text-muted small">الوصول الفوري لأكثر الأدوات استخداماً</span>
                </div>
                <div class="quick-actions-row py-2">
                    <a href="javascript:void(0);" onclick="openCreateModal()" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-user-plus'></i></div>
                        <span class="qa-label">تسجيل طالب</span>
                    </a>
                    <a href="javascript:void(0);" onclick="openSelectionModal('10', 'رصد الدرجات')" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-edit-alt'></i></div>
                        <span class="qa-label">رصد الدرجات</span>
                    </a>
                    <a href="javascript:void(0);" onclick="openSelectionModal('6', 'الواجبات المنزلية')" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-calendar-edit'></i></div>
                        <span class="qa-label">إضافة واجب</span>
                    </a>
                    <a href="<?= base_url('admin/reports') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-printer'></i></div>
                        <span class="qa-label">التقارير</span>
                    </a>
                    <a href="javascript:void(0);" onclick="openSelectionModal('7', 'الجداول الدراسية')" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-time-five'></i></div>
                        <span class="qa-label">إدارة الجداول</span>
                    </a>
                    <a href="<?= base_url('admin/settings') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-cog'></i></div>
                        <span class="qa-label">الإعدادات</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Simplified Stats Cards -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-group"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela">إجمالي الطلاب</div>
                <div class="stat-value-sela"><?= number_format($stats['students']) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-trending-up me-1'></i> نمو مستمر</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-user-voice"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela">المعلمون</div>
                <div class="stat-value-sela"><?= number_format($stats['teachers']) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-check-circle me-1'></i> 100% الآن</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-school"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela">الفصول الدراسية</div>
                <div class="stat-value-sela"><?= number_format($stats['classes']) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-building-house me-1'></i> موزعة عالمياً</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-gold-light text-gold"><i class="bx bxs-wallet"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela">إيرادات المنصة</div>
                <div class="stat-value-sela"><?= $stats['revenue'] ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-coin-stack me-1'></i> تحصيل نشط</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-8 animate__animated animate__fadeInLeft">
        <div class="card-sela shadow-sm border-0 bg-white">
            <div class="card-header border-0 bg-white py-4 px-5">
                <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bx-line-chart me-2 text-gold'></i> إحصائيات التسجيل والنمو</h6>
            </div>
            <div class="card-body p-5 pt-0">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 animate__animated animate__fadeInRight">
        <div class="card-sela shadow-sm border-0 bg-white">
            <div class="card-header border-0 bg-white py-4 px-5">
                <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bxs-chart me-2 text-gold'></i> توزيع المستويات</h6>
            </div>
            <div class="card-body p-5 pt-0">
                <div class="chart-pie mt-3" style="height: 280px;">
                    <canvas id="levelChart"></canvas>
                </div>
                <div class="mt-5 text-center d-flex justify-content-center gap-3">
                    <div class="legend-item"><span class="dot bg-nile"></span> ابتدائي</div>
                    <div class="legend-item"><span class="dot bg-gold"></span> إعدادي</div>
                    <div class="legend-item"><span class="dot bg-light border"></span> ثانوي</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-nile-light { background-color: rgba(25, 42, 86, 0.05); }
    .bg-gold-light { background-color: rgba(197, 160, 33, 0.05); }
    
    .quick-actions-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; }
    .quick-action-link { background: #fff; border-radius: 18px; padding: 20px 10px; text-decoration: none; text-align: center; border: 1px solid #f0f0f0; transition: all 0.2s; }
    .quick-action-link:hover { transform: translateY(-5px); border-color: var(--secondary-color); }
    .qa-label { font-weight: 700; color: var(--primary-color); font-size: 0.8rem; }
    
    .stat-card-sela { background: #fff; border-radius: 20px; padding: 25px; display: flex; align-items: center; border: 1px solid #f3f4f6; }
    .stat-icon-sela { width: 55px; height: 55px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-left: 15px; }
    .stat-label-sela { font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 2px; }
    .stat-value-sela { font-size: 1.6rem; font-weight: 800; color: var(--primary-color); line-height: 1.2; }
    .stat-meta-sela { font-size: 0.65rem; }
</style>

<style>
    /* Quick Actions Grid */
    .quick-actions-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; }
    .quick-action-link { background: #fff; border-radius: 20px; padding: 25px 15px; text-decoration: none; text-align: center; border: 1px solid #f0f0f0; transition: all 0.3s; }
    .quick-action-link:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.06) !important; border-color: var(--secondary-color); }
    .qa-icon { width: 50px; height: 50px; border-radius: 15px; margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; }
    .qa-label { font-weight: 800; font-size: 0.85rem; color: var(--primary-color); }
    .bg-glass-gold { background: linear-gradient(135deg, rgba(253, 250, 240, 0.5) 0%, #fff 100%); }

    /* Premium Stat Cards */
    .stat-card-premium { position: relative; background: #fff; border-radius: 24px; padding: 30px; overflow: hidden; display: flex; align-items: center; border: 1px solid #f8f9fa; }
    .stat-icon { width: 65px; height: 65px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-left: 20px; }
    .stat-content { flex: 1; }
    .stat-label { font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 1.8rem; font-weight: 900; color: var(--primary-color); line-height: 1; margin-bottom: 8px; }
    .stat-meta { font-size: 0.7rem; font-weight: 600; opacity: 0.8; }
    
    .stat-nile .stat-icon { background: #f1f5f9; color: var(--primary-color); }
    .stat-gold .stat-icon { background: #fffcf2; color: var(--secondary-color); }
    .stat-blue .stat-icon { background: #f0f9ff; color: #0ea5e9; }
    .stat-green .stat-icon { background: #f0fdf4; color: #22c55e; }
    
    .legend-item { display: flex; align-items: center; font-size: 0.8rem; font-weight: 700; color: #64748b; }
    .legend-item .dot { width: 10px; height: 10px; border-radius: 50%; margin-left: 6px; }
</style>
<!-- Selection Modal -->
<div class="modal fade modal-sela" id="selectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-nile" id="modalTitle">تحديد النطاق</h5>
                    <p class="text-muted small mb-0" id="modalSubTitle">يرجى اختيار الصف والفصل المطلوب</p>
                </div>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('admin/students/selector') ?>" method="POST" id="hubSelectorForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="service_id" id="modal_service_id">
                    <input type="hidden" name="class_id" id="hidden_class_id">
                    <input type="hidden" name="section_id" id="hidden_section_id">
                    
                    <!-- Class Selection Step -->
                    <div id="step_class">
                        <label class="service-label mb-3">اختر الصف الدراسي</label>
                        <div class="selection-grid">
                            <?php foreach($classes as $row): ?>
                                <div class="selection-item-card" onclick="selectClass('<?= $row['class_id'] ?>', this)">
                                    <i class='bx bxs-school'></i>
                                    <div class="item-name"><?= $row['name'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Section Selection Step (Initially Hidden) -->
                    <div id="step_section" style="display: none;" class="mt-4 animate__animated animate__fadeIn">
                        <label class="service-label mb-3">اختر الفصل</label>
                        <div class="selection-grid" id="sections_grid">
                            <!-- Dynamic Sections Grid -->
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-sm btn-link text-muted" onclick="backToClasses()">
                                <i class='bx bx-chevron-right'></i> العودة لاختيار الصف
                            </button>
                        </div>
                    </div>

                    <div class="mt-5" id="submit_container" style="display: none;">
                        <button type="submit" class="btn-service-go w-100 shadow-lg">
                            استمرار للخدمة <i class='bx bx-right-arrow-alt ms-2'></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal & Selection styles */
    .selection-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
    .selection-item-card { background: #f8fafc; border: 2px solid #f1f5f9; border-radius: 15px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; }
    .selection-item-card:hover { border-color: var(--secondary-color); background: #fff; transform: translateY(-3px); }
    .selection-item-card.active { border-color: var(--secondary-color); background: rgba(197, 160, 33, 0.05); }
    .selection-item-card i { font-size: 2rem; color: var(--primary-color); margin-bottom: 10px; display: block; }
    .selection-item-card .item-name { font-weight: 700; font-size: 0.9rem; color: var(--primary-color); }
    .service-label { font-weight: 800; color: var(--primary-color); font-size: 1.1rem; }
    .btn-service-go { background: var(--primary-color); color: #fff; border: none; padding: 18px; border-radius: 15px; font-weight: 800; font-size: 1.1rem; transition: all 0.3s; }
    .btn-service-go:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(25, 42, 86, 0.2) !important; filter: brightness(1.2); }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Unified Chart Design Tokens
    const nileBlue = '#192A56';
    const goldColor = '#c5a021';
    const softNile = 'rgba(25, 42, 86, 0.05)';
    const softGold = 'rgba(197, 160, 33, 0.1)';
    
    Chart.defaults.font.family = "'Tajawal', sans-serif";
    Chart.defaults.color = '#64748b';

    // Registration Chart
    const ctx = document.getElementById('registrationChart').getContext('2d');
    const regGradient = ctx.createLinearGradient(0, 0, 0, 400);
    regGradient.addColorStop(0, 'rgba(25, 42, 86, 0.1)');
    regGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'الطلاب الجدد',
                data: [65, 78, 60, 95, 82, 110],
                borderColor: nileBlue,
                backgroundColor: regGradient,
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: nileBlue,
                pointBorderWidth: 3,
                pointRadius: 5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: nileBlue,
                    padding: 12,
                    displayColors: false,
                    rtl: true
                }
            },
            scales: {
                y: { grid: { color: '#f1f5f9', drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Level Chart
    const ctxPie = document.getElementById('levelChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['ابتدائي', 'إعدادي', 'ثانوي'],
            datasets: [{
                data: [55, 30, 15],
                backgroundColor: [nileBlue, goldColor, '#e2e8f0'],
                borderWidth: 5,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: nileBlue,
                    padding: 12,
                    rtl: true
                }
            },
            cutout: '80%',
        }
    });

    // Selection Modal Logic
    const selectionModal = new bootstrap.Modal(document.getElementById('selectionModal'));
    let selectedServiceId = 0;

    function openSelectionModal(id, title) {
        selectedServiceId = id;
        document.getElementById('modal_service_id').value = id;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalSubTitle').innerText = 'إدارة ' + title;

        // Reset steps
        backToClasses();
        selectionModal.show();
    }

    function selectClass(classId, element) {
        document.querySelectorAll('#step_class .selection-item-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('hidden_class_id').value = classId;

        const skipSections = ['1', '3', '6', '9', '11'];
        if (skipSections.includes(selectedServiceId)) {
            document.getElementById('submit_container').style.display = 'block';
            document.getElementById('step_section').style.display = 'none';
        } else {
            $('#sections_grid').html('<div class="col-12 text-center py-4"><div class="spinner-border text-gold" role="status"></div></div>');
            $('#step_class').hide();
            $('#step_section').show();
            
            $.ajax({
                url: '<?= base_url('admin/students/get_sections_grid') ?>/' + classId,
                type: 'GET',
                success: function(response) {
                    $('#sections_grid').html(response);
                }
            });
        }
    }

    function selectSection(sectionId, element) {
        document.querySelectorAll('#step_section .selection-item-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('hidden_section_id').value = sectionId;
        document.getElementById('submit_container').style.display = 'block';
    }

    function backToClasses() {
        $('#step_class').show();
        $('#step_section').hide();
        $('#submit_container').hide();
        document.getElementById('hidden_class_id').value = '';
        document.getElementById('hidden_section_id').value = '';
        document.querySelectorAll('.selection-item-card').forEach(card => card.classList.remove('active'));
    }
</script>
<?= $this->endSection() ?>
