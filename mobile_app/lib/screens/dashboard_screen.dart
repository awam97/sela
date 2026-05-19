import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'students_screen.dart';
import 'attendance_screen.dart';
import 'registrations_screen.dart';
import 'subjects_screen.dart';
import 'students_qr_scanner_screen.dart';
import 'profile_screen.dart';
import 'finance_screen.dart';
import 'marks_screen.dart';
import 'student_photos_screen.dart';
import 'student_affairs_screen.dart';
import '../services/app_titles.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  bool _isLoading = true;
  int _userId = 0;
  String _userName = '';
  String _userRole = '';
  String _schoolName = '';
  Map<String, dynamic> _stats = {};
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadUserSession();
  }

  void _loadUserSession() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _userId = prefs.getInt('user_id') ?? 0;
      _userName = prefs.getString('name') ?? 'مدير النظام';
      _userRole = prefs.getString('role') ?? 'admin';
      _schoolName = prefs.getString('school_name') ?? '';
    });
    
    // Only fetch admin stats if not a student
    if (_userRole != 'student') {
      _fetchStats();
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _fetchStats() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getDashboard();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result['status'] == 'success') {
          _stats = result['data'];
        } else {
          _errorMessage = result['message'];
        }
      });
    }
  }

  void _handleLogout() async {
    // Show confirmation dialog in Arabic
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(AppTitles.lblLogout, style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
        content: Text('هل أنت متأكد من رغبتك في تسجيل الخروج من النظام؟', style: GoogleFonts.cairo()),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('إلغاء', style: GoogleFonts.cairo(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              await ApiService.logout();
              if (mounted) {
                Navigator.of(context).pushReplacement(
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                );
              }
            },
            child: Text('خروج', style: GoogleFonts.cairo(color: Colors.redAccent, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isSuper = _userRole == 'super_admin';
    final isStudent = _userRole == 'student';
    final pendingCount = _stats['pending_registrations'] ?? 0;

    if (isStudent) {
      return _buildStudentDashboard(context);
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(
          isSuper ? 'منصة صلة - الإدارة العامة' : _schoolName.isNotEmpty ? _schoolName : 'إدارة المدرسة',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.logout_rounded, color: Colors.white),
            tooltip: AppTitles.lblLogout,
            onPressed: _handleLogout,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchStats,
        color: const Color(0xff192A56),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Welcome Greetings Card
              InkWell(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const ProfileScreen()),
                  ).then((_) => _loadUserSession());
                },
                borderRadius: BorderRadius.circular(24),
                child: Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(24),
                    color: Colors.white,
                    border: Border.all(color: const Color(0xffC5A021).withOpacity(0.15), width: 1.5),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xff192A56).withOpacity(0.04),
                        blurRadius: 20,
                        spreadRadius: 2,
                      ),
                    ],
                  ),
                  padding: const EdgeInsets.all(24),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              isSuper ? AppTitles.dashWelcomeAdmin : AppTitles.dashWelcomeTeacher,
                              style: GoogleFonts.cairo(
                                fontSize: 13,
                                color: const Color(0xff64748b), // Slate gray
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            Text(
                              _userName,
                              style: GoogleFonts.cairo(
                                fontSize: 22,
                                fontWeight: FontWeight.w900,
                                color: const Color(0xff192A56), // Official Nile Blue
                              ),
                            ),
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                              decoration: BoxDecoration(
                                color: const Color(0xffC5A021).withOpacity(0.1),
                                borderRadius: BorderRadius.circular(30),
                              ),
                              child: Text(
                                isSuper ? 'المدير العام للمنصة' : 'إدارة المدرسة',
                                style: GoogleFonts.cairo(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: const Color(0xffC5A021),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Elegant Premium Profile Avatar Glow
                      Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: const Color(0xffC5A021).withOpacity(0.3), width: 1.5),
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(100),
                          child: Container(
                            width: 64,
                            height: 64,
                            color: const Color(0xff192A56).withOpacity(0.05),
                            child: Image.network(
                              'https://graya.ly/upload/${_userRole}_image/$_userId.jpg',
                              fit: BoxFit.cover,
                              errorBuilder: (ctx, err, st) {
                                return Center(
                                  child: Text(
                                    _userName.isNotEmpty ? _userName.substring(0, 1) : 'م',
                                    style: GoogleFonts.cairo(
                                      fontSize: 24,
                                      fontWeight: FontWeight.w900,
                                      color: const Color(0xff192A56),
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 28),

              // Statistics Section Header
              Text(
                'إحصائيات سريعة',
                style: GoogleFonts.cairo(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xff192a56),
                ),
              ),
              const SizedBox(height: 16),

              // Dynamic statistics loading state
              if (_isLoading) ...[
                const Center(
                  child: Padding(
                    padding: EdgeInsets.symmetric(vertical: 40),
                    child: CircularProgressIndicator(color: Color(0xff192a56)),
                  ),
                ),
              ] else if (_errorMessage != null) ...[
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.amber.shade50,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.amber.shade300),
                  ),
                  child: Text(
                    _errorMessage!,
                    style: GoogleFonts.cairo(color: Colors.amber.shade900, fontSize: 13),
                    textAlign: TextAlign.center,
                  ),
                ),
              ] else ...[
                // Grid of statistics
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                  childAspectRatio: 1.3,
                  children: [
                    if (isSuper) ...[
                      _buildStatCard(
                        'المدارس المسجلة',
                        '${_stats['total_schools'] ?? 0}',
                        Icons.apartment_rounded,
                        Colors.blue,
                      ),
                    ] else ...[
                      _buildStatCard(
                        'الفصول الدراسية',
                        '${_stats['total_classes'] ?? 0}',
                        Icons.class_rounded,
                        Colors.blue,
                      ),
                    ],
                    _buildStatCard(
                      'إجمالي الطلاب',
                      '${_stats['total_students'] ?? 0}',
                      Icons.people_alt_rounded,
                      Colors.green,
                    ),
                    _buildStatCard(
                      'المعلمون',
                      '${_stats['total_teachers'] ?? 0}',
                      Icons.co_present_rounded,
                      Colors.purple,
                    ),
                    _buildStatCard(
                      'طلبات التحاق جديدة',
                      '$pendingCount',
                      Icons.notifications_active_rounded,
                      Colors.orange,
                      highlight: pendingCount > 0,
                    ),
                  ],
                ),
              ],
              const SizedBox(height: 32),

              // Control Panel Actions
              Text(
                AppTitles.dashSectionTitle,
                style: GoogleFonts.cairo(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xff192a56),
                ),
              ),
              const SizedBox(height: 16),

              // Action buttons list
              _buildActionButton(
                context,
                AppTitles.studentAffairs,
                'إدارة ملفات الطلاب، الحضور والغياب، رصد الدرجات، الصور الشخصية، والطلبات الجديدة.',
                Icons.school_rounded,
                const Color(0xff192a56),
                () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => StudentAffairsScreen(
                      isSuper: isSuper,
                      pendingCount: pendingCount,
                      onBack: () => _fetchStats(),
                    ),
                  ),
                ),
                badgeCount: pendingCount,
              ),
              const SizedBox(height: 12),

              _buildActionButton(
                context,
                AppTitles.subjects,
                'استعراض المواد والمنهج الدراسي والدرجات المطلوبة للنجاح.',
                Icons.menu_book_rounded,
                const Color(0xffc5a021),
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const SubjectsScreen()),
                ),
              ),
              const SizedBox(height: 12),

              _buildActionButton(
                context,
                AppTitles.finance,
                'عرض خلاصة فواتير المدرسة وسجل العمليات والمدفوعات والمستحقات المتبقية.',
                Icons.account_balance_wallet_rounded,
                const Color(0xff16a34a),
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const FinanceScreen()),
                ),
              ),
              const SizedBox(height: 12),

              _buildActionButton(
                context,
                AppTitles.profile,
                'تحديث بياناتك الشخصية، البريد الإلكتروني، ورقم الهاتف، وتعديل كلمة المرور.',
                Icons.manage_accounts_rounded,
                const Color(0xff192a56),
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const ProfileScreen()),
                ).then((_) => _loadUserSession()),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color, {bool highlight = false}) {
    // Curate beautiful soft HSL light backgrounds and matching rich accent colors
    Color baseBgColor;
    Color accentColor;

    if (highlight) {
      baseBgColor = const Color(0xffFFF7ED); // Soft orange
      accentColor = const Color(0xffC5A021); // Gold accent
    } else if (color == Colors.blue) {
      baseBgColor = const Color(0xffEFF6FF); // Ice blue
      accentColor = const Color(0xff1E40AF); // Royal blue
    } else if (color == Colors.green) {
      baseBgColor = const Color(0xffECFDF5); // Soft emerald
      accentColor = const Color(0xff047857); // Deep emerald
    } else if (color == Colors.purple) {
      baseBgColor = const Color(0xffF5F3FF); // Soft violet
      accentColor = const Color(0xff6D28D9); // Violet accent
    } else {
      baseBgColor = color.withOpacity(0.05);
      accentColor = color;
    }

    return Card(
      elevation: 0,
      color: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(24),
        side: BorderSide(
          color: highlight ? const Color(0xffC5A021).withOpacity(0.3) : const Color(0xffe2e8f0),
          width: highlight ? 1.8 : 1.2,
        ),
      ),
      child: Container(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: baseBgColor,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(icon, color: accentColor, size: 24),
                ),
                if (highlight)
                  Container(
                    width: 8,
                    height: 8,
                    decoration: const BoxDecoration(
                      color: Colors.redAccent,
                      shape: BoxShape.circle,
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  value,
                  style: GoogleFonts.cairo(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xff192A56),
                    height: 1.1,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  title,
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: const Color(0xff64748b), // Slate gray
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildActionButton(
    BuildContext context,
    String title,
    String subtitle,
    IconData icon,
    Color color,
    VoidCallback onTap, {
    int badgeCount = 0,
  }) {
    // Beautiful soft HSL light backgrounds and matching rich accent colors
    Color baseBgColor = color.withOpacity(0.08);
    Color accentColor = color == const Color(0xffc5a021) ? const Color(0xffC5A021) : color;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Card(
        elevation: 0,
        color: Colors.white,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(24),
          side: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: baseBgColor,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Icon(icon, color: accentColor, size: 26),
              ),
              const SizedBox(width: 18),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xff192A56),
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        color: const Color(0xff64748b),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
              if (badgeCount > 0) ...[
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.redAccent,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    '$badgeCount',
                    style: GoogleFonts.cairo(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
              ],
              const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Color(0xff94a3b8)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStudentDashboard(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'منصة صلة - بوابة الطالب',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.logout_rounded, color: Colors.white),
            tooltip: 'تسجيل الخروج',
            onPressed: _handleLogout,
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Student Welcome Profile Info Card
            Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(24),
                color: Colors.white,
                border: Border.all(color: const Color(0xffC5A021).withOpacity(0.15), width: 1.5),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xff192A56).withOpacity(0.04),
                    blurRadius: 20,
                    spreadRadius: 2,
                  ),
                ],
              ),
              padding: const EdgeInsets.all(24),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'أهلاً بك يا بطل،',
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            color: const Color(0xff64748b), // Slate gray
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        Text(
                          _userName,
                          style: GoogleFonts.cairo(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: const Color(0xff192A56), // Official Nile Blue
                          ),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: const Color(0xffC5A021).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(30),
                          ),
                          child: Text(
                            _schoolName.isNotEmpty ? _schoolName : 'مدرسة صلة النموذجية',
                            style: GoogleFonts.cairo(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xffC5A021),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 16),
                  // Elegant Premium Profile Avatar Glow
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: const Color(0xffC5A021).withOpacity(0.3), width: 1.5),
                    ),
                    child: CircleAvatar(
                      radius: 32,
                      backgroundColor: const Color(0xff192A56).withOpacity(0.05),
                      child: Text(
                        _userName.isNotEmpty ? _userName.substring(0, 1) : 'ط',
                        style: GoogleFonts.cairo(
                          fontSize: 24,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xff192A56),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),

            // Immersive QR Code Presentation Card
            Card(
              elevation: 0,
              color: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(28),
                side: const BorderSide(color: Color(0xffe2e8f0), width: 1.2),
              ),
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 24),
                child: Column(
                  children: [
                    Text(
                      'رمز الحضور الرقمي الخاص بك',
                      style: GoogleFonts.cairo(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff192A56),
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'أبرز هذا الرمز للمشرف أو المعلم لتسجيل حضورك اليومي',
                      style: GoogleFonts.cairo(
                        fontSize: 12,
                        color: Colors.grey.shade600,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 28),

                    // Elegant QR Code Wrapper
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(24),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.grey.shade200,
                            blurRadius: 16,
                            spreadRadius: 2,
                          ),
                        ],
                        border: Border.all(color: const Color(0xffC5A021).withOpacity(0.3), width: 2),
                      ),
                      child: Image.network(
                        'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=$_userId',
                        width: 200,
                        height: 200,
                        loadingBuilder: (context, child, loadingProgress) {
                          if (loadingProgress == null) return child;
                          return Container(
                            width: 200,
                            height: 200,
                            alignment: Alignment.center,
                            child: const CircularProgressIndicator(color: Color(0xff192A56)),
                          );
                        },
                        errorBuilder: (context, error, stackTrace) {
                          return Container(
                            width: 200,
                            height: 200,
                            color: Colors.grey.shade50,
                            alignment: Alignment.center,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(Icons.qr_code_2_rounded, size: 64, color: Colors.grey),
                                const SizedBox(height: 10),
                                Text(
                                  'تأكد من الاتصال بالإنترنت',
                                  style: GoogleFonts.cairo(fontSize: 11, color: Colors.grey),
                                ),
                              ],
                            ),
                          );
                        },
                      ),
                    ),
                    const SizedBox(height: 24),

                    Text(
                      'معرف الطالب الرقمي: #$_userId',
                      style: GoogleFonts.cairo(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff1e1b4b),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 28),

            // Student Quick Portal Tools
            Text(
              'الخدمات الأكاديمية',
              style: GoogleFonts.cairo(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: const Color(0xff192a56),
              ),
            ),
            const SizedBox(height: 16),

            _buildActionButton(
              context,
              'جدول المناهج والمواد الدراسية',
              'تصفح المواد الدراسية ودرجات النجاح الخاصة بصفك.',
              Icons.menu_book_rounded,
              const Color(0xffc5a021),
              () => Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const SubjectsScreen()),
              ),
            ),
            const SizedBox(height: 12),

            _buildActionButton(
              context,
              'بطاقة تفاصيل الطالب',
              'عرض بياناتك الدراسية المسجلة بقاعدة بيانات المنصة.',
              Icons.contact_mail_rounded,
              const Color(0xff192a56),
              () async {
                setState(() => _isLoading = true);
                final result = await ApiService.identifyStudent(_userId);
                setState(() => _isLoading = false);
                if (result['status'] == 'success') {
                  if (context.mounted) {
                    _showStudentDetailsDialog(context, result['data']['student']);
                  }
                }
              },
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }

  void _showStudentDetailsDialog(BuildContext context, Map<String, dynamic> student) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('بطاقتك الدراسية الرقمية', style: GoogleFonts.cairo(fontWeight: FontWeight.bold), textAlign: TextAlign.right),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            _buildDetailPopupRow('اسم الطالب', student['name'] ?? ''),
            const Divider(),
            _buildDetailPopupRow('الصف الدراسي', student['class_name'] ?? 'غير معين'),
            const Divider(),
            _buildDetailPopupRow('المدرسة', student['school_name'] ?? 'مدرسة السيلا'),
            const Divider(),
            _buildDetailPopupRow('الرقم الوطني', student['nationalid'] ?? 'غير مسجل'),
            const Divider(),
            _buildDetailPopupRow('تاريخ الميلاد', student['birthday'] ?? 'غير مسجل'),
            const Divider(),
            _buildDetailPopupRow('اسم الأم', student['mother'] ?? 'غير مسجل'),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('حسناً', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailPopupRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Text(
              value,
              style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.grey.shade800),
              textAlign: TextAlign.left,
            ),
          ),
          const SizedBox(width: 8),
          Text(
            '$label:',
            style: GoogleFonts.cairo(fontSize: 12, color: Colors.grey.shade500),
          ),
        ],
      ),
    );
  }
}
