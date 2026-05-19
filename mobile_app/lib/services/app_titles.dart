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

  // Screen specific titles (Mapped to Web settings type keys)
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
}
