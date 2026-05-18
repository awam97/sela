<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela">
    <h1><i class='bx bxs-dashboard text-gold me-2'></i> <?= $system_settings['sa_dash_title'] ?? 'Control Panel' ?></h1>
    <div>
        <span class="badge bg-nile p-2 rounded-pill shadow-sm">
            <?= str_replace('{system_name}', $system_name, $system_settings['sa_dash_subtitle'] ?? 'Central Administration') ?>
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Quick Actions Panel (Cloned from Admin) -->
    <div class="col-12">
        <div class="card-sela gold shadow-sm overflow-hidden border-0">
            <div class="card-body p-4 bg-glass-gold">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
                    <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bxs-bolt-circle me-2 text-gold'></i> <?= $system_settings['sa_dash_quick_actions_title'] ?? 'Quick Actions' ?></h6>
                    <span class="text-muted small"><?= $system_settings['sa_dash_quick_actions_subtitle'] ?? 'Immediate access' ?></span>
                </div>
                <div class="quick-actions-row py-2">
                    <a href="<?= base_url('superadmin/schools/create') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-school'></i></div>
                        <span class="qa-label"><?= $system_settings['action_add_school'] ?? 'Add School' ?></span>
                    </a>
                    <a href="<?= base_url('superadmin/cities') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-map-alt'></i></div>
                        <span class="qa-label"><?= $system_settings['action_manage_cities'] ?? 'Cities' ?></span>
                    </a>
                    <a href="<?= base_url('superadmin/admins/create') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-user-plus'></i></div>
                        <span class="qa-label"><?= $system_settings['action_create_manager'] ?? 'Manager' ?></span>
                    </a>
                    <a href="<?= base_url('superadmin/student-center') ?>" class="quick-action-link">
                        <div class="qa-icon bg-gold shadow-sm"><i class='bx bxs-grid-alt'></i></div>
                        <span class="qa-label"><?= $system_settings['action_student_center'] ?? 'Students' ?></span>
                    </a>
                    <a href="<?= base_url('superadmin/settings') ?>" class="quick-action-link">
                        <div class="qa-icon bg-nile text-white shadow-sm"><i class='bx bxs-cog'></i></div>
                        <span class="qa-label"><?= $system_settings['action_system_settings'] ?? 'Settings' ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Stat Cards (Synchronized Style) -->
    <div class="col-md-3 col-6 animate__animated animate__fadeInUp">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-map-alt"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela"><?= $system_settings['sa_dash_stat_1_label'] ?? 'Cities' ?></div>
                <div class="stat-value-sela"><?= number_format($total_cities) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-globe me-1'></i> <?= $system_settings['sa_dash_stat_1_meta'] ?? 'Coverage' ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-school"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela"><?= $system_settings['sa_dash_stat_2_label'] ?? 'Schools' ?></div>
                <div class="stat-value-sela"><?= number_format($total_schools) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-check-circle me-1'></i> <?= $system_settings['sa_dash_stat_2_meta'] ?? 'Active' ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-nile-light text-nile"><i class="bx bxs-group"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela"><?= $system_settings['sa_dash_stat_3_label'] ?? 'Students' ?></div>
                <div class="stat-value-sela"><?= number_format($total_students) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-trending-up me-1'></i> <?= $system_settings['sa_dash_stat_3_meta'] ?? 'Growth' ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="stat-card-sela shadow-sm h-100">
            <div class="stat-icon-sela bg-gold-light text-gold"><i class="bx bxs-user-account"></i></div>
            <div class="stat-content-sela">
                <div class="stat-label-sela"><?= $system_settings['sa_dash_stat_4_label'] ?? 'Managers' ?></div>
                <div class="stat-value-sela"><?= number_format($total_admins) ?></div>
                <div class="stat-meta-sela text-muted"><i class='bx bx-shield-quarter me-1'></i> <?= $system_settings['sa_dash_stat_4_meta'] ?? 'Active' ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section (Synchronized with Admin) -->
<div class="row g-4 mt-2">
    <div class="col-lg-8 animate__animated animate__fadeInLeft">
        <div class="card-sela shadow-sm border-0 bg-white">
            <div class="card-header border-0 bg-white py-4 px-5">
                <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bx-line-chart me-2 text-gold'></i> <?= $system_settings['sa_dash_chart_1_title'] ?></h6>
            </div>
            <div class="card-body p-5 pt-0">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="schoolGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 animate__animated animate__fadeInRight">
        <div class="card-sela shadow-sm border-0 bg-white">
            <div class="card-header border-0 bg-white py-4 px-5">
                <h6 class="m-0 fw-bold text-nile fs-5"><i class='bx bxs-chart me-2 text-gold'></i> <?= $system_settings['sa_dash_chart_2_title'] ?></h6>
            </div>
            <div class="card-body p-5 pt-0">
                <div class="chart-pie mt-3" style="height: 280px;">
                    <canvas id="cityDistributionChart"></canvas>
                </div>
                <div class="mt-5 text-center d-flex justify-content-center gap-3">
                    <div class="legend-item"><span class="dot bg-nile"></span> <?= $system_settings['sa_dash_chart_legend_1'] ?></div>
                    <div class="legend-item"><span class="dot bg-gold"></span> <?= $system_settings['sa_dash_chart_legend_2'] ?></div>
                    <div class="legend-item"><span class="dot bg-light border"></span> <?= $system_settings['sa_dash_chart_legend_3'] ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <!-- Recents Table -->
    <div class="col-lg-8">
        <div class="card-sela primary shadow-sm border-0">
            <div class="card-header bg-white py-4 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-nile mb-0"><i class='bx bx-time-five text-gold me-2'></i> <?= $system_settings['sa_dash_recent_schools_title'] ?? 'Recent' ?></h5>
                <a href="<?= base_url('superadmin/schools') ?>" class="text-gold text-decoration-none small fw-bold"><?= $system_settings['action_view_all'] ?? 'View All' ?> <i class='bx bx-chevron-left'></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4"><?= $system_settings['sa_dash_recent_schools_th_1'] ?? 'Name' ?></th>
                                <th><?= $system_settings['sa_dash_recent_schools_th_2'] ?? 'Year' ?></th>
                                <th class="text-center"><?= $system_settings['sa_dash_recent_schools_th_3'] ?? 'Status' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_schools as $school): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-nile"><?= $school['name'] ?></div>
                                        <small class="text-muted"><?= $school['email'] ?></small>
                                    </td>
                                    <td><?= $school['year'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success p-2 px-3 rounded-pill small"><?= $system_settings['sa_dash_active_badge'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Settings (Small Panel) -->
    <div class="col-lg-4">
        <div class="card-sela gold shadow-sm border-0 h-100">
            <div class="card-header bg-white py-4 px-4 border-bottom text-center">
                <h5 class="fw-bold text-nile mb-0"><i class='bx bx-cog text-gold me-2'></i> <?= $system_settings['menu_settings'] ?></h5>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush rounded-3 overflow-hidden border">
                    <a href="<?= base_url('superadmin/settings') ?>" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center border-0">
                        <div>
                            <div class="fw-bold text-nile"><?= $system_settings['settings_tab_identity'] ?></div>
                            <small class="text-muted"><?= $system_settings['settings_tab_identity_desc'] ?></small>
                        </div>
                        <i class='bx bx-chevron-left text-gold fs-4'></i>
                    </a>
                    <a href="<?= base_url('superadmin/settings') ?>" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center border-0 border-top">
                        <div>
                            <div class="fw-bold text-nile"><?= $system_settings['settings_tab_academic'] ?></div>
                            <small class="text-muted"><?= $system_settings['settings_tab_academic_desc'] ?></small>
                        </div>
                        <i class='bx bx-chevron-left text-gold fs-4'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Cloned from Admin to Ensure Sync */
    .bg-nile-light { background-color: rgba(25, 42, 86, 0.05); }
    .bg-gold-light { background-color: rgba(197, 160, 33, 0.05); }
    .bg-glass-gold { background: linear-gradient(135deg, rgba(253, 250, 240, 0.5) 0%, #fff 100%); }
    
    .quick-actions-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; }
    .quick-action-link { background: #fff; border-radius: var(--border-radius-lg); padding: 25px 15px; text-decoration: none; text-align: center; border: 1px solid #f0f0f0; transition: all 0.3s; }
    .quick-action-link:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.06) !important; border-color: var(--secondary-color); }
    .qa-icon { width: 50px; height: 50px; border-radius: var(--border-radius-md); margin: 0 auto 15px auto; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; }
    .qa-label { font-weight: 800; font-size: 0.85rem; color: var(--primary-color); }
    
    .stat-card-sela { background: #fff; border-radius: var(--border-radius-lg); padding: 25px; display: flex; align-items: center; border: 1px solid #f3f4f6; }
    .stat-icon-sela { width: 55px; height: 55px; border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-left: 15px; }
    .stat-label-sela { font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 2px; }
    .stat-value-sela { font-size: 1.6rem; font-weight: 800; color: var(--primary-color); line-height: 1.2; }
    .stat-meta-sela { font-size: 0.65rem; }

    .legend-item { display: flex; align-items: center; font-size: 0.8rem; font-weight: 700; color: #64748b; }
    .legend-item .dot { width: 10px; height: 10px; border-radius: 50%; margin-left: 6px; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const nileBlue = '#192A56';
    const goldColor = '#c5a021';
    
    Chart.defaults.font.family = "'Tajawal', sans-serif";
    Chart.defaults.color = '#64748b';

    // School Growth Chart
    const ctx = document.getElementById('schoolGrowthChart').getContext('2d');
    const regGradient = ctx.createLinearGradient(0, 0, 0, 400);
    regGradient.addColorStop(0, 'rgba(25, 42, 86, 0.1)');
    regGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['months']) ?>,
            datasets: [{
                label: '<?= $system_settings['sa_dash_chart_1_legend'] ?? 'Registrations' ?>',
                data: <?= json_encode($chart_data['registrations']) ?>,
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
                tooltip: { backgroundColor: nileBlue, padding: 12, displayColors: false, rtl: true }
            },
            scales: {
                y: { grid: { color: '#f1f5f9', drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // City Distribution Chart
    const ctxPie = document.getElementById('cityDistributionChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['<?= $system_settings['sa_dash_chart_legend_1'] ?>', '<?= $system_settings['sa_dash_chart_legend_2'] ?>', '<?= $system_settings['sa_dash_chart_legend_3'] ?>'],
            datasets: [{
                data: <?= json_encode($chart_data['distribution']) ?>,
                backgroundColor: [nileBlue, goldColor, '#e2e8f0'],
                borderWidth: 5,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: nileBlue, padding: 12, rtl: true } },
            cutout: '80%',
        }
    });
</script>
<?= $this->endSection() ?>
