import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class StudentsQrScannerScreen extends StatefulWidget {
  const StudentsQrScannerScreen({Key? key}) : super(key: key);

  @override
  State<StudentsQrScannerScreen> createState() => _StudentsQrScannerScreenState();
}

class _StudentsQrScannerScreenState extends State<StudentsQrScannerScreen> with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  final TextEditingController _idController = TextEditingController();
  bool _isSearching = false;
  String? _scanErrorMessage;

  @override
  void initState() {
    super.initState();
    // Setup pulse scan laser animation
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _animationController.dispose();
    _idController.dispose();
    super.dispose();
  }

  Future<void> _handleIdentifyStudent(int studentId) async {
    setState(() {
      _isSearching = true;
      _scanErrorMessage = null;
    });

    final result = await ApiService.identifyStudent(studentId);

    setState(() {
      _isSearching = false;
    });

    if (result['status'] == 'success') {
      if (mounted) {
        _showStudentProfileSheet(context, result['data']);
      }
    } else {
      setState(() {
        _scanErrorMessage = result['message'];
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['message'] ?? 'فشل التعرف على الطالب',
              style: GoogleFonts.cairo(color: Colors.white),
              textAlign: TextAlign.right,
            ),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    }
  }

  void _showStudentProfileSheet(BuildContext context, Map<String, dynamic> data) {
    final student = data['student'] ?? {};
    final attendance = data['attendance'] as List? ?? [];
    final name = student['name'] ?? 'طالب';
    final className = student['class_name'] ?? 'غير معين';
    final schoolName = student['school_name'] ?? 'مدرسة السيلا';
    final phone = student['phone'] ?? 'غير مسجل';
    final mother = student['mother'] ?? 'غير مسجل';
    final nationalid = student['nationalid'] ?? 'غير مسجل';
    final birthday = student['birthday'] ?? 'غير مسجل';
    final parent = student['parent_name'] ?? 'غير مسجل';
    final id = student['id'] ?? 0;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.85,
          minChildSize: 0.50,
          maxChildSize: 0.95,
          builder: (context, scrollController) {
            return Container(
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(32),
                  topRight: Radius.circular(32),
                ),
              ),
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
              child: ListView(
                controller: scrollController,
                children: [
                  // Handle indicator bar
                  Center(
                    child: Container(
                      width: 50,
                      height: 5,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Header with Gold-Border Avatar
                  Center(
                    child: Column(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(4),
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: LinearGradient(
                              colors: [Color(0xff192A56), Color(0xffC5A021)],
                            ),
                          ),
                          child: CircleAvatar(
                            radius: 50,
                            backgroundColor: Colors.grey.shade100,
                            child: Text(
                              name.isNotEmpty ? name[0] : 'ط',
                              style: GoogleFonts.cairo(
                                fontSize: 36,
                                fontWeight: FontWeight.bold,
                                color: const Color(0xff192A56),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          name,
                          style: GoogleFonts.cairo(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xff192A56),
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                          decoration: BoxDecoration(
                            color: const Color(0xffC5A021).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(30),
                          ),
                          child: Text(
                            '$schoolName | $className',
                            style: GoogleFonts.cairo(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xffC5A021),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Student Details Grid Header
                  Text(
                    'البيانات الشخصية والدراسية',
                    style: GoogleFonts.cairo(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xff192A56),
                    ),
                    textAlign: TextAlign.right,
                  ),
                  const SizedBox(height: 16),
                  // Modern Grid of Student Details
                  Container(
                    decoration: BoxDecoration(
                      color: const Color(0xfff8fafc),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: const Color(0xffe2e8f0)),
                    ),
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      children: [
                        _buildDetailRow('رقم المعرف للطالب', '#$id', Icons.fingerprint_rounded),
                        const Divider(color: Color(0xffe2e8f0)),
                        _buildDetailRow('اسم الأم', mother, Icons.face_retouching_natural_rounded),
                        const Divider(color: Color(0xffe2e8f0)),
                        _buildDetailRow('الرقم الوطني', nationalid, Icons.credit_card_rounded),
                        const Divider(color: Color(0xffe2e8f0)),
                        _buildDetailRow('تاريخ الميلاد', birthday, Icons.calendar_month_rounded),
                        const Divider(color: Color(0xffe2e8f0)),
                        _buildDetailRow('رقم الهاتف', phone, Icons.phone_android_rounded),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Recent Attendance
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: const Color(0xffeff6ff),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          'آخر 10 أيام',
                          style: GoogleFonts.cairo(fontSize: 11, color: const Color(0xff1d4ed8), fontWeight: FontWeight.bold),
                        ),
                      ),
                      Text(
                        'سجل الحضور والغياب الأخير',
                        style: GoogleFonts.cairo(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xff192A56),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  if (attendance.isEmpty) ...[
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: const Color(0xfff8fafc),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: const Color(0xffe2e8f0)),
                      ),
                      child: Center(
                        child: Text(
                          'لا يوجد سجل حضور وغياب مسجل لهذا الطالب بعد.',
                          style: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 13),
                          textAlign: TextAlign.center,
                        ),
                      ),
                    ),
                  ] else ...[
                    ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: attendance.length,
                      separatorBuilder: (context, index) => const SizedBox(height: 10),
                      itemBuilder: (context, index) {
                        final record = attendance[index];
                        final dateStr = record['date'] ?? '';
                        final status = record['status'] ?? 'absent';
                        final isPresent = status == 'present' || status == '1';

                        return Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          decoration: BoxDecoration(
                            color: isPresent ? const Color(0xfff0fdf4) : const Color(0xfffef2f2),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isPresent ? const Color(0xffbbf7d0) : const Color(0xfffecaca),
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                                decoration: BoxDecoration(
                                  color: isPresent ? const Color(0xff16a34a) : const Color(0xffdc2626),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  isPresent ? 'حاضر' : 'غائب',
                                  style: GoogleFonts.cairo(
                                    color: Colors.white,
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              Text(
                                dateStr,
                                style: GoogleFonts.cairo(
                                  fontSize: 13,
                                  fontWeight: FontWeight.bold,
                                  color: isPresent ? const Color(0xff15803d) : const Color(0xffb91c1c),
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                  ],
                  const SizedBox(height: 36),

                  // Close button
                  SizedBox(
                    height: 52,
                    child: ElevatedButton(
                      onPressed: () => Navigator.pop(context),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xff192A56),
                        elevation: 0,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      ),
                      child: Text(
                        'إغلاق بطاقة الطالب',
                        style: GoogleFonts.cairo(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Text(
              value,
              style: GoogleFonts.cairo(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: const Color(0xff192A56),
              ),
              textAlign: TextAlign.left,
            ),
          ),
          const SizedBox(width: 10),
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                label,
                style: GoogleFonts.cairo(
                  fontSize: 13,
                  color: const Color(0xff64748b),
                ),
                textAlign: TextAlign.right,
              ),
              const SizedBox(width: 8),
              Icon(icon, color: const Color(0xffC5A021), size: 20),
            ],
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          'مسح معرف الطالب (QR)',
          style: GoogleFonts.cairo(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xff192A56),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Beautiful Animated Laser Scanning Overlay View
            Center(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(28),
                child: Container(
                  width: 280,
                  height: 280,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    border: Border.all(color: const Color(0xffC5A021), width: 3),
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: const [
                      BoxShadow(
                        color: Color(0xffeff6ff),
                        blurRadius: 16,
                        spreadRadius: 2,
                      ),
                    ],
                  ),
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // Scanner corners style decoration
                      Positioned(
                        top: 20,
                        child: Text(
                          'جاري محاكاة الكاميرا والمسح التلقائي...',
                          style: GoogleFonts.cairo(fontSize: 11, color: const Color(0xff64748b), fontWeight: FontWeight.bold),
                        ),
                      ),
                      
                      // QR Vector Mock Graphics inside
                      Icon(Icons.qr_code_scanner_rounded, size: 140, color: const Color(0xff192A56).withOpacity(0.08)),
 
                      // Pulsating laser animation
                      AnimatedBuilder(
                        animation: _animationController,
                        builder: (context, child) {
                          return Positioned(
                            top: 40 + (_animationController.value * 190),
                            left: 20,
                            right: 20,
                            child: Container(
                              height: 4,
                              decoration: BoxDecoration(
                                color: const Color(0xffC5A021),
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: const [
                                  BoxShadow(
                                    color: Color(0xffC5A021),
                                    blurRadius: 10,
                                    spreadRadius: 2,
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 28),
 
            // Quick Tester Grid for PC/Chrome Browser Previews
            Text(
              'محاكاة المسح السريع للطلاب (للاختبار والويب)',
              style: GoogleFonts.cairo(fontSize: 14, fontWeight: FontWeight.bold, color: const Color(0xff192A56)),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildQuickTestButton('طالب (محمد)', 1),
                _buildQuickTestButton('طالبة (فاطمة)', 2),
                _buildQuickTestButton('طالب (أحمد)', 3),
              ],
            ),
            const SizedBox(height: 28),
 
            // Divider or manual input
            Row(
              children: [
                const Expanded(child: Divider(color: Color(0xffe2e8f0))),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Text(
                    'أو أدخل معرف الطالب يدوياً',
                    style: GoogleFonts.cairo(fontSize: 12, color: const Color(0xff94a3b8)),
                  ),
                ),
                const Expanded(child: Divider(color: Color(0xffe2e8f0))),
              ],
            ),
            const SizedBox(height: 20),
 
            // Manual Search Textfield
            TextField(
              controller: _idController,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                labelText: 'رقم معرف الطالب (مثال: 1)',
                labelStyle: GoogleFonts.cairo(color: const Color(0xff64748b), fontSize: 13),
                prefixIcon: const Icon(Icons.search_rounded, color: Color(0xff192A56)),
                filled: true,
                fillColor: const Color(0xfff8fafc),
                contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xffe2e8f0), width: 1),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xff192A56), width: 1.5),
                ),
              ),
            ),
            const SizedBox(height: 20),
 
            // Submit Manual ID Identification Button
            SizedBox(
              height: 52,
              child: ElevatedButton(
                onPressed: _isSearching
                    ? null
                    : () {
                        final text = _idController.text.trim();
                        if (text.isEmpty) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(
                                'الرجاء إدخال رقم المعرف أولاً',
                                style: GoogleFonts.cairo(),
                                textAlign: TextAlign.right,
                              ),
                            ),
                          );
                          return;
                        }
                        final id = int.tryParse(text);
                        if (id == null) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(
                                'الرجاء إدخال رقم صحيح فقط',
                                style: GoogleFonts.cairo(),
                                textAlign: TextAlign.right,
                              ),
                            ),
                          );
                          return;
                        }
                        _handleIdentifyStudent(id);
                      },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xff192A56),
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: _isSearching
                    ? const CircularProgressIndicator(color: Colors.white)
                    : Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.verified_user_rounded, color: Colors.white),
                          const SizedBox(width: 8),
                          Text(
                            'التحقق من هوية الطالب',
                            style: GoogleFonts.cairo(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                          ),
                        ],
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }
 
  Widget _buildQuickTestButton(String label, int studentId) {
    return ElevatedButton(
      onPressed: _isSearching ? null : () => _handleIdentifyStudent(studentId),
      style: ElevatedButton.styleFrom(
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xff192A56),
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
          side: const BorderSide(color: Color(0xffe2e8f0), width: 1),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      ),
      child: Text(
        label,
        style: GoogleFonts.cairo(fontSize: 12, fontWeight: FontWeight.bold),
      ),
    );
  }
}
