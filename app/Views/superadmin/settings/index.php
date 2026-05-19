<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-nile mb-1">إعدادات المنصة الموحدة</h4>
            <p class="text-muted small mb-0">إدارة كافة نصوص، قوائم، وهوية النظام من مكان واحد</p>
        </div>
        <div class="d-flex gap-3">
            <a href="<?= base_url('superadmin/settings/repair') ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                <i class='bx bx-sync me-1'></i> إصلاح المزامنة
            </a>
        </div>
    </div>

    <form action="<?= base_url('superadmin/settings/update') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- Search & Global Actions Sticky Header -->
        <div class="sticky-top bg-light pt-2 pb-3" style="z-index: 1040; top: 70px;">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="position-relative">
                                <i class='bx bx-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted fs-5'></i>
                                <input type="text" id="settingsSearch" class="form-control form-control-lg pe-5 border-0 bg-light rounded-pill" placeholder="ابحث عن أي نص أو إعداد (مثلاً: لوحة التحكم، اللون، واتساب...)">
                            </div>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <button type="submit" class="btn-sela btn-sela-primary rounded-pill px-5 shadow-sm py-2 fw-bold">
                                <i class='bx bxs-save me-1'></i> حفظ كافة التغييرات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="settingsContainer" class="mt-4 pb-5">
            <div id="noResults" class="text-center py-5 section-hidden">
                <i class='bx bx-search-alt fs-1 text-muted opacity-25'></i>
                <h5 class="text-muted fw-bold mt-3">لم يتم العثور على نتائج</h5>
            </div>

            <!-- SECTION 1: System Identity -->
            <div class="settings-section mb-5" data-section="identity">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-gold">الهوية والمعلومات العامة</h5>
                <div class="card-sela primary shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">اسم النظام / المنصة</label>
                                <input type="text" name="system_name" value="<?= $system_name ?? '' ?>" class="form-control border-light bg-light" required>
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">شعار النص (Tagline)</label>
                                <input type="text" name="system_tagline" value="<?= $system_tagline ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-12 search-field">
                                <div class="form-check form-switch p-3 bg-light rounded-3">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="true" id="maintenanceToggle" <?= ($maintenance_mode ?? 'false') == 'true' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold ms-2" for="maintenanceToggle">تفعيل وضع الصيانة (Maintenance Mode)</label>
                                    <small class="text-muted d-block mt-1 ps-4">عند التفعيل، سيتم إغلاق الموقع أمام الزوار وعرض صفحة "تحت الصيانة".</small>
                                </div>
                            </div>
                            <div class="col-md-12 search-field">
                                <label class="form-label text-muted small fw-bold">رمز العبور السري لتجاوز الصيانة (Bypass Security Key)</label>
                                <input type="text" name="maintenance_bypass_key" id="maintenanceBypassKey" value="<?= $maintenance_bypass_key ?? 'SelaAdminPasscode2026' ?>" class="form-control border-light bg-light" required>
                                <small class="text-muted d-block mt-1">عند تفعيل الصيانة، يجب على المدير العام كتابة هذا الرمز لتسجيل الدخول، مثال: <code><?= base_url('auth/login?key=') ?><span id="bypassKeyExample" class="fw-bold text-primary"><?= $maintenance_bypass_key ?? 'SelaAdminPasscode2026' ?></span></code></small>
                            </div>
                            <div class="col-md-12 search-field">
                                <label class="form-label text-muted small fw-bold">وصف المنصة (Footer Description)</label>
                                <textarea name="system_desc" class="form-control border-light bg-light no-editor" rows="2"><?= $system_desc ?? '' ?></textarea>
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">اللون الأساسي للنظام</label>
                                <input type="color" name="primary_color" class="form-control form-control-color w-100 border-0" value="<?= $primary_color ?? '#192a56' ?>">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">اللون الثانوي للنظام</label>
                                <input type="color" name="secondary_color" class="form-control form-control-color w-100 border-0" value="<?= $secondary_color ?? '#c5a021' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Contact & Footer -->
            <div class="settings-section mb-5" data-section="contact">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-primary">معلومات التواصل والتذييل</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">بريد التواصل</label>
                                <input type="email" name="contact_email" value="<?= $contact_email ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">العنوان الفيزيائي</label>
                                <input type="text" name="contact_address" value="<?= $contact_address ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">رقم الهاتف</label>
                                <input type="text" name="contact_phone" value="<?= $contact_phone ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">رقم الواتساب</label>
                                <input type="text" name="contact_whatsapp" value="<?= $contact_whatsapp ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">عنوان قسم السوشيال ميديا</label>
                                <input type="text" name="footer_social_title" value="<?= $footer_social_title ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">نص الحقوق (Copyright)</label>
                                <input type="text" name="footer_copyright" value="<?= $footer_copyright ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Academic & SMTP -->
            <div class="settings-section mb-5" data-section="academic">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-gold">الإعدادات الأكاديمية والبريد (SMTP)</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">العام الدراسي الحالي</label>
                                <input type="text" name="current_year" value="<?= $current_year ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">تاريخ بدء العام</label>
                                <input type="date" name="year_start" value="<?= $year_start ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom"><h6 class="fw-bold mb-0 text-nile">إعدادات البريد الإلكتروني</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">خادم SMTP (Host)</label>
                                <input type="text" name="smtp_host" value="<?= $smtp_host ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">المنفذ (Port)</label>
                                <input type="text" name="smtp_port" value="<?= $smtp_port ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">اسم المستخدم (User/Email)</label>
                                <input type="text" name="smtp_user" value="<?= $smtp_user ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">كلمة المرور (Password)</label>
                                <input type="password" name="smtp_pass" value="<?= $smtp_pass ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">نوع التشفير (Encryption)</label>
                                <select name="smtp_crypto" class="form-select border-light bg-light">
                                    <option value="none" <?= ($smtp_crypto ?? '') == 'none' ? 'selected' : '' ?>>بدون (None)</option>
                                    <option value="ssl" <?= ($smtp_crypto ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="tls" <?= ($smtp_crypto ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                </select>
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">البريد المرسل (From Address)</label>
                                <input type="email" name="mail_from" value="<?= $mail_from ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Security & OTP -->
            <div class="settings-section mb-5" data-section="security">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-danger">الأمان والتحقق (OTP)</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12 search-field">
                                <div class="form-check form-switch p-3 bg-light rounded-3">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_otp_enabled" value="true" id="waOtpToggle" <?= ($whatsapp_otp_enabled ?? 'false') == 'true' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold ms-2" for="waOtpToggle">تفعيل التحقق الثنائي (OTP) لتسجيل الدخول (الويب والتطبيقات)</label>
                                </div>
                            </div>
                            <div class="col-md-12 search-field">
                                <label class="form-label text-muted small fw-bold">TextMeBot API Key</label>
                                <input type="text" name="textmebot_api_key" value="<?= $textmebot_api_key ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Landing Page Phrases -->
            <div class="settings-section mb-5" data-section="frontend">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-gold">نصوص الصفحة الرئيسية (Landing Page)</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-nile mb-3">قسم الواجهة (Hero Section)</h6>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">العنوان الرئيسي</label>
                                <input type="text" name="landing_hero_h1" value="<?= $landing_hero_h1 ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">الوصف التعريفي</label>
                                <textarea name="landing_hero_p" class="form-control border-light bg-light no-editor" rows="1"><?= $landing_hero_p ?? '' ?></textarea>
                            </div>
                        </div>

                        <h6 class="fw-bold text-nile mb-3 border-top pt-4">قسم الإحصائيات (Stats Section)</h6>
                        <div class="row g-4 mb-4">
                            <?php for($i=1; $i<=3; $i++): ?>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">الإحصائية <?= $i ?> (قيمة / عنوان)</label>
                                <div class="d-flex gap-2">
                                    <input type="text" name="landing_stats_<?= $i ?>_value" value="<?= ${"landing_stats_{$i}_value"} ?? '' ?>" class="form-control border-light bg-light">
                                    <input type="text" name="landing_stats_<?= $i ?>_label" value="<?= ${"landing_stats_{$i}_label"} ?? '' ?>" class="form-control border-light bg-light">
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>

                        <h6 class="fw-bold text-nile mb-3 border-top pt-4">قسم المميزات (Features)</h6>
                        <div class="row g-4 mb-4">
                            <?php for($i=1; $i<=4; $i++): ?>
                            <div class="col-md-6 search-field">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="form-label text-muted small fw-bold">الميزة <?= $i ?> (عنوان / وصف)</label>
                                    <input type="text" name="landing_feature_<?= $i ?>_title" value="<?= ${"landing_feature_{$i}_title"} ?? '' ?>" class="form-control border-white mb-2 shadow-sm">
                                    <textarea name="landing_feature_<?= $i ?>_desc" class="form-control border-white no-editor shadow-sm" rows="2"><?= ${"landing_feature_{$i}_desc"} ?? '' ?></textarea>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: Dashboard Content -->
            <div class="settings-section mb-5" data-section="dashboard">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-gold">لوحة تحكم مدير النظام والإحصائيات</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">عنوان لوحة التحكم</label>
                                <input type="text" name="sa_dash_title" value="<?= $sa_dash_title ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">العنوان الفرعي</label>
                                <input type="text" name="sa_dash_subtitle" value="<?= $sa_dash_subtitle ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <?php for($i=1; $i<=4; $i++): ?>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">تسمية إحصائية <?= $i ?></label>
                                <input type="text" name="sa_dash_stat_<?= $i ?>_label" value="<?= ${"sa_dash_stat_{$i}_label"} ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 7: Sidebar & Menus -->
            <div class="settings-section mb-5" data-section="sidebar">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-nile">القوائم الجانبية (Sidebar)</h5>
                <div class="table-responsive rounded-4 shadow-sm border bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted smallest fw-bold" style="width: 50%;">القسم / الرابط</th>
                                <th class="py-3 text-muted smallest fw-bold">النص المعروض (2 في كل صف افتراضياً)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Combine into grid-like rows -->
                            <tr class="table-section-header bg-light/50"><td colspan="2" class="ps-4 py-2 fw-bold text-nile small">قوائم النظام</td></tr>
                            <tr class="search-row">
                                <td colspan="2" class="p-0">
                                    <div class="row g-0">
                                        <?php 
                                        $all_menus = [
                                            'menu_dashboard' => 'Super: لوحة التحكم',
                                            'menu_cities' => 'Super: المدن',
                                            'menu_schools' => 'Super: المدارس',
                                            'menu_managers' => 'Super: المديرين',
                                            'menu_settings' => 'Super: الإعدادات',
                                            'menu_admin_dashboard' => 'Admin: لوحة التحكم',
                                            'menu_admin_students' => 'Admin: الطلاب',
                                            'menu_admin_teachers' => 'Admin: المعلمون',
                                            'menu_admin_academic' => 'Admin: الأكاديمية',
                                            'menu_admin_attendance' => 'Admin: الحضور',
                                            'menu_admin_registrations' => 'Admin: التسجيل',
                                            'menu_admin_finance' => 'Admin: المالية',
                                            'menu_admin_reports' => 'Admin: التقارير',
                                            'menu_admin_settings' => 'Admin: الإعدادات'
                                        ];
                                        foreach($all_menus as $key => $label): ?>
                                        <div class="col-md-6 border-bottom border-end p-3 search-field">
                                            <label class="small fw-bold d-block mb-1"><?= $label ?></label>
                                            <input type="text" name="<?= $key ?>" value="<?= $all_settings[$key] ?? '' ?>" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-section-header bg-light/50"><td colspan="2" class="ps-4 py-2 fw-bold text-nile small">تسميات شاشات تطبيق الهاتف (Mobile App Views)</td></tr>
                            <tr class="search-row">
                                <td colspan="2" class="p-0">
                                    <div class="row g-0">
                                        <?php 
                                        $mobile_views = [
                                            'menu_admin_students' => 'شاشة دليل وشؤون الطلاب',
                                            'menu_admin_attendance' => 'شاشة تسجيل الحضور والغياب',
                                            'menu_admin_registrations' => 'شاشة طلبات الالتحاق الجديدة',
                                            'menu_admin_finance' => 'شاشة الحسابات والمالية والفواتير',
                                            'menu_admin_settings' => 'شاشة الملف الشخصي للمسؤول',
                                            'menu_mobile_subjects' => 'شاشة دليل المناهج والمواد',
                                            'menu_mobile_marks' => 'شاشة رصد الدرجات',
                                            'menu_mobile_student_affairs' => 'شاشة شؤون الطلبة والقبول',
                                            'menu_mobile_qr_scanner' => 'شاشة مسح الـ QR للكاميرا',
                                            'menu_mobile_student_photos' => 'شاشة إدارة صور الطلاب'
                                        ];
                                        foreach($mobile_views as $key => $label): ?>
                                        <div class="col-md-6 border-bottom border-end p-3 search-field">
                                            <label class="small fw-bold d-block mb-1"><?= $label ?></label>
                                            <input type="text" name="<?= $key ?>" value="<?= $all_settings[$key] ?? '' ?>" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-section-header bg-light/50"><td colspan="2" class="ps-4 py-2 fw-bold text-nile small">توصيفات خيارات تطبيق الهاتف (Mobile App Descriptions)</td></tr>
                            <tr class="search-row">
                                <td colspan="2" class="p-0">
                                    <div class="row g-0">
                                        <?php 
                                        $mobile_descs = [
                                            'menu_mobile_students_desc' => 'توصيف: دليل وشؤون الطلاب',
                                            'menu_mobile_qr_scanner_desc' => 'توصيف: مسح رمز الطالب (QR)',
                                            'menu_mobile_attendance_desc' => 'توصيف: تسجيل الحضور والغياب',
                                            'menu_mobile_marks_desc' => 'توصيف: رصد درجات المواد',
                                            'menu_mobile_student_photos_desc' => 'توصيف: إدارة صور الطلاب',
                                            'menu_mobile_registrations_desc' => 'توصيف: طلبات الالتحاق الجديدة'
                                        ];
                                        foreach($mobile_descs as $key => $label): ?>
                                        <div class="col-md-6 border-bottom border-end p-3 search-field">
                                            <label class="small fw-bold d-block mb-1"><?= $label ?></label>
                                            <input type="text" name="<?= $key ?>" value="<?= $all_settings[$key] ?? '' ?>" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-section-header bg-light/50"><td colspan="2" class="ps-4 py-2 fw-bold text-nile small">نصوص تسجيل الدخول والترحيب بالتطبيق (Mobile Welcome & Login Screen)</td></tr>
                            <tr class="search-row">
                                <td colspan="2" class="p-0">
                                    <div class="row g-0">
                                        <?php 
                                        $mobile_login_dash = [
                                            'login_welcome_title' => 'شاشة الدخول: العنوان الرئيسي',
                                            'login_welcome_subtitle' => 'شاشة الدخول: العنوان الفرعي الترحيبي',
                                            'login_btn_text' => 'شاشة الدخول: نص زر تسجيل الدخول',
                                            'login_username_hint' => 'شاشة الدخول: تلميح حقل اسم المستخدم',
                                            'login_password_hint' => 'شاشة الدخول: تلميح حقل كلمة المرور',
                                            'dash_welcome_admin' => 'الرئيسية: ترحيب المدير العام (Super Admin)',
                                            'dash_welcome_teacher' => 'الرئيسية: ترحيب المعلمين والموظفين',
                                            'dash_section_title' => 'الرئيسية: عنوان قسم لوحة التحكم والإدارة'
                                        ];
                                        foreach($mobile_login_dash as $key => $label): ?>
                                        <div class="col-md-6 border-bottom border-end p-3 search-field">
                                            <label class="small fw-bold d-block mb-1"><?= $label ?></label>
                                            <input type="text" name="<?= $key ?>" value="<?= $all_settings[$key] ?? '' ?>" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-section-header bg-light/50"><td colspan="2" class="ps-4 py-2 fw-bold text-nile small">تسميات أزرار ونصوص وتنبيهات تطبيق الهاتف (Mobile App Action Phrases & Labels)</td></tr>
                            <tr class="search-row">
                                <td colspan="2" class="p-0">
                                    <div class="row g-0">
                                        <?php 
                                        $mobile_labels = [
                                            'lbl_save_attendance' => 'الحضور: زر حفظ الحضور والغياب',
                                            'lbl_select_class' => 'الحضور: تلميح اختيار الفصل الدراسي',
                                            'lbl_present' => 'الحضور: تسمية حالة (حاضر)',
                                            'lbl_absent' => 'الحضور: تسمية حالة (غائب)',
                                            'lbl_late' => 'الحضور: تسمية حالة (متأخر)',
                                            'lbl_excused' => 'الحضور: تسمية حالة (إجازة/عذر)',
                                            'lbl_total_paid' => 'المالية: تسمية (إجمالي المدفوعات)',
                                            'lbl_remaining_balance' => 'المالية: تسمية (المستحقات المتبقية)',
                                            'lbl_invoices' => 'المالية: عنوان تبويب (الفواتير)',
                                            'lbl_logout' => 'الملف الشخصي: زر تسجيل الخروج',
                                            'lbl_change_password' => 'الملف الشخصي: زر تغيير كلمة المرور',
                                            'lbl_qr_instruction' => 'الكاميرا: إرشادات مسح الـ QR',
                                            'lbl_search_student' => 'الطلاب: تلميح حقل البحث عن الطلاب',
                                            'lbl_save_marks' => 'الدرجات: زر حفظ ورصد الدرجات',
                                            'lbl_select_exam_category' => 'الدرجات: تلميح اختيار فئة التقييم',
                                            'lbl_success_save' => 'تنبيه: رسالة النجاح عند الحفظ'
                                        ];
                                        foreach($mobile_labels as $key => $label): ?>
                                        <div class="col-md-6 border-bottom border-end p-3 search-field">
                                            <label class="small fw-bold d-block mb-1"><?= $label ?></label>
                                            <input type="text" name="<?= $key ?>" value="<?= $all_settings[$key] ?? '' ?>" class="form-control form-control-sm border-0 bg-light">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION 8: Quick Actions -->
            <div class="settings-section mb-5" data-section="actions">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-gold">تسميات الأزرار والإجراءات</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <?php 
                            $actions = [
                                'action_add_school' => 'إضافة مدرسة',
                                'action_manage_cities' => 'إدارة المدن',
                                'action_create_manager' => 'إنشاء مدير',
                                'action_student_center' => 'مركز الطلاب',
                                'action_system_settings' => 'إعدادات النظام',
                                'action_view_all' => 'عرض الكل'
                            ];
                            foreach($actions as $key => $label): ?>
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold"><?= $label ?></label>
                                <input type="text" name="<?= $key ?>" value="<?= $$key ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 9: General Phrases -->
            <div class="settings-section mb-5" data-section="ui">
                <h5 class="section-title fw-bold text-nile mb-4 ps-3 border-start border-4 border-primary">نصوص الواجهة العامة</h5>
                <div class="card-sela shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 search-field">
                                <label class="form-label text-muted small fw-bold">تسمية دور مدير النظام</label>
                                <input type="text" name="user_role_sa" value="<?= $user_role_sa ?? '' ?>" class="form-control border-light bg-light">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Save Button (Mobile Only or Floating) -->
        <div class="d-md-none position-fixed bottom-0 start-0 w-100 p-3 bg-white border-top shadow-lg" style="z-index: 1050;">
            <button type="submit" class="btn-sela btn-sela-primary w-100 rounded-pill py-3 fw-bold">
                <i class='bx bxs-save me-1'></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>

<style>
    .settings-section { scroll-margin-top: 150px; }
    .section-title { font-size: 1.1rem; }
    .field-hidden, .section-hidden { display: none !important; }
    .smallest { font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }
    
    .form-control:focus { 
        background: #fff !important; 
        box-shadow: 0 0 0 4px rgba(25, 42, 86, 0.05); 
        border-color: var(--gold) !important; 
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('settingsSearch');
        const sections = document.querySelectorAll('.settings-section');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let totalMatches = 0;

            if (query === '') {
                document.querySelectorAll('.search-field, .search-row, .settings-section, .table-section-header').forEach(el => {
                    el.classList.remove('field-hidden', 'section-hidden');
                });
                noResults.classList.add('section-hidden');
                return;
            }

            sections.forEach(section => {
                let sectionHasMatch = false;

                const cardFields = section.querySelectorAll('.search-field');
                cardFields.forEach(field => {
                    const label = field.querySelector('label')?.innerText || '';
                    const input = field.querySelector('input, textarea')?.value || '';
                    if (label.toLowerCase().includes(query) || input.toLowerCase().includes(query)) {
                        field.classList.remove('field-hidden');
                        sectionHasMatch = true;
                        totalMatches++;
                    } else {
                        field.classList.add('field-hidden');
                    }
                });

                const tableRows = section.querySelectorAll('.search-row');
                tableRows.forEach(row => {
                    const label = row.querySelector('label')?.innerText || '';
                    const input = row.querySelector('input')?.value || '';
                    if (label.toLowerCase().includes(query) || input.toLowerCase().includes(query)) {
                        row.classList.remove('field-hidden');
                        sectionHasMatch = true;
                        totalMatches++;
                    } else {
                        row.classList.add('field-hidden');
                    }
                });

                section.classList.toggle('section-hidden', !sectionHasMatch);
                
                const tableHeaders = section.querySelectorAll('.table-section-header');
                tableHeaders.forEach(header => {
                    let next = header.nextElementSibling;
                    let hasVisibleRow = false;
                    while(next && next.classList.contains('search-row')) {
                        if (!next.classList.contains('field-hidden')) hasVisibleRow = true;
                        next = next.nextElementSibling;
                    }
                    header.classList.toggle('field-hidden', !hasVisibleRow);
                });
            });

            noResults.classList.toggle('section-hidden', totalMatches > 0);
        });

        // Live bypass key preview
        const bypassInput = document.getElementById('maintenanceBypassKey');
        const bypassExample = document.getElementById('bypassKeyExample');
        if (bypassInput && bypassExample) {
            bypassInput.addEventListener('input', function() {
                bypassExample.textContent = this.value || 'SelaAdminPasscode2026';
            });
        }
    });
</script>

<?= $this->endSection() ?>
