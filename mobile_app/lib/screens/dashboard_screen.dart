import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'students_screen.dart';
import 'attendance_screen.dart';
import 'registrations_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  bool _isLoading = true;
  String _userName = '';
  String _userRole = '';
  String _schoolName = '';
  Map<String, dynamic> _stats = {};
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadUserSession();
    _fetchStats();
  }

  void _loadUserSession() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _userName = prefs.getString('name') ?? 'مدير النظام';
      _userRole = prefs.getString('role') ?? 'admin';
      _schoolName = prefs.getString('school_name') ?? '';
    });
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
        title: Text('تسجيل الخروج', style: GoogleFonts.cairo(fontWeight: FontWeight.bold)),
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
    final pendingCount = _stats['pending_registrations'] ?? 0;

    return Scaffold(
      appBar: AppBar(
        title: Text(isSuper ? 'منصة صلة - الإدارة العامة' : _schoolName.isNotEmpty ? _schoolName : 'إدارة المدرسة'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout_rounded),
            tooltip: 'تسجيل الخروج',
            onPressed: _handleLogout,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchStats,
        color: const Color(0xffc5a021),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Welcome Greetings Card
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
                child: Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(24),
                    gradient: const LinearGradient(
                      colors: [Color(0xff192a56), Color(0xff273c75)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                  ),
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'أهلاً بك،',
                        style: GoogleFonts.cairo(
                          fontSize: 16,
                          color: Colors.white70,
                        ),
                      ),
                      Text(
                        _userName,
                        style: GoogleFonts.cairo(
                          fontSize: 22,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xffc5a021), // Sela Gold
                        ),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white10,
                          borderRadius: BorderRadius.circular(30),
                        ),
                        child: Text(
                          isSuper ? 'المدير العام للمنصة' : 'إدارة المدرسة',
                          style: GoogleFonts.cairo(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
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
                'لوحة التحكم والإدارة',
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
                'دليل وشؤون الطلاب',
                'تصفح، ابحث، وتحكم في بيانات الطلاب بكل سهولة.',
                Icons.supervised_user_circle_rounded,
                const Color(0xff192a56),
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const StudentsScreen()),
                ),
              ),
              const SizedBox(height: 12),
              
              // Only School Admins register daily attendance
              if (!isSuper) ...[
                _buildActionButton(
                  context,
                  'تسجيل الحضور والغياب',
                  'تسجيل الحضور والغياب اليومي للطلاب في الفصول.',
                  Icons.assignment_turned_in_rounded,
                  const Color(0xffc5a021),
                  () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const AttendanceScreen()),
                  ),
                ),
                const SizedBox(height: 12),
              ],

              _buildActionButton(
                context,
                'طلبات الالتحاق الجديدة',
                'مراجعة واعتماد الطلاب الجدد المنضمين للمنصة.',
                Icons.person_add_rounded,
                Colors.orange.shade700,
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const RegistrationsScreen()),
                ).then((_) => _fetchStats()), // Reload stats on return in case of approval
                badgeCount: pendingCount,
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color, {bool highlight = false}) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
        side: highlight
            ? const BorderSide(color: Color(0xffc5a021), width: 2)
            : BorderSide(color: Colors.grey.shade200),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Icon(icon, color: highlight ? const Color(0xffc5a021) : color, size: 28),
                if (highlight)
                  Container(
                    width: 10,
                    height: 10,
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                  ),
              ],
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  value,
                  style: GoogleFonts.cairo(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xff192a56),
                  ),
                ),
                Text(
                  title,
                  style: GoogleFonts.cairo(
                    fontSize: 11,
                    color: Colors.grey.shade600,
                    fontWeight: FontWeight.bold,
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
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: BorderSide(color: Colors.grey.shade100),
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(icon, color: color, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.cairo(
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xff192a56),
                      ),
                    ),
                    Text(
                      subtitle,
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        color: Colors.grey.shade500,
                      ),
                    ),
                  ],
                ),
              ),
              if (badgeCount > 0) ...[
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.red,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    '$badgeCount',
                    style: GoogleFonts.cairo(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
              const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
            ],
          ),
        ),
      ),
    );
  }
}
