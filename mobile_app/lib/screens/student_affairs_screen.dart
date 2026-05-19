import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'students_screen.dart';
import 'attendance_screen.dart';
import 'students_qr_scanner_screen.dart';
import 'student_photos_screen.dart';
import 'registrations_screen.dart';
import 'marks_screen.dart';

class StudentAffairsScreen extends StatelessWidget {
  final bool isSuper;
  final int pendingCount;
  final VoidCallback? onBack;

  const StudentAffairsScreen({
    Key? key,
    required this.isSuper,
    required this.pendingCount,
    this.onBack,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final primaryColor = const Color(0xff192A56);

    // List of student affairs items
    final List<Map<String, dynamic>> items = [
      {
        'title': 'دليل وشؤون الطلاب',
        'desc': 'البحث عن الطلاب والتحكم ببياناتهم وسجلاتهم.',
        'icon': Icons.supervised_user_circle_rounded,
        'color': const Color(0xff0f172a),
        'page': const StudentsScreen(),
      },
      {
        'title': 'مسح رمز الطالب (QR)',
        'desc': 'امسح الرمز المربوط بالهوية للتحقق الفوري.',
        'icon': Icons.qr_code_scanner_rounded,
        'color': const Color(0xff192a56),
        'page': const StudentsQrScannerScreen(),
      },
      if (!isSuper)
        {
          'title': 'تسجيل الحضور والغياب',
          'desc': 'رصد الحضور والغياب اليومي للطلاب بالفصول.',
          'icon': Icons.assignment_turned_in_rounded,
          'color': const Color(0xffc5a021),
          'page': const AttendanceScreen(),
        },
      {
        'title': 'رصد درجات المواد',
        'desc': 'إدخال وتحديث درجات الطلاب في الاختبارات والأنشطة.',
        'icon': Icons.app_registration_rounded,
        'color': const Color(0xffc5a021),
        'page': const MarksScreen(),
      },
      {
        'title': 'إدارة صور الطلاب',
        'desc': 'التقاط وتحديث الصور الشخصية للطلاب عبر الكاميرا.',
        'icon': Icons.camera_enhance_rounded,
        'color': const Color(0xff0284c7),
        'page': const StudentPhotosScreen(),
      },
      {
        'title': 'طلبات الالتحاق الجديدة',
        'desc': 'مراجعة واعتماد ملفات تسجيل الطلاب المنضمين حديثاً.',
        'icon': Icons.person_add_rounded,
        'color': Colors.orange.shade700,
        'page': const RegistrationsScreen(),
        'badge': pendingCount,
      },
    ];

    return Scaffold(
      backgroundColor: const Color(0xfff8fafc),
      appBar: AppBar(
        title: Text(
          'شؤون الطلبة والطلاب',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: Colors.transparent,
        foregroundColor: primaryColor,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_rounded),
          onPressed: () {
            if (onBack != null) onBack!();
            Navigator.pop(context);
          },
        ),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Page Header
              Text(
                'الخدمات والأدوات الطلابية',
                style: GoogleFonts.cairo(
                  fontSize: 16,
                  fontWeight: FontWeight.w900,
                  color: primaryColor,
                ),
                textAlign: TextAlign.right,
              ),
              const SizedBox(height: 6),
              Text(
                'اختر أحد الأقسام التالية لإدارة شؤون الطلاب والعمليات الأكاديمية.',
                style: GoogleFonts.cairo(
                  fontSize: 12,
                  color: const Color(0xff64748b),
                  fontWeight: FontWeight.w600,
                ),
                textAlign: TextAlign.right,
              ),
              const SizedBox(height: 24),

              // Grid of Student Affairs Items
              Expanded(
                child: GridView.builder(
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 16,
                    mainAxisSpacing: 16,
                    childAspectRatio: 1.45,
                  ),
                  itemCount: items.length,
                  itemBuilder: (context, index) {
                    final item = items[index];
                    final Color baseBgColor = item['color'].withOpacity(0.08);
                    final Color itemAccentColor = item['color'];
                    final int badge = item['badge'] ?? 0;

                    return InkWell(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => item['page']),
                        ).then((_) {
                          if (item['page'] is RegistrationsScreen && onBack != null) {
                            onBack!();
                          }
                        });
                      },
                      borderRadius: BorderRadius.circular(16),
                      child: Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: const Color(0xffe2e8f0), width: 1.2),
                          boxShadow: [
                            BoxShadow(
                              color: primaryColor.withOpacity(0.02),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        padding: const EdgeInsets.all(12),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Stack(
                              clipBehavior: Clip.none,
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(8),
                                  decoration: BoxDecoration(
                                    color: baseBgColor,
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: Icon(item['icon'], color: itemAccentColor, size: 20),
                                ),
                                if (badge > 0)
                                  Positioned(
                                    top: -4,
                                    right: -4,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: Colors.redAccent,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Text(
                                        '$badge',
                                        style: GoogleFonts.cairo(
                                          color: Colors.white,
                                          fontSize: 8,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              item['title'],
                              style: GoogleFonts.cairo(
                                fontSize: 12,
                                fontWeight: FontWeight.w900,
                                color: primaryColor,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 2),
                            Text(
                              item['desc'],
                              style: GoogleFonts.cairo(
                                fontSize: 9,
                                color: const Color(0xff64748b),
                                fontWeight: FontWeight.bold,
                                height: 1.2,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
