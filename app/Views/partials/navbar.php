<nav class="navbar navbar-expand navbar-light mb-4 static-top border-0">
    <button id="sidebarCollapse" class="btn btn-link rounded-circle me-3 d-md-none">
        <i class="bx bx-menu"></i>
    </button>

    <div class="d-none d-sm-inline-block me-auto">
    </div>

    <ul class="navbar-nav ms-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle text-nile" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <span class="me-3 d-none d-lg-inline"><?= session()->get('role') === 'super_admin' ? 'مدير النظام' : 'مسؤول المدرسة' ?></span>
                <img class="img-profile rounded-circle border border-2 border-gold" src="https://ui-avatars.com/api/?name=Admin&background=192A56&color=fff" width="40">
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in border-0 rounded-3">
                <a class="dropdown-item" href="#">
                    <i class='bx bxs-user text-gold'></i> الملف الشخصي
                </a>
                <a class="dropdown-item" href="#">
                    <i class='bx bxs-cog text-gold'></i> الإعدادات
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                    <i class='bx bxs-log-out'></i> تسجيل الخروج
                </a>
            </div>
        </li>
    </ul>
</nav>
