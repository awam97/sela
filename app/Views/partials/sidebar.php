<nav id="sidebar">
    <div class="sidebar-header pt-5 position-relative">
        <button id="desktopSidebarCollapse" class="btn btn-white rounded-circle shadow-sm position-absolute d-none d-md-flex align-items-center justify-content-center" 
                style="top: 20px; left: -18px; width: 36px; height: 36px; z-index: 1002; border: 1px solid #eee; background: #fff !important; color: #192A56;">
            <i class='bx bx-chevron-right fs-4'></i>
        </button>
        <div class="logo-wrapper p-3">
            <img src="<?= base_url('public/uploads/logo.png') ?>" alt="Sela School Logo" class="img-fluid"
                style="max-height: 80px; filter: brightness(0) invert(1);">
            <div class="mt-2 text-white-50 small opacity-75"><?= $system_settings['system_tagline'] ?? 'نظام إدارة المدارس الذكي' ?></div>
        </div>
    </div>

    <ul class="list-unstyled components">
        <?php if (session()->get('role') === 'super_admin'): ?>
            <li class="<?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>">
                <a href="<?= base_url('superadmin/dashboard') ?>"><i class='bx bxs-dashboard'></i> <span class="menu-text"><?= $system_settings['menu_dashboard'] ?? 'الرئيسية (المدير)' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'cities') ? 'active' : '' ?>">
                <a href="<?= base_url('superadmin/cities') ?>"><i class='bx bxs-map-alt'></i> <span class="menu-text"><?= $system_settings['menu_cities'] ?? 'إدارة المدن' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'schools') ? 'active' : '' ?>">
                <a href="<?= base_url('superadmin/schools') ?>"><i class='bx bxs-school'></i> <span class="menu-text"><?= $system_settings['menu_schools'] ?? 'إدارة المدارس' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'admins') ? 'active' : '' ?>">
                <a href="<?= base_url('superadmin/admins') ?>"><i class='bx bxs-user-account'></i> <span class="menu-text"><?= $system_settings['menu_managers'] ?? 'حسابات المديرين' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'settings') ? 'active' : '' ?>">
                <a href="<?= base_url('superadmin/settings') ?>"><i class='bx bxs-cog'></i> <span class="menu-text"><?= $system_settings['menu_settings'] ?? 'إعدادات النظام' ?></span></a>
            </li>
        <?php else: ?>
            <li class="<?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/dashboard') ?>"><i class='bx bxs-dashboard'></i> <span class="menu-text"><?= $system_settings['menu_admin_dashboard'] ?? 'لوحة التحكم' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'students') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/students') ?>"><i class='bx bxs-user-detail'></i> <span class="menu-text"><?= $system_settings['menu_admin_students'] ?? 'شؤون الطلاب' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'registrations') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/registrations') ?>" class="d-flex justify-content-between align-items-center">
                    <div><i class='bx bxs-user-plus'></i> <span class="menu-text"><?= $system_settings['menu_admin_registrations'] ?? 'طلبات التسجيل' ?></span></div>
                    <?php if ($pending_registrations_count > 0): ?>
                        <span class="badge rounded-pill bg-danger me-2" style="font-size: 0.65rem; padding: 4px 8px;"><?= $pending_registrations_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'teachers') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/teachers') ?>"><i class='bx bxs-briefcase'></i> <span class="menu-text"><?= $system_settings['menu_admin_teachers'] ?? 'المعلمون' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'academic') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/academic') ?>"><i class='bx bxs-book-content'></i> <span class="menu-text"><?= $system_settings['menu_admin_academic'] ?? 'الشؤون الأكاديمية' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'attendance') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/attendance') ?>"><i class='bx bxs-calendar-check'></i> <span class="menu-text"><?= $system_settings['menu_admin_attendance'] ?? 'الحضور والغياب' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'finance') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/finance') ?>"><i class='bx bxs-bank'></i> <span class="menu-text"><?= $system_settings['menu_admin_finance'] ?? 'الشؤون المالية' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'reports') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/reports') ?>"><i class='bx bxs-report'></i> <span class="menu-text"><?= $system_settings['menu_admin_reports'] ?? 'التقارير' ?></span></a>
            </li>
            <li class="<?= (isset($active_menu) && $active_menu == 'settings') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/settings') ?>"><i class='bx bxs-cog'></i> <span class="menu-text"><?= $system_settings['menu_admin_settings'] ?? 'الإعدادات' ?></span></a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-footer p-3 mt-auto">
        <small class="text-white-50 opacity-50">&copy; Sela v2.1 2026</small>
    </div>
</nav>