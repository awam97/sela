<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        
        $data = [
            'active_menu' => 'settings'
        ];
        
        foreach ($settings as $row) {
            $data[$row['type']] = $row['description'];
        }
        
        // Provide defaults if not found to avoid undefined variable errors in view
        $defaults = [
            'system_name' => 'Sela Education System',
            'contact_email' => 'support@sela-plus.ly',
            'system_desc' => 'Sela Plus system for managing schools and educational institutions in an integrated and smart way.',
            'current_year' => '2025-2026',
            'maintenance_mode' => 'false',
            'primary_color' => '#192A56',
            'secondary_color' => '#C5A021',
            'year_start' => '2025-09-01',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_crypto' => 'tls',
            'mail_from' => 'no-reply@sela.ly',
            'whatsapp_otp_enabled' => 'false',
            'textmebot_api_key' => '',
            'maintenance_bypass_key' => 'SelaAdminPasscode2026',
            // Landing Page Phrases
            'landing_hero_h1' => 'Educational Excellence <br>in One Platform',
            'landing_hero_p' => 'An integrated smart system for school management, combining administrative efficiency and educational excellence in a modern and easy-to-use environment.',
            'landing_stats_1_label' => 'Educational Institution',
            'landing_stats_1_value' => '50+',
            'landing_stats_2_label' => 'Students & Users',
            'landing_stats_2_value' => '12k',
            'landing_stats_3_label' => 'Accuracy & Security',
            'landing_stats_3_value' => '100%',
            'landing_feature_1_title' => 'Smart Management',
            'landing_feature_1_desc' => 'Organize administrative and financial operations with high precision through interactive interfaces that support correct decision-making.',
            'landing_feature_2_title' => 'Performance Reports',
            'landing_feature_2_desc' => 'Get a comprehensive view of students\' academic level and track their growth through detailed charts and reports.',
            'landing_feature_3_title' => 'Complete Privacy',
            'landing_feature_3_desc' => 'We put your data security at the forefront of our priorities, with advanced encryption systems and daily backups.',
            'landing_feature_4_title' => 'Lightning Speed',
            'landing_feature_4_desc' => 'An advanced cloud platform that ensures you can access your data anytime, anywhere with lightning speed.',
            'landing_roles_title' => 'Smart Solutions for Every Role',
            'landing_roles_subtitle' => 'The platform is designed to enable every individual in the educational institution to perform their tasks efficiently and professionally.',
            'landing_role_1_title' => 'School Managers',
            'landing_role_1_list' => "Comprehensive and complete view of all school departments.\nManage personnel, salaries, and finances.\nFollow up on registration and admission requests for new students.\nExtract statistical reports with the click of a button.",
            'landing_role_2_title' => 'Teachers & Educators',
            'landing_role_2_list' => "Monitor daily attendance and absence of students easily.\nRecord academic grades and results accurately.\nFollow up on student performance and determine their levels.\nEffective and fast communication with the school administration.",
            // About Page Phrases
            'about_hero_h1' => 'Our Vision for Education',
            'about_hero_p' => 'We are here to create a better educational future through technology and innovation.',
            'about_who_title' => 'Who Are We?',
            'about_who_p1' => '{system_name} platform is the result of years of experience in the field of education and software. The platform was launched with the aim of overcoming the administrative obstacles facing schools and transforming them into seamless digital processes that allow educators to focus on their highest mission.',
            'about_who_p2' => 'We believe that a school is not just a building, but an integrated system that needs smart tools to connect the student, teacher, and guardian in one interactive environment that ensures success for everyone.',
            // Super Admin Dashboard Phrases
            'sa_dash_title' => 'Control Panel (General Administration)',
            'sa_dash_subtitle' => '{system_name} - Central Administration',
            'sa_dash_quick_actions_title' => 'Quick Actions',
            'sa_dash_quick_actions_subtitle' => 'Immediate access to overall management tools',
            'sa_dash_stat_1_label' => 'Total Cities',
            'sa_dash_stat_2_label' => 'Registered Schools',
            'sa_dash_stat_3_label' => 'Total Students',
            'sa_dash_stat_4_label' => 'Executive Managers',
            'sa_dash_chart_1_title' => 'School Registration Growth',
            'sa_dash_chart_1_legend' => 'New Schools',
            'sa_dash_chart_2_title' => 'Schools Distribution by Region',
            'sa_dash_recent_schools_title' => 'Recently Added Schools',
            'sa_dash_stat_1_meta' => 'Geographical Coverage',
            'sa_dash_stat_2_meta' => 'Across Branches',
            'sa_dash_stat_3_meta' => 'Continuous Growth',
            'sa_dash_stat_4_meta' => 'Active Accounts',
            'sa_dash_recent_schools_th_1' => 'School',
            'sa_dash_recent_schools_th_2' => 'Registration Date',
            'sa_dash_recent_schools_th_3' => 'Status',
            'sa_dash_active_badge' => 'Active',
            'sa_dash_chart_legend_1' => 'Tripoli',
            'sa_dash_chart_legend_2' => 'Benghazi',
            'sa_dash_chart_legend_3' => 'Others',
            // Schools List Phrases
            'sa_schools_title' => 'Schools and Facilities Management',
            'sa_schools_subtitle' => 'Manage all educational institutions participating in the platform',
            'sa_schools_add_btn' => 'Add New School',
            // Auth Phrases
            'auth_login_title' => 'Login',
            'auth_login_subtitle' => 'Welcome to {system_name}, please enter your details to continue.',
            'auth_otp_title' => 'Identity Verification',
            'auth_otp_subtitle' => 'A verification code has been sent to your communication method.',
            // Notification Templates
            'notify_otp_wa_template' => "Your verification code in {system_name} is: {otp}\nThis code is valid for 5 minutes only.",
            'notify_otp_mail_subject' => 'Login Verification Code - {system_name}',
            'notify_otp_mail_body' => 'Dear user, your verification code is: {otp}. Please do not share this code with anyone.',
            // Sidebar & General UI
            'system_tagline' => 'Smart School Management System',
            'menu_dashboard' => 'Dashboard (Admin)',
            'menu_cities' => 'Cities Management',
            'menu_schools' => 'Schools Management',
            'menu_managers' => 'Managers Accounts',
            'menu_settings' => 'System Settings',
            'user_role_sa' => 'System Admin',
            
            // Admin Sidebar (School Managers)
            'menu_admin_dashboard' => 'Dashboard',
            'menu_admin_students' => 'Student Affairs',
            'menu_admin_registrations' => 'Registration Requests',
            'menu_admin_teachers' => 'Teachers',
            'menu_admin_academic' => 'Academic Affairs',
            'menu_admin_attendance' => 'Attendance',
            'menu_admin_finance' => 'Financial Affairs',
            'menu_admin_reports' => 'Reports',
            'menu_admin_settings' => 'Settings',
            
            // Quick Actions
            'action_add_school' => 'Add School',
            'action_manage_cities' => 'Cities Management',
            'action_create_manager' => 'Create Manager',
            'action_student_center' => 'Student Center Org',
            'action_system_settings' => 'System Settings',
            'action_view_all' => 'View All',
            
            // Settings UI Labels (stored as phrases)
            'settings_header_title' => 'General System Settings',
            'settings_header_subtitle' => 'Customize visual identity and platform defaults',
            'settings_tab_identity' => 'System Identity',
            'settings_tab_identity_desc' => 'Control logo and name',
            'settings_tab_academic' => 'General Academic Year',
            'settings_tab_academic_desc' => 'Change current system year',
            
            // Footer & Contact
            'footer_copyright' => 'All Rights Reserved © {year} {system_name}',
            'footer_social_title' => 'Follow us on social media',
            'contact_address' => 'Tripoli, Libya - Ennasr Street',
            'contact_phone' => '+218 91 000 0000',
            'contact_whatsapp' => '+218 92 000 0000',
            
            // Mobile App Dynamic Phrases & Titles
            'menu_mobile_subjects' => 'دليل المناهج والمواد',
            'menu_mobile_marks' => 'رصد درجات المواد',
            'menu_mobile_student_affairs' => 'شؤون الطلبة والقبول',
            'menu_mobile_qr_scanner' => 'مسح معرف الطالب (QR)',
            'menu_mobile_student_photos' => 'إدارة صور الطلاب',
            'menu_mobile_students_desc' => 'البحث عن الطلاب والتحكم ببياناتهم وسجلاتهم.',
            'menu_mobile_qr_scanner_desc' => 'امسح الرمز المربوط بالهوية للتحقق الفوري.',
            'menu_mobile_attendance_desc' => 'رصد الحضور والغياب اليومي للطلاب بالفصول.',
            'menu_mobile_marks_desc' => 'إدخال وتحديث درجات الطلاب في الاختبارات والأنشطة.',
            'menu_mobile_student_photos_desc' => 'التقاط وتحديث الصور الشخصية للطلاب عبر الكاميرا.',
            'menu_mobile_registrations_desc' => 'مراجعة واعتماد ملفات تسجيل الطلاب المنضمين حديثاً.',
            'login_welcome_title' => 'مرحباً بك في منصة صلة',
            'login_welcome_subtitle' => 'الرجاء تسجيل الدخول لمتابعة حسابك التعليمي',
            'login_btn_text' => 'تسجيل الدخول',
            'login_username_hint' => 'اسم المستخدم أو رقم الهاتف',
            'login_password_hint' => 'كلمة المرور',
            'dash_welcome_admin' => 'أهلاً بك مجدداً في إدارة المدرسة',
            'dash_welcome_teacher' => 'معلمنا الفاضل، أهلاً بك في البوابة',
            'dash_section_title' => 'لوحة التحكم والإدارة'
        ];
        
        foreach ($defaults as $key => $val) {
            if (!isset($data[$key])) {
                $data[$key] = $val;
            }
        }

        return view('superadmin/settings/index', $data);
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $posted = $this->request->getPost();
        
        // Handle checkboxes/switches that are not sent when unchecked
        if (!isset($posted['whatsapp_otp_enabled'])) {
            $posted['whatsapp_otp_enabled'] = 'false';
        }
        if (!isset($posted['maintenance_mode'])) {
            $posted['maintenance_mode'] = 'false';
        }

        foreach ($posted as $key => $value) {
            // Skip CSRF tokens, honeypots, and other non-setting fields
            if (in_array($key, ['csrf_test_name', 'csrf_token_name', 'token'])) continue;
            
            $db->table('settings')->replace([
                'type' => $key,
                'description' => $value
            ]);
        }

        return redirect()->to(base_url('superadmin/settings'))->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    /**
     * EMERGENCY REPAIR & SYNC: 
     * 1. Fixes table structure
     * 2. Removes duplicates
     * 3. Syncs missing phrase keys from defaults
     * Visit: /superadmin/settings/repair
     */
    public function repair()
    {
        $db = \Config\Database::connect();
        
        try {
            // 1. Ensure 'type' is VARCHAR(191) to avoid BLOB indexing errors
            $db->query("ALTER TABLE settings MODIFY type VARCHAR(191)");

            // 2. Load Defaults from the index method logic
            $defaults = [
                'system_name' => 'Sela Education System',
                'system_tagline' => 'Smart School Management System',
                'system_desc' => 'Your trusted partner for educational digital transformation.',
                'current_year' => '2025-2026',
                'year_start' => '2025-09-01',
                'maintenance_mode' => 'false',
                'primary_color' => '#192a56',
                'secondary_color' => '#c5a021',
                'contact_email' => 'support@sela.ly',
                'contact_phone' => '+218 900 000 000',
                'contact_whatsapp' => '+218 900 000 000',
                'contact_address' => 'Tripoli, Libya',
                'footer_copyright' => 'All rights reserved © 2025 Sela Solutions',
                'footer_social_title' => 'Follow Us',
                'whatsapp_otp_enabled' => 'false',
                'textmebot_api_key' => '',
                'landing_hero_h1' => 'Sela Education Platform',
                'landing_hero_p' => 'The ultimate digital partner for ambitious educational institutions.',
                'landing_stats_1_value' => '50+',
                'landing_stats_1_label' => 'Schools',
                'landing_stats_2_value' => '12k',
                'landing_stats_2_label' => 'Students',
                'landing_stats_3_value' => '100%',
                'landing_stats_3_label' => 'Security',
                'landing_feature_1_title' => 'Smart Management',
                'landing_feature_1_desc' => 'Manage students, teachers, and exams in one place.',
                'landing_feature_2_title' => 'Advanced Reports',
                'landing_feature_2_desc' => 'Real-time statistics and deep performance audit.',
                'landing_feature_3_title' => 'Top Security',
                'landing_feature_3_desc' => 'Multi-factor authentication and data encryption.',
                'landing_feature_4_title' => 'Fast Performance',
                'landing_feature_4_desc' => 'Cloud-based system with high availability.',
                'landing_roles_title' => 'Who is this for?',
                'landing_roles_subtitle' => 'Integrated solutions for every member of the institution.',
                'landing_role_1_title' => 'For Administrators',
                'landing_role_1_list' => "Full control over the institution\nFinancial management\nAcademic tracking",
                'landing_role_2_title' => 'For Teachers',
                'landing_role_2_list' => "Electronic attendance\nMark entry\nTimetable management",
                'sa_dash_title' => 'Control Panel',
                'sa_dash_subtitle' => 'System Overview & Statistics',
                'sa_dash_stat_1_label' => 'Cities',
                'sa_dash_stat_2_label' => 'Schools',
                'sa_dash_stat_3_label' => 'Students',
                'sa_dash_stat_4_label' => 'Managers',
                'menu_dashboard' => 'Dashboard',
                'menu_cities' => 'Cities',
                'menu_schools' => 'Schools',
                'menu_managers' => 'Managers',
                'menu_settings' => 'Settings',
                'menu_admin_dashboard' => 'Dashboard',
                'menu_admin_students' => 'Students',
                'menu_admin_teachers' => 'Teachers',
                'menu_admin_academic' => 'Academic',
                'menu_admin_attendance' => 'Attendance',
                'menu_admin_registrations' => 'Registrations',
                'menu_admin_finance' => 'Finance',
                'menu_admin_reports' => 'Reports',
                'menu_admin_settings' => 'Settings',
                'menu_mobile_subjects' => 'دليل المناهج والمواد',
                'menu_mobile_marks' => 'رصد درجات المواد',
                'menu_mobile_student_affairs' => 'شؤون الطلبة والقبول',
                'menu_mobile_qr_scanner' => 'مسح معرف الطالب (QR)',
                'menu_mobile_student_photos' => 'إدارة صور الطلاب',
                'menu_mobile_students_desc' => 'البحث عن الطلاب والتحكم ببياناتهم وسجلاتهم.',
                'menu_mobile_qr_scanner_desc' => 'امسح الرمز المربوط بالهوية للتحقق الفوري.',
                'menu_mobile_attendance_desc' => 'رصد الحضور والغياب اليومي للطلاب بالفصول.',
                'menu_mobile_marks_desc' => 'إدخال وتحديث درجات الطلاب في الاختبارات والأنشطة.',
                'menu_mobile_student_photos_desc' => 'التقاط وتحديث الصور الشخصية للطلاب عبر الكاميرا.',
                'menu_mobile_registrations_desc' => 'مراجعة واعتماد ملفات تسجيل الطلاب المنضمين حديثاً.',
                'login_welcome_title' => 'مرحباً بك في منصة صلة',
                'login_welcome_subtitle' => 'الرجاء تسجيل الدخول لمتابعة حسابك التعليمي',
                'login_btn_text' => 'تسجيل الدخول',
                'login_username_hint' => 'اسم المستخدم أو رقم الهاتف',
                'login_password_hint' => 'كلمة المرور',
                'dash_welcome_admin' => 'أهلاً بك مجدداً في إدارة المدرسة',
                'dash_welcome_teacher' => 'معلمنا الفاضل، أهلاً بك في البوابة',
                'dash_section_title' => 'لوحة التحكم والإدارة',
                'action_add_school' => 'Add School',
                'action_manage_cities' => 'Manage Cities',
                'action_create_manager' => 'Create Manager',
                'action_student_center' => 'Student Center',
                'action_system_settings' => 'System Settings',
                'action_view_all' => 'View All',
                'smtp_host' => '',
                'smtp_port' => '587',
                'smtp_user' => '',
                'smtp_pass' => '',
                'smtp_crypto' => 'tls',
                'mail_from' => 'noreply@sela.ly'
            ];

            // 3. Fetch current data and deduplicate in memory
            $all = $db->table('settings')->get()->getResultArray();
            $merged = $defaults; // Start with defaults
            foreach ($all as $row) {
                if (!empty(trim($row['description']))) {
                    $merged[$row['type']] = $row['description']; // Only overwrite if not empty
                }
            }

            // 4. Wipe and Restore Clean Data
            $db->table('settings')->truncate();
            foreach ($merged as $type => $desc) {
                $db->table('settings')->insert(['type' => $type, 'description' => $desc]);
            }

            // 5. Enforce UNIQUE constraint
            $db->query("ALTER TABLE settings ADD UNIQUE (type)");

            return "SUCCESS: Settings table repaired and " . count($merged) . " phrases synced. <a href='".base_url('superadmin/settings')."'>Return to Portal</a>";
        } catch (\Exception $e) {
            return "ERROR: " . $e->getMessage();
        }
    }

}
