import 'package:shared_preferences/shared_preferences.dart';

class AppTitles {
  static SharedPreferences? _prefs;

  /**
   * Initialize SharedPreferences once at startup for fast retrieval
   */
  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  /**
   * Generic getter with robust local defaults
   */
  static String get(String key, String defaultValue) {
    if (_prefs == null) return defaultValue;
    final val = _prefs!.getString('app_title_$key');
    if (val == null || val.trim().isEmpty) return defaultValue;
    return val;
  }

  // ==================== SCREEN TITLE PHRASES ====================
  static String get students => get('menu_admin_students', 'دليل وشؤون الطلاب');
  static String get attendance => get('menu_admin_attendance', 'تسجيل الحضور والغياب');
  static String get registrations => get('menu_admin_registrations', 'طلبات الالتحاق الجديدة');
  static String get finance => get('menu_admin_finance', 'الإدارة المالية والفواتير');
  static String get profile => get('menu_admin_settings', 'الملف الشخصي للمسؤول');
  static String get subjects => get('menu_mobile_subjects', 'دليل المناهج والمواد');
  static String get marks => get('menu_mobile_marks', 'رصد درجات المواد');
  static String get studentAffairs => get('menu_mobile_student_affairs', 'شؤون الطلبة والقبول');
  static String get qrScanner => get('menu_mobile_qr_scanner', 'مسح معرف الطالب (QR)');
  static String get studentPhotos => get('menu_mobile_student_photos', 'إدارة صور الطلاب');

  // ==================== SCREEN SUBTITLE PHRASES ====================
  static String get studentsDesc => get('menu_mobile_students_desc', 'البحث عن الطلاب والتحكم ببياناتهم وسجلاتهم.');
  static String get qrScannerDesc => get('menu_mobile_qr_scanner_desc', 'امسح الرمز المربوط بالهوية للتحقق الفوري.');
  static String get attendanceDesc => get('menu_mobile_attendance_desc', 'رصد الحضور والغياب اليومي للطلاب بالفصول.');
  static String get marksDesc => get('menu_mobile_marks_desc', 'إدخال وتحديث درجات الطلاب في الاختبارات والأنشطة.');
  static String get studentPhotosDesc => get('menu_mobile_student_photos_desc', 'التقاط وتحديث الصور الشخصية للطلاب عبر الكاميرا.');
  static String get registrationsDesc => get('menu_mobile_registrations_desc', 'مراجعة واعتماد ملفات تسجيل الطلاب المنضمين حديثاً.');

  // ==================== LOGIN SCREEN PHRASES ====================
  static String get loginWelcomeTitle => get('login_welcome_title', 'مرحباً بك في منصة صلة');
  static String get loginWelcomeSubtitle => get('login_welcome_subtitle', 'الرجاء تسجيل الدخول لمتابعة حسابك التعليمي');
  static String get loginBtnText => get('login_btn_text', 'تسجيل الدخول');
  static String get loginUsernameHint => get('login_username_hint', 'اسم المستخدم أو رقم الهاتف');
  static String get loginPasswordHint => get('login_password_hint', 'كلمة المرور');

  // ==================== DASHBOARD PHRASES ====================
  static String get dashWelcomeAdmin => get('dash_welcome_admin', 'أهلاً بك مجدداً في إدارة المدرسة');
  static String get dashWelcomeTeacher => get('dash_welcome_teacher', 'معلمنا الفاضل، أهلاً بك في البوابة');
  static String get dashSectionTitle => get('dash_section_title', 'لوحة التحكم والإدارة');
}
